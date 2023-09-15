<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'real_amount' => 'decimal:2',
        'delete' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        $payment = $this->fresh();

        return LogOptions::defaults()
            ->useLogName('payment')
            ->logOnly(['from', 'to', 'user_id', 'ticket', 'payment_id', 'category', 'type', 'amount', 'gateway', 'status', 'channel', 'currency', 'description', 'account_no', 'account_type', 'approval_date', 'comment', 'real_amount', 'payment_charges', 'handleBy'])
            ->setDescriptionForEvent(function (string $eventName) use ($payment) {
                $actorName = Auth::user() ? Auth::user()->first_name : 'User with ID - ' . $payment->user_id;
                return "{$actorName} has {$eventName} payment_id of {$payment->payment_id}.";
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function ofUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function toAccount()
    {
        return $this->belongsTo(TradingAccount::class, 'to', 'meta_login');
    }
    public function fromAccount()
    {
        return $this->belongsTo(TradingAccount::class, 'from', 'meta_login');
    }}
