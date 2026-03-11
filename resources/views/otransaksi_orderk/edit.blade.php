@extends('layouts.main')

<style>
    .card {

    }

    .form-control:focus {
        background-color: #E0FFFF !important;
    }

	.table-scrollable {
		margin: 0;
		padding: 0;
	}

	table {
		table-layout: fixed !important;
	}

	.uppercase {
		text-transform: uppercase;
	}
</style>

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">

        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="content">
        <div class="container-fluid">
        <div class="row">
            <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <form action="{{($tipx=='new')? url('/orderk/store?flagz='.$flagz.'&golz='.$golz.'') : url('/orderk/update/'.$header->NO_ID.'&flagz='.$flagz.'&golz='.$golz.'' ) }}" method="POST" name ="entri" id="entri" >
  
	    			      @csrf
        
                        <div class="tab-content mt-3">
        
                            <div class="form-group row">
								<div class="col-md-1" align="left">
                                    <label for="NO_BUKTI" class="form-label">Bukti#</label>
                                </div>
								

                                   <input type="text" class="form-control NO_ID" id="NO_ID" name="NO_ID"
                                    placeholder="Masukkan NO_ID" value="{{$header->NO_ID ?? ''}}" hidden readonly>

									<input name="tipx" class="form-control tipx" id="tipx" value="{{$tipx}}" hidden >
									<input name="flagz" class="form-control flagz" id="flagz" value="{{$flagz}}" hidden >
									<input name="golz" class="form-control golz" id="golz" value="{{$golz}}" hidden >
			
								
                                <div class="col-md-2">
                                    <input type="text" class="form-control NO" id="NO_BUKTI" name="NO_BUKTI"
                                    placeholder="Masukkan Bukti#" value="{{$header->NO_BUKTI}}" readonly >
                                </div>

								<div class="col-md-2" align="right">
                                </div>
        
                                <div class="col-md-2" align="right">
                                    <label class="form-label">Customer</label>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control KODEC" id="KODEC" name="KODEC" placeholder="No Customer" value="{{$header->KODEC}}" readonly>
                                </div>
                            </div>

							<div class="form-group row">
                                <div class="col-md-1" align="left">
                                    <label for="TGL" class="form-label">Tgl</label>
                                </div>
                                <div class="col-md-2">
                                   <input class="form-control date" id="TGL" name="TGL" data-date-format="dd-mm-yyyy" type="text" autocomplete="off" value="{{date('d-m-Y',strtotime($header->TGL))}}">
                                </div>
                                <div class="col-md-1" align="right">
                                    <label for="JTEMPO" class="form-label">Jtempo</label>
                                </div>
                                <div class="col-md-2">
                                   <input class="form-control date" id="JTEMPO" name="JTEMPO" data-date-format="dd-mm-yyyy" type="text" autocomplete="off" value="{{date('d-m-Y',strtotime($header->JTEMPO))}}">
                                </div>
                                <div class="col-md-1">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control NAMAC" id="NAMAC" name="NAMAC" placeholder="Nama Customer" value="{{$header->NAMAC}}" readonly>
                                </div>
                            </div>
        
							<div class="form-group row">
                                <div class="col-md-1" align="left">
									<label style="color:red;font-size:20px">* </label>								
                                    <label class="form-label">SO#</label>
                                </div>
								<div class="col-md-2 input-group" >
									<input type="text" class="form-control NO_SO" id="NO_SO" name="NO_SO" placeholder="Pilih SO"value="{{$header->NO_SO}}" style="text-align: left" readonly >
									<button type="button" class="btn btn-primary" onclick="browseSo()"><i class="fa fa-search"></i></button>
								</div>
                                <div class="col-md-1" align="right">
                                    <!-- <label class="form-label">Seri#</label> -->
                                </div>
                                <div class="col-md-2">
                                    <!-- <input type="text" class="form-control NO_SERI" id="NO_SERI" name="NO_SERI" placeholder="No Seri" value="{{$header->NO_SERI}}" readonly> -->
                                </div>
                                <div class="col-md-1">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control ALAMAT" id="ALAMAT" name="ALAMAT" placeholder="Alamat Customer" value="{{$header->ALAMAT}}" readonly>
                                </div>
                            </div>
							
							<div class="form-group row">
							<div class="col-md-1" align="left">
                                    <label for="NOTES" class="form-label">Notes</label>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control NOTES" id="NOTES" name="NOTES" placeholder="Masukkan Notes" value="{{$header->NOTES}}">
                                </div>
                                <div class="col-md-1" align="right">
                                </div>
                                <div class="col-md-2">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control KOTA" id="KOTA" name="KOTA" placeholder="Kota Customer" value="{{$header->KOTA}}" readonly>
                                </div>
                            </div>
							
                            
                            <hr style="margin-top: 30px; margin-buttom: 30px">
							
							<div style="overflow-y:scroll;" class="col-md-12 scrollable" align="right">
							
								<table id="datatable" class="table table-striped table-border">

									<thead>
										<tr>
											<th style="text-align: center;" width="75px">No.</th>
											<th style="text-align: center;">
												<!-- <label style="color:red;font-size:20px">* </label>									 -->
												<label class="form-label">Kode</label>
											</th>
											<th style="text-align: center;">Uraian</th>
											<th style="text-align: center;">Satuan</th>
											<th style="text-align: center;">Qty SO</th>
											<th style="text-align: center;">
												<label style="color:red;font-size:20px">* </label>									
												<label class="form-label">Formula</label>
											</th>
											<th style="text-align: center;">Qty</th>
											<th style="text-align: center;"></th>
											<th></th>										
										</tr>
										
									</thead>
									<tbody id="detailSod">
									
									<?php $no=0 ?>
									@foreach ($detail as $detail)
										<tr>
											<td>
												<input type="hidden" name="NO_ID[]" id="NO_ID{{$no}}" type="text" value="{{$detail->NO_ID}}" 
												class="form-control NO_ID" onkeypress="return tabE(this,event)" readonly>
												
												<input name="REC[]" id="REC{{$no}}" type="text" value="{{$detail->REC}}" class="form-control REC" onkeypress="return tabE(this,event)" readonly>
											</td>

											<td>
												<input name="KD_BRG[]" id="KD_BRG{{$no}}" type="text" class="form-control KD_BRG " 
												value="{{$detail->KD_BRG}}">
											</td>

											<td>
												<input name="NA_BRG[]" id="NA_BRG{{$no}}" type="text" class="form-control NA_BRG " value="{{$detail->NA_BRG}}">
											</td>

											<td>
												<input name="SATUAN[]" id="SATUAN{{$no}}" type="text" value="{{$detail->SATUAN}}" class="form-control SATUAN" readonly required>
											</td>
											
											<td>
												<input name="QTY_SO[]"  onblur="hitung()" value="{{$detail->QTY_SO}}" id="QTY_SO{{$no}}" type="text" style="text-align: right"  class="form-control QTY_SO text-primary" >
											</td>
											
											<td>
												<input name="NO_FO[]" id="NO_FO{{$no}}" type="text" value="{{$detail->NO_FO}}"
												class="form-control NO_FO " onclick="browseFo({{$no}})" >
											</td>
											<td>
												<input name="QTY[]"  onblur="hitung()" value="{{$detail->QTY}}" id="QTY{{$no}}" type="text" style="text-align: right"  class="form-control QTY text-primary" >
											</td>                                    
											
											<td>
											
												<button type="button" class="btn btn-sm btn-circle btn-outline-danger btn-delete" onclick="">
													<i class="fa fa-fw fa-trash"></i>
												</button>
											</td>
										</tr>
										
									<?php $no++; ?>		
									@endforeach
									</tbody>
									<tfoot>
										<td></td>
										<!-- <td></td> -->
										<!-- <td {{( $golz =='B') ? '' : 'hidden' }}></td>
										<td {{( $golz =='B') ? '' : 'hidden' }}></td> -->
										<td></td>
										<td></td>
										<td></td>		
										<td><input class="form-control TOTAL_QTY_SO  text-primary font-weight-bold" style="text-align: right"  id="TOTAL_QTY_SO" name="TOTAL_QTY_SO" value="{{$header->TOTAL_QTY_SO}}" readonly></td>
										<td></td>
										<td><input class="form-control TOTAL_QTY  text-primary font-weight-bold" style="text-align: right"  id="TOTAL_QTY" name="TOTAL_QTY" value="{{$header->TOTAL_QTY}}" readonly></td>
										<td></td>
									</tfoot>
								</table>					
							</div>	
						   
                            <div class="col-md-2 row">
                               <a type="button" id='PLUSX' onclick="tambah()" class="fas fa-plus fa-sm md-3" ></a>					
							</div>		
							
                        </div> 
						
						<hr style="margin-top: 30px; margin-buttom: 30px">		
                                 
						<!-- <div class="tab-content mt-6">

                            <div class="form-group row">
                                <div class="col-md-8" align="right">
                                    <label for="PPN" class="form-label">Ppn</label>
                                </div>
                                <div class="col-md-2">
                                    <input type="text"  onclick="select()" onkeyup="hitung()" class="form-control PPN" id="PPN" name="PPN" placeholder="PPN" value="{{$header->PPN}}" style="text-align: right" readonly>
                                </div>
							</div>
							
                            <div class="form-group row">
                                <div class="col-md-8" align="right">
                                    <label for="NETT" class="form-label">Nett</label>
                                </div>
                                <div class="col-md-2">
                                    <input type="text"  onclick="select()" onkeyup="hitung()" class="form-control NETT" id="NETT" name="NETT" placeholder="NETT" value="{{$header->NETT}}" style="text-align: right" readonly>
                                </div>
							</div>
							
						</div> -->
						
						<div class="mt-3 col-md-12 form-group row">
							<div class="col-md-4">
								<button type="button" id='TOPX'  onclick="location.href='{{url('/orderk/edit/?idx=' .$idx. '&tipx=top&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-outline-primary">Top</button>
								<button type="button" id='PREVX' onclick="location.href='{{url('/orderk/edit/?idx='.$header->NO_ID.'&tipx=prev&flagz='.$flagz.'&golz='.$golz.'&buktix='.$header->NO_BUKTI )}}'" class="btn btn-outline-primary">Prev</button>
								<button type="button" id='NEXTX' onclick="location.href='{{url('/orderk/edit/?idx='.$header->NO_ID.'&tipx=next&flagz='.$flagz.'&golz='.$golz.'&buktix='.$header->NO_BUKTI )}}'" class="btn btn-outline-primary">Next</button>
								<button type="button" id='BOTTOMX' onclick="location.href='{{url('/orderk/edit/?idx=' .$idx. '&tipx=bottom&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-outline-primary">Bottom</button>
							</div>
							<div class="col-md-5">
								<button type="button" id='NEWX' onclick="location.href='{{url('/orderk/edit/?idx=0&tipx=new&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-warning">New</button>
								<button type="button" id='EDITX' onclick='hidup()' class="btn btn-secondary">Edit</button>                    
								<button type="button" id='UNDOX' onclick="location.href='{{url('/orderk/edit/?idx=' .$idx. '&tipx=undo&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-info">Undo</button>  
								<button type="button" id='SAVEX' onclick='simpan()'   class="btn btn-success" class="fa fa-save"></i>Save</button>

							</div>
							<div class="col-md-3">
								<button type="button" id='HAPUSX'  onclick="hapusTrans()" class="btn btn-outline-danger">Hapus</button>
								<button type="button" id='CLOSEX'  onclick="location.href='{{url('/orderk?flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-outline-secondary">Close</button>
							</div>
						</div>
						
						

                    </form>
                </div>
            </div>
            <!-- /.card -->
            </div>
        </div>
        <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
	

	<div class="modal fade" id="browseSoModal" tabindex="-1" role="dialog" aria-labelledby="browseSoModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseCustModalLabel">Cari Sales Order</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-so">
				<thead>
					<tr>
						<th>No Bukti</th>
						<th>Tanggal</th>
						<th>Customer</th>
						<th>Kode</th>
						<th>Barang</th>
						<th>Satuan</th>
						<th>Qty</th>
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


	<div class="modal fade" id="browseFoModal" tabindex="-1" role="dialog" aria-labelledby="browseFoModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseFoModalLabel">Cari Item</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-fo">
				<thead>
					<tr>
						<th>No FO#</th>
						<th>Kode</th>
						<th>Uraian</th>
						<th>Qty</th>	
						
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


	<div class="modal fade" id="browseBahanModal" tabindex="-1" role="dialog" aria-labelledby="browseBahanModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseBahanModalLabel">Cari Item</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bbahan">
				<thead>
					<tr>
						<th>Item#</th>
						<th>Nama</th>
						<th>Satuan</th>
						<th>Qty</th>
						<th>Kirim</th>
						<th>Sisa</th>
						<th>Harga</th>	
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

