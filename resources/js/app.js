import { Chart, BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend } from 'chart.js';
import annotationPlugin from 'chartjs-plugin-annotation';

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend, annotationPlugin);

function jalankanJam() {
    const waktuEl = document.getElementById('jam-waktu');
    const tanggalEl = document.getElementById('jam-tanggal');

    if (!waktuEl || !tanggalEl) return;

    const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const bulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    function perbarui() {
        const sekarang = new Date();
        const pad = (n) => String(n).padStart(2, '0');

        waktuEl.textContent = `${pad(sekarang.getHours())}:${pad(sekarang.getMinutes())}:${pad(sekarang.getSeconds())}`;
        tanggalEl.textContent =
            `${hari[sekarang.getDay()]}, ${pad(sekarang.getDate())} ${bulan[sekarang.getMonth()]} ${sekarang.getFullYear()}`;
    }

    perbarui();
    setInterval(perbarui, 1000);
}

function jalankanGrafikDashboard() {
    const kanvas = document.getElementById('grafik-jenis');
    if (!kanvas) return;

    let data;
    try {
        data = JSON.parse(document.getElementById('grafik-jenis-data').textContent);
    } catch (e) {
        return;
    }

    const overload = data.totalSisa > data.kapasitas;

    new Chart(kanvas, {
        type: 'bar',
        data: {
            labels: data.stokPerJenis.map((d) => d.jenis),
            datasets: [
                {
                    label: 'Sisa Stok',
                    data: data.stokPerJenis.map((d) => d.sisa),
                    backgroundColor: overload ? 'rgba(225, 29, 72, 0.85)' : 'rgba(28, 49, 73, 0.85)',
                    hoverBackgroundColor: overload ? 'rgba(190, 18, 60, 1)' : 'rgba(44, 82, 122, 1)',
                    borderRadius: 5,
                    maxBarThickness: 34,
                },
                {
                    label: 'Terjual',
                    data: data.stokPerJenis.map((d) => d.terjual),
                    backgroundColor: 'rgba(148, 163, 184, 0.5)',
                    hoverBackgroundColor: 'rgba(100, 116, 139, 0.8)',
                    borderRadius: 5,
                    maxBarThickness: 34,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: '#334155', font: { family: 'Instrument Sans', weight: 600 } } },
                tooltip: {
                    backgroundColor: '#101f30',
                    padding: 10,
                    titleFont: { family: 'Instrument Sans', weight: 600 },
                    bodyFont: { family: 'Instrument Sans' },
                    callbacks: {
                        label: (ctx) => ` ${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString('id-ID')} unit`,
                    },
                },
                annotation: {
                    annotations: {
                        batasKapasitas: {
                            type: 'line',
                            yMin: data.kapasitas,
                            yMax: data.kapasitas,
                            borderColor: '#f59e0b',
                            borderWidth: 2,
                            borderDash: [6, 4],
                            label: {
                                display: true,
                                content: `Kapasitas Maks: ${data.kapasitas.toLocaleString('id-ID')} unit`,
                                position: 'end',
                                backgroundColor: '#f59e0b',
                                color: '#1c3149',
                                font: { family: 'Instrument Sans', weight: 700, size: 11 },
                            },
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#475569', font: { family: 'Instrument Sans', weight: 600 } },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(148, 163, 184, 0.2)' },
                    border: { display: false },
                    ticks: {
                        color: '#64748b',
                        font: { family: 'Instrument Sans' },
                        callback: (v) => v.toLocaleString('id-ID'),
                    },
                },
            },
        },
    });

    const kanvasMerk = document.getElementById('grafik-merk');
    if (!kanvasMerk) return;

    let dataMerk;
    try {
        dataMerk = JSON.parse(document.getElementById('grafik-merk-data').textContent);
    } catch (e) {
        return;
    }

    new Chart(kanvasMerk, {
        type: 'bar',
        data: {
            labels: dataMerk.map((d) => d.merk),
            datasets: [
                {
                    label: 'Sisa Stok',
                    data: dataMerk.map((d) => d.total),
                    backgroundColor: 'rgba(28, 49, 73, 0.75)',
                    hoverBackgroundColor: 'rgba(44, 82, 122, 1)',
                    borderRadius: 5,
                    maxBarThickness: 40,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#101f30',
                    padding: 10,
                    titleFont: { family: 'Instrument Sans', weight: 600 },
                    bodyFont: { family: 'Instrument Sans' },
                    callbacks: {
                        label: (ctx) => ` Sisa stok: ${ctx.parsed.y.toLocaleString('id-ID')} unit`,
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#475569', font: { family: 'Instrument Sans', weight: 600 } },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(148, 163, 184, 0.2)' },
                    border: { display: false },
                    ticks: {
                        color: '#64748b',
                        font: { family: 'Instrument Sans' },
                        callback: (v) => v.toLocaleString('id-ID'),
                    },
                },
            },
        },
    });
}

function jalankanAnimasiAngka() {
    const kurangiGerak = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('[data-hitung]').forEach((el) => {
        const teksAkhir = (el.textContent || '').trim();
        const cocok = teksAkhir.match(/-?\d+(?:\.\d+)*/);
        if (!cocok) return;

        const target = parseInt(cocok[0].replace(/\./g, ''), 10);
        if (Number.isNaN(target) || target <= 0) return;

        const durasi = 900;
        const mulai = performance.now();
        const format = (nilai) => nilai.toLocaleString('id-ID');

        function isiUlang(angka) {
            el.textContent = teksAkhir.replace(cocok[0], format(angka));
        }

        function langkah(sekarang) {
            const progres = Math.min((sekarang - mulai) / durasi, 1);
            const eased = 1 - Math.pow(1 - progres, 3);
            isiUlang(Math.round(target * eased));
            if (progres < 1) {
                requestAnimationFrame(langkah);
            }
        }

        if (kurangiGerak) {
            isiUlang(target);
        } else {
            requestAnimationFrame(langkah);
        }
    });
}

function jalankanKonfirmasiHapus() {
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            if (!window.confirm(form.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    jalankanJam();
    jalankanGrafikDashboard();
    jalankanAnimasiAngka();
    jalankanKonfirmasiHapus();
});