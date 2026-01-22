@extends('layouts.master')

@section('title', 'Registrasi Fingerprint')

@section('content')
<div class="container-fluid">

  {{-- ================= HEADER ================= --}}
  <div class="card mb-3">
    <div class="card-body d-flex justify-content-between align-items-center">
      <div>
        <h4 class="mb-0">Registrasi Fingerprint</h4>
        <small class="text-muted">Mode registrasi (bukan absensi)</small>
      </div>

      <div style="width:260px">
        <select id="classSelect" class="form-control">
          <option value="">— Pilih Kelas —</option>
          @foreach($classes as $class)
            <option value="{{ $class->id }}">{{ $class->name }}</option>
          @endforeach
        </select>
      </div>
    </div>
  </div>

  <div class="row">

    {{-- ================= SCAN PANEL ================= --}}
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
          <strong>🖐 Scan Fingerprint</strong>
        </div>

        <div class="card-body text-center">

          <div id="waitingBox">
            <i class="fas fa-fingerprint fa-4x text-secondary mb-3"></i>
            <h5>Menunggu Fingerprint...</h5>
            <small class="text-muted">
              Silakan siswa scan jari di device
            </small>
          </div>

          <div id="scanResult" class="d-none">
            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
            <h5>Fingerprint Terdeteksi</h5>
            <p class="mb-1">
              ID: <strong id="fingerprintIdText"></strong>
            </p>
            <small class="text-muted">
              Silakan hubungkan ke siswa
            </small>
          </div>

        </div>
      </div>
    </div>

    {{-- ================= MAPPING PANEL ================= --}}
    <div class="col-md-7">
      <div class="card shadow-sm">
        <div class="card-header bg-light">
          <strong>🔗 Hubungkan ke Siswa</strong>
        </div>

        <div class="card-body">

          <form id="mappingForm">
            @csrf

            <input type="hidden" id="fingerprintInput" name="fingerprint_id">

            <div class="form-group">
              <label>Pilih Siswa</label>
              <select id="studentSelect" name="student_id" class="form-control" disabled>
                <option value="">— Pilih siswa —</option>
              </select>
            </div>

            <button type="submit" class="btn btn-primary" id="mapBtn" disabled>
              <i class="fas fa-link"></i> Hubungkan Fingerprint
            </button>
          </form>

          <div class="alert alert-info mt-3 mb-0">
            <small>
              ⚠️ Pastikan siswa yang dipilih adalah siswa
              yang barusan melakukan scan.
            </small>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@push('script')
<script>
const classes = @json($classes);

let currentFingerprint = null;

const classSelect   = document.getElementById('classSelect');
const studentSelect = document.getElementById('studentSelect');
const mapBtn        = document.getElementById('mapBtn');
const waitingBox    = document.getElementById('waitingBox');
const scanResult    = document.getElementById('scanResult');
const fingerprintText = document.getElementById('fingerprintIdText');
const fingerprintInput = document.getElementById('fingerprintInput');

/* ================= HELPER ================= */
function updateMapButtonState() {
    mapBtn.disabled = !(currentFingerprint && studentSelect.value);
}

/* ================= LOAD STUDENTS BY CLASS ================= */
classSelect.addEventListener('change', function () {
    studentSelect.innerHTML = '<option value="">— Pilih siswa —</option>';
    studentSelect.disabled = true;
    currentFingerprint = null;

    waitingBox.classList.remove('d-none');
    scanResult.classList.add('d-none');

    const cls = classes.find(c => c.id == this.value);
    if (!cls) {
        updateMapButtonState();
        return;
    }

    cls.students.forEach(s => {
        if (s.fingerprint_id) return;
        studentSelect.innerHTML += `
          <option value="${s.id}">
            ${s.name}
          </option>`;
    });

    studentSelect.disabled = false;
    updateMapButtonState();
});

/* ================= ENABLE BUTTON ON STUDENT SELECT ================= */
studentSelect.addEventListener('change', () => {
    updateMapButtonState();
});

/* ================= POLLING SCAN REGISTRASI ================= */
setInterval(async () => {
    if (!classSelect.value) return;

    const res = await fetch('/api/fingerprint/register/last');
    if (!res.ok) return;

    const data = await res.json();
    if (!data.fingerprint_id) return;
    if (data.fingerprint_id === currentFingerprint) return;

    currentFingerprint = data.fingerprint_id;

    waitingBox.classList.add('d-none');
    scanResult.classList.remove('d-none');

    fingerprintText.innerText = data.fingerprint_id;
    fingerprintInput.value = data.fingerprint_id;

    updateMapButtonState();
}, 2000);

/* ================= SUBMIT MAPPING ================= */
document.getElementById('mappingForm').addEventListener('submit', async e => {
    e.preventDefault();

    if (!currentFingerprint || !studentSelect.value) return;

    const form = e.target;

    const res = await fetch('/fingerprint/map', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': form.querySelector('input[name=_token]').value,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            fingerprint_id: currentFingerprint,
            student_id: studentSelect.value
        })
    });

    if (res.ok) {
        // reset state for next student
        currentFingerprint = null;
        studentSelect.value = '';

        scanResult.classList.add('d-none');
        waitingBox.classList.remove('d-none');

        updateMapButtonState();

        alert('Fingerprint berhasil dihubungkan');
    }
});
</script>
@endpush
