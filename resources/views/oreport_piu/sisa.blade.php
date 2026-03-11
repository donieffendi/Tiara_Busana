@extends('layouts.plain')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Laporan Cetak Amplop</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Laporan Cetak Amplop</li>
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
                    <form method="POST" action="{{url('jasper-piusisa-report')}}">
                    @csrf						
						<div class="form-group row">
                            <div class="col-md-1" align="right">						
                                <label class="form-label">No. Supplier</label>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control KODES" id="KODES" name="KODES" placeholder="Pilih Customer" value="{{ session()->get('filter_kodes') }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-1" align="right">
                                <label class="form-label">Nama</label>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control NAMAS" id="NAMAS" name="NAMAS" placeholder="Nama" value="{{ session()->get('filter_namas') }}" readonly>
                            </div>
						</div>

                        <div class="form-group row">
                            <div class="col-md-1" align="right">
                                <label class="form-label">Alamat</label>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control ALAMAT" id="ALAMAT" name="ALAMAT" placeholder="Alamat" value="{{ session()->get('filter_alamat') }}" readonly>
                            </div>
						</div>

                        <div class="form-group row">
                            <div class="col-md-1" align="right">
                                <label class="form-label">Kota</label>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control KOTA" id="KOTA" name="KOTA" placeholder="Kota" value="{{ session()->get('filter_kota') }}" readonly>
                            </div>
						</div>

                        <div class="form-group row">
                            <div class="col-md-1" align="right">
                                <label class="form-label">Telp</label>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control TELP" id="TELP" name="TELP" placeholder="No. Telp" value="{{ session()->get('filter_telp') }}" readonly>
                            </div>
						</div>
                    
                    <button class="btn btn-primary" type="submit" id="filter" class="filter" name="filter">Filter</button>
                    <button class="btn btn-danger" type="button" id="resetfilter" class="resetfilter" onclick="window.location='{{url("rsisapiu")}}'">Reset</button>
					<button class="btn btn-warning" type="submit" id="cetak" class="cetak" formtarget="_blank">Cetak</button>
                    </form>
                    <div style="margin-bottom: 15px;"></div>
                    
                    <div class="report-content" col-md-12 style="max-width: 100%; overflow-x: scroll;">
                        <?php
                        use \koolreport\datagrid\DataTables;

                        if($hasil)
                        {
                            DataTables::create(array(
                                "dataSource" => $hasil,
                                "name" => "example",
                                "fastRender" => true,
                                "fixedHeader" => true,
                                'scrollX' => true,
                                "showFooter" => true,
                                "showFooter" => "bottom",
                                "columns" => array(
                                    "KODES" => array(
                                        "label" => "No. Supplier",
                                    ),
                                    "NAMAS" => array(
                                        "label" => "Nama Supplier",
                                    ),
                                    "ALAMAT" => array(
                                        "label" => "Alamat",
                                    ),
                                    "KOTA" => array(
                                        "label" => "Kota",
                                    ),
                                    "TELP" => array(
                                        "label" => "No. Telp",
                                    )
                                ),
                                "cssClass" => array(
                                    "table" => "table table-hover table-striped table-bordered compact",
                                    "th" => "label-title",
                                    "td" => "detail",
                                    "tf" => "footerCss"
                                ),
                                "options" => array(
                                    "columnDefs"=>array(
                                        array(
                                            "className" => "dt-right", 
                                            "targets" => [5],
                                        ),
                                    ),
                                    "order" => [],
                                    "paging" => true,
                                    // "pageLength" => 12,
                                    "searching" => true,
                                    "colReorder" => true,
                                    "select" => true,
                                    "dom" => 'Blfrtip', // B e dilangi
                                    // "dom" => '<"row"<col-md-6"B><"col-md-6"f>> <"row"<"col-md-12"t>><"row"<"col-md-12">>',
                                    "buttons" => array(
                                        array(
                                            "extend" => 'collection',
                                            "text" => 'Export',
                                            "buttons" => [
                                                'copy',
                                                'excel',
                                                'csv',
                                                'pdf',
                                                'print'
                                            ],
                                        ),
                                    ),
                                ),
                            ));
                        }
                        ?>
                    </div>
                    <!-- DISINI BATAS AKHIR KOOLREPORT-->

                </div>
            </div>
            </div>
        </div>
        </div>
    </div>
</div>

	<div class="modal fade" id="browseSupModal" tabindex="-1" role="dialog" aria-labelledby="browseSupModalLabel" aria-hidden="true">
	  	<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="browseSupModalLabel">Cari Supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-stripped table-bordered" id="table-Sup">
                        <thead>
                            <tr>
                                <th>No. Supplier</th>
                                <th>Nama Supplier</th>
                                <th>Alamat</th>
                                <th>Kota</th>
                                <th>Telp</th>
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
	
    <div class="modal fade" id="browseTujuanModal" tabindex="-1" role="dialog" aria-labelledby="browseTujanModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="browseSuplierModalLabel">Cari Tujuan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <table class="table table-stripped table-bordered" id="table-btujuan">
                <thead>
                    <tr>
                        <th>Tujuan</th>
                        <th>-</th>
                        <th>Alamat</th>
                        <th>Kota</th>
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
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        $('.date').datepicker({  
            dateFormat: 'dd-mm-yy'
        }); 
        /*
        function fill_datatable( kodec = '', tglDr = '', tglSmp = '', lebih30 = '')
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
                //'scrollX': true,
                'scrollY': '400px',
                "order": [[ 0, "asc" ]],
                ajax: 
                {
                    url: "{{ url('get-piu-sisa') }}",
                    data: {
                        kodec: kodec,
                        tglDr: tglDr,
                        tglSmp: tglSmp,
			            lebih30: lebih30,
                    }
                },
                columns: 
                [
                    {data: 'DT_RowIndex', orderable: false, searchable: false },
                    {data: 'NO_BUKTI', name: 'NO_BUKTI'},
                    {data: 'TGL', name: 'TGL'},
                    {data: 'KODEC', name: 'KODEC'},
                    {data: 'NAMAC', name: 'NAMAC'},
                    {
                     data: 'SISA', 
                     name: 'SISA',
                     render: $.fn.dataTable.render.number( ',', '.', 2, '' )
                    },
                 ],
                 
                 columnDefs: [
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
                    "targets": [5]
                  }
               
                 ],
                
                ///////////////////////////////////////////////////
                
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
         
                    // Remove the formatting to get integer data for summation
                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                    };
         
                    // Total over this page
                    pageDebetTotal = api
                        .column(5, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
         
                    // Update footer
                    $(api.column(5).footer()).html(pageDebetTotal.toLocaleString('en-US'));
                    //$(api.column(6).footer()).html(pageKreditTotal.toLocaleString('en-US'));
                },
                
                
            });
        }
        
        $('#filter').click(function() {
            var kodec = $('#kodec').val();
            var tglDr = $('#tglDr').val();
            var tglSmp = $('#tglSmp').val();
	        var lebih30 = (document.getElementById('lebih30').checked==true) ? 1 : 0;
            
            if (kodec != '' || (tglDr != '' && tglSmp != ''))
            {
                $('.datatable').DataTable().destroy();
                fill_datatable(kodec, tglDr, tglSmp, lebih30);
            }
        });

        $('#resetfilter').click(function() {
            var kodec = '';
            var tglDr = '';
            var tglSmp = '';
	        var lebih30 = 0;

            $('.datatable').DataTable().destroy();
            fill_datatable(kodec, tglDr, tglSmp, lebih30);
        });
            */

    });
    
		var dTableSup;
		loadDataSup = function(){
		
			$.ajax(
			{
				type: 'GET', 		
				url: "{{url('sup/browse_amplop')}}",
				success: function( response )
				{
					resp = response;
					if(dTableSup){
						dTableSup.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableSup.row.add([
							'<a href="javascript:void(0);" onclick="chooseSup(\''+resp[i].KODES+'\',  \''+resp[i].NAMAS+'\', \''+resp[i].ALAMAT+'\',  \''+resp[i].KOTA+'\',  \''+resp[i].TELP+'\')">'+resp[i].KODES+'</a>',
							resp[i].NAMAS,
							resp[i].ALAMAT,
							resp[i].KOTA,
							resp[i].TELP,
						]);
					}
					dTableSup.draw();
				}
			});
		}
		
		dTableSup = $("#table-Sup").DataTable({
			
		});
		
		browseSup = function(){
			loadDataSup();
			$("#browseSupModal").modal("show");
		}
		
		chooseSup = function(KODES, NAMAS, ALAMAT, KOTA, TELP){
			$("#KODES").val(KODES);
			$("#NAMAS").val(NAMAS);
            $("#ALAMAT").val(ALAMAT);
            $("#KOTA").val(KOTA);	
            $("#TELP").val(TELP);	
			$("#browseSupModal").modal("hide");
		}
		
		$("#KODES").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseSup();
			}
		});
		
		
        var dTableBTujuan;
        var rowidTujuan;
        loadDataBTujuan = function(){
            $.ajax(
            {
                type: 'GET',    
                url: "{{url('tujuan/browse')}}",
                data: {
                    'GOL': 'Z',
                },
                success: function(resp)
                {
                    if(dTableBTujuan){
                        dTableBTujuan.clear();
                    }
                    for(i=0; i<resp.length; i++){
                        
                        dTableBTujuan.row.add([
                            '<a href="javascript:void(0);" onclick="chooseTujuan(\''+resp[i].KODET+'\',  \''+resp[i].NAMAT+'\',   \''+resp[i].ALAMAT+'\', \''+resp[i].KOTA+'\' )">'+resp[i].KODET+'</a>',
                            resp[i].NAMAT,
                            resp[i].ALAMAT,
                            resp[i].KOTA,
                            
                        ]);
                    }
                    dTableBTujuan.draw();
                }
            });
        }
        
        dTableBTujuan = $("#table-btujuan").DataTable({
            
        });
        
        browseTujuan = function(){
            loadDataBTujuan();
            $("#browseTujuanModal").modal("show");
        }
        
        chooseTujuan = function(KODET,NAMAT,ALAMAT,KOTA){
            $("#kodet").val(KODET);
            $("#NAMAT").val(NAMAT);			
            $("#browseTujuanModal").modal("hide");
        }
        
        $("#kodet").keypress(function(e){
            if(e.keyCode == 46){
                e.preventDefault();
                browseTujuan();
            }
        });
</script>
@endsection
