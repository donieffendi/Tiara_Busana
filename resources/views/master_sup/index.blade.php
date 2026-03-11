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

                            <!-- filter kolom di index -->

                                <!-- Button to open modal -->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#columnModal">
                                    Filter Columns
                                </button>
                                <!-- Modal -->
                                <div class="modal fade" id="columnModal" tabindex="-1" aria-labelledby="columnModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="columnModalLabel">Toggle Columns</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close">X</button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Column visibility checkboxes -->
                                                <form id="columnToggleForm">
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="0" id="columnNo" checked>
                                                        <label class="form-check-label" for="columnNo">No</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="1" id="columnAction" checked>
                                                        <label class="form-check-label" for="columnAction">Action</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="2" id="columnKode" checked>
                                                        <label class="form-check-label" for="columnKode">Kode</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="3" id="columnNama" checked>
                                                        <label class="form-check-label" for="columnNama">Nama</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="4" id="columnPemilik" checked>
                                                        <label class="form-check-label" for="columnPemilik">Pemilik</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="5" id="columnEmail" checked>
                                                        <label class="form-check-label" for="columnEmail">Email</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="6" id="columnAlamatKantor">
                                                        <label class="form-check-label" for="columnAlamatKantor">Alamat Kantor</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="7" id="columnKotaPemilik">
                                                        <label class="form-check-label" for="columnKotaPemilik">Kota Pemilik</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="8" id="columnAlamatGudang">
                                                        <label class="form-check-label" for="columnAlamatGudang">Alamat Gudang</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="9" id="columnAlamatRumah" checked>
                                                        <label class="form-check-label" for="columnAlamatRumah">Alamat Rumah</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="10" id="columnTlpKantor" checked>
                                                        <label class="form-check-label" for="columnTlpKantor">Tlp Kantor</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="11" id="columnTlpRumah" checked>
                                                        <label class="form-check-label" for="columnTlpRumah">Tlp Rumah</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="12" id="columnFax" checked>
                                                        <label class="form-check-label" for="columnFax">Fax</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="13" id="columnBank" checked>
                                                        <label class="form-check-label" for="columnBank">Bank</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="14" id="columnKotaBank" checked>
                                                        <label class="form-check-label" for="columnKotaBank">Kota Bank</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="15" id="columnNamaRekening" checked>
                                                        <label class="form-check-label" for="columnNamaRekening">Nama Rekening</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="16" id="columnRekening" checked>
                                                        <label class="form-check-label" for="columnRekening">Rekening</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="17" id="columnCaraBayar" checked>
                                                        <label class="form-check-label" for="columnCaraBayar">Cara Bayar</label>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary"
                                                    id="applyColumnToggle">Apply</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                            <!-- batas filter -->

                        
                                <table class="table table-fixed table-striped table-border table-hover nowrap datatable"
                                    id="datatable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col" style="text-align: center">No</th>
                                            <th scope="col" style="text-align: center">-</th>
                                            <th scope="col" style="text-align: center">Kodes</th>
                                            <th scope="col" style="text-align: center">Namas</th>
                                            <th scope="col" style="text-align: center">Pemilik</th>
                                            <th scope="col" style="text-align: center">Email</th>
                                            <th scope="col" style="text-align: center">Alamat Kantor</th>
                                            <th scope="col" style="text-align: center">Kota Pemilik</th>
                                            <th scope="col" style="text-align: center">Alamat Gudang</th>
                                            <th scope="col" style="text-align: center">Alamat Rumah</th>
                                            <th scope="col" style="text-align: center">Tlp Kantor</th>
                                            <th scope="col" style="text-align: center">Tlp Rumah</th>
                                            <th scope="col" style="text-align: center">Fax</th>
                                            <th scope="col" style="text-align: center">Bank</th>
                                            <th scope="col" style="text-align: center">Kota Bank</th>
                                            <th scope="col" style="text-align: center">Nama Rekening</th>
                                            <th scope="col" style="text-align: center">Rekening</th>
                                            <th scope="col" style="text-align: center">Cara Bayar</th>
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

    <script>

        // filter kolom di index
        window.addEventListener('message', (event) => {
            if (event.origin !== window.location.origin) {
                console.warn('Origin mismatch!');
                return;
            }

            const currentData = event.data;
            console.log(currentData); // Use currentData as needed
        });
        // batas filter

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
                    url: '{{ route('get-sup') }}'
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
                        data: 'KODES',
                        name: 'KODES'
                    },
                    {
                        data: 'NAMAS',
                        name: 'NAMAS',
                        render: function(data) {
                            return '<span class="badge badge-pill badge-warning">' + data +
                                '</span>';
                        }
                    },
                    {
                        data: 'PEMILIK',
                        name: 'PEMILIK'
                    },
                    {
                        data: 'EMAIL',
                        name: 'EMAIL'
                    },
                    {
                        data: 'P_ALMT',
                        name: 'P_ALMT'
                    },
                    {
                        data: 'P_KOTA',
                        name: 'P_KOTA'
                    },
                    {
                        data: 'G_ALMT',
                        name: 'G_ALMT'
                    },
                    {
                        data: 'R_ALMT',
                        name: 'R_ALMT'
                    },
                    {
                        data: 'P_TLP',
                        name: 'P_TLP'
                    },
                    {
                        data: 'R_TLP',
                        name: 'R_TLP'
                    },
                    {
                        data: 'P_FAX',
                        name: 'P_FAX'
                    },
                    {
                        data: 'B_BANK',
                        name: 'B_BANK'
                    },
                    {
                        data: 'B_KOTA',
                        name: 'B_KOTA'
                    },
                    {
                        data: 'B_NAMA',
                        name: 'B_NAMA'
                    },
                    {
                        data: 'B_ACC',
                        name: 'B_ACC'
                    },
                    {
                        data: 'CARA',
                        name: 'CARA'
                    }
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

            // filter kolom di index

            // Handle column visibility toggle
            $('#applyColumnToggle').on('click', function() {
                $('#columnToggleForm .column-checkbox').each(function() {
                    var column = dataTable.column($(this).val());
                    column.visible($(this).is(':checked'));
                });
                $('#columnModal').modal('hide'); // Close the modal
            });

            $('#columnToggleForm .column-checkbox').each(function() {
                var column = dataTable.column($(this).val());
                column.visible($(this).is(':checked'));
            });
            
            // batas filter

            $("div.test_btn").html(
                '<a class="btn btn-lg btn-md btn-success" href="{{ url('sup/edit?idx=0&tipx=new') }}"> <i class="fas fa-plus fa-sm md-3" ></i></a'
            );
        });
        // Open modal programmatically
        // document.querySelector('.btn-primary').addEventListener('click', function(e) {
        //     e.preventDefault(); // Optional, only if needed
        //     var myModal = new bootstrap.Modal(document.getElementById('columnModal'));
        //     myModal.show();
        // });
    </script>
@endsection
