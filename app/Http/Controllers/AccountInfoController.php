<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountInfo\AddTradingAccountRequest;
use App\Http\Requests\AccountInfo\UpdateLeverageRequest;
use App\Models\AccountType;
use App\Models\SettingLeverage;
use App\Models\TradingAccount;
use App\Models\TradingUser;
use App\Services\CTraderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AccountInfoController extends Controller
{
    public function account_info($setting = null)
    {
        $accountTypes = AccountType::where('id', 1)->first();

        return Inertia::render('AccountInfo/AccountInfo', [
            'accountTypes' => $accountTypes,
            'leverages' => SettingLeverage::all(),
            'setting' => $setting,
        ]);
    }

    public function getTradingAccounts()
    {
        $user = Auth::user();
        $conn = (new CTraderService)->connectionStatus();
        if ($conn['code'] == 0) {
            try {
                (new CTraderService)->getUserInfo($user->tradingUsers);
            } catch (\Exception $e) {
                \Log::error('CTrader Error');
            }
        }

        $trading_accounts = TradingAccount::query()
            ->with('accountType')
            ->where('user_id', $user->id)
            ->paginate(5);

        return response()->json($trading_accounts);
    }

    public function add_trading_account(AddTradingAccountRequest $request)
    {
        $conn = (new CTraderService)->connectionStatus();
        if ($conn['code'] != 0) {
            if ($conn['code'] == 10) {
                return response()->json(['success' => false, 'message' => 'No connection with cTrader Server']);
            }
            return response()->json(['success' => false, 'message' => $conn['message']]);
        }

        $user = Auth::user();

        /*
        $mainPassword = Str::random(8);
        $investorPassword = Str::random(8);
         Mail::to($user->email)->send(new NewMetaAccount($mainPassword, $investorPassword));
        return true; */

        $group = $request->group;
        $mainPassword = Str::random(8);
        $investorPassword = Str::random(8);
        // $group = 'real\ETIQECNElite_A';
        // $group = Group::where('value', $group)->first()->value('meta_group_name');
        $group = AccountType::with('metaGroup')->where('id', $group)->get()->value('metaGroup.meta_group_name');

        $remarks = $user->remark . ' ' . $request->additionalNotes;
        $ctAccount = (new CTraderService)->createUser($user,  $mainPassword, $investorPassword, $group, $request->leverage, $request->group, null, null, $remarks);
        //Mail::to($user->email)->send(new NewMetaAccount($ctAccount['login'], $mainPassword, $investorPassword));
        return back()->with('toast', 'Successfully Created Trading Account');
        // return true;
    }

    public function change_leverage(UpdateLeverageRequest $request)
    {
        $conn = (new CTraderService)->connectionStatus();
        if ($conn['code'] != 0) {
            if ($conn['code'] == 10) {
                return response()->json(['success' => false, 'message' => 'No connection with cTrader Server']);
            }
            return response()->json(['success' => false, 'message' => $conn['message']]);
        }

        $trading_account = TradingAccount::where('meta_login', $request->account_no)->first();
        if ($trading_account) {
            try {
                (new CTraderService)->updateLeverage($request->account_no, $request->leverage);
            } catch (\Throwable $e) {
                if ($e->getMessage() == "Not found") {
                    TradingUser::firstWhere('meta_login', $request->account_no)->update(['acc_status' => 'Inactive']);
                } else {
                    Log::error($e->getMessage());
                }
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            $trading_account = TradingAccount::where('user_id', Auth::id())->get();
            return back()->with('toast', 'Successfully Updated Leverage');
        }
        return redirect()->back();
    }
}
