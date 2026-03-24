@extends('layouts.plain')

@section('styles')
<link rel="stylesheet" href="{{url('AdminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{url('http://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css') }}">
{{-- <link rel="stylesheet" href="{{url('https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap4.min.css') }}"> --}}

@endsection

@section('content')
	<div class="content-wrapper">
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0">Report Pembelian</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item active">Report Pembelian</li>
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
								@if (isset($error))
									<div class="alert alert-danger alert-dismissible">
										<button type="button" class="close" data-dismiss="alert">&times;</button>
										<strong>Error:</strong> {{ $error }}
									</div>
								@endif

								<form method="POST" action="{{ url('jasper-pembelian-report') }}" id="reportForm">
									@csrf

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

									<!-- Nav tabs -->
									<ul class="nav nav-tabs" id="reportTabs" role="tablist">
										<li class="nav-item" role="presentation">
											<a class="nav-link active" id="detail-tab" data-toggle="tab" href="#detail" role="tab" aria-controls="detail" aria-selected="true">
												<i class="fas fa-cube mr-1"></i>Periode
											</a>
										</li>
										<li class="nav-item" role="presentation">
											<a class="nav-link" id="summary-tab" data-toggle="tab" href="#summary" role="tab" aria-controls="summary" aria-selected="false">
												<i class="fas fa-calendar mr-1"></i>Sub Beli
											</a>
										</li>
										<li class="nav-item" role="presentation">
											<a class="nav-link" id="kasir-tab" data-toggle="tab" href="#kasir" role="tab" aria-controls="kasir" aria-selected="false">
												<i class="fas fa-cash-register mr-1"></i>Report Retur
											</a>
										</li>
										<li class="nav-item" role="presentation">
											<a class="nav-link" id="rconter-tab" data-toggle="tab" href="#rconter" role="tab" aria-controls="rconter" aria-selected="false">
												<i class="fas fa-warehouse mr-1"></i>Sub Retur
											</a>
										</li>
										<li class="nav-item" role="presentation">
											<a class="nav-link" id="rjual-tab" data-toggle="tab" href="#rjual" role="tab" aria-controls="rjual" aria-selected="false">
												<i class="fas fa-cart-plus mr-1"></i>Sub Konsinyasi
											</a>
										</li>
										<li class="nav-item" role="presentation">
											<a class="nav-link" id="rhari-tab" data-toggle="tab" href="#rhari" role="tab" aria-controls="rhari" aria-selected="false">
												<i class="fas fa-clock mr-1"></i>Transaksi Lain-Lain
											</a>
										</li>
									</ul>

									<!-- Tab panes -->
									<div class="tab-content" id="reportTabContent">
										<!-- Detail Transaksi Tab -->
										<div class="tab-pane fade show active" id="detail" role="tabpanel" aria-labelledby="detail-tab">
											<div class="pt-3">
												<div class="form-group">
													<!-- Search Filter Row -->
													<div class="row align-items-end mb-3">
														<div class="col-8">
															<button class="btn btn-primary mr-1" type="button" id="btnFilterDetail" onclick="filterPembelian('detail')">
																<i class="fas fa-search mr-1"></i>Filter
															</button>
															<button class="btn btn-danger mr-1" type="button" onclick="resetFilter('detail')">
																<i class="fas fa-redo mr-1"></i>Reset
															</button>
															<button class="btn btn-warning mr-1" type="button" onclick="cetakDetail()">
																<i class="fas fa-print mr-1"></i>Cetak
															</button>
														</div>
													</div>

													<!-- Data Table Detail -->
													<div class="col-md-12 report-content" id="detail-result">
														@if (!empty($hasilPembelian))
															<div class="table-responsive">
																<table id="tabelDetail" class="table table-striped table-bordered nowrap" style="width:100%">
																	<thead>
																		<tr>
																			<th>No. Bukti</th>
																			<th>Tanggal</th>
																			<th>Ref</th>
																			<th>Supplier</th>
																			<th>Nama</th>
																			<th>Bruto</th>
																			<th>Dis Promosi</th>
																			<th>DPP</th>
																			<th>PPN</th>
																		</tr>
																	</thead>
																	<tbody>
																		@foreach ($hasilPembelian as $item)
																			<tr>
																				<td>{{ $item->NO_BUKTI ?? '' }}</td>
																				<td>{{ isset($item->TGL) ? date('d/m/Y', strtotime($item->TGL)) : '' }}</td>
																				<td>{{ $item->REF ?? '' }}</td>
																				<td>{{ $item->kodes ?? '' }}</td>
																				<td>{{ $item->namas ?? '' }}</td>
																				<td class="text-right">{{ number_format($item->total ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->PROM ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->dpp ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->ppn ?? 0, 0, ',', '.') }}</td>
																			</tr>
																		@endforeach
																	</tbody>
																</table>
															</div>
														@else
															<div class="alert alert-info">
																<i class="fas fa-info-circle mr-2"></i>
																Silakan Klik Filter untuk menampilkan Data.
															</div>
														@endif
													</div>
												</div>
											</div>
										</div>

										<!-- Summary Barang Tab -->
										<div class="tab-pane fade" id="summary" role="tabpanel" aria-labelledby="summary-tab">
											<div class="pt-3">
												<div class="form-group">
													<div class="row align-items-end mb-3">
														<div class="col-8">
															<button class="btn btn-primary mr-1" type="button" id="btnFilterSummary" onclick="filterPembelian('summary')">
																<i class="fas fa-search mr-1"></i>Filter
															</button>
															<button class="btn btn-danger mr-1" type="button" onclick="resetFilter('summary')">
																<i class="fas fa-redo mr-1"></i>Reset
															</button>
															<button class="btn btn-warning mr-1" type="button" onclick="cetakSummary()">
																<i class="fas fa-print mr-1"></i>Cetak
															</button>
														</div>
													</div>

													<div class="col-md-12 report-content" id="summary-result">
														@if (!empty($hasilPembelian))
															<div class="table-responsive">
																<table id="tabelSummary" class="table table-striped table-bordered nowrap" style="width:100%">
																	<thead>
																		<tr>
																			<th>Sub</th>
																			<th>Kelompok</th>
																			<th>Bruto</th>
																			<th>Prom</th>
																		</tr>
																	</thead>
																	<tbody>
																		@foreach ($hasilPembelian as $item)
																			<tr>
																				<td>{{ $item->cnt ?? '' }}</td>
																				<td>{{ $item->NA_CNT ?? '' }}</td>
																				<td class="text-right">{{ number_format($item->bruto ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->prom ?? 0, 0, ',', '.') }}</td>
																			</tr>
																		@endforeach
																	</tbody>
																</table>
															</div>
														@else
															<div class="alert alert-info">
																<i class="fas fa-info-circle mr-2"></i>
																Silakan Klik Filter untuk menampilkan ringkasan barang.
															</div>
														@endif
													</div>
												</div>
											</div>
										</div>

										<!-- Data Kasir Tab -->
										<div class="tab-pane fade" id="kasir" role="tabpanel" aria-labelledby="kasir-tab">
											<div class="pt-3">
												<div class="form-group">
													<div class="row align-items-end mb-3">
														<div class="col-8">
															<button class="btn btn-primary mr-1" type="button" id="btnFilterKasir" onclick="filterPembelian('kasir')">
																<i class="fas fa-search mr-1"></i>Proses
															</button>
															<button class="btn btn-danger mr-1" type="button" onclick="resetFilter('kasir')">
																<i class="fas fa-redo mr-1"></i>Reset
															</button>
															<button class="btn btn-warning mr-1" type="button" onclick="cetakKasir()">
																<i class="fas fa-print mr-1"></i>Cetak
															</button>
														</div>
													</div>

													<div class="col-md-12 report-content" id="kasir-result">
														@if (!empty($hasilPembelian))
															<div class="table-responsive">
																<table id="tabelKasir" class="table table-striped table-bordered nowrap" style="width:100%">
																	<thead>
																		<tr>
																			<th>No. Bukti</th>
																			<th>Tanggal</th>
																			<th>Ref</th>
																			<th>Supplier</th>
																			<th>Nama</th>
																			<th>Bruto</th>
																			<th>Dis Promosi</th>
																			<th>DPP</th>
																			<th>PPN</th>
																		</tr>
																	</thead>
																	<tbody>
																		@foreach ($hasilPembelian as $item)
																			<tr>
																				<td>{{ $item->NO_BUKTI ?? '' }}</td>
																				<td>{{ isset($item->TGL) ? date('d/m/Y', strtotime($item->TGL)) : '' }}</td>
																				<td>{{ $item->REF ?? '' }}</td>
																				<td>{{ $item->kodes ?? '' }}</td>
																				<td>{{ $item->namas ?? '' }}</td>
																				<td class="text-right">{{ number_format($item->total ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->PROM ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->dpp ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->ppn ?? 0, 0, ',', '.') }}</td>
																			</tr>
																		@endforeach
																	</tbody>
																</table>
															</div>
														@else
															<div class="alert alert-info">
																<i class="fas fa-info-circle mr-2"></i>
																Silakan Klik Filter untuk menampilkan ringkasan barang.
															</div>
														@endif
													</div>
												</div>
											</div>
										</div>

										<!-- Data RConter Tab -->
										<div class="tab-pane fade" id="rconter" role="tabpanel" aria-labelledby="rconter-tab">
											<div class="pt-3">
												<div class="form-group">
													<div class="row align-items-end mb-3">
														<div class="col-8">
															<button class="btn btn-primary mr-1" type="button" id="btnFilterRCounter" onclick="filterPembelian('rconter')">
																<i class="fas fa-search mr-1"></i>Filter
															</button>
															<button class="btn btn-danger mr-1" type="button" onclick="resetFilter('rconter')">
																<i class="fas fa-redo mr-1"></i>Reset
															</button>
															<button class="btn btn-warning mr-1" type="button" onclick="cetakCounter()">
																<i class="fas fa-print mr-1"></i>Cetak
															</button>
														</div>
													</div>

													<div class="col-md-12 report-content" id="rconter-result">
														@if (!empty($hasilPembelian))
															<div class="table-responsive">
																<table id="tabelKasir" class="table table-striped table-bordered nowrap" style="width:100%">
																	<thead>
																		<tr>
																			<th>Sub</th>
																			<th>Kelompok</th>
																			<th>Bruto</th>
																			<th>Prom</th>
																		</tr>
																	</thead>
																	<tbody>
																		@foreach ($hasilPembelian as $item)
																			<tr>
																				<td>{{ $item->sub ?? '' }}</td>
																				<td>{{ $item->kelompok ?? '' }}</td>
																				<td class="text-right">{{ number_format($item->bruto ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->prom ?? 0, 0, ',', '.') }}</td>
																			</tr>
																		@endforeach
																	</tbody>
																</table>
															</div>
														@else
															<div class="alert alert-info">
																<i class="fas fa-info-circle mr-2"></i>
																Silakan Klik Filter untuk menampilkan ringkasan barang.
															</div>
														@endif
													</div>
												</div>
											</div>
										</div>

										<!-- Data Rekap Pembelian Tab -->
										<div class="tab-pane fade" id="rjual" role="tabpanel" aria-labelledby="rjual-tab">
											<div class="pt-3">
												<div class="form-group">
													<div class="row align-items-end mb-3">
														<div class="col-8">
															<button class="btn btn-primary mr-1" type="button" id="btnFilterRJual" onclick="filterPembelian('rjual')">
																<i class="fas fa-search mr-1"></i>Filter
															</button>
															<button class="btn btn-danger mr-1" type="button" onclick="resetFilter('rjual')">
																<i class="fas fa-redo mr-1"></i>Reset
															</button>
															<button class="btn btn-warning mr-1" type="button" onclick="cetakJual()">
																<i class="fas fa-print mr-1"></i>Cetak
															</button>
														</div>
													</div>

													<div class="col-md-12 report-content" id="rjual-result">
														@if (!empty($hasilPembelian))
															<div class="table-responsive">
																<table id="tabelKasir" class="table table-striped table-bordered nowrap" style="width:100%">
																	<thead>
																		<tr>
																			<th>Sub</th>
																			<th>Kelompok</th>
																			<th>Total</th>
																			<th>Ppn</th>
																			<th>Nett</th>
																			<th>Prom</th>
																		</tr>
																	</thead>
																	<tbody>
																		@foreach ($hasilPembelian as $item)
																			<tr>
																				<td>{{ $item->sub ?? '' }}</td>
																				<td>{{ $item->kelompok ?? '' }}</td>
																				<td class="text-right">{{ number_format($item->total ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->ppn ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->nett ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->prom ?? 0, 0, ',', '.') }}</td>
																			</tr>
																		@endforeach
																	</tbody>
																</table>
															</div>
														@else
															<div class="alert alert-info">
																<i class="fas fa-info-circle mr-2"></i>
																Silakan Klik Filter untuk menampilkan ringkasan barang.
															</div>
														@endif
													</div>
												</div>
											</div>
										</div>

										<!-- Data Rekap Harian Tab -->
										<div class="tab-pane fade" id="rhari" role="tabpanel" aria-labelledby="rhari-tab">
											<div class="pt-3">
												<div class="form-group">
													<div class="row align-items-end mb-3">
														<div class="col-8">
															<button class="btn btn-primary mr-1" type="button" id="btnFilterRHari" onclick="filterPembelian('rhari')">
																<i class="fas fa-search mr-1"></i>Filter
															</button>
															<button class="btn btn-danger mr-1" type="button" onclick="resetFilter('rhari')">
																<i class="fas fa-redo mr-1"></i>Reset
															</button>
															<button class="btn btn-warning mr-1" type="button" onclick="cetakHari()">
																<i class="fas fa-print mr-1"></i>Cetak
															</button>
														</div>
													</div>

													<div class="col-md-12 report-content" id="rhari-result">
														@if (!empty($hasilPembelian))
															<div class="table-responsive">
																<table id="tabelKasir" class="table table-striped table-bordered nowrap" style="width:100%">
																	<thead>
																		<tr>
																			<th>No. Bukti</th>
																			<th>Tanggal</th>
																			<th>Perkiraan</th>
																			<th>Supp</th>
																			<th>Keterangan</th>
																			<th>Debet</th>
																			<th>Kredit</th>
																		</tr>
																	</thead>
																	<tbody>
																		@foreach ($hasilPembelian as $item)
																			<tr>
																				<td>{{ $item->NO_BUKTI ?? '' }}</td>
																				<td>{{ isset($item->TGL) ? date('d/m/Y', strtotime($item->TGL)) : '' }}</td>
																				<td>{{ $item->ACNO ?? '' }}</td>
																				<td>{{ $item->KODES ?? '' }}</td>
																				<td>{{ $item->KET ?? '' }}</td>
																				<td class="text-right">{{ number_format($item->DEBET ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->KREDIT ?? 0, 0, ',', '.') }}</td>
																			</tr>
																		@endforeach
																	</tbody>
																</table>
															</div>
														@else
															<div class="alert alert-info">
																<i class="fas fa-info-circle mr-2"></i>
																Silakan Klik Filter untuk menampilkan ringkasan barang.
															</div>
														@endif
													</div>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Summary -->
	<div class="modal fade" id="summaryModal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Summary Kasir Bantu</h5>
					<button type="button" class="close" data-dismiss="modal">
						<span>&times;</span>
					</button>
				</div>
				<div class="modal-body" id="summaryContent">
					<div class="text-center">
						<i class="fas fa-spinner fa-spin"></i> Loading...
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
	<div class="modal fade" id="browseCounter2Modal" tabindex="-1" role="dialog" aria-labelledby="browseCounter2ModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="browseCounter2ModalLabel">Cari Counter2</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<table class="table table-stripped table-bordered" id="table-Counter2">
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
<script src="{{url('AdminLTE/plugins/datatables/jquery.dataTables.js') }}"></script>
<script src="{{url('AdminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{url('http://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js') }}"></script>
<script src="{{url('https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js') }}"></script>
<script src="{{url('https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js') }}"></script>
<script src="{{url('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){

	//Datepicker untuk tanggal
	$('.date').datepicker({  
		dateFormat: 'dd-mm-yy'
	});

    // Tab Bootstrap
    $('#reportTabs a').on('click', function(e){
        e.preventDefault();
        $(this).tab('show');
    });

    // Simpan tab aktif
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        localStorage.setItem('activePembelianTab', $(e.target).attr('href'));
    });

    // Restore tab aktif
    var activeTab = localStorage.getItem('activePembelianTab');
    if(activeTab){
        $('#reportTabs a[href="'+activeTab+'"]').tab('show');
    }

    // Auto format periode input
    $('#periode_detail, #periode_summary, #periode_kasir').on('input', function(){
        var value = this.value.replace(/\D/g,'');
        if(value.length>=2) this.value = value.substring(0,2)+'-'+value.substring(2,6);
    });

    // Inisialisasi DataTable awal (Detail)
    @if(!empty($hasilPembelian))
        $('#tabelDetail').DataTable({
            pageLength: 25,
            searching: true,
            ordering: true,
            responsive: true,
            columnDefs: [{className:'dt-right', targets:[4]}],
            language:{url:'//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'}
        });
    @endif

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
	///////////////////////////////

	var dTableCounter2;
		var targetCNT = "";
		loadDataCounter2 = function(){
		
			$.ajax(
			{
				type: 'GET', 		
				url: "{{url('counter/browse_th')}}",
				success: function( response )
				{
					resp = response;
					if(dTableCounter2){
						dTableCounter2.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableCounter2.row.add([
							'<a href="javascript:void(0);" onclick="chooseCounter2(\''+resp[i].CNT+'\')">'+resp[i].CNT+'</a>',
							resp[i].NA_CNT,
							resp[i].SUP,
							resp[i].NAMAS,
						]);
					}
					dTableCounter2.draw();
				}
			});
		}
		
		dTableCounter2 = $("#table-Counter2").DataTable({
			
		});
		
		browseCounter2 = function(){
			loadDataCounter2();
			$("#browseCounter2Modal").modal("show");
		}
		
		chooseCounter2 = function(CNT){
			$("#" + targetCNT).val(CNT);
			$("#browseCounter2Modal").modal("hide");
		}
		
		$("#CNT1").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				targetCNT = "CNT1";
				browseCounter2();
			}
		});

		$("#CNT2").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				targetCNT = "CNT2";
				browseCounter2();
			}
		});

	///////////////////////////////

});

