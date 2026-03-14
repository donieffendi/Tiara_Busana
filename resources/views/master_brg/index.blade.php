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
                                                            value="2" id="columnItem" checked>
                                                        <label class="form-check-label" for="columnItem">Item Supplier</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="3" id="columnSub" checked>
                                                        <label class="form-check-label" for="columnSub">Sub</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="4" id="columnPLU" checked>
                                                        <label class="form-check-label" for="columnPLU">P.L.U</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="5" id="columnNama" checked>
                                                        <label class="form-check-label" for="columnNama">Nama Barang</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="6" id="columnUkuran" checked>
                                                        <label class="form-check-label" for="columnUkuran">Ukuran</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="7" id="columnKemasan" checked>
                                                        <label class="form-check-label" for="columnKemasan">Kemasan</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="8" id="columnSupplier" checked>
                                                        <label class="form-check-label" for="columnSupplier">Supplier</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="9" id="columnJumlah">
                                                        <label class="form-check-label" for="columnJumlah">Jumlah</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="10" id="columnHbeli" checked>
                                                        <label class="form-check-label" for="columnHbeli">H.Beli</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="11" id="columnDis1">
                                                        <label class="form-check-label" for="columnDis1">Dis 1</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="12" id="columnDis2">
                                                        <label class="form-check-label" for="columnDis2">Dis 2</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="12" id="columnDis3">
                                                        <label class="form-check-label" for="columnDis3">Dis 3</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="13" id="columnTotal">
                                                        <label class="form-check-label" for="columnTotal">Total</label>
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
                                <div class="form-group">
                                    <div class="row mb-2">

                                        <div class="col-md-2">
                                            <input type="text" id="sub" name="sub" class="form-control" placeholder="Sub">
                                        </div>

                                        <div class="col-md-3">
                                            <input type="text" id="supp" name="supp" class="form-control" placeholder="Supplier">
                                        </div>

                                        <div class="col-md-2">
                                            <button id="btnPrint" class="btn btn-warning">
                                                <i class="fas fa-print"></i> Print
                                            </button>
                                        </div>

                                    </div>
                                </div>
                        
                                <table class="table table-fixed table-striped table-border table-hover nowrap datatable"
                                    id="datatable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col" style="text-align: center">No</th>
                                            <th scope="col" style="text-align: center">-</th>
                                            <th scope="col" style="text-align: center">Item Suplier</th>
                                            <th scope="col" style="text-align: center">Sub</th>
                                            <th scope="col" style="text-align: center">P.L.U</th>
                                            <th scope="col" style="text-align: center">Nama Barang</th>
                                            <th scope="col" style="text-align: center">Ukuran</th>
                                            <th scope="col" style="text-align: center">Kemasan</th>
                                            <th scope="col" style="text-align: center">Supplier</th>
                                            <th scope="col" style="text-align: center">Jumlah</th>
                                            <th scope="col" style="text-align: center">Harga Beli</th>
                                            <th scope="col" style="text-align: center">Dis 1</th>
                                            <th scope="col" style="text-align: center">Dis 2</th>
                                            <th scope="col" style="text-align: center">Dis 3</th>
                                            <th scope="col" style="text-align: center">Total</th>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    url: '{{ route('get-brg') }}'
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
                        data: 'ITEM_SUP',
                        name: 'ITEM_SUP'
                    },
                    {
                        data: 'SUB',
                        name: 'SUB'
                    },
                    {
                        data: 'PLU',
                        name: 'PLU'
                    },
                    {
                        data: 'NMBAR',
                        name: 'NMBAR',
                        render: function(data) {
                            return '<span class="badge badge-pill badge-warning">' + data +
                                '</span>';
                        }
                    },
                    {
                        data: 'KET_UK',
                        name: 'KET_UK'
                    },
                    {
                        data: 'KET_KEM',
                        name: 'KET_KEM'
                    },
                    {
                        data: 'SUPP',
                        name: 'SUPP'
                    },
                    {data: 'QTY_BELI1', name: 'QTY_BELI1', render: $.fn.dataTable.render.number( ',', '.', 0, '' )},				
                    {data: 'HB', name: 'HB', render: $.fn.dataTable.render.number( ',', '.', 0, '' )},				
                    {data: 'DIS_A', name: 'DIS_A', render: $.fn.dataTable.render.number( ',', '.', 0, '' )},			
                    {data: 'DIS_B', name: 'DIS_B', render: $.fn.dataTable.render.number( ',', '.', 0, '' )},
                    {data: 'DIS_C', name: 'DIS_C', render: $.fn.dataTable.render.number( ',', '.', 0, '' )},
                    {data: 'TOT_BL', name: 'TOT_BL', render: $.fn.dataTable.render.number( ',', '.', 0, '' )}
                ],
                columnDefs: [
                    {
                        "className": "dt-center",
                        "targets": 0
                    },
                    {
                        "className": "dt-right", 
                        "targets": [9,10,11,12,13,14]
                    }		
                ],
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
                '<a class="btn btn-lg btn-md btn-success" href="{{ url('brg/edit?idx=0&tipx=new') }}"> <i class="fas fa-plus fa-sm md-3" ></i></a'
            );

            $('#btnPrint').on('click', function() {
				let sub = $('#sub').val();
                let supp = $('#supp').val();

				// if (!sub) {
				// 	Swal.fire({
				// 		icon: 'warning',
				// 		title: 'Sub belum diisi!',
				// 		text: 'Silakan isi Sub terlebih dahulu sebelum mencetak data.',
				// 		confirmButtonText: 'OK',
				// 		confirmButtonColor: '#3085d6'
				// 	});
				// 	return;
				// }

				Swal.fire({
					title: 'Cetak Data Barang?',
					text: "Laporan akan dibuka di tab baru sesuai filter Sub yang dipilih.",
					icon: 'question',
					showCancelButton: true,
					confirmButtonText: 'Ya, Cetak!',
					cancelButtonText: 'Batal',
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33'
				}).then((result) => {
					if (result.isConfirmed) {
						// buka jasper report di tab baru
						window.open(`{{ url('brg/print') }}?sub=${sub}&supp=${supp}`, '_blank');
					}
				});
			});
        });
        // Open modal programmatically
        // document.querySelector('.btn-primary').addEventListener('click', function(e) {
        //     e.preventDefault(); // Optional, only if needed
        //     var myModal = new bootstrap.Modal(document.getElementById('columnModal'));
        //     myModal.show();
        // });
    </script>
@endsection
