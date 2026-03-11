@extends('layouts.plain')

@section('content')
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Laporan Pemantauan Barang Busana Turun Harga</h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item active">Laporan Pemantauan Barang Busana Turun Harga</li>
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
					<form method="POST" action="{{url('jasper-pantau-report')}}">
					@csrf					
					<div class="form-group row">
						<div class="col-md-1">
							<label><strong>Cabang :</strong></label>
							<select name="cbg" id="cbg" class="form-control cbg" style="width: 200px">
								<option value="">--Pilih Cabang--</option>
								@foreach($cbg as $cbgD)
									<option value="{{$cbgD->KODE}}"  {{ (session()->get('filter_cbg') == $cbgD->KODE) ? 'selected' : '' }}>{{$cbgD->KODE}}</option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-3">
							<input class="form-control date tglDr" id="tglDr" name="tglDr"
							type="text" autocomplete="off" value="{{ session()->get('filter_tglDari') }}"> 
						</div>
						<div>s.d.</div> 
						<div class="col-md-3">
							<input class="form-control date tglSmp" id="tglSmp" name="tglSmp"
							type="text" autocomplete="off" value="{{ session()->get('filter_tglSampai') }}">
						</div>

						{{-- <div class="col-md-1">
							<input type="hidden" name="semua" value="0">
							<input type="checkbox" class="form-check-input" id="semua" name="semua" value="1" {{ session()->get('filter_semua',1) == 1 ? 'checked' : '' }}>
							<label class="form-check-label" id="label_semua" for="semua"><strong>semua</strong></label>
						</div> --}}
					</div>

					{{-- <div class="form-group row">
						<div class="col-md-2">						
							<label class="form-label">Counter</label>
							<input type="text" class="form-control CNT1" id="CNT1" name="CNT1" placeholder="Pilih Customer" value="{{ session()->get('filter_CNT1') }}" readonly>
						</div>  
						<div class="col-md-2">						
							<label class="form-label">s/d</label>
							<input type="text" class="form-control CNT2" id="CNT2" name="CNT2" placeholder="Pilih Customer" value="{{ session()->get('filter_CNT2') }}" readonly>
						</div>
					</div> --}}
						
                    <button class="btn btn-primary" type="submit" id="filter" class="filter" name="filter">Filter</button>
                    <button class="btn btn-danger" type="button" id="resetfilter" class="resetfilter" onclick="window.location='{{url("rpantau")}}'">Reset</button>
					<button class="btn btn-warning" type="submit" id="cetak" class="cetak" formtarget="_blank">Cetak</button>
					</form>
					<div style="margin-bottom: 15px;"></div>
					<!--
					<table class="table table-fixed table-striped table-border table-hover nowrap datatable">
						<thead class="table-dark">
							<tr>
								<th scope="col" style="text-align: center">#</th>
								<th scope="col" style="text-align: left">Bukti</th>
								<th scope="col" style="text-align: left">Tgl</th>
								<th scope="col" style="text-align: left">SO#</th>
								<th scope="col" style="text-align: left">Customer#</th>
								<th scope="col" style="text-align: left">-</th>
								<th scope="col" style="text-align: left">Faktur#</th>
								<th scope="col" style="text-align: right">Bayar</th>
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
                                    "NO_BUKTI" => array(
                                        "label" => "No. Perubahan Harga Jual",
                                    ),
									"SUB" => array(
                                        "label" => "Sub",
                                    ),
									"ITEM" => array(
                                        "label" => "Item / PLU",
                                    ),
									"NA_BRG" => array(
                                        "label" => "Nama Barang",
                                    ),
									"QTY_LAKU" => array(
                                        "label" => "Jumlah Laku",
                                        "type" => "number",
                                        "decimals" => 2,
                                        "decimalPoint" => ".",
                                        "thousandSeparator" => ",",
                                    ),
									"SISA" => array(
                                        "label" => "Sisa",
                                        "type" => "number",
                                        "decimals" => 2,
                                        "decimalPoint" => ".",
                                        "thousandSeparator" => ",",
                                    ),
									"TGL_BELI" => array(
                                        "label" => "Tanggal Beli",
										"type" => "date",
										"format" => "Y-m-d",
										"displayFormat" => "d-m-Y",
                                    ),
									"HARI" => array(
                                        "label" => "Masa Jual",
										"decimals" => 0,
                                        "decimalPoint" => ".",
                                        "thousandSeparator" => ",",
                                    ),
									"DISKON" => array(
                                        "label" => "% Diskon",
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
<div class="modal fade" id="browseCustModal" tabindex="-1" role="dialog" aria-labelledby="browseCustModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="browseCustModalLabel">Cari Customer</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-cust">
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
	$(document).ready(function() {
		$('.date').datepicker({  
			dateFormat: 'dd-mm-yy'
		}); 
		/////////////////////////

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
