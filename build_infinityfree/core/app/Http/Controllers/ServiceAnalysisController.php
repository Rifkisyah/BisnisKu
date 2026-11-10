<?php

namespace App\Http\Controllers;

use App\Models\ServiceRepair;
use App\Models\ServiceRepairItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ServiceAnalysisController extends Controller
{
    private function getReportData(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
        $endDate   = $request->get('end_date', now()->toDateString());

        $startCarbon = Carbon::parse($startDate)->startOfDay();
        $endCarbon   = Carbon::parse($endDate)->endOfDay();

        $repairs = ServiceRepair::with(['technician', 'items'])
            ->whereBetween('start_date', [$startCarbon, $endCarbon])
            ->orderByDesc('start_date')
            ->get();

        $serviceStats = [
            'total'      => $repairs->count(),
            'active'     => $repairs->whereNotIn('status', ['done', 'cancelled'])->count(),
            'done'       => $repairs->where('status', 'done')->count(),
            'cancelled'  => $repairs->where('status', 'cancelled')->count(),
            'revenue'    => $repairs->whereIn('status', ['done', 'picked_up'])->sum('total_cost'),
            'service_fee' => $repairs->whereIn('status', ['done', 'picked_up'])->sum('service_fee'),
            'component_cost' => $repairs->whereIn('status', ['done', 'picked_up'])->sum('component_cost'),
        ];

        // Service trend monthly
        $serviceTrend = ServiceRepair::whereBetween('start_date', [$startCarbon, $endCarbon])
            ->selectRaw('DATE_FORMAT(start_date, "%Y-%m") as month, COUNT(*) as count, SUM(total_cost) as revenue')
            ->groupByRaw('DATE_FORMAT(start_date, "%Y-%m")')
            ->orderBy('month')
            ->get();

        // Top spareparts used (from stock)
        $topSpareparts = ServiceRepairItem::whereNotNull('parent_id')
            ->whereNotNull('component_code')
            ->whereHas('serviceRepair', fn($q) => $q->whereBetween('start_date', [$startCarbon, $endCarbon]))
            ->join('products', 'service_repair_items.component_code', '=', 'products.product_code')
            ->selectRaw('products.name, service_repair_items.component_code,
                SUM(service_repair_items.quantity) as total_qty,
                SUM(service_repair_items.subtotal) as total_cost')
            ->groupBy('products.name', 'service_repair_items.component_code')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return compact('startDate', 'endDate', 'repairs', 'serviceStats', 'serviceTrend', 'topSpareparts');
    }

    public function index(Request $request)
    {
        $data = $this->getReportData($request);
        return view('reports.service-analysis.index', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getReportData($request);
        
        $data['chart_service_trend'] = $request->input('chart_service_trend');
        
        $data['storeName'] = \App\Models\Setting::get('store_name', 'Nama Toko');
        $data['storeAddress'] = \App\Models\Setting::get('store_address', '');
        $data['storePhone'] = \App\Models\Setting::get('store_phone', '');

        $pdf = Pdf::loadView('reports.pdf.service-analysis', $data)->setPaper('a4', 'landscape');
        return $pdf->download('laporan-analisis-layanan-perbaikan-' . now()->format('Y-m-d') . '.pdf');
    }

    public function trends(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
        $endDate   = $request->get('end_date', now()->toDateString());

        $startCarbon = Carbon::parse($startDate)->startOfDay();
        $endCarbon   = Carbon::parse($endDate)->endOfDay();

        $repairs = ServiceRepair::with(['technician', 'items'])
            ->whereBetween('start_date', [$startCarbon, $endCarbon])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderByDesc('start_date')
            ->paginate(50)
            ->withQueryString();

        return view('reports.service-analysis.details.trends', compact('repairs', 'startDate', 'endDate'));
    }

    public function spareparts(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
        $endDate   = $request->get('end_date', now()->toDateString());

        $startCarbon = Carbon::parse($startDate)->startOfDay();
        $endCarbon   = Carbon::parse($endDate)->endOfDay();

        $sort = $request->get('sort', 'desc') === 'asc' ? 'asc' : 'desc';

        $spareparts = ServiceRepairItem::whereNotNull('parent_id')
            ->whereNotNull('component_code')
            ->whereHas('serviceRepair', fn($q) => $q->whereBetween('start_date', [$startCarbon, $endCarbon]))
            ->join('products', 'service_repair_items.component_code', '=', 'products.product_code')
            ->selectRaw('products.name, service_repair_items.component_code,
                SUM(service_repair_items.quantity) as total_qty,
                SUM(service_repair_items.subtotal) as total_cost')
            ->groupBy('products.name', 'service_repair_items.component_code')
            ->orderBy('total_qty', $sort)
            ->paginate(50)
            ->withQueryString();

        return view('reports.service-analysis.details.spareparts', compact('spareparts', 'startDate', 'endDate'));
    }
}
