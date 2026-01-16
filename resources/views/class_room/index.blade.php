@extends('layouts.master')
@push('style')
    <link rel="stylesheet" href="/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Data Kelas</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item active">Kelas</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-sm-12">
                <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <a href="{{ route('class.create') }}" class="btn btn-info btn-sm">Tambah</a>
                            <a href="{{ route('class.manage') }}" class="btn btn-success btn-sm">Manage Kelas</a>
                        </div>
                        <div class="table-responsive">
                            <table id="dataStudent" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                      <th>No</th>
                                      <th>Nama Kelas</th>
                                      <th>Jumlah Siswa</th>
                                      <th>Aksi</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach ($classes as $i => $class)
                                      <tr>
                                          <td>{{ $i+1 }}</td>
                                          <td>{{ $class->name }}</td>
                                          <td>{{ $class->students_count }}</td>
                                          <td>
                                              <a href="{{ route('class.show', $class->id) }}" class="btn btn-primary btn-sm">
                                                  Lihat Siswa
                                              </a>
                                          </td>
                                      </tr>
                                  @endforeach
                              </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="/admin/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
@endpush
