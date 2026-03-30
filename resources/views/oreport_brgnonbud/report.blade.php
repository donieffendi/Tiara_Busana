@extends('layouts.plain')

@section('content')
<div class="content-wrapper">
	<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0">Laporan Data Barang Non Budget</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item active">Laporan Data Barang Non Budget</li>
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
					<form method="POST" action="{{url('jasper-brgnonbud-report')}}">
					@csrf
					<div class="form-group row">
						<div class="col-md-2">
							<label class="form-label">Periode</label>
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
							<label class="form-label">Dari Sub</label>
							<input type="text" class="form-control sub1" id="sub1" name="sub1" placeholder="Pilih Sub" value="{{ session()->get('filter_sub1') }}" readonly>
						</div>
						<div class="col-md-2">
							<label class="form-label">Sampai Sub</label>		
							<input type="text" class="form-control sub2" id="sub2" name="sub2" placeholder="Pilih Sub" value="{{ session()->get('filter_sub2') }}" readonly>
						</div>  
					</div>

					<div class="form-group row">
						<div class="col-md-2">
							<label class="form-label">Dari PLU</label>
							<input type="text" class="form-control kdbar1" id="kdbar1" name="kdbar1" placeholder="pilih No. Supplier" value="{{ session()->get('filter_kdbar1') }}" readonly>
						</div>
						<div class="col-md-2">	
							<label class="form-label">Sampai PLU</label>					
							<input type="text" class="form-control kdbar2" id="kdbar2" name="kdbar2" placeholder="Nama Supplier" value="{{ session()->get('filter_kdbar2') }}" readonly>
						</div>  
					</div>
					
					<button class="btn btn-primary" type="submit" id="filter" class="filter" name="filter">Filter</button>
					<button class="btn btn-danger" type="button" id="resetfilter" class="resetfilter" onclick="window.location='{{url("rbrgnonbud")}}'">Reset</button>
					<button class="btn btn-warning" type="submit" id="cetak" class="cetak" formtarget="_blank">Cetak</button>
					</form>
					<div style="margin-bottom: 15px;"></div>
					
				<!-- PASTE DIBAWAH INI -->
				<!-- DISINI BATAS AWAL KOOLREPORT-->
				<div class="report-content" col-md-12 style="max-width: 100%; overflow-x: scroll;">
					<?php
					use \koolreport\datagrid\DataTables;

					$data = $hasil ?? [];
					DataTables::create(array(
						"dataSource" => $hasil,
						"name" => "example",
						"fastRender" => true,
						"fixedHeader" => true,
						'scrollX' => true,
						"showFooter" => true,
						"showFooter" => "bottom",
						"columns" => array(
							"ROWNUM" => array(
								"label" => "No",
							),
							"SUB" => array(
								"label" => "Sub",
							),
							"KDBAR" => array(
								"label" => "P.L.U",
							),
							"NMBAR" => array(
								"label" => "Nama Barang",
							),
							"KET_UK" => array(
								"label" => "Ukuran",
							),
							"HB" => array(
								"label" => "Harga Beli",
								"type" => "number",
								"decimals" => 2,
								"decimalPoint" => ".",
								"thousandSeparator" => ",",
							),
							"DIS_A" => array(
								"label" => "Diskon 1",
								"type" => "number",
								"decimals" => 2,
								"decimalPoint" => ".",
								"thousandSeparator" => ",",
							),
							"DIS_B" => array(
								"label" => "Diskon 2",
								"type" => "number",
								"decimals" => 2,
								"decimalPoint" => ".",
								"thousandSeparator" => ",",
							),
							"DIS_C" => array(
								"label" => "Diskon 3",
								"type" => "number",
								"decimals" => 2,
								"decimalPoint" => ".",
								"thousandSeparator" => ",",
							),
							"SUPP" => array(
								"label" => "Suplier",
							),
							"CAT" => array(
								"label" => "Keterangan",
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
									"targets" => [5,6,7,8],
								),
								array(
									"className" => "dt-center", 
									"targets" => [0],
								),
							),
							"order" => [],
							"paging" => true,
							// "pageLength" => 12,
							"lengthMenu" => [[10, 25, 50,-1], [10,25,50, "All"]],
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
<div class="modal fade" id="browseBarangModal" tabindex="-1" role="dialog" aria-labelledby="browseBarangModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="browseBarangModalLabel">Cari Barang</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bbarang">
				<thead>
					<tr>
						<th>Kode Barang</th>
						<th>Nama Barang</th>
						<th>Sub</th>
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

<div class="modal fade" id="browseBarang2Modal" tabindex="-1" role="dialog" aria-labelledby="browseBarang2ModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="browseBarang2ModalLabel">Cari Barang2</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bbarang2">
				<thead>
					<tr>
						<th>Kode Barang</th>
						<th>Nama Barang</th>
						<th>Sub</th>
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

<div class="modal fade" id="browseSubModal" tabindex="-1" role="dialog" aria-labelledby="browseSubModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="browseSubModalLabel">Cari Sub</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bsub">
				<thead>
					<tr>
						<th>Sub</th>
						<th>Kelompok</th>
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

<div class="modal fade" id="browseSub2Modal" tabindex="-1" role="dialog" aria-labelledby="browseSub2ModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="browseSub2ModalLabel">Cari Sub</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bsub2">
				<thead>
					<tr>
						<th>Sub</th>
						<th>Kelompok</th>
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

		var dTableBBarang;
		loadDataBBarang = function(){
		
			$.ajax(
			{
				type: 'GET', 		
				url: "{{url('brg/browse_plu')}}",
				success: function( response )
				{
					resp = response;
					if(dTableBBarang){
						dTableBBarang.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBBarang.row.add([
							'<a href="javascript:void(0);" onclick="chooseBarang(\''+resp[i].KDBAR+'\')">'+resp[i].KDBAR+'</a>',
							resp[i].NMBAR,
							resp[i].SUB,
						]);
					}
					dTableBBarang.draw();
				}
			});
		}
		
		dTableBBarang = $("#table-bbarang").DataTable({
			
		});
		
		browseBarang = function(){
			loadDataBBarang();
			$("#browseBarangModal").modal("show");
		}
		
		chooseBarang = function(KDBAR){
			$("#kdbar1").val(KDBAR);
			$("#browseBarangModal").modal("hide");
		}
		
		$("#kdbar1").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseBarang();
			}
		}); 

		//////////////////////////////////////////////////////////////////////

		var dTableBBarang2;
		loadDataBBarang2 = function(){
		
			$.ajax(
			{
				type: 'GET', 		
				url: "{{url('brg/browse_plu')}}",
				success: function( response )
				{
					resp = response;
					if(dTableBBarang2){
						dTableBBarang2.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBBarang2.row.add([
							'<a href="javascript:void(0);" onclick="chooseBarang2(\''+resp[i].KDBAR+'\')">'+resp[i].KDBAR+'</a>',
							resp[i].NMBAR,
							resp[i].SUB,
						]);
					}
					dTableBBarang2.draw();
				}
			});
		}
		
		dTableBBarang2 = $("#table-bbarang2").DataTable({
			
		});
		
		browseBarang2 = function(){
			loadDataBBarang2();
			$("#browseBarang2Modal").modal("show");
		}
		
		chooseBarang2 = function(KDBAR){
			$("#kdbar2").val(KDBAR);	
			$("#browseBarang2Modal").modal("hide");
		}
		
		$("#kdbar2").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseBarang2();
			}
		}); 

		////////////////////////////////////////////////////////////////////////////

		var dTableBSub;
		loadDataBSub = function(){
		
			$.ajax(
			{
				type: 'GET', 		
				url: "{{url('brg/browse_sub')}}",
				success: function( response )
				{
					resp = response;
					if(dTableBSub){
						dTableBSub.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBSub.row.add([
							'<a href="javascript:void(0);" onclick="chooseSub(\''+resp[i].SUB+'\')">'+resp[i].SUB+'</a>',
							resp[i].KELOMPOK,
						]);
					}
					dTableBSub.draw();
				}
			});
		}
		
		dTableBSub = $("#table-bsub").DataTable({
			
		});
		
		browseSub = function(){
			loadDataBSub();
			$("#browseSubModal").modal("show");
		}
		
		chooseSub = function(SUB){
			$("#sub1").val(SUB);
			$("#browseSubModal").modal("hide");
		}
		
		$("#sub1").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseSub();
			}
		}); 

		//////////////////////////////////////////////////////////////////////

		var dTableBSub2;
		loadDataBSub2 = function(){
		
			$.ajax(
			{
				type: 'GET', 		
				url: "{{url('brg/browse_sub')}}",
				success: function( response )
				{
					resp = response;
					if(dTableBSub2){
						dTableBSub2.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBSub2.row.add([
							'<a href="javascript:void(0);" onclick="chooseSub2(\''+resp[i].SUB+'\')">'+resp[i].SUB+'</a>',
							resp[i].KELOMPOK,
						]);
					}
					dTableBSub2.draw();
				}
			});
		}
		
		dTableBSub2 = $("#table-bsub2").DataTable({
			
		});
		
		browseSub2 = function(){
			loadDataBSub2();
			$("#browseSub2Modal").modal("show");
		}
		
		chooseSub2 = function(SUB){
			$("#sub2").val(SUB);	
			$("#browseSub2Modal").modal("hide");
		}
		
		$("#sub2").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseSub2();
			}
		}); 

		////////////////////////////////////////////////////////////////////

		$('#per').on('change', function () {
			var per = $(this).val(); // contoh: 01/2026

			if (per) {
				var split = per.split('/');
				var bulan = parseInt(split[0]);
				var tahun = parseInt(split[1]);

				// tanggal pertama
				var tglAwal = '01-' + (bulan < 10 ? '0' + bulan : bulan) + '-' + tahun;

				// cari tanggal terakhir
				var lastDay = new Date(tahun, bulan, 0).getDate();
				var tglAkhir = lastDay + '-' + (bulan < 10 ? '0' + bulan : bulan) + '-' + tahun;

				// set ke input
				$('#tglDr').val(tglAwal);
				$('#tglSmp').val(tglAkhir);
			}
		});

		$('#per').trigger('change');
	});

//////////////////////////////////////////////


</script>
@endsection
