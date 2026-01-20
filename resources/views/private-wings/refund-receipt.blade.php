@extends('layouts.master')
@section('title', 'وصل استرداد')
@section('css')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap');
    
    body {
        background-color: #f4f4f9;
        font-family: 'Cairo', 'DejaVu Sans', sans-serif;
    }
    
    * {
        font-family: 'Cairo', 'DejaVu Sans', sans-serif !important;
    }
    
    .receipt-container {
        max-width: 800px;
        margin: 40px auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 40px;
    }
    
    .receipt-header {
        text-align: center;
        margin-bottom: 30px;
        border-bottom: 3px solid #28a745;
        padding-bottom: 20px;
    }
    
    .receipt-header h1 {
        font-size: 28px;
        color: #333;
        margin: 10px 0;
    }
    
    .receipt-header h2 {
        font-size: 20px;
        color: #555;
        margin: 5px 0;
    }
    
    .receipt-title {
        background-color: #28a745;
        color: white;
        padding: 15px;
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        border-radius: 5px;
        margin: 20px 0;
    }
    
    .receipt-info {
        margin: 30px 0;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px;
        border-bottom: 1px solid #eee;
    }
    
    .info-row:nth-child(even) {
        background-color: #f9f9f9;
    }
    
    .info-label {
        font-weight: bold;
        color: #444;
        min-width: 200px;
    }
    
    .info-value {
        color: #666;
        text-align: left;
    }
    
    .amount-box {
        background-color: #e8f5e9;
        border: 2px solid #28a745;
        border-radius: 8px;
        padding: 20px;
        margin: 30px 0;
        text-align: center;
    }
    
    .amount-label {
        font-size: 18px;
        color: #333;
        margin-bottom: 10px;
    }
    
    .amount-value {
        font-size: 36px;
        font-weight: bold;
        color: #28a745;
    }
    
    .signature-section {
        margin-top: 50px;
        display: flex;
        justify-content: space-between;
    }
    
    .signature-box {
        text-align: center;
        min-width: 200px;
    }
    
    .signature-line {
        border-top: 2px solid #333;
        margin-top: 60px;
        padding-top: 10px;
    }
    
    .btn-print {
        display: block;
        margin: 30px auto 0;
        padding: 12px 30px;
        background-color: #28a745;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }
    
    .btn-print:hover {
        background-color: #218838;
    }
    
    .no-print {
        display: block;
    }
    
    @media print {
        @page {
            size: A4;
            margin: 15mm;
        }
        
        body {
            background: white !important;
        }
        
        .receipt-container {
            max-width: 100%;
            margin: 0;
            padding: 20px;
            box-shadow: none;
            border-radius: 0;
        }
        
        .no-print {
            display: none !important;
        }
        
        * {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
    }
</style>
@endsection

@section('content')
<div class="receipt-container">
    <div class="receipt-header">
        <h1>جمهورية العراق</h1>
        <h2>وزارة الصحة</h2>
        <h2>{{ $privateWing->health_department }}</h2>
        <h2>{{ $privateWing->hospital }}</h2>
    </div>

    <div class="receipt-title">
        وصل استرداد أموال
    </div>

    <div class="receipt-info">
        <div class="info-row">
            <span class="info-label">رقم الوصل:</span>
            <span class="info-value">{{ $latestRefund->receipt_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">التاريخ:</span>
            <span class="info-value">{{ $latestRefund->refund_date->format('Y-m-d') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">اسم المريض:</span>
            <span class="info-value">{{ $privateWing->patient_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">رقم الجناح (السنوي):</span>
            <span class="info-value">{{ $privateWing->annual_number }}/{{ $privateWing->fiscal_year }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">اسم المستلم:</span>
            <span class="info-value">{{ $latestRefund->receiver_name }}</span>
        </div>
        @if($latestRefund->receiver_id_number)
        <div class="info-row">
            <span class="info-label">رقم الهوية:</span>
            <span class="info-value">{{ $latestRefund->receiver_id_number }}</span>
        </div>
        @endif
    </div>

    <div class="amount-box">
        <div class="amount-label">المبلغ المسترد</div>
        <div class="amount-value">{{ number_format($latestRefund->amount, 0) }} د.ع</div>
    </div>

    @if($latestRefund->notes)
    <div class="receipt-info">
        <div class="info-row">
            <span class="info-label">ملاحظات:</span>
            <span class="info-value">{{ $latestRefund->notes }}</span>
        </div>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                توقيع المستلم
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                توقيع المحاسب
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                توقيع المدير
            </div>
        </div>
    </div>

    <button class="btn-print no-print" onclick="window.print()">
        <i class="fas fa-print"></i> طباعة الوصل
    </button>
    
    <a href="{{ route('private-wings.index') }}" class="btn btn-secondary no-print" style="display: block; text-align: center; margin-top: 10px; padding: 12px; text-decoration: none;">
        <i class="fas fa-arrow-right"></i> رجوع للقائمة
    </a>
</div>
@endsection

@section('js')
<script>
    // الطباعة التلقائية عند التحميل (اختياري - يمكن تفعيله)
    // window.addEventListener('load', function() {
    //     setTimeout(function() {
    //         window.print();
    //     }, 500);
    // });
</script>
@endsection
