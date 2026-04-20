@extends('layouts.plain')
@section('styles')
    <!-- <link rel="stylesheet" href="{{ url('http://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('foxie_js_css/jquery.dataTables.min.css') }}" />
@endsection

<style>
    .card {
        padding: 5px 10px !important;
    }

    .table thead {
        background-color: #fff;
        color: #000;
    }

    .datatable tbody td {
        padding: 5px !important;
    }

    .datatable {
        border-right: solid 2px #000;
        border-left: solid 2px #000;
    }


    .btn-secondary {
        background-color: #42047e !important;
    }

    th {
        font-size: 13px;
    }

    td {
        font-size: 13px;
    }

    .x {
        Color: red
    }

    /* menghilangkan padding */
    .content-header {
        padding: 0 !important;
    }

    /*
    BG WARNA PUTIH
     */

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
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>

            <!-- tambahan notifikasinya untuk delete di index -->
            <script>
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Data has been deleted. {{ session('status') }}',
                    icon: 'success',
                    confirmButtonText: 'OK'
                })
            </script>
            <!-- tutupannya -->
        @endif

        @if (session('success'))
            <script>
                Swal.fire({
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'OK'
                })
            </script>
        @endif

        @if (session('error'))
            <script>
                Swal.fire({
                    title: 'Oops!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'OK'
                })
            </script>
        @endif

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">

                                <!-- filter kolom di index -->
                                @if (session('errorInsert'))
                                    <div class="alert alert-danger" role="alert">
                                        <strong>Gagal menyimpan:</strong> {{ session('errorInsert') }}
                                    </div>
                                @endif
                                <div class="d-flex justify-content-start gap-2 mb-3">
                                    <!-- Button to open modal -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#columnModal">
                                        Filter Columns
                                    </button>

                                </div>
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
                                                            value="0" id="columnDetail" checked>
                                                        <label class="form-check-label" for="columnDetail">Detail</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="1" id="columnNo" checked>
                                                        <label class="form-check-label" for="columnNo">No</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="2" id="columnAction" checked>
                                                        <label class="form-check-label" for="columnAction">Action</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="3" id="columnBukti" checked>
                                                        <label class="form-check-label" for="columnBukti">Bukti#</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="4" id="columnTgl" checked>
                                                        <label class="form-check-label" for="columnTgl">Tgl</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="5" id="columnTgl" checked>
                                                        <label class="form-check-label" for="columnTgl">Tgl</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="6" id="columnNotes" checked>
                                                        <label class="form-check-label" for="columnNotes">Keterangan</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input column-checkbox" type="checkbox"
                                                            value="7" id="columnPosted" checked>
                                                        <label class="form-check-label" for="columnPosted">Status Posting</label>
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

                                {{-- <input name="flagz" class="form-control flagz" id="flagz" value="{{ $flagz }}"
                                    hidden> --}}


                                {{-- <table class="table table-fixed table-striped table-border table-hover nowrap datatable" id="datatable">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" style="text-align: center"></th>
                            <th scope="col" style="text-align: center">#</th>
				     		<th scope="col" style="text-align: center">-</th>
                            <th scope="col" style="text-align: left">Bukti#</th>
                            <th scope="col" style="text-align: left">Tgl</th>
                            <th scope="col" style="text-align: right">Total_Qty</th>
                            <th scope="col" style="text-align: left">Notes</th>
                        </tr>
                    </thead>

                    <tbody>
                    </tbody>
                </table> --}}
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <button class="nav-link active" id="tab1-tab" data-bs-toggle="tab"
                                            data-bs-target="#tab1" type="button">
                                            Usulan
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" id="tab2-tab" data-bs-toggle="tab"
                                            data-bs-target="#tab2" type="button">
                                            Pengesahan
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content mt-3">



                                    <!-- TAB 1 -->
                                    <div class="tab-pane fade show active" id="tab1">
									<!-- Scan Barcode -->
                                    <a class="btn btn-success btn-lg"
                                        href="{{ url('ubbrgdw/edit?idx=0&tipx=new') }}">
                                        <i class="fas fa-file"></i> Usulan Baru
                                    </a>
                                        <table class="table table-striped table-hover nowrap datatable" id="datatable1">
                                            <thead class="table-dark">
                                                <tr>
                                                    {{-- <th></th>
                                                    <th>#</th>
                                                    <th>-</th>
                                                    <th>Bukti#</th>
                                                    <th>Tgl</th>
                                                    <th>Total_Qty</th>
                                                    <th>Notes</th>
                                                    <th>Posted</th> --}}

                                                    <th scope="col" style="text-align: center"></th>
                                                    <th scope="col" style="text-align: center">#</th>
                                                    <th scope="col" style="text-align: center">Action</th>
                                                    <th scope="col" style="text-align: center">No Beli</th>
                                                    <th scope="col" style="text-align: center">No Bukti</th>
                                                    <th scope="col" style="text-align: center">Tgl</th>
                                                    <th scope="col" style="text-align: center">Supplier#</th>
                                                    <th scope="col" style="text-align: center">Nama Supplier</th>
                                                    <th scope="col" style="text-align: center">Keterangan</th>
                                                    <th scope="col" style="text-align: center">User</th>
                                                    <th scope="col" style="text-align: center">Posted</th>
                                                    <th scope="col" style="text-align: center">Selesai</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>

                                    <!-- TAB 2 -->
                                    <div class="tab-pane fade" id="tab2">
									 <!-- New -->
                                    {{-- <a class="btn btn-primary btn-lg"
                                        href="{{ url('ubbrgdw-new/edit?idx=0&tipx=new') }}">
                                        <i class="fas fa-check"></i> Pengesahan
                                    </a> --}}
                                        <table class="table table-striped table-hover nowrap datatable" id="datatable2">
                                            <thead class="table-dark">
                                                <tr>
                                                    {{-- <th></th>
                                                    <th>#</th>
                                                    <th>-</th>
                                                    <th>Bukti#</th>
                                                    <th>Tgl</th>
                                                    <th>Total_Qty</th>
                                                    <th>Notes</th> --}}

                                                    <th scope="col" style="text-align: center"></th>
                                                    <th scope="col" style="text-align: center">#</th>
                                                    <th scope="col" style="text-align: center">Action</th>
                                                    <th scope="col" style="text-align: center">No Beli</th>
                                                    <th scope="col" style="text-align: center">No Bukti</th>
                                                    <th scope="col" style="text-align: center">Tgl</th>
                                                    <th scope="col" style="text-align: center">Supplier#</th>
                                                    <th scope="col" style="text-align: center">Nama Supplier</th>
                                                    <th scope="col" style="text-align: center">Keterangan</th>
                                                    <th scope="col" style="text-align: center">User</th>
                                                    <th scope="col" style="text-align: center">Posted</th>
                                                    <th scope="col" style="text-align: center">Selesai</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



     <!-- Modal Detail -->
     <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel"
         aria-hidden="true">
         <div class="modal-dialog modal-xl" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="detailModalLabel">Detail Perubahan Harga</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     <table class="table table-striped table-bordered" id="table-detail">
                         <thead>
                             <tr>
                                 <th>No.</th>
                                 <th>Kode Barang</th>
                                 <th>Nama Barang</th>
                                 <th>Qty</th>
                                 <th>Harga Lama</th>
                                 <th>Harga Baru</th>
                                 <th>Disk Lama</th>
                                 <th>Disk Baru</th>
                                 <th>Disk2 Lama</th>
                                 <th>Disk2 Baru</th>
                                 <th>Disk3 Lama</th>
                                 <th>Disk3 Baru</th>
                                 <th>Disk4 Lama</th>
                                 <th>Disk4 Baru</th>
                                 <th>Total</th>
                                 <th>Keterangan</th>
                             </tr>
                         </thead>
                         <tbody>
                         </tbody>
                     </table>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                 </div>
             </div>
         </div>
     </div>
