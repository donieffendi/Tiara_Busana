@extends('layouts.plain')
@section('styles')
<link rel="stylesheet" href="{{url('AdminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{url('http://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css') }}">
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

    .badge-warning {
        background-color: #06ba00 !important;
        /* Warna default badge-warning (kuning) */
        color: white !important;
        /* Warna teks putih */
    }

    .datatable,
	.dataTables_wrapper,
	.datatable thead,
	.datatable tbody tr,
	.dataTables_scrollBody {
		background-color: white;
		color: black;
	}

	/* Override baris ganjil/genap supaya semua putih */
	.datatable tbody tr.odd,
	.datatable tbody tr.even {
		background-color: white !important;
		color: black !important;
	}

	/* Hover effect untuk baris */
	.datatable tbody tr:hover {
		background-color: #f2f2f2 !important; /* ganti sesuai selera */
		color: black !important;
		cursor: pointer;
	}
</style>

@section('content')

<!-- Sweetalert delete -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!--  -->

<div class="content-wrapper">

    <!-- Status -->
    <!-- @if (session('status'))
        <div class="alert alert-success">
            {{session('status')}}
        </div>

        <script>
            Swal.fire({
              title: 'Deleted!',
              text: 'Data has been deleted. {{session('status')}}',
              icon: 'success',
              confirmButtonText: 'OK'
            })
        </script>
    @endif -->
    <!-- tutupannya -->


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
                                                        value="1" id="columnKode" checked>
                                                    <label class="form-check-label" for="columnKode">Kode Barang</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input column-checkbox" type="checkbox"
                                                        value="2" id="columnNama" checked>
                                                    <label class="form-check-label" for="columnNama">Nama Barang</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input column-checkbox" type="checkbox"
                                                        value="3" id="columnKodes" checked>
                                                    <label class="form-check-label" for="columnKodes">Kode Supplier</label>
                                                </div><div class="form-check">
                                                    <input class="form-check-input column-checkbox" type="checkbox"
                                                        value="4" id="columnNamas" checked>
                                                    <label class="form-check-label" for="columnNamas">Nama Supplier</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input column-checkbox" type="checkbox"
                                                        value="5" id="columnHarga" checked>
                                                    <label class="form-check-label" for="columnHarga">Harga</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input column-checkbox" type="checkbox"
                                                        value="6" id="columnDiskon" checked>
                                                    <label class="form-check-label" for="columnDiskon">Diskon</label>
                                                </div><div class="form-check">
                                                    <input class="form-check-input column-checkbox" type="checkbox"
                                                        value="7" id="columnDiskon2" checked>
                                                    <label class="form-check-label" for="columnDiskon2">Diskon 2</label>
                                                </div><div class="form-check">
                                                    <input class="form-check-input column-checkbox" type="checkbox"
                                                        value="8" id="columnDiskon3" checked>
                                                    <label class="form-check-label" for="columnDiskon3">Diskon 3</label>
                                                </div><div class="form-check">
                                                    <input class="form-check-input column-checkbox" type="checkbox"
                                                        value="9" id="columnDiskon4" checked>
                                                    <label class="form-check-label" for="columnDiskon4">Diskon 4</label>
                                                </div><div class="form-check">
                                                    <input class="form-check-input column-checkbox" type="checkbox"
                                                        value="9" id="columnPpn" checked>
                                                    <label class="form-check-label" for="columnPpn">PPN (%)</label>
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
                            <div style="margin-bottom: 20px;"></div>
                            <!-- batas filter -->

                            <table class="table table-fixed table-striped table-border table-hover nowrap datatable" id="datatable">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col" style="text-align: center">No</th>
                                        <th scope="col" style="text-align: center">Kode Barang</th>
                                        <th scope="col" style="text-align: center">Nama Barang</th>
                                        <th scope="col" style="text-align: center">Kode Supplier</th>
                                        <th scope="col" style="text-align: center">Nama Supplier</th>
                                        <th scope="col" style="text-align: center">Harga</th>
                                        <th scope="col" style="text-align: center">Diskon</th>
                                        <th scope="col" style="text-align: center">Diskon 2</th>
                                        <th scope="col" style="text-align: center">Diskon 3</th>
                                        <th scope="col" style="text-align: center">PPN (%)</th>
                                        <!-- <th scope="col" style="text-align: center">Stock Gd</th> -->
                                    </tr>
                                </thead>

                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascripts')
<script src="{{url('AdminLTE/plugins/datatables/jquery.dataTables.js') }}"></script>
<script src="{{url('AdminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{url('http://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js') }}"></script>

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
            // 'scrollX': true,
            'scrollY': '400px',
            "order": [
                [0, "asc"]
            ],
            ajax: {
                url: "{{ route('get-vbrgdw') }}"
            },
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },

                {
                    data: 'KD_BRG',
                    name: 'KD_BRG'
                },
                {
                    data: 'NA_BRG',
                    name: 'NA_BRG'
                    
                }, {
                    data: 'KODES',
                    name: 'KODES',

                }, {
                    data: 'NAMAS',
                    name: 'NAMAS',
                    render: function(data, type, row, meta) {
                        return ' <h5><span class="badge badge-pill badge-info">' + data + '</span></h5>';
                    }
                }, {
                    data: 'HARGA',
                    name: 'HARGA',
                    render: function (data, type, row, meta) {
                        if (type === 'display' || type === 'filter') {
                            let rupiah = new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format(data);

                            return '<h5><span class="badge badge-pill badge-warning">' + rupiah + '</span></h5>';
                        }
                        return data;
                    }

                }, {
                    data: 'DISC',
                    name: 'DISC',
                    render: $.fn.dataTable.render.number( ',', '.', 2, '' )
                },{
                    data: 'DISC2',
                    name: 'DISC2',
                    render: $.fn.dataTable.render.number( ',', '.', 2, '' )
                },{
                    data: 'DISC3',
                    name: 'DISC3',
                    render: $.fn.dataTable.render.number( ',', '.', 2, '' )
                },{
                    data: 'PPN',
                    name: 'PPN',
                    render: $.fn.dataTable.render.number( ',', '.', 2, '' )
                },

            ],
            columnDefs: [
                {
                    "className": "dt-center",
                    "targets": [0, 1, 2, 3, 9]
                }, 
                {
                    "className": "dt-right",
                    "targets": [6, 7, 8]
                }
        ],
            dom: "<'row'<'col-md-6'><'col-md-6'>>" +
                "<'row'<'col-md-2'l><'col-md-6 test_btn m-auto'><'col-md-4'f>>" +
                "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",

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

        //$("div.test_btn").html(`<a class="btn btn-lg btn-md btn-success" href="{{url('vbrgdw/edit?idx=0&tipx=new')}}"> <i class="fas fa-plus fa-sm md-3" ></i></a>`);
    });

    function deleteRow(link) {
        console.log('Masuk');
        Swal.fire({
            title: 'Are you sure?',
            text: "Are you sure?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = link;
            }
        });
    }
</script>
@endsection