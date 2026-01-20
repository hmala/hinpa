<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>اختبار النسخ الاحتياطية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-database"></i> اختبار النسخ الاحتياطية</h4>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-primary" id="testBackupBtn">
                            <i class="fas fa-plus"></i>
                            اختبار إنشاء نسخة احتياطية
                        </button>
                        
                        <div id="results" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // إعداد CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $('#testBackupBtn').click(function() {
            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري الاختبار...');
            
            $('#results').html('<div class="alert alert-info">جاري إنشاء النسخة الاحتياطية...</div>');
            
            $.ajax({
                url: '{{ route("backup.create") }}',
                method: 'POST',
                timeout: 120000, // 2 دقيقة
                success: function(response) {
                    console.log('استجابة الخادم:', response);
                    btn.prop('disabled', false).html('<i class="fas fa-plus"></i> اختبار إنشاء نسخة احتياطية');
                    
                    if (response.success) {
                        $('#results').html(`
                            <div class="alert alert-success">
                                <h5>تم بنجاح!</h5>
                                <p><strong>اسم الملف:</strong> ${response.filename}</p>
                                <p><strong>حجم الملف:</strong> ${response.size}</p>
                            </div>
                        `);
                    } else {
                        $('#results').html(`
                            <div class="alert alert-danger">
                                <h5>فشل!</h5>
                                <p>${response.message}</p>
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('خطأ:', xhr.responseText);
                    btn.prop('disabled', false).html('<i class="fas fa-plus"></i> اختبار إنشاء نسخة احتياطية');
                    
                    let errorMsg = 'حدث خطأ غير معروف';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        errorMsg = xhr.responseText;
                    }
                    
                    $('#results').html(`
                        <div class="alert alert-danger">
                            <h5>خطأ في الطلب!</h5>
                            <p><strong>الحالة:</strong> ${status}</p>
                            <p><strong>الخطأ:</strong> ${error}</p>
                            <p><strong>التفاصيل:</strong> ${errorMsg}</p>
                        </div>
                    `);
                }
            });
        });
    });
    </script>
</body>
</html>
