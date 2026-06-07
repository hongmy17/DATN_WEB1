<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        $defaultAddress = null;
        $addresses = collect();

        if (Auth::check()) {
            $addresses = Auth::user()->addresses()->latest()->get();
            $defaultAddress = $addresses->firstWhere('is_default', true)
                ?? $addresses->first();
        }

        return view('pages.checkout.index', compact('defaultAddress', 'addresses'));
    }
}