<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة وصل خدمة خارجية - {{ $visit->visitor_name }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 20px; direction: rtl; text-align: right; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0,0,0,0.15); font-size: 14px; line-height: 24px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        .table th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; background-color: #e9ecef; }
        .btn-print { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 15px; font-size: 16px; }
        @media print { .btn-print { display: none; } }
    </style>
</head>
<body>

<div style="text-align: center;">
    <button onclick="window.print()" class="btn-print">طباعة الوصل</button>
</div>

<div class="invoice-box">
    <div class="header">
        <h2>{{ $visit->health_department }}</h2>
        <h3>{{ $visit->hospital }}</h3>
        <h4>وصل استلام خدمات خارجية (زيارة)</h4>
    </div>

    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td><strong>اسم الزائر:</strong> {{ $visit->visitor_name }}</td>
            <td><strong>تاريخ الزيارة:</strong> {{ $visit->visit_date ? $visit->visit_date->format('Y-m-d') : '' }}</td>
        </tr>
        <tr>
            <td><strong>رقم الهاتف:</strong> {{ $visit->visitor_phone ?: '-' }}</td>
            <td><strong>رقم الوصل:</strong> {{ $visit->receipt_number }}</td>
        </tr>
        <tr>
            <td><strong>طريقة الدفع:</strong> {{ $visit->payment_type }}</td>
            <td><strong>تاريخ الوصل:</strong> {{ $visit->receipt_date ? $visit->receipt_date->format('Y-m-d') : '' }}</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>اسم الخدمة</th>
                <th>الطبيب المعالج</th>
                <th>درجة العملية</th>
                <th>المبلغ المسدد</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visit->services as $service)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $service->serviceSpecialization ? $service->serviceSpecialization->namesv : '-' }}</td>
                <td>{{ $service->doctor ? $service->doctor->name : '-' }}</td>
                <td>{{ $service->operation_degree ?: '-' }}</td>
                <td>{{ number_format($service->service_fee, 2) }} د.ع</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4">الإجمالي الكلي المسدد</td>
                <td>{{ number_format($visit->total_amount, 2) }} د.ع</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 40px; display: flex; justify-content: space-between;">
        <div>منظم الوصل: __________________</div>
        <div>المحاسب: __________________</div>
    </div>
</div>

</body>
</html>
