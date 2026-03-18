@extends('layouts.plain')

@section('content')
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Laporan Konsinyasi</h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item active">Laporan Konsinyasi</li>
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
					<form method="POST" action="{{url('jasper-konsinyasi-report')}}">
					@csrf
					<div class="form-group row">
						<div class="col-md-2">						
							<label class="form-label">Counter</label>
							<input type="text" class="form-control cnt" id="cnt" name="cnt" placeholder="Pilih Counter" value="{{ session()->get('filter_cnt') }}" readonly>
						</div>  
						<div class="col-md-3">
							<label class="form-label">Nama</label>
							<input type="text" class="form-control ncnt" id="ncnt" name="ncnt" placeholder="Nama" value="{{ session()->get('filter_ncnt') }}" readonly>
						</div>
					</div>
					
					<div class="form-group row">
						<div class="col-md-2">
							<label><strong>Periode :</strong></label>
							<select name="per" id="per" class="form-control per" style="width: 200px">
								<option value="">--Pilih Periode--</option>
								@foreach($per as $perD)
									<option value="{{$perD->PERIO}}"  {{ (session()->get('filter_periode') == $perD->PERIO) ? 'selected' : '' }}>{{$perD->PERIO}}</option>
								@endforeach
							</select>
						</div>

						<div class="col-md-1">
							<input type="hidden" name="rekap" value="0">
							<input type="checkbox" class="form-check-input" id="rekap" name="rekap" value="1" {{ session()->get('filter_rekap',1) == 1 ? 'checked' : '' }}>
							<label class="form-check-label" id="label_rekap" for="rekap"><strong>Rekap Konsesi</strong></label>
						</div>
					</div>
						
                    <button class="btn btn-primary" type="submit" id="filter" class="filter" name="filter">Filter</button>
                    <button class="btn btn-danger" type="button" id="resetfilter" class="resetfilter" onclick="window.location='{{url("rkonsinyasi")}}'">Reset</button>
					<button class="btn btn-warning" type="submit" id="cetak" class="cetak" formtarget="_blank">Cetak</button>
					</form>
					<div style="margin-bottom: 15px;"></div>
					<!--
					<table class="table table-fixed table-striped table-border table-hover nowrap datatable">
						<thead class="table-dark">
							<tr>
								<th scope="col" style="text-align: center">#</th>
								<th scope="col" style="text-align: left">Bukti#</th>
								<th scope="col" style="text-align: left">Tgl</th>
								<th scope="col" style="text-align: left">SO#</th>
								<th scope="col" style="text-align: left">Customer#</th>
								<th scope="col" style="text-align: left">-</th>
								<th scope="col" style="text-align: right">Total</th>
								<th scope="col" style="text-align: notes">Notes</th>
							</tr>
						</thead>
						<tbody>
						</tbody> 
					</table> -->
					
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
                                        "label" => "cnt#",
                                    ),
                                    "conter" => array(
                                        "label" => "Counter",
                                    ),
                                    "per" => array(
                                        "label" => "Periode",
                                    ),
                                    "nett" => array(
                                        "label" => "Nett",
                                        "type" => "number",
                                        "decimals" => 2,
                                        "decimalPoint" => ".",
                                        "thousandSeparator" => ",",
                                        "footer" => "sum",
                                        "footerText" => "<b>@value</b>",
                                    ),
                                    "tgl_min" => array(
                                        "label" => "Tanggal Min",
										"type" => "date",
										"format" => "Y-m-d",
										"displayFormat" => "d-m-Y",
                                    ),
									"tgl_max" => array(
										"label" => "Tanggal Max",
										"type" => "date",
										"format" => "Y-m-d",
										"displayFormat" => "d-m-Y",
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
                                            "targets" => [3],
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
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="browseCounterModalLabel">Cari Counteromer</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-counter">
				<thead>
					<tr>
						<th>Counter</th>
						<th>Nama Counter</th>
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
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> -->
<script src="{{asset('foxie_js_css/bootstrap.bundle.min.js')}}"></script>
<script>
	$(document).ready(function() {
		
		$('.date').datepicker({  
			dateFormat: 'dd-mm-yy'
		});
		
		var dTableCounter;
		loadDataCounter = function(){
		
			$.ajax(
			{
				type: 'GET', 		
				url: "{{url('kirim/browse_cnt')}}",

				success: function( response )

				{
					resp = response;
					if(dTableCounter){
						dTableCounter.clear();
					}
					for(i=0; i<resp.length; i++){
							
						dTableCounter.row.add([
							'<a href="javascript:void(0);" onclick="chooseCounter(\''+resp[i].CNT+'\', \''+resp[i].NCNT+'\')">'+resp[i].CNT+'</a>',
							resp[i].NCNT
						]);
					}
					dTableCounter.draw();
				}
			});
		}
		
		dTableCounter = $("#table-counter").DataTable({
			
		});
		
		browseCounter = function(){
			loadDataCounter();
			$("#browseCounterModal").modal("show");
		}
		
		chooseCounter = function(CNT, NCNT){
			$("#cnt").val(CNT);
			$("#ncnt").val(NCNT);	
			$("#browseCounterModal").modal("hide");
		}
		
		$("#cnt").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseCounter();
			}
		});
	});

	function updateLabelRekap(){
		if($("#rekap").is(":checked")){
			$("#label_rekap").html("<strong>Rekap Konsesi</strong>");
		}else{
			$("#label_rekap").html("<strong>Kwitansi</strong>");
		}
	}

	$("#rekap").change(function(){
		updateLabelRekap();
	});

	// jalankan saat halaman dibuka
	updateLabelRekap();
	
	

</script>
@endsection
