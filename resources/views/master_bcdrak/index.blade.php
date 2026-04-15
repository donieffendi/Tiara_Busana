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

                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-md-1" align="right">
                                            <label>Filter</label>
                                        </div>
                                        <div class="col-md-2">
                                            <select id="TYPE" class="form-control"  name="TYPE">
                                            <option value="" disabled selected hidden>--Pilih Tipe--</option>
                                                <option value="PerSub">Kode Barang</option>
                                                <option value="PerSupp">Per Counter</option>
                                            </select>
                                        </div>

                                        <div class="col-md-2" id="filterSub" style="display:none;">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <input type="text" id="sub1" class="form-control" placeholder="Kode Barang">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2" id="filterSupp" style="display:none;">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <input type="text" id="supp1" class="form-control" placeholder="Per Counter">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <button id="btnTampil" class="btn btn-primary">
                                                <i class="fas fa-search"></i> Tampil
                                            </button>

                                            <button id="btnBarcode" class="btn btn-danger">
                                                <i class="fas fa-recycle"></i> Proses
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
                                            <th scope="col" style="text-align: center">Kode Barang</th>
                                            <th scope="col" style="text-align: center">Barcode</th>
                                            <th scope="col" style="text-align: center">Nama Barang</th>
                                            <th scope="col" style="text-align: center">Kode Conter</th>
                                            <th scope="col" style="text-align: center">Nama Conter</th>
                                            <th scope="col" style="text-align: center">Harga</th>
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

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <script>
        $(document).ready(function() {

            $('#filterSub').hide();
            $('#filterSupp').hide();

            var dataTable = $('.datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                scrollY: '400px',
                deferLoading: 0,
                order: [
                    [0, "asc"]
                ],
                ajax: {
                    url: '{{ route('get-bcdrak') }}',
                    data: function(d) {
                        d.sub1 = $('#sub1').val();
                        d.supp1 = $('#supp1').val();
                    }
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
                        data: 'KD_BRG',
                        name: 'KD_BRG'
                    },
                    {
                        data: 'BARCODE',
                        name: 'BARCODE'
                    },
                    {
                        data: 'NA_BRG',
                        name: 'NA_BRG'
                    },
                    {
                        data: 'CNT',
                        name: 'CNT',
                        render: function(data) {
                            return '<span class="badge badge-pill badge-warning">' + data +
                                '</span>';
                        }
                    },
                    {
                        data: 'NCNT',
                        name: 'NCNT'
                    },
                    {data: 'HJUAL', name: 'HJUAL', render: $.fn.dataTable.render.number( ',', '.', 0, '' )},
                ],
                columnDefs: [
                    {
                        "className": "dt-center",
                        "targets": 0
                    },
                    {
                        "className": "dt-right",
                        "targets": [7]
                    }
                ],
                dom: "<'row'<'col-md-6'><'col-md-6'>>" +
                    "<'row'<'col-md-2'l><'col-md-6 test_btn m-auto'><'col-md-4'f>>" +
                    "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
                stateSave: false
            });

            $(document).on('click', '#btnTampil', function () {
				dataTable.ajax.reload();
			});

            $('#btnBarcode').on('click', function() {
                let sub1 = $('#sub1').val();
                let supp1 = $('#supp1').val();

                if (sub1 == '' && supp1 == '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'Anda belum mengisi filter!',
                    });
                    return;
                }

                Swal.fire({
                    title: 'Cetak Barcode',
                    html: `
                        <p>Masukkan jumlah cetak per barcode:</p>
                        <input type="number" id="qtyCetak" class="swal2-input" placeholder="Jumlah" value="1" min="1">
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Cetak',
                    cancelButtonText: 'Batal',
                    preConfirm: () => {
                        let qty = document.getElementById('qtyCetak').value;
                        if (!qty || qty <= 0) {
                            Swal.showValidationMessage('Jumlah harus lebih dari 0');
                        }
                        return qty;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        let qty = result.value;

                        window.open(`{{ url('bcdrak/barcode') }}?sub1=${sub1}&supp1=${supp1}&qty=${qty}`, '_blank');
                    }
                });
            });

            $('#TYPE').on('change', function(){

                let type = $(this).val();

                // reset input
                $('#sub1').val('');
                $('#supp1').val('');

                // hide semua dulu
                $('#filterSub').hide();
                $('#filterSupp').hide();

                if(type === 'PerSub'){
                    $('#filterSub').show();
                }
                else if(type === 'PerSupp'){
                    $('#filterSupp').show();
                }

            });

            // $('#TYPE').trigger('change');
        });
    </script>
@endsection
