	@extends('layouts.plain')
	@section('styles')
	<link rel="stylesheet" href="{{asset('foxie_js_css/jquery.dataTables.min.css')}}" />
	@endsection

	<style>
		.card {
			padding: 5px 10px !important;
		}

		.table thead {
			background-color: #FFFFFF;
			color: #000000;
		}

		.datatable tbody td {
			padding: 5px !important;
			background-color: #FFFFFF;
		}

		.datatable {
			border-right: solid 2px #000;
			border-left: solid 2px #000;
		}

		.btn-secondary {
			background-color: #42047e !important;
		}

		/* DataTables pagination styling */
		.dataTables_wrapper .dataTables_paginate {
			float: right;
			text-align: right;
			padding-top: 0.25em;
		}

		.dataTables_wrapper .dataTables_paginate .paginate_button {
			box-sizing: border-box;
			display: inline-block;
			min-width: 1.5em;
			padding: 0.5em 1em;
			margin-left: 2px;
			text-align: center;
			text-decoration: none !important;
			cursor: pointer;
			border: 1px solid transparent;
			border-radius: 2px;
		}

		.dataTables_wrapper .dataTables_length,
		.dataTables_wrapper .dataTables_filter,
		.dataTables_wrapper .dataTables_info,
		.dataTables_wrapper .dataTables_paginate {
			color: #333;
		}

		th {
			font-size: 12px;
		}

		td {
			font-size: 12px;
		}

		.content-header {
			padding: 0 !important;
		}

		.badge-warning {
			background-color: #5a01d5 !important;
			color: white !important;
		}

		.badge-success {
			background-color: #068f3f !important;
			color: white !important;
		}
	</style>

	@section('content')
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<div class="content-wrapper">
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">

				</div>
			</div>
		</div>

		<div class="content">
			<div class="container-fluid">
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-body">

								<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#columnModal">
									Filter Columns
								</button>

								<!-- Modal Filter Columns -->
								<div class="modal fade" id="columnModal" tabindex="-1" aria-labelledby="columnModalLabel" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="columnModalLabel">Toggle Columns</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">
												<form id="columnToggleForm">
													<div class="form-check">
														<input class="form-check-input column-checkbox" type="checkbox" value="0" id="columnDetail" checked>
														<label class="form-check-label" for="columnDetail">Detail</label>
													</div>
													<div class="form-check">
														<input class="form-check-input column-checkbox" type="checkbox" value="1" id="columnNo" checked>
														<label class="form-check-label" for="columnNo">No</label>
													</div>
													<div class="form-check">
														<input class="form-check-input column-checkbox" type="checkbox" value="2" id="columnAction" checked>
														<label class="form-check-label" for="columnAction">Action</label>
													</div>

													<div class="form-check">
														<input class="form-check-input column-checkbox" type="checkbox" value="3" id="columnBukti" checked>
														<label class="form-check-label" for="columnBukti">No Beli</label>
													</div>
													<div class="form-check">
														<input class="form-check-input column-checkbox" type="checkbox" value="4" id="columnTgl" checked>
														<label class="form-check-label" for="columnTgl">Tgl</label>
													</div>
													<div class="form-check">
														<input class="form-check-input column-checkbox" type="checkbox" value="5" id="columnSup" checked>
														<label class="form-check-label" for="columnSup">Supplier#</label>
													</div>
													<div class="form-check">
														<input class="form-check-input column-checkbox" type="checkbox" value="6" id="columnNama" checked>
														<label class="form-check-label" for="columnNama">Nama Supplier</label>

													</div>
													<div class="form-check">
														<input class="form-check-input column-checkbox" type="checkbox" value="7" id="columnKet" checked>
														<label class="form-check-label" for="columnKet">Keterangan</label>
													</div>
													<div class="form-check">
														<input class="form-check-input column-checkbox" type="checkbox" value="8" id="columnUser" checked>
														<label class="form-check-label" for="columnUser">User</label>
													</div>
													<div class="form-check">
														<input class="form-check-input column-checkbox" type="checkbox" value="9" id="columnPost" checked>
														<label class="form-check-label" for="columnPost">Posted</label>
													</div>
													<div class="form-check">
														<input class="form-check-input column-checkbox" type="checkbox" value="10" id="columnSelesai" checked>
														<label class="form-check-label" for="columnSelesai">Selesai</label>
													</div>
												</form>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
												<button type="button" class="btn btn-primary" id="applyColumnToggle">Apply</button>
											</div>
										</div>
									</div>
								</div>

								<table class="table table-fixed table-striped table-border table-hover nowrap datatable" id="datatable">
									<thead class="table-dark">
										<tr>
											<th scope="col" style="text-align: center"></th>
											<th scope="col" style="text-align: center">#</th>
											<th scope="col" style="text-align: center">Action</th>
											<th scope="col" style="text-align: center">No Bukti</th>
											<th scope="col" style="text-align: center">Supplier#</th>
											<th scope="col" style="text-align: center">Nama Supplier</th>
											<th scope="col" style="text-align: center">Harga</th>
											<th scope="col" style="text-align: center">Disc</th>
											<th scope="col" style="text-align: center">Disc 2</th>
											<th scope="col" style="text-align: center">Disc 3</th>
											<th scope="col" style="text-align: center">Disc 4</th>
											<th scope="col" style="text-align: center">PPN</th>
											<th scope="col" style="text-align: center">Posted</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Detail -->
	<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="detailModalLabel">Detail Perubahan Harga</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<table class="table table-striped table-bordered" id="table-detail">
						<thead>
							<tr>
								<th>No.</th>
								<th>Kode Barang</th>
								<th>Nama Barang</th>
								<th>Qty</th>
								<th>Harga Lama</th>
								<th>Harga Baru</th>
								<th>Disk Lama</th>
								<th>Disk Baru</th>
								<th>Disk2 Lama</th>
								<th>Disk2 Baru</th>
								<th>Disk3 Lama</th>
								<th>Disk3 Baru</th>
								<th>Disk4 Lama</th>
								<th>Disk4 Baru</th>
								<th>Total</th>
								<th>Keterangan</th>
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

	@section('footer-scripts')
	<script src="{{asset('foxie_js_css/jquery.dataTables.min.js')}}"></script>
	<script src="{{asset('foxie_js_css/dataTables.bootstrap4.min.js')}}"></script>
	<script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js"></script>

	<script>
		$(function() {
			// Initialize DataTable
			var table = $('#datatable').DataTable({
				"responsive": true,
				"lengthChange": true,
				"autoWidth": false,
				"searching": true,
				"serverSide": true,
				"paging": true,
				"pageLength": 25,
				"lengthMenu": [10, 25, 50, 100],
				"ajax": {
					"url": "{{ route('hps_hrgBeli.browse') }}",
					"type": "GET",
					"data": function(d) {
					
						
					}
				},
				"columns": [{
					
						data: null, // Column for the button
						orderable: false,
						searchable: false,
						className: "text-center",
						render: function(data, type, row, meta) {

							// kalau ada query POST di bagian paling atas, pada onclick perlu di tambah "event.preventDefault()"
							return `<button class="btn btn-success btn-sm toggle-button" data-no_bukti="${row.NO_BELI}" onclick="event.preventDefault();toggleButton(this)">+</button>`;
						}
					},
					{
						"data": null,
						"className": "text-center",
						"orderable": false,
						"render": function(data, type, row, meta) {
							return meta.row + meta.settings._iDisplayStart + 1;
						}
					}, {
						"data": "action",
						"className": "text-center"
					},

					{
						"data": "NO_BUKTI",
						"className": "text-center"
					},
					{
						"data": "KODES",
						"className": "text-center"
					},
					{
						"data": "NAMAS",
						"className": "text-left",
						"render": function(data, type, row, meta) {
							// Bisa pakai badge-warning atau badge-success sesuai kebutuhan
							// Misal semua badge warning:
							return `<span class="badge badge-warning badge-pill">${data}</span>`;
						}
					},
					{
						"data": "HARGA",
						"className": "text-right"
					},
					{
						"data": "DISC",
						"className": "text-right"
					},
					{
						"data": "DISC2",
						"className": "text-right"
					}, 
					{
						"data": "DISC3",
						"className": "text-right"
					},
					{
						"data": "DISC4",
						"className": "text-right"
					},
					{
						"data": "PPN",
						"className": "text-right"
					},
					{
						data: 'POSTED',
						name: 'POSTED',
						render: function(data, type, row, meta) {

							if (row['POSTED'] == "1") {
								return '<input type="checkbox" checked style="pointer-events: none;">';
							} else {
								return '';
							}
						}
					},
					
				],
				"columnDefs": [{
					"className": "dt-center",
					"targets": [0, 1, 2, 3,12]
				}, ],
				"lengthMenu": [
					[8, 10, 20, 50, 100, -1],
					[8, 10, 20, 50, 100, "All"]
				],
				"dom": "<'row'<'col-md-6'><'col-md-6'>>" +
					"<'row'<'col-md-2'l><'col-md-6 test_btn m-auto'><'col-md-4'f>>" +
					"<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
				"stateSave": true
			});

			// Filter button event
			$('#filterBtn').click(function() {
				table.ajax.reload();
			});

			// Modal close handlers
			$(document).on('click', '[data-dismiss="modal"]', function() {
				$(this).closest('.modal').modal('hide');
			});
			$(document).ready(function() {
				$("div.test_btn").html(
					`<a class="btn btn-lg btn-md btn-success" href="{{url('hps_hrgBeli/edit?idx=0&tipx=new')}}"> <i class="fas fa-plus fa-sm md-3" ></i></a> `
				);
			});
			// Column toggle functionality
			$('#applyColumnToggle').click(function() {
				console.log('Apply column toggle clicked');
				console.log('Table object:', table);
				console.log('Checkboxes found:', $('.column-checkbox').length);

				$('.column-checkbox').each(function() {
					var column = table.column($(this).val());
					console.log('Setting column', $(this).val(), 'visible:', $(this).is(':checked'));
					column.visible($(this).is(':checked'));
				});
				$('#columnModal').modal('hide');
			});

			// Period input mask
			$('#periode').mask('99/9999', {
				placeholder: 'MM/YYYY'
			});

			// Number format function
			function numberFormat(num, decimals) {
				if (num === null || num === undefined || num === '') return '0.00';
				var number = parseFloat(num);
				if (isNaN(number)) return '0.00';
				return number.toLocaleString('en-US', {
					minimumFractionDigits: decimals || 2,
					maximumFractionDigits: decimals || 2
				});
			}
		});

		// Global number format function
		function numberFormatGlobal(num, decimals) {
			if (num === null || num === undefined || num === '') return '0.00';
			var number = parseFloat(num);
			if (isNaN(number)) return '0.00';

			// Test console log
			console.log('Formatting number:', num, 'Result:', number.toLocaleString('en-US', {
				minimumFractionDigits: decimals || 2,
				maximumFractionDigits: decimals || 2
			}));

			return number.toLocaleString('en-US', {
				minimumFractionDigits: decimals || 2,
				maximumFractionDigits: decimals || 2
			});
		}

		// Toggle button function for expanding/collapsing detail rows
		function toggleButton(button) {
			const no_bukti = $(button).data('no_bukti');

			if (button.innerText === '+') {
				button.innerText = '-';
				button.classList.remove('btn-success');
				button.classList.add('btn-danger');

				// Fetch and show detail data
				$.ajax({
					url: "{{ route('hps_hrgBeli.browse_detail') }}",
					method: 'GET',
					data: {
						no_bukti: no_bukti
					},
					success: function(response) {
						// let totalQty = 0;
						let totalHargaLama = 0;
						let totalHargaBaru = 0;
						// let totalGrandTotal = 0;

						let detailHtml = `
							<div class="p-3">
								<table class="table table-bordered table-sm">
									<thead class="table-light">
										<tr>
											<th>No.</th>
											<th>Kode Barang</th>
											<th>Nama Barang</th>
											<th>Harga Lama</th>
											<th>Harga Baru</th>
											<th>Disk Lama</th>
											<th>Disk Baru</th>
											<th>Disk2 Lama</th>
											<th>Disk2 Baru</th>
											<th>Disk3 Lama</th>
											<th>Disk3 Baru</th>
											<th>Disk4 Lama</th>
											<th>Disk4 Baru</th>
											<th>PPN Lama</th>
											<th>PPN Baru</th>
										</tr>
									</thead>
									<tbody>
						`;

						if (response.data && response.data.length > 0) {
							response.data.forEach((item, index) => {
								// totalQty += parseFloat(item.QTY || 0);
								totalHargaLama += parseFloat(item.HARGALAMA || 0);
								totalHargaBaru += parseFloat(item.HARGA || 0);
								// totalGrandTotal += parseFloat(item.TOTAL || 0);

								detailHtml += `
									<tr>
										<td><div style="background-color: #f7d8b4; padding: 0.5rem;">${index + 1}</div></td>
										<td><div style="background-color: #f7d8b4; padding: 0.5rem;">${item.KD_BRG || ''}</div></td>
										<td><div style="background-color: #f7d8b4; padding: 0.5rem;">${item.NA_BRG || ''}</div></td>
										<td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.HARGALAMA || 0, 2)}</div></td>
										<td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.HARGA || 0, 2)}</div></td>
										<td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.DISKLAMA || 0, 2)}</div></td>
										<td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.DISK || 0, 2)}</div></td>
										<td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.DISKLAMA2 || 0, 2)}</div></td>
										<td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.DISK2 || 0, 2)}</div></td>
										<td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.DISKLAMA3 || 0, 2)}</div></td>
										<td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.DISK3 || 0, 2)}</div></td>
										<td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.DISKLAMA4 || 0, 2)}</div></td>
										<td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.DISK4 || 0, 2)}</div></td>
										<td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.PPNLAMA || 0, 2)}</div></td>
										<td><div style="background-color: #f7d8b4; padding: 0.5rem; text-align: right">${numberFormatGlobal(item.PPN || 0, 2)}</div></td>
									</tr>
								`;
							});
						} else {
							detailHtml += `
								<tr>
									<td colspan="16" style="text-align: center;"><div style="background-color: #f7d8b4; padding: 0.5rem;">Tidak ada data detail perubahan harga</div></td>
								</tr>
							`;
						}

						detailHtml += `
								
								</tbody>
							</table>
						</div>
						`;

						var detailRow = `<tr class="detail-row"><td colspan="10">${detailHtml}</td></tr>`;
						$(button).closest('tr').after(detailRow);
					},
					error: function() {
						var errorRow = `<tr class="detail-row"><td colspan="10"><div class="p-3 text-center text-danger"><strong>Error loading detail data</strong></div></td></tr>`;
						$(button).closest('tr').after(errorRow);
					}
				});
			} else {
				button.innerText = '+';
				button.classList.remove('btn-danger');
				button.classList.add('btn-success');
				$(button).closest('tr').next('.detail-row').remove();
			}
		}
	</script>
	@endsection