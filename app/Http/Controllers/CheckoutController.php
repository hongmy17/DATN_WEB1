<?php
namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

        // Load danh sách mã giảm giá còn hiệu lực
        $coupons = Coupon::where('status', 1)
            ->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->where(function($q) {
                $q->whereNull('max_usage')
                  ->orWhereColumn('used_count', '<', 'max_usage');
            })
            ->get();

        return view('pages.checkout.index', compact('defaultAddress', 'addresses', 'coupons'));
    }
}