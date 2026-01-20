@extends('layouts.master')
@section('content')

<div class="row">
    <div class="col-lg-6 col-md-8 offset-lg-3 offset-md-2">
        <!-- نجاح العملية -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- رسائل الخطأ -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- بطاقة تغيير كلمة المرور -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title text-center mb-4">تغيير كلمة المرور</h5>

                <!-- نموذج تغيير كلمة المرور -->
                {!! Form::open(['method' => 'PATCH', 'route' => ['users.change-password', $user->id]]) !!}
                
                <div class="form-group">
                    <label for="password">كلمة المرور الجديدة:</label>
                    {!! Form::password('password', ['class' => 'form-control', 'id' => 'password', 'required']) !!}
                </div>
                
                <div class="form-group">
                    <label for="confirm-password">تأكيد كلمة المرور:</label>
                    {!! Form::password('confirm-password', ['class' => 'form-control', 'id' => 'confirm-password', 'required']) !!}
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">تحديث</button>
                </div>
                
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection