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
        <button id="prev-year-btn" class="year-btn"><ion-icon name="chevron-back-outline"></ion-icon></button>
        <button id="next-year-btn" class="year-btn"><ion-icon name="chevron-forward-outline"></ion-icon></button>
        <button id="back-btn" class="year-btn" style="display:none;">Kembali ke Tahunan</button>
        <canvas id="loanChart"></canvas>
    </div>

    <!-- Modal for displaying loan details -->
    <div id="loanModal" class="modal">
        <div class="modal-content">
            <h2 id="modalHeader">Rincian Peminjaman</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Buku</th>
                            <th>Tanggal Batas</th>
                            <th>Tanggal Kembali</th>
                        </tr>
                    </thead>
                    <tbody id="loanDetails"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('loanChart').getContext('2d');
    const loanModal = document.getElementById('loanModal');
    const loanDetails = document.getElementById('loanDetails');
    const modalHeader = document.getElementById('modalHeader');
    const backbtn = document.getElementById('back-btn');
    const prevYearBtn = document.getElementById('prev-year-btn');
    const nextYearBtn = document.getElementById('next-year-btn');

    window.onclick = function(event) {
        if (event.target == loanModal) {
            loanModal.classList.remove('show');
            setTimeout(() => {
                loanModal.style.display = "none";
            }, 300);
        }
    };

    const yearlyData = {!! json_encode($yearlyData) !!};
    let currentYearIndex = yearlyData.length - 1;

    function updateChart(yearIndex) {
        const yearData = yearlyData[yearIndex];
        loanChart.data.labels = yearData.months;
        loanChart.data.datasets[0].data = yearData.counts;
        loanChart.update();
        backbtn.style.display = 'none';
        prevYearBtn.style.display = 'inline-block';
        nextYearBtn.style.display = 'inline-block';
    }

    const months = {
        'Januari': 1, 'Februari': 2, 'Maret': 3, 'April': 4, 'Mei': 5, 'Juni': 6,
        'Juli': 7, 'Agustus': 8, 'September': 9, 'Oktober': 10, 'November': 11, 'Desember': 12
    };

    const loanChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: yearlyData[currentYearIndex].months,
            datasets: [{
                label: 'Jumlah Peminjaman',
                data: yearlyData[currentYearIndex].counts,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
                tension: 0
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
            },
            onClick: async (event, elements) => {
                if (elements.length > 0) {
                    const index = elements[0].index;
                    const selectedMonth = yearlyData[currentYearIndex].months[index];
                    const selectedYear = yearlyData[currentYearIndex].year;

                    const month = selectedMonth.split(' ')[0];

                    try {
                        const response = await fetch(`/daily-loans?year=${selectedYear}&month=${month}`);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const dailyData = await response.json();

                        const dailyLabels = dailyData.days.map(day => `${day} ${selectedMonth}`);
                        const dailyCounts = dailyData.data;

                        loanChart.data.labels = dailyLabels;
                        loanChart.data.datasets[0].data = dailyCounts;
                        loanChart.update();

                        backbtn.style.display = 'inline-block';
                        prevYearBtn.style.display = 'none';
                        nextYearBtn.style.display = 'none';

                        loanChart.options.onClick = async (event, elements) => {
                            if (elements.length > 0) {
                                const dayIndex = elements[0].index;
                                const selectedDay = dailyLabels[dayIndex];
                                const selectedDate = `${selectedYear}-${months[month].toString().padStart(2, '0')}-${(dayIndex + 1).toString().padStart(2, '0')}`;

                                modalHeader.textContent = `Rincian Peminjaman ${selectedDay}`;

                                try {
                                    const response = await fetch(`/daily-loan-details?date=${selectedDate}`);
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    const loanDetailsData = await response.json();

                                    function capitalizeWords(str) {
                                        return str.replace(/\b\w/g, char => char.toUpperCase());
                                    }

                                    loanDetails.innerHTML = loanDetailsData.map(loan => `
                                        <tr>
                                            <td>${loan.user && loan.user.first_name && loan.user.last_name ? `${capitalizeWords(loan.user.first_name)} ${capitalizeWords(loan.user.last_name)}` : '<span class="missing-data">Tidak ada data</span>'}</td>
                                            <td>${loan.book && loan.book.title ? capitalizeWords(loan.book.title) : '<span class="missing-data">Tidak ada data</span>'}</td>
                                            <td>${loan.limit_date ? new Date(loan.limit_date).toLocaleDateString('id-ID') : '<span class="missing-data">Tidak ada data</span>'}</td>
                                            <td>${loan.return_date ? new Date(loan.return_date).toLocaleDateString('id-ID') : 'Buku belum dikembalikan'}</td>
                                        </tr>
                                    `).join('');

                                    loanModal.style.display = "block";
                                    setTimeout(() => {
                                        loanModal.classList.add('show');
                                    }, 0);
                                } catch (error) {
                                    console.error('Error fetching loan details:', error);
                                }
                            }
                        };
                    } catch (error) {
                        console.error('Error fetching daily data:', error);
                    }
                }
            }
        }
    });

    document.getElementById('prev-year-btn').addEventListener('click', () => {
        if (currentYearIndex > 0) {
            currentYearIndex--;
            updateChart(currentYearIndex);
        }
    });

    document.getElementById('next-year-btn').addEventListener('click', () => {
        if (currentYearIndex < yearlyData.length - 1) {
            currentYearIndex++;
            updateChart(currentYearIndex);
        }
    });

    backbtn.addEventListener('click', () => {
        updateChart(currentYearIndex);
        backbtn.style.display = 'none';
        loanChart.options.onClick = async (event, elements) => {
            if (elements.length > 0) {
                const index = elements[0].index;
                const selectedMonth = yearlyData[currentYearIndex].months[index];
                const selectedYear = yearlyData[currentYearIndex].year;

                const month = selectedMonth.split(' ')[0];

                try {
                    const response = await fetch(`/daily-loans?year=${selectedYear}&month=${month}`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const dailyData = await response.json();

                    const dailyLabels = dailyData.days.map(day => `${day} ${selectedMonth}`);
                    const dailyCounts = dailyData.data;

                    loanChart.data.labels = dailyLabels;
                    loanChart.data.datasets[0].data = dailyCounts;
                    loanChart.update();

                    backbtn.style.display = 'inline-block';
                    prevYearBtn.style.display = 'none';
                    nextYearBtn.style.display = 'none';

                    loanChart.options.onClick = async (event, elements) => {
                        if (elements.length > 0) {
                            const dayIndex = elements[0].index;
                            const selectedDay = dailyLabels[dayIndex];
                            const selectedDate = `${selectedYear}-${months[month].toString().padStart(2, '0')}-${(dayIndex + 1).toString().padStart(2, '0')}`;

                            modalHeader.textContent = `Rincian Peminjaman ${selectedDay}`;

                            try {
                                const response = await fetch(`/daily-loan-details?date=${selectedDate}`);
                                if (!response.ok) {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                }
                                const loanDetailsData = await response.json();

                                function capitalizeWords(str) {
                                    return str.replace(/\b\w/g, char => char.toUpperCase());
                                }

                                loanDetails.innerHTML = loanDetailsData.map(loan => `
                                    <tr>
                                        <td>${loan.user && loan.user.first_name && loan.user.last_name ? `${capitalizeWords(loan.user.first_name)} ${capitalizeWords(loan.user.last_name)}` : '<span class="missing-data">Tidak ada data</span>'}</td>
                                        <td>${loan.book && loan.book.title ? capitalizeWords(loan.book.title) : '<span class="missing-data">Tidak ada data</span>'}</td>
                                        <td>${loan.limit_date ? new Date(loan.limit_date).toLocaleDateString('id-ID') : '<span class="missing-data">Tidak ada data</span>'}</td>
                                        <td>${loan.return_date ? new Date(loan.return_date).toLocaleDateString('id-ID') : 'Buku belum dikembalikan'}</td>
                                    </tr>
                                `).join('');

                                loanModal.style.display = "block";
                                setTimeout(() => {
                                    loanModal.classList.add('show');
                                }, 0);
                            } catch (error) {
                                console.error('Error fetching loan details:', error);
                            }
                        }
                    };
                } catch (error) {
                    console.error('Error fetching daily data:', error);
                }
            }
        };
    });
});



</script>
@endsection







<style>
/* Add your custom styles here */

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
    text-align: center;
}
.chart-container canvas {
    max-height: 300px;
}
.year-btn {
    padding: 10px 20px;
    background-color: var(--blue);
    color: var(--white);
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin: 5px;
}
.back-btn {
    padding: 10px 20px;
    background-color: var(--blue);
    color: var(--white);
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: block;
    margin-bottom: 20px;
}
/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.8); /* Adjusted for full page darkening */
    padding-top: 60px;
    opacity: 0;
    transition: opacity 0.3s ease, display 0s linear 0.3s;
}

.modal.show {
    display: block;
    opacity: 1;
    transition: opacity 0.3s ease, display 0s linear 0s;
}

.modal-content {
    background-color: #fff;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 10px;
    position: relative;
}
.modal-content h2{
    text-align: center;
}

.table-responsive {
    width: 100%;
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.table th, .table td {
    padding: 8px;
    border: 1px solid #ddd;
    text-align: left;
}

.table th {
    background-color: #f2f2f2;
}

.table tbody tr:hover {
    background-color: #e0e0e0;
}

.loan-entry {
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
}

.loan-entry.show {
    opacity: 1;
}

.missing-data {
    color: red;
}
</style>
