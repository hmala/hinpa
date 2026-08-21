@extends('layouts.master')
@section('title', 'وصل استرداد')
@section('css')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap');
    
    body {
        background-color: #e9ecef;
        font-family: 'Amiri', 'Arial', sans-serif;
    }
    
    * {
        font-family: 'Amiri', 'Arial', sans-serif !important;
        box-sizing: border-box;
    }
    
    .receipt-container {
        width: 148mm;
        min-height: 210mm;
        margin: 20px auto;
        background: #ffffff;
        padding: 10mm 15mm;
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
        position: relative;
    }
    
    /* رقم الوصل في الأعلى */
    .receipt-number-top {
        position: absolute;
        top: 20mm;
        left: 25mm;
        font-size: 32px;
        font-weight: bold;
        color: #000;
        letter-spacing: 2px;
    }
    
    /* شعار الوزارة */
    .ministry-header {
        position: absolute;
        top: 15mm;
        right: 20mm;
        text-align: right;
        line-height: 1.3;
    }
    
    .ministry-header p {
        margin: 2px 0;
        font-size: 13px;
        color: #000;
    }
    
    /* الترويسة */
    .receipt-header {
        text-align: center;
        margin-top: 25mm;
        margin-bottom: 15mm;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
    }
    
    .receipt-header h1 {
        font-size: 18px;
        color: #000;
        margin: 4px 0;
        font-weight: 700;
    }
    
    .receipt-header h2 {
        font-size: 16px;
        color: #000;
        margin: 3px 0;
        font-weight: 400;
    }
    
    /* عنوان الوصل */
    .receipt-title {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin: 20px 0;
        padding: 8px;
        color: #000;
        text-decoration: underline;
        text-underline-offset: 5px;
    }
    
    /* التاريخ */
    .receipt-date {
        text-align: center;
        font-size: 16px;
        margin-bottom: 25px;
        direction: rtl;
    }
    
    /* محتوى الوصل */
    .receipt-body {
        margin: 30px 0;
        line-height: 2.2;
        font-size: 17px;
        text-align: right;
        direction: rtl;
        text-align: justify;
    }
    
    .receipt-text {
        margin: 0;
        padding: 0;
        display: inline;
        text-align: justify;
    }
    
    .receipt-paragraph {
        margin: 15px 0;
        text-align: justify;
        line-height: 2.5;
    }
    
    .inline-field {
        display: inline;
        border-bottom: 1px solid #000;
        padding: 0 15px;
        min-width: 120px;
        text-align: center;
        font-weight: bold;
    }
    
    .long-field {
        display: inline;
        border-bottom: 1px solid #000;
        padding: 0 15px;
        min-width: 250px;
        text-align: center;
        font-weight: normal;
    }
    
    /* منطقة التوقيعات */
    .signatures-area {
        margin-top: 60mm;
        position: relative;
    }
    
    .signature-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        text-align: center;
        direction: rtl;
    }
    
    .signature-item {
        text-align: center;
    }
    
    .signature-title {
        font-size: 14px;
        margin-bottom: 3px;
        color: #000;
    }
    
    .signature-name {
        font-size: 14px;
        margin: 3px 0;
        min-height: 50px;
        color: #000;
    }
    
    .signature-line-text {
        font-size: 13px;
        color: #000;
        padding-top: 5px;
        border-top: 1px solid #000;
        margin-top: 3px;
    }
    
    /* منطقة الختم */
    .stamp-box {
        position: absolute;
        bottom: 50mm;
        right: 40mm;
        width: 80px;
        height: 80px;
        border: 1.5px dashed #aaa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        color: #999;
        transform: rotate(-15deg);
    }
    
    /* التذييل */
    .footer-text {
        position: absolute;
        bottom: 10mm;
        left: 20mm;
        right: 20mm;
        font-size: 10px;
        text-align: center;
        color: #555;
        border-top: 0.5px solid #ccc;
        padding-top: 8px;
    }
    
    /* أزرار التحكم */
    .control-buttons {
        margin-top: 30px;
        text-align: center;
    }
    
    .btn-print {
        padding: 12px 35px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        margin: 0 5px;
        transition: all 0.3s;
    }
    
    .btn-print:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .btn-back {
        padding: 12px 35px;
        background-color: #6c757d;
        color: #fff;
        border: none;
        border-radius: 6px;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 0 5px;
        transition: all 0.3s;
    }
    
    .btn-back:hover {
        background-color: #545b62;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        color: #fff;
    }
    
    .no-print {
        display: block;
    }
    
    @media print {
        @page {
            size: A4 portrait;
            margin: 0;
        }
        
        /* إخفاء كل محتويات الصفحة */
        body * {
            visibility: hidden;
        }
        
        /* إظهار الوصل فقط */
        .receipt-container,
        .receipt-container * {
            visibility: visible;
        }
        
        html, body {
            background: white !important;
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;
        }
        
        .receipt-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 210mm;
            min-height: 297mm;
            margin: 0;
            padding: 15mm 20mm;
            box-shadow: none;
            page-break-after: avoid;
        }
        
        .no-print {
            display: none !important;
        }
        
        .stamp-box {
            border-color: transparent;
            color: transparent;
        }
        
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
@endsection

