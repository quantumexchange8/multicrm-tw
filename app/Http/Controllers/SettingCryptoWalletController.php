<?php

namespace App\Http\Controllers;

use App\Models\SettingCryptoWallet;
use Illuminate\Http\Request;

class SettingCryptoWalletController extends Controller
{
    public function store(Request $request)
    {
        $setting_crypto_wallet = SettingCryptoWallet::create([
           'name' => $request->name,
           'symbol' => $request->symbol,
           'gateway' => 'crypto',
           'wallet_address' => $request->wallet_address,
        ]);

        $setting_crypto_wallet->addMedia($request->crypto_image)->toMediaCollection('setting_crypto_wallet');
        $setting_crypto_wallet->addMedia($request->crypto_qr)->toMediaCollection('setting_crypto_qr');

        return 'success';
    }
}
