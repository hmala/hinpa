@extends('layouts.master')
@section('title')
ألاسرة المهيئة
@stop
@section('css')
<style>
	@page {
		size: A4;
		/* يمكنك ضبط الحجم هنا (مثلاً، A4، A3، Letter) */
		margin: 5mm;
		/* ضبط الهوامش */
	}

	.table {
		width: 100%;
		table-layout: fixed;
		/* جعل عرض الجدول ثابت لضبط العرض */
	}

	th,
	td {
		height: auto;
		/* ضبط ارتفاع الخلايا ليكون تلقائي لتفادي تج										<th class="border-bottom-0">العيادات الاستشارية</th>
										<th class="border-bottom-0">العيادات العامة</th>
										<th class="border-bottom-0">عيادات الطوارئ</th>
										<th class="border-bottom-0">الفحوصات الساندة (مراجعين)</th>
										<th class="border-bottom-0">الفحوصات المختبرية (مراجعين)</th>
										<th class="border-bottom-0">الفحوصات الساندة (راقدين)</th>
										<th class="border-bottom-0">الفحوصات المختبرية (راقدين)</th>
										<th class="border-bottom-0">الأسرة في المخزن</th>
										<th class="border-bottom-0">الأسرة الأخرى</th>
										<th class="border-bottom-0">الأسرة في الاستشاريات</th>
										<th class="border-bottom-0">الأسرة في الردهات المغلقة</th>
										<th class="border-bottom-0">مجموع الأسرة الكلية المهيئة</th>
										<th class="border-bottom-0">مجموع الأسرة الكلي</th>
										<th class="border-bottom-0">إجمالي الأسرة</th>*/
		max-width: 100px;
		/* ضبط العرض الأقصى للخلايا حسب الرغبة */
	}

	.table th {
		font-size: 11px !important;
		/* تغيير القيمة حسب الرغبة */
	}

	.table td {
		font-size: 10px !important;
		/* تغيير القيمة حسب الرغبة */
	}

	@media print {
		@page {
			size: A4;
			margin: 15mm;
		}

		/* جعل اسم المؤسسة والشهر والسنة يظهر في جميع الصفحات */
		.header {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			background-color: white;
			text-align: center;
			font-size: 14pt;
			font-weight: bold;
			padding: 5mm;
			border-bottom: 2px solid black;
		}

		/* ضبط الجداول بحيث لا تتجاوز حدود الصفحة */
		.table-container {
			margin-top: 50px;
			/* لضبط المسافة بين العنوان والجداول */
		}

		table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 20px;
		}

		th,
		td {
			border: 1px solid black;
			padding: 5px;
			text-align: center;
			vertical-align: middle;
			word-wrap: break-word;
			white-space: normal;
			overflow: hidden;
		}

		/* إخفاء العناصر غير الضرورية عند الطباعة */
		#print_Button,
		#ser,
		#month,
		#year,
		#mon,
		#yea {
			display: none;
		}
	}

	.table-container {
		display: flex;
		justify-content: space-between;
		flex-wrap: wrap;
	}

	.table-container .col-md-6 {
		width: 48%;
		margin-bottom: 20px;
	}
</style>

<!-- Internal Data table css -->
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
<!--Internal   Notify -->
<link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
@endsection
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
	<div class="my-auto">
		<div class="d-flex">
			<h4 class="content-title mb-0 my-auto">استمارة المستشفى الشهرية</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">
			</span>
		</div>
	</div>

</div>
<!-- breadcrumb -->
@endsection
@section('content')


