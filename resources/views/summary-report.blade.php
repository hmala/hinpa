@extends('layouts.master')
@section('css')
<!--  Owl-carousel css-->
<link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
<!-- Maps css -->
<link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
<style>
    .card-counter {
        box-shadow: 2px 2px 10px #DADADA;
        padding: 20px 10px;
        background-color: #fff;
        height: 100px;
        border-radius: 5px;
        transition: .3s linear all;
    }

    .card-counter:hover {
        box-shadow: 4px 4px 20px #DADADA;
        transition: .3s linear all;
    }

    .card-counter i {
        font-size: 5em;
        opacity: 0.2;
    }

    .card-counter .count-numbers {
        position: absolute;
        right: 35px;
        top: 20px;
        font-size: 32px;
        display: block;
    }

    .card-counter .count-name {
        position: absolute;
        right: 35px;
        top: 65px;
        font-style: italic;
        text-transform: capitalize;
        opacity: 0.5;
        display: block;
        font-size: 18px;
    }
</style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">التقارير</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ Summary Report</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card-counter bg-info text-white">
                <i class="fa fa-users"></i>
                <span class="count-numbers">{{ $totalPatients }}</span>
                <span class="count-name">إجمالي المرضى</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-counter bg-success text-white">
                <i class="fa fa-hospital"></i>
                <span class="count-numbers">{{ $totalSurgeries }}</span>
                <span class="count-name">العمليات الجراحية</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-counter bg-warning text-white">
                <i class="fa fa-exclamation-triangle"></i>
                <span class="count-numbers">{{ $totalAccidents }}</span>
                <span class="count-name">الحوادث</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-counter bg-danger text-white">
                <i class="fa fa-file-medical"></i>
                <span class="count-numbers">{{ $totalCases }}</span>
                <span class="count-name">الحالات</span>
            </div>
        </div>
    </div>    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h3 class="card-title mb-0">تحليل المرضى حسب الردهات</h3>
                    <select id="monthSelector" class="form-control float-left" style="width: 200px;">
                        <option value="all">كل الأشهر</option>
                        <option value="1">يناير</option>
                        <option value="2">فبراير</option>
                        <option value="3">مارس</option>
                        <option value="4">أبريل</option>
                        <option value="5">مايو</option>
                        <option value="6">يونيو</option>
                        <option value="7">يوليو</option>
                        <option value="8">أغسطس</option>
                        <option value="9">سبتمبر</option>
                        <option value="10">أكتوبر</option>
                        <option value="11">نوفمبر</option>
                        <option value="12">ديسمبر</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:400px;">
                        <canvas id="departmentsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">التوزيع الشهري للمرضى</h3>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="monthlyDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var monthlyData = @json($monthlyStatsByDept);
    var departments = @json($departments);
    
    const monthNames = ["يناير", "فبراير", "مارس", "إبريل", "مايو", "يونيو", 
                      "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر"];
    
    const colors = [
        'rgba(255, 99, 132, 0.7)',
        'rgba(54, 162, 235, 0.7)',
        'rgba(255, 206, 86, 0.7)',
        'rgba(75, 192, 192, 0.7)',
        'rgba(153, 102, 255, 0.7)',
        'rgba(255, 159, 64, 0.7)',
        'rgba(255, 99, 132, 0.7)',
        'rgba(54, 162, 235, 0.7)'
    ];

    // Initialize both charts
    let departmentsChart;
    let monthlyDistributionChart;

    function updateCharts(selectedMonth = 'all') {
        // Process data for departments chart
        let chartData;
        if (selectedMonth === 'all') {
            // Sum all months for each department
            chartData = departments.map((dept, index) => {
                const deptData = monthlyData.filter(item => item.spebed === dept.id);
                const total = deptData.reduce((sum, item) => sum + item.count, 0);
                return {
                    label: dept.Spcuname,
                    data: [total],
                    backgroundColor: colors[index % colors.length],
                }
            });
        } else {
            // Show data for selected month only
            chartData = departments.map((dept, index) => {
                const deptData = monthlyData.find(item => 
                    item.spebed === dept.id && item.month === parseInt(selectedMonth)
                );
                return {
                    label: dept.Spcuname,
                    data: [deptData ? deptData.count : 0],
                    backgroundColor: colors[index % colors.length],
                }
            });
        }

        // Update departments chart
        if (departmentsChart) {
            departmentsChart.destroy();
        }
        const ctx1 = document.getElementById('departmentsChart').getContext('2d');
        departmentsChart = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: ['عدد المرضى'],
                datasets: chartData
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Process data for monthly distribution
        if (monthlyDistributionChart) {
            monthlyDistributionChart.destroy();
        }
        const ctx2 = document.getElementById('monthlyDistributionChart').getContext('2d');
        const monthlyTotals = Array(12).fill(0);
        monthlyData.forEach(item => {
            monthlyTotals[item.month - 1] += item.count;
        });

        monthlyDistributionChart = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: monthNames,
                datasets: [{
                    label: 'إجمالي المرضى',
                    data: monthlyTotals,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Initialize charts
    updateCharts();

    // Add event listener for month selection
    document.getElementById('monthSelector').addEventListener('change', function(e) {
        updateCharts(e.target.value);
    });
</script>
@endsection
