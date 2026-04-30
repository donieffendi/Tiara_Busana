@extends('layouts.plain')
@section('styles')
    <link rel="stylesheet" href="{{ url('http://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css') }}">
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $judul }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">{{ $judul }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" id="entri"
                                    action="{{ url('posting/proses?flagz=' . $flagz) }}">
                                    @csrf
                                    <input name="flagz" type="hidden" value="{{ $flagz }}">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <button class="btn btn-danger" type="button" onclick="simpan()">Posting</button>

                                        @if ($flagz == 'BL' || $flagz == 'B3' || $flagz == 'B5' || $flagz == 'B8' || $flagz == 'TS' || $flagz == 'RB')
                                            <h5 class="mb-0 ms-3">MAX PER 15 NO BUKTI UNTUK DIPOSTING</h5>
                                        @endif
                                    </div>


                                    <table class="table table-striped table-bordered table-hover datatable" id="datatable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th scope="col" style="text-align: center">#</th>
                                                <th scope="col" style="text-align: center">No Bukti</th>
                                                <th scope="col" style="text-align: center">Tanggal</th>
                                                <th scope="col" style="text-align: center">Supplier</th>
                                                <th scope="col" style="text-align: center">Nama</th>
                                                <th scope="col" style="text-align: center">Total Qty</th>
                                                <th scope="col" style="text-align: center">Total / Bruto</th>
                                                <th scope="col" style="text-align: center">Total Nett</th>
                                                <th scope="col" style="text-align: center">Notes</th>
                                                <th scope="col" style="text-align: center">Type</th>
                                                <th scope="col">Cek</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
    <script src="{{ asset('js/autoNumerics/autoNumeric.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('status'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('status') }}'
            });
        </script>
    @endif
    <script>
        $(document).ready(function() {
            var dataTable = $('.datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                scrollX: true,
                scrollY: '400px',
                scrollCollapse: true,
                "order": [
                    [0, "asc"]
                ],
                ajax: {
                    url: "{{ url('get-posting') }}",
                    data: {
                        filterpost: 1,
                        flagz: "{{ $flagz }}",
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'NO_BUKTI',
                        name: 'NO_BUKTI'
                    },
                    {
                        data: 'TGL',
                        name: 'TGL',
                        render: $.fn.dataTable.render.moment('DD-MM-YYYY')
                    },
                    {
                        data: 'KODES',
                        name: 'KODES'
                    },
                    {
                        data: 'NAMAS',
                        name: 'NAMAS'
                    },
                    {
                        data: 'TOTAL_QTY',
                        name: 'TOTAL_QTY',
                        render: $.fn.dataTable.render.number(',', '.', 0, '')
                    },
                    {
                        data: 'TOTAL',
                        name: 'TOTAL',
                        render: $.fn.dataTable.render.number(',', '.', 0, '')
                    },
                    {
                        data: 'NETT',
                        name: 'NETT',
                        render: $.fn.dataTable.render.number(',', '.', 0, '')
                    },
                    {
                        data: 'NOTES',
                        name: 'NOTES'
                    },
                    {
                        data: 'TYPE',
                        name: 'TYPE'
                    },
                    {
                        data: 'cek',
                        name: 'cek'
                    }
                ],
                columnDefs: [{
                        "className": "dt-center",
                        "targets": [0, 1, 2, 3, 4]
                    },
                    {
                        "className": "dt-right",
                        "targets": [5, 6, 7]
                    },
                ],
            });

        });

        function simpan() {
            Swal.fire({
                title: 'Yakin ingin posting?',
                text: 'Data yang diposting tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Posting!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("entri").submit();
                }
            });
        }
    </script>
@endsection
