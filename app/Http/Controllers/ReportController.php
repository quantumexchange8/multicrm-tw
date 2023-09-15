<?php

namespace App\Http\Controllers;

use App\Exports\TradingAccountRebateRevenueExport;
use App\Models\RebateAllocationRate;
use App\Models\TradingAccountRebateRevenue;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function listing()
    {
        return Inertia::render('Report/Report');
    }

    public function getRevenueReport(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
    {
        $user = \Auth::user();

        $ibRebate = TradingAccountRebateRevenue::where('status', 'approve')
            ->where('user_id', $user->id)
            ->when($request->filled('date'), function ($query) use ($request) {
                $date = $request->input('date');
                $start_date = Carbon::createFromFormat('Y-m-d', $date[0])->startOfDay();
                $end_date = Carbon::createFromFormat('Y-m-d', $date[1])->endOfDay();
                $query->whereBetween('closed_time', [$start_date, $end_date]);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->whereHas('ofTicketUser', function ($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                        ->orWhere('meta_login', 'like', '%' . $search . '%');
                });
            })
            ->with(['ofAccountType', 'ofTicketUser']);

        if ($request->has('export')) {
            return Excel::download(new TradingAccountRebateRevenueExport($ibRebate), Carbon::now() . '-ib-rebate-report.xlsx');
        }

        $ibTotalRebate = clone $ibRebate;

        return response()->json([
            'ibRebateReport' => $ibRebate->latest()->paginate(10),
            'ibTotalRebate' => $ibTotalRebate->get()->sum('revenue'),
        ]);
    }
}