@section('footer-scripts')
<!-- TAMBAH 1 -->
<script src="{{ asset('js/autoNumerics/autoNumeric.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<script>

	var idrow = 1;
	var baris = 1;
    function numberWithCommas(x) {
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

// TAMBAH HITUNG
	$(document).ready(function() {
		
		idrow=<?php echo $no; ?>;
		baris=<?php echo $no; ?>;

		$('body').on('keydown', 'input, select', function(e) {
			if (e.key === "Enter") {
				var self = $(this), form = self.parents('form:eq(0)'), focusable, next;
				focusable = form.find('input,select,textarea').filter(':visible');
				next = focusable.eq(focusable.index(this)+1);
				console.log(next);
				if (next.length) {
					next.focus().select();
				} else {
					tambah();
					var nomer = idrow-1;
					console.log("REC"+nomor);
					document.getElementById("REC"+nomor).focus();
					// form.submit();
				}
				return false;
			}
		});

		$tipx = $('#tipx').val();
		$searchx = $('#CARI').val();
		
		
        if ( $tipx == 'new' )
		{
			 baru();	
            //  tambah();				 
		}

        if ( $tipx != 'new' )
		{
			 ganti();			
		}    
		
		$("#TOTAL_QTY_SO").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TOTAL_QTY").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});


		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#QTY" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#QTY_SO" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});

		}
		
		$('body').on('click', '.btn-delete', function() {
			var val = $(this).parents("tr").remove();
			baris--;
			hitung();
			nomor();
		});
		
		$(".date").datepicker({
			'dateFormat': 'dd-mm-yy',
		})
		
		
//////////////////////////////////////////////////////////////////

	var dTableSo;
	var rowidSo;
	loadDataSo = function(){
		
		$.ajax(
		{
			type: 'GET',    
			url: "{{url('orderk/browseSo')}}",
			data: {
				// kdbrg: kode,
				'GOL': "{{$golz}}",
			},
			success: function( response )
			{
				resp = response;
				if(dTableSo){
					dTableSo.clear();
				}
				for(i=0; i<resp.length; i++){
					
					dTableSo.row.add([
						'<a href="javascript:void(0);" onclick="chooseSo(\''+resp[i].NO_BUKTI+'\', \''+resp[i].KODEC+'\',  \''+resp[i].NAMAC+'\',  \''+resp[i].ALAMAT+'\',  \''+resp[i].KOTA+'\')">'+resp[i].NO_BUKTI+'</a>',
						resp[i].TGL,
						resp[i].NAMAC,
						resp[i].KD_BRG,
						resp[i].NA_BRG,
						resp[i].SATUAN,
						resp[i].QTY,
					]);
				}
				dTableSo.draw();
			}
		});
	}
	
	dTableSo = $("#table-so").DataTable({

		columnDefs: 
		[
			{
				className: "dt-right", 
				targets: [6],
			},		
			{
				targets: 1,
				render: $.fn.dataTable.render.moment( 'DD-MM-YYYY' ),
			}
		],

	});
	
		browseSo = function(rid){
			rowidSo = rid;
			loadDataSo();
			$("#browseSoModal").modal("show");
		}
	
	chooseSo = function(NO_BUKTI,KODEC, NAMAC, ALAMAT, KOTA){
		$("#NO_SO").val(NO_BUKTI);
		$("#KODEC").val(KODEC);
		$("#NAMAC").val(NAMAC);
		$("#ALAMAT").val(ALAMAT);
		$("#KOTA").val(KOTA);
		// $("#KD_BRG").val(KD_BRG);
		// $("#NA_BRG").val(NA_BRG);
		// $("#SATUAN").val(SATUAN);
		// $("#QTY_SO"+rowidSo).val(QTY!=0 ? QTY : 0);
		// $("#QTY_SO"+rowidSo).val(QTY);
		$("#browseSoModal").modal("hide");

		getSod(NO_BUKTI);
	}
	
	$("#NO_SO").keypress(function(e){
		if(e.keyCode == 46){
			e.preventDefault();
			browseSo();
		}
	}); 

//////////////////////////////////////////////////////////////////

	function getSod(bukti)
	{
		
		var mulai = (idrow==baris) ? idrow-1 : idrow;

		$.ajax(
			{
				type: 'GET',    
				url: "{{url('orderk/browse_detail')}}",
				data: {
					nobukti: bukti,
				},
				success: function( resp )
				{
					var html = '';
					for(i=0; i<resp.length; i++){
						html+=`<tr>
                                    <td><input name='REC[]' id='REC${i}' value=${resp[i].REC+1} type='text' class='REC form-control' onkeypress='return tabE(this,event)' readonly></td>
                                    <td><input name='KD_BRG[]' id='KD_BRG${i}' value="${resp[i].KD_BRG}" type='text' class='form-control KD_BRG' readonly></td>
                                    <td><input name='NA_BRG[]' id='NA_BRG${i}' value="${resp[i].NA_BRG}" type='text' class='form-control  NA_BRG' readonly></td>
                                    <td><input name='SATUAN[]' id='SATUAN${i}' value="${resp[i].SATUAN}" type='text' class='form-control  SATUAN' readonly></td>
                                    <td>
										<input name='QTY_SO[]' onclick='select()' onkeyup='hitung()' id='QTY_SO${i}' value="${resp[i].QTY_SO}" type='text' style='text-align: right' class='form-control QTY_SO text-primary' readonly >
									</td>
									<td>
										<input name='NO_FO[]' onclick="browseFo(${i})" id='NO_FO${i}' value="" type='text' class='form-control NO_FO' readonly>
									</td>
                                    <td>
										<input name='QTY[]' onclick='select()' onkeyup='hitung()' id='QTY${i}' value="0" type='text' style='text-align: right' class='form-control QTY text-primary'> 
									</td>
									<td><button type='button' class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button></td>
                                </tr>`;
					}
					$('#detailSod').html(html);

					$(".QTY_SO").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".QTY_SO").autoNumeric('update');

					$(".QTY").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".QTY").autoNumeric('update');

					
					// $(".NO_FO").each(function() {
					// 	var getid = $(this).attr('id');
					// 	var noid = getid.substring(6,11);

					// 	$("#NO_FO"+noid).keypress(function(e){
					// 		if(e.keyCode == 46){
					// 			e.preventDefault();
					// 			browseFo(noid);
					// 		}
					// 	}); 
					// });

					$("#NO_FO"+idrow).keypress(function(e){
						if(e.keyCode == 46){
							e.preventDefault();
							browseFo(eval($(this).data("rowid")));
						}
					});

					idrow=resp.length;
					baris=resp.length;

					nomor();
				}
			});
	}

