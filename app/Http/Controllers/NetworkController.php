<?php

namespace App\Http\Controllers;

use App\Models\AccountTypeSymbolGroup;
use App\Models\IbAccountTypeSymbolGroupRate;
use App\Models\RebateAllocation;
use App\Models\RebateAllocationRate;
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
    private function buildTree($users, $isRoot = false, $parentLevel = 0) {
        $tree = [];
        foreach ($users as $user) {
            $level = $isRoot ? $parentLevel : $parentLevel + 1;

            $userNode = [
                'name' => $user->first_name,
                'profile_photo' => $user->getFirstMediaUrl('profile_photo'),
                'total_group_deposit' => $user->totalGroupDeposit($user->id),
                'total_group_withdrawal' => $user->totalGroupWithdrawal($user->id),
                'email' => $user->email,
                'role' => $user->role,
                'level' => $level,
                'total_ib' => count($user->getIbUserIds()),
                'total_member' => count($user->getMemberUserIds()),
            ];

            if ($user->downline->isNotEmpty()) {
                // Pass the correct arguments: $user->downline for users and $isRoot remains the same
                // Also, increment the level for the recursive call
                $userNode['children'] = $this->buildTree($user->downline, false, $level);
            }

            // If it's the root node and has only one child with no further children,
            // return its single child directly instead of an array containing the child
            if ($isRoot && count($tree) === 0 && isset($userNode['children'])) {
                return $userNode['children'];
            }

            $tree[] = $userNode;
        }

        return $tree;
    }

    protected function searchChildren($user, $search)
    {
        $filteredChildren = collect();

        foreach ($user->downline as $child) {
            if (str_contains($child->first_name, $search) || str_contains($child->email, $search)) {
                $filteredChildren->push($child);
            }

            $nestedFiltered = $this->searchChildren($child, $search);
            if ($nestedFiltered->count() > 0) {
                $child->downline = $nestedFiltered;
                $filteredChildren->push($child);
            }
        }

        return $filteredChildren;
    }

    public function network(Request $request)
    {
        $user = Auth::user();

        $usersQuery = User::where('id', $user->id)
            ->orWhereHas('upline', function ($query) use ($user) {
                $query->where('id', $user->id);
            });

        $users = $usersQuery->get();

        if ($request->filled('search')) {
            $search = $request->input('search');

            $filteredUsers = collect();
            foreach ($users as $user) {
                if (str_contains($user->first_name, $search) || str_contains($user->email, $search)) {
                    $filteredUsers->push($user);
                }

                $filteredChildren = $this->searchChildren($user, $search);
                if ($filteredChildren->count() > 0) {
                    $user->downline = $filteredChildren;
                    $filteredUsers->push($user);
                }
            }

            $users = $filteredUsers;
        }

        if ($users->isEmpty()) {
            // For view = no or no upline and no users, set the root node to an empty array
            $rootNode = [
                'name' => 'No Records'
            ];
        } else {
            // For view = no or no upline, set the root node to the current user's data
            $rootNode = [
                'name' => $users->first()->first_name,
                'profile_photo' => $users->first()->getFirstMediaUrl('profile_photo'),
                'total_group_deposit' => $users->first()->totalGroupDeposit($users->first()->id),
                'total_group_withdrawal' => $users->first()->totalGroupWithdrawal($users->first()->id),
                'total_ib' => count($users->first()->getIbUserIds()),
                'total_member' => count($users->first()->getMemberUserIds()),
                'email' => $users->first()->email,
                'role' => $users->first()->role,
                'level' => 1,
                'children' => []
            ];

            $tree = $this->buildTree($users, 1, 1);
            $rootNode['children'] = $tree;
        }

        return Inertia::render('GroupNetwork/NetworkTree', [
            'root' => $rootNode,
            'filters' => $request->only('search')
        ]);
    }

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

            if ($parent && $amount >= $parent->amount) {
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
}
