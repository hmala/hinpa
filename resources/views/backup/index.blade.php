
@extends('layouts.master')

@section('css')
<link href="{{ asset('css/backup.css') }}" rel="stylesheet">
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">النسخ الاحتياطية</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ إدارة النسخ الاحتياطية</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="backup-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card backup-card">
                    <div class="card-header backup-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-database backup-icon"></i>
                            النسخ الاحتياطية لقاعدة البيانات
                        </h4>
                        <button type="button" class="btn backup-btn" id="createBackupBtn">
                            <i class="fas fa-plus"></i>
                            إنشاء نسخة احتياطية جديدة
                        </button>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert backup-alert backup-alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert backup-alert backup-alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div id="alertContainer"></div>

                        <div class="table-responsive">
                            <table class="table backup-table">
                                <thead>
                                    <tr>
                                        <th>اسم الملف</th>
                                        <th>حجم الملف</th>
                                        <th>تاريخ الإنشاء</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody id="backupTableBody">
                                    @if(count($backups) > 0)
                                        @foreach($backups as $backup)
                                            <tr>
                                                <td>
                                                    <i class="fas fa-file-archive backup-file-icon text-primary"></i>
                                                    {{ $backup['filename'] }}
                                                </td>
                                                <td>
                                                    <span class="badge backup-size-badge">{{ $backup['size'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="backup-date">{{ $backup['date'] }}</span>
                                                </td>
                                                <td>
                                                    <div class="backup-actions">
                                                        <a href="{{ route('backup.download', $backup['filename']) }}" 
                                                           class="btn btn-sm backup-download-btn" 
                                                           title="تحميل">
                                                            <i class="fas fa-download"></i>
                                                            تحميل
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-sm backup-delete-btn delete-backup-btn" 
                                                                data-filename="{{ $backup['filename'] }}"
                                                                title="حذف">
                                                            <i class="fas fa-trash"></i>
                                                            حذف
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="backup-empty-state">
                                                <i class="fas fa-info-circle"></i>
                                                <h5>لا توجد نسخ احتياطية حالياً</h5>
                                                <p>قم بإنشاء نسخة احتياطية جديدة للبدء</p>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade backup-loading-modal" id="loadingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <h5>جاري إنشاء النسخة الاحتياطية...</h5>
                <p class="text-muted">يرجى الانتظار، قد تستغرق هذه العملية بضع دقائق.</p>
                <div class="progress mt-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
// في backup/index.blade.php - استبدل كامل section js:
@section('js')
<script>
$(document).ready(function() {
    // CSRF token
    const token = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': token
        }
    });
    
    let processing = false;
    
    // إنشاء نسخة احتياطية
    $('#createBackupBtn').click(function() {
        if (processing) {
            alert('العملية قيد التنفيذ...');
            return;
        }
        
        processing = true;
        const btn = $(this);
        const originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> جاري العمل...');
        $('#loadingModal').modal('show');
        
        console.log('بدء طلب النسخ الاحتياطي');
        
        $.ajax({
            url: '{{ route("backup.create") }}',
            type: 'POST',
            dataType: 'json',
            timeout: 120000, // دقيقتان فقط
            success: function(data) {
                console.log('نجح:', data);
                
                if (data.success) {
                    showMessage('success', data.message + (data.size ? ' (' + data.size + ')' : ''));
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showMessage('error', data.message || 'فشل غير محدد');
                }
            },
            error: function(xhr, status, error) {
                console.log('خطأ:', status, error);
                console.log('Response:', xhr.responseText);
                
                let msg = 'حدث خطأ';
                
                if (status === 'timeout') {
                    msg = 'انتهت المهلة. تحقق من النتيجة...';
                    setTimeout(() => location.reload(), 3000);
                } else if (xhr.status === 419) {
                    msg = 'انتهت الجلسة. إعادة تحميل...';
                    setTimeout(() => location.reload(), 1000);
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    msg = 'خطأ الخادم: ' + xhr.status;
                }
                
                showMessage('error', msg);
            },
            complete: function() {
                processing = false;
                btn.prop('disabled', false).html(originalText);
                $('#loadingModal').modal('hide');
            }
        });
    });

    // حذف نسخة احتياطية
    $(document).on('click', '.delete-backup-btn', function() {
        const filename = $(this).data('filename');
        const row = $(this).closest('tr');
        
        if (!confirm('حذف ' + filename + '?')) return;
        
        const btn = $(this);
        btn.prop('disabled', true);
        
        $.ajax({
            url: '{{ url("/backup/delete") }}/' + filename,
            type: 'DELETE',
            success: function(data) {
                if (data.success) {
                    row.remove();
                    showMessage('success', 'تم الحذف');
                    
                    if ($('#backupTableBody tr').length === 0) {
                        $('#backupTableBody').html('<tr><td colspan="4" style="text-align:center;">لا توجد نسخ احتياطية</td></tr>');
                    }
                } else {
                    showMessage('error', data.message || 'فشل الحذف');
                    btn.prop('disabled', false);
                }
            },
            error: function() {
                showMessage('error', 'خطأ في الحذف');
                btn.prop('disabled', false);
            }
        });
    });

    function showMessage(type, text) {
        const className = type === 'success' ? 'alert-success' : 'alert-danger';
        const html = `<div class="alert ${className} alert-dismissible">
            ${text}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
        
        $('#alertContainer').html(html);
        
        setTimeout(() => $('.alert').fadeOut(), 5000);
    }
});
</script>
@endsection