//////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////
	var dTableFo;
	var rowidfo;
	loadDataFo = function(idx){
		$.ajax(
		{
			type: 'GET',    
			url: "{{url('orderk/browseFo')}}",
			data: {
				kdbrg: $("#KD_BRG"+idx).val(),
			},
			success: function( response )
			{
				resp = response;
				if(dTableFo){
					dTableFo.clear();
				}
				for(i=0; i<resp.length; i++){
					
					dTableFo.row.add([
						'<a href="javascript:void(0);" onclick="chooseFo(\''+resp[i].NO_BUKTI+'\')">'+resp[i].NO_BUKTI+'</a>',
						resp[i].KD_BRG,
						resp[i].NA_BRG,
						resp[i].TOTAL_QTY,
					]);
				}
				dTableFo.draw();
			}
		});
	}
		
		dTableFo = $("#table-fo").DataTable({

		});
		
		browseFo = function(idx){
			rowidfo=idx;
			loadDataFo(idx);
			$("#browseFoModal").modal("show");
		}
		
		chooseFo = function(NO_BUKTI){
			$("#NO_FO"+rowidfo).val(NO_BUKTI);	
			$("#browseFoModal").modal("hide");
			// getFod(NO_BUKTI);
		}
		
		$("#NO_FO").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseFo();
			}
		}); 
		
