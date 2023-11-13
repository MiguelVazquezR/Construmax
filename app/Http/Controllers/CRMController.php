<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $current_year_opportunities = Opportunity::whereYear('created_at', today())
            ->when(!$is_super_admin, function ($query) {
                $query->where('seller_id', auth()->id());
            })
            ->whereNotNull('finished_at')
            ->get(['id', 'amount', 'service_type', 'name', 'created_at']);

        $last_year_opportunities = Opportunity::whereYear('created_at', today()->subYear())
            ->when(!$is_super_admin, function ($query) {
                $query->where('seller_id', auth()->id());
            })
            ->whereNotNull('finished_at')
            ->get(['id', 'amount', 'service_type', 'name', 'created_at']);

        $sellers_total_amount_raw = Opportunity::select('seller_id', DB::raw('SUM(amount) as total_amount'))
            ->whereNotNull('finished_at')
            ->groupBy('seller_id')
            ->get();
        // Ahora mapear los resultados para obtener el formato deseado
        $sellers_total_amount = $sellers_total_amount_raw->map(function ($item) {
            $seller_name = User::find($item->seller_id)->name; // Reemplaza 'User' con el nombre de tu modelo de usuario
            return [
                'seller' => $seller_name,
                'amount' => $item->total_amount,
            ];
        });


        return inertia('CRM/Dashboard', compact('month_opportunities', 'current_year_opportunities', 'last_year_opportunities', 'sellers_total_amount'));
    }
}
