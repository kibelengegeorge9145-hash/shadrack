<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;

class ChildController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $parent = $user->parent;
        $children = $parent->students()->with(['schoolClass', 'user'])->get();

        return view('parent.children', compact('parent', 'children'));
    }
}
