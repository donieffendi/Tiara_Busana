@extends('layouts.plain')
<style>
    .bigdrop {
        width: 410px !important;
    }
</style>
@section('content')
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Laporan Perubahan Harga Beli Disc Budget</h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item active">Laporan Perubahan Harga Beli Disc Budget</li>
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
							<form method="POST" action="{{url('jasper-rubahharga_discbudget-report')}}">
							@csrf

							<div class="form-group row">
								<div class="col-md-2">
									<label><strong>Cabang :</strong></label>
									<select name="cbg" id="cbg" class="form-control cbg" style="width: 200px">
										<option value="">--Pilih Cabang--</option>
										@foreach($cbg as $cbgD)
											<option value="{{$cbgD->KODE}}"  {{ (session()->get('filter_cbg') == $cbgD->KODE) ? 'selected' : '' }}>{{$cbgD->KODE}}</option>
										@endforeach
									</select>
								</div>
							</div>

                            <!-- Filter Tanggal -->
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
							</div>


							{{-- <div class="form-group row">
								<div class="col-md-2">
									<label><strong>P.L.U :</strong></label>
									<input type="text" class="form-control kode1" id="kode1" name="kode1" placeholder="Pilih Barang" value="{{ session()->get('filter_kode1') }}" readonly>
								</div>
								<div class="col-md-3">
									<label><strong>Nama :</strong></label>
									<input type="text" class="form-control nama1" id="nama1" name="nama1" placeholder="Nama Barang" value="{{ session()->get('filter_nama1') }}" readonly>
								</div>
							</div> --}}

							<button class="btn btn-primary" type="submit" id="filter" class="filter" name="filter">Filter</button>
							<button class="btn btn-danger" type="button" id="resetfilter" class="resetfilter" onclick="window.location='{{url("rubahharga_discbudget")}}'">Reset</button>
							<button class="btn btn-warning" type="submit" id="cetak" class="cetak" formtarget="_blank">Cetak</button>
							</form>
							<div style="margin-bottom: 15px;"></div>

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
											"ROWNUM" => array(
												"label" => "No",
												"value" => function($row, $index) {
													return $index + 1;
												}
											),
											"NO_BUKTI" => array(
												"label" => "No. Agenda",
											),
											"TGL" => array(
												"label" => "Tanggal",
												"type" => "date",
												"format" => "Y-m-d",
												"displayFormat" => "d-m-Y",
											),
											"NO_SP" => array(
												"label" => "No. SP",
											),
											"KODES" => array(
												"label" => "Kode Supplier",
											),
											"NAMAS" => array(
												"label" => "Nama Supplier",
											),
											"KDBAR" => array(
												"label" => "P.L.U",
											),
											"NMBAR" => array(
												"label" => "Nama Barang",
											),
											"HARGA" => array(
												"label" => "Perubahan Harga",
												"type" => "number",
												"decimals" => 2,
												"decimalPoint" => ".",
												"thousandSeparator" => ",",
												"footer" => "sum",
												"footerText" => "<b>@value</b>",
											),
											"KET" => array(
												"label" => "Keterangan",
											)
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
													"targets" => [8],
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

<div class="modal fade" id="browseBrgModal" tabindex="-1" role="dialog" aria-labelledby="browseBrgModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="browseBrgModalLabel">Cari Barang</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table class="table table-stripped table-bordered" id="table-brg">
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
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>
	$(document).ready(function() {
		//select2_no_so();

		$('.date').datepicker({
			dateFormat: 'dd-mm-yy'
		});

		var dTableBrg;
		loadDataBrg = function(indeks){

			$.ajax(
			{
				type: 'GET',
				url: "{{url('brg/browse_plu')}}",

				success: function( response )
				{
					resp = response;
					if(dTableBrg){
						dTableBrg.clear();
					}
					for(i=0; i<resp.length; i++){

						dTableBrg.row.add([
							'<a href="javascript:void(0);" onclick="chooseBrg(\''+resp[i].KDBAR+'\',  \''+resp[i].NMBAR+'\')">'+resp[i].KDBAR+'</a>',
							resp[i].NMBAR,
						]);
					}
					dTableBrg.draw();
				}
			});
		}

		dTableBrg = $("#table-brg").DataTable({

		});

		browseBrg = function(indeks){
			loadDataBrg(indeks);
			$("#browseBrgModal").modal("show");
		}

		chooseBrg = function(KDBAR, NMBAR){
			$("#kode1").val(KDBAR);
			$("#nama1").val(NMBAR);
			$("#browseBrgModal").modal("hide");
		}

		$("#kode1").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseBrg(1);
			}
		});
	});


    function select2_no_so() {
        $('#no_so1').select2({
            ajax: {
                url: "{{ url('so/get-select-so') }}",
                dataType: "json",
                type: "GET",
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page
                    }
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: data.total_count
                        }
                    };
                },
                cache: true
            },
			allowClear: true,
            dropdownCssClass: "bigdrop",
            // dropdownAutoWidth: true,
            placeholder: 'Pilih SO# ...',
            minimumInputLength: 0,
            templateResult: format,
            templateSelection: formatSelection,
            theme: "classic",
        });
    }

    function format(repo) {
        if (repo.loading) {
            return repo.text;
        }

        var $container = $(
            "<div class='select2-result-repository clearfix text_input'>" +
            "<div class='select2-result-repository__title text_input'></div>" +
            "</div>"
        );

        $container.find(".select2-result-repository__title").text(repo.text);
        return $container;
    }

    function formatSelection(repo) {
        return repo.text;
    }
</script>
@endsection
