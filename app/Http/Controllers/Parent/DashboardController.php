<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Services\GradingService;

class DashboardController extends Controller
{
    public function index(GradingService $gradingService)
    {
        $user = auth()->user();
        $parent = $user->parent;
        $children = $parent->students()->with(['schoolClass', 'user'])->get();

        $totalBalance = 0;
        $resultsCount = 0;

        foreach ($children as $child) {
            $totalBalance += (float) $child->studentFees()->sum('balance');
            $resultsCount += $child->results()->published()->count();
        }

        return view('parent.dashboard', compact('parent', 'children', 'totalBalance', 'resultsCount'));
    }
}
