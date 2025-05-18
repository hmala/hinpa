@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="height: 100vh; direction: rtl;">
    <div class="col-md-6">
        <div class="card border-0 shadow-lg">
            <div class="card-header text-center text-white fw-bold" style="background-color: #4CAF50;">
                تسجيل الدخول
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger text-center">
                        <strong>{{$errors->first()}}</strong>
                    </div>
                @endif

                <form method="POST" action="{{ route('2fa') }}">
                    {{ csrf_field() }}

                    <div class="mb-4 text-center">
                        <p>يرجى إدخال الكود <strong>OTP</strong> الموجود في تطبيق المصادقة.
                            <br>الكود يتغير كل 30 ثانية.</p>
                    </div>

                    <div class="form-group mb-3">
                        <label for="one_time_password" class="form-label fw-bold">الكود يستخدم لمرة واحدة:</label>
                        <input id="one_time_password" type="number" class="form-control form-control-lg" name="one_time_password" placeholder="أدخل الكود" required autofocus>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-lg text-white" style="background-color: #4CAF50;">
                            تسجيل الدخول
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection