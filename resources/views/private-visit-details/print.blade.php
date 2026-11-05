<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف حسابات ووصولات الزيارات الخارجية المعتمد</title>
    <style>
        @page {
            size: landscape A4;
            margin: 8mm 6mm;
        }

        * {
            box-sizing: border-box;
            font-family: 'Tahoma', 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #f8fafc;
            color: #1e293b;
            font-size: 11px;
            direction: rtl;
            text-align: right;
            padding: 15px;
        }

        .report-wrapper {
            background: #ffffff;
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 15px 20px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        /* شريط أزرار التحكم العلوي (يختفي أثناء الطباعة) */
        .control-bar {
            background: #1e293b;
            color: #fff;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-block;
            font-weight: bold;
            padding: 6px 14px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
        }

        .btn-print { background: #2563eb; color: #fff; }
        .btn-print:hover { background: #1d4ed8; }
        .btn-close { background: #64748b; color: #fff; margin-right: 5px; }

        /* ترويسة التقرير الرسمية */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .header-col-right {
            width: 32%;
            text-align: right;
            line-height: 1.5;
        }

        .header-col-center {
            width: 36%;
            text-align: center;
        }

        .header-col-left {
            width: 32%;
            text-align: left;
            line-height: 1.5;
        }

        .header-title {
            font-size: 15px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .header-period {
            font-size: 11px;
            font-weight: bold;
            color: #2563eb;
            background: #eff6ff;
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            border: 1px solid #bfdbfe;
        }

        /* كروت ملخص المجاميع السريعة قبل الجدول */
        .summary-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            gap: 10px;
        }

        .summary-item {
            flex: 1;
            padding: 6px 10px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            text-align: center;
        }

        .summary-label {
            font-size: 10px;
            color: #64748b;
            display: block;
            margin-bottom: 2px;
        }

        .summary-val {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
        }

        /* جدول السجلات المعتمد */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 15px;
        }

        th, td {
            border: 1px solid #475569;
            padding: 4px 5px;
            text-align: center;
            vertical-align: middle;
        }

        thead th {
            background-color: #e2e8f0;
            color: #0f172a;
            font-weight: bold;
            font-size: 10px;
        }

        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        tfoot tr {
            background-color: #f1f5f9;
            font-weight: bold;
        }

        tfoot td {
            border-top: 2px solid #0f172a;
            font-size: 11px;
        }

        .text-right { text-align: right !important; }
        .text-left { text-align: left !important; }
        .badge {
            display: inline-block;
            padding: 1px 5px;
            font-size: 9px;
            border-radius: 3px;
            font-weight: normal;
        }
        .badge-cash { background: #dcfce7; color: #166534; }
        .badge-pos { background: #e0f2fe; color: #075985; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }

        /* خانات التوقيع والمصادقة المعتمدة */
        .signatures-section {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
            border-top: 1px dashed #94a3b8;
            padding-top: 15px;
        }

        .sig-box {
            width: 23%;
            text-align: center;
            line-height: 1.6;
        }

        .sig-title {
            font-weight: bold;
            font-size: 11px;
            color: #0f172a;
            margin-bottom: 25px;
        }

        .sig-line {
            font-size: 10px;
            color: #64748b;
        }

        /* قواعد الطباعة الخاصة بالطابعة ومتصفح الويب */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                font-size: 9.5px !important;
            }
            .report-wrapper {
                box-shadow: none !important;
                padding: 0 !important;
                border-radius: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            table {
                font-size: 9px !important;
            }
            th, td {
                padding: 3px 4px !important;
                border-color: #333333 !important;
            }
            thead th {
                background-color: #e5e7eb !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .header-period {
                background-color: #f3f4f6 !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .summary-item {
                background: #f9fafb !important;
                border-color: #666 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="report-wrapper">

    <!-- شريط التحكم للطباعة السريعة (يختفي بالطباعة) -->
    <div class="control-bar no-print">
        <div>
            <strong>معاينة الكشف الطباعي المعتمد للزيارات الخارجية</strong>
            <span style="font-size: 11px; opacity: 0.8; margin-right: 10px;">(إجمالي السجلات في هذا الكشف: {{ number_format($totalCount) }})</span>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-print">
                <svg style="width:14px;height:14px;vertical-align:middle;margin-left:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                طباعة الكشف الآن (أو حفظ PDF)
            </button>
            <button onclick="window.close()" class="btn btn-close">إغلاق</button>
        </div>
    </div>

    <!-- ترويسة الكشف الرسمية -->
    <div class="report-header">
        <div class="header-col-right">
            <div><strong>جمهورية العراق</strong></div>
            <div><strong>وزارة الصحة</strong></div>
            <div>{{ $mohName ?: 'دائرة الصحة' }}</div>
            <div>{{ $hospitalName ?: 'المستشفى / المؤسسة' }}</div>
        </div>

        <div class="header-col-center">
            <div class="header-title">كشف حسابات ووصولات الزيارات والخدمات الخارجية</div>
            <div class="header-period">
                @if($dateFrom || $dateTo)
                    للفترة من: {{ $dateFrom ?: 'بداية السجلات' }} إلى: {{ $dateTo ?: date('Y-m-d') }}
                @else
                    كشف شامل لكافة السجلات
                @endif
            </div>
        </div>

        <div class="header-col-left">
            <div><strong>تاريخ التنظيم:</strong> {{ date('Y-m-d') }}</div>
            <div><strong>وقت الطباعة:</strong> {{ date('H:i') }}</div>
            <div><strong>المستخدم المنظم:</strong> {{ auth()->user()->name }}</div>
            <div><strong>إجمالي السجلات:</strong> {{ number_format($totalCount) }} مريض/زائر</div>
        </div>
    </div>

    <!-- شريط الإجماليات والمجاميع المالية السريعة -->
    <div class="summary-bar">
        <div class="summary-item">
            <span class="summary-label">عدد المرضى والزيارات</span>
            <span class="summary-val">{{ number_format($totalCount) }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">مقبوضات النقد (كاش)</span>
            <span class="summary-val" style="color: #166534;">{{ number_format($totalCash) }} د.ع</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">مقبوضات الدفع الإلكتروني (POS)</span>
            <span class="summary-val" style="color: #0369a1;">{{ number_format($totalElectronic) }} د.ع</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">مجموع أجور الخدمات</span>
            <span class="summary-val">{{ number_format($totalFees) }} د.ع</span>
        </div>
        <div class="summary-item" style="background:#fef3c7;border-color:#fde047;">
            <span class="summary-label" style="color:#854d0e;">الإيراد الكلي الشامل</span>
            <span class="summary-val" style="color:#b45309;">{{ number_format($grandTotal) }} د.ع</span>
        </div>
    </div>

    <!-- جدول السجلات: صف معتمد كامل لكل مريض -->
    <table>
        <thead>
            <tr>
                <th style="width: 3%;">ت</th>
                <th style="width: 5%;">الترقيم السنوي</th>
                <th style="width: 15%;">اسم المريض / الزائر</th>
                <th style="width: 6%;">الجنسية</th>
                <th style="width: 8%;">رقم الهاتف</th>
                <th style="width: 14%;">الخدمة الطبية / الفحص</th>
                <th style="width: 12%;">الطبيب المعالج</th>
                <th style="width: 7%;">تاريخ الزيارة</th>
                <th style="width: 5%;">الوقت</th>
                <th style="width: 6%;">رقم الوصل</th>
                <th style="width: 7%;">تاريخ الوصل</th>
                <th style="width: 5%;">الدفع</th>
                <th style="width: 7%;">أجور الخدمة</th>
                <th style="width: 7%;">المبلغ المقبوض</th>
            </tr>
        </thead>
        <tbody>
            @forelse($visits as $visit)
            <tr style="{{ $visit->cancelled_at ? 'background-color: #fee2e2;' : '' }}">
                <td>{{ $loop->iteration }}</td>
                <td><strong>{{ $visit->annual_number ?: '-' }}</strong></td>
                <td class="text-right">
                    <strong>{{ $visit->visitor_name ?? $visit->patient_name ?? '-' }}</strong>
                    @if($visit->cancelled_at)
                        <span class="badge badge-cancelled">ملغي</span>
                    @endif
                </td>
                <td>{{ $visit->visitor_nationality ?? $visit->patient_nationality ?? 'عراقي' }}</td>
                <td dir="ltr" style="font-size: 9px;">{{ $visit->visitor_phone ?? $visit->patient_phone ?? '-' }}</td>
                <td class="text-right"><strong>{{ $visit->serviceSpecialization ? $visit->serviceSpecialization->namesv : '-' }}</strong></td>
                <td>{{ $visit->doctor ? $visit->doctor->name : '-' }}</td>
                <td>{{ $visit->visit_date ? $visit->visit_date->format('Y-m-d') : '-' }}</td>
                <td>{{ $visit->visit_time ? (is_string($visit->visit_time) ? substr($visit->visit_time, 0, 5) : $visit->visit_time->format('H:i')) : '-' }}</td>
                <td>{{ $visit->receipt_number ?: '-' }}</td>
                <td>{{ $visit->receipt_date ? $visit->receipt_date->format('Y-m-d') : '-' }}</td>
                <td>
                    <span class="badge {{ $visit->payment_type == 'كاش' ? 'badge-cash' : 'badge-pos' }}">
                        {{ $visit->payment_type ?: 'كاش' }}
                    </span>
                </td>
                <td>{{ number_format($visit->service_fee ?? 0, 0) }}</td>
                <td><strong>{{ number_format($visit->total_amount ?? $visit->service_fee ?? 0, 0) }}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="14" style="padding: 25px; color: #64748b;">
                    لا توجد سجلات زيارات مطابقة للفترة المحددة
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($visits->count() > 0)
        <tfoot>
            <tr>
                <td colspan="12" class="text-left" style="padding: 7px 10px;">
                    المجموع الكلي المعتمد للفترة المحددة ({{ number_format($totalCount) }} مريض):
                </td>
                <td>{{ number_format($totalFees, 0) }}</td>
                <td style="color: #b45309; font-size: 11px;">{{ number_format($grandTotal, 0) }} د.ع</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- خانة التوقيع المعتمدة: منظم الكشف فقط -->
    <div class="signatures-section" style="display: flex; justify-content: flex-end; padding-left: 50px;">
        <div class="sig-box" style="width: 260px; text-align: center;">
            <div class="sig-title" style="font-size: 13px; font-weight: bold; margin-bottom: 35px; color: #0f172a;">منظم الكشف</div>
            <div class="sig-line" style="font-size: 11px; margin-bottom: 5px;"><strong>الاسم:</strong> {{ auth()->user()->name }}</div>
            <div class="sig-line" style="font-size: 11px; margin-bottom: 5px;"><strong>التوقيع:</strong> ...................................</div>
            <div class="sig-line" style="font-size: 10px; color: #64748b;"><strong>التاريخ:</strong> {{ date('Y-m-d') }}</div>
        </div>
    </div>

</div>

</body>
</html>

