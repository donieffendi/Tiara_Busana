@extends('layouts.plain')

@section('content')
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Laporan Penjualan BC</h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item active">Laporan Penjualan BC</li>
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
							<form method="POST" action="{{url('jasper-penjualan-report')}}">
								@csrf					
								<div class="form-group row">
									<div class="col-md-1" align="right">
										<label><strong>Cabang :</strong></label>
									</div>
									<div class="col-md-2">
										<select name="cbg" id="cbg" class="form-control cbg" style="width: 200px">
											<option value="">--Pilih Cabang--</option>
											@foreach($cbg as $cbgD)
												<option value="{{$cbgD->KODE}}"  {{ (session()->get('filter_cbg') == $cbgD->KODE) ? 'selected' : '' }}>{{$cbgD->KODE}}</option>
											@endforeach
										</select>
									</div>

									<div class="col-md-1" align="right">
										<label class="form-label">Conter</label>
									</div>
									<div class="col-md-2">
										<input type="text" class="form-control CNT" id="CNT" name="CNT" placeholder="Pilih Conter" value="{{ session()->get('filter_CNT') }}" readonly>
									</div>  
									<div class="col-md-4" align="right">
										<input type="text" class="form-control NA_CNT" id="NA_CNT" name="NA_CNT" placeholder="Nama Conter" value="{{ session()->get('filter_NA_CNT') }}" readonly>
									</div>
								</div>

								<div class="form-group row">
									<div class="col-md-1" align="right">
										<label><strong>Periode :</strong></label>
									</div>
									<div class="col-md-2">
										<select name="per" id="per" class="form-control per" style="width: 200px">
											<option value="">--Pilih Periode--</option>
											@foreach($per as $perD)
												<option value="{{$perD->PERIO}}"  {{ (session()->get('filter_periode') == $perD->PERIO) ? 'selected' : '' }}>{{$perD->PERIO}}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row">
									<div class="col-md-1" align="right">
										<label><strong>Tanggal :</strong></label>
									</div>
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

						{{-- <button class="btn btn-danger" type="button" id="resetfilter" class="resetfilter" onclick="window.location='{{url("rpenjualan")}}'">Reset</button> --}}
							{{-- </form> --}}
							<div style="margin-bottom: 15px;"></div>
							
							<!---- INI UNTUK TAB ---->
							<ul class="nav nav-tabs" id="myTab" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" data-toggle="tab" href="#tab1">Per Item</a>
								</li>

								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#tab2">Per Tgl</a>
								</li>

								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#tab3">Per Conter</a>
								</li>
							</ul>
							
							<!-- PASTE DIBAWAH INI -->
							<!-- DISINI BATAS AWAL KOOLREPORT-->
							<div class="tab-content" style="margin-top:15px">

								<div class="tab-pane fade show active" id="tab1">

									<div style="margin-bottom:10px;">
										<button type="submit" name="proses" value="1" class="btn btn-dark">
											PROSES
										</button>
										<button class="btn btn-danger" type="button"
											onclick="window.location='{{url("rpenjualan")}}'">
											Reset
										</button>
									</div>
									</form> 
									<div class="report-content" style="max-width:100%; overflow-x:scroll;">
										<?php
										use \koolreport\datagrid\DataTables;

										if($hasil)
										{
											DataTables::create(array(
												"dataSource" => $hasil,
												"name" => "example",
												"htmlAttributes" => [
													"id" => "tableReport"
												],
												"fastRender" => true,
												"fixedHeader" => true,
												'scrollX' => true,
												"showFooter" => true,
												"showFooter" => "bottom",
												"columns" => array(
													"no_bukti" => array(
														"label" => "Kitir SPM",
													),
													"bukti2" => array(
														"label" => "Kitir BC",
													),
													"initanggal" => array(
														"label" => "Tanggal",
														"type" => "date",
														"format" => "Y-m-d",
														"displayFormat" => "d-m-Y",
													),
													"KD_BRG" => array(
														"label" => "Kode Barang",
													),
													"NA_BRG" => array(
														"label" => "Nama Barang",
													),
													"qty" => array(
														"label" => "Qty",
														"type" => "number",
														"decimals" => 2,
														"decimalPoint" => ".",
														"thousandSeparator" => ",",
													),
													"harga" => array(
														"label" => "Harga",
														"type" => "number",
														"decimals" => 2,
														"decimalPoint" => ".",
														"thousandSeparator" => ",",
													),
													"total" => array(
														"label" => "Total",
														"type" => "number",
														"decimals" => 2,
														"decimalPoint" => ".",
														"thousandSeparator" => ",",
													),
													"cabang" => array(
														"label" => "Cabang",
													),
													"KSR" => array(
														"label" => "Kasir",
													),
													"usrnm" => array(
														"label" => "Petugas Kasir",
													),
													"posted" => array(
														"label" => "Post Status",
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
															"targets" => [5,6,7],
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
								</div>
								<div class="tab-pane fade show active" id="tab2">

									<div style="margin-bottom:10px;">
										<button type="submit" name="tampil" value="1" class="btn btn-dark">
											Tampilkan
										</button>
									</div>
									<div class="report-content" style="max-width:100%; overflow-x:scroll;">
										<?php
										use \koolreport\datagrid\DataTables;

										if($hasil)
										{
											DataTables::create(array(
												"dataSource" => $hasil,
												"name" => "example",
												"htmlAttributes" => [
													"id" => "tableReport"
												],
												"fastRender" => true,
												"fixedHeader" => true,
												'scrollX' => true,
												"showFooter" => true,
												"showFooter" => "bottom",
												"columns" => array(
													"CNT" => array(
														"label" => "CNT",
													),
													"NA_CNT" => array(
														"label" => "Nama Counter",
													),
													"st_pjk" => array(
														"label" => "S. Pajak",
													),
													"tgl_jual" => array(
														"label" => "Tanggal",
														"type" => "date",
														"format" => "Y-m-d",
														"displayFormat" => "d-m-Y",
													),
													"KD_BRG" => array(
														"label" => "Kode Barang",
													),
													"NA_BRG" => array(
														"label" => "Nama Barang",
													),
													"qty" => array(
														"label" => "Qty",
														"type" => "number",
														"decimals" => 2,
														"decimalPoint" => ".",
														"thousandSeparator" => ",",
													),
													"harga" => array(
														"label" => "Harga",
														"type" => "number",
														"decimals" => 2,
														"decimalPoint" => ".",
														"thousandSeparator" => ",",
													),
													"total" => array(
														"label" => "Total",
														"type" => "number",
														"decimals" => 2,
														"decimalPoint" => ".",
														"thousandSeparator" => ",",
													),
													"cabang" => array(
														"label" => "Cabang",
													),
													"KSR" => array(
														"label" => "Kasir",
													),
													"usrnm" => array(
														"label" => "Petugas Kasir",
													),
													"posted" => array(
														"label" => "Post Status",
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
															"targets" => [5,6,7],
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
								</div>
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


<div class="modal fade" id="browseCust2Modal" tabindex="-1" role="dialog" aria-labelledby="browseCust2ModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="browseCust2ModalLabel">Cari Customer 2</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-cust2">
				<thead>
					<tr>
						<th>Customer</th>
						<th>Nama</th>
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
	var tableReport;
	$(document).ready(function() {
		$('.date').datepicker({  
			dateFormat: 'dd-mm-yy'
		}); 

		/////////////////////////
		tableReport = $('#tableReport').DataTable();
		////////////////////////	
		/// AGAR TIDAK RUSAK SAAT PINDAH TAB ///
		$('a[data-toggle="tab"]').on('shown.bs.tab', function () {
			$.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
		});

		////////////////////////

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

		////////////////////////

		var dTableCounter;
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
							'<a href="javascript:void(0);" onclick="chooseCounter(\''+resp[i].CNT+'\', \''+resp[i].NA_CNT+'\')">'+resp[i].CNT+'</a>',
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
		
		chooseCounter = function(CNT, NA_CNT){
				$("#CNT").val(CNT);
				$("#NA_CNT").val(NA_CNT);	
				$("#browseCounterModal").modal("hide");
			}
		
		$("#CNT").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseCounter();
			}
		});
		////////////////////////////////
	});
</script>
@endsection
