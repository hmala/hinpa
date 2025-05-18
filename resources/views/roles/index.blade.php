@extends('layouts.master')
@section('css')
    <!-- Font Awesome للأيقونات -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
    <!-- CSS إضافي للتنسيق -->
    <style>
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .icon-container i {
            font-size: 50px;
            color: #ffffff;
            background-color: #007bff;
            border-radius: 50%;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .icon-container i:hover {
            background-color: #0056b3;
            color: #ffffff;
            transform: scale(1.1);
        }

        .btn-group .btn {
            margin: 0 5px;
        }
    </style>
@stop

@section('title')
    صلاحيات المستخدمين - مورا سوفت للادارة القانونية
@stop

@section('content')
<div class="container mt-5">
    <div class="row">
        @foreach($roles as $key => $role)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm text-center">
                <div class="card-body d-flex flex-column align-items-center">
                    <!-- أيقونة مميزة -->
                    <div class="icon-container mb-3">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <!-- اسم الصلاحية -->
                    <h5 class="card-title mb-3">{{ $role->name }}</h5>
                    <!-- أزرار العمليات -->
                    <div class="btn-group">
                        <a href="{{ route('roles.show', $role->id) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-eye"></i> عرض
                        </a>
                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        @if ($role->name !== 'Admin')
                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id], 'style' => 'display:inline']) !!}
                        {!! Form::submit('حذف', ['class' => 'btn btn-danger btn-sm']) !!}
                        {!! Form::close() !!}
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection


@section('js')
<!--Internal Notify js -->
<script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
@endsection