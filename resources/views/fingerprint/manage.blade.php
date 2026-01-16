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
                    <h1 class="m-0">Fingerprint Kelas

                        @if($selected_class)
                        {{ $selected_class->name }}
                    @endif
                    </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Fingerprint</li>
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
                            <div class="table-responsive">
                                <table id="registerFingerprint" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            @foreach ($table['columns'] as $column)
                                                <th>{{ $column['label'] }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
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
    <script type="text/javascript">
        $(document).ready(function() {
            $('#registerFingerprint').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json',
                },

                processing: true,
                serverSide: false,
                ajax: '{{ $table['table_url'] }}',
                ordering: true,
                // searching: true,
                paging: true,
                autoWidth: true,
                scrollY: false,
                // info: false,

                columns: [
                    @foreach ($table['columns'] as $column)
                        {
                            data: '{{ $column['name'] }}',
                            name: '{{ $column['name'] }}'
                        },
                    @endforeach
                ],
                drawCallback: function() {
                    // Delete Confirmation
                    $(".delete-form").on("click", function() {
                        var form = $(this).parent().find("form");
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Apakah kamu yakin ingin menghapus item ini?",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes!'
                        }).then((result) => {
                            if (result.value) {
                                form.submit();
                            }
                        })
                    });
                }
            });
        });
    </script>
@endpush
