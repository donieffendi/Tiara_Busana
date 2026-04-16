@extends('layouts.plain')
@section('styles')
    <link rel="stylesheet" href="{{ url('AdminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ url('http://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css') }}">
@endsection

<style>
    th {
        font-size: 13px;
    }

    td {
        font-size: 13px;
    }

    /* menghilangkan padding */
    .content-header {
        padding: 0 !important;
    }

    .input-group-text {
        background-color: #f4f6f9;
    }
</style>

@section('content')
    <div class="content-wrapper">

        <!-- Status -->
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">

                                <table class="table table-fixed table-striped table-border table-hover nowrap datatable"
                                    id="datatable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col" style="text-align: center">No</th>
                                            <th scope="col" style="text-align: center">-</th>
                                            <th scope="col" style="text-align: center">Kode Counter</th>
                                            <th scope="col" style="text-align: center">Nama Counter</th>
                                            <th scope="col" style="text-align: center">Kode Barang</th>
                                            <th scope="col" style="text-align: center">Nama Barang</th>
                                            <th scope="col" style="text-align: center">Barcode</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- /.card -->
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('javascripts')

    <!-- filter kolom di index -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- batas filter  -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>

        $(document).ready(function() {
            var dataTable = $('.datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                scrollY: '400px',
                order: [
                    [0, "asc"]
                ],
                ajax: {
                    url: '{{ route('get-cekbcd') }}'
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                    {
                        data: 'CNT',
                        name: 'CNT'
                    },
                    {
                        data: 'NCNT',
                        name: 'NCNT',
                        render: function(data) {
                            return '<span class="badge badge-pill badge-warning">' + data +
                                '</span>';
                        }
                    },
                    {
                        data: 'KD_BRG',
                        name: 'KD_BRG'
                    },
                    {
                        data: 'NA_BRG',
                        name: 'NA_BRG'
                    },
                    {
                        data: 'BARCODE',
                        name: 'BARCODE'
                    },
                ],
                columnDefs: [{
                    "className": "dt-center",
                    "targets": 0
                }],
                dom: "<'row'<'col-md-6'><'col-md-6'>>" +
                    "<'row'<'col-md-2'l><'col-md-6 test_btn m-auto'><'col-md-4'f>>" +
                    "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
                stateSave: false
            });

            $("div.test_btn").html(
                '<button id="btnPrint" class="btn btn-warning">' +
                    '<i class="fas fa-print"></i> Print' +
                '</button>'
            );

            $(document).on('click', '#btnPrint', function() {
                Swal.fire({
                    title: 'Cetak Data Barang?',
                    text: "Laporan akan dibuka di tab baru",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Cetak!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open(`{{ url('cekbcd/print') }}`, '_blank');
                    }
                });
            });
        });
    </script>
@endsection
