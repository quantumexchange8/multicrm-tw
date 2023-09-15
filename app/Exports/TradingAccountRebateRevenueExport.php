<?php

namespace App\Exports;

use App\Models\TradingAccountRebateRevenue;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TradingAccountRebateRevenueExport implements FromCollection,WithHeadings
{
    private $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $records = $this->query->latest()->get();
        $result = array();
        foreach($records as $ib_rebate){
            $result[] = array(
                'account_no' => $ib_rebate->meta_login,
                'account_type' => $ib_rebate->ofAccountType->name,
                'ticket_user_name' => $ib_rebate->ofTicketUser->first_name,
                'symbol' =>  strtoupper($ib_rebate->symbol),
                'volume' => strtoupper($ib_rebate->volume),
                'final_net_rebate' => $ib_rebate->final_net_rebate,
                'ticket' =>  $ib_rebate->ticket,
                'revenue' => number_format((float)$ib_rebate->revenue, 2, '.', ''),
            );
        }

        return collect($result);
    }

    public function headings(): array
    {
        return [
            'Acc No',
            'Acc Type',
            'Client Name',
            'Symbol',
            'Volume (Lot)',
            'Rebate / Lot',
            'Ticket No',
            'Revenue',
        ];
    }
}
