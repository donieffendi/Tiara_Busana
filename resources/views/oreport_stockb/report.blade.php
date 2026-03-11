@extends('layouts.plain')

@section('content')
<div class="content-wrapper">
	<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0">Laporan Diskon Per Counter</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
			<li class="breadcrumb-item active">Laporan Diskon Per Counter</li>
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
					<form method="POST" action="{{url('jasper-stockb-report')}}">
					@csrf
					<div class="form-group row" hidden>
						<div class="col-md-3">
							<input class="form-control date tglDr" id="tglDr" name="tglDr"
							type="text" autocomplete="off" value="{{ session()->get('filter_tglDari') }}"> 
						</div>
						<div>s.d.</div> 
						<div class="col-md-3">
							<input class="form-control date tglSmp" id="tglSmp" name="tglSmp"
							type="text" autocomplete="off" value="{{ session()->get('filter_tglSampai') }}">
						</div>
					</div>
					
					<div class="form-group row">
						<div class="col-md-1">
							<label><strong>Periode</strong></label>
							<select name="per" id="per" class="form-control per" style="width: 200px">
								<option value="">--Pilih Periode--</option>
								@foreach($per as $perD)
									<option value="{{$perD->PERIO}}"  {{ (session()->get('filter_periode') == $perD->PERIO) ? 'selected' : '' }}>{{$perD->PERIO}}</option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-2">						
							<label class="form-label">Counter</label>
							<input type="text" class="form-control CNT1" id="CNT1" name="CNT1" placeholder="Pilih Customer" value="{{ session()->get('filter_CNT1') }}" readonly>
						</div>  
						<div class="col-md-2">						
							<label class="form-label">s/d</label>
							<input type="text" class="form-control CNT2" id="CNT2" name="CNT2" placeholder="Pilih Customer" value="{{ session()->get('filter_CNT2') }}" readonly>
						</div>

						<div class="col-md-1">
							<input type="hidden" name="semua" value="0">
							<input type="checkbox" class="form-check-input" id="semua" name="semua" value="1" {{ session()->get('filter_semua',1) == 1 ? 'checked' : '' }}>
							<label class="form-check-label" id="label_semua" for="semua"><strong>semua</strong></label>
						</div>
					</div>
					
                    <button class="btn btn-primary" type="submit" id="filter" class="filter" name="filter">Filter</button>
                    <button class="btn btn-danger" type="button" id="resetfilter" class="resetfilter" onclick="window.location='{{url("rstockb")}}'">Reset</button>
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
                                    "cnt" => array(
                                        "label" => "Counter",
                                    ),
                                    "ncnt" => array(
                                        "label" => "Nama Counter",
                                    ),
                                    "kd_brg" => array(
                                        "label" => "Kode",
                                    ),
                                    "na_brg" => array(
                                        "label" => "Nama",
                                    ),
                                    "tgl" => array(
                                        "label" => "Tanggal",
										"type" => "datetime",
										"format" => "Y-m-d",
										"displayFormat" => "d-m-Y",
										"footerText" => "<b>Grand Total :</b>",
                                    ),
									"qtyx" => array(
											"label" => "Qty",
											"type" => "number",
											"decimals" => 2,
											"decimalPoint" => ".",
											"thousandSeparator" => ",",
											"footer" => "sum",
											"footerText" => "<b>@value</b>",
									),
									"total_cash" => array(
											"label" => "Cash",
											"type" => "number",
											"decimals" => 2,
											"decimalPoint" => ".",
											"thousandSeparator" => ",",
											"footer" => "sum",
											"footerText" => "<b>@value</b>",
									),
									"dis" => array(
											"label" => "Diskon",
											"type" => "number",
											"decimals" => 2,
											"decimalPoint" => ".",
											"thousandSeparator" => ",",
											"footer" => "sum",
											"footerText" => "<b>@value</b>",
									),
									"par" => array(
											"label" => "Partsp",
											"type" => "number",
											"decimals" => 2,
											"decimalPoint" => ".",
											"thousandSeparator" => ",",
											"footer" => "sum",
											"footerText" => "<b>@value</b>",
									),
									"beban_cash" => array(
											"label" => "Dis Cash",
											"type" => "number",
											"decimals" => 2,
											"decimalPoint" => ".",
											"thousandSeparator" => ",",
											"footer" => "sum",
											"footerText" => "<b>@value</b>",
									),
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
                                            "targets" => [5,6,7,8,9],
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
<script>
	$(document).ready(function() {
		
		$('.date').datepicker({  
			dateFormat: 'dd-mm-yy'
		});

		////////////////////////////

		$("#per").change(function(){

			var per = $(this).val(); // contoh: 08/2025

			if(per != ""){
				var pecah = per.split("/");

				var bulan = pecah[0];
				var tahun = pecah[1];

				// tanggal awal
				var tglAwal = "01-" + bulan + "-" + tahun;

				// cari tanggal akhir bulan
				var lastDay = new Date(tahun, bulan, 0).getDate();

				var tglAkhir = lastDay + "-" + bulan + "-" + tahun;

				$("#tglDr").val(tglAwal);
				$("#tglSmp").val(tglAkhir);
			}

		});

		if($("#per").val() != ""){
			$("#per").trigger("change");
		}

		////////////////////////////////

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
	});

	function updateLabelSemua(){
		if($("#semua").is(":checked")){
			$("#label_semua").html("<strong>Semua</strong>");
		}else{
			$("#label_semua").html("<strong>Per Item</strong>");
		}
	}

	$("#semua").change(function(){
		updateLabelSemua();
	});

	// jalankan saat halaman dibuka
	updateLabelSemua();
</script>
@endsection
