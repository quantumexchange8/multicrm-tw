<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class IbAccountType extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'rebate_wallet' => 'decimal:2',
        'trade_lot' => 'decimal:2',
        'created_at' => 'datetime:Y-m-d',
    ];

    public function getIbChildrenIds()
    {
        $ibUsers = IbAccountType::query()->where('hierarchyList', 'like', '%-' . $this->id . '-%')
            ->pluck('id')->toArray();

        return $ibUsers;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $ib_account_type = $this->fresh();

        return LogOptions::defaults()
            ->useLogName('ib_account_type')
            ->logOnly(['user_id', 'upline_id', 'hierarchyList', 'account_type', 'rebate_wallet', 'trade_lot'])
            ->setDescriptionForEvent(function (string $eventName) use ($ib_account_type) {
                $actorName = Auth::user() ? Auth::user()->first_name : 'New Registered User - ' . $ib_account_type->user_id;
                return "{$actorName} has {$eventName} ib_account_type_id of {$ib_account_type->id}.";
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function ofUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function symbolGroups()
    {
        return $this->hasMany(IbAccountTypeSymbolGroupRate::class, 'ib_account_type', 'id');
    }

    public function accountType()
    {
        return $this->belongsTo(AccountType::class, 'account_type', 'id');
    }
    public function children()
    {
        return $this->hasMany(IbAccountType::class, 'referral', 'ib_id');
    }
    public function parent()
    {
        return $this->belongsTo(IbAccountType::class, 'referral', 'ib_id');
    }
    public function downline()
    {
        return $this->hasMany(IbAccountType::class, 'upline_id', 'id');
    }
    public function upline()
    {
        return $this->belongsTo(IbAccountType::class, 'upline_id', 'id');
    }
}