// -------------------------------
// Fungsi Filter per Tab
// -------------------------------
function filterPembelian(tabType){

    var per = $('#per').val();
    var btnId='';

    switch(tabType){
        case 'detail':
            btnId = '#btnFilterDetail';
            if(!per){ 
                alert('Pilih Periode terlebih dahulu'); 
                return; 
            }
        break;

        case 'summary':
            btnId = '#btnFilterSummary';
            if(!per){ 
                alert('Pilih Periode terlebih dahulu'); 
                return; 
            }
        break;

        case 'kasir':
            btnId = '#btnFilterKasir';
            if(!per){ 
                alert('Pilih Periode terlebih dahulu'); 
                return; 
            }
        break;

		case 'rconter':
            btnId = '#btnFilterRCounter';
            if(!per){ 
                alert('Pilih Periode terlebih dahulu'); 
                return; 
            }
        break;

		case 'rjual':
            btnId = '#btnFilterRJual';
            if(!per){ 
                alert('Pilih Periode terlebih dahulu'); 
                return; 
            }
        break;

		case 'rhari':
            btnId = '#btnFilterRHari';
            if(!per){ 
                alert('Pilih Periode terlebih dahulu'); 
                return; 
            }
        break;
    }

    $(btnId).html('<i class="fas fa-spinner fa-spin mr-1"></i>Loading...').prop('disabled',true);

    $.ajax({
        url: '{{ route("get-pembelian-report-ajax") }}',
        method: 'GET',
        data: { 
            tab: tabType,
            per: per,
        },
        success: function(res){
            if(res.success){
                displayTabData(tabType, res.data);
            }else{
                alert(res.message || 'Gagal memuat data');
            }
        },
        error: function(xhr){
            console.error(xhr);
            alert('Terjadi kesalahan saat memuat data');
        },
        complete: function(){
            $(btnId).html('<i class="fas fa-search mr-1"></i>Filter').prop('disabled', false);
        }
    });
}

