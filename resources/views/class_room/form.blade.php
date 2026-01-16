@extends('layouts.master')

@push('style')
    <link rel="stylesheet" href="/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush
@section('content')
    @php
        if (isset($class)) {
            $actionUrl = route('class.update', $class->id);
        } else {
            $actionUrl = route('class.store');
        }
    @endphp
{{ Session::get('message') }}
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    @if(isset($class))
                        <h1 class="m-0">Edit Kelas</h1>
                    @else
                        <h1 class="m-0">Tambah Kelas</h1>
                    @endif

                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Kelas</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ isset($class) ? 'Edit Kelas' : 'Tambah Kelas' }}</h3>
                        </div>
                        <form id="submitClass" method="POST" action="{{ $actionUrl }}" enctype="multipart/form-data">
                            @if (@isset($class))
                                {{ method_field('PUT') }}
                                <input type="hidden" name="user_id" value="{{ $class->id }}" />
                            @endif
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Kelas</label>
                                    <input type="text" required class="form-control" name="name" id="name"
                                        placeholder="Kelas" value="{{ isset($class) ? $class->name : old('name') }}">
                                    @error('name')
                                        <div class="mt-2 text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" submitClass="submit" class="btn btn-info btn-sm">Submit</button>
                                <a href="{{ url()->previous() ?? route('class.manage') }}" class="btn btn-secondary btn-sm">
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="/admin/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('#description').summernote({
        placeholder: 'Deskripsi',
        tabsize: 2,
        height: 150
        });
    });
</script>


