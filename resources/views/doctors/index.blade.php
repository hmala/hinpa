@extends('layouts.master')
@section('title')
الأطباء
@stop
@section('css')
<!-- Internal Data table css -->
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
<!--Internal   Notify -->
<link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
@endsection
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto"></h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">قائمة الأطباء
            </span>
        </div>
    </div>

</div>
<!-- breadcrumb -->
@endsection
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">قائمة الأطباء</h2>
                    <div>
                        <a href="{{ route('doctors.create') }}" class="btn btn-primary">إضافة جديد</a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- فلتر وبحث الأطباء الشامل -->
                    <form method="GET" action="{{ route('doctors.index') }}" class="mb-4">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="ابحث باسم الطبيب، المسمى الوظيفي، أو التخصص..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i> بحث
                                        </button>
                                        @if(request()->filled('search') || request()->filled('moh_id') || request()->filled('fck_id'))
                                            <a href="{{ route('doctors.index') }}" class="btn btn-secondary" title="إلغاء البحث">
                                                <i class="fas fa-times"></i> مسح
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if(isset($availableMohs) && $availableMohs->isNotEmpty())
                            <div class="col-md-3">
                                <select name="moh_id" id="moh_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">جميع الدوائر</option>
                                    @foreach($availableMohs as $moh)
                                        <option value="{{ $moh->id }}" {{ request('moh_id') == $moh->id ? 'selected' : '' }}>{{ $moh->mohname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            @if(isset($availableFcks) && $availableFcks->isNotEmpty())
                            <div class="col-md-3">
                                <select name="fck_id" id="fck_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">جميع المستشفيات</option>
                                    @foreach($availableFcks as $fck)
                                        <option value="{{ $fck->id }}" data-moh-id="{{ $fck->moh_id }}" {{ request('fck_id') == $fck->id ? 'selected' : '' }}>{{ $fck->Fckname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>
                    </form>

                    <div class="table-responsive">
                       <table id="example1" class="table key-buttons text-md-nowrap" style="text-align: center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>المسمى الوظيفي</th>
                                    <th>دائرة الصحة</th>
                                    <th>اسم المؤسسة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($doctors as $doctor)
                                <tr>
                                    <td>{{ ($doctors->currentPage() - 1) * $doctors->perPage() + $loop->iteration }}</td>
                                    <td>{{ $doctor->name }}</td>
                                    <td>{{ $doctor->job_title }}</td>
                                    <td>{{ $doctor->moh ? $doctor->moh->mohname : 'N/A' }}</td>
                                    <td>{{ $doctor->fck ? $doctor->fck->Fckname : 'غير محدد' }}</td>
                                    <td>
                                        <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-info btn-sm">عرض</a>
                                        <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-primary btn-sm">تعديل</a>
                                        <form action="{{ route('doctors.destroy', $doctor) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-user-md fa-2x mb-2 d-block"></i>
                                        لا توجد نتائج مطابقة للبحث
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $doctors->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/pdfmake.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/vfs_fonts.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
<script>
    $(function() {
        $('#example1').DataTable({
            responsive: true,
            scrollX: true,
            autoWidth: false,
            searching: false,
            paging: false,
            info: false,
            ordering: false
        });

        var $mohSelect = $('#moh_id');
        var $fckSelect = $('#fck_id');
        if ($mohSelect.length && $fckSelect.length) {
            var allFckOptions = $fckSelect.find('option').clone();
            $mohSelect.on('change', function() {
                var selectedMoh = $(this).val();
                $fckSelect.empty();
                $fckSelect.append('<option value="">جميع المستشفيات</option>');
                allFckOptions.each(function() {
                    var mohId = $(this).data('moh-id');
                    if (!selectedMoh || mohId == selectedMoh) {
                        if ($(this).val() !== '') {
                            $fckSelect.append($(this).clone());
                        }
                    }
                });
            });
        }
    });
</script>
<!--Internal  Notify js -->
<script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
@endsection