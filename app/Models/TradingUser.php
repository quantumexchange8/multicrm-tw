<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TradingUser extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'balance' => 'decimal:2',
        'credit' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'commission_daily' => 'decimal:2',
        'commission_montly' => 'decimal:2',
        'balance_prev_day' => 'decimal:2',
        'balance_prev_month' => 'decimal:2',
        'equity_prev_day' => 'decimal:2',
        'equity_prev_month' => 'decimal:2',
        'created_at' => 'datetime:Y-m-d',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        $trading_user = $this->fresh();

        return LogOptions::defaults()
            ->useLogName('trading_user')
            ->logOnly(['user_id', 'meta_login', 'meta_group', 'balance', 'credit', 'leverage', 'module', 'last_access', 'acc_status', 'remarks'])
            ->setDescriptionForEvent(function (string $eventName) use ($trading_user) {
                $actorName = Auth::user() ? Auth::user()->first_name : 'New Registered User - ' . $trading_user->meta_login;
                return "{$actorName} has {$eventName} trading user of {$trading_user->meta_login}.";
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function ofUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
