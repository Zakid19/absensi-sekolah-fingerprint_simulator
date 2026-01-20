@extends('layouts.master')

@push('style')
<style>
.fingerprint.pulse {
    animation: pulse 1.2s infinite;
}
@keyframes pulse {
    0%   { transform: scale(1); opacity: .6; }
    50%  { transform: scale(1.1); opacity: 1; }
    100% { transform: scale(1); opacity: .6; }
}

.log-item {
    font-size: 13px;
    border-bottom: 1px dashed #e5e5e5;
    padding: 6px 0;
}
.log-item:last-child { border-bottom: none; }
.log-new { background: #e6fffa; }
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
        <span id="deviceStatus" class="text-muted">Checking...</span><br>
        <a href="#" data-toggle="modal" data-target="#lastScanModal">
          Lihat Scan Terakhir
        </a>
      </div>

    </div>
  </div>

  {{-- ================= REALTIME LOG ================= --}}
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
        <h5>Scanning...</h5>
        <p class="text-muted mb-1" id="scanStudentName"></p>
        <small class="text-muted" id="scanClassName"></small>
      </div>

      {{-- SUCCESS --}}
      <div id="scanSuccess" class="d-none">
        <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
        <h4>Scan Berhasil</h4>
        <p id="successText"></p>
      </div>

      {{-- FAILED --}}
      <div id="scanFailed" class="d-none">
        <i class="fas fa-times-circle fa-5x text-danger mb-3"></i>
        <h4>Scan Gagal</h4>
        <p id="failedText"></p>
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

      <button class="btn btn-secondary btn-sm mt-3" data-dismiss="modal">
        Tutup
      </button>

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
let lastScanTime = null;

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

/* ================= SCAN (NO CONFIRM) ================= */
document.getElementById('scanForm').addEventListener('submit', async e => {
    e.preventDefault();

    const now = Date.now();

    // Silent anti double scan
    if (lastFingerprint === studentSelect.value && now - lastScanTime < 60000) {
        $('#scanFailed').removeClass('d-none');
        $('#failedText').text('Fingerprint baru saja discan.');
        $('#scanModal').modal('show');
        setTimeout(()=>$('#scanModal').modal('hide'),1500);
        return;
    }

    scanBtn.disabled = true;

    document.getElementById('scanStudentName').innerText =
      studentSelect.options[studentSelect.selectedIndex].text;
    document.getElementById('scanClassName').innerText =
      classSelect.options[classSelect.selectedIndex].text;

    $('#scanModal').modal({ backdrop:'static', keyboard:false });
    $('#scanLoading').show();
    $('#scanSuccess,#scanFailed').addClass('d-none');

    try {
        const res = await fetch('/api/fingerprint/scan', {
            method: 'POST',
            headers: { 'Content-Type':'application/json','Accept':'application/json' },
            body: JSON.stringify({ fingerprint_id: studentSelect.value })
        });

        const data = await res.json();
        $('#scanLoading').hide();

        if (res.ok) {
            $('#scanSuccess').removeClass('d-none');
            $('#successText').html(`
              <strong>${data.student}</strong><br>
              Jam: ${data.time}<br>
              Status: ${data.is_late ? 'TERLAMBAT':'HADIR'}
            `);

            updateLastScan(data);
            prependLog({
                student: data.student,
                time: data.time,
                status: data.is_late ? 'TERLAMBAT':'HADIR'
            });

            lastFingerprint = studentSelect.value;
            lastScanTime = now;

            setTimeout(()=>$('#scanModal').modal('hide'),1500);
        } else {
            $('#scanFailed').removeClass('d-none');
            $('#failedText').text(data.message || 'Scan gagal');
            setTimeout(()=>$('#scanModal').modal('hide'),1800);
        }

    } catch {
        $('#scanLoading').hide();
        $('#scanFailed').removeClass('d-none');
        $('#failedText').text('Koneksi gagal');
        setTimeout(()=>$('#scanModal').modal('hide'),1800);
    } finally {
        setTimeout(()=>scanBtn.disabled = false, 1500);
    }
});

/* ================= LOG ================= */
function prependLog(log) {
    const el = document.createElement('div');
    el.className = 'log-item log-new';
    el.innerHTML = `
      <strong>${log.student}</strong><br>
      <small>${log.time} —
        <span class="${log.status==='HADIR'?'text-success':'text-warning'}">
          ${log.status}
        </span>
      </small>`;
    logBox.prepend(el);
    setTimeout(()=>el.classList.remove('log-new'),1000);
}

function updateLastScan(d) {
    $('#lastName').text(d.student);
    $('#lastStatus').text(d.is_late ? 'TERLAMBAT':'HADIR');
    $('#lastTime').text(d.time);
}
</script>
@endpush
