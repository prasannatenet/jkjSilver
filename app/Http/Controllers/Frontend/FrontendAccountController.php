<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontendAccountController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $orders = $user->orders()
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('account.index', compact('user', 'orders'));
    }
}
