<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PaymentAccount extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, LogsActivity;

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        $payment_account = $this->fresh();

        return LogOptions::defaults()
            ->useLogName('payment_account')
            ->logOnly(['user_id', 'payment_account_name', 'payment_platform', 'payment_platform_name', 'account_no', 'bank_branch_address', 'bank_swift_code', 'bank_code', 'bank_code_type', 'country', 'currency'])
            ->setDescriptionForEvent(function (string $eventName) use ($payment_account) {
                $actorName = Auth::user() ? Auth::user()->first_name : 'New Registered User - ' . $payment_account->user_id;
                return "{$actorName} has {$eventName} payment_account_id of {$payment_account->id}.";
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
