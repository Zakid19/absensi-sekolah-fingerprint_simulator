@extends('layouts.master')

@section('content')
<div class="container-fluid d-flex justify-content-center align-items-center" style="min-height:80vh">

  <div class="card shadow-lg" style="width:420px; border-radius:15px">
    <div class="card-header bg-dark text-white text-center">
      <h4 class="mb-0">
        <i class="fas fa-fingerprint"></i> Fingerprint Scanner
      </h4>
      <small class="text-muted">Attendance Simulator</small>
    </div>

    <div class="card-body text-center">

      <div class="mb-3">
        <i class="fas fa-hand-point-up fa-4x text-secondary"></i>
        <p class="mt-2 mb-1"><strong>Tempelkan jari Anda</strong></p>
        <small class="text-muted">Pilih user lalu tekan scan</small>
      </div>

      <form id="scanForm">
        <select name="fingerprint_id" class="form-control mb-3">
          @foreach($classes->flatMap->students->whereNotNull('fingerprint_id') as $s)
            <option value="{{ $s->fingerprint_id }}">{{ $s->name }} — {{ $s->fingerprint_id }}</option>
          @endforeach
        </select>

        <button class="btn btn-success btn-block btn-lg">
          <i class="fas fa-play"></i> Scan Finger
        </button>
      </form>

      <div id="scanResult" class="mt-3"></div>

    </div>

    <div class="card-footer text-center text-muted small">
      Device Status: <span class="text-success">Online</span>
    </div>
  </div>

</div>
@endsection

@push('script')
<script>
document.getElementById('scanForm').addEventListener('submit', async e => {
  e.preventDefault();

  const form = e.target;
  const fingerprint = form.fingerprint_id.value;

  document.getElementById('scanResult').innerHTML =
    `<div class="alert alert-info">Scanning...</div>`;

  const res = await fetch('/api/fingerprint/scan', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept':'application/json' },
    body: JSON.stringify({ fingerprint_id: fingerprint })
  });

  const data = await res.json();

  document.getElementById('scanResult').innerHTML =
    `<div class="alert alert-${res.ok ? 'success':'danger'}">
      ${data.message || data.status}
    </div>`;
});
</script>
@endpush
