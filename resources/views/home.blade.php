@extends('layouts.master')
@section('title')
الواجهة الرئيسية
@stop

@section('css')
<!-- إضافة خط Cairo -->
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
<style>
    .chart-container {
        direction: rtl;
        font-family: 'Cairo', sans-serif;
        width: 100% !important;
        height: 500px !important;
        position: relative;
    }
	#calendar-container {
		max-height: 600px;
		overflow-y: auto;
	}
</style>

<style>
	@media print {
		#print_Button {
			display: none;
		}
	}

    .chart-content {
        margin: 20px;
        padding: 15px;
        background: white;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .chart-container {
        min-height: 400px;
        margin-bottom: 20px;
    }
</style>
<!--  Owl-carousel css-->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
<!-- Maps css -->
<link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .table-stats {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
        direction: rtl;
        font-family: 'Cairo', sans-serif;
    }
    
    .table-stats th,
    .table-stats td {
        padding: 12px 15px;
        text-align: right;
        border-bottom: 1px solid #e3e3e3;
    }
    
    .table-stats th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #1f2937;
    }
    
    .table-stats tr:hover {
        background-color: #f8f9fa;
    }
    
    .table-stats .stats-value {
        font-weight: 600;
        color: #3b82f6;
    }
    
    @media screen and (max-width: 600px) {
        .table-stats {
            font-size: 14px;
        }
        
        .table-stats th,
        .table-stats td {
            padding: 8px 10px;
        }
    }
    
    .card-title {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 20px;
        text-align: center;
        color: #333;
    }

    @media print {
        /* تنسيق عام للطباعة */
        @page {
            size: landscape A4;
            margin: 0.5cm;
        }

        /* تنسيق المؤشرات */
        .row-sm {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            page-break-inside: avoid !important;
        }

        .col-xl-2 {
            width: 16% !important;
            flex: 0 0 16% !important;
            max-width: 16% !important;
        }

        /* تنسيق البطاقات */
        .card {
            border: 1px solid #ddd !important;
            margin: 5px !important;
            page-break-inside: avoid !important;
        }

        .sales-card {
            background: #f8f9fa !important;
            color: #000 !important;
        }

        .text-white {
            color: #000 !important;
        }

        /* تنسيق المخطط */
        .chart-container {
            width: 100% !important;
            height: 500px !important;
            margin: 0 !important;
            page-break-before: always !important;
        }

        .chart-content {
            page-break-inside: avoid !important;
            border: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        canvas {
            max-width: 100% !important;
            height: 100% !important;
        }

        /* تنسيق النص في المخطط */
        .chart-title {
            font-size: 16px !important;
            margin-bottom: 10px !important;
        }

        /* إخفاء العناصر غير المطلوبة */
        .pagination,
        .btn,
        #calendar-container,
        .no-print {
            display: none !important;
        }

        /* تنسيق الجدول */
        .table-stats {
            page-break-inside: avoid !important;
            width: 100% !important;
            margin-bottom: 20px !important;
        }
        
        .table-stats th {
            background-color: #f8f9fa !important;
            color: #000 !important;
            border: 1px solid #ddd;
            font-size: 12px;
        }
        
        .table-stats td {
            border: 1px solid #ddd;
            font-size: 12px;
        }

        /* تنسيق العنوان */
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 18px;
        }

        /* إخفاء عناصر غير ضرورية */
        form,
        select,
        .form-group {
            display: none !important;
        }
    }

    @media screen and (max-width: 768px) {
        .table-stats {
            font-size: 14px;
        }
        
        .table-stats th, .table-stats td {
            padding: 8px 10px;
        }
        
        .card-title {
            font-size: 18px;
        }
    }

    /* تنسيق أزرار التنقل */
    .pagination {
        direction: rtl;
        padding: 0;
        margin: 20px 0;
        font-family: 'Cairo', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 5px;
    }
    
    .pagination li {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .pagination li a,
    .pagination li span {
        min-width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 10px;
        color: #333;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 14px;
    }
    
    .pagination li.active span {
        background-color: #0162e8;
        color: #fff;
        border-color: #0162e8;
    }
    
    .pagination li a:hover {
        background-color: #f8f9fa;
        border-color: #0162e8;
        color: #0162e8;
    }
    
    .pagination .disabled span {
        color: #6c757d;
        background-color: #f8f9fa;
        border-color: #dee2e6;
        cursor: not-allowed;
    }
    
    /* تخصيص أزرار التالي والسابق */
    .pagination li:first-child a,
    .pagination li:last-child a {
        min-width: 80px;
    }
    
    .pagination li:first-child a::before {
        content: "السابق";
    }
    
    .pagination li:last-child a::before {
        content: "التالي";
    }
    
    .pagination li:first-child a span,
    .pagination li:last-child a span {
        display: none;
    }
    
    /* إخفاء الأسهم غير المرغوب فيها */
    .page-item:first-child .page-link,
    .page-item:last-child .page-link {
        border-radius: 4px !important;
    }
    
    /* تنسيق الأرقام */
    .pagination .page-item:not(:first-child):not(:last-child) .page-link {
        font-weight: 500;
    }
</style>
@endsection
@section('page-header')
<!-- breadcrumb -->

<!-- /breadcrumb -->
@endsection
@section('content')
<!-- row -->


<!-- row closed -->

<!-- row opened -->
<div class="row">

	<div class="col">
		<form method="POST" action="{{ route('index') }}">
			@csrf
			<label for="month">الشهر:</label>
			<select id="month" name="month">
				<option value="1">كانون الثاني</option>
				<option value="2">شباط</option>
				<option value="3">آذار</option>
				<option value="4">نيسان</option>
				<option value="5">أيار</option>
				<option value="6">حزيران</option>
				<option value="7">تموز</option>
				<option value="8">آب</option>
				<option value="9">أيلول</option>
				<option value="10">تشرين الأول</option>
				<option value="11">تشرين الثاني</option>
				<option value="12">كانون الأول</option>
			</select>

			<label for="year">السنة:</label>
			<select id="year" name="year">
				@for ($i = 2024; $i <= 2025; $i++)
					<option value="{{ $i }}">{{ $i }}</option>
					@endfor
			</select>

			<button class="btn btn-info-sm" type="submit">بحث</button>
		</form>

		<h2 class="text-center">مؤشرات شهر {{ $monthName }} سنة {{ $year }}</h2>

	</div>




</div>
<BR>
<div>
	<div class="row row-sm">

		<div class="col-xl-2 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden sales-card bg-primary-gradient">
				<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
					<div class="">
						<h2 class="mb-3 tx-24 text-white"> الخاصة </h2>
					</div>
					<div class="pb-0 mt-0">
						<div class="d-flex">
							<div class="">
								<h4 class="tx-20 font-weight-bold mb-1 text-white">
									{{$khasa}}
								</h4>
							</div>
							<span class="float-right my-auto mr-auto">
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-2 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden sales-card bg-primary-gradient">
				<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
					<div class="">
						<h6 class="mb-3 tx-24 text-white"> فوق الكبرى </h6>
					</div>
					<div class="pb-0 mt-0">
						<div class="d-flex">
							<div class="">
								<h4 class="tx-20 font-weight-bold mb-1 text-white">
									{{$fkubra}}
								</h4>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-2 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden sales-card bg-primary-gradient">
				<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
					<div class="">
						<h6 class="mb-3 tx-24 text-white">الكبرى</h6>
					</div>
					<div class="pb-0 mt-0">
						<div class="d-flex">
							<div class="">
								<h4 class="tx-20 font-weight-bold mb-1 text-white">
									{{$kubra}}
								</h4>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-2 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden sales-card bg-primary-gradient">
				<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
					<div class="">
						<h6 class="mb-3 tx-24 text-white"> متوسطة</h6>
					</div>
					<div class="pb-0 mt-0">
						<div class="d-flex">
							<div class="">
								<h4 class="tx-20 font-weight-bold mb-1 text-white">
									{{$mtws}}
								</h4>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-2 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden sales-card bg-primary-gradient">
				<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
					<div class="">
						<h6 class="mb-3 tx-24 text-white"> صغرى</h6>
					</div>
					<div class="pb-0 mt-0">
						<div class="d-flex">
							<div class="">
								<h4 class="tx-20 font-weight-bold mb-1 text-white">
									{{$sugra}}
								</h4>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-2 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden sales-card bg-primary-gradient">
				<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
					<div class="">
						<h2 class="mb-3 tx-24 text-white"> عدد اسرة الصالات </h2>
					</div>
					<div class="pb-0 mt-0">
						<div class="d-flex">
							<div class="">
								<h4 class="tx-20 font-weight-bold mb-1 text-white">
									{{$salat}}
								</h4>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

	@if(isset($labels1) && isset($chart_data1))
	<div class="row row-sm">
		<div class="col-xl-12">
			<div class="card mg-b-20">
				<div class="card-body">
					<h6 class="card-title mb-3">إحصائيات الأسرة حسب الاختصاص</h6>
                    <div class="d-flex justify-content-end mb-3">
                        <button onclick="window.print()" class="btn btn-primary btn-sm no-print">
                            <i class="fas fa-print"></i> طباعة الإحصائيات
                        </button>
                    </div>
                    <div class="table-responsive">
                        <div class="table-wrapper">
                            <table class="table table-stats table-striped table-hover">
                                <thead class="thead-primary">
                                    <tr>
                                        <th class="th-lg">الاختصاص</th>
                                        <th class="text-center th-md">عدد الأسرة</th>
                                        <th class="text-center th-sm">النسبة المئوية</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($paginatedData as $item)
                                    <tr>
                                        <td><strong>{{ $item['name'] }}</strong></td>
                                        <td class="stats-value text-center">{{ number_format($item['count']) }}</td>
                                        <td class="text-center percentage-cell">{{ number_format(($item['count'] / ($total ?: 1)) * 100, 1) }}%</td>
                                    </tr>
                                    @endforeach
                                    <tr class="table-info total-row">
                                        <td><strong>المجموع الكلي</strong></td>
                                        <td class="stats-value text-center"><strong>{{ number_format($total) }}</strong></td>
                                        <td class="text-center"><strong>100%</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center mt-4">
                                {{ $paginatedData->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
	@endif

	<div class="row">
		<div class="col-md-12 col-lg-12 col-xl-12">
			<div class="card">
				<div class="card-body chart-content">
					<div class="chart-container">
						@if(isset($chartjs2) && $chartjs2)
							{!! $chartjs2->render() !!}
						@else
							<div class="text-center p-5">
								<div class="alert alert-info" role="alert">
									<i class="fas fa-info-circle ml-2"></i>
									لا توجد بيانات متاحة لعرض المخطط البياني
								</div>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

</div>
</div>
</div>



<style>
	#calendar-container {
		display: flex;
		justify-content: center;
		align-items: center;
		height: 100vh;
		/* يضمن أن يكون التقويم في منتصف الشاشة */
	}

	#calendar {
		max-width: 800px;
		/* تحديد عرض التقويم */
		width: 100%;
	}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'listYear', // عرض كل الأشهر دفعة واحدة
        height: 'auto', // ضبط ارتفاع التقويم
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'listYear'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            fetch('/getCalendarData?year=' + fetchInfo.start.getFullYear())
                .then(response => response.json())
                .then(data => {
                    console.log("📌 البيانات المسترجعة:", data);
                    let events = data.map(inst => ({
                        title: inst.entry_exists ? inst.Fckname : inst.Fckname + " (غير مدخل)",
                        color: inst.entry_exists ? '#28a745' : '#dc3545',
                        start: `${fetchInfo.start.getFullYear()}-${inst.month}-01` // بداية كل شهر فقط
                    }));
                    successCallback(events);
                }).catch(error => {
                    console.error("⚠️ فشل تحميل البيانات!", error);
                });
        }
    });
    calendar.render();
});
س
</script>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
</div>
<!-- Container closed -->
@endsection
@section('js')
<script type="text/javascript">
	function printDiv() {
		var printContents = document.getElementById('print').innerHTML;
		var originalContents = document.body.innerHTML;
		document.body.innerHTML = printContents;
		window.print();
		document.body.innerHTML = originalContents;
		location.reload();
	}
