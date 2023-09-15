<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class RebateAllocation extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        $rebate_allocation = $this->fresh();

        return LogOptions::defaults()
            ->useLogName('rebate_allocation')
            ->logOnly(['request_id', 'from', 'to', 'status'])
            ->setDescriptionForEvent(function (string $eventName) use ($rebate_allocation) {
                return Auth::user()->first_name . " has {$eventName} rebate_allocation_id of {$rebate_allocation->id}.";
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function request()
    {
        return $this->belongsTo(RebateAllocationRequest::class, 'request_id', 'id');
    }

    public function allocationRates()
    {
        return $this->hasMany(RebateAllocationRate::class, 'allocation_id', 'id');
    }
    public function fromIbAccountType()
    {
        return $this->belongsTo(IbAccountType::class, 'from', 'id');
    }
    public function toIbAccountType()
    {
        return $this->belongsTo(IbAccountType::class, 'to', 'id');
    }

    public function toOfUser()
    {
        return $this->hasManyThrough(User::class, IbAccountType::class, 'to', 'user_id', 'id', 'id');
    }
}
