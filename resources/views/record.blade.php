@extends('layouts.master')
@section('title', 'نموذج تسجيل')

@section('css')
<style>
    body {
        font-family: Arial, sans-serif;
        direction: rtl;
        text-align: center;
        background-color: #f4f4f9;
        padding: 20px;
    }
    form {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 30px;
        border: 1px solid #ccc;
        border-radius: 10px;
        background-color: #ffffff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 80%;
        max-width: 500px;
        margin: 0 auto;
    }
    form > div {
        margin-bottom: 20px;
        width: 100%;
    }
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #333;
    }
    select, input, button {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f9f9f9;
    }
    button {
        background-color: #4CAF50;
        color: white;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    button:hover {
        background-color: #45a049;
    }
    .message {
        margin-bottom: 20px;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
    }
    .message-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .message-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>
@endsection

@section('content')
<h1 style="margin-bottom: 20px;">نموذج تسجيل المستخدم</h1>

@if (session('success'))
    <div class="message message-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="message message-error">
        {{ session('error') }}
    </div>
@endif

<form action="{{ route('record.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div>
        <label for="institution_type">نوع المؤسسة:</label>
        <select name="institution_type" id="institution_type" onchange="filterInstitutions()">
            <option value="">اختر نوع المؤسسة...</option>
            @foreach ($fctypes as $fctype)
                <option value="{{ $fctype->id }}">{{ $fctype->Fname }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="fck">اختر المؤسسة:</label>
        <select name="fck" id="fck">
            <option value="" disabled selected>اختر المؤسسة...</option>
        </select>
    </div>

    <div>
    <label style="font-weight: bold;">الشهر</label>
            <select class="form-control" id="month" name="month" required>
                <option value="">اختر الشهر</option>
                <option value="1">كانون الثاني</option>
                <option value="2">شباط</option>
                <option value="3">آذار</option>
                <option value="4">نيسان</option>
                <option value="5">أيار</option>
                <option value="6">حزيران</option>
                <option value="7">تموز</option>
                <option value="8">آب</option>
                <option value="9">أيلول</option>
                <option value="10">تشرين الأول</option>
                <option value="11">تشرين الثاني</option>
                <option value="12">كانون الأول</option>
            </select>
    </div>

    <div>
        <label for="year">السنة:</label>
        <select name="year" id="year" required>
            <option value="" disabled selected>اختر السنة...</option>
            @for ($i = now()->year - 1; $i <= now()->year + 1; $i++)
                <option value="{{ $i }}">{{ $i }}</option>
            @endfor
        </select>
    </div>

    <div style="text-align: center; margin: 20px;">
    <label for="attachment" style="font-size: 18px; cursor: pointer;">
        <i class="fa fa-upload" style="font-size: 40px; color: #007bff;"></i>
    </label>
    <input type="file" name="attachment" id="attachment" accept="application/pdf" style="display: none;">
</div>

    <button type="submit" >إرسال</button>
</form>
@endsection

@section('js')
<script>
    function filterInstitutions() {
        const institutionType = document.getElementById('institution_type').value;

        if (!institutionType) {
            document.getElementById('fck').innerHTML = '<option value="" disabled selected>اختر المؤسسة...</option>';
            return;
        }

        fetch(`/filter-institutions?type=${institutionType}`)
            .then(response => response.json())
            .then(data => {
                const fckSelect = document.getElementById('fck');
                fckSelect.innerHTML = '<option value="" disabled selected>اختر المؤسسة...</option>';
                data.forEach(institution => {
                    const option = document.createElement('option');
                    option.value = institution.id;
                    option.textContent = institution.Fckname;
                    fckSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching institutions:', error));
    }
</script>
@endsection