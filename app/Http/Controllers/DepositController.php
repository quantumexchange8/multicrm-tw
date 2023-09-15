<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\DepositRequest;
use App\Models\Deposit;

class DepositController extends Controller
{
    public function store(DepositRequest $request)
    {

        Deposit::create([
            'deposit_method' => $request->deposit_method,
            'deposit_amount' => $request->deposit_amount,
            'txid' => $request->txid,
        ]);


        return redirect()->back();
    }
}
