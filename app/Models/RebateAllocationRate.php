<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class RebateAllocationRate extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = [];
    protected $casts = [
        'old' => 'decimal:2',
        'new' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        $rebate_allocation_rate = $this->fresh();

        return LogOptions::defaults()
            ->useLogName('rebate_allocation_rate')
            ->logOnly(['allocation_id', 'symbol_group', 'old', 'new'])
            ->setDescriptionForEvent(function (string $eventName) use ($rebate_allocation_rate) {
                return Auth::user()->first_name . " has {$eventName} rebate_allocation_id of {$rebate_allocation_rate->allocation_id}.";
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function rebateAllocation()
    {
        return $this->belongsTo(RebateAllocation::class, 'allocation_id', 'id');
    }
    public function symbolGroup()
    {
        return $this->belongsTo(SymbolGroup::class, 'symbol_group', 'id');
    }
}
