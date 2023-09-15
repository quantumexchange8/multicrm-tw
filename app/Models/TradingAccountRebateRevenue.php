<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TradingAccountRebateRevenue extends Model
{
    use HasFactory, LogsActivity;
    protected $table = 'trading_account_rebate_revenue';

    protected $fillable = ['status'];

    public function getActivitylogOptions(): LogOptions
    {
        $trading_account_rebate_revenue = $this->fresh();

        return LogOptions::defaults()
            ->useLogName('trading_account_rebate_revenue')
            ->logOnly(['status'])
            ->setDescriptionForEvent(function (string $eventName) use ($trading_account_rebate_revenue) {
                return Auth::user()->first_name . " has {$eventName} trading_account_rebate_revenue of {$trading_account_rebate_revenue->id}.";
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function ofUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function ofTicketUser()
    {
        return $this->belongsTo(User::class, 'ticket_user_id', 'id');
    }
    public function ofAccountType()
    {
        return $this->belongsTo(AccountType::class, 'account_type', 'id');
    }
}
