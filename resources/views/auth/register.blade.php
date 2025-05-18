@extends('layouts.app')

@section('content')
<div class="container" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card" style="border-radius: 15px; box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);">
                <div class="card-header" style="background-color:rgb(113, 162, 202); color: white; text-align: center; font-weight: bold; border-radius: 15px 15px 0 0;">
                    {{ __('مستخدم جديد') }}
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- الاسم الثلاثي -->
                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('الاسم الثلاثي') }}</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus style="border-radius: 10px; border: 2px solid #00BCD4;">
                            </div>
                        </div>

                        <!-- البريد الإلكتروني -->
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('الايميل') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required style="border-radius: 10px; border: 2px solid #00BCD4;">
                            </div>
                        </div>

                        <!-- كلمة المرور -->
                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('الباسورد') }}</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required style="border-radius: 10px; border: 2px solid #00BCD4;">
                            </div>
                        </div>

                        <!-- تأكيد كلمة المرور -->
                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('تاكيد الباسورد') }}</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required style="border-radius: 10px; border: 2px solid #00BCD4;">
                            </div>
                        </div>

                        <!-- دائرة الصحة -->
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">دائرة الصحة</label>
                            <div class="col-md-6">
                                <select name="section_id" id="section_id" class="form-control" required style="border-radius: 10px; border: 2px solid #00BCD4;">
                                    <option value="" selected disabled>--حدد القسم--</option>
                                    @foreach ($mohs as $moh)
                                        <option value="{{ $moh->id }}">{{ $moh->mohname }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- نوع المؤسسة -->
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">نوع المؤسسة</label>
                            <div class="col-md-6">
                                <select name="fckt" id="fckt" class="form-control" required style="border-radius: 10px; border: 2px solid #00BCD4;">
                                    <option value="" selected disabled>--حدد النوع--</option>
                                    @foreach ($fctypes as $fctype)
                                        <option value="{{ $fctype->id }}">{{ $fctype->Fname }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- اسم المؤسسة -->
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">اسم المؤسسة</label>
                            <div class="col-md-6">
                                <select name="fckn" id="fckn" class="form-control" required style="border-radius: 10px; border: 2px solid #00BCD4;">
                                    <option value="" selected disabled>--حدد المؤسسة--</option>
                                </select>
                            </div>
                        </div>

                        <!-- الزر -->
                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-success" style="background-color:rgb(123, 173, 219); border-color: #00BCD4; color: white; font-weight: bold; border-radius: 10px; padding: 10px 20px;">
                                    {{ __('تسجيل') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- سكربت -->
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                <script>
                    $(document).ready(function() {
                        $('#section_id, #fckt').change(function() {
                            var section_id = $('#section_id').val();
                            var fckt = $('#fckt').val();

                            if (section_id && fckt) {
                                $.ajax({
                                    url: "{{ route('getInstitutions') }}",
                                    type: "POST",
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        section_id: section_id,
                                        fckt: fckt
                                    },
                                    success: function(data) {
                                        $('#fckn').empty();
                                        $('#fckn').append('<option value="" disabled selected>--حدد المؤسسة--</option>');
                                        $.each(data, function(key, value) {
                                            $('#fckn').append('<option value="' + value.id + '">' + value.Fckname + '</option>');
                                        });
                                    },
                                    error: function() {
                                        alert('حدث خطأ أثناء جلب البيانات.');
                                    }
                                });
                            } else {
                                $('#fckn').empty();
                                $('#fckn').append('<option value="" disabled selected>--حدد المؤسسة--</option>');
                            }
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</div>
@endsection