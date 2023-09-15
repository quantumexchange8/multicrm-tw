<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\TradingUser;
use App\Models\User;
use App\Services\ChangeTraderBalanceType;
use App\Services\CTraderService;
use App\Services\RunningNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class InternalTransferController extends Controller
{
    public function transaction()
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

        if ($user->hasRole('ib')) {
            $tradingUsers = TradingUser::where('user_id', $user->id)->where('module', '!=', 'pamm')->get();
        } else {
            $tradingUsers = $user->tradingUsers; // or $trading_users = [];
        }

        return Inertia::render('Transaction/InternalTransfer', [
            'tradingUsers' => $tradingUsers,
        ]);
    }

    public function getTransaction(Request $request): \Illuminate\Http\JsonResponse
    {
        $paymentTypes = [
            'Deposit' => 'payment',
            'Withdrawal' => 'payment',
            'WalletToAccount' => 'internal_transfer',
            'AccountToWallet' => 'internal_transfer',
            'AccountToAccount' => 'internal_transfer',
            'RebateToWallet' => 'rebate_payout'
        ];

        $filteredPayments = [];

        foreach ($paymentTypes as $type => $category) {
            $payments = Payment::query()
                ->where('user_id', Auth::id())
                ->where('category', $category)
                ->where('type', $type)
                ->when($request->filled('date'), function ($query) use ($request) {
                    $date = $request->input('date');
                    $start_date = Carbon::createFromFormat('Y-m-d', $date[0])->startOfDay();
                    $end_date = Carbon::createFromFormat('Y-m-d', $date[1])->endOfDay();
                    $query->whereBetween('created_at', [$start_date, $end_date]);
                })
                ->when($request->filled('search'), function ($query) use ($request) {
                    $search = $request->input('search');
                    $query->where('to', 'like', '%' . $search . '%')
                        ->orWhere('from', 'like', '%' . $search . '%');
                })
                ->latest()
                ->paginate(10);

            $filteredPayments[$type] = $payments;
        }

        return response()->json($filteredPayments);
    }

    public function wallet_to_account(Request $request)
    {
        $conn = (new CTraderService)->connectionStatus();
        if ($conn['code'] != 0) {
            if ($conn['code'] == 10) {
                return response()->json(['success' => false, 'message' => 'No connection with cTrader Server']);
            }
            return response()->json(['success' => false, 'message' => $conn['message']]);
        }

        $request->validate([
            'account_no' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);
        //$user = Auth::user();
        $user_id = Auth::id();
        $user = User::find($user_id);
        $amount = floatval($request->amount);
        if ($user->cash_wallet < $amount) {
            throw ValidationException::withMessages(['amount' => trans('Insufficient balance')]);
        }

        $payment_id = RunningNumberService::getID('transaction');
        try {
            $trade = (new CTraderService)->createTrade($request->account_no, $request->amount, "Wallet To Account", ChangeTraderBalanceType::DEPOSIT);
        } catch (\Throwable $e) {
            if ($e->getMessage() == "Not found") {
                TradingUser::firstWhere('meta_login', $request->account_no)->update(['acc_status' => 'Inactive']);
            } else {
                Log::error($e->getMessage());
            }
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        $ticket = $trade->getTicket();
        Payment::create([
            'user_id' => $user_id,
            'payment_id' => $payment_id,
            'category' => 'internal_transfer',
            'type' => 'WalletToAccount',
            'to' => $request->account_no,
            'amount' => $amount,
            'ticket' => $ticket,
            'status' => 'Successful',
        ]);

        $user->cash_wallet -= $amount;
        $user->save();
        return redirect()->back()->with('toast', 'Successful Transfer Wallet To Account!');
    }

    public function account_to_wallet(Request $request)
    {
        $conn = (new CTraderService)->connectionStatus();
        if ($conn['code'] != 0) {
            if ($conn['code'] == 10) {
                return response()->json(['success' => false, 'message' => 'No connection with cTrader Server']);
            }
            return response()->json(['success' => false, 'message' => $conn['message']]);
        }

        $request->validate([
            'account_no' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        $tradingUser = TradingUser::firstWhere('meta_login', $request->account_no);
        (new CTraderService)->getUserInfo([$tradingUser]);
        $tradingUser = TradingUser::firstWhere('meta_login', $request->account_no);

        if ($tradingUser->balance < $request->amount) {
            throw ValidationException::withMessages(['amount' => trans('Insufficient balance')]);
        }

        $payment_id = RunningNumberService::getID('transaction');
        try {
            $trade = (new CTraderService)->createTrade($request->account_no, $request->amount, "Account To Wallet", ChangeTraderBalanceType::WITHDRAW);
        } catch (\Throwable $e) {
            if ($e->getMessage() == "Not found") {
                TradingUser::firstWhere('meta_login', $request->account_no)->update(['acc_status' => 'Inactive']);
            } else {
                Log::error($e->getMessage());
            }
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        $ticket = $trade->getTicket();

        Payment::create([
            'user_id' => $user->id,
            'payment_id' => $payment_id,
            'category' => 'internal_transfer',
            'type' => 'AccountToWallet',
            'from' => $request->account_no,
            'amount' => $request->amount,
            'ticket' => $ticket,
            'status' => 'Successful',

        ]);
        $user->cash_wallet += $request->amount;
        $user->save();
        return redirect()->back()->with('toast', 'Successful Transfer Account To Wallet!');
    }

    public function account_to_account(Request $request)
    {
        $conn = (new CTraderService)->connectionStatus();
        if ($conn['code'] != 0) {
            if ($conn['code'] == 10) {
                return response()->json(['success' => false, 'message' => 'No connection with cTrader Server']);
            }
            return response()->json(['success' => false, 'message' => $conn['message']]);
        }

        $request->validate([
            'account_no_1' => 'required|string',
            'account_no_2' => 'required|string|different:account_no_1',
            'amount' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $tradingUser = TradingUser::firstWhere('meta_login', $request->account_no_1);
        (new CTraderService)->getUserInfo([$tradingUser]);
        $tradingUser = TradingUser::firstWhere('meta_login', $request->account_no_1);

        if ($tradingUser->balance < $request->amount) {
            throw ValidationException::withMessages(['amount' => trans('Insufficient balance')]);
        }

        $payment_id = RunningNumberService::getID('transaction');
        try {
            $trade_1 = (new CTraderService)->createTrade($request->account_no_1, $request->amount, "Account To Account", ChangeTraderBalanceType::WITHDRAW);
        } catch (\Throwable $e) {
            if ($e->getMessage() == "Not found") {
                TradingUser::firstWhere('meta_login', $request->account_no_1)->update(['acc_status' => 'Inactive']);
            } else {
                Log::error($e->getMessage());
            }
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        try {
            $trade_2 = (new CTraderService)->createTrade($request->account_no_2, $request->amount, "Account To Account", ChangeTraderBalanceType::DEPOSIT);
        } catch (\Throwable $e) {
            if ($e->getMessage() == "Not found") {
                TradingUser::firstWhere('meta_login', $request->account_no_2)->update(['acc_status' => 'Inactive']);
            } else {
                Log::error($e->getMessage());
            }
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        $ticket = $trade_1->getTicket() . ', ' . $trade_2->getTicket();

        Payment::create([
            'user_id' => $user->id,
            'payment_id' => $payment_id,
            'category' => 'internal_transfer',
            'type' => 'AccountToAccount',
            'from' => $request->account_no_1,
            'to' => $request->account_no_2,
            'amount' => $request->amount,
            'ticket' => $ticket,
            'status' => 'Successful',

        ]);
        return redirect()->back()->with('toast', 'Successfully Transfer Account to Account');
    }
}
