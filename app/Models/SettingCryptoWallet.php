<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SettingCryptoWallet extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'setting_crypto_wallets';

    protected $fillable = [
        'name', 'symbol', 'gateway', 'wallet_address', 'status', 'min_deposit'
    ];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
}
