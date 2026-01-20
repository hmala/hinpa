<?php

namespace App\Http\Controllers;

use App\Models\PrivateWing;
use App\Models\PrivateWingService;
use App\Models\Service_Specialization;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PrivateWingDashboardController extends Controller
{
    public function index()
    {
        try {
            // 1. إجمالي الأجنحة (بسيط - يحسب كل السجلات)
            $totalWings = DB::table('private_wings')->count();

            // 2. الأجنحة المشغولة (التي لها تاريخ دخول ولا يوجد تاريخ خروج)
            $occupiedWings = DB::table('private_wings')
                ->whereNotNull('entry_date')
                ->whereNull('exit_date')
                ->count();

            // 3. معدل الإشغال (نسبة الأجنحة المشغولة من الإجمالي)
            $occupancyRate = $totalWings > 0 ? 
                round(($occupiedWings / $totalWings) * 100, 1) : 0;

            // 4. الإيرادات الشهرية (مجموع التكلفة للشهر الحالي)
            $monthlyRevenue = DB::table('private_wing_services')
                ->join('private_wings', 'private_wing_services.private_wing_id', '=', 'private_wings.id')
                ->whereMonth('private_wings.entry_date', now()->month)
                ->whereYear('private_wings.entry_date', now()->year)
                ->sum('private_wing_services.total_amount');

            // 5. الخدمات الأكثر طلباً
            $topServices = DB::table('private_wing_services as pws')
                ->join('service_specialization as ss', 'pws.service_id', '=', 'ss.id')
                ->select(
                    'ss.namesv as service_name',
                    DB::raw('COUNT(*) as total_requests'),
                    DB::raw('SUM(pws.total_amount) as total_amount')
                )
                ->groupBy('ss.id', 'ss.namesv')
                ->orderByDesc('total_requests')
                ->limit(5)
                ->get();

            $data = [
                'totalWings' => $totalWings,
                'occupiedWings' => $occupiedWings,
                'occupancyRate' => $occupancyRate,
                'monthlyRevenue' => $monthlyRevenue,
                'topServices' => $topServices,
            ];

            // تسجيل البيانات للتأكد من وجودها
            \Log::info('Dashboard Data:', $data);

            return view('private-wing.dashboard', $data);

        } catch (\Exception $e) {
            \Log::error('Dashboard Error:', ['message' => $e->getMessage()]);
            
            return view('private-wing.dashboard', [
                'totalWings' => 0,
                'occupiedWings' => 0,
                'occupancyRate' => 0,
                'monthlyRevenue' => 0,
                'topServices' => [],
                'error' => 'حدث خطأ في جلب البيانات'
            ]);
        }
    }
}