/////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//CHOOSE Bahan
		var dTableBBahan;
		var rowidBahan;
		loadDataBBahan = function(){
			$.ajax(
			{
				type: 'GET',    
				url: "{{url('so/browse_detail')}}",
				data: 
				{
                    KD_BHN : $("#KD_BHN"+rowidBahan).val(),
                    NO_SO : $("#NO_SO").val(), 					
				},
				success: function( response )
				{
					resp = response;
					if(dTableBBahan){
						dTableBBahan.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBBahan.row.add([
							'<a href="javascript:void(0);" onclick="chooseBahan(\''+resp[i].KD_BHN+'\',\''+resp[i].NA_BHN+'\', \''+resp[i].SATUAN+'\' , \''+resp[i].QTY+'\', \''+resp[i].KIRIM+'\' , \''+resp[i].SISA+'\' , \''+resp[i].HARGA+'\' )">'+resp[i].KD_BHN+'</a>',
							resp[i].NA_BHN,
							resp[i].SATUAN,
							resp[i].QTY,
							resp[i].KIRIM,
							resp[i].SISA,
							resp[i].HARGA,
							
						]);
					}
					dTableBBahan.draw();
				}
			});
		}
		
		dTableBBahan = $("#table-bbahan").DataTable({
			
		});
		
		browseBahan = function(rid){
			rowidBahan = rid;
			loadDataBBahan();
			$("#browseBahanModal").modal("show");
		}
		
		chooseBahan = function(KD_BHN, NA_BHN, SATUAN, QTY, KIRIM, SISA, HARGA ){
			$("#KD_BHN"+rowidBahan).val(KD_BHN);
			$("#NA_BHN"+rowidBahan).val(NA_BHN);
			$("#SATUAN"+rowidBahan).val(SATUAN);
			$("#QTY"+rowidBahan).val(SISA);
			$("#HARGA"+rowidBahan).val(HARGA);
			hitung();
			
			
			$("#browseBahanModal").modal("hide");
		}
		
		
		$("#KD_BHN0").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseBahan(0);
			}
		}); 

