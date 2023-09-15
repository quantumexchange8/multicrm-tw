<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GatewayExchangeRate extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        'deposit_rate' => 'decimal:2',
        'withdrawal_rate' => 'decimal:2',
        'deposit_charge_amount' => 'decimal:2',
        'withdrawal_charge_amount' => 'decimal:2',
        'delete' => 'boolean',
    ];

    public function ofGateway()
    {
        return $this->belongsTo(GatewayList::class, 'gateway', 'id');
    }
}