<!-- row -->
<div class="row" id="print">
	<div class="col-xl-12">
		<div class="card mg-b-20">
			<div class="card-body">
				<div class="table-container">
					<h2 style="text-align: center;">استمارة المستشفى الشهرية</h2>
					<form method="POST" action="{{ route('print_pations') }}">
						@csrf
						<label for="month" id="mon">Month:</label>
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

						<label for="year" id="yea">Year:</label>
						<select id="year" name="year">
							@for ($i = 2024; $i <= 2025; $i++)
								<option value="{{ $i }}">{{ $i }}</option>
								@endfor
						</select>

						<button type="submit" id="ser">بحث</button>
					</form>
					<br>

					<button class="btn btn-danger mt-3 mr-2" id="print_Button" onclick="printDiv()">
						<i class="mdi mdi-printer ml-1"></i>طباعة
					</button>
				</div>
				<div>
					@php
					$displayedMohNames = [];
					$displayedFckNames = [];
					$displayedYears = [];
					$displayedMonths = []; // Initialize an array to keep track of displayed months
					$months = [
					1 => 'كانون الثاني',
					2 => 'شباط',
					3 => 'اذار',
					4 => 'نيسان',
					5 => 'ايار',
					6 => 'حزيران',
					7 => 'تموز',
					8 => 'أب',
					9 => 'ايلول',
					10 => 'تشرين الاول',
					11 => 'تشرين الثاني',
					12 => 'كانون الاول',
					];
					@endphp

					<table style="text-align: center;">
						<tr>
							<th style="text-align:center">دائرة الصحة</th>
							<th style="text-align:center">اسم المستشفى</th>
							<th style="text-align:center">الشهر</th>
							<th style="text-align:center">السنة</th>
						</tr>
						@foreach ($pations as $pation)
						@if (!in_array($pation->moh ? $pation->moh->mohname : 'No Section', $displayedMohNames) ||
						!in_array($pation->fck ? $pation->fck->Fckname : 'No Section', $displayedFckNames) ||
						!in_array($months[$pation->month] ?? 'No Month', $displayedMonths) ||
						!in_array($pation->year ?? 'No Year', $displayedYears))
						<tr>
							<td>{{ $pation->moh ? $pation->moh->mohname : 'No Section' }}</td>
							<td>{{ $pation->fck ? $pation->fck->Fckname : 'No Section' }}</td>
							<td>{{ $months[$pation->month] ?? 'No Month' }}</td>
							<td>{{ $pation->year ?? 'No Year' }}</td>
						</tr>
						@php
						$displayedMohNames[] = $pation->moh ? $pation->moh->mohname : 'No Section';
						$displayedFckNames[] = $pation->fck ? $pation->fck->Fckname : 'No Section';
						$displayedMonths[] = $months[$pation->month] ?? 'No Month';
						$displayedYears[] = $pation->year ?? 'No Year';
						@endphp
						@endif
						@endforeach
					</table>





					<br>
					<div class="table-container">
						<div class="col-md-6">
							<table border="hover" class="table key-buttons text-md-nowrap" style="table-layout: auto; width: 100%; text-align: center;" class="table table-bordered">
								<thead>
									<tr>
										<th class="border-bottom-0">ت</th>
										<th class="border-bottom-0">اختصاص السرير</th>
										<th class="border-bottom-0">عدد الوحدات</th>
										<th class="border-bottom-0">عدد الاسرة المهيئة</th>
										<th class="border-bottom-0">الخارجين خلال الشهر</th>
										<th class="border-bottom-0">الباقين آخر الشهر</th>
										<th class="border-bottom-0">أيام المكوث</th>
										<th class="border-bottom-0">عدد الوفيات</th>
									</tr>
								</thead>
								<tbody>
									@php
									$i = 0;
									$totalUnitnum = 0;
									$totalBedm = 0;
									$totalOutpationmon = 0;
									$totalStayoutpation = 0;
									$totalMkoth = 0;
									$totalDeath = 0;
									@endphp
									@foreach ($pations as $pation)
									@php
									$i++;
									$totalUnitnum += $pation->unitnum;
									$totalBedm += $pation->bedm;
									$totalOutpationmon += $pation->outpationmon;
									$totalStayoutpation += $pation->stayoutpation;
									$totalMkoth += $pation->mkoth;
									$totalDeath += $pation->death;
									@endphp
									@if ($pation->month >= 1 && $pation->month <= 12)
										<tr>
										<td>{{ $i }}</td>
										<td>{{ $pation->rdhs ? $pation->rdhs->Spcuname : 'No Section' }}</td>
										<td>{{ $pation->unitnum }}</td>
										<td>{{ $pation->bedm }}</td>
										<td>{{ $pation->outpationmon }}</td>
										<td>{{ $pation->stayoutpation }}</td>
										<td>{{ $pation->mkoth }}</td>
										<td>{{ $pation->death }}</td>
										</tr>
										@endif
										@endforeach
										<tr>
											<td colspan="2"><strong>الإجمالي</strong></td>
											<td><strong>{{ $totalUnitnum }}</strong></td>
											<td><strong>{{ $totalBedm }}</strong></td>
											<td><strong>{{ $totalOutpationmon }}</strong></td>
											<td><strong>{{ $totalStayoutpation }}</strong></td>
											<td><strong>{{ $totalMkoth }}</strong></td>
											<td><strong>{{ $totalDeath }}</strong></td>
										</tr>
								</tbody>
							</table>
						</div>

						<div class="col-md-6">
							<table border="hover" class="table key-buttons text-md-nowrap" style="table-layout: auto; width: 100%; text-align: center;" class="table table-bordered">
								<thead>
									<tr>
										<th class="border-bottom-0">ت</th>
										<th class="border-bottom-0">نوع العملية</th>
										<th class="border-bottom-0">خاصة </th>
										<th class="border-bottom-0">فوق الكبرى</th>
										<th class="border-bottom-0">الكبرى</th>
										<th class="border-bottom-0">المتوسطة</th>
										<th class="border-bottom-0">الصغرى</th>
									</tr>
								</thead>
								<tbody>
									@php
									$i = 0;
									$totalKhasa = 0;
									$totalFkubra = 0;
									$totalKubra = 0;
									$totalMtws = 0;
									$totalSugra = 0;
									@endphp
									@foreach ($surgery as $surgery)
									@php
									$i++;
									$totalKhasa += $surgery->khasa;
									$totalFkubra += $surgery->fkubra;
									$totalKubra += $surgery->kubra;
									$totalMtws += $surgery->mtws;
									$totalSugra += $surgery->sugra;
									@endphp
									@if ($surgery->month >= 1 && $surgery->month <= 12)
										<tr>
										<td>{{ $i }}</td>
										<td>{{ $surgery->surg ? $surgery->surg->surgname : 'No Section' }}</td>
										<td>{{ $surgery->khasa }}</td>
										<td>{{ $surgery->fkubra }}</td>
										<td>{{ $surgery->kubra }}</td>
										<td>{{ $surgery->mtws }}</td>
										<td>{{ $surgery->sugra }}</td>
										</tr>
										@endif
										@endforeach
										<tr>
											<td colspan="2"><strong>الإجمالي</strong></td>
											<td><strong>{{ $totalKhasa }}</strong></td>
											<td><strong>{{ $totalFkubra }}</strong></td>
											<td><strong>{{ $totalKubra }}</strong></td>
											<td><strong>{{ $totalMtws }}</strong></td>
											<td><strong>{{ $totalSugra }}</strong></td>
										</tr>
								</tbody>
							</table>
						</div>

						<div class="col-md-6">
							<table border="hover" class="table key-buttons text-md-nowrap" style="table-layout: auto; width: 100%; text-align: center;" class="table table-bordered">
								<thead>
									<tr>
										<th class="border-bottom-0">نوع الحادث</th>
										<th class="border-bottom-0">ذكور</th>
										<th class="border-bottom-0">اناث</th>
										<th class="border-bottom-0">خنثى</th>
										<th class="border-bottom-0">المجموع</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>الولادات الحية (طبيعية)</td>
										<td>{{ $hwadth['livebnm'] ?? 0 }}</td>
										<td>{{ $hwadth['livebnf'] ?? 0 }}</td>
										<td>{{ $hwadth['livebnkh'] ?? 0 }}</td>
										<td>{{ $hwadth['livebnt'] ?? 0 }}</td>
									</tr>
									<tr>
										<td>الولادات الحية (قيصرية)</td>
										<td>{{ $hwadth['livebsm'] ?? 0 }}</td>
										<td>{{ $hwadth['livebsf'] ?? 0 }}</td>
										<td>{{ $hwadth['livebskh'] ?? 0 }}</td>
										<td>{{ $hwadth['livebst'] ?? 0 }}</td>
									</tr>
									<tr>
										<td>المجموع الكلي للولادات الحية </td>
										<td>{{ $hwadth['totalbm'] ?? 0 }}</td>
										<td>{{ $hwadth['totalbf'] ?? 0 }}</td>
										<td>{{ $hwadth['totalbkh'] ?? 0 }}</td>
										<td>{{ $hwadth['totalbt'] ?? 0 }}</td>
									</tr>
									<tr>
										<td>الولادات الميتة (طبيعية)</td>
										<td>{{ $hwadth['bdeadnm'] ?? 0 }}</td>
										<td>{{ $hwadth['bdeadnf'] ?? 0 }}</td>
										<td>{{ $hwadth['bdeadnkh'] ?? 0 }}</td>
										<td>{{ $hwadth['bdeadnt'] ?? 0 }}</td>
									</tr>
									<tr>
										<td>الولادات الميتة (قيصرية)</td>
										<td>{{ $hwadth['bdeadsm'] ?? 0 }}</td>
										<td>{{ $hwadth['bdeadsf'] ?? 0 }}</td>
										<td>{{ $hwadth['bdeadskh'] ?? 0 }}</td>
										<td>{{ $hwadth['bdeadst'] ?? 0 }}</td>
									</tr>
									<tr>
										<td>الوفيات أقل من سنة من الراقدين</td>
										<td>{{ $hwadth['deadlm'] ?? 0 }}</td>
										<td>{{ $hwadth['deadlf'] ?? 0 }}</td>
										<td>{{ $hwadth['deadlkh'] ?? 0 }}</td>
										<td>{{ $hwadth['deadlt'] ?? 0 }}</td>
									</tr>
									<tr>
										<td>وفيات سنة فاكثر من الراقدين</td>
										<td>{{ $hwadth['deadmm'] ?? 0 }}</td>
										<td>{{ $hwadth['deadmf'] ?? 0 }}</td>
										<td>{{ $hwadth['deadmkh'] ?? 0 }}</td>
										<td>{{ $hwadth['deadmt'] ?? 0 }}</td>
									</tr>
									<tr>
										<td>المجموع الكلي للوفيات</td>
										<td>{{ $hwadth['totaldm'] ?? 0 }}</td>
										<td>{{ $hwadth['totaldf'] ?? 0 }}</td>
										<td>{{ $hwadth['totaldkh'] ?? 0 }}</td>
										<td>{{ $hwadth['totaldt'] ?? 0 }}</td>
									</tr>
									<tr>
										<td>وفيات الأمهات</td>
										<td>{{ $hwadth['mdeadm'] ?? 0 }}</td>
										<td>{{ $hwadth['mdeadf'] ?? 0 }}</td>
										<td>{{ $hwadth['mdeadkh'] ?? 0 }}</td>
										<td>{{ $hwadth['mdeadt'] ?? 0 }}</td>
									</tr>
									<tr>
										<td colspan="4">الوفيات التي احيلت للطب العدلي </td>
										<td>{{ $hwadth['deadtb'] ?? 0 }}</td>
									</tr>
								</tbody>
							</table>
						</div>

						<div class="col-md-6">
							<table border="hover" class="table key-buttons text-md-nowrap" style="table-layout: auto; width: 100%; text-align: center;" class="table table-bordered">
								<thead>
									<tr>
										<th class="border-bottom-0">ت</th>
										<th class="border-bottom-0">نوع الصالة</th>
										<th class="border-bottom-0">عدد وحدات الصالات</th>
										<th class="border-bottom-0">عدد اسرة الصالات</th>
									</tr>
								</thead>
								<tbody>
									@php
									$i = 0;
									$totalSalnum = 0;
									$totalBsalnum = 0;
									@endphp
									@foreach ($salat as $salat)
									@php
									$i++;
									$totalSalnum += $salat->salnum;
									$totalBsalnum += $salat->bsalnum;
									@endphp
									@if ($salat->month >= 1 && $salat->month <= 12)
										<tr>
										<td>{{ $i }}</td>
										<td>{{ $salat->salsurs ? $salat->salsurs->salsur : 'No Section' }}</td>
										<td>{{ $salat->salnum }}</td>
										<td>{{ $salat->bsalnum }}</td>
										</tr>
										@endif
										@endforeach
										<tr>
											<td colspan="2"><strong>الإجمالي</strong></td>
											<td><strong>{{ $totalSalnum }}</strong></td>
											<td><strong>{{ $totalBsalnum }}</strong></td>
										</tr>
								</tbody>
							</table>
						</div>

						<div style="page-break-before: always;" class="col-md-12">
							<table border="hover" class="table key-buttons text-md-nowrap" style="table-layout: auto; width: 100%; text-align: center;" class="table table-bordered">
								<thead>
									<tr>
										<th class="border-bottom-0">نوع الحالة</th>
										<th class="border-bottom-0">العدد</th>
										<th class="border-bottom-0">المجموع</th>
									</tr>
								</thead>
								<tbody>
									@if(!empty($cases))
									@php
									$totalCaseest = $cases->caseest ?? 0;
									$totalCasekhaf = $cases->casekhaf ?? 0;
									$totalCaseesemer = $cases->caseesemer ?? 0;
									$totalCheoutpationsan = $cases->cheoutpationsan ?? 0;
									$totalCheoutpationlab = $cases->cheoutpationlab ?? 0;
									$totalCheinpationsan = $cases->cheinpationsan ?? 0;
									$totalCheinpationlab = $cases->cheinpationlab ?? 0;
									$totalBedssav = $cases->bedssav ?? 0;
									$totalOtherbed = $cases->otherbed ?? 0;
									$totalBedest = $cases->bedest ?? 0;
									$totalBedrdhclose = $cases->bedrdhclose ?? 0;
									$totalTbedsmoh = $cases->tbedsmoh ?? 0;
									$totalTotalbed = $cases->totalbed ?? 0;
									$totalTbedsf = $cases->tbedsf ?? 0;
									@endphp
									<tr>
										<td>الحالات المرضية في العيادات الاستشارية</td>
										<td>{{ $cases->caseest ?? 0 }}</td>
										<td>{{ $totalCaseest }}</td>
									</tr>
									<tr>
										<td>الحالات المرضية في العيادات العامة</td>
										<td>{{ $cases->casekhaf ?? 0 }}</td>
										<td>{{ $totalCasekhaf }}</td>
									</tr>
									<tr>
										<td>الحالات المرضية في عيادات الطوارئ</td>
										<td>{{ $cases->caseesemer ?? 0 }}</td>
										<td>{{ $totalCaseesemer }}</td>
									</tr>
									<tr>
										<td>الفحوصات الساندة للمراجعين</td>
										<td>{{ $cases->cheoutpationsan ?? 0 }}</td>
										<td>{{ $totalCheoutpationsan }}</td>
									</tr>
									<tr>
										<td>الفحوصات المختبرية للمراجعين</td>
										<td>{{ $cases->cheoutpationlab ?? 0 }}</td>
										<td>{{ $totalCheoutpationlab }}</td>
									</tr>
									<tr>
										<td>الفحوصات الساندة للراقدين</td>
										<td>{{ $cases->cheinpationsan ?? 0 }}</td>
										<td>{{ $totalCheinpationsan }}</td>
									</tr>
									<tr>
										<td>الفحوصات المختبرية للراقدين</td>
										
										<td>{{ $cases->cheinpationlab ?? 0 }}</td>
										<td>{{ $totalCheinpationlab }}</td>
									</tr>
									<tr>
										<td>الأسرة في المخزن</td>
										
										<td>{{ $cases->bedssav ?? 0 }}</td>
										<td>{{ $totalBedssav }}</td>
									</tr>
									<tr>
										<td>الأسرة الأخرى</td>
									
										<td>{{ $cases->otherbed ?? 0 }}</td>
										<td>{{ $totalOtherbed }}</td>
									</tr>
									<tr>
										<td>الأسرة في الاستشاريات</td>
									
										<td>{{ $cases->bedest ?? 0 }}</td>
										<td>{{ $totalBedest }}</td>
									</tr>
									<tr>
										<td>الأسرة في الردهات المغلقة</td>
									
										<td>{{ $cases->bedrdhclose ?? 0 }}</td>
										<td>{{ $totalBedrdhclose }}</td>
									</tr>
									<tr>
										<td>مجموع الأسرة الكلية المهيئة</td>
										
										<td>{{ $cases->tbedsmoh ?? 0 }}</td>
										<td>{{ $totalTbedsmoh }}</td>
									</tr>
									<tr>
										<td>مجموع الأسرة الكلي</td>
										
										<td>{{ $cases->totalbed ?? 0 }}</td>
										<td>{{ $totalTotalbed }}</td>
									</tr>
									
									@else
									<tr>
										<td colspan="5">لا توجد بيانات</td>
									</tr>
									@endif
																	</tbody>
							</table>
						</div>


					</div>
					<div id="footer" style="margin-top: 50px; text-align: center;">
						<table id="specificTable" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
							<thead style="background-color: #f3f3f3;">
								<tr>
									<th style="padding: 10px; text-align: center; vertical-align: middle; width: 16.6%;"> منظم الاستمارة</th>
									<th style="padding: 10px; text-align: center; vertical-align: middle; width: 16.6%;">التوقيع</th>
									<th style="padding: 10px; text-align: center; vertical-align: middle; width: 16.6%;">مسؤول وحدة سياسات المستشفى</th>
									<th style="padding: 10px; text-align: center; vertical-align: middle; width: 16.6%;">التوقيع</th>
									<th style="padding: 10px; text-align: center; vertical-align: middle; width: 16.6%;">مدير المستشفى</th>
									<th style="padding: 10px; text-align: center; vertical-align: middle; width: 16.6%;">التوقيع</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="height: 50px; text-align: center; vertical-align: middle;"></td>
									<td style="height: 50px; text-align: center; vertical-align: middle;"></td>
									<td style="height: 50px; text-align: center; vertical-align: middle;"></td>
									<td style="height: 50px; text-align: center; vertical-align: middle;"></td>
									<td style="height: 50px; text-align: center; vertical-align: middle;"></td>
									<td style="height: 50px; text-align: center; vertical-align: middle;"></td>
								</tr>
							</tbody>
						</table>
						<table id="specificTable" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
							<thead style="background-color: #f3f3f3;">
								<tr>
									<th style="padding: 10px; text-align: center; vertical-align: middle; width: 16.6%;">مسؤول شعبة احصاء الدائرة</th>
									<th style="padding: 10px; text-align: center; vertical-align: middle; width: 16.6%;">التوقيع</th>
									<th style="padding: 10px; text-align: center; vertical-align: middle; width: 16.6%;">مسؤول شعبة سياسات الدائرة</th>
									<th style="padding: 10px; text-align: center; vertical-align: middle; width: 16.6%;">التوقيع</th>
									<th style="padding: 10px; text-align: center; vertical-align: middle; width: 16.6%;">مدير قسم التخطيط</th>
									<th style="padding: 10px; text-align: center; vertical-align: middle; width: 16.6%;">التوقيع</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="height: 50px; text-align: center; vertical-align: middle;"></td>
									<td style="height: 50px; text-align: center; vertical-align: middle;"></td>
									<td style="height: 50px; text-align: center; vertical-align: middle;"></td>
									<td style="height: 50px; text-align: center; vertical-align: middle;"></td>
									<td style="height: 50px; text-align: center; vertical-align: middle;"></td>
									<td style="height: 50px; text-align: center; vertical-align: middle;"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!--/div-->
</div>


</div>
<!-- row closed -->
</div>
<!-- Container closed -->
</div>
<!-- main-content closed -->
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
<!-- Internal Data tables -->
<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/pdfmake.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/vfs_fonts.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
<!--Internal  Datatable js -->
<script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
<!--Internal  Notify js -->
<script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>

<script>
	$('#delete_invoice').on('show.bs.modal', function(event) {
		var button = $(event.relatedTarget)
		var invoice_id = button.data('invoice_id')
		var modal = $(this)
		modal.find('.modal-body #invoice_id').val(invoice_id);
	})
</script>

<script>
	$('#Transfer_invoice').on('show.bs.modal', function(event) {
		var button = $(event.relatedTarget)
		var invoice_id = button.data('invoice_id')
		var modal = $(this)
		modal.find('.modal-body #invoice_id').val(invoice_id);
	})
</script>

<script>
	function printPage() {
		window.print();
	}
</script>





@endsection