</script>

<!--Internal  Chart.bundle js -->
<script src="{{URL::asset('assets/plugins/chart.js/Chart.bundle.min.js')}}"></script>
<!-- Moment js -->
<script src="{{URL::asset('assets/plugins/raphael/raphael.min.js')}}"></script>
<!--Internal  Flot js-->
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.pie.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.resize.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.categories.js')}}"></script>
<script src="{{URL::asset('assets/js/dashboard.sampledata.js')}}"></script>
<script src="{{URL::asset('assets/js/chart.flot.sampledata.js')}}"></script>
<!--Internal Apexchart js-->
<script src="{{URL::asset('assets/js/apexcharts.js')}}"></script>
<!-- Internal Map -->
<script src="{{URL::asset('assets/plugins/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
<script src="{{URL::asset('assets/js/modal-popup.js')}}"></script>
<!--Internal  index js -->
<script src="{{URL::asset('assets/js/index.js')}}"></script>
<script src="{{URL::asset('assets/js/jquery.vmap.sampledata.js')}}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تكوين الخيارات العامة للرسوم البيانية
    Chart.defaults.font.family = 'Cairo';
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#333';
    
    let charts = document.querySelectorAll('canvas');
    charts.forEach(canvas => {
        let chartInstance = Chart.getChart(canvas);
        if (chartInstance && chartInstance.data && chartInstance.data.datasets && chartInstance.data.datasets.length > 0) {
            // تحديث خيارات المخطط
            chartInstance.options = {
                ...chartInstance.options,
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 10, // تصغير حجم الخط في وضع الطباعة
                                family: 'Cairo'
                            },
                            boxWidth: 10, // تقليل حجم مربع الألوان
                            padding: 10
                        }
                    },
                    datalabels: {
                        display: function(context) {
                            // إظهار القيم فقط إذا كانت أكبر من الصفر
                            return context.dataset.data[context.dataIndex] > 0;
                        },
                        color: '#000',
                        font: {
                            weight: 'bold',
                            size: 10 // تصغير حجم القيم
                        },
                        formatter: (value) => {
                            if (value === 0) return '';
                            return value.toLocaleString('ar-SA');
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        ticks: {
                            font: {
                                size: 10, // تصغير حجم النص على المحور السيني
                                family: 'Cairo'
                            },
                            maxRotation: 45,
                            minRotation: 45,
                            autoSkip: false // عدم تخطي أي تسمية
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        display: true,
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 10, // تصغير حجم النص على المحور الصادي
                                family: 'Cairo'
                            }
                        }
                    }
                },
                layout: {
                    padding: {
                        left: 10,
                        right: 10,
                        top: 20,
                        bottom: 10
                    }
                }
            };
            
            // إضافة مستمع لحدث الطباعة
            window.addEventListener('beforeprint', function() {
                chartInstance.resize();
            });
            
            // تحديث المخطط
            chartInstance.update();
        }
    });
});
</script>
@endsection