//////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////
		
 		var dTableBBarang;
		var rowidBarang;
		loadDataBBarang = function(){
			$.ajax(
			{
				type: 'GET',    
				url: "{{url('so/browse_detail2')}}",
				data: 
				{
                    KD_BRG : $("#KD_BRG"+rowidBarang).val(),
                    NO_SO : $("#NO_SO").val(), 					
				},				
				
				success: function( response )
				{
					resp = response;
					if(dTableBBarang){
						dTableBBarang.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBBarang.row.add([
							'<a href="javascript:void(0);" onclick="chooseBarang(\''+resp[i].KD_BRG+'\',\''+resp[i].NA_BRG+'\', \''+resp[i].SATUAN+'\' , \''+resp[i].QTY+'\' , \''+resp[i].KIRIM+'\' , \''+resp[i].SISA+'\' , \''+resp[i].HARGA+'\'  )">'+resp[i].KD_BRG+'</a>',
							resp[i].NA_BRG,
							resp[i].SATUAN,
							resp[i].QTY,
							resp[i].KIRIM,
							resp[i].SISA,
							resp[i].HARGA,							
						]);
					}
					dTableBBarang.draw();
				}
			});
		}
		
		dTableBBarang = $("#table-bbarang").DataTable({
			
		});
		
		browseBarang = function(rid){
			rowidBarang = rid;
			loadDataBBarang();
			$("#browseBarangModal").modal("show");
		}
		
		chooseBarang = function(KD_BRG, NA_BRG, SATUAN, QTY, KIRIM, SISA, HARGA){
			$("#KD_BRG"+rowidBarang).val(KD_BRG);
			$("#NA_BRG"+rowidBarang).val(NA_BRG);
			$("#SATUAN"+rowidBarang).val(SATUAN);
			$("#QTY"+rowidBarang).val(SISA);
			$("#HARGA"+rowidBarang).val(HARGA);			
			$("#browseBarangModal").modal("hide");
			hitung();
		}
		
		
		$("#KD_BRG0").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseBarang(0);
			}
		}); 
	});




