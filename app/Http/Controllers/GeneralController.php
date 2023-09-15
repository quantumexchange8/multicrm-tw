<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class GeneralController extends Controller
{
    public function monthly_deposit()
    {
        $monthly_deposit = Payment::query()
            ->where('user_id', Auth::id())
            ->where('category', '=', 'payment')
            ->where('type', '=', 'Deposit')
            ->where('status', '=', 'Successful')
            ->whereBetween('created_at', [
                now()->startOfMonth(), // Start of the current month
                now()->endOfMonth(),   // End of the current month
            ])
            ->sum('amount');

        return response()->json([
            'monthly_deposit' => $monthly_deposit,
        ]);
    }

    public function setLang(Request $request, $locale)
    {
        App::setLocale($locale);
        Session::put("locale", $locale);

        return redirect()->back();
    }
}
