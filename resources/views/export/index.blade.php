@extends('layouts.master')

@section('css')
<style>
.export-page {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 20px 0;
}

.export-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 30px;
}

.export-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 20px;
    border-bottom: none;
}

.export-header h4 {
    margin: 0;
    font-weight: 600;
}

.form-group label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.form-control, .form-select {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 10px 15px;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.btn-export {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    border-radius: 8px;
    padding: 12px 25px;
    color: white;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-export:hover {
    background: linear-gradient(135deg, #0056b3 0%, #007bff 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
}

.btn-preview {
    background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
    border: none;
    border-radius: 8px;
    padding: 12px 25px;
    color: white;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-preview:hover {
    background: linear-gradient(135deg, #ff8c00 0%, #ffc107 100%);
    transform: translateY(-2px);
}

.columns-container {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    background: #f8f9fa;
}

.form-check {
    margin-bottom: 10px;
}

.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.preview-container {
    margin-top: 20px;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.table-preview {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

.table-preview th {
    background: #007bff;
    color: white;
    border: none;
    padding: 12px;
}

.table-preview td {
    border: 1px solid #dee2e6;
    padding: 10px;
}

.alert-info {
    border: none;
    border-radius: 8px;
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border-left: 4px solid #17a2b8;
}

.loading-spinner {
    display: none;
    text-align: center;
    padding: 20px;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

.stats-card {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: white;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
}

.stats-number {
    font-size: 2rem;
    font-weight: bold;
}

.stats-label {
    font-size: 0.9rem;
    opacity: 0.9;
}
</style>
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">تصدير البيانات</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تصدير Excel</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="export-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card export-card">
                    <div class="card-header export-header">
                        <h4 class="mb-0">
                            <i class="fas fa-file-excel export-icon"></i>
                            تصدير البيانات إلى Excel
                        </h4>
                    </div>
                    <div class="card-body">
                        <form id="exportForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="table">اختر الجدول:</label>
                                        <select id="table" name="table" class="form-select" required>
                                            <option value="">-- اختر الجدول --</option>
                                            @foreach($tables as $table)
                                                <option value="{{ $table['name'] }}">{{ $table['display_name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="circle">اختر الدائرة:</label>
                                        <select id="circle" name="circle" class="form-select">
                                            <option value="all">جميع الدوائر</option>
                                            @foreach($circles as $circle)
                                                <option value="{{ $circle->id }}">{{ $circle->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>اختر الأعمدة المطلوبة:</label>
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllColumns">تحديد الكل</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllColumns">إلغاء تحديد الكل</button>
                                </div>
                                <div id="columnsContainer" class="columns-container">
                                    <p class="text-muted">يرجى اختيار جدول أولاً لعرض الأعمدة المتاحة</p>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="button" id="previewBtn" class="btn btn-preview">
                                    <i class="fas fa-eye"></i>
                                    معاينة البيانات
                                </button>
                                <button type="submit" id="exportBtn" class="btn btn-export">
                                    <i class="fas fa-download"></i>
                                    تصدير إلى Excel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- منطقة المعاينة -->
                <div id="previewContainer" class="preview-container" style="display: none;">
                    <h5><i class="fas fa-eye"></i> معاينة البيانات</h5>
                    <div id="previewContent"></div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- إحصائيات -->
                <div class="stats-card">
                    <div class="stats-number" id="totalRecords">0</div>
                    <div class="stats-label">إجمالي السجلات</div>
                </div>

                <!-- تعليمات -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> تعليمات الاستخدام</h6>
                    </div>
                    <div class="card-body">
                        <ol class="mb-0">
                            <li>اختر الجدول المطلوب تصديره</li>
                            <li>اختر الدائرة (أو اتركها "جميع الدوائر")</li>
                            <li>حدد الأعمدة المطلوبة</li>
                            <li>اضغط "معاينة البيانات" للتأكد</li>
                            <li>اضغط "تصدير إلى Excel" لتحميل الملف</li>
                        </ol>
                    </div>
                </div>

                <!-- معلومات إضافية -->
                <div class="card mt-3">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-file-excel"></i> معلومات الملف</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>التنسيق:</strong> Excel (.xlsx)</p>
                        <p><strong>الترميز:</strong> UTF-8</p>
                        <p><strong>التنسيق:</strong> جدول منسق مع عناوين</p>
                        <p class="mb-0"><strong>اللغة:</strong> العربية</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading -->
        <div class="loading-spinner" id="loadingSpinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">جاري التحميل...</span>
            </div>
            <p class="mt-2">جاري معالجة البيانات...</p>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // عند تغيير الجدول
    $('#table').change(function() {
        const tableName = $(this).val();
        if (tableName) {
            loadTableColumns(tableName);
        } else {
            $('#columnsContainer').html('<p class="text-muted">يرجى اختيار جدول أولاً لعرض الأعمدة المتاحة</p>');
        }
        $('#previewContainer').hide();
        $('#totalRecords').text('0');
    });

    // تحديد جميع الأعمدة
    $('#selectAllColumns').click(function() {
        $('#columnsContainer input[type="checkbox"]').prop('checked', true);
    });

    // إلغاء تحديد جميع الأعمدة
    $('#deselectAllColumns').click(function() {
        $('#columnsContainer input[type="checkbox"]').prop('checked', false);
    });

    // معاينة البيانات
    $('#previewBtn').click(function() {
        previewData();
    });

    // تصدير البيانات
    $('#exportForm').submit(function(e) {
        e.preventDefault();
        exportData();
    });

    // جلب أعمدة الجدول
    function loadTableColumns(tableName) {
        $.ajax({
            url: '{{ route("export.columns") }}',
            method: 'POST',
            data: {
                table: tableName,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    let html = '';
                    response.columns.forEach(function(column) {
                        html += `
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="columns[]" value="${column}" id="col_${column}" checked>
                                <label class="form-check-label" for="col_${column}">
                                    ${column}
                                </label>
                            </div>
                        `;
                    });
                    $('#columnsContainer').html(html);
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                showAlert('danger', 'حدث خطأ في جلب أعمدة الجدول');
            }
        });
    }

    // معاينة البيانات
    function previewData() {
        const formData = getFormData();
        if (!validateForm(formData)) return;

        $('#loadingSpinner').show();

        $.ajax({
            url: '{{ route("export.preview") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#loadingSpinner').hide();
                if (response.success) {
                    displayPreview(response.data, response.total_count, response.preview_count);
                    $('#totalRecords').text(response.total_count.toLocaleString());
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                $('#loadingSpinner').hide();
                showAlert('danger', 'حدث خطأ في معاينة البيانات');
            }
        });
    }

    // تصدير البيانات
    function exportData() {
        const formData = getFormData();
        if (!validateForm(formData)) return;

        $('#loadingSpinner').show();
        $('#exportBtn').prop('disabled', true);

        // إنشاء form مخفي للتحميل
        const form = $('<form>', {
            'method': 'POST',
            'action': '{{ route("export.download") }}'
        });

        // إضافة CSRF token
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': $('meta[name="csrf-token"]').attr('content')
        }));

        // إضافة البيانات
        Object.keys(formData).forEach(function(key) {
            if (Array.isArray(formData[key])) {
                formData[key].forEach(function(value) {
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': key + '[]',
                        'value': value
                    }));
                });
            } else {
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': key,
                    'value': formData[key]
                }));
            }
        });

        // إضافة النموذج وإرساله
        form.appendTo('body').submit().remove();

        setTimeout(function() {
            $('#loadingSpinner').hide();
            $('#exportBtn').prop('disabled', false);
            showAlert('success', 'تم تصدير البيانات بنجاح!');
        }, 2000);
    }

    // الحصول على بيانات النموذج
    function getFormData() {
        const formData = {
            table: $('#table').val(),
            circle: $('#circle').val(),
            columns: [],
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $('#columnsContainer input[type="checkbox"]:checked').each(function() {
            formData.columns.push($(this).val());
        });

        return formData;
    }

    // التحقق من صحة النموذج
    function validateForm(formData) {
        if (!formData.table) {
            showAlert('warning', 'يرجى اختيار جدول');
            return false;
        }

        if (formData.columns.length === 0) {
            showAlert('warning', 'يرجى اختيار عمود واحد على الأقل');
            return false;
        }

        return true;
    }

    // عرض المعاينة
    function displayPreview(data, totalCount, previewCount) {
        if (data.length === 0) {
            $('#previewContent').html('<div class="alert alert-info">لا توجد بيانات لعرضها</div>');
            $('#previewContainer').show();
            return;
        }

        let html = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                عرض ${previewCount} من أصل ${totalCount.toLocaleString()} سجل
            </div>
            <div class="table-responsive">
                <table class="table table-preview">
                    <thead>
                        <tr>
        `;

        // عناوين الأعمدة
        Object.keys(data[0]).forEach(function(key) {
            html += `<th>${key}</th>`;
        });
        html += '</tr></thead><tbody>';

        // البيانات
        data.forEach(function(row) {
            html += '<tr>';
            Object.values(row).forEach(function(value) {
                html += `<td>${value || '-'}</td>`;
            });
            html += '</tr>';
        });

        html += '</tbody></table></div>';
        $('#previewContent').html(html);
        $('#previewContainer').show();
    }

    // عرض الرسائل
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // إزالة الرسائل السابقة وإضافة الجديدة
        $('.alert').remove();
        $('.export-card .card-body').prepend(alertHtml);
        
        // إخفاء الرسالة بعد 5 ثواني
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>
@endsection