//////////////////////////////////////////////////////////////////


	function simpan() {

		hitung();
		
		var tgl = $('#TGL').val();
		var bulanPer = {{session()->get('periode')['bulan']}};
		var tahunPer = {{session()->get('periode')['tahun']}};
		
        var check = '0';
		
		
			if ( tgl.substring(3,5) != bulanPer ) 
			{
				
				check = '1';
				alert("Bulan tidak sama dengan Periode");
			}	
			

			if ( tgl.substring(tgl.length-4) != tahunPer )
			{
				check = '1';
				alert("Tahun tidak sama dengan Periode");
				
		    }	 

			if ( $('#KD_BRG').val()=='' ) 
            {				
			    check = '1';
				alert("Bahan# Harus Diisi.");
			}

			if ( $('#KD_BHN').val()=='' ) 
            {				
			    check = '1';
				alert("Bahan# Harus Diisi.");
			}

        
			if ( $('#KODEC').val()=='' ) 
            {				
			    check = '1';
				alert("Customer# Harus Diisi.");
			}
			
		if ( check == '0' )
			{
				
		      document.getElementById("entri").submit();  
			}
			
	      
	}



    function nomor() {
		var i = 1;
		$(".REC").each(function() {
			$(this).val(i++);
		});
	//	hitung();
	}
 
	
	
	function hitung() {

		var TOTAL_QTY_SO = 0;
		var TOTAL_QTY = 0;


		$(".QTY").each(function() {
			
			let z = $(this).closest('tr');
			var QTYX = parseFloat(z.find('.QTY').val().replace(/,/g, ''));
			var QTY_SOX = parseFloat(z.find('.QTY_SO').val().replace(/,/g, ''));
		
            TOTAL_QTY_SO +=QTY_SOX;				
            TOTAL_QTY +=QTYX;	
			
		});
		
		if(isNaN(TOTAL_QTY_SO)) TOTAL_QTY_SO = 0;

		$('#TOTAL_QTY_SO').val(numberWithCommas(TOTAL_QTY_SO));		
		$("#TOTAL_QTY_SO").autoNumeric('update');


		if(isNaN(TOTAL_QTY)) TOTAL_QTY = 0;

		$('#TOTAL_QTY').val(numberWithCommas(TOTAL_QTY));		
		$("#TOTAL_QTY").autoNumeric('update');

		
	}

 
 
	function baru() {
		
		 kosong();
		 hidup();
	
	}
	
	function ganti() {
		
		 mati();
	
	}
	
	function batal() {
		
		// alert($header[0]->NO_BUKTI);
		
		 //$('#NO_BUKTI').val($header[0]->NO_BUKTI);	
		 mati();
	
	}
	
 

	
	
	function hidup() {

		
		$("#TOPX").attr("disabled", true);
	    $("#PREVX").attr("disabled", true);
	    $("#NEXTX").attr("disabled", true);
	    $("#BOTTOMX").attr("disabled", true);

	    $("#NEWX").attr("disabled", true);
	    $("#EDITX").attr("disabled", true);
	    $("#UNDOX").attr("disabled", false);
	    $("#SAVEX").attr("disabled", false);
		
	    $("#HAPUSX").attr("disabled", true);
//	    $("#CLOSEX").attr("disabled", true);

		$("#CARI").attr("readonly", true);	
	    $("#SEARCHX").attr("disabled", true);
		
	    $("#PLUSX").attr("hidden", false)
		   
			$("#NO_BUKTI").attr("readonly", true);		   
			$("#TGL").attr("readonly", false);
			$("#NO_SO").attr("readonly", true);
			$("#KODEC").attr("readonly", true);			
			$("#NAMAC").attr("readonly", true);
			$("#ALAMAT").attr("readonly", true);
			$("#KOTA").attr("readonly", true);			
			$("#NOTES").attr("readonly", false);
		
	

		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#REC" + i.toString()).attr("readonly", true);
			$("#KD_BHN" + i.toString()).attr("readonly", false);
			$("#KD_BRG" + i.toString()).attr("readonly", false);
			$("#NA_BHN" + i.toString()).attr("readonly", true);
			$("#NA_BRG" + i.toString()).attr("readonly", true);
			$("#SATUAN" + i.toString()).attr("readonly", false);
			$("#QTY" + i.toString()).attr("readonly", false);
			$("#HARGA" + i.toString()).attr("readonly", true);
			$("#TOTAL" + i.toString()).attr("readonly", false);
			$("#DPP" + i.toString()).attr("readonly", true);
			$("#PPN" + i.toString()).attr("readonly", true);
			$("#KET" + i.toString()).attr("readonly", false);

			$("#DELETEX" + i.toString()).attr("hidden", false);

			$tipx = $('#tipx').val();
		
			
			if ( $tipx != 'new' )
			{
				$("#KD_BHN" + i.toString()).attr("readonly", true);	
				$("#KD_BHN" + i.toString()).removeAttr('onblur');
				
				$("#KD_BRG" + i.toString()).attr("readonly", true);	
				$("#KD_BRG" + i.toString()).removeAttr('onblur');
			}
		}

		
	}


	function mati() {

		
	    $("#TOPX").attr("disabled", false);
	    $("#PREVX").attr("disabled", false);
	    $("#NEXTX").attr("disabled", false);
	    $("#BOTTOMX").attr("disabled", false);


	    $("#NEWX").attr("disabled", false);
	    $("#EDITX").attr("disabled", false);
	    $("#UNDOX").attr("disabled", true);
	    $("#SAVEX").attr("disabled", true);
	    $("#HAPUSX").attr("disabled", false);
	    $("#CLOSEX").attr("disabled", false);

		$("#CARI").attr("readonly", false);	
	    $("#SEARCHX").attr("disabled", false);
		
		
	    $("#PLUSX").attr("hidden", true)
		
	    $(".NO_BUKTI").attr("readonly", true);	
		
		$("#TGL").attr("readonly", true);
		$("#KODEC").attr("readonly", true);			
		$("#NAMAC").attr("readonly", true);
		$("#ALAMAT").attr("readonly", true);
		$("#KOTA").attr("readonly", true);	
		$("#NOTES").attr("readonly", true);

		
		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#REC" + i.toString()).attr("readonly", true);
			$("#KD_BHN" + i.toString()).attr("readonly", true);
			$("#NA_BHN" + i.toString()).attr("readonly", true);
			$("#KD_BRG" + i.toString()).attr("readonly", true);
			$("#NA_BRG" + i.toString()).attr("readonly", true);
			$("#SATUAN" + i.toString()).attr("readonly", true);
			$("#QTY" + i.toString()).attr("readonly", true);
			$("#HARGA" + i.toString()).attr("readonly", true);
			$("#TOTAL" + i.toString()).attr("readonly", true);
			$("#DPP" + i.toString()).attr("readonly", true);
			$("#PPNX" + i.toString()).attr("readonly", true);
			$("#KET" + i.toString()).attr("readonly", true);
			$("#DELETEX" + i.toString()).attr("hidden", true);
		}


		
	}


	function kosong() {
				
		 $('#NO_BUKTI').val("+");	
		 $('#NO_SO').val("");
		 $('#KODEC').val("");
		 $('#NAMAC').val("");
		 $('#ALAMAT').val("");
		 $('#KOTA').val("");		 
	//	 $('#TGL').val("");	
		 $('#NOTES').val("");	
		 $('#TTOTAL_QTY').val("0.00");	
		 $('#TTOTAL').val("0.00");

		 $('#PPN').val("0.00");	
		 $('#NETT').val("0.00");
		 $('#DPP').val("0.00");
		 $('#PPNX').val("0.00");
		 
		var html = '';
		$('#detailx').html(html);	
		
	}
	
	function hapusTrans() {
		let text = "Hapus Transaksi "+$('#NO_BUKTI').val()+"?";
		if (confirm(text) == true) 
		{
			window.location ="{{url('/orderk/delete/'.$header->NO_ID .'/?flagz='.$flagz.'&golz=' .$golz.'' )}}";
			//return true;
		} 
		return false;
	}
	

	function CariBukti() {
		
		var flagz = "{{ $flagz }}";
		var cari = $("#CARI").val();
		var loc = "{{ url('/orderk/edit/') }}" + '?idx={{ $header->NO_ID}}&tipx=search&flagz=' + encodeURIComponent(flagz) + '&golz=' + encodeURIComponent(golz) + '&buktix=' +encodeURIComponent(cari);
		window.location = loc;
		
	}

    function tambah() {

        // var x = document.getElementById('datatable').insertRow(baris + 1);
 
		// html=`<tr>

        //         <td>
 		// 			<input name='NO_ID[]' id='NO_ID${idrow}' type='hidden' class='form-control NO_ID' value='new' readonly> 
		// 			<input name='REC[]' id='REC${idrow}' type='text' class='REC form-control' onkeypress='return tabE(this,event)' readonly>
	    //         </td>
						       
        //         <td>
		// 		    <input name='NO_FO[]' data-rowid=${idrow} onblur='browseBahan(${idrow})' id='NO_FO${idrow}' type='text' class='form-control  NO_FO' >
        //         </td>
				
		// 		<td>
		//             <input name='QTY_SO[]'  onblur='hitung()' value='0' id='QTY_SO${idrow}' type='text' style='text-align: right' class='form-control QTY_SO text-primary' required >
        //         </td>
				
		// 		<td>
		//             <input name='QTY[]'  onblur='hitung()' value='0' id='QTY${idrow}' type='text' style='text-align: right' class='form-control QTY text-primary' required >
        //         </td>
				
        //         <td>
		// 			<button type='button' id='DELETEX${idrow}'  class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button>
        //         </td>				
        //  </tr>`;
				
        // x.innerHTML = html;
        // var html='';
		
 

		// jumlahdata = 100;
		
		// for (i = 0; i <= jumlahdata; i++) {
		// 	$("#QTY_SO" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
		// 	$("#QTY" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
	
		// }
		
		// // $("#KD_BRG"+idrow).keypress(function(e){
		// // 	if(e.keyCode == 46){
		// // 		e.preventDefault();
		// // 		browseBarang(eval($(this).data("rowid")));
		// // 	}
		// // }); 
		
		// idrow++;
		// baris++;
		// nomor();
		// $(".ronly").on('keydown paste', function(e) {
		// 	e.preventDefault();
		// 	e.currentTarget.blur();
		// });
     }
</script>



<script src="autonumeric.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
<script src="https://unpkg.com/autonumeric"></script>

@endsection