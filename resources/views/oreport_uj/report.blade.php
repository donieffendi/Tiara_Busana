@extends('layouts.plain')

@section('content')
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Laporan Turun Harga </h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item active">Laporan Turun Harga </li>
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
					<form method="POST" action="{{url('jasper-uj-report')}}">
					@csrf
					<div class="form-group row">
						<div class="col-md-2">						
							<label class="form-label">Counter</label>
							<input type="text" class="form-control CNT1" id="CNT1" name="CNT1" placeholder="Pilih Customer" value="{{ session()->get('filter_CNT1') }}" readonly>
						</div>  
						<div class="col-md-2">						
							<label class="form-label">s/d</label>
							<input type="text" class="form-control CNT2" id="CNT2" name="CNT2" placeholder="Pilih Customer" value="{{ session()->get('filter_CNT2') }}" readonly>
						</div>
					</div>
						
                    <button class="btn btn-primary" type="submit" id="filter" class="filter" name="filter">Filter</button>
                    <button class="btn btn-danger" type="button" id="resetfilter" class="resetfilter" onclick="window.location='{{url("ruj")}}'">Reset</button>
					<button class="btn btn-warning" type="submit" id="cetak" class="cetak" formtarget="_blank">Cetak</button>
					</form>
					<div style="margin-bottom: 15px;"></div>
					
                    <!-- PASTE DIBAWAH INI -->
                    <!-- DISINI BATAS AWAL KOOLREPORT-->
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
                                    "CNT" => array(
                                        "label" => "Counter",
                                    ),
									"NCNT" => array(
                                        "label" => "Nama Counter",
                                    ),
									"NO_DTH" => array(
                                        "label" => "No. DTH",
                                    ),
                                    "BARCODE" => array(
                                        "label" => "Barcode",
                                    ),
                                    "KD_BRG" => array(
                                        "label" => "Kode",
                                    ),
                                    "NA_BRG" => array(
                                        "label" => "Nama Item",
                                    ),
									"TGL_JUAL" => array(
                                        "label" => "Tanggal Jual",
										"type" => "datetime",
										"format" => "Y-m-d",
										"displayFormat" => "d-m-Y",
                                    ),
									"TGL_TRM" => array(
                                        "label" => "Tanggal Terima",
										"type" => "datetime",
										"format" => "Y-m-d",
										"displayFormat" => "d-m-Y",
										"footerText" => "<b>Grand Total :</b>",
                                    ),
									"HJUAL" => array(
                                        "label" => "Harga",
                                        "type" => "number",
                                        "decimals" => 2,
                                        "decimalPoint" => ".",
                                        "thousandSeparator" => ",",
                                        "footer" => "sum",
                                        "footerText" => "<b>@value</b>",
                                    ),
									"AK" => array(
                                        "label" => "Stok",
                                        "type" => "number",
                                        "decimals" => 2,
                                        "decimalPoint" => ".",
                                        "thousandSeparator" => ",",
                                        "footer" => "sum",
                                        "footerText" => "<b>@value</b>",
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
                                            "targets" => [8,9],
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

<div class="modal fade" id="browseCounterModal" tabindex="-1" role="dialog" aria-labelledby="browseCounterModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="browseCounterModalLabel">Cari Counteromer</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table class="table table-stripped table-bordered" id="table-Counter">
					<thead>
						<tr>
							<th>Counter</th>
							<th>Nama Counter</th>
							<th>Sup</th>
							<th>Nama</th>
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
	
	});
	
	var dTableCounter;
	var targetCNT = "";
	loadDataCounter = function(){
	
		$.ajax(
		{
			type: 'GET', 		
			url: "{{url('counter/browse_th')}}",
			success: function( response )
			{
				resp = response;
				if(dTableCounter){
					dTableCounter.clear();
				}
				for(i=0; i<resp.length; i++){
					
					dTableCounter.row.add([
						'<a href="javascript:void(0);" onclick="chooseCounter(\''+resp[i].CNT+'\')">'+resp[i].CNT+'</a>',
						resp[i].NA_CNT,
						resp[i].SUP,
						resp[i].NAMAS,
					]);
				}
				dTableCounter.draw();
			}
		});
	}
	
	dTableCounter = $("#table-Counter").DataTable({
		
	});
	
	browseCounter = function(){
		loadDataCounter();
		$("#browseCounterModal").modal("show");
	}
	
	chooseCounter = function(CNT){
		$("#" + targetCNT).val(CNT);
		$("#browseCounterModal").modal("hide");
	}
	
	$("#CNT1").keypress(function(e){
		if(e.keyCode == 46){
			e.preventDefault();
			targetCNT = "CNT1";
			browseCounter();
		}
	});

	$("#CNT2").keypress(function(e){
		if(e.keyCode == 46){
			e.preventDefault();
			targetCNT = "CNT2";
			browseCounter();
		}
	});
</script>
@endsection
