<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RebateAllocationRequest extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function requestOfUser()
    {
        return $this->belongsTo(User::class, 'requestBy', 'id');
    }
    public function handleOfUser()
    {
        return $this->belongsTo(User::class, 'handleBy', 'id');
    }
    public function rebateAllocation()
    {
        return $this->hasMany(RebateAllocation::class, 'request_id', 'id');
    }
}
