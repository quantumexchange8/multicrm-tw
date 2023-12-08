<?php

namespace App\Http\Controllers;

use App\Models\AccountTypeSymbolGroup;
use App\Models\IbAccountTypeSymbolGroupRate;
use App\Models\Payment;
use App\Models\RebateAllocation;
use App\Models\RebateAllocationRate;
use App\Models\SettingCountry;
use App\Models\TradingUser;
use App\Services\CTraderService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\AccountType;
use App\Models\IbAccountType;
use App\Models\User;

class NetworkController extends Controller
{
    public function network(Request $request)
    {
        return Inertia::render('GroupNetwork/NetworkTree');
    }

    // new tree testing
    public function getTreeData(Request $request)
    {
        $searchUser = null;
        $searchTerm = $request->input('search');

        if ($searchTerm) {
            $searchUser = User::where('first_name', 'like', '%' . $searchTerm . '%')
                ->orWhere('email', 'like', '%' . $searchTerm . '%')
                ->first();

            if (!$searchUser) {
                return response()->json(['error' => 'User not found for the given search term.'], 404);
            }
        }

        $user = $searchUser ?? Auth::user();

        $users = User::whereHas('upline', function ($query) use ($user) {
            $query->where('id', $user->id);
        })->get();

//        if ($searchUser) {
//            $query->orWhere('id', $searchUser->id);
//        }
//
//        $users = $query->get();

        $level = 0;
        $rootNode = [
            'name' => $user->first_name,
            'profile_photo' => $user->getFirstMediaUrl('profile_photo'),
            'email' => $user->email,
            'level' => $level,
            'total_ib' => count($user->getIbUserIds()),
            'total_member' => count($user->getMemberUserIds()),
            'role' => $user->role,
            'total_group_deposit' => $this->getTotalGroupDeposit($user),
            'total_group_withdrawal' => $this->getTotalGroupWithdrawal($user),
            'children' => $users->map(function ($user) {
                return $this->mapUser($user, 0);
            })
        ];

        return response()->json($rootNode);
    }

    protected function mapUser($user, $level) {
        $children = $user->children;

        $mappedChildren = $children->map(function ($child) use ($level) {
            return $this->mapUser($child, $level + 1);
        });

        $mappedUser = [
            'name' => $user->first_name,
            'profile_photo' => $user->getFirstMediaUrl('profile_photo'),
            'email' => $user->email,
            'level' => $level + 1,
            'total_ib' => count($user->getIbUserIds()),
            'total_member' => count($user->getMemberUserIds()),
            'role' => $user->role,
            'total_group_deposit' => $this->getTotalGroupDeposit($user),
            'total_group_withdrawal' => $this->getTotalGroupWithdrawal($user),
        ];

        // Add 'children' only if there are children
        if (!$mappedChildren->isEmpty()) {
            $mappedUser['children'] = $mappedChildren;
        }

        return $mappedUser;
    }

    protected function getTotalGroupWithdrawal($user)
    {
        $users = $user->getChildrenIds();
        $users[] = $user->id;

        return Payment::query()
            ->whereIn('user_id', $users)
            ->where('type', '=', 'Withdrawal')
            ->where('status', '=', 'Successful')
            ->sum('amount');
    }

    protected function getTotalGroupDeposit($user)
    {
        $users = $user->getChildrenIds();
        $users[] = $user->id;

        return Payment::query()
            ->whereIn('user_id', $users)
            ->where('type', '=', 'Deposit')
            ->where('status', '=', 'Successful')
            ->sum('amount');
    }

    //end testing

    public function getRebateAllocation()
    {
        $user = Auth::user();
        $search = \Request::input('search');

        $ib = IbAccountType::where('user_id', $user->id)->with(['ofUser', 'symbolGroups.symbolGroup', 'accountType'])->first();

        $childrenIds = $ib->getIbChildrenIds();

        $query = IbAccountType::whereIn('id', $childrenIds)
            ->with(['ofUser', 'symbolGroups.symbolGroup', 'accountType']);

        if ($search) {
            $query->whereHas('ofUser', function ($userQuery) use ($search) {
                $userQuery->where('first_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $children = $query->get();

        $children->each(function ($child) {
            $profilePicUrl = $child->ofUser->getFirstMediaUrl('profile_photo');
            $child->profile_pic = $profilePicUrl;
        });

        return Inertia::render('GroupNetwork/RebateAllocation', [
            'ib' => $ib,
            'children' => $children,
            'filters' => \Request::only(['search'])
        ]);
    }

    public function updateRebateAllocation(Request $request)
    {
        $curIb = IbAccountType::find($request->user_id);
        $upline = IbAccountType::where('user_id', Auth::id())->first();
        $downline = $curIb->downline;
        $curIbRate = IbAccountTypeSymbolGroupRate::where('ib_account_type', $request->user_id)->get()->keyBy('symbol_group');

        $validationErrors = new MessageBag();

        // Collect the validation errors for ibGroupRates
        foreach ($request->ibGroupRates as $key => $amount) {
            $parent = IbAccountTypeSymbolGroupRate::with('symbolGroup')
                ->where('ib_account_type', $upline->id)
                ->where('symbol_group', $key)
                ->first();

            if ($parent && $amount > $parent->amount) {
                $fieldKey = 'ibGroupRates.' . $key;
                $errorMessage = $parent->symbolGroup->name . ' amount cannot be higher than ' . $parent->amount;
                $validationErrors->add($fieldKey, $errorMessage);
            }
        }

        // Collect the validation errors for each downline's ibGroupRates
        foreach ($downline as $child) {
            foreach ($request->ibGroupRates as $key => $amount) {
                $childRate = IbAccountTypeSymbolGroupRate::with('symbolGroup')
                    ->where('ib_account_type', $child->id)
                    ->where('symbol_group', $key)
                    ->first();

                if ($childRate && $amount < $childRate->amount) {
                    $fieldKey = 'ibGroupRates.' . $key;
                    $errorMessage = $childRate->symbolGroup->name . ' amount cannot be lower than ' . $childRate->amount;
                    $validationErrors->add($fieldKey, $errorMessage);
                }
            }
        }

        // If there are validation errors, return them in the response
        if ($validationErrors->count() > 0) {
            return redirect()->back()->withErrors($validationErrors);
        }

        $rebateAllocation = RebateAllocation::create(['from' => $curIb->upline_id, 'to' => $request->user_id]);

        foreach ($request->ibGroupRates as $key => $amount) {

            RebateAllocationRate::create([
                'allocation_id' => $rebateAllocation->id,
                'symbol_group' => $key,
                'old' => $curIbRate[$key]->amount,
                'new' => $amount
            ]);

            $curIbRate[$key]->update(['amount' => $amount]);

        }

        return redirect()->back()->with('toast', 'The rebate allocation has been saved!');
    }

    public function downline_info(Request $request)
    {
        return Inertia::render('GroupNetwork/DownlineInfo', [
            'filters' => \Request::only(['search', 'role']),
        ]);
    }

    public function getDownlineInfo(Request $request)
    {
        $user = Auth::user();

        $members = User::query()
            ->whereIn('id', $user->getChildrenIds())
            ->whereIn('role', ['member', 'ib'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('tradingAccounts', function ($accQuery) use ($search) {
                            $accQuery->where('meta_login', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                $role = $request->input('role');
                $query->where('role', $role);
            })
            ->with(['tradingAccounts:user_id,meta_login,balance,credit,equity', 'upline:id,email'])
            ->select('id', 'first_name', 'email', 'created_at', 'role', 'upline_id', 'cash_wallet')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return response()->json($members);
    }
}
