@extends('layouts.master')
@section('title', 'وصل استرداد')
@section('css')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');
    
    body {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        font-family: 'Cairo', 'Arial', sans-serif;
        min-height: 100vh;
        padding: 20px 0;
    }
    
    * {
        font-family: 'Cairo', 'Arial', sans-serif !important;
        box-sizing: border-box;
    }
    
    .receipt-container {
        width: 148mm;
        min-height: 210mm;
        margin: 20px auto;
        background: linear-gradient(to bottom, #ffffff 0%, #f8f9fa 100%);
        padding: 10mm 15mm;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        position: relative;
        border-radius: 8px;
        border: 3px solid #bdc3c7;
    }
    
    /* شريط علوي رسمي */
    .receipt-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #2c3e50, #34495e, #2c3e50);
        border-radius: 8px 8px 0 0;
    }
    
    /* رقم الوصل في الأعلى */
    .receipt-number-top {
        position: absolute;
        top: 12mm;
        left: 15mm;
        font-size: 28px;
        font-weight: 700;
        color: #1a252f;
        letter-spacing: 1px;
    }
    
    /* الترويسة */
    .receipt-header {
        text-align: center;
        margin-top: 20mm;
        margin-bottom: 12mm;
        padding: 15px;
        background: linear-gradient(135deg, #ecf0f115 0%, #bdc3c715 100%);
        border-radius: 10px;
        border-right: 4px solid #2c3e50;
        border-left: 4px solid #34495e;
    }
    
    .receipt-header h1 {
        font-size: 17px;
        color: #2c3e50;
        margin: 3px 0;
        font-weight: 700;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }
    
    .receipt-header h2 {
        font-size: 15px;
        color: #34495e;
        margin: 2px 0;
        font-weight: 500;
    }
    
    /* عنوان الوصل */
    .receipt-title {
        text-align: center;
        font-size: 17px;
        font-weight: 700;
        margin: 18px 0;
        padding: 12px;
        color: #fff;
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(44, 62, 80, 0.4);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* التاريخ */
    .receipt-date {
        text-align: center;
        font-size: 14px;
        margin-bottom: 20px;
        direction: rtl;
        color: #7f8c8d;
        font-weight: 600;
        background: #ecf0f1;
        display: inline-block;
        padding: 6px 20px;
        border-radius: 20px;
        margin-left: 50%;
        transform: translateX(-50%);
    }
    
    /* محتوى الوصل */
    .receipt-body {
        margin: 25px 0;
        line-height: 2.3;
        font-size: 15px;
        text-align: right;
        direction: rtl;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        border: 2px solid #e8e8e8;
    }
    
    .receipt-paragraph {
        margin: 12px 0;
        text-align: justify;
        line-height: 2.4;
        color: #2c3e50;
    }
    
    .inline-field {
        display: inline;
        border-bottom: 2px dotted #34495e;
        padding: 0 12px;
        min-width: 100px;
        text-align: center;
        font-weight: 700;
        color: #2c3e50;
        background: #ecf0f1;
    }
    
    .long-field {
        display: inline;
        border-bottom: 2px dotted #7f8c8d;
        padding: 0 12px;
        min-width: 200px;
        text-align: center;
        font-weight: 600;
        color: #34495e;
        background: #ecf0f1;
    }
    
    /* منطقة التوقيعات */
    .signatures-area {
        margin-top: 40mm;
        position: relative;
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        border: 2px solid #e8e8e8;
    }
    
    .signature-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        text-align: center;
        direction: rtl;
    }
    
    .signature-item {
        text-align: center;
        background: #fff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: transform 0.3s;
    }
    
    .signature-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(44, 62, 80, 0.2);
    }
    
    .signature-title {
        font-size: 13px;
        margin-bottom: 5px;
        color: #2c3e50;
        font-weight: 700;
    }
    
    .signature-name {
        font-size: 13px;
        margin: 5px 0;
        min-height: 40px;
        color: #2c3e50;
        font-weight: 600;
    }
    
    .signature-line-text {
        font-size: 12px;
        color: #7f8c8d;
        padding-top: 8px;
        border-top: 2px solid #2c3e50;
        margin-top: 5px;
        font-weight: 600;
    }
    
    /* منطقة الختم */
    .stamp-box {
        position: absolute;
        bottom: 35mm;
        right: 25mm;
        width: 70px;
        height: 70px;
        border: 2px dashed #e74c3c;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: #e74c3c;
        transform: rotate(-15deg);
        background: 25px;
        text-align: center;
    }
    
    .btn-print {
        padding: 12px 35px;
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: #fff;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-size: 15px;
        margin: 0 5px;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(44, 62, 80, 0.4);
        font-weight: 600;
    }
    
    .btn-print:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(44, 62, 80, 0.6);
    }
    
    .btn-back {
        padding: 12px 35px;
        background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
        color: #fff;
        border: none;
        border-radius: 25px;
        text-decoration: none;
        display: inline-block;
        font-size: 15px;
        margin: 0 5px;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(127, 140, 141, 0.3);
        font-weight: 600;
    }
    
    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(127, 140, 141, 0.5);
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
        
        body {
            background: white !important;
        }
        
        body * {
            visibility: hidden;
        }
        
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
            border: none;
            border-radius: 0;
            background: white !important;
        }
        
        .receipt-container::before {
            display: none;
        }
        
        /* تكبير المحتوى للطباعة */
        .receipt-number-top {
            font-size: 32px !important;
            top: 15mm !important;
            left: 20mm !important;
        }
        
        .receipt-header {
            margin-top: 25mm !important;
            margin-bottom: 15mm !important;
        }
        
        .receipt-header h1 {
            font-size: 20px !important;
        }
        
        .receipt-header h2 {
            font-size: 18px !important;
        }
        
        .receipt-title {
            font-size: 20px !important;
            padding: 15px !important;
            margin: 20px 0 !important;
        }
        
        .receipt-date {
            font-size: 16px !important;
            margin-bottom: 25px !important;
        }
        
        .receipt-body {
            font-size: 17px !important;
            line-height: 2.5 !important;
            padding: 25px !important;
        }
        
        .receipt-paragraph {
            margin: 18px 0 !important;
            line-height: 2.6 !important;
        }
        
        .inline-field {
            font-size: 16px !important;
            padding: 0 15px !important;
        }
        
        .long-field {
            font-size: 16px !important;
            padding: 0 15px !important;
        }
        
        .signatures-area {
            margin-top: 50mm !important;
            padding: 25px !important;
        }
        
        .signature-item {
            padding: 20px !important;
        }
        
        .signature-title {
            font-size: 15px !important;
        }
        
        .signature-name {
            font-size: 15px !important;
            min-height: 60px !important;
        }
        
        .signature-line-text {
            font-size: 14px !important;
        }
        
        .stamp-box {
            width: 90px !important;
            height: 90px !important;
            bottom: 40mm !important;
            right: 30mm !important;
            border-color: #ccc !important;
            color: #999 !important;
            box-shadow: none !important;
        }
        
        .footer-text {
            font-size: 11px !important;
            bottom: 12mm !important;
        }
        
        .no-print {
            display: none !important;
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
