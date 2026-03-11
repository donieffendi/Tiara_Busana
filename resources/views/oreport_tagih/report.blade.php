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
					<h1 class="m-0">Laporan Barang Macet </h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item active">Laporan Barang Macet </li>
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
							<form method="POST" action="{{url('jasper-tagih-report')}}">
								@csrf

								<div class="form-group row">
									<div class="col-md-1" align="right">
										<label class="form-label">Kode Conter</label>
									</div>
									<div class="col-md-3">
										<input type="text" class="form-control CNT" id="CNT" name="CNT" placeholder="Pilih Conter" value="{{ session()->get('filter_CNT') }}" readonly>
									</div>  
									<div class="col-md-1" align="right">
										<label class="form-label">Nama Conter</label>
									</div>
									<div class="col-md-3">
										<input type="text" class="form-control NA_CNT" id="NA_CNT" name="NA_CNT" placeholder="Nama Conter" value="{{ session()->get('filter_NA_CNT') }}" readonly>
									</div>
								</div>

								<div class="form-group row">
									<div class="col-md-1" align="right">
										<label><strong>Outlet :</strong></label>
									</div>
									<div class="col-md-3">
											<select name="cbg" id="cbg" class="form-control cbg" style="width: 200px">
												<option value="">--Pilih Cabang--</option>
											@foreach($cbg as $cbgD)
												<option value="{{$cbgD->KODE}}"  {{ (session()->get('filter_cbg') == $cbgD->KODE) ? 'selected' : '' }}>{{$cbgD->KODE}}</option>
											@endforeach
										</select>
									</div>
									<div class="col-md-1">
										<input type="hidden" name="stok" value="0">
										<input type="checkbox" class="form-check-input" id="stok" name="stok" value="1" {{ session()->get('filter_stok') ? 'checked' : '' }}>
										<label class="form-check-label" for="stok"><strong>Ada Stok</strong></label>
									</div>
								</div>
								
								<button class="btn btn-primary" type="submit" id="filter" class="filter" name="filter">Filter</button>
								<button class="btn btn-danger" type="button" id="resetfilter" class="resetfilter" onclick="window.location='{{url("rtagih")}}'">Reset</button>
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
										"CBG" => array(
											"label" => "Outlet",
										),
										"KD_BRG" => array(
											"label" => "Kode Barang",
										),
										"NA_BRG" => array(
											"label" => "Nama Barang",
										),
										"BARCODE" => array(
											"label" => "Barcode",
										),
										"TGL_TRM" => array(
											"label" => "Tanggal",
											"type" => "datetime",
											"format" => "Y-m-d",
											"displayFormat" => "d-m-Y",
										),
										"TGL_JUAL" => array(
											"label" => "Tanggal",
											"type" => "datetime",
											"format" => "Y-m-d",
											"displayFormat" => "d-m-Y",
										),
										"BLN" => array(
											"label" => "Bulan",
										),
										"LAKU" => array(
											"label" => "%Laku",
											"type" => "number",
											"decimals" => 2,
											"decimalPoint" => ".",
											"thousandSeparator" => ",",
											"footer" => "sum",
											"footerText" => "<b>@value</b>",
										),
										"STOK" => array(
											"label" => "Stok Akhir",
											"type" => "number",
											"decimals" => 2,
											"decimalPoint" => ".",
											"thousandSeparator" => ",",
											"footer" => "sum",
											"footerText" => "<b>@value</b>",
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
												"targets" => [7,8],
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
	
	});
	
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

/////////////////////////////////////////////////////////////////////

	
var dTableCust2;
	loadDataCust2 = function(){
	
		$.ajax(
		{
			type: 'GET', 		
			url: "{{url('cust/browse')}}",
			data: {
				'GOL': $('#gol').val(),
			},
			success: function( response )
			{
				resp = response;
				if(dTableCust2){
					dTableCust2.clear();
				}
				for(i=0; i<resp.length; i++){
					
					dTableCust2.row.add([
						'<a href="javascript:void(0);" onclick="chooseCust2(\''+resp[i].KODEC+'\')">'+resp[i].KODEC+'</a>',
						resp[i].NAMAC,
						resp[i].ALAMAT,
						resp[i].KOTA,
					]);
				}
				dTableCust2.draw();
			}
		});
	}
	
	dTableCust2 = $("#table-cust2").DataTable({
		
	});
	
	browseCust2 = function(){
		loadDataCust2();
		$("#browseCust2Modal").modal("show");
	}
	
	chooseCust2 = function(KODEC){
		$("#kodec2").val(KODEC);
		// $("#NAMAC").val(NAMAC);	
		$("#browseCust2Modal").modal("hide");
	}
	
	$("#kodec2").keypress(function(e){
		if(e.keyCode == 46){
			e.preventDefault();
			browseCust2();
		}
	});

///////////////////////////////////////////////////////////////////
	
	var dTableBGudang;
	var rowidGudang;
	loadDataBGudang = function(){
		$.ajax(
		{
			type: 'GET',    
			url: "{{url('gdg/browse')}}",
			success: function(resp)
			{
				if(dTableBGudang){
					dTableBGudang.clear();
				}
				for(i=0; i<resp.length; i++){
					
					dTableBGudang.row.add([
						'<a href="javascript:void(0);" onclick="chooseGudang(\''+resp[i].KODE+'\',  \''+resp[i].NAMA+'\' )">'+resp[i].KODE+'</a>',
						resp[i].NAMA,
						
					]);
				}
				dTableBGudang.draw();
			}
		});
	}
	
	dTableBGudang = $("#table-bgudang").DataTable({
		
	});
	
	browseGudang = function(){
		loadDataBGudang();
		$("#browseGudangModal").modal("show");
	}
	
	chooseGudang = function(KODE,NAMA ){
		$("#kdgd1").val(KODE);		
		$("#browseGudangModal").modal("hide");
	}
	
	$("#kdgd1").keypress(function(e){
		if(e.keyCode == 46){
			e.preventDefault();
			browseGudang();
		}
	}); 

	
    var dTableBrg;
    loadDataBrg = function(indeks){
    
        $.ajax(
        {
            type: 'GET', 		
            url: "{{url('brg/browse')}}",
            data: {
                'GOL': 'Y',
            },
            success: function( response )
            {
                resp = response;
                if(dTableBrg){
                    dTableBrg.clear();
                }
                for(i=0; i<resp.length; i++){
                    
                    dTableBrg.row.add([
                        '<a href="javascript:void(0);" onclick="chooseBrg(\''+resp[i].KD_BRG+'\',  \''+resp[i].NA_BRG+'\', \''+indeks+'\')">'+resp[i].KD_BRG+'</a>',
                        resp[i].NA_BRG,
                        resp[i].SATUAN,
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
    
    chooseBrg = function(KD_BRG, NA_BRG, indeks){
        $("#brg"+indeks).val(KD_BRG);
        $("#nabrg"+indeks).val(NA_BRG);	
        $("#browseBrgModal").modal("hide");
    }
    
    $("#brg1").keypress(function(e){
        if(e.keyCode == 46){
            e.preventDefault();
            browseBrg(1);
        }
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