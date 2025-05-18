@extends('layouts.master')
@section('title', 'نسيت كلمة المرور')
@section('content')
<div class="container">
  <div class="row justify-content-center mt-5">
    <div class="col-md-6">
      @if(session('status'))
      <div class="alert alert-success">
          {{ session('status') }}
      </div>
      @endif
      <div class="card">
        <div class="card-header">نسيت كلمة المرور</div>
        <div class="card-body">
          <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
              <label for="email">أدخل بريدك الإلكتروني</label>
              <input id="email" type="email" class="form-control" name="email" placeholder="example@example.com" required autofocus>
            </div>
            <div class="form-group mt-3">
              <button type="submit" class="btn btn-primary">أرسل رابط إعادة تعيين كلمة المرور</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection