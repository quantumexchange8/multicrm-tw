<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\DepositRequest;
use App\Http\Requests\Payment\WithdrawalRequest;
use App\Models\GatewayExchangeRate;
use App\Models\IbAccountType;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\SettingCryptoWallet;
use App\Models\TradingAccount;
use App\Models\TradingUser;
use App\Models\User;
use App\Notifications\DepositRequestNotification;
use App\Services\ChangeTraderBalanceType;
use App\Services\CTraderService;
use App\Services\RunningNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class PaymentController extends Controller
{
    public function deposit(DepositRequest $request)
    {
        $conn = (new CTraderService)->connectionStatus();
        if ($conn['code'] != 0) {
            if ($conn['code'] == 10) {
                return back()->withErrors(['connection' => ['No connection with cTrader Server']]);
            }
            return back()->withErrors(['connection' => [$conn['message']]]);
        }
        /*  $date = date('Y/m/d h:i:s a', time());
        $date = (int) filter_var($date, FILTER_SANITIZE_NUMBER_INT);
        $payment_id = "DEMOORDER_" . $date; */

        $meta_login = $request->account_no;
        $amount = number_format($request->amount, 2, '.', '');

        $payment_id = RunningNumberService::getID('transaction');

        $currency = $request->currency;

        $payment_charges = null;
        $real_amount = $amount;
        $user = Auth::user();

        $payment = Payment::create([
            'to' => $meta_login,
            'user_id' => $user->id,
            'category' => 'payment',
            'payment_id' => $payment_id,
            'type' => 'Deposit',
            'channel' => $request->deposit_method,
            'TxID' => $request->txid,
            'comment' => 'Deposit',
            'amount' => $amount,
            'currency' => $currency,
            'description' => $request->description,
            'real_amount' => $real_amount,
            'payment_charges' => $payment_charges,
        ]);

        if ($request->hasFile('payment_receipt')) {
            $payment->addMedia($request->payment_receipt)->toMediaCollection('payment_receipt');
        }

        Notification::route('mail', 'payment@currenttech.pro')
            ->notify(new DepositRequestNotification($payment, $user));

        return redirect()->back()->with('toast', 'Successfully submitted your deposit request');

    }

    public function depositResult(Request $request)
    {
        $data = $request->all();
        Log::debug($data);

        return Inertia::render('Dashboard');
    }

    public function updateResult(Request $request)
    {
        $data = $request->all();
        Log::debug($data);
        $result = [
            "Info" => $data['Info'],
            "MerchantCode" => $data['MerchantCode'],
            "SerialNo" => $data["SerialNo"],
            "CurrencyCode" => $data["CurrencyCode"],
            "Amount" => $data["Amount"],
            "Status" => $data['Status'],
            "Token" => $data["Token"],
        ];
        Log::debug($result);

        // Get the currency from the data
        $currency = $data['CurrencyCode'];

        // Get the currency configuration based on the provided currency code
        $currencyConfig = config('currency_setting');

        if ($result["Token"] == md5($result['SerialNo'] . $currencyConfig[$currency]['apiKey'] . $currencyConfig[$currency]['secretKey'])) {
            $payment = Payment::query()->where('payment_id', Str::upper($result['SerialNo']))->first();
            if ($payment->status == "Submitted" || $payment->status == "Processing") {
                if ($result['Status'] == 1) {
                    try {
                        $trade = (new CTraderService)->createTrade($payment->to, $payment->amount, $payment->comment, ChangeTraderBalanceType::DEPOSIT);
                        $payment->ticket = $trade->getTicket();

                        $user = User::find($payment->user_id);
                        $user->total_deposit += $payment->amount;
                        $user->save();
                    } catch (\Throwable $e) {
                        if ($e->getMessage() == "Not found") {
                            TradingUser::firstWhere('meta_login', $payment->to)->update(['acc_status' => 'Inactive']);
                        }
                        Log::error($e->getMessage() . " " . $payment->payment_id);
                    }
                }
                $payment->status = $this->Status[$result['Status']];
                $payment->save();
            }
        }
    }

    public function requestWithdrawal(WithdrawalRequest $request)
    {
        $user = Auth::user();
        $amount = floatval($request->amount);
        if ($user->cash_wallet < $amount) {
            throw ValidationException::withMessages(['amount' => trans('Insufficient balance')]);
        }
        $user->cash_wallet -= $amount;
        $user->save();
        $payment_id = RunningNumberService::getID('transaction');

        $currency = PaymentAccount::query()
            ->where('account_no', $request->account_no)
            ->select('currency')
            ->first();

        Payment::create([
            'user_id' => $user->id,
            'payment_id' => $payment_id,
            'category' => 'payment',
            'type' => 'Withdrawal',
            'channel' => $request->channel,
            'amount' => $amount,
            'account_no' => $request->account_no,
            'account_type' => $request->account_type,
            'currency' => $currency->currency,
        ]);

        return back()->with('toast', 'Successfully Submitted Withdrawal Request');
    }

    public function applyRebate(Request $request)
    {
        $accountType = IbAccountType::where('user_id', Auth::id())->where('account_type', $request->account_type)->with('accountType')->first();
        $user = User::find(Auth::id());
        if ($accountType->rebate_wallet <= 0) {
            return response()->json([
                'success' => false,
                'message' => trans('Insufficient balance to apply the rebate. You have not earned any rebate yet.')
            ], 422);
        }

        $payment_id = RunningNumberService::getID('transaction');

        Payment::create([
            'user_id' => $user->id,
            'payment_id' => $payment_id,
            'category' => 'rebate_payout',
            'type' => 'RebateToWallet',
            'amount' => $accountType->rebate_wallet,
            'status' => 'Successful',
        ]);
        $user->cash_wallet = number_format($user->cash_wallet + $accountType->rebate_wallet, 2, '.', '');
        $user->save();
        $accountType->update(['rebate_wallet' => 0, 'trade_lot' => 0]);

        return response()->json([
            'success' => true,
            'message' => 'Congratulation, we have received your rebate request. The rebate will be transferred to your cash wallet shortly. Once processed, you will be able to withdraw or transfer your funds.',
            'cash_wallet' => $user->cash_wallet,
            'rebate_wallet' => $accountType->rebate_wallet
        ]);
    }

    public function deposit_approval(Request $request)
    {
        $payment = Payment::find($request->id);

        if ($payment->status != 'Submitted') {
            return redirect()->back()->with('toast', 'It appears you have already completed approval action');
        }

        $status = $request->status == "approve" ? "Successful" : "Rejected";
        $payment->status = $status;
        $payment->description = 'Deposit from Email Notification';
        $payment->approval_date = Carbon::today();
        $payment->save();

        if ($payment->status == "Successful") {
            try {
                $trade = (new CTraderService)->createTrade($payment->to, $payment->amount, $payment->comment, ChangeTraderBalanceType::DEPOSIT);
                $payment->ticket = $trade->getTicket();

                $user = User::find($payment->user_id);
                $user->total_deposit += $payment->amount;
                $user->save();

                return redirect()->back()->with('toast', 'Successfully Approved Deposit Request');
            } catch (\Throwable $e) {
                if ($e->getMessage() == "Not found") {
                    TradingUser::firstWhere('meta_login', $payment->to)->update(['acc_status' => 'Inactive']);
                }
                \Log::error($e->getMessage() . " " . $payment->payment_id);
            }
        }

        return redirect()->back()->with('toast', 'Successfully Rejected Deposit Request');
    }
}
