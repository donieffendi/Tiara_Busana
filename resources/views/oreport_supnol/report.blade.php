@extends('layouts.plain')

@section('content')
<div class="content-wrapper">
	<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0">Laporan Supplier Tidak Ada Budget</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item active">Laporan Supplier Tidak Ada Budget</li>
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
					<form method="POST" action="{{url('jasper-supnol-report')}}">
					@csrf
					<div class="form-group row">
						    
						<div class="col-md-1" align="right">						
							<label class="form-label">No. Supl</label>
						</div>
						<div class="col-md-2">
							<input type="text" class="form-control kodes1" id="kodes1" name="kodes1" placeholder="pilih No. Supplier" value="{{ session()->get('filter_kodes1') }}" readonly>
						</div>
						<div class="col-md-4">						
							<input type="text" class="form-control namas1" id="namas1" name="namas1" placeholder="Nama Supplier" value="{{ session()->get('filter_namas1') }}" readonly>
						</div>  
					</div>

					<div class="form-group row">
						    
						<div class="col-md-1">						
							<label class="form-label">Sampai No. Supl</label>
						</div>
						<div class="col-md-2">
							<input type="text" class="form-control kodes2" id="kodes2" name="kodes2" placeholder="pilih No. Supplier" value="{{ session()->get('filter_kodes2') }}" readonly>
						</div>
						<div class="col-md-4">						
							<input type="text" class="form-control namas2" id="namas2" name="namas2" placeholder="Nama Supplier" value="{{ session()->get('filter_namas2') }}" readonly>
						</div>  
					</div>
					
					<button class="btn btn-primary" type="submit" id="filter" class="filter" name="filter">Filter</button>
					<button class="btn btn-danger" type="button" id="resetfilter" class="resetfilter" onclick="window.location='{{url("rsupnol")}}'">Reset</button>
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
							"NO_SUPL" => array(
								"label" => "Kode Supplier",
							),
							"NAMA" => array(
								"label" => "Nama Supplier",
							),
							"ALMT_K" => array(
								"label" => "Alamat",
							),
							"QTY" => array(
								"label" => "Qty",
								"type" => "number",
								"decimals" => 2,
								"decimalPoint" => ".",
								"thousandSeparator" => ",",
							),
							"BUDGET" => array(
								"label" => "Budget",
								"type" => "number",
								"decimals" => 2,
								"decimalPoint" => ".",
								"thousandSeparator" => ",",
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
									"targets" => [4,5],
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
<div class="modal fade" id="browseSuplierModal" tabindex="-1" role="dialog" aria-labelledby="browseSuplierModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="browseSuplierModalLabel">Cari Suplier</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bsuplier">
				<thead>
					<tr>
						<th>Suplier</th>
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

<div class="modal fade" id="browseSuplier2Modal" tabindex="-1" role="dialog" aria-labelledby="browseSuplier2ModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="browseSuplier2ModalLabel">Cari Suplier2</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bsuplier2">
				<thead>
					<tr>
						<th>Suplier</th>
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
@endsection

@section('javascripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>
	$(document).ready(function() {
		
		$('.date').datepicker({  
			dateFormat: 'dd-mm-yy'
		}); 

		var dTableBSuplier;
		loadDataBSuplier = function(){
		
			$.ajax(
			{
				type: 'GET', 		
				url: "{{url('sup/browse_stegur')}}",
				success: function( response )
				{
					resp = response;
					if(dTableBSuplier){
						dTableBSuplier.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBSuplier.row.add([
							'<a href="javascript:void(0);" onclick="chooseSuplier(\''+resp[i].KODES+'\',\''+resp[i].NAMAS+'\')">'+resp[i].KODES+'</a>',
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
		
		browseSuplier = function(){
			loadDataBSuplier();
			$("#browseSuplierModal").modal("show");
		}
		
		chooseSuplier = function(KODES,NAMAS){
			$("#kodes1").val(KODES);
			$("#namas1").val(NAMAS);
			$("#browseSuplierModal").modal("hide");
		}
		
		$("#kodes1").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseSuplier();
			}
		}); 

		//////////////////////////////////////////////////////////////////////

		var dTableBSuplier2;
		loadDataBSuplier2 = function(){
		
			$.ajax(
			{
				type: 'GET', 		
				url: "{{url('sup/browse_stegur')}}",
				success: function( response )
				{
					resp = response;
					if(dTableBSuplier2){
						dTableBSuplier2.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBSuplier2.row.add([
							'<a href="javascript:void(0);" onclick="chooseSuplier2(\''+resp[i].KODES+'\',\''+resp[i].NAMAS+'\')">'+resp[i].KODES+'</a>',
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
		
		browseSuplier2 = function(){
			loadDataBSuplier2();
			$("#browseSuplier2Modal").modal("show");
		}
		
		chooseSuplier2 = function(KODES,NAMAS){
			$("#kodes2").val(KODES);
			$("#namas2").val(NAMAS);	
			$("#browseSuplier2Modal").modal("hide");
		}
		
		$("#kodes2").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseSuplier2();
			}
		}); 


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
