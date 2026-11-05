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
                                <div><strong>الترقيم السنوي:</strong> {{ $privateWing->annual_number }}/{{ $privateWing->fiscal_year }}</div>
                                <div><strong>الرقم الإحصائي:</strong> {{ $privateWing->statistical_number }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>البيان</td>
                <td>الطبيب</td>
                <td>درجة العملية</td>
                <td>عدد الأيام</td>
                <td>المبلغ (د.ع)</td>
                @if($privateWing->is_insured)
                    <td>الضمان (%)</td>
                    <td>مبلغ المريض</td>
                    <td>مبلغ الضمان</td>
                @endif
            </tr>
            @foreach($services as $service)
            <tr class="item">
                <td>{{ $service->service_name }}</td>
                <td>{{ $service->doctor_name ?? '' }}</td>
                <td>{{ $service->operation_degree ?? '' }}</td>
                <td>{{ $service->is_daily ? $privateWing->days_count : '-' }}</td>
                <td>{{ number_format($service->total_amount, 0) }}</td>
                @if($privateWing->is_insured)
                    <td>{{ $service->is_insured ? ($service->insurance_percentage ?? 10) : '-' }}</td>
                    <td>{{ number_format($service->patient_amount ?? $service->total_amount, 0) }}</td>
                    <td>{{ number_format($service->insurance_amount ?? 0, 0) }}</td>
                @endif
            </tr>
            @endforeach

            @php
                // حساب المبلغ المريض والضمان
                $totalPatient = 0;
                $totalInsurance = 0;
                foreach($services as $service) {
                    if($privateWing->is_insured && $service->is_insured) {
                        $totalPatient += $service->patient_amount ?? 0;
                        $totalInsurance += $service->insurance_amount ?? 0;
                    } else {
                        $totalPatient += $service->total_amount;
                    }
                }
                
                $exceptionDiscount = 0;
                if (($privateWing->exception_percentage ?? 0) > 0) {
                    $exceptionDiscount = $totalPatient * ($privateWing->exception_percentage / 100);
                    $totalPatient = $totalPatient - $exceptionDiscount;
                }
                
                // حساب الفائدة والمجموع الكلي المطلوب والوصولات
                $interestAmount = $privateWing->interest_amount ?? 0;
                $grandTotal = $totalPatient + $interestAmount;
                $totalDeposits = $privateWing->receipts->sum('amount');
                
                // المعادلة: المبلغ المتبقي = إجمالي الوصولات - المجموع الكلي المطلوب
                $remaining = $totalDeposits - $grandTotal;
            @endphp

            <tr class="total">
                <td colspan="{{ $privateWing->is_insured ? 8 : 4 }}">إجمالي الخدمات</td>
                <td>{{ number_format($totalAmount, 0) }} د.ع</td>
            </tr>
            
            @if($privateWing->is_insured)
            <tr class="item">
                <td colspan="{{ $privateWing->is_insured ? 8 : 4 }}">مبلغ المريض</td>
                <td>{{ number_format($totalPatient + $exceptionDiscount, 0) }} د.ع</td>
            </tr>
            <tr class="item">
                <td colspan="{{ $privateWing->is_insured ? 8 : 4 }}">مبلغ الضمان</td>
                <td>{{ number_format($totalInsurance, 0) }} د.ع</td>
            </tr>
            @endif

            @if(($privateWing->exception_percentage ?? 0) > 0)
            <tr class="item" style="background-color: #f8f0ff;">
                <td colspan="{{ $privateWing->is_insured ? 8 : 4 }}">نسبة الاستثناء ({{ $privateWing->exception_from ?: 'استثناء رسمي' }}) - {{ (float)$privateWing->exception_percentage }}%</td>
                <td style="color: #6f42c1; font-weight: bold;">-{{ number_format($exceptionDiscount, 0) }} د.ع</td>
            </tr>
            @endif

            @if($interestAmount > 0)
            <tr class="item">
                <td colspan="{{ $privateWing->is_insured ? 8 : 4 }}">مبلغ الفائدة المضاف</td>
                <td>{{ number_format($interestAmount, 0) }} د.ع</td>
            </tr>
            @endif

            <tr class="item" style="font-weight: bold; background: #f8f9fa;">
                <td colspan="{{ $privateWing->is_insured ? 8 : 4 }}">المجموع الكلي المطلوب</td>
                <td>{{ number_format($grandTotal, 0) }} د.ع</td>
            </tr>

            <tr class="item">
                <td colspan="{{ $privateWing->is_insured ? 8 : 4 }}">إجمالي وصولات التأمين</td>
                <td>{{ number_format($totalDeposits, 0) }} د.ع</td>
            </tr>

            <tr class="total">
                <td colspan="{{ $privateWing->is_insured ? 8 : 4 }}">المبلغ المتبقي</td>
                <td>{{ number_format(abs($remaining), 0) }} د.ع {{ $remaining < 0 ? '(عليه)' : ($remaining > 0 ? '(له / استرداد)' : '(خالص)') }}</td>
            </tr>
        </table>

        @if($privateWing->receipts->count() > 0)
        <div style="margin-top: 20px">
            <h4>وصولات التأمين:</h4>
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="background: #eee; border: 1px solid #ddd;">
                    <th style="border: 1px solid #ddd; padding: 5px; text-align: center;">#</th>
                    <th style="border: 1px solid #ddd; padding: 5px;">رقم الوصل</th>
                    <th style="border: 1px solid #ddd; padding: 5px;">المبلغ (د.ع)</th>
                    <th style="border: 1px solid #ddd; padding: 5px;">التاريخ</th>
                    <th style="border: 1px solid #ddd; padding: 5px;">نوع الدفع</th>
                </tr>
                @foreach($privateWing->receipts as $index => $receipt)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 5px; text-align: center;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #ddd; padding: 5px;">{{ $receipt->receipt_number }}</td>
                    <td style="border: 1px solid #ddd; padding: 5px;">{{ number_format($receipt->amount, 0) }}</td>
                    <td style="border: 1px solid #ddd; padding: 5px;">{{ $receipt->receipt_date ? $receipt->receipt_date->format('Y-m-d') : '' }}</td>
                    <td style="border: 1px solid #ddd; padding: 5px;">{{ $receipt->payment_type }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif

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
