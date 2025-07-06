@extends('layouts.master')
@section('title')
طباعة الفاتورة
@stop
@section('css')
<style>
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        font-size: 16px;
        line-height: 24px;
        direction: rtl;
    }

    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: right;
    }

    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }

    .invoice-box table tr td:nth-child(2) {
        text-align: left;
    }

    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.information table td {
        padding-bottom: 40px;
    }

    .invoice-box table tr.heading td {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }

    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.item td {
        border-bottom: 1px solid #eee;
    }

    .invoice-box table tr.total td {
        border-top: 2px solid #eee;
        font-weight: bold;
    }

    @media print {
        .no-print {
            display: none;
        }
        .invoice-box {
            margin: 0;
            border: none;
            box-shadow: none;
        }
    }
</style>
@endsection
@section('content')
<div class="container">
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="4">
                    <table>
                        <tr>
                            <td>
                                <h2>فاتورة الجناح الخاص</h2>
                                <div>رقم الفاتورة: {{ $privateWing->id }}</div>
                                <div>تاريخ الإصدار: {{ now()->format('Y-m-d') }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="4">
                    <table>
                        <tr>
                            <td>
                                <div><strong>المستشفى:</strong> {{ $privateWing->hospital }}</div>
                                <div><strong>دائرة صحة:</strong> {{ $privateWing->health_department }}</div>
                            </td>

                            <td>
                                <div><strong>اسم المريض:</strong> {{ $privateWing->patient_name }}</div>
                                <div><strong>رقم الملف:</strong> {{ $privateWing->file_number }}</div>
                                <div><strong>الرقم الإحصائي:</strong> {{ $privateWing->statistical_number }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td colspan="2">البيان</td>
                <td>عدد الأيام</td>
                <td>المبلغ (د.ع)</td>
            </tr>            <tr class="item">
                <td colspan="2">أجور رقود المريض</td>
                <td>{{ $privateWing->days_count }}</td>
                <td>{{ number_format($privateWing->patient_bed_fee * $privateWing->days_count, 2) }}</td>
            </tr>

            @if($privateWing->companion_bed_fee > 0)
            <tr class="item">
                <td colspan="2">أجور رقود المرافق</td>
                <td>{{ $privateWing->days_count }}</td>
                <td>{{ number_format($privateWing->companion_bed_fee, 2) }}</td>
            </tr>
            @endif

            @if($privateWing->nutrition_fee > 0)
            <tr class="item">
                <td colspan="2">أجور التغذية</td>
                <td>-</td>
                <td>{{ number_format($privateWing->nutrition_fee, 2) }}</td>
            </tr>
            @endif

            @if($privateWing->medicine_supplies_fee > 0)
            <tr class="item">
                <td colspan="2">أجور الأدوية والمستلزمات</td>
                <td>-</td>
                <td>{{ number_format($privateWing->medicine_supplies_fee, 2) }}</td>
            </tr>
            @endif

            @if($privateWing->laboratory_tests_fee > 0)
            <tr class="item">
                <td colspan="2">أجور الفحوصات المختبرية</td>
                <td>-</td>
                <td>{{ number_format($privateWing->laboratory_tests_fee, 2) }}</td>
            </tr>
            @endif

            @if($privateWing->xray_fees > 0)
            <tr class="item">
                <td colspan="2">أجور الأشعة</td>
                <td>-</td>
                <td>{{ number_format($privateWing->xray_fees, 2) }}</td>
            </tr>
            @endif

            @if($privateWing->sonar_fees > 0)
            <tr class="item">
                <td colspan="2">أجور فحوصات السونار</td>
                <td>-</td>
                <td>{{ number_format($privateWing->sonar_fees, 2) }}</td>
            </tr>
            @endif

            <tr class="total">
                <td colspan="3">إجمالي المبلغ</td>
                <td>{{ number_format($privateWing->total_amount ?? 0, 2) }} د.ع</td>
            </tr>

            <tr class="item">
                <td colspan="3">مبلغ التأمينات</td>
                <td>{{ number_format($privateWing->deposit_amount, 2) }} د.ع</td>
            </tr>

            <tr class="total">
                <td colspan="3">المبلغ المتبقي</td>
                <td>{{ number_format(($privateWing->total_amount ?? 0) - $privateWing->deposit_amount, 2) }} د.ع</td>
            </tr>
        </table>

        <div style="margin-top: 40px">
            <p><strong>رقم وصل التأمينات:</strong> {{ $privateWing->receipt_number }}</p>
            <p><strong>تاريخ الوصل:</strong> {{ $privateWing->receipt_date }}</p>
        </div>

        <div style="margin-top: 60px">
            <p style="text-align: center">التوقيع: ________________</p>
        </div>
    </div>

    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-primary">طباعة الفاتورة</button>
        <a href="{{ route('private-wings.show', $privateWing) }}" class="btn btn-secondary">رجوع</a>
    </div>
</div>
@endsection
