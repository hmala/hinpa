@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">إدارة ربط الخدمات بالتخصصات</h5>
                    <a href="{{ route('service-specializations.create') }}" class="btn btn-primary">ربط خدمة جديدة</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>رمز التخصص</th>
                                    <th>رمز الخدمة</th>
                                    <th>رمز الخدمة في التخصص</th>
                                    <th>اسم الخدمة في التخصص</th>
                                    <th>السعر</th>
                                    <th>ملاحظات</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($services as $service)
                                    @foreach ($service->typeSpecializations as $specialization)
                                        <tr>
                                            <td>{{ $specialization->pivot->tscode }}</td>
                                            <td>{{ $specialization->pivot->sercode }}</td>
                                            <td>{{ $specialization->pivot->codesv }}</td>
                                            <td>{{ $specialization->pivot->namesv }}</td>
                                            <td>{{ $specialization->pivot->price }}</td>
                                            <td>{{ $specialization->pivot->notes }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('service-specializations.edit', [$service->id, $specialization->id]) }}" 
                                                       class="btn btn-sm btn-primary">تعديل</a>
                                                    <form action="{{ route('service-specializations.destroy', [$service->id, $specialization->id]) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الربط؟');"
                                                          style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
