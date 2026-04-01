@extends('layouts.plain')

@section('content')
<div class="content-wrapper">
	<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0">Laporan Barang Yang Di Order Per Suplier</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item active">Laporan Barang Yang Di Order Per Suplier</li>
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
					<form method="POST" action="{{url('jasper-sup-report')}}">
					@csrf
					
					{{-- <div class="form-group row">
						<div class="col-md-1" align	="right">
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
					</div> --}}

					<div class="form-group row">
						<div class="col-md-1" align="right">
							<label class="form-label">Dari Supplier</label>
						</div>
						<div class="col-md-2">
							<input type="text" class="form-control kodes1" id="kodes1" name="kodes1" placeholder="Masukkan No. Supl" value="{{ session()->get('filter_kodes1') }}" readonly>
						</div>
						<div>s/d</div>
						<div class="col-md-2">
							<input type="text" class="form-control kodes2" id="kodes2" name="kodes2" placeholder="Masukkan No. Supl" value="{{ session()->get('filter_kodes1') }}" readonly>
						</div>
					</div>

					{{-- <div class="form-group row">
						<div class="col-md-1" align="right">
							<label class="form-label">Budget</label>
						</div>
						<div class="col-md-2">
							<input type="text" class="form-control budget" id="budget" name="budget" placeholder="Masukkan Jenis" value="{{ session()->get('filter_budget') }}">
						</div>
					</div> --}}

					

					{{-- <div class="form-group row">
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
					</div> --}}

					<button class="btn btn-primary" type="submit" id="filter" class="filter" name="filter">Filter</button>
					<button class="btn btn-danger" type="button" id="resetfilter" class="resetfilter" onclick="window.location='{{url("rsup")}}'">Reset</button>
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
							"HB" => array(
								"label" => "Harga Beli",
								"type" => "number",
								"decimals" => 2,
								"decimalPoint" => ".",
								"thousandSeparator" => ",",
								"footer" => "sum",
								"footerText" => "<b>@value</b>",
							),
							"QTY_BELI1" => array(
								"label" => "Beli AKhir",
								"type" => "number",
								"decimals" => 2,
								"decimalPoint" => ".",
								"thousandSeparator" => ",",
								"footer" => "sum",
								"footerText" => "<b>@value</b>",
							),
							"TG_BELI1" => array(
								"label" => "Tg Beli AKhir",
								"type" => "date",
								"format" => "Y-m-d",
								"displayFormat" => "d-m-Y",
							),
							"STOCKR" => array(
								"label" => "Stokr",
								"type" => "number",
								"decimals" => 2,
								"decimalPoint" => ".",
								"thousandSeparator" => ",",
								"footer" => "sum",
								"footerText" => "<b>@value</b>",
							),
							"QTY" => array(
								"label" => "Jumlah Order",
								"type" => "number",
								"decimals" => 2,
								"decimalPoint" => ".",
								"thousandSeparator" => ",",
								"footer" => "sum",
								"footerText" => "<b>@value</b>",
							),
							"KET" => array(
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
									"targets" => [4,5,7,8],
								),
								array(
									"className" => "dt-center", 
									"targets" => [0],
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
							'<a href="javascript:void(0);" onclick="chooseSuplier(\''+resp[i].KODES+'\')">'+resp[i].KODES+'</a>',
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
		
		chooseSuplier = function(KODES){
			$("#kodes1").val(KODES);
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
							'<a href="javascript:void(0);" onclick="chooseSuplier2(\''+resp[i].KODES+'\')">'+resp[i].KODES+'</a>',
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
		
		chooseSuplier2 = function(KODES){
			$("#kodes2").val(KODES);
			// $("#NAMAS").val(NAMAS);	
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

</script>
@endsection