@section('content')
<div class="receipt-container">
    <!-- رقم الوصل في الأعلى يسار -->
    <div class="receipt-number-top">
        {{ $latestRefund->receipt_number }}
    </div>

    <!-- معلومات الوزارة في الأعلى يمين -->
    <div class="ministry-header no-print">
        <p>وزارة الصحة</p>
        <p>دائرة صحة ..................</p>
        <p>الجناح الخاص </p>
    </div>

    <!-- الترويسة المركزية -->
    <div class="receipt-header">
        <h1>جمهورية العراق</h1>
        <h1>وزارة الصحة</h1>
        <h2>دائرة {{ $privateWing->health_department ?? 'صحة ........................' }}</h2>
        <h2>{{ $privateWing->hospital }}</h2>
        <h2>الجناح الخاص</h2>
    </div>

    <!-- عنوان الوصل -->
    <div class="receipt-title">
        ورقة تأييد استلام منتفع تأمينات المريض
    </div>

    <!-- التاريخ -->
    <div class="receipt-date">
        ت/ {{ $latestRefund->refund_date->format('Y/n/j') }}
    </div>

    <!-- محتوى الوصل -->
    <div class="receipt-body">
        <div class="receipt-paragraph">
            انني: <span class="inline-field">{{ $latestRefund->receiver_name }}</span> صاحب الهوية: <span class="inline-field">{{ $latestRefund->receiver_id_number ?? '..................................' }}</span> اؤيد استلامي للمبلغ: <span class="inline-field">{{ number_format($latestRefund->amount, 0) }}</span> دينار (رقماً)
        </div>
        
        <div class="receipt-paragraph">
            وقدره: <span class="long-field">{{ \App\Helpers\NumberToArabicWords::convert($latestRefund->amount) }}</span> (كتابة)
        </div>
        
        <div class="receipt-paragraph">
            عن متبقي مبلغ التأمينات لقائمة الحساب رقم: <span class="inline-field">{{ $privateWing->annual_number }}/{{ $privateWing->fiscal_year }}</span> للمريض: <span class="inline-field">{{ $privateWing->patient_name }}</span>
        </div>

        @if($latestRefund->notes)
        <div class="receipt-paragraph" style="margin-top: 25px;">
            ملاحظات: <span class="long-field">{{ $latestRefund->notes }}</span>
        </div>
        @endif
    </div>

    <!-- منطقة الختم -->
    <div class="stamp-box no-print">
        ختم
    </div>

    <!-- قسم التوقيعات -->
    <div class="signatures-area">
        <div class="signature-grid">
            <div class="signature-item">
                <div class="signature-title">اسم وتوقيع المدقق</div>
                <div class="signature-name">&nbsp;</div>
                <div class="signature-line-text">التوقيع</div>
            </div>
            
            <div class="signature-item">
                <div class="signature-title">اسم وتوقيع المحاسب</div>
                <div class="signature-name">&nbsp;</div>
                <div class="signature-line-text">التوقيع</div>
            </div>

            <div class="signature-item">
                <div class="signature-title">اسم وتوقيع المريض</div>
                <div class="signature-name">{{ $latestRefund->receiver_name }}</div>
                <div class="signature-line-text">التوقيع</div>
            </div>
        </div>
    </div>

    <!-- التذييل -->
    <div class="footer-text">
        وزارة الصحة / مطابع دائرة المالية الطبية الدولية للطباعة
    </div>

    <!-- أزرار التحكم -->
    <div class="control-buttons no-print">
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> طباعة الوصل
        </button>
        
        <a href="{{ route('private-wings.index') }}" class="btn-back">
            <i class="fas fa-arrow-right"></i> رجوع للقائمة
        </a>
    </div>
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
