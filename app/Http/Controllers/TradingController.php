<?php

namespace App\Http\Controllers;

use App\Models\SymbolGroup;
use App\Models\TradingRebateSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class TradingController extends Controller
{
    public function rebate_summary()
    {
        return Inertia::render('Trading/RebateSummary', [
            'symbolGroups' => SymbolGroup::all(),
        ]);
    }

    public function getRebateSummary(Request $request): \Illuminate\Http\JsonResponse
    {
        $totalBySymbolGroup = TradingRebateSummary::query()
            ->where('upline_user_id', auth()->id())
            ->with('user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                        ->orWhere('meta_login', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $date = $request->input('date');
                $start_date = Carbon::createFromFormat('Y-m-d', $date[0])->startOfDay();
                $end_date = Carbon::createFromFormat('Y-m-d', $date[1])->endOfDay();
                $query->whereBetween('closed_time', [$start_date, $end_date]);
            })
            ->select('symbol_group')
            ->selectRaw('SUM(volume) as total_volume')
            ->selectRaw('SUM(rebate) as total_rebate')
            ->groupBy('symbol_group')
            ->get();

        $trading_summary = TradingRebateSummary::query()
            ->select('upline_user_id', 'closed_time', 'user_id', 'meta_type', 'meta_login')
            ->selectRaw('SUM(volume) as total_volume')
            ->selectRaw('SUM(rebate) as total_rebate');

        $symbolGroups = SymbolGroup::all(); // Fetch all symbol groups

        foreach ($symbolGroups as $symbolGroup) {
            $symbolGroupId = $symbolGroup->id;
            $symbolGroupName = $symbolGroup->name;

            $trading_summary->selectRaw("SUM(CASE WHEN symbol_group = $symbolGroupId THEN volume ELSE 0 END) as total_volume_$symbolGroupId");
            $trading_summary->selectRaw("SUM(CASE WHEN symbol_group = $symbolGroupId THEN rebate ELSE 0 END) as total_rebate_$symbolGroupId");

            // Optionally, you can also include the symbol group name as a select
//            $trading_summary->selectRaw("'$symbolGroupName' as symbol_group_name_$symbolGroupId");
        }

        $trading_summary = $trading_summary
            ->where('upline_user_id', \Auth::id())
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                        ->orWhere('meta_login', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $date = $request->input('date');
                $start_date = Carbon::createFromFormat('Y-m-d', $date[0])->startOfDay();
                $end_date = Carbon::createFromFormat('Y-m-d', $date[1])->endOfDay();
                $query->whereBetween('closed_time', [$start_date, $end_date]);
            })
            ->groupBy('upline_user_id', 'user_id', 'meta_type', 'meta_login', 'closed_time')
            ->orderByDesc('closed_time')
            ->paginate(10);

        // Load relationships
        $trading_summary->load(['user', 'account_type']);

        return response()->json([
            'totalBySymbolGroup' => $totalBySymbolGroup,
            'tradingSummary' => $trading_summary,
        ]);
    }
}
