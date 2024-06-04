@extends('layouts.app')

@section('content')
<div class="main active">
    <h1>Admin Dashboard</h1>

    <!-- Statistics Section -->
    <div class="statistics">
        <div class="stat-card">
            <h3>Total Buku</h3>
            <p>{{ $books->count() }}</p>
        </div>
        <div class="stat-card">
            <h3>Total Anggota</h3>
            <p>{{ $userCount }}</p>
        </div>
    </div>

    <!-- Loan Counter Chart Section -->
    <div class="chart-container">
        <canvas id="loanChart"></canvas>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('loanChart').getContext('2d');
        const loanChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($loanMonths) !!},
                datasets: [{
                    label: 'Number of Loans',
                    data: {!! json_encode($loanCounts) !!},
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return Number.isInteger(value) ? value : null;
                            },
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });
    });
</script>
@endsection


<style>
    .statistics {
        display: flex;
        justify-content: space-around;
        margin-bottom: 20px;
    }
    .stat-card {
        background-color: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 30px;
        text-align: center;
        width: 200px;
        transition: transform 0.3s ease;
    }
    .stat-card:hover {
        transform: scale(1.05);
    }
    .stat-card h3 {
        margin: 0 0 10px;
        font-size: 18px;
        color: #333;
    }
    .stat-card p {
        margin: 0;
        font-size: 24px;
        font-weight: bold;
        color: #007bff;
    }
    .chart-container {
        padding: 20px;
        background-color: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>
