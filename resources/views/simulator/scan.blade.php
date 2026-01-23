@extends('layouts.master')

@push('style')
<style>
.fingerprint.pulse {
    animation: pulse 1.2s infinite;
}
@keyframes pulse {
    0% { transform: scale(1); opacity:.6 }
    50% { transform: scale(1.1); opacity:1 }
    100% { transform: scale(1); opacity:.6 }
}

.log-item {
    font-size: 13px;
    border-bottom: 1px dashed #e5e5e5;
    padding: 6px 0;
}
.log-item:last-child { border-bottom:none }
.log-new { background:#e6fffa }
.log-info { color:#6c757d }
</style>
@endpush

@section('content')
<div class="container-fluid">
<div class="row justify-content-center">

  {{-- ================= SCANNER ================= --}}
  <div class="col-md-5">
    <div class="card shadow-lg" style="border-radius:16px">
      <div class="card-header bg-dark text-white text-center">
        <h4 class="mb-0">
          <i class="fas fa-fingerprint"></i> Fingerprint Scanner
        </h4>
        <small class="text-muted">Attendance Simulator</small>
      </div>

      <div class="card-body text-center">
        <div class="mb-4">
          <i class="fas fa-hand-point-up fa-4x text-secondary"></i>
          <p class="mt-3 mb-1"><strong>Simulasi Absensi</strong></p>
          <small class="text-muted">Pilih kelas → siswa → scan</small>
        </div>

        <form id="scanForm">
          <div class="form-group">
            <select id="classSelect" class="form-control">
              <option value="">— Pilih Kelas —</option>
              @foreach($classes as $class)
                <option value="{{ $class->id }}">{{ $class->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <select id="studentSelect" class="form-control" disabled>
              <option value="">— Pilih Siswa —</option>
            </select>
          </div>

          <button class="btn btn-success btn-block btn-lg" disabled id="scanBtn">
            <i class="fas fa-play"></i> Scan Finger
          </button>
        </form>
      </div>

      <div class="card-footer text-center text-muted small">
        Device Status:
        <span id="deviceStatus">Checking...</span><br>
        <a href="#" data-toggle="modal" data-target="#lastScanModal">
          Lihat Scan Terakhir
        </a>
      </div>
    </div>
  </div>

  {{-- ================= LOG ================= --}}
  <div class="col-md-5">
    <div class="card shadow-sm" style="border-radius:14px">
      <div class="card-header bg-light">
        <strong>Log Scan Realtime</strong>
        <small class="text-muted float-right">Auto refresh</small>
      </div>

      <div class="card-body p-2" style="max-height:420px; overflow:auto" id="logBox">
        <p class="text-muted text-center mb-0">Menunggu scan...</p>
      </div>
    </div>
  </div>

</div>
</div>

{{-- ================= MODAL SCAN ================= --}}
<div class="modal fade" id="scanModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4 border-0 shadow-lg">

      {{-- LOADING --}}
      <div id="scanLoading">
        <div class="fingerprint pulse mb-3">
          <i class="fas fa-fingerprint fa-5x text-secondary"></i>
        </div>
        <h5 class="mb-2">Scanning...</h5>
        <p class="mb-0 text-muted" id="modalStudent">-</p>
        <small class="text-muted" id="modalTime">-</small>
      </div>

      {{-- RESULT --}}
      <div id="scanResult" class="d-none">
        <i id="modalIcon" class="fas fa-check-circle fa-5x mb-3"></i>
        <h4 id="modalTitle"></h4>

        <table class="table table-sm table-borderless mt-3 mb-0">
          <tr>
            <th class="text-right" width="40%">Nama</th>
            <td class="text-left" id="resultName">-</td>
          </tr>
          <tr>
            <th class="text-right">Waktu</th>
            <td class="text-left" id="resultTime">-</td>
          </tr>
          <tr>
            <th class="text-right">Status</th>
            <td class="text-left">
              <span id="resultStatus" class="badge px-3 py-2"></span>
            </td>
          </tr>
        </table>
      </div>

    </div>
  </div>
</div>

{{-- ================= LAST SCAN ================= --}}
<div class="modal fade" id="lastScanModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4">
      <h5 class="mb-3 text-center">📌 Scan Terakhir</h5>
      <table class="table table-sm mb-0">
        <tr><th>Nama</th><td id="lastName">-</td></tr>
        <tr><th>Status</th><td id="lastStatus">-</td></tr>
        <tr><th>Jam</th><td id="lastTime">-</td></tr>
      </table>
      <button class="btn btn-secondary btn-sm mt-3" data-dismiss="modal">Tutup</button>
    </div>
  </div>
</div>
@endsection

@push('script')
<script>
const classes = @json($classes);
const classSelect   = document.getElementById('classSelect');
const studentSelect = document.getElementById('studentSelect');
const scanBtn       = document.getElementById('scanBtn');
const logBox        = document.getElementById('logBox');

let lastFingerprint = null;
let lastScanTime    = null;

/* ================= DEVICE STATUS ================= */
fetch('/api/fingerprint/ping')
  .then(()=>$('#deviceStatus').text('Online').addClass('text-success'))
  .catch(()=>$('#deviceStatus').text('Offline').addClass('text-danger'));

/* ================= CLASS → STUDENT ================= */
classSelect.addEventListener('change', function () {
    studentSelect.innerHTML = `<option value="">— Pilih Siswa —</option>`;
    studentSelect.disabled = true;
    scanBtn.disabled = true;

    const cls = classes.find(c => c.id == this.value);
    if (!cls) return;

    cls.students.forEach(s => {
        if (!s.fingerprint_id) return;
        studentSelect.innerHTML += `
          <option value="${s.fingerprint_id}">
            ${s.name} — ${s.fingerprint_id}
          </option>`;
    });

    studentSelect.disabled = false;
});

studentSelect.addEventListener('change', () => {
    scanBtn.disabled = !studentSelect.value;
});

/* ================= SCAN ================= */
document.getElementById('scanForm').addEventListener('submit', async e => {
    e.preventDefault();
    const now = Date.now();

    const selectedText =
      studentSelect.options[studentSelect.selectedIndex].text;

    // ALWAYS SHOW LOADING FIRST
    $('#modalStudent').text(selectedText);
    $('#modalTime').text('-');
    $('#scanModal').modal({ backdrop:'static', keyboard:false });
    $('#scanLoading').show();
    $('#scanResult').addClass('d-none');

    // anti double scan (still loading first)
    if (lastFingerprint === studentSelect.value && now - lastScanTime < 60000) {
        setTimeout(() => {
            showResultModal({
                name: selectedText,
                time: '-',
                status: 'SUDAH ABSEN'
            });
        }, 400);
        return;
    }

    scanBtn.disabled = true;

    try {
        const res = await fetch('/api/fingerprint/scan', {
            method:'POST',
            headers:{
              'Content-Type':'application/json',
              'Accept':'application/json'
            },
            body: JSON.stringify({
              fingerprint_id: studentSelect.value
            })
        });

        const data = await res.json();

        setTimeout(() => {

            if (res.ok && data.status !== 'already') {
                showResultModal({
                    name: data.student,
                    time: data.time,
                    status: data.is_late ? 'TERLAMBAT' : 'HADIR'
                });
                prependLog(
                    data.student,
                    data.time,
                    data.is_late ? 'TERLAMBAT' : 'HADIR'
                );
            }
            else if (data.status === 'already') {
                showResultModal({
                    name: data.student,
                    time: data.time,
                    status: 'SUDAH ABSEN'
                });
                prependLog(
                    data.student,
                    data.time,
                    'SUDAH ABSEN',
                    true
                );
            }
            else {
                showResultModal({
                    name: '-',
                    time: '-',
                    status: 'GAGAL'
                });
            }

            updateLastScan(data);
            lastFingerprint = studentSelect.value;
            lastScanTime = now;

        }, 400);

    } catch {
        setTimeout(() => {
            showResultModal({
                name: '-',
                time: '-',
                status: 'GAGAL'
            });
        }, 400);
    }
    finally {
        setTimeout(()=>scanBtn.disabled=false,1500);
    }
});

/* ================= UI HELPERS ================= */
function showResultModal({ name, time, status }) {
    $('#scanLoading').hide();
    $('#scanResult').removeClass('d-none');

    $('#resultName').text(name ?? '-');
    $('#resultTime').text(time ?? '-');

    const icon  = $('#modalIcon');
    const title = $('#modalTitle');
    const badge = $('#resultStatus');

    if (status === 'HADIR') {
        icon.attr('class','fas fa-check-circle fa-5x text-success mb-3');
        title.text('Absensi Tercatat');
        badge.attr('class','badge badge-success px-3 py-2').text('HADIR');
    }
    else if (status === 'TERLAMBAT') {
        icon.attr('class','fas fa-exclamation-circle fa-5x text-warning mb-3');
        title.text('Absensi Tercatat');
        badge.attr('class','badge badge-warning px-3 py-2').text('TERLAMBAT');
    }
    else if (status === 'SUDAH ABSEN') {
        icon.attr('class','fas fa-info-circle fa-5x text-secondary mb-3');
        title.text('Informasi');
        badge.attr('class','badge badge-secondary px-3 py-2').text('SUDAH ABSEN');
    }
    else {
        icon.attr('class','fas fa-times-circle fa-5x text-danger mb-3');
        title.text('Scan Gagal');
        badge.attr('class','badge badge-danger px-3 py-2').text('GAGAL');
    }

    setTimeout(()=>$('#scanModal').modal('hide'),1600);
}

function prependLog(name, time, status, info=false) {
    const el = document.createElement('div');
    el.className = 'log-item ' + (info ? 'log-info' : 'log-new');
    el.innerHTML = `
      <strong>${name}</strong><br>
      <small>${time} —
        <span>${status}</span>
      </small>`;
    logBox.prepend(el);
}

function updateLastScan(d) {
    $('#lastName').text(d.student ?? '-');
    $('#lastStatus').text(
      d.status ?? (d.is_late ? 'TERLAMBAT' : 'HADIR')
    );
    $('#lastTime').text(d.time ?? '-');
}
</script>
@endpush