@endsection

@section('javascripts')
    <!-- filter kolom di index -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('foxie_js_css/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('foxie_js_css/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js"></script>

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
            let table1, table2;

            table1 = initDataTable('datatable1', "{{ route('ubbrgdw.browse') }}");

            // TAB 2 load saat diklik
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                let target = $(e.target).attr("data-bs-target");

                if (target === '#tab2' && !$.fn.DataTable.isDataTable('#datatable2')) {
                    table2 = initDataTable('datatable2', "{{ route('ubbrgdw.browse') }}");
                }
            });

            function initDataTable(tableId, url) {
                return $('#' + tableId).DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    scrollY: '400px',

                    ajax: {
                        url: url,
                        data: function(d) {

                            d.periode = $('#periode').val();
                            // flagz: $('#flagz').val()
                        }
                    },
                    columns: [
                        //add tombol +
                        {

                            data: null, // Column for the button
                            orderable: false,
                            searchable: false,
                            className: "text-center",
                            render: function(data, type, row, meta) {

                                // kalau ada query POST di bagian paling atas, pada onclick perlu di tambah "event.preventDefault()"
                                return `<button class="btn btn-success btn-sm toggle-button" data-no_bukti="${row.NO_BELI}" onclick="event.preventDefault();toggleButton(this)">+</button>`;
                            }
                        },
                        {
                            "data": null,
                            "className": "text-center",
                            "orderable": false,
                            "render": function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        }, {
                            "data": "action",
                            "className": "text-center"
                        },

                        {
                            "data": "NO_BELI",
                            "className": "text-center"
                        },
                        {
                            "data": "NO_BUKTI",
                            "className": "text-center"
                        },
                        {
                            "data": "TGL",
                            "className": "text-center"
                        },
                        {
                            "data": "KODES",
                            "className": "text-center"
                        },
                        {
                            "data": "NAMAS",
                            "className": "text-left"
                        },
                        {
                            "data": "KET",
                            "className": "text-left"
                        },
                        {
                            "data": "USRNM",
                            "className": "text-center"
                        },
                        {
                            data: 'POSTED',
                            name: 'POSTED',
                            render: function(data, type, row, meta) {
                                if (row['POSTED'] == "0") {
                                    return '';
                                } else {
                                    return '<input type="checkbox" checked style="pointer-events: none;">';
                                }
                            }
                        }, {
                            data: 'SELESAI',
                            name: 'SELESAI',
                            render: function(data, type, row, meta) {
                                if (row['SELESAI'] == "0") {
                                    return '';
                                } else {
                                    return '<input type="checkbox" checked style="pointer-events: none;">';
                                }
                            }
                        }
                    ],
                    columnDefs: [
                        {
                            "className": "dt-center",
                            "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                        },
                        // {
                        //     "className": "dt-right",
                        //     "targets": 4
                        // },
                        {
                            targets: 5,
                            render: $.fn.dataTable.render.moment('DD-MM-YYYY')
                        }
                    ],
                    lengthMenu: [
                        [8, 10, 20, 50, 100, -1],
                        [8, 10, 20, 50, 100, "All"]
                    ],


                    dom: "<'row'<'col-md-6'><'col-md-6'>>" +
                        "<'row'<'col-md-2'l><'col-md-6 test_btn m-auto'><'col-md-4'f>>" +
                        "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
                });



                // Filter button event
                $('#filterBtn').click(function() {
                    table.ajax.reload();
                });

                // Modal close handlers
                $(document).on('click', '[data-dismiss="modal"]', function() {
                    $(this).closest('.modal').modal('hide');
                });
                $(document).ready(function() {
                    $("div.test_btn").html(`
                        <a class="btn btn-lg btn-md btn-success" href="{{ url('ubbrgdw/edit?idx=0&tipx=new') }}">
                            <i class="fas fa-plus fa-sm md-3"></i>
                        </a>
                        <a id="btn-otomatis" class="btn btn-lg btn-md btn-warning">
                            Otomatis
                        </a>
                    `);

                    $(document).on('click', '#btn-otomatis', function() {

                        $('#loading-overlay').show();

                        $(this).html(`
                            <span class="spinner-border spinner-border-sm"></span> Proses...
                        `);

                        $(this).prop('disabled', true);

                        window.location.href = "{{ url('ubbrgdw/ubbrgdw-otomatis') }}";
                    });
                });

                // Column toggle functionality
                $('#applyColumnToggle').click(function() {
                    console.log('Apply column toggle clicked');
                    console.log('Table object:', table);
                    console.log('Checkboxes found:', $('.column-checkbox').length);

                    $('.column-checkbox').each(function() {
                        var column = table.column($(this).val());
                        console.log('Setting column', $(this).val(), 'visible:', $(this).is(
                            ':checked'));
                        column.visible($(this).is(':checked'));
                    });
                    $('#columnModal').modal('hide');
                });

                // Period input mask
                $('#periode').mask('99/9999', {
                    placeholder: 'MM/YYYY'
                });

                // Number format function
                function numberFormat(num, decimals) {
                    if (num === null || num === undefined || num === '') return '0.00';
                    var number = parseFloat(num);
                    if (isNaN(number)) return '0.00';
                    return number.toLocaleString('en-US', {
                        minimumFractionDigits: decimals || 2,
                        maximumFractionDigits: decimals || 2
                    });
                }
             });

            // Global number format function
            function numberFormatGlobal(num, decimals) {
                if (num === null || num === undefined || num === '') return '0.00';
                var number = parseFloat(num);
                if (isNaN(number)) return '0.00';

                // Test console log
                console.log('Formatting number:', num, 'Result:', number.toLocaleString('en-US', {
                    minimumFractionDigits: decimals || 2,
                    maximumFractionDigits: decimals || 2
                }));

                return number.toLocaleString('en-US', {
                    minimumFractionDigits: decimals || 2,
                    maximumFractionDigits: decimals || 2
                });
            }

            // Toggle button function for expanding/collapsing detail rows
            function toggleButton(button) {
                const no_bukti = $(button).data('no_bukti');

                if (button.innerText === '+') {
                    button.innerText = '-';
                    button.classList.remove('btn-success');
                    button.classList.add('btn-danger');

                    // Fetch and show detail data
                    $.ajax({
                        url: "{{ route('ubbrgdw.browse_detail') }}",
                        method: 'GET',
                        data: {
                            no_bukti: no_bukti
                        },
                        success: function(response) {
                            let totalQty = 0;
                            let totalHargaLama = 0;
                            let totalHargaBaru = 0;
                            let totalGrandTotal = 0;

                            let detailHtml = `
                                <div class="p-3">
                                    <table class="table table-bordered table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No.</th>
                                                <th>Kode Barang</th>
                                                <th>Nama Barang</th>
                                                <th>Qty</th>
                                                <th>Harga Lama</th>
                                                <th>Harga Baru</th>
                                                <th>Disk Lama</th>
                                                <th>Disk Baru</th>
                                                <th>Disk2 Lama</th>
                                                <th>Disk2 Baru</th>
                                                <th>Disk3 Lama</th>
                                                <th>Disk3 Baru</th>
                                                <th>Disk4 Lama</th>
                                                <th>Disk4 Baru</th>
                                                <th>Total</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;

                            if (response.data && response.data.length > 0) {
                                response.data.forEach((item, index) => {
                                    totalQty += parseFloat(item.QTY || 0);
                                    totalHargaLama += parseFloat(item.HARGALAMA || 0);
                                    totalHargaBaru += parseFloat(item.HARGA || 0);
                                    totalGrandTotal += parseFloat(item.TOTAL || 0);

                                    detailHtml += `
                                        <tr>
                                            <td><div style="background-color: #f7d8b4; padding: 0.5rem;">${index + 1}</div></td>
                                            <td><div style="background-color: #f7d8b4; padding: 0.5rem;">${item.KD_BRG || ''}</div></td>
                                            <td><div style="background-color: #f7d8b4; padding: 0.5rem;">${item.NA_BRG || ''}</div></td>
                                            <td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.QTY || 0, 2)}</div></td>
                                            <td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.HARGALAMA || 0, 2)}</div></td>
                                            <td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.HARGA || 0, 2)}</div></td>
                                            <td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.DISKLAMA || 0, 2)}</div></td>
                                            <td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.DISK || 0, 2)}</div></td>
                                            <td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.DISKLAMA2 || 0, 2)}</div></td>
                                            <td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.DISK2 || 0, 2)}</div></td>
                                            <td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.DISKLAMA3 || 0, 2)}</div></td>
                                            <td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.DISK3 || 0, 2)}</div></td>
                                            <td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.DISKLAMA4 || 0, 2)}</div></td>
                                            <td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.DISK4 || 0, 2)}</div></td>
                                            <td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.TOTAL || 0, 2)}</div></td>
                                            <td><div style="background-color: #f7d8b4; padding: 0.5rem;">${item.KET || ''}</div></td>
                                        </tr>
                                    `;
                                });
                            } else {
                                detailHtml += `
                                    <tr>
                                        <td colspan="16" style="text-align: center;"><div style="background-color: #f7d8b4; padding: 0.5rem;">Tidak ada data detail perubahan harga</div></td>
                                    </tr>
                                `;
                            }

                            detailHtml += `

                                    </tbody>
                                </table>
                            </div>
                            `;

                            var detailRow = `<tr class="detail-row"><td colspan="10">${detailHtml}</td></tr>`;
                            $(button).closest('tr').after(detailRow);
                        },
                        error: function() {
                            var errorRow =
                                `<tr class="detail-row"><td colspan="10"><div class="p-3 text-center text-danger"><strong>Error loading detail data</strong></div></td></tr>`;
                            $(button).closest('tr').after(errorRow);
                        }
                    });
                } else {
                    button.innerText = '+';
                    button.classList.remove('btn-danger');
                    button.classList.add('btn-success');
                    $(button).closest('tr').next('.detail-row').remove();
                }
            }

            function deleteRow(link) {
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

                        $.ajax({
                            url: link,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                Swal.fire('Deleted!', 'Data berhasil dihapus.', 'success');

                                location.reload();
                            },
                            error: function(err) {
                                Swal.fire('Error!', 'Gagal hapus data.', 'error');
                            }
                        });

                    }
                });
            }
        }

    </script>
@endsection
