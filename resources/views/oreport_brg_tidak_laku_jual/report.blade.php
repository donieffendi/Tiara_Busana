@extends('layouts.plain')

@section('content')
<div class="content-wrapper">
	<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0">Laporan Barang Tidak Laku & Tidak Ada Transaksi Jual</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item active">Laporan Barang Tidak Laku & Tidak Ada Transaksi Jual</li>
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
					<form method="POST" action="{{url('jasper-brg_tidak_laku_jual-report')}}">
					@csrf
					<div class="form-group row">
						{{-- <div class="col-md-2">
							<label class="form-label">Suplier 1</label>
							<input type="text" class="form-control kodes" id="kodes" name="kodes" placeholder="Pilih Suplier" value="{{ session()->get('filter_kodes1') }}" readonly>
						</div> --}}
						<!-- <div class="col-md-3">
							<label class="form-label"></label>
							<input type="text" class="form-control NAMAS" id="NAMAS" name="NAMAS" placeholder="Nama" value="{{ session()->get('filter_namas1') }}" readonly>
						</div> -->
						{{-- <div class="col-md-1">
							<label class="form-label"> s.d </label>
						</div>
						<div class="col-md-2">
							<label class="form-label">Suplier 2</label>
							<input type="text" class="form-control kodes2" id="kodes2" name="kodes2" placeholder="ZZZ" value="{{ session()->get('filter_kodes2') }}" readonly>
						</div> --}}

						{{-- <div class="col-md-1">
							<label><strong>Cabang :</strong></label>
							<select name="cbg" id="cbg" class="form-control cbg" style="width: 200px">
								<option value="">--Pilih Cabang--</option>
								@foreach($cbg as $cbgD)
									<option value="{{$cbgD->CBG}}"  {{ (session()->get('filter_cbg') == $cbgD->CBG) ? 'selected' : '' }}>{{$cbgD->CBG}}</option>
								@endforeach
							</select>
						</div> --}}

                        {{-- <div class="col-md-3">
							<label><strong>Filter :</strong></label>

							<select name="gol" id="gol" class="form-control gol">
								<option value="A" {{ session()->get('filter_gol')=='A' ? 'selected': ''}}>A</option>
								<option value="B" {{ session()->get('filter_gol')=='B' ? 'selected': ''}}>B</option>
								<option value="C" {{ session()->get('filter_gol')=='C' ? 'selected': ''}}>C</option>
							</select>
						</div> --}}

					</div>

					<!--<div class="form-group row">	-->

					<!--</div>-->

					<div class="form-group row">
						<div class="col-md-2">
							<label class="form-label"> Dari Barang</label>
							<input type="text" class="form-control brg1" id="brg1" name="brg1" placeholder="Pilih Barang# 1" value="{{ session()->get('filter_brg1') }}" readonly>
						</div>

						<div class="col-md-2">
							<label class="form-label">Sampai Barang</label>
							<input type="text" class="form-control brg2" id="brg2" name="brg2" placeholder="Pilih Barang# 2" value="{{ session()->get('filter_brg2') }}" readonly>
						</div>
						{{-- <div class="col-md-3">
							<label class="form-label">Nama</label>
							<input type="text" class="form-control nabrg1" id="nabrg1" name="nabrg1" placeholder="Nama" value="{{ session()->get('filter_nabrg1') }}" readonly>
						</div> --}}
					</div>

					<div class="form-group row">
						
						{{-- <div class="col-md-3">
							<label class="form-label">Nama</label>
							<input type="text" class="form-control nabrg2" id="nabrg2" name="nabrg2" placeholder="Nama" value="{{ session()->get('filter_nabrg2') }}" readonly>
						</div> --}}
					</div>

					<!-- Filter Tanggal -->
					{{-- <div class="form-group row">
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
					<button class="btn btn-danger" type="button" id="resetfilter" class="resetfilter" onclick="window.location='{{url("rbrg_tidak_laku_jual")}}'">Reset</button>
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
								"label" => "No.",
							),
							"KDBAR" => array(
								"label" => "P.L.U",
							),
							"NMBAR" => array(
								"label" => "Nama Barang",
							),
							"SA" => array(
								"label" => "SA",
								"type" => "number",
								"decimals" => 2,
								"decimalPoint" => ".",
								"thousandSeparator" => ",",
							),
							"BL" => array(
								"label" => "BL",
								"type" => "number",
								"decimals" => 2,
								"decimalPoint" => ".",
								"thousandSeparator" => ",",
							),
							"RJ" => array(
								"label" => "RJ",
								"type" => "number",
								"decimals" => 2,
								"decimalPoint" => ".",
								"thousandSeparator" => ",",
							),
							"JL" => array(
								"label" => "JL",
								"type" => "number",
								"decimals" => 2,
								"decimalPoint" => ".",
								"thousandSeparator" => ",",
							),
							"KR1" => array(
								"label" => "KR+",
								"type" => "number",
								"decimals" => 2,
								"decimalPoint" => ".",
								"thousandSeparator" => ",",
							),
							"KR2" => array(
								"label" => "KR-",
								"type" => "number",
								"decimals" => 2,
								"decimalPoint" => ".",
								"thousandSeparator" => ",",
							),
							"SALDO" => array(
								"label" => "Saldo",
								"type" => "number",
								"decimals" => 2,
								"decimalPoint" => ".",
								"thousandSeparator" => ",",
							),
							"STOKR" => array(
								"label" => "Koreksi",
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
									"targets" => [3,4,5,6,7,8,9,10],
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
	<div class="modal-dialog" role="document">
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
						<th>Barang#</th>
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

<div class="modal fade" id="browseBarang2Modal" tabindex="-1" role="dialog" aria-labelledby="browseBarang2ModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="browseBarang2ModalLabel">Cari Barang</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bbarang2">
				<thead>
					<tr>
						<th>Barang#</th>
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
			$("#brg1").val(KDBAR);
			$("#browseBarangModal").modal("hide");
		}


		$("#brg1").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseBarang();
			}
		});

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
			$("#brg2").val(KDBAR);
			$("#browseBarang2Modal").modal("hide");
		}


		$("#brg2").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseBarang2();
			}
		});
	});


//////////////////////////////////////////////////////////////////////

	

//////////////////////////////////////////////


</script>
@endsection
