@extends('layouts.master')
@section('css')

@section('title')
    المستخدمين - مورا سوفت للادارة الفواتير
@stop

<!-- Internal Data table css -->
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
<!--Internal Notify -->
<link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />

@endsection
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">المستخدمين</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة المستخدمين</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto left-content">
        <div class="mb-3 mb-xl-0">
            <a href="{{ route('users.create') }}" class="btn btn-primary mr-2">
                <i class="fas fa-user-plus"></i> إضافة مستخدم جديد
            </a>
            <a href="{{ route('users.export') }}" class="btn btn-success">
                <i class="mdi mdi-file-excel"></i> تصدير Excel
            </a>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection

@section('content')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<!-- row opened -->
<div class="row row-sm">
    <div class="col-xl-12">
        <div class="card">            <div class="card-header pb-0">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title mg-b-0">البحث عن مستخدم</h4>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('users.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>اسم المستخدم</label>
                                <input type="text" class="form-control" name="name" value="{{ request('name') }}" placeholder="ابحث بالاسم">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>البريد الإلكتروني</label>
                                <input type="text" class="form-control" name="email" value="{{ request('email') }}" placeholder="ابحث بالبريد الإلكتروني">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>الحالة</label>
                                <select class="form-control" name="status">
                                    <option value="">الكل</option>
                                    <option value="مفعل" {{ request('status') == 'مفعل' ? 'selected' : '' }}>مفعل</option>
                                    <option value="غير مفعل" {{ request('status') == 'غير مفعل' ? 'selected' : '' }}>غير مفعل</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>المؤسسة</label>
                                <select class="form-control" name="fck_id">
                                    <option value="">الكل</option>
                                    @foreach($fcks as $fck)
                                        <option value="{{ $fck->id }}" {{ request('fck_id') == $fck->id ? 'selected' : '' }}>
                                            {{ $fck->Fckname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>الدائرة</label>
                                <select class="form-control" name="moh_id">
                                    <option value="">الكل</option>
                                    @foreach($mohs_list as $moh)
                                        <option value="{{ $moh->id }}" {{ request('moh_id') == $moh->id ? 'selected' : '' }}>
                                            {{ $moh->mohname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> بحث</button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary"><i class="fas fa-redo"></i> إعادة تعيين</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- row opened -->
<div class="row row-sm">
    <div class="col-xl-12">
        <div class="card">            <div class="card-body">
                <div class="table-responsive hoverable-table">
                    <table class="table table-hover" id="example1" data-page-length='5' style=" text-align: center;">
                        <thead>
                            <tr>
                                <th class="wd-10p border-bottom-0">#</th>
                                <th class="wd-15p border-bottom-0">اسم المستخدم</th>
                                <th class="wd-20p border-bottom-0">البريد الالكتروني</th>
                                <th class="wd-15p border-bottom-0">حالة المستخدم</th>
                                <th class="wd-15p border-bottom-0">المؤسسة التابع لها</th>
                                <th class="wd-15p border-bottom-0">الدائرة التابع لها</th>
                                <th class="wd-15p border-bottom-0">كود المصادقة الثنائية</th>
                                <th class="wd-15p border-bottom-0">نوع المستخدم</th>
                                <th class="wd-10p border-bottom-0">العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $user)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->Status == 'مفعل')
                                            <span class="label text-success d-flex">
                                                <div class="dot-label bg-success ml-1"></div>{{ $user->Status }}
                                            </span>
                                        @else
                                            <span class="label text-danger d-flex">
                                                <div class="dot-label bg-danger ml-1"></div>{{ $user->Status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $user->fck ? $user->fck->Fckname : 'لا يوجد مؤسسة' }}</td>
                                    <td>{{ $user->moh ? $user->moh->mohname : 'لا يوجد دائرة' }}</td>
                                    <td>
                                        @if ($user->google2fa_secret)
                                            <div class="d-flex align-items-center justify-content-center">
                                                <input type="text" class="form-control form-control-sm" 
                                                       id="secret_{{ $user->id }}" 
                                                       value="{{ $user->google2fa_secret }}" 
                                                       readonly 
                                                       style="width: 150px; display: inline-block; margin-left: 5px; font-size: 12px;">
                                                <button class="btn btn-sm btn-primary" 
                                                        onclick="copySecret({{ $user->id }})" 
                                                        title="نسخ">
                                                    <i class="far fa-copy"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted">غير مفعل</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (!empty($user->getRoleNames()))
                                            @foreach ($user->getRoleNames() as $v)
                                                <label class="badge badge-success">{{ $v }}</label>
                                            @endforeach
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-info"
                                            title="تعديل">
                                            <i class="las la-pen"></i>
                                        </a>
                                        <a href="{{ route('users.change-password', $user->id) }}" class="btn btn-sm btn-warning"
                                            title="تغيير كلمة المرور">
                                            <i class="las la-key"></i>
                                        </a>
                                        <a class="modal-effect btn btn-sm btn-danger" data-effect="effect-scale"
                                            data-user_id="{{ $user->id }}" data-username="{{ $user->name }}"
                                            data-toggle="modal" href="#modaldemo8" title="حذف">
                                            <i class="las la-trash"></i>
                                        </a>

                                      
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Pagination links -->
                   
                </div>
            </div>
        </div>
    </div>

    <!-- Modal effects -->
    <div class="modal" id="modaldemo8">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">حذف المستخدم</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('users.destroy', 'test') }}" method="post">
                    {{ method_field('delete') }}
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <p>هل انت متاكد من عملية الحذف ؟</p><br>
                        <input type="hidden" name="user_id" id="user_id" value="">
                        <input class="form-control" name="username" id="username" type="text" readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                        <button type="submit" class="btn btn-danger">تاكيد</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /row -->

@endsection

@section('js')
<!-- Internal Data tables -->
<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
<!--Internal Datatable js -->
<script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
<!--Internal Notify js -->
<script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
<!-- Internal Modal js-->
<script src="{{ URL::asset('assets/js/modal.js') }}"></script>

<script>
    $('#modaldemo8').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var user_id = button.data('user_id')
        var username = button.data('username')
        var modal = $(this)
        modal.find('.modal-body #user_id').val(user_id);
        modal.find('.modal-body #username').val(username);
    })

    function copySecret(userId) {
        var secretInput = document.getElementById('secret_' + userId);
        secretInput.select();
        secretInput.setSelectionRange(0, 99999); // للأجهزة المحمولة
        
        document.execCommand('copy');
        
        // إظهار رسالة نجاح
        notif({
            msg: "تم نسخ كود المصادقة الثنائية بنجاح!",
            type: "success",
            position: "center",
            timeout: 2000
        });
    }
</script>
@endsection