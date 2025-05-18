@extends('layouts.master')
@section('title')
الواجهة الرئيسية
@stop

@section('css')

<style>
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
</style>
<!--  Owl-carousel css-->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
<!-- Maps css -->
<link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
	<div class="row">
		<div class="col-md-12 col-lg-12 col-xl-6">
			<div class="card">

				<div class="card-body" style="width:100%;">
					{!! $chartjs1->render() !!}
				</div>
			</div>
		</div>

		<div class="col-md-12 col-lg-12 col-xl-6">
			<div class="card">

				<div class="card-body" style="width:100%;">
					{!! $chartjs2->render() !!}
				</div>
			</div>
		</div>
	</div>
</div>

</div>
</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<h3 class="text-center">تقويم الإدخالات الطبية حسب الشهر</h3>
				<div id="calendar-container">
					<div id="calendar"></div>
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
@endsection