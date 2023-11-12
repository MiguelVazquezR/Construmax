<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use Illuminate\Http\Request;

class CRMController extends Controller
{
    public function dashboard()
    {
        $is_super_admin = auth()->user()->hasRole('Super admin');

        $month_opportunities = Opportunity::whereMonth('created_at', today())
            ->when(!$is_super_admin, function ($query) {
                $query->where('seller_id', auth()->id());
            })
            ->whereNotNull('finished_at')
            ->get(['id', 'amount', 'service_type', 'name']);

        $year_opportunities = Opportunity::whereYear('created_at', today())
            ->when(!$is_super_admin, function ($query) {
                $query->where('seller_id', auth()->id());
            })
            ->whereNotNull('finished_at')
            ->get(['id', 'amount', 'service_type', 'name', 'created_at']);

        $last_year_opportunities = Opportunity::whereYear('created_at', today())
            ->when(!$is_super_admin, function ($query) {
                $query->where('seller_id', auth()->id());
            })
            ->whereNotNull('finished_at')
            ->get(['id', 'amount', 'service_type', 'name', 'created_at']);


        return inertia('CRM/Dashboard', compact('month_opportunities', 'year_opportunities', 'last_year_opportunities'));
    }
}
