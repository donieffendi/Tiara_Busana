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
						<h1 class="m-0">Report Penjualan</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item active">Report Penjualan</li>
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

								<form method="POST" action="{{ url('jasper-penjualan-report') }}" id="reportForm">
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

									<!-- Nav tabs -->
									<ul class="nav nav-tabs" id="reportTabs" role="tablist">
										<li class="nav-item" role="presentation">
											<a class="nav-link active" id="detail-tab" data-toggle="tab" href="#detail" role="tab" aria-controls="detail" aria-selected="true">
												<i class="fas fa-cube mr-1"></i>Per Item
											</a>
										</li>
										<li class="nav-item" role="presentation">
											<a class="nav-link" id="summary-tab" data-toggle="tab" href="#summary" role="tab" aria-controls="summary" aria-selected="false">
												<i class="fas fa-calendar mr-1"></i>Per Tanggal
											</a>
										</li>
										<li class="nav-item" role="presentation">
											<a class="nav-link" id="kasir-tab" data-toggle="tab" href="#kasir" role="tab" aria-controls="kasir" aria-selected="false">
												<i class="fas fa-cash-register mr-1"></i>Per Conter
											</a>
										</li>
										<li class="nav-item" role="presentation">
											<a class="nav-link" id="rconter-tab" data-toggle="tab" href="#rconter" role="tab" aria-controls="rconter" aria-selected="false">
												<i class="fas fa-warehouse mr-1"></i>Rekap Conter
											</a>
										</li>
										<li class="nav-item" role="presentation">
											<a class="nav-link" id="rjual-tab" data-toggle="tab" href="#rjual" role="tab" aria-controls="rjual" aria-selected="false">
												<i class="fas fa-cart-plus mr-1"></i>Rekap Penjualan
											</a>
										</li>
										<li class="nav-item" role="presentation">
											<a class="nav-link" id="rhari-tab" data-toggle="tab" href="#rhari" role="tab" aria-controls="rhari" aria-selected="false">
												<i class="fas fa-clock mr-1"></i>Rekap Harian
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
															<button class="btn btn-primary mr-1" type="button" id="btnFilterDetail" onclick="filterPenjualan('detail')">
																<i class="fas fa-search mr-1"></i>Filter
															</button>
															<button class="btn btn-danger mr-1" type="button" onclick="resetFilter('detail')">
																<i class="fas fa-redo mr-1"></i>Reset
															</button>
															{{-- <button class="btn btn-warning mr-1" type="button" onclick="cetakDetail()">
																<i class="fas fa-print mr-1"></i>Cetak
															</button> --}}
														</div>
													</div>

													<!-- Data Table Detail -->
													<div class="col-md-12 report-content" id="detail-result">
														@if (!empty($hasilPenjualan))
															<div class="table-responsive">
																<table id="tabelDetail" class="table table-striped table-bordered nowrap" style="width:100%">
																	<thead>
																		<tr>
																			<th>Kitir SPM</th>
																			<th>Kitir BC</th>
																			<th>Tanggal</th>
																			<th>Kode Barang</th>
																			<th>Nama Barang</th>
																			<th>Qty</th>
																			<th>Harga</th>
																			<th>Total</th>
																			<th>Cabang</th>
																			<th>Kasir</th>
																			<th>Petugas Kasir</th>
																			<th>Post Status</th>
																		</tr>
																	</thead>
																	<tbody>
																		@foreach ($hasilPenjualan as $item)
																			<tr>
																				<td>{{ $item->no_bukti ?? '' }}</td>
																				<td>{{ $item->bukti2 ?? '' }}</td>
																				<td>{{ isset($item->initanggal) ? date('d/m/Y', strtotime($item->initanggal)) : '' }}</td>
																				<td>{{ $item->KD_BRG ?? '' }}</td>
																				<td>{{ $item->NA_BRG ?? '' }}</td>
																				<td class="text-right">{{ number_format($item->qty ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->harga ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->total ?? 0, 0, ',', '.') }}</td>
																				<td>{{ $item->cabang ?? '' }}</td>
																				<td>{{ $item->KSR ?? '' }}</td>
																				<td>{{ $item->usrnm ?? '' }}</td>
																				<td>{{ $item->posted ?? '' }}</td>
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
															<button class="btn btn-primary mr-1" type="button" id="btnFilterSummary" onclick="filterPenjualan('summary')">
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
														@if (!empty($hasilPenjualan))
															<div class="table-responsive">
																<table id="tabelSummary" class="table table-striped table-bordered nowrap" style="width:100%">
																	<thead>
																		<tr>
																			<th>CNT</th>
																			<th>Nama Counter</th>
																			<th>S. Pajak</th>
																			<th>Tanggal</th>
																			<th>Laku</th>
																			<th>Laku Kredit</th>
																			<th>T Laku</th>
																			<th>Nilai Laku</th>
																			<th>Nilai Kredit</th>
																			<th>Jumlah Nilai</th>
																			<th>Qty Refund</th>
																			<th>Total Refund</th>
																		</tr>
																	</thead>
																	<tbody>
																		@foreach ($hasilPenjualan as $item)
																			<tr>
																				<td>{{ $item->cnt ?? '' }}</td>
																				<td>{{ $item->na_cnt ?? '' }}</td>
																				<td>{{ $item->st_pjk ?? '' }}</td>
																				<td>{{ isset($item->tgl_jual) ? date('d/m/Y', strtotime($item->tgl_jual)) : '' }}</td>
																				<td class="text-right">{{ number_format($item->qcash ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->qkred ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->qjml ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->cash ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->kred ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->jml ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->qtyrf ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->totalrf ?? 0, 0, ',', '.') }}</td>
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
														<div class="col-md-2">						
															<label class="form-label">Counter</label>
															<input type="text" class="form-control CNT1" id="CNT1" name="CNT1" placeholder="Pilih Customer" value="{{ session()->get('filter_CNT1') }}" readonly>
														</div>  
														<div class="col-md-2">						
															<label class="form-label">s/d</label>
															<input type="text" class="form-control CNT2" id="CNT2" name="CNT2" placeholder="Pilih Customer" value="{{ session()->get('filter_CNT2') }}" readonly>
														</div>
														<div class="col-8">
															<button class="btn btn-primary mr-1" type="button" id="btnFilterKasir" onclick="filterPenjualan('kasir')">
																<i class="fas fa-search mr-1"></i>Filter
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
														@if (!empty($hasilPenjualan))
															<div class="table-responsive">
																<table id="tabelKasir" class="table table-striped table-bordered nowrap" style="width:100%">
																	<thead>
																		<tr>
																			<th>Tanggal</th>
																			<th>Counter</th>
																			<th>Nama Counter</th>
																			<th>Kodes</th>
																			<th>Qty</th>
																			<th>Bruto</th>
																			<th>Dis</th>
																			<th>Par</th>
																			<th>Dis Tiara</th>
																			<th>Dis Supp</th>
																			<th>Margin</th>
																			<th>Harga Jual</th>
																			<th>Nilai Margin</th>
																			<th>Nilai Nota</th>
																			<th>PPN</th>
																			<th>NETT</th>
																		</tr>
																	</thead>
																	<tbody>
																		@foreach ($hasilPenjualan as $item)
																			<tr>
																				<td>{{ isset($item->tgl_jual) ? date('d/m/Y', strtotime($item->tgl_jual)) : '' }}</td>
																				<td>{{ $item->cnt ?? '' }}</td>
																				<td>{{ $item->na_cnt ?? '' }}</td>
																				<td>{{ $item->kodes ?? '' }}</td>
																				<td class="text-right">{{ number_format($item->qty ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->tharga ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->dis ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->par ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->ptiara ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->psup ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->margin ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->nilai_jual ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->nilai_margin ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->nilai_nota ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->ppn ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->nett ?? 0, 0, ',', '.') }}</td>
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
															<button class="btn btn-primary mr-1" type="button" id="btnFilterRCounter" onclick="filterPenjualan('rconter')">
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
														@if (!empty($hasilPenjualan))
															<div class="table-responsive">
																<table id="tabelKasir" class="table table-striped table-bordered nowrap" style="width:100%">
																	<thead>
																		<tr>
																			<th>Tanggal</th>
																			<th>Counter</th>
																			<th>Nama Counter</th>
																			<th>Kodes</th>
																			<th>Qty</th>
																			<th>Bruto</th>
																			<th>Dis</th>
																			<th>Par</th>
																			<th>Dis Tiara</th>
																			<th>Dis Supp</th>
																			<th>Nilai jual</th>
																			<th>DPP</th>
																			<th>PPN</th>
																		</tr>
																	</thead>
																	<tbody>
																		@foreach ($hasilPenjualan as $item)
																			<tr>
																				<td>{{ isset($item->tgl_jual) ? date('d/m/Y', strtotime($item->tgl_jual)) : '' }}</td>
																				<td>{{ $item->cnt ?? '' }}</td>
																				<td>{{ $item->na_cnt ?? '' }}</td>
																				<td>{{ $item->kodes ?? '' }}</td>
																				<td class="text-right">{{ number_format($item->qty ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->tharga ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->dis ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->par ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->ptiara ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->psup ?? 0, 0, ',', '.') }}</td>\
																				<td class="text-right">{{ number_format($item->nilai_jual ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->nilai_nota ?? 0, 0, ',', '.') }}</td>
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

										<!-- Data Rekap Penjualan Tab -->
										<div class="tab-pane fade" id="rjual" role="tabpanel" aria-labelledby="rjual-tab">
											<div class="pt-3">
												<div class="form-group">
													<div class="row align-items-end mb-3">
														<div class="col-8">
															<button class="btn btn-primary mr-1" type="button" id="btnFilterRJual" onclick="filterPenjualan('rjual')">
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
														@if (!empty($hasilPenjualan))
															<div class="table-responsive">
																<table id="tabelKasir" class="table table-striped table-bordered nowrap" style="width:100%">
																	<thead>
																		<tr>
																			<th>Counter</th>
																			<th>Nama Counter</th>
																			<th>Yer</th>
																			<th>01</th>
																			<th>02</th>
																			<th>03</th>
																			<th>04</th>
																			<th>05</th>
																			<th>06</th>
																			<th>07</th>
																			<th>08</th>
																			<th>09</th>
																			<th>10</th>
																			<th>11</th>
																			<th>12</th>
																			<th>Jumlah</th>
																		</tr>
																	</thead>
																	<tbody>
																		@foreach ($hasilPenjualan as $item)
																			<tr>
																				{{-- <td>{{ isset($item->tgl_jual) ? date('d/m/Y', strtotime($item->tgl_jual)) : '' }}</td> --}}
																				<td>{{ $item->CNT ?? '' }}</td>
																				<td>{{ $item->NA_CNT ?? '' }}</td>
																				<td>{{ $item->YER ?? '' }}</td>
																				<td class="text-right">{{ number_format($item->B01 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->B02 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->B03 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->B04 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->B05 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->B06 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->B07 ?? 0, 0, ',', '.') }}</td>	
																				<td class="text-right">{{ number_format($item->B08 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->B09 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->B10 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->B11 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->B12 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->JUMB ?? 0, 0, ',', '.') }}</td>
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
															<button class="btn btn-primary mr-1" type="button" id="btnFilterRHari" onclick="filterPenjualan('rhari')">
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
														@if (!empty($hasilPenjualan))
															<div class="table-responsive">
																<table id="tabelKasir" class="table table-striped table-bordered nowrap" style="width:100%">
																	<thead>
																		<tr>
																			<th>Cabang</th>
																			<th>Cnt</th>
																			<th>Counter</th>
																			<th>Kode</th>
																			<th>Nama</th>
																			<th>01</th>
																			<th>02</th>
																			<th>03</th>
																			<th>04</th>
																			<th>05</th>
																			<th>06</th>
																			<th>07</th>
																			<th>08</th>
																			<th>09</th>
																			<th>10</th>
																			<th>11</th>
																			<th>12</th>
																			<th>13</th>
																			<th>14</th>
																			<th>15</th>
																			<th>16</th>
																			<th>17</th>
																			<th>18</th>
																			<th>19</th>
																			<th>20</th>
																			<th>21</th>
																			<th>22</th>
																			<th>23</th>
																			<th>24</th>
																			<th>25</th>
																			<th>26</th>
																			<th>27</th>
																			<th>28</th>
																			<th>29</th>
																			<th>30</th>
																			<th>31</th>
																			<th>Harga</th>
																			<th>Total</th>
																		</tr>
																	</thead>
																	<tbody>
																		@foreach ($hasilPenjualan as $item)
																			<tr>
																				{{-- <td>{{ isset($item->tgl_jual) ? date('d/m/Y', strtotime($item->tgl_jual)) : '' }}</td> --}}
																				<td>{{ $item->cbg ?? '' }}</td>
																				<td>{{ $item->cnt ?? '' }}</td>
																				<td>{{ $item->na_cnt ?? '' }}</td>
																				<td>{{ $item->kd_brg ?? '' }}</td>
																				<td>{{ $item->na_brg ?? '' }}</td>
																				<td class="text-right">{{ number_format($item->b01 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b02 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b03 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b04 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b05 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b06 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b07 ?? 0, 0, ',', '.') }}</td>	
																				<td class="text-right">{{ number_format($item->b08 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b09 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b10 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b11 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b12 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b13 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b14 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b15 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b16 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b17 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b18 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b19 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b20 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b21 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b22 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b23 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b24 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b25 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b26 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b27 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b28 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b29 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b30 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->b31 ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->hjual ?? 0, 0, ',', '.') }}</td>
																				<td class="text-right">{{ number_format($item->total ?? 0, 0, ',', '.') }}</td>
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
        localStorage.setItem('activePenjualanTab', $(e.target).attr('href'));
    });

    // Restore tab aktif
    var activeTab = localStorage.getItem('activePenjualanTab');
    if(activeTab){
        $('#reportTabs a[href="'+activeTab+'"]').tab('show');
    }

    // Auto format periode input
    $('#periode_detail, #periode_summary, #periode_kasir').on('input', function(){
        var value = this.value.replace(/\D/g,'');
        if(value.length>=2) this.value = value.substring(0,2)+'-'+value.substring(2,6);
    });

    // Inisialisasi DataTable awal (Detail)
    @if(!empty($hasilPenjualan))
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
function filterPenjualan(tabType){

    var cbg = $('#cbg').val();
    var per = $('#per').val();
    var CNT = $('#CNT').val();
    var tglDr = $('#tglDr').val();
    var tglSmp = $('#tglSmp').val();
	var CNT1 = '';
    var CNT2 = '';
    var btnId='';

    switch(tabType){
        case 'detail':
            btnId = '#btnFilterDetail';
            if(!cbg){ 
                alert('Pilih cabang terlebih dahulu'); 
                return; 
            }
        break;

        case 'summary':
            btnId = '#btnFilterSummary';
            if(!cbg){ 
                alert('Pilih cabang terlebih dahulu'); 
                return; 
            }
        break;

        case 'kasir':
            btnId = '#btnFilterKasir';
			CNT1 = $('#CNT1').val();
			CNT2 = $('#CNT2').val();
            if(!cbg){ 
                alert('Pilih cabang terlebih dahulu'); 
                return; 
            }
        break;

		case 'rconter':
            btnId = '#btnFilterRCounter';
            if(!cbg){ 
                alert('Pilih cabang terlebih dahulu'); 
                return; 
            }
        break;

		case 'rjual':
            btnId = '#btnFilterRJual';
            if(!cbg){ 
                alert('Pilih cabang terlebih dahulu'); 
                return; 
            }
        break;

		case 'rhari':
            btnId = '#btnFilterRHari';
            if(!cbg){ 
                alert('Pilih cabang terlebih dahulu'); 
                return; 
            }
        break;
    }

    $(btnId).html('<i class="fas fa-spinner fa-spin mr-1"></i>Loading...').prop('disabled',true);

    $.ajax({
        url: '{{ route("get-penjualan-report-ajax") }}',
        method: 'GET',
        data: { 
            tab: tabType,
            cbg: cbg,
            per: per,
            CNT: CNT,
            tglDr: tglDr,
            tglSmp: tglSmp,
			CNT1: CNT1,
			CNT2: CNT2
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
            html += '<th>Kitir SPM</th><th>Kitir BC</th><th>Tanggal</th><th>Kode Barang</th><th>Nama Barang</th><th>Qty</th><th>Harga</th><th>Total</th><th>Cabang</th><th>Kasir</th><th>Petugas Kasir</th><th>Post Status</th>';
        } else if(tabType==='summary'){
            html += '<th>CNT</th><th>Nama Counter</th><th>S. Pajak</th><th>Tanggal</th><th>Laku</th><th>Laku Kredit</th><th>T Laku</th><th>Nilai Laku</th><th>Nilai Kredit</th><th>Jumlah Nilai</th><th>Qty Refund</th><th>Total Refund</th>';
        } else if(tabType==='kasir'){
            html += '<th>Tanggal</th><th>Conter</th><th>Nama</th><th>Kodes</th><th>Qty</th><th>Bruto</th><th>Dis</th><th>Par</th><th>Dis Tiara</th><th>Dis Supp</th><th>Margin</th><th>Harga Jual</th><th>Nilai Margin</th><th>Nilai Nota</th><th>PPN</th><th>NETT</th>';
		} else if(tabType==='rconter'){
			html += '<th>Tanggal</th><th>Conter</th><th>Nama</th><th>Kodes</th><th>Qty</th><th>Bruto</th><th>Dis</th><th>Par</th><th>Dis Tiara</th><th>Dis Supp</th><th>Nilai jual</th><th>DPP</th><th>PPN</th>';
		} else if(tabType==='rjual'){
			html += '<th>Conter</th><th>Nama</th><th>YER</th><th>01</th><th>02</th><th>03</th><th>04</th><th>05</th><th>06</th><th>07</th><th>08</th><th>09</th><th>10</th><th>11</th><th>12</th><th>Total</th>';
		} else if(tabType==='rhari'){
			html += '<th>Cabang</th><th>Cnt</th><th>Counter</th><th>Kode</th><th>Nama</th><th>01</th><th>02</th><th>03</th><th>04</th><th>05</th><th>06</th><th>07</th><th>08</th><th>09</th><th>10</th><th>11</th><th>12</th><th>13</th><th>14</th><th>15</th><th>16</th><th>17</th><th>18</th><th>19</th><th>20</th><th>21</th><th>22</th><th>23</th><th>24</th><th>25</th><th>26</th><th>27</th><th>28</th><th>29</th><th>30</th><th>31</th><th>Harga</th><th>Total</th>';
		}
        html += '</tr></thead><tbody>';

        $.each(data,function(i,item){
            html += '<tr>';
            if(tabType==='detail'){
                html += '<td>'+item.no_bukti+'</td><td>'+item.bukti2+'</td><td>'+formatDate(item.initanggal)+'</td><td>'+item.KD_BRG+'</td><td>'+item.NA_BRG+'</td><td class="text-right">'+formatNumber(item.qty)+'</td><td class="text-right">'+formatNumber(item.harga)+'</td><td class="text-right">'+formatNumber(item.total)+'</td><td>'+item.cabang+'</td><td>'+item.KSR+'</td><td>'+item.usrnm+'</td><td>'+item.posted+'</td>';
            } else if(tabType==='summary'){
                html += '<td>'+item.cnt+'</td><td>'+item.na_cnt+'</td><td>'+item.st_pjk+'</td><td>'+formatDate(item.tgl_jual)+'</td><td class="text-right">'+formatNumber(item.qcash)+'</td><td class="text-right">'+formatNumber(item.qkred)+'</td><td class="text-right">'+formatNumber(item.qjml)+'</td><td class="text-right">'+formatNumber(item.cash)+'</td><td class="text-right">'+formatNumber(item.kred)+'</td><td class="text-right">'+formatNumber(item.jml)+'</td><td class="text-right">'+formatNumber(item.qtyrf)+'</td><td class="text-right">'+formatNumber(item.totalrf)+'</td>';
            } else if(tabType==='kasir'){
                html += '<td>'+formatDate(item.tgl_jual)+'</td><td>'+item.cnt+'</td><td>'+item.na_cnt+'</td><td>'+item.kodes+'</td><td class="text-right">'+formatNumber(item.qty)+'</td><td class="text-right">'+formatNumber(item.tharga)+'</td><td class="text-right">'+formatNumber(item.dis)+'</td><td class="text-right">'+formatNumber(item.par)+'</td><td class="text-right">'+formatNumber(item.ptiara)+'</td><td class="text-right">'+formatNumber(item.psup)+'</td><td class="text-right">'+formatNumber(item.margin)+'</td><td class="text-right">'+formatNumber(item.nilai_jual)+'</td><td class="text-right">'+formatNumber(item.nilai_margin)+'</td><td class="text-right">'+formatNumber(item.nilai_nota)+'</td><td class="text-right">'+formatNumber(item.ppn)+'</td><td class="text-right">'+formatNumber(item.nett)+'</td>';
			} else if(tabType==='rconter'){
				html += '<td>'+formatDate(item.tgl_jual)+'</td><td>'+item.cnt+'</td><td>'+item.na_cnt+'</td><td>'+item.kodes+'</td><td class="text-right">'+formatNumber(item.qty)+'</td><td class="text-right">'+formatNumber(item.tharga)+'</td><td class="text-right">'+formatNumber(item.dis)+'</td><td class="text-right">'+formatNumber(item.par)+'</td><td class="text-right">'+formatNumber(item.ptiara)+'</td><td class="text-right">'+formatNumber(item.psup)+'</td><td class="text-right">'+formatNumber(item.nilai_jual)+'</td><td class="text-right">'+formatNumber(item.DPP)+'</td><td class="text-right">'+formatNumber(item.PPN)+'</td>';
			} else if(tabType==='rjual'){
				html += '<td>'+item.CNT+'</td><td>'+item.NA_CNT+'</td><td>'+item.YER+'</td><td class="text-right">'+formatNumber(item.B01)+'</td><td class="text-right">'+formatNumber(item.B02)+'</td><td class="text-right">'+formatNumber(item.B03)+'</td><td class="text-right">'+formatNumber(item.B04)+'</td><td class="text-right">'+formatNumber(item.B05)+'</td><td class="text-right">'+formatNumber(item.B06)+'</td><td class="text-right">'+formatNumber(item.B07)+'</td><td class="text-right">'+formatNumber(item.B08)+'</td><td class="text-right">'+formatNumber(item.B09)+'</td><td class="text-right">'+formatNumber(item.B10)+'</td><td class="text-right">'+formatNumber(item.B11)+'</td><td class="text-right">'+formatNumber(item.B12)+'</td><td class="text-right">'+formatNumber(item.JUMB)+'</td>';
			} else if(tabType==='rhari'){
				html += '<td>'+item.cbg+'</td><td>'+item.cnt+'</td><td>'+item.na_cnt+'</td><td>'+item.kd_brg+'</td><td>'+item.na_brg+'</td><td class="text-right">'+formatNumber(item.b01)+'</td><td class="text-right">'+formatNumber(item.b02)+'</td><td class="text-right">'+formatNumber(item.b03)+'</td><td class="text-right">'+formatNumber(item.b04)+'</td><td class="text-right">'+formatNumber(item.b05)+'</td><td class="text-right">'+formatNumber(item.b06)+'</td><td class="text-right">'+formatNumber(item.b07)+'</td><td class="text-right">'+formatNumber(item.b08)+'</td><td class="text-right">'+formatNumber(item.b09)+'</td><td class="text-right">'+formatNumber(item.b10)+'</td><td class="text-right">'+formatNumber(item.b11)+'</td><td class="text-right">'+formatNumber(item.b12)+'</td><td class="text-right">'+formatNumber(item.b13)+'</td><td class="text-right">'+formatNumber(item.b14)+'</td><td class="text-right">'+formatNumber(item.b15)+'</td><td class="text-right">'+formatNumber(item.b16)+'</td><td class="text-right">'+formatNumber(item.b17)+'</td><td class="text-right">'+formatNumber(item.b18)+'</td><td class="text-right">'+formatNumber(item.b19)+'</td><td class="text-right">'+formatNumber(item.b20)+'</td><td class="text-right">'+formatNumber(item.b21)+'</td><td class="text-right">'+formatNumber(item.b22)+'</td><td class="text-right">'+formatNumber(item.b23)+'</td><td class="text-right">'+formatNumber(item.b24)+'</td><td class="text-right">'+formatNumber(item.b25)+'</td><td class="text-right">'+formatNumber(item.b26)+'</td><td class="text-right">'+formatNumber(item.b27)+'</td><td class="text-right">'+formatNumber(item.b28)+'</td><td class="text-right">'+formatNumber(item.b29)+'</td><td class="text-right">'+formatNumber(item.b30)+'</td><td class="text-right">'+formatNumber(item.b31)+'</td><td class="text-right">'+formatNumber(item.hjual)+'</td><td class="text-right">'+formatNumber(item.total)+'</td>';
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
			$('#cbg').val('');
			$('#per').val('');
            $('#periode').val('');
            $('#CNT').val('');
            $('#NA_CNT').val('');
            $('#tglDr').val('');
			$('#tglSmp').val('');
            break;
        case 'summary':
			$('#cbg').val('');
			$('#per').val('');
            $('#periode').val('');
            $('#CNT').val('');
            $('#NA_CNT').val('');
            $('#tglDr').val('');
			$('#tglSmp').val('');
            break;
        case 'kasir':
            $('#cbg').val('');
			$('#per').val('');
            $('#periode').val('');
            $('#CNT').val('');
            $('#NA_CNT').val('');
            $('#tglDr').val('');
			$('#tglSmp').val('');
			$('#CNT1').val('');
			$('#CNT2').val('');
            break;
		case 'rconter':
			$('#cbg').val('');
			$('#per').val('');
			$('#periode').val('');
			$('#CNT').val('');
			$('#NA_CNT').val('');
			$('#tglDr').val('');
			$('#tglSmp').val('');
			break;
		case 'rjual':
			$('#cbg').val('');
			$('#per').val('');
			$('#periode').val('');
			$('#CNT').val('');
			$('#NA_CNT').val('');
			$('#tglDr').val('');
			$('#tglSmp').val('');
			break;
		case 'rhari':
			$('#cbg').val('');
			$('#per').val('');
			$('#periode').val('');
			$('#CNT').val('');
			$('#NA_CNT').val('');
			$('#tglDr').val('');
			$('#tglSmp').val('');
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
			var cbg = $('#cbg_kasir').val();

			if (!cbg) {
				alert('Silakan lengkapi Cabang terlebih dahulu');
				return;
			}

			var params = new URLSearchParams({
				report_type: 1,
				cbg: cbg,
			});

			var url = '{{ route('jasper-penjualan-report') }}?' + params.toString();
			printReport(url);
}

function cetakDetail() {
			var cbg = $('#cbg_detail').val();

			if (!cbg) {
				alert('Silakan lengkapi Cabang terlebih dahulu');
				return;
			}

			var params = new URLSearchParams({
				report_type: 1,
				cbg: cbg,
			});

			var url = '{{ route('jasper-penjualandetail-report') }}?' + params.toString();
			printReport(url);
}

function cetakSummary() {
			var cbg = $('#cbg_summary').val();

			if (!cbg) {
				alert('Silakan lengkapi Cabang terlebih dahulu');
				return;
			}

			var params = new URLSearchParams({
				report_type: 1,
				cbg: cbg,
			});

			var url = '{{ route('jasper-penjualansummary-report') }}?' + params.toString();
			printReport(url);
}


</script>
@endsection