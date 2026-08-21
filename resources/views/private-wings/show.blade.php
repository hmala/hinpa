@extends('layouts.master')
@section('title', 'فاتورة الجناح الخاص')
@section('css')
<link href="{{ URL::asset('assets/css/private-wings.css') }}" rel="stylesheet" />
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap');
    
    body {
        background-color: #f4f4f9;
        font-family: 'Cairo', 'DejaVu Sans', sans-serif;
    }
    
    * {
        font-family: 'Cairo', 'DejaVu Sans', sans-serif !important;
    }
    .invoice-container {
        max-width: 800px;
        margin: 40px auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        display: block;
    }
    .invoice-header {
        text-align: center;
        margin-bottom: 20px;
    }
    .invoice-header h1 {
        font-size: 24px;
        color: #333;
    }
    .invoice-header h2 {
        font-size: 18px;
        color: #555;
    }
    .invoice-section {
        margin-bottom: 20px;
    }
    .invoice-section h3 {
        font-size: 18px;
        color: #444;
        border-bottom: 2px solid #ddd;
        padding-bottom: 5px;
        margin-bottom: 15px;
    }
    .invoice-table {
        width: 100%;
        border-collapse: collapse;
    }
    .invoice-table th, .invoice-table td {
        border: 1px solid #ddd;
        padding: 4px;
        text-align: center;
        font-size: 12px;
    }
    .invoice-table th {
        background-color: #f7f7f7;
        color: #333;
    }
    .text-right {
        text-align: right;
    }
    .text-center {
        text-align: center;
    }
    .btn-print {
        display: block;
        margin: 20px auto;
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .btn-print:hover {
        background-color: #0056b3;
    }
    .no-print {
        /* يخفى عند الطباعة */
    }
    @media print {
        @page {
            size: A4;
            margin: 10mm;
        }
        .no-print {
            display: none !important;
        }
        body {
            font-size: 10pt !important;
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
            color: black !important;
        }
        .invoice-container {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 10px !important;
            box-shadow: none !important;
            border: 1px solid #000 !important;
            background: white !important;
            border-radius: 0 !important;
            color: black !important;
        }
        .invoice-header {
            margin-bottom: 10px !important;
            text-align: center !important;
            background: white !important;
            color: black !important;
        }
        .invoice-header h1 {
            font-size: 16pt !important;
            margin: 5px 0 !important;
            line-height: 1.2 !important;
            color: black !important;
            font-weight: bold !important;
        }
        .invoice-header h2 {
            font-size: 14pt !important;
            margin: 5px 0 !important;
            line-height: 1.2 !important;
            color: black !important;
            font-weight: bold !important;
        }
        .invoice-section {
            margin-bottom: 8px !important;
            page-break-inside: avoid !important;
            background: white !important;
            color: black !important;
        }
        .invoice-section h3 {
            font-size: 12pt !important;
            margin: 5px 0 !important;
            padding-bottom: 3px !important;
            border-bottom: 1pt solid #000 !important;
            color: black !important;
            font-weight: bold !important;
            background: white !important;
        }
        .invoice-table {
            font-size: 6pt !important;
            table-layout: fixed !important;
            width: 100% !important;
            margin-bottom: 2px !important;
            border-collapse: collapse !important;
            background: white !important;
            color: black !important;
            word-wrap: break-word !important;
        }
        .invoice-table th, .invoice-table td {
            padding: 4px !important;
            font-size: 11pt !important;
            white-space: normal !important;
            overflow: visible !important;
            text-overflow: clip !important;
            border: 1pt solid #000 !important;
            line-height: 1.2 !important;
            background: white !important;
            color: black !important;
            word-wrap: break-word !important;
            max-width: none !important;
        }
        .invoice-table th {
            background-color: #f0f0f0 !important;
            font-weight: bold !important;
            color: black !important;
        }
        .text-right {
            text-align: right !important;
        }
        .btn-print {
            display: none !important;
        }
        .badge-success {
            background-color: #28a745 !important;
            color: white !important;
            padding: 2px 4px !important;
            border-radius: 3px !important;
            font-size: 8pt !important;
        }
        .badge-danger {
            background-color: #dc3545 !important;
            color: white !important;
            padding: 2px 4px !important;
            border-radius: 3px !important;
            font-size: 8pt !important;
        }
        #barcode-container {
            display: block !important;
            text-align: center !important;
            margin: 10px 0 !important;
        }
        #barcode {
            width: 80px !important;
            height: 80px !important;
        }
        * {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            background: white !important;
            color: black !important;
        }
    }
</style>
@endsection
@section('content')
<div class="invoice-container print-only" id="print">
    <div class="invoice-header">
        <h1>جمهورية العراق</h1>
        <h2>وزارة الصحة</h2>
        <h1>فاتورة حساب الجناح الخاص</h1>
        @if($privateWing->cancelled_at)
            <div class="alert alert-warning text-center mt-3">
                <h4><i class="fas fa-exclamation-triangle"></i> هذه القائمة ملغاة</h4>
                <p><strong>سبب الإبطال:</strong> {{ $privateWing->cancellation_reason }}</p>
                <p><strong>تاريخ الإبطال:</strong> {{ $privateWing->cancelled_at->format('Y-m-d H:i') }}</p>
                @if($privateWing->cancelled_by)
                    <p><strong>أبطلها:</strong> {{ $privateWing->cancelledByUser ? $privateWing->cancelledByUser->name : 'غير محدد' }}</p>
                @endif
            </div>
        @endif
    </div>

    <div class="invoice-section">
        <h3>بيانات المريض والمستشفى</h3>
        <table class="invoice-table">
            <tr>
                <th>المستشفى</th>
                <td>{{ $privateWing->hospital }}</td>
                <th>دائرة الصحة</th>
                <td>{{ $privateWing->health_department }}</td>
                <th rowspan="3" style="vertical-align: middle; text-align: center; width: 120px;">
                    <div id="barcode-container" style="display: inline-block;"></div>
                    <br><span style="font-size: 8px; color: #666;">امسح للعرض</span>
                </th>
            </tr>
            <tr>
                <th>اسم المريض</th>
                <td>{{ $privateWing->patient_name }}</td>
                <th>الترقيم السنوي</th>
                <td>{{ $privateWing->annual_number }} / {{ $privateWing->fiscal_year }}</td>
            </tr>
            <tr>
                <th>الرقم الإحصائي</th>
                <td>{{ $privateWing->statistical_number }}</td>
            </tr>
        </table>
    </div>

    <div class="invoice-section">
        <h3>الخدمات المقدمة</h3>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>الخدمة</th>
                    <th>الطبيب</th>
                    <th>الدرجة</th>
                    <th>الأجور</th>
                    <th>الأيام</th>
                    <th>الإجمالي</th>
                    @if($privateWing->is_insured)
                        <th>نسبة الضمان (%)</th>
                        <th>مبلغ المريض</th>
                        <th>مبلغ الضمان</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                <tr>
                    <td>{{ $service->service_name }}</td>
                    <td>{{ $service->doctor_name ?? '-' }}</td>
                    <td>{{ $service->operation_degree ?? '-' }}</td>
                    <td>{{ number_format($service->service_fee, 0) }}</td>
                    <td>{{ $service->is_daily ? 'نعم' : 'لا' }}</td>
                    <td>{{ number_format($service->total_amount, 0) }}</td>
                    @if($privateWing->is_insured)
                        <td>{{ $service->is_insured ? ($service->insurance_percentage ?? 10) : 'غير مشمول' }}</td>
                        <td>{{ number_format($service->patient_amount ?? $service->total_amount, 0) }}</td>
                        <td>{{ number_format($service->insurance_amount ?? 0, 0) }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="invoice-section">
        <h3>بيانات إضافية</h3>
        <table class="invoice-table">
            <tr>
                <th>الجنسية</th>
                <td>{{ $privateWing->patient_nationality }}</td>
                <th>الأيام</th>
                <td>{{ $privateWing->days_count }}</td>
            </tr>
            <tr>
                <th>تاريخ التنظيم</th>
                <td>{{ $privateWing->created_at ? $privateWing->created_at->format('Y-m-d') : '' }}</td>
                <th>مشمول بالضمان</th>
                <td>{{ $privateWing->is_insured ? 'نعم' : 'لا' }}</td>
            </tr>
            <tr>
                <th>تاريخ الدخول</th>
                <td>
                    {{ $privateWing->entry_date ? $privateWing->entry_date->format('Y-m-d') : '' }}
                    @if($privateWing->entry_time)
                        <br><small>{{ $privateWing->entry_time }}</small>
                    @endif
                </td>
                <th>تاريخ الخروج</th>
                <td>
                    {{ $privateWing->exit_date ? $privateWing->exit_date->format('Y-m-d') : '' }}
                    @if($privateWing->exit_time)
                        <br><small>{{ $privateWing->exit_time }}</small>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="invoice-section">
        <h3>وصولات التأمين</h3>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>رقم الوصل</th>
                    <th>المبلغ</th>
                    <th>التاريخ</th>
                    <th>النوع</th>
                    <th>النسبة (%)</th>
                    <th>الفائدة</th>
                    <th>الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($privateWing->receipts as $index => $receipt)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $receipt->receipt_number }}</td>
                    <td>{{ number_format($receipt->amount, 0) }}</td>
                    <td>{{ $receipt->receipt_date->format('Y-m-d') }}</td>
                    <td>{{ $receipt->payment_type }}</td>
                    <td>{{ number_format($receipt->interest_rate, 2) }}</td>
                    <td>{{ number_format($receipt->interest_amount, 0) }}</td>
                    <td>{{ number_format($receipt->amount + $receipt->interest_amount, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="invoice-section">
        <h3>ملخص الحساب</h3>
        <table class="invoice-table">
            @php
                // حساب إجمالي الخدمات
                $totalServices = $privateWing->total_amount;
                
                // حساب مبلغ المريض
                $totalPatientAmount = 0;
                $totalInsuranceAmount = 0;
                
                foreach($services as $service) {
                    if($privateWing->is_insured && $service->is_insured) {
                        $totalPatientAmount += $service->patient_amount ?? 0;
                        $totalInsuranceAmount += $service->insurance_amount ?? 0;
                    } else {
                        $totalPatientAmount += $service->total_amount;
                    }
                }
                
                // حساب الإيداعات والفوائد
                $totalDeposits = $privateWing->receipts->sum('amount');
                $totalInterest = $privateWing->receipts->sum('interest_amount');
                $totalDepositsWithInterest = $totalDeposits + $totalInterest;
                
                // المعادلة: المبلغ المتبقي = مبلغ الوصولات (بدون فائدة) - مبلغ المريض
                $remaining = $totalDeposits - $totalPatientAmount;
            @endphp
            
            <tr>
                <th>إجمالي الخدمات (الكلي)</th>
                <td class="text-right"><strong>{{ number_format($totalServices, 0) }} د.ع</strong></td>
            </tr>
            @if($privateWing->is_insured)
            <tr>
                <th>مبلغ المريض</th>
                <td class="text-right"><strong>{{ number_format($totalPatientAmount, 0) }} د.ع</strong></td>
            </tr>
            <tr>
                <th>مبلغ الضمان</th>
                <td class="text-right"><strong>{{ number_format($totalInsuranceAmount, 0) }} د.ع</strong></td>
            </tr>
            @endif
            <tr>
                <th>إجمالي الوصولات (بدون فائدة)</th>
                <td class="text-right"><strong>{{ number_format($totalDeposits, 0) }} د.ع</strong></td>
            </tr>
            <tr>
                <th>إجمالي الفائدة</th>
                <td class="text-right"><strong>{{ number_format($totalInterest, 0) }} د.ع</strong></td>
            </tr>
            <tr>
                <th>إجمالي الإيداعات (مع الفائدة)</th>
                <td class="text-right"><strong>{{ number_format($totalDepositsWithInterest, 0) }} د.ع</strong></td>
            </tr>
            <tr style="background-color: #f0f8ff;">
                <th>المبلغ المتبقي</th>
                <td class="text-right">
                    <strong class="{{ $remaining < 0 ? 'text-danger' : ($remaining > 0 ? 'text-success' : 'text-info') }}">
                        {{ number_format(abs($remaining), 0) }} د.ع
                        @if($remaining < 0)
                            (عليه مبلغ)
                        @elseif($remaining > 0)
                            (له مبلغ)
                        @else
                            (مسدد)
                        @endif
                    </strong>
                </td>
            </tr>
        </table>
    </div>

    @if($remaining > 0 && !$privateWing->cancelled_at)
    <!-- قسم الاسترداد -->
    <div class="invoice-section no-print">
        <h3>إدارة الاسترداد</h3>
        <div class="alert alert-success">
            <strong>المبلغ المتاح للاسترداد:</strong> {{ number_format($remaining - ($privateWing->refund_amount ?? 0), 0) }} د.ع
        </div>

        @if($remaining - ($privateWing->refund_amount ?? 0) > 0)
        <!-- نموذج إضافة وصل استرداد -->
        <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addRefundModal">
            <i class="fas fa-plus"></i> إضافة وصل استرداد
        </button>
        @endif

        @if($privateWing->refunds && $privateWing->refunds->count() > 0)
        <!-- جدول وصولات الاسترداد -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>المبلغ</th>
                    <th>رقم الوصل</th>
                    <th>تاريخ الاسترداد</th>
                    <th>اسم المستلم</th>
                    <th>رقم الهوية</th>
                    <th>ملاحظات</th>
                    <th class="no-print">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($privateWing->refunds as $refund)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ number_format($refund->amount, 0) }} د.ع</td>
                    <td>{{ $refund->receipt_number }}</td>
                    <td>{{ $refund->refund_date->format('Y-m-d') }}</td>
                    <td>{{ $refund->receiver_name }}</td>
                    <td>{{ $refund->receiver_id_number ?? '-' }}</td>
                    <td>{{ $refund->notes ?? '-' }}</td>
                    <td class="no-print">
                        <form action="{{ route('private-wings.delete-refund', $refund->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا الوصل؟')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="7" class="text-left"><strong>إجمالي المسترد:</strong></td>
                    <td><strong>{{ number_format($privateWing->refund_amount ?? 0, 0) }} د.ع</strong></td>
                </tr>
            </tbody>
        </table>
        @else
        <p class="text-muted">لا توجد وصولات استرداد حتى الآن</p>
        @endif
    </div>
    @endif

    <button class="btn-print" onclick="printDiv()">طباعة الفاتورة</button>
</div>

<!-- Modal إضافة وصل استرداد -->
<div class="modal fade" id="addRefundModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('private-wings.add-refund', $privateWing->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة وصل استرداد</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>المبلغ المسترد (د.ع)</label>
                        <input type="number" name="amount" class="form-control" required min="0" 
                               max="{{ $remaining - ($privateWing->refund_amount ?? 0) }}" step="0.01">
                        <small class="text-muted">الحد الأقصى: {{ number_format($remaining - ($privateWing->refund_amount ?? 0), 0) }} د.ع</small>
                    </div>
                    <div class="form-group">
                        <label>رقم وصل الاسترداد</label>
                        <input type="text" name="receipt_number" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>تاريخ الاسترداد</label>
                        <input type="date" name="refund_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>اسم المستلم</label>
                        <input type="text" name="receiver_name" class="form-control" required value="{{ $privateWing->patient_name }}">
                    </div>
                    <div class="form-group">
                        <label>رقم هوية المستلم</label>
                        <input type="text" name="receiver_id_number" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs2@0.0.2/qrcode.min.js"></script>
<script type="text/javascript">
	function printDiv() {
		var printContents = document.getElementById('print').innerHTML;
		var originalContents = document.body.innerHTML;
		document.body.innerHTML = printContents;
		window.print();
		document.body.innerHTML = originalContents;
		location.reload();
	}

	// إنشاء QR Code عند تحميل الصفحة
	window.addEventListener('load', function() {
		console.log('Starting QR Code generation');
		var qrData = '{{ route("private-wings.qr", $privateWing->id) }}'; // رابط ذكي - PDF للموبايل، صفحة للكمبيوتر
		console.log('QR Data:', qrData);
		var container = document.getElementById('barcode-container');
		console.log('Container element:', container);
		
		if (container) {
			try {
				// إنشاء QR Code
				new QRCode(container, {
					text: qrData,
					width: 100,
					height: 100,
					colorDark: "#000000",
					colorLight: "#ffffff",
					correctLevel: QRCode.CorrectLevel.H
				});
				console.log('QR Code generated successfully');
			} catch (error) {
				console.error('QR Code error:', error);
			}
		} else {
			console.error('Container element not found');
		}
	});
</script>
@endsection
