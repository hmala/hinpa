@extends('layouts.master2')

@section('title')
تسجيل الدخول
@stop

@section('css')
<!-- Sidemenu-respoansive-tabs css -->
<link href="{{ URL::asset('assets/plugins/sidemenu-responsive-tabs/css/sidemenu-responsive-tabs.css') }}" rel="stylesheet">
<!-- Font Awesome للرموز -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<style>
    /* تعديل الألوان */
    .bg-primary-transparent {
        background-color: #f5f5f5 !important; /* خلفية رمادية فاتحة */
    }

    .btn-main-primary {
        background-color: #4CAF50; /* لون زر أخضر فاتح */
        color: #fff; /* نص أبيض */
        border-radius: 5px; /* زوايا مستديرة */
        padding: 10px 20px; /* حجم الأزرار */
        flex-shrink: 0; /* منع تقليص الأزرار */
    }

    .btn-main-primary:hover {
        background-color: #45a049; /* لون أغمق عند التحليق */
    }

    /* تصميم الأيقونات */
    i {
        margin-right: 8px; /* مسافة بين الأيقونة والنص */
        color: #fff; /* لون الأيقونة */
        font-size: 1.2em; /* حجم الأيقونة */
    }

    btn-main-primary:hover i {
        color: #333; /* تغيير اللون عند التحليق */
        transform: scale(1.2); /* تكبير الأيقونة قليلاً */
        transition: transform 0.3s ease, color 0.3s ease;
    }

    /* تصميم الحقول */
    .form-control {
        border: 2px solid #4CAF50; /* حدود خضراء */
        border-radius: 10px; /* زوايا دائرية */
        padding: 10px; /* مسافة داخلية */
        font-size: 1.1em; /* حجم النص */
        transition: box-shadow 0.3s ease;
    }

    .form-control:focus {
        box-shadow: 0px 0px 10px rgba(76, 175, 80, 0.5); /* تأثير التركيز */
        outline: none; /* إزالة الإطار الافتراضي */
    }

    .form-group label {
        font-size: 1em; /* حجم النص */
        color: #333; /* لون النص */
        font-weight: bold; /* نص عريض */
    }

    /* تأثيرات على الصورة */
    img {
        box-shadow: 10px 10px 20px rgba(0, 0, 0, 0.2);
        border-radius: 15px; /* زوايا أكثر انحناء */
        transition: transform 0.4s ease-in-out;
    }

    img:hover {
        transform: rotate(5deg); /* دوران بسيط عند التحليق بالفأرة */
    }

    /* لجعل الأزرار في صف واحد */
    .buttons-container {
        display: flex;
        justify-content: center; /* لمحاذاة الأزرار في المنتصف */
        gap: 10px; /* إضافة مسافة بين الأزرار */
        margin-top: 20px; /* مسافة أعلى */
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row no-gutter">
        <!-- القسم الأول -->
        <div class="col-md-6 col-lg-6 col-xl-5 bg-white">
            <div class="login d-flex align-items-center py-2">
                <div class="container p-0">
                    <div class="row">
                        <div class="col-md-10 col-lg-10 col-xl-9 mx-auto">
                            <div class="card-sigin">
                                <div class="mb-5 d-flex">
                                    <h1 class="main-logo1 ml-1 mr-0 my-auto tx-28">ألاسرة المهيئة والكلية</h1>
                                </div>
                                <div class="card-sigin">
                                    <div class="main-signup-header">
                                        <form method="POST" action="{{ route('login') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label>البريد الإلكتروني</label>
                                                <input id="email" type="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    name="email" value="{{ old('email') }}" required
                                                    autocomplete="email" autofocus>
                                                @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label>كلمة المرور</label>
                                                <input id="password" type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    name="password" required autocomplete="current-password">
                                                @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>

                                            <div class="buttons-container">
                                                <button type="submit" class="btn btn-main-primary">
                                                    <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                                                </button>
                                                <a href="{{ route('register') }}" class="btn btn-main-primary">
                                                    <i class="fas fa-user-plus"></i> مستخدم جديد
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- القسم الثاني -->
        <div class="col-md-6 col-lg-6 col-xl-7 d-none d-md-flex bg-primary-transparent">
            <div class="row wd-100p mx-auto text-center">
                <div class="col-md-12 col-lg-12 col-xl-12 my-auto mx-auto wd-100p">
                    <img src="{{ URL::asset('assets/img/media/pngtree-hospital-medical-billing-service-with-health-insurance-form-for-hospitalization-or-png-image_6254623.png') }}"
                        class="my-auto ht-xl-80p wd-md-100p wd-xl-80p mx-auto" alt="logo">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
@endsection