// -------------------------------
// Fungsi Render Data di Tab
// -------------------------------
function displayTabData(tabType, data){
    var targetDiv = '#' + tabType + '-result';
    var html = '';

    if(data.length===0){
        html = '<div class="alert alert-warning">Tidak ada data untuk parameter yang dipilih</div>';
    } else {
        html = '<div class="table-responsive"><table class="table table-striped table-bordered" id="table-'+tabType+'"><thead><tr>';

        if(tabType==='detail'){
            html += '<th>No. Bukti</th><th>Tanggal</th><th>Ref</th><th>Supplier</th><th>Nama</th><th>Bruto</th><th>Dis Prom</th><th>DPP</th><th>PPN</th>';
        } else if(tabType==='summary'){
            html += '<th>Sub</th><th>Kelompok</th><th>Bruto</th><th>Prom</th>';
        } else if(tabType==='kasir'){
            html += '<th>No. Bukti</th><th>Tanggal</th><th>Ref</th><th>Supplier</th><th>Nama</th><th>Bruto</th><th>Dis Prom</th><th>DPP</th><th>PPN</th>';
		} else if(tabType==='rconter'){
			html += '<th>Sub</th><th>Kelompok</th><th>Bruto</th><th>Prom</th>';
		} else if(tabType==='rjual'){
			html += '<th>Sub</th><th>Kelompok</th><th>Total</th><th>Ppn</th><th>Nett</th><th>Prom</th>';
		} else if(tabType==='rhari'){
			html += '<th>No. Bukti</th><th>Tanggal</th><th>Perkiraan</th><th>Supp</th><th>Keterangan</th><th>Debet</th><th>Kredit</th>';
		}
        html += '</tr></thead><tbody>';

        $.each(data,function(i,item){
            html += '<tr>';
            if(tabType==='detail'){
                html += '<td>'+item.NO_BUKTI+'</td><td>'+formatDate(item.TGL)+'</td><td>'+item.REF+'</td><td>'+item.kodes+'</td><td>'+item.namas+'</td><td class="text-right">'+formatNumber(item.total)+'</td><td class="text-right">'+formatNumber(item.PROM)+'</td><td class="text-right">'+formatNumber(item.dpp)+'</td><td class="text-right">'+formatNumber(item.ppn)+'</td>';
            } else if(tabType==='summary'){
                html += '<td>'+item.cnt+'</td><td>'+item.NA_CNT+'</td><td class="text-right">'+formatNumber(item.bruto)+'</td><td class="text-right">'+formatNumber(item.prom)+'</td>';
            } else if(tabType==='kasir'){
                html += '<td>'+item.NO_BUKTI+'</td><td>'+formatDate(item.TGL)+'</td><td>'+item.REF+'</td><td>'+item.kodes+'</td><td>'+item.namas+'</td><td class="text-right">'+formatNumber(item.total)+'</td><td class="text-right">'+formatNumber(item.PROM)+'</td><td class="text-right">'+formatNumber(item.dpp)+'</td><td class="text-right">'+formatNumber(item.ppn)+'</td>';
			} else if(tabType==='rconter'){
				html += '<td>'+item.sub+'</td><td>'+item.kelompok+'</td><td class="text-right">'+formatNumber(item.bruto)+'</td><td class="text-right">'+formatNumber(item.prom)+'</td>';
			} else if(tabType==='rjual'){
				html += '<td>'+item.sub+'</td><td>'+item.kelompok+'</td><td class="text-right">'+formatNumber(item.total)+'</td><td class="text-right">'+formatNumber(item.ppn)+'</td><td class="text-right">'+formatNumber(item.nett)+'</td><td class="text-right">'+formatNumber(item.prom)+'</td>';
			} else if(tabType==='rhari'){
				html += '<td>'+item.NO_BUKTI+'</td><td>'+formatDate(item.TGL)+'</td><td>'+item.ACNO+'</td><td>'+item.KODES+'</td><td>'+item.KET+'</td><td class="text-right">'+formatNumber(item.DEBET)+'</td><td class="text-right">'+formatNumber(item.KREDIT)+'</td>';
			}
            html += '</tr>';
        });

        html += '</tbody></table></div>';
    }

    $(targetDiv).html(html);

    if(data.length>0){
        $('#table-'+tabType).DataTable({
            pageLength:25,
            searching:true,
            ordering:true,
            responsive:true,
            // scrollX:true,
            dom:'Blfrtip',
            buttons:['copy','excel','csv','pdf','print'],
            language:{url:'//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'}
        });
    }
}

// -------------------------------
// Helper Format
// -------------------------------
function formatNumber(num){ return Number(num).toLocaleString('id-ID'); }
function formatDate(dateStr){ return dateStr ? new Date(dateStr).toLocaleDateString('id-ID') : ''; }

function resetFilter(tabType){
    switch(tabType){
        case 'detail':
			$('#per').val('');
            break;
        case 'summary':
			$('#per').val('');
            break;
        case 'kasir':
			$('#per').val('');
            break;
		case 'rconter':
			$('#per').val('');
			break;
		case 'rjual':
			$('#per').val('');
			break;
		case 'rhari':
			$('#per').val('');
			break;
    }

    // Kosongkan hasil tabel
    $('#' + tabType + '-result').html('<div class="alert alert-info"><i class="fas fa-info-circle mr-2"></i>Silakan Klik Filter untuk menampilkan data.</div>');

    // Jika tabel DataTable sebelumnya sudah diinisialisasi, destroy dulu
    var tableId = '#table-' + tabType;
    if($.fn.DataTable.isDataTable(tableId)){
        $(tableId).DataTable().destroy();
    }
}

function printReport(url) {
			var form = $('<form>', {
				'method': 'POST',
				'action': url,
				'target': '_blank'
			});

			form.append($('<input>', {
				'type': 'hidden',
				'name': '_token',
				'value': $('meta[name="csrf-token"]').attr('content')
			}));

			form.appendTo('body').submit().remove();
}

// Print function
function cetakKasir() {
    var per = $('#per').val();
	var url = '{{ route('jasper-pembelian-report') }}' + '?per=' + per;
	window.open(url, '_blank');
}

function cetakDetail() {
    var per = $('#per').val(); 
    var url = '{{ route('jasper-pembeliandetail-report') }}' + '?per=' + per;

    window.open(url, '_blank');
}

function cetakSummary() {
			
    var per = $('#per').val(); 
	var url = '{{ route('jasper-pembeliansummary-report') }}' + '?per=' + per;
	window.open(url, '_blank');
}

function cetakCounter() {
			
    var per = $('#per').val(); 
	var url = '{{ route('jasper-pembeliansubretur-report') }}' + '?per=' + per;
	window.open(url, '_blank');
}

function cetakJual() {
			
    var per = $('#per').val(); 
	var url = '{{ route('jasper-pembeliankons-report') }}' + '?per=' + per;
	window.open(url, '_blank');
}

function cetakHari() {
			
    var per = $('#per').val(); 
	var url = '{{ route('jasper-pembelianlain-report') }}' + '?per=' + per;
	window.open(url, '_blank');
}


</script>
@endsection