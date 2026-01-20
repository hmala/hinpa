<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>فاتورة الجناح الخاص</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        
        @font-face {
            font-family: 'Tahoma';
            src: url('storage/fonts/tahoma.ttf') format('truetype');
        }
        
        * {
            font-family: 'Tahoma', 'Arial', 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            background: white;
            direction: rtl;
            text-align: right;
        }
        
        .invoice-container {
            width: 100%;
            padding: 10px;
        }
        
        .invoice-header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .invoice-header h1 {
            font-size: 16pt;
            margin: 3px 0;
            font-weight: bold;
        }
        
        .invoice-header h2 {
            font-size: 14pt;
            margin: 3px 0;
            font-weight: bold;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            margin: 10px 0;
            text-align: center;
        }
        
        .invoice-section {
            margin-bottom: 10px;
        }
        
        .invoice-section h3 {
            font-size: 12pt;
            margin: 5px 0;
            padding: 3px 0;
            border-bottom: 1px solid #000;
            font-weight: bold;
        }
        
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        
        .invoice-table th,
        .invoice-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-size: 9pt;
        }
        
        .invoice-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .text-right {
            text-align: right !important;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <h1>جمهورية العراق</h1>
            <h2>وزارة الصحة</h2>
            <h1>فاتورة حساب الجناح الخاص</h1>
            @if($privateWing->cancelled_at)
                <div class="alert-warning">
                    <h4>هذه القائمة ملغاة</h4>
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
                    <th>رقم الطبلة</th>
                    <td>{{ $privateWing->table_number }}</td>
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
                    <td>{{ $privateWing->entry_date ? $privateWing->entry_date->format('Y-m-d') : '' }}</td>
                    <th>تاريخ الخروج</th>
                    <td>{{ $privateWing->exit_date ? $privateWing->exit_date->format('Y-m-d') : '' }}</td>
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
                        <th>النسبة</th>
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
                        <td>{{ number_format($receipt->amount + $receipt->interest_amount, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="invoice-section">
            <h3>إجمالي المبلغ</h3>
            <table class="invoice-table">
                <tr>
                    <th>المبلغ الإجمالي</th>
                    <td class="text-right"><strong>{{ number_format($privateWing->total_amount, 0) }} د.ع</strong></td>
                </tr>
                <tr>
                    <th>المبلغ المتبقي</th>
                    <td class="text-right"><strong>{{ number_format($privateWing->total_amount - $privateWing->receipts->sum(function($receipt) { return $receipt->amount + $receipt->interest_amount; }), 0) }} د.ع</strong></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
