@extends('layouts.plain')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Cetak Barcode</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">Cetak Barcode</li>
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
                                <form method="POST" action="{{ url('jasper-cetakbcd-report') }}">
                                    @csrf
                                    <div class="form-group row">
                                        <div class="col-md-2">
                                            <label><strong>Filter :</strong></label>
                                            <select name="filter" id="filter" class="form-control filter" style="width: 200px">
                                                <option value="" selected disabled>--Pilih Filter--</option>
                                                <option value="KODE">KODE</option>
                                                <option value="BUKTI">BUKTI</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <div class="col-md-2">
                                            <label class="form-label">Kode / Bukti</label>
                                            <input type="text" class="form-control kd" id="kd" name="kd"
                                                placeholder="" value="{{ session()->get('filter_kd') }}">
                                        </div>

                                        <div class="col-md-1">
                                            <label class="form-label">Jumlah</label>
                                            <input type="text" class="form-control jm" id="jm" name="jm"
                                                placeholder="" value="{{ session()->get('filter_jm') }}">
                                        </div>
                                    </div>

                                    {{-- <button class="btn btn-primary" type="submit" id="filter" class="filter" name="filter">Filter</button> --}}
                                    <button class="btn btn-danger" type="button" id="resetfilter" class="resetfilter" onclick="window.location='{{ url('rcetakbcd') }}'">Reset</button>
                                    <button class="btn btn-warning" type="submit" id="cetak" class="cetak" formtarget="_blank">Cetak</button>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <script>
        $(document).ready(function() {

            $('.date').datepicker({
                dateFormat: 'dd-mm-yy'
            });
            /*
            function fill_datatable( kodes = '' ,  gol='', tglDr = '', tglSmp = '' )
            {
            	var dataTable = $('.datatable').DataTable({
            		dom: '<"row"<"col-4"B>>fltip',
            		lengthMenu: [
            			[ 10, 25, 50, -1 ],
            			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
            		],
            		processing: true,
            		serverSide: true,
            		autoWidth: true,
            		'scrollX': true,
            		'scrollY': '400px',
            		"order": [[ 0, "asc" ]],
            		ajax:
            		{
            			url: "{{ route('get-po-report') }}",
            			data: {
            				kodes: kodes,
            				gol: gol,
            				tglDr: tglDr,
            				tglSmp: tglSmp,
            			}
            		},
            		columns:
            		[
            			{data: 'DT_RowIndex', orderable: false, searchable: false },
            			{data: 'NO_BUKTI', name: 'NO_BUKTI'},
            			{data: 'TGL', name: 'TGL'},
            			{data: 'KODES', name: 'KODES'},
            			{data: 'NAMAS', name: 'NAMAS'},
            			{data: 'KD_BHN', name: 'KD_BHN'},
            			{data: 'NA_BHN', name: 'NA_BHN'},
            			{
            				data: 'QTY',
            				name: 'QTY',
            				render: $.fn.dataTable.render.number( ',', '.', 0, '' )
            			},
            			{
            				data: 'HARGA',
            				name: 'HARGA',
            				render: $.fn.dataTable.render.number( ',', '.', 0, '' )
            			},
            			{
            				data: 'TOTAL',
            				name: 'TOTAL',
            				render: $.fn.dataTable.render.number( ',', '.', 0, '' )
            			}
            		],

            		columnDefs:
            		[
            			{
            			"className": "dt-center",
            			"targets": 0
            			},
            			{
            			targets: 2,
            			render: $.fn.dataTable.render.moment( 'DD-MM-YYYY' )
            			},
            			{
            			"className": "dt-right",
            			"targets": [7,8,9]
            			},
            		],

            		footerCallback: function (row, data, start, end, display) {
            				var api = this.api();

            				// Remove the formatting to get integer data for summation
            				var intVal = function (i) {
            					return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            				};

            				// Total over all pages
            				totalQty = api
            					.column(7)
            					.data()
            					.reduce(function (a, b) {
            						return intVal(a) + intVal(b);
            					}, 0);

            				// Total over this page
            				pageQtyTotal = api
            					.column(7, { page: 'current' })
            					.data()
            					.reduce(function (a, b) {
            						return intVal(a) + intVal(b);
            					}, 0);
            				pageSubTotal = api
            					.column(9, { page: 'current' })
            					.data()
            					.reduce(function (a, b) {
            						return intVal(a) + intVal(b);
            					}, 0);

            				// Update footer
            				$(api.column(7).footer()).html(pageQtyTotal.toLocaleString('en-US'));
            				$(api.column(9).footer()).html(pageSubTotal.toLocaleString('en-US'));
            			},

            	});
            }

            $('#filter').click(function() {
            	var kodes = $('#kodes').val();
            	var gol = $('#gol').val();
            	var tglDr = $('#tglDr').val();
            	var tglSmp = $('#tglSmp').val();

            	if (kodes != '' || (tglDr != '' && tglSmp != ''))
            	{
            		$('.datatable').DataTable().destroy();
            		fill_datatable(kodes, gol, tglDr, tglSmp);
            	}
            });

            $('#resetfilter').click(function() {
            	var kodes = '';
            	var gol = '';
            	var tglDr = '';
            	var tglSmp = '';

            	$('.datatable').DataTable().destroy();
            	fill_datatable(kodes, gol, tglDr, tglSmp);
            });
            */
        });

        var dTableBSuplier;
        loadDataBSuplier = function() {

            $.ajax({
                type: 'GET',
                url: "{{ url('sup/browse') }}",
                data: {
                    'GOL': $('#gol').val(),
                },
                success: function(response) {
                    resp = response;
                    if (dTableBSuplier) {
                        dTableBSuplier.clear();
                    }
                    for (i = 0; i < resp.length; i++) {

                        dTableBSuplier.row.add([
                            '<a href="javascript:void(0);" onclick="chooseSuplier(\'' + resp[i]
                            .KODES + '\')">' + resp[i].KODES + '</a>',
                            resp[i].NAMAS,
                            resp[i].ALAMAT,
                            resp[i].KOTA,
                        ]);
                    }
                    dTableBSuplier.draw();
                }
            });
        }

        dTableBSuplier = $("#table-bsuplier").DataTable({

        });

        browseSuplier = function() {
            loadDataBSuplier();
            $("#browseSuplierModal").modal("show");
        }

        chooseSuplier = function(KODES) {
            $("#kodes").val(KODES);
            // $("#NAMAS").val(NAMAS);
            $("#browseSuplierModal").modal("hide");
        }

        $("#kodes").keypress(function(e) {
            if (e.keyCode == 46) {
                e.preventDefault();
                browseSuplier();
            }
        });

        //////////////////////////////////////////////////////////////////////

        var dTableBSuplier2;
        loadDataBSuplier2 = function() {

            $.ajax({
                type: 'GET',
                url: "{{ url('sup/browse') }}",
                data: {
                    'GOL': $('#gol').val(),
                },
                success: function(response) {
                    resp = response;
                    if (dTableBSuplier2) {
                        dTableBSuplier2.clear();
                    }
                    for (i = 0; i < resp.length; i++) {

                        dTableBSuplier2.row.add([
                            '<a href="javascript:void(0);" onclick="chooseSuplier2(\'' + resp[i]
                            .KODES + '\')">' + resp[i].KODES + '</a>',
                            resp[i].NAMAS,
                            resp[i].ALAMAT,
                            resp[i].KOTA,
                        ]);
                    }
                    dTableBSuplier2.draw();
                }
            });
        }

        dTableBSuplier2 = $("#table-bsuplier2").DataTable({

        });

        browseSuplier2 = function() {
            loadDataBSuplier2();
            $("#browseSuplier2Modal").modal("show");
        }

        chooseSuplier2 = function(KODES) {
            $("#kodes2").val(KODES);
            // $("#NAMAS").val(NAMAS);
            $("#browseSuplier2Modal").modal("hide");
        }

        $("#kodes2").keypress(function(e) {
            if (e.keyCode == 46) {
                e.preventDefault();
                browseSuplier2();
            }
        });

        //////////////////////////////////////////////////////////////////////

        var dTableBBarang;
        loadDataBBarang = function() {
            $.ajax({
                type: 'GET',
                url: "{{ url('brg/browse') }}",
                data: {
                    'GOL': $('#gol').val(),
                },
                success: function(response) {
                    resp = response;
                    if (dTableBBarang) {
                        dTableBBarang.clear();
                    }
                    for (i = 0; i < resp.length; i++) {

                        dTableBBarang.row.add([
                            '<a href="javascript:void(0);" onclick="chooseBarang(\'' + resp[i]
                            .KD_BRG + '\',  \'' + resp[i].NA_BRG + '\',   \'' + resp[i].SATUAN +
                            '\')">' + resp[i].KD_BRG + '</a>',
                            resp[i].NA_BRG,
                            resp[i].SATUAN,
                        ]);

                    }
                    dTableBBarang.draw();
                }
            });
        }

        dTableBBarang = $("#table-bbarang").DataTable({

        });

        browseBarang = function() {
            loadDataBBarang();
            $("#browseBarangModal").modal("show");
        }

        chooseBarang = function(KD_BRG, NA_BRG) {
            $("#brg1").val(KD_BRG);
            $("#nabrg1").val(NA_BRG);
            $("#browseBarangModal").modal("hide");
        }


        $("#brg1").keypress(function(e) {
            if (e.keyCode == 46) {
                e.preventDefault();
                browseBarang();
            }
        });

        //////////////////////////////////////////////
    </script>
@endsection
