/**
 * حل مشاكل ضعف الإنترنت والحفاظ على البيانات المدخلة
 * Auto-save form data and handle weak internet connection
 */

(function($) {
    'use strict';

    // ====== 1. حل مشكلة القوائم المنسدلة عند ضعف الإنترنت ======
    
    /**
     * إضافة مؤشر تحميل للقوائم المنسدلة عند التحميل عبر AJAX
     */
    window.showSelectLoading = function(selectElement) {
        var $select = $(selectElement);
        $select.prop('disabled', true);
        $select.empty();
        $select.append('<option value="">جاري التحميل...</option>');
    };

    /**
     * إخفاء مؤشر التحميل بعد اكتمال التحميل
     */
    window.hideSelectLoading = function(selectElement, defaultText) {
        var $select = $(selectElement);
        $select.prop('disabled', false);
        $select.empty();
        if (defaultText) {
            $select.append('<option value="">' + defaultText + '</option>');
        }
    };

    /**
     * تحسين AJAX للقوائم المنسدلة مع معالجة الأخطاء وإعادة المحاولة
     */
    window.loadSelectDataWithRetry = function(url, targetSelect, options) {
        var settings = $.extend({
            method: 'GET',
            data: {},
            loadingText: 'جاري التحميل...',
            defaultText: 'اختر...',
            valueField: 'id',
            textField: 'name',
            retries: 3,
            timeout: 10000,
            onSuccess: null,
            onError: null
        }, options);

        var attempt = 0;

        function tryLoad() {
            attempt++;
            showSelectLoading(targetSelect);

            $.ajax({
                url: url,
                type: settings.method,
                data: settings.data,
                dataType: 'json',
                timeout: settings.timeout,
                success: function(data) {
                    hideSelectLoading(targetSelect, settings.defaultText);
                    
                    if (Array.isArray(data)) {
                        $.each(data, function(key, item) {
                            var value = typeof item === 'object' ? item[settings.valueField] : item;
                            var text = typeof item === 'object' ? item[settings.textField] : item;
                            $(targetSelect).append('<option value="' + value + '">' + text + '</option>');
                        });
                    } else if (typeof data === 'object') {
                        $.each(data, function(key, value) {
                            $(targetSelect).append('<option value="' + key + '">' + value + '</option>');
                        });
                    }

                    if (settings.onSuccess) {
                        settings.onSuccess(data);
                    }

                    // حفظ البيانات في localStorage للاستخدام المستقبلي
                    try {
                        var cacheKey = 'select_cache_' + url.replace(/[^a-zA-Z0-9]/g, '_');
                        localStorage.setItem(cacheKey, JSON.stringify(data));
                    } catch(e) {
                        console.warn('Failed to cache select data:', e);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    
                    // محاولة تحميل من الكاش
                    var cacheKey = 'select_cache_' + url.replace(/[^a-zA-Z0-9]/g, '_');
                    var cachedData = localStorage.getItem(cacheKey);
                    
                    if (cachedData) {
                        try {
                            var data = JSON.parse(cachedData);
                            hideSelectLoading(targetSelect, settings.defaultText);
                            
                            if (Array.isArray(data)) {
                                $.each(data, function(key, item) {
                                    var value = typeof item === 'object' ? item[settings.valueField] : item;
                                    var text = typeof item === 'object' ? item[settings.textField] : item;
                                    $(targetSelect).append('<option value="' + value + '">' + text + '</option>');
                                });
                            }
                            
                            // إظهار رسالة للمستخدم أن البيانات من الكاش
                            showNotification('تم تحميل البيانات من الذاكرة المؤقتة', 'warning');
                            return;
                        } catch(e) {
                            console.error('Failed to load from cache:', e);
                        }
                    }

                    // إعادة المحاولة إذا لم نصل للحد الأقصى
                    if (attempt < settings.retries) {
                        hideSelectLoading(targetSelect, 'جاري إعادة المحاولة (' + attempt + '/' + settings.retries + ')...');
                        setTimeout(tryLoad, 2000 * attempt); // زيادة وقت الانتظار مع كل محاولة
                    } else {
                        hideSelectLoading(targetSelect, settings.defaultText);
                        showNotification('فشل تحميل البيانات. يرجى التحقق من الاتصال بالإنترنت', 'error');
                        
                        if (settings.onError) {
                            settings.onError(xhr, status, error);
                        }
                    }
                }
            });
        }

        tryLoad();
    };

    /**
     * إظهار إشعار للمستخدم
     */
    window.showNotification = function(message, type) {
        type = type || 'info';
        var alertClass = 'alert-info';
        
        if (type === 'error') alertClass = 'alert-danger';
        else if (type === 'success') alertClass = 'alert-success';
        else if (type === 'warning') alertClass = 'alert-warning';

        var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
            message +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</div>');

        $('body').append(notification);
        
        setTimeout(function() {
            notification.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    };

    // ====== 2. حل مشكلة فقدان البيانات عند Refresh ======

    /**
     * حفظ بيانات النموذج تلقائياً في localStorage
     */
    window.FormAutoSave = {
        storagePrefix: 'form_autosave_',
        saveInterval: 2000, // حفظ كل ثانيتين
        
        /**
         * تفعيل الحفظ التلقائي لنموذج معين
         */
        init: function(formSelector, options) {
            var self = this;
            var $form = $(formSelector);
            
            if ($form.length === 0) {
                console.warn('Form not found:', formSelector);
                return;
            }

            var settings = $.extend({
                excludeFields: [], // حقول لا نريد حفظها (مثل كلمات المرور)
                storageKey: null,  // مفتاح مخصص للتخزين
                showRestoreMessage: true,
                onRestore: null,
                onSave: null
            }, options);

            // إنشاء مفتاح تخزين فريد للنموذج
            var storageKey = settings.storageKey || 
                            self.storagePrefix + (formSelector.replace(/[^a-zA-Z0-9]/g, '_')) + '_' + window.location.pathname.replace(/[^a-zA-Z0-9]/g, '_');

            // استعادة البيانات المحفوظة عند تحميل الصفحة
            self.restoreFormData($form, storageKey, settings);

            // حفظ البيانات عند كل تغيير
            var saveTimeout;
            $form.on('change keyup', 'input, select, textarea', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(function() {
                    self.saveFormData($form, storageKey, settings);
                }, self.saveInterval);
            });

            // حذف البيانات المحفوظة عند إرسال النموذج بنجاح
            $form.on('submit', function() {
                // تأخير بسيط للسماح بإرسال النموذج
                setTimeout(function() {
                    if (!$form.find('.is-invalid, .has-error').length) {
                        self.clearFormData(storageKey);
                    }
                }, 500);
            });

            // إضافة زر لمسح البيانات المحفوظة يدوياً
            self.addClearButton($form, storageKey);
        },

        /**
         * حفظ بيانات النموذج
         */
        saveFormData: function($form, storageKey, settings) {
            var formData = {};
            var self = this;

            $form.find('input, select, textarea').each(function() {
                var $field = $(this);
                var name = $field.attr('name');
                var type = $field.attr('type');

                // تخطي الحقول المستثناة
                if (!name || settings.excludeFields.indexOf(name) !== -1) {
                    return;
                }

                // تخطي كلمات المرور والـ CSRF token
                if (type === 'password' || name === '_token') {
                    return;
                }

                if (type === 'checkbox') {
                    formData[name] = $field.is(':checked');
                } else if (type === 'radio') {
                    if ($field.is(':checked')) {
                        formData[name] = $field.val();
                    }
                } else if ($field.is('select[multiple]')) {
                    formData[name] = $field.val() || [];
                } else {
                    formData[name] = $field.val();
                }
            });

            try {
                localStorage.setItem(storageKey, JSON.stringify({
                    data: formData,
                    timestamp: new Date().getTime()
                }));

                if (settings.onSave) {
                    settings.onSave(formData);
                }
            } catch (e) {
                console.error('Failed to save form data:', e);
            }
        },

        /**
         * استعادة بيانات النموذج
         */
        restoreFormData: function($form, storageKey, settings) {
            try {
                var stored = localStorage.getItem(storageKey);
                
                if (!stored) {
                    return;
                }

                var savedData = JSON.parse(stored);
                var formData = savedData.data;
                var timestamp = savedData.timestamp;

                // التحقق من أن البيانات ليست قديمة جداً (24 ساعة)
                var maxAge = 24 * 60 * 60 * 1000; // 24 ساعة بالميلي ثانية
                if (new Date().getTime() - timestamp > maxAge) {
                    this.clearFormData(storageKey);
                    return;
                }

                // استعادة البيانات
                $.each(formData, function(name, value) {
                    var $field = $form.find('[name="' + name + '"]');
                    
                    if ($field.length === 0) {
                        return;
                    }

                    var type = $field.attr('type');

                    if (type === 'checkbox') {
                        $field.prop('checked', value);
                    } else if (type === 'radio') {
                        $field.filter('[value="' + value + '"]').prop('checked', true);
                    } else if ($field.is('select')) {
                        $field.val(value);
                        // تحديث Select2 إذا كان مفعلاً
                        if ($field.hasClass('select2-hidden-accessible')) {
                            $field.trigger('change.select2');
                        }
                    } else {
                        $field.val(value);
                    }
                });

                // إظهار رسالة للمستخدم
                if (settings.showRestoreMessage) {
                    showNotification('تم استعادة البيانات المحفوظة مسبقاً', 'info');
                }

                if (settings.onRestore) {
                    settings.onRestore(formData);
                }

            } catch (e) {
                console.error('Failed to restore form data:', e);
            }
        },

        /**
         * حذف البيانات المحفوظة
         */
        clearFormData: function(storageKey) {
            try {
                localStorage.removeItem(storageKey);
            } catch (e) {
                console.error('Failed to clear form data:', e);
            }
        },

        /**
         * إضافة زر لمسح البيانات المحفوظة
         */
        addClearButton: function($form, storageKey) {
            var self = this;
            
            // التحقق من وجود بيانات محفوظة
            var stored = localStorage.getItem(storageKey);
            if (!stored) {
                return;
            }

            var $clearBtn = $('<button type="button" class="btn btn-sm btn-warning" style="margin: 10px 0;">' +
                '<i class="fa fa-trash"></i> مسح البيانات المحفوظة' +
                '</button>');

            $clearBtn.on('click', function() {
                if (confirm('هل أنت متأكد من حذف البيانات المحفوظة؟')) {
                    self.clearFormData(storageKey);
                    $form[0].reset();
                    $(this).remove();
                    showNotification('تم حذف البيانات المحفوظة', 'success');
                }
            });

            $form.prepend($clearBtn);
        }
    };

    // ====== 3. تحسين معالجة الأخطاء العامة لـ AJAX ======

    /**
     * معالج عام لأخطاء AJAX
     */
    $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
        if (jqxhr.status === 0) {
            showNotification('لا يوجد اتصال بالإنترنت. يرجى التحقق من الاتصال', 'error');
        } else if (jqxhr.status == 404) {
            showNotification('الصفحة المطلوبة غير موجودة', 'error');
        } else if (jqxhr.status == 500) {
            showNotification('خطأ في الخادم. يرجى المحاولة لاحقاً', 'error');
        } else if (thrownError === 'timeout') {
            showNotification('انتهت مهلة الطلب. يرجى التحقق من الاتصال', 'error');
        }
    });

    // ====== 4. مراقبة حالة الاتصال بالإنترنت ======

    /**
     * إظهار تحذير عند فقدان الاتصال بالإنترنت
     */
    var connectionWarning = null;
    
    window.addEventListener('offline', function() {
        connectionWarning = $('<div class="alert alert-danger" style="position: fixed; top: 0; left: 0; right: 0; z-index: 99999; margin: 0; border-radius: 0; text-align: center;">' +
            '<strong>تحذير:</strong> لا يوجد اتصال بالإنترنت. قد لا تعمل بعض الميزات بشكل صحيح.' +
            '</div>');
        $('body').prepend(connectionWarning);
    });

    window.addEventListener('online', function() {
        if (connectionWarning) {
            connectionWarning.removeClass('alert-danger').addClass('alert-success')
                .html('<strong>رائع!</strong> تم استعادة الاتصال بالإنترنت.');
            
            setTimeout(function() {
                connectionWarning.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        }
    });

    // ====== 5. تحسين Select2 للعمل مع الحفظ التلقائي ======

    /**
     * حفظ وإستعادة قيم Select2
     */
    if (typeof $.fn.select2 !== 'undefined') {
        $(document).on('select2:select select2:unselect', '.select2', function() {
            $(this).trigger('change');
        });
    }

})(jQuery);

// ====== مثال على الاستخدام ======
/*
// في صفحة النموذج، أضف هذا الكود:

$(document).ready(function() {
    // تفعيل الحفظ التلقائي للنموذج
    FormAutoSave.init('form', {
        excludeFields: ['password', 'password_confirmation'],
        showRestoreMessage: true
    });

    // مثال على استخدام تحميل القوائم المنسدلة المحسّن
    $('#section_id').change(function() {
        var sectionId = $(this).val();
        
        if (sectionId) {
            loadSelectDataWithRetry(
                '/api/sections/' + sectionId + '/specializations',
                '#specialization_id',
                {
                    method: 'GET',
                    defaultText: 'اختر التخصص',
                    valueField: 'id',
                    textField: 'name',
                    retries: 3
                }
            );
        }
    });
});
*/
