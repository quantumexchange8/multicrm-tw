<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TradingAccount extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'balance' => 'decimal:2',
        'credit' => 'decimal:2',
        'margin' => 'decimal:2',
        'margin_free' => 'decimal:2',
        'margin_level' => 'decimal:2',
        'profit' => 'decimal:2',
        'storage' => 'decimal:2',
        'commission' => 'decimal:2',
        'floating' => 'decimal:2',
        'equity' => 'decimal:2',
        'so_level' => 'decimal:2',
        'so_equity' => 'decimal:2',
        'so_margin' => 'decimal:2',
        'assets' => 'decimal:2',
        'liabilities' => 'decimal:2',
        'blocked_commission' => 'decimal:2',
        'blocked_profit' => 'decimal:2',
        'margin_initial' => 'decimal:2',
        'margin_maintenance' => 'decimal:2',
        'created_at' => 'datetime:Y-m-d',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        $trading_account = $this->fresh();

        return LogOptions::defaults()
            ->useLogName('trading_account')
            ->logOnly(['user_id', 'meta_login', 'currency_digits', 'balance', 'credit', 'margin_leverage', 'equity', 'account_type'])
            ->setDescriptionForEvent(function (string $eventName) use ($trading_account) {
                $actorName = Auth::user() ? Auth::user()->first_name : 'User Meta Acc No - ' . $trading_account->meta_login;
                return "{$actorName} has {$eventName} trading account of {$trading_account->meta_login}.";
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function ofUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function accountType()
    {
        return $this->belongsTo(AccountType::class, 'account_type', 'id');
    }
    public function tradingUser()
    {
        return $this->hasOne(TradingUser::class, 'meta_login', 'meta_login');
    }
}
