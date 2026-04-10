@extends('layouts.plain')

<style>
    .card {

    }

    .form-control:focus {
        background-color: #b5e5f9 !important;
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

	/* query LOADX */

	.loader {
      position: fixed;
        top: 50%;
        left: 50%;
      width: 100px;
      aspect-ratio: 1;
      background:
        radial-gradient(farthest-side,#ffa516 90%,#0000) center/16px 16px,
        radial-gradient(farthest-side,green   90%,#0000) bottom/12px 12px;
      background-repeat: no-repeat;
      animation: l17 1s infinite linear;
      position: relative;
    }
    .loader::before {    
      content:"";
      position: absolute;
      width: 8px;
      aspect-ratio: 1;
      inset: auto 0 16px;
      margin: auto;
      background: #ccc;
      border-radius: 50%;
      transform-origin: 50% calc(100% + 10px);
      animation: inherit;
      animation-duration: 0.5s;
    }
    @keyframes l17 { 
      100%{transform: rotate(1turn)}
    }

	/* penutup LOADX */

    /* menghilangkan padding */
    .content-header {
        padding: 0 !important;
    }

</style>

@section('content')

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">

        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
        <div class="row">
            <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <form action="{{($tipx=='new')? url('/harga/store?flagz='.$flagz.'') : url('/harga/update/'.$header->NO_ID.'&flagz='.$flagz.'' ) }}" method="POST" name ="entri" id="entri" >
  
                        @csrf
                        <div class="tab-content mt-3">
                            <div class="form-group row">
                                <div class="col-md-1" align="left">
                                    <label for="NO_BUKTI" class="form-label">Bukti#</label>
                                </div>
								

                                   <input type="text" class="form-control NO_ID" id="NO_ID" name="NO_ID"
                                    placeholder="Masukkan NO_ID" value="{{$header->NO_ID ?? ''}}" hidden readonly>

									<input name="tipx" class="form-control tipx" id="tipx" value="{{$tipx}}" hidden>
									<input name="flagz" class="form-control flagz" id="flagz" value="{{$flagz}}" hidden>

								
								
                                <div class="col-md-2">
                                    <input type="text" class="form-control NO_BUKTI" id="NO_BUKTI" name="NO_BUKTI"
                                    placeholder="Masukkan Bukti#" value="{{$header->NO_BUKTI}}" readonly>
                                </div>
								
								<div class="col-md-1" align="left">								
									<label for="CNT" class="form-label">Conter</label>
								</div>
								<div class="col-md-2 input-group" >
									<input type="text" class="form-control CNT" id="CNT" name="CNT" placeholder="Pilih"value="{{$header->CNT}}" style="text-align: left" readonly >
									<button type="button" class="btn btn-primary" onclick="browseConter()"><i class="fa fa-search"></i></button>
								</div>
								
                                <div class="col-md-2">
                                    <input type="text" class="form-control NCNT" id="NCNT" name="NCNT" placeholder=""  value="{{$header->NCNT}}"  readonly >
                                </div>

                            </div>
							
							<div class="form-group row">

                                <div class="col-md-1" align="left">
                                    <label for="TGL" class="form-label">Tgl</label>
                                </div>
                                <div class="col-md-2">
								  <input class="form-control date" id="TGL" name="TGL" data-date-format="dd-mm-yyyy" type="text" autocomplete="off" value="{{date('d-m-Y',strtotime($header->TGL))}}">
                                </div>

								<div class="col-md-1" align="left">								
									<label for="KODES" class="form-label">Kode</label>
								</div>
								<div class="col-md-2 input-group" >
									<input type="text" class="form-control KODES" id="KODES" name="KODES" placeholder="" value="{{$header->KODES}}" style="text-align: left" readonly >
									<button type="button" class="btn btn-primary" onclick="browseSup()"><i class="fa fa-search"></i></button>
								</div>
								
                                <div class="col-md-2">
                                    <input type="text" class="form-control NAMAS" id="NAMAS" name="NAMAS" placeholder=""  value="{{$header->NAMAS}}"  readonly >
                                </div>
								
                            </div>

							<!-- loader tampil di modal  -->
							<div class="loader" style="z-index: 1055;" id='LOADX' ></div>
							<!-- tutupan load -->

							<!-- style text box model baru -->

							<style>
								/* Ensure specificity with class targeting */
								.form-group.special-input-label {
									position: relative;

									/* geser kanan kirinya di atur disini */
									margin-left: 50px ;
								}
						
								/* Ensure only bottom border for input */
								.form-group.special-input-label input {
									width: 100%;
									padding: 10px 0;
									border: none !important;
									border-bottom: 2px solid #ccc !important;
									outline: none !important;
									font-size: 16px !important;
									background: transparent !important; /* Remove any background color */
								}
						
								/* Bottom border color change on focus */
								.form-group.special-input-label input:focus {
									border-bottom: 2px solid #007BFF !important; /* Change color on focus */
								}
						
								/* Style the label with a higher specificity */
								.form-group.special-input-label label {
									position: absolute;
									top: 12px;
									/* buat label di inputan */
									color: #888 !important;
									font-size: 16px !important;
									transition: 0.3s ease all;
									pointer-events: none;
								}
						
								/* Move label above input when focused or has content */
								.form-group.special-input-label input:focus + label,
								.form-group.special-input-label input:not(:placeholder-shown) + label {
									top: -10px !important;
									font-size: 14px !important;
									/* buat label diatas */
									color: #007BFF !important;
								}
							</style>

							<!-- tutupannya -->

							<div class="form-group row" align="left">
								<!-- code text box baru -->
								
								<!-- tutupannya -->
                            </div>

							<div class="form-group row">

                                <div class="col-md-4 form-group row special-input-label">
									<input type="text" class="NOTES" id="NOTES" name="NOTES" 
										value="{{$header->NOTES}}" placeholder=" " >
									<label for="NOTES">Notes</label>
								</div>
								
                            </div>
							
							
                            <hr style="margin-top: 30px; margin-buttom: 30px">
							
							<div style="overflow-y:scroll;" class="col-md-12 scrollable" align="right">

								<table id="datatable" class="table table-striped table-border">

									<thead>
										<tr>
											<th width="100px" style="text-align:center">No.</th>
											<th width="150px" style="text-align:center">Kode</th>
											<th {{ $flagz == 'HG' ? 'hidden' : '' }} width="200px" style="text-align:center">Barcode</th>
											<th width="300px" style="text-align:center">Nama Barang</th>
											<th width="200px" style="text-align:center">JNS</th>
											<th {{ $flagz == 'HT' ? 'hidden' : '' }} width="200px" style="text-align:center">Harga Lama</th>
											<th {{ $flagz == 'HT' ? 'hidden' : '' }} width="200px" style="text-align:center">Harga Kasir</th>
											<th width="200px" style="text-align:center">Harga Baru</th>
											<th {{ $flagz == 'HG' ? 'hidden' : '' }} width="200px" style="text-align:center">Sisa</th>
											<th {{ $flagz == 'HG' ? 'hidden' : '' }} width="200px" style="text-align:center">Diskon</th>
											<th width="200px" style="text-align:center">Keterangan</th>  

											<th></th>
																
										</tr>
									<tbody id="detailHargad">
			
									<tbody>
									<?php $no=0 ?>
									@foreach ($detail as $detail)		
										<tr>
											<td>
												<input type="hidden" name="NO_ID[]{{$no}}" id="NO_ID" type="text" value="{{$detail->NO_ID}}" 
												class="form-control NO_ID" onkeypress="return tabE(this,event)" readonly>
												
												<input name="REC[]" id="REC{{$no}}" type="text" value="{{$detail->REC}}" class="form-control REC" onkeypress="return tabE(this,event)" readonly style="text-align:center">
											</td>									 -->

											<td>
												<input name="KD_BRG[]" id="KD_BRG{{$no}}" type="text" class="form-control KD_BRG " 
												value="{{$detail->KD_BRG}}" onblur="browseBarang({{$no}})">
											</td>

											<td {{ $flagz == 'HG' ? 'hidden' : '' }}>
												<input name="BARCODE[]" id="BARCODE{{$no}}" type="text" class="form-control BARCODE " value="{{$detail->BARCODE}}" readonly>
											</td>

											<td>
												<input name="NA_BRG[]" id="NA_BRG{{$no}}" type="text" class="form-control NA_BRG " value="{{$detail->NA_BRG}}" readonly>
											</td>

											<td>
												<input name="JNS[]" id="JNS{{$no}}" type="text" class="form-control JNS" value="{{$detail->JNS}}" readonly>
											</td>									
											<td {{ $flagz == 'HT' ? 'hidden' : '' }}>
												<input name="HARGAJL[]" onclick="select()" value="{{$detail->HARGAJL}}" id="HARGAJL{{$no}}" type="text" style="text-align: right"  class="form-control HARGAJL" readonly>
											</td>									
											<td {{ $flagz == 'HT' ? 'hidden' : '' }}>
												<input name="HARGAKSR[]" onclick="select()" value="{{$detail->HARGAKSR}}" id="HARGAKSR{{$no}}" type="text" style="text-align: right"  class="form-control HARGAKSR" readonly>
											</td>										
											<td>
												<input name="HARGA[]" onclick="select()" value="{{$detail->HARGA}}" id="HARGA{{$no}}" type="text" style="text-align: right"  class="form-control HARGA" >
											</td>

											<td {{ $flagz == 'HG' ? 'hidden' : '' }}>
												<input name="SISA[]" onclick="select()" value="{{$detail->SISA}}" id="SISA{{$no}}" type="text" style="text-align: right"  class="form-control SISA" readonly>
											</td>

											<td {{ $flagz == 'HG' ? 'hidden' : '' }}>
												<input name="DTH[]" onclick="select()" value="{{$detail->DTH}}" id="DTH{{$no}}" type="text" style="text-align: right"  class="form-control DTH" >
											</td>
											
											<td>
												<input name="KET[]" id="KET{{$no}}" type="text" class="form-control KET" value="{{$detail->KET}}" readonly >
											</td>
											
											<td>
												<button type='button' id='DELETEX{{$no}}'  class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button>
											</td> 

										</tr>
									
									<?php $no++; ?>
									@endforeach
									</tbody>

									<tfoot>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										{{-- <td><input class="form-control THARGAJL  text-primary" style="text-align: right"  id="THARGAJL" name="THARGAJL" value="{{$header->HARGAJL}}" readonly></td> --}}
										<td></td>									
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tfoot>
								</table>
							</div>

                            <div class="col-md-2 row">
                               <a type="button" id='PLUSX' onclick="tambah()" class="fas fa-plus fa-sm md-3" style="font-size: 20px" ></a>
					
							</div>	
							
                        </div> 
						
						   
						<div class="mt-3 col-md-12 form-group row">
							<div class="col-md-4">
								<button hidden type="button" id='TOPX'  onclick="location.href='{{url('/harga/edit/?idx=' .$idx. '&tipx=top&flagz='.$flagz.'' )}}'" class="btn btn-outline-primary">Top</button>
								<button hidden type="button" id='PREVX' onclick="location.href='{{url('/harga/edit/?idx='.$header->NO_ID.'&tipx=prev&flagz='.$flagz.'&buktix='.$header->NO_BUKTI )}}'" class="btn btn-outline-primary">Prev</button>
								<button hidden type="button" id='NEXTX' onclick="location.href='{{url('/harga/edit/?idx='.$header->NO_ID.'&tipx=next&flagz='.$flagz.'&buktix='.$header->NO_BUKTI )}}'" class="btn btn-outline-primary">Next</button>
								<button hidden type="button" id='BOTTOMX' onclick="location.href='{{url('/harga/edit/?idx=' .$idx. '&tipx=bottom&flagz='.$flagz.'' )}}'" class="btn btn-outline-primary">Bottom</button>
							</div>
							<div class="col-md-5">
								<button hidden type="button" id='NEWX' onclick="location.href='{{url('/harga/edit/?idx=0&tipx=new&flagz='.$flagz.'' )}}'" class="btn btn-warning">New</button>
								<button hidden type="button" id='EDITX' onclick='hidup()' class="btn btn-secondary">Edit</button>                    
								<button hidden type="button" id='UNDOX' onclick="location.href='{{url('/harga/edit/?idx=' .$idx. '&tipx=undo&flagz='.$flagz.'' )}}'" class="btn btn-info">Undo</button>  
								<button type="button" id='SAVEX' onclick='simpan()'   class="btn btn-success" class="fa fa-save"></i>Save</button>

							</div>
							<div class="col-md-3">
								<button hidden type="button" id='HAPUSX'  onclick="hapusTrans()" class="btn btn-outline-danger">Hapus</button>

								<!-- <button type="button" id='CLOSEX'  onclick="location.href='{{url('/harga?flagz='.$flagz.'' )}}'" class="btn btn-outline-secondary">Close</button> -->
							
								<!-- tombol close sweet alert -->
								<button type="button" id='CLOSEX' onclick="closeTrans()" class="btn btn-outline-secondary">Close</button></div>   
							</div>
						</div>
						
						
                    </form>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>


 	<div class="modal fade" id="browseSupModal" tabindex="-1" role="dialog" aria-labelledby="browseSupModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseSupModalLabel">Cari Sup</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bsup">
				<thead>
					<tr>
						<th>Kode</th>
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

	<div class="modal fade" id="browseConterModal" tabindex="-1" role="dialog" aria-labelledby="browseConterModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseConterModalLabel">Cari Conter</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bconter">
				<thead>
					<tr>
						<th>No Conter</th>
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


	<div class="modal fade" id="browseBarangModal" tabindex="-1" role="dialog" aria-labelledby="browseBarangModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseBarangModalLabel">Cari Item</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bbarang">
				<thead>
					<tr>
						<th>Kode#</th>
						<th>Barcode</th>
						<th>Nama Barang</th>
						<th>Jenis</th>
							
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
	  <div class="modal-dialog modal-xl" role="document">
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
<script src="{{ asset('js/autoNumerics/autoNumeric.min.js') }}"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script> -->
<script src="{{asset('foxie_js_css/bootstrap.bundle.min.js')}}"></script>

<!-- tambahan untuk sweetalert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- tutupannya -->

<script>
	var idrow = 1;
	var baris = 1;

	function numberWithCommas(x) {
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	
    $(document).ready(function () {

		setTimeout(function(){

		$("#LOADX").hide();

		},500);

		idrow=<?=$no?>;
		baris=<?=$no?>;

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
					// var nomer = idrow-1;
					// console.log("REC"+nomor);
					// document.getElementById("REC"+nomor).focus();
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
             tambah();				 
		}

        if ( $tipx != 'new' )
		{
			 ganti();			
		}    
		
		// $("#THARGAJL").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});



		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#HARGAJL" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#HARGAKSR" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#HARGA" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#SISA" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#DTH" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
		}	


		
        $('body').on('click', '.btn-delete', function() {
			var val = $(this).parents("tr").remove();
			baris--;
			// hitung();
			nomor();
			
		});

		$('.date').datepicker({  
            dateFormat: 'dd-mm-yy'
		});
		
		
 	
		
		//	CHOOSE Sup
 		var dTableBSup;
		loadDataBSup = function(){
		
			$.ajax(
			{
				type: 'GET', 		
				url: '{{url('harga/browse_sup')}}',

				beforeSend: function(){
					$("#LOADX").show();
				},

				success: function( response )
				{
					$("#LOADX").hide();

					resp = response;
					if(dTableBSup){
						dTableBSup.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBSup.row.add([
							'<a href="javascript:void(0);" onclick="chooseSup(\''+resp[i].KODES+'\' ,\''+resp[i].NAMAS+'\')">'+resp[i].KODES+'</a>',
							resp[i].NAMAS,
						]);
					}
					dTableBSup.draw();
				}
			});
		}
		
		dTableBSup = $("#table-bsup").DataTable({
			
		});
		
		browseSup = function(){
			loadDataBSup();
			$("#browseSupModal").modal("show");

		}
		
		chooseSup = function( KODES,NAMAS){

			$("#KODES").val(KODES);
			$("#NAMAS").val(NAMAS);			
			$("#browseSupModal").modal("hide");
		} 

		//////////////////////////////////////////////////////////////////

		//	CHOOSE Conter
		var dTableBConter;
		loadDataBConter = function(){
		
			$.ajax(
			{
				type: 'GET', 		
				url: '{{url('harga/browse_conter')}}',

				beforeSend: function(){
					$("#LOADX").show();
				},

				success: function( response )
				{
					$("#LOADX").hide();

					resp = response;
					if(dTableBConter){
						dTableBConter.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBConter.row.add([
							'<a href="javascript:void(0);" onclick="chooseConter(\''+resp[i].CNT+'\', \''+resp[i].NCNT+'\')">'+resp[i].CNT+'</a>',
							resp[i].NCNT,
						]);
					}
					dTableBConter.draw();
				}
			});
		}
		
		dTableBConter = $("#table-bconter").DataTable({
			
		});
		
		browseConter = function(){
			loadDataBConter();
			$("#browseConterModal").modal("show");
		}
		
		chooseConter = function( CNT, NCNT){

			$("#CNT").val(CNT);
			$("#NCNT").val(NCNT);			
			$("#browseConterModal").modal("hide");
		}

		//////////////////////////////////////////////////////////////////////

		//////////////////////////////////////////////////////////////////

		var dTableBBarang;
		var rowidBarang;
		loadDataBBarang = function(){
			$.ajax(
			{
				type: 'GET',    
				url: "{{url('harga/browse_brg')}}",
				data: 
				{	
					CNT : $("#CNT").val(),				
				},				
				
				success: function( response )
				{
					resp = response;
					if(dTableBBarang){
						dTableBBarang.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBBarang.row.add([
							'<a href="javascript:void(0);" onclick="chooseBarang(\''+resp[i].KD_BRG+'\',\''+resp[i].BARCODE+'\', \''+resp[i].NA_BRG+'\' , \''+resp[i].JNS+'\' , \''+resp[i].HARGAJL+'\' , \''+resp[i].HARGAKSR+'\' , \''+resp[i].SISA+'\')">'+resp[i].KD_BRG+'</a>',
							resp[i].BARCODE,
							resp[i].NA_BRG,
							resp[i].JNS,							
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
		
		chooseBarang = function(KD_BRG, BARCODE, NA_BRG, JNS, HARGAJL, HARGAKSR, SISA ){
			$("#KD_BRG"+rowidBarang).val(KD_BRG);
			$("#BARCODE"+rowidBarang).val(BARCODE);
			$("#NA_BRG"+rowidBarang).val(NA_BRG);
			$("#JNS"+rowidBarang).val(JNS);
			$("#HARGAJL"+rowidBarang).val(HARGAJL);
			$("#HARGAKSR"+rowidBarang).val(HARGAKSR);			
			$("#SISA"+rowidBarang).val(SISA);			
			$("#browseBarangModal").modal("hide");
			// hitung();
		}
	});


///////////////////////////////////////		
    

    function cekDetail(){
		var cekBarang = '';
		$(".KD_BRG").each(function() {
			
			let z = $(this).closest('tr');
			var KD_BRGX = z.find('.KD_BRG').val();
			// var QTY_POX = parseFloat(z.find('.QTY_PO').val().replace(/,/g, ''));
			// var SISAX = parseFloat(z.find('.SISA').val().replace(/,/g, ''));

			// alert(QTYX);
			// alert(SEDIAX);
			
			if( KD_BRGX =="" )
			{
					cekBarang = '1';
					
			}	

			// if( QTY_POX > SISAX )
			// {
			// 		cekBarang = '1';
					
			// }	
		});
		
		return cekBarang;
	}
	

 	function simpan() {
		// hitung();
		
		var tgl = $('#TGL').val();
		var bulanPer = {{session()->get('periode')['bulan']}};
		var tahunPer = {{session()->get('periode')['tahun']}};
		
        var check = '0';

			if (cekDetail())
    		{	
    			check = '1';
    
    			Swal.fire({
    				icon: 'warning',
    				title: 'Warning',
    				text: '#Periksa Barang dan QTY PO, QTY PO tidak boleh melebihi SISA PO'
    			});
    			return;
    		}
			
			if ( $('#KODES').val()=='' ) 
            {				
			    check = '1';
				Swal.fire({
					icon: 'warning',
					title: 'Warning',
					text: 'Suplier# Harus Diisi.'
				});
				return; // Stop function execution
			}

			if ( $('#CNT').val()=='' ) 
            {				
			    check = '1';
				Swal.fire({
					icon: 'warning',
					title: 'Warning',
					text: 'Conter# Harus Diisi.'
				});
				return; // Stop function execution
			}

			if ( tgl.substring(3,5) != bulanPer ) 
			{
				check = '1';
				Swal.fire({
					icon: 'warning',
					title: 'Warning',
					text: 'Bulan tidak sama dengan Periode'
				});
				return; // Stop function execution
			}	
			

			if ( tgl.substring(tgl.length-4) != tahunPer )
			{
				check = '1';
				Swal.fire({
					icon: 'warning',
					title: 'Warning',
					text: 'Tahun tidak sama dengan Periode'
				});
				return; // Stop function execution
		    }	 
			
			if (baris==0)
			{
				check = '1';
				Swal.fire({
					icon: 'warning',
					title: 'Warning',
					text: 'Data detail kosong (Tambahkan 1 baris kosong jika ingin mengosongi detail)'
				});
				return;
			}

			if (check == '0') {
				Swal.fire({
					title: 'Are you sure?',
					text: 'Are you sure you want to save?',
					icon: 'question',
					showCancelButton: true,
					confirmButtonText: 'Yes, save it!',
					cancelButtonText: 'No, cancel',
				}).then((result) => {
					if (result.isConfirmed) {
						document.getElementById("entri").submit();
					} else {
						Swal.fire({
							icon: 'info',
							title: 'Cancelled',
							text: 'Your data was not saved'
						});
					}
				});
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'Masih ada kesalahan'
				});
			}

		// tutupannya
			
			$("#LOADX").hide();
	}
		
    function nomor() {
		var i = 1;
		$(".REC").each(function() {
			$(this).val(i++);
		});
		
	//	hitung();
	
	}

    // function hitung() {
	// 	var TTOTAL_QTY = 0;
	// 	var TTOTAL = 0;
	// 	var TDISK = 0;
	// 	var TDPPX = 0;
	// 	var TPPNX = 0;
	// 	var NETTX = 0;

		
	// 	$(".QTY_PO").each(function() {
			
	// 		let z = $(this).closest('tr');
	// 		var QTY_POX = parseFloat(z.find('.QTY_PO').val().replace(/,/g, ''));
	// 		var XQTYX = parseFloat(z.find('.XQTY').val().replace(/,/g, ''));
	// 		var XX = parseFloat(z.find('.KALI').val().replace(/,/g, ''));
	// 		var HARGAX = parseFloat(z.find('.HARGA').val().replace(/,/g, ''));
	// 		var PPN = parseFloat(z.find('.PPNX').val().replace(/,/g, ''));
	// 		var DISKX = parseFloat(z.find('.DISK').val().replace(/,/g, ''));
	
	// 		var PKPX  = $('#PKP').val();

	// 		var FLAGZ = $('#flagz').val();
	
	// 		if (FLAGZ == 'RB'){
				
	// 			var XQTYX  = ( XQTYX * -1 ) ;
	// 			var QTY_POX  = ( QTY_POX * -1 ) ;
	// 			var DISKX  = ( DISKX * -1 ) ;
				
	// 			z.find('.QTY_PO').autoNumeric('update');
	// 			z.find('.DISKX').autoNumeric('update');
	// 			z.find('.XQTY').autoNumeric('update');

	// 		} 

	// 		var QTYX  = ( XQTYX * XX );
	// 		z.find('.QTY').val(QTYX);

	// 	    z.find('.KALI').autoNumeric('update');	
	// 	    z.find('.QTY').autoNumeric('update');	

            
    //         var TOTALX  =  ( XQTYX * HARGAX ) - DISKX;
            
	// 		z.find('.TOTAL').val(TOTALX);


	// 		var DPPX = 0 ;
	// 		var PPNX = 0;
			
    //         DPPX = TOTALX;
	//      	z.find('.DPP').val(DPPX);

	// 		if (PKPX == '0' ) {
	// 		    PPNX = 0;
			    
	// 		} 

	     		
	// 		if (PKPX == '1' ) {
	// 		    DPPX = TOTALX * 100/111;
	// 		    PPNX = TOTALX - DPPX;
	//      	    z.find('.DPP').val(DPPX);
	     	
	// 		} 


            
	// 		z.find('.PPNX').val(PPNX);	

	// 	    z.find('.HARGA').autoNumeric('update');			
	// 	    z.find('.QTY').autoNumeric('update');	
	// 	    z.find('.TOTAL').autoNumeric('update');				
	// 	    z.find('.DPP').autoNumeric('update');			
	// 	    z.find('.DISK').autoNumeric('update');			
	// 	    z.find('.PPNX').autoNumeric('update');		

    //         TTOTAL_QTY +=QTYX;		
    //         TTOTAL +=TOTALX;				
    //         TPPNX +=PPNX;
    //         TDPPX +=DPPX;
            
    //         TDISK +=DISKX;				
		
	// 	});

		
	// 	NETTX = TTOTAL ;

		
	// 	if(isNaN(TTOTAL_QTY)) TTOTAL_QTY = 0;

	// 	$('#TTOTAL_QTY').val(numberWithCommas(TTOTAL_QTY));		
	// 	$("#TTOTAL_QTY").autoNumeric('update');
		
	// 	if(isNaN(TTOTAL)) TTOTAL = 0;

	// 	$('#TTOTAL').val(numberWithCommas(TTOTAL));		
	// 	$("#TTOTAL").autoNumeric('update');

	// 	$('#TDISK').val(numberWithCommas(TDISK));		
	// 	$("#TDISK").autoNumeric('update');


	// 	$('#TDPP').val(numberWithCommas(TDPPX));		
	// 	$("#TDPP").autoNumeric('update');
		
	// 	$('#TPPN').val(numberWithCommas(TPPNX));		
	// 	$("#TPPN").autoNumeric('update');

	// 	$('#NETT').val(numberWithCommas(NETTX));		
	// 	$("#NETT").autoNumeric('update');

		
	// }
	

	
  
	function baru() {
		
		 kosong();
		 hidup();
	
	}
	
	function ganti() {
		
// 		 mati();
		 hidup();
	
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
	    $("#CLOSEX").attr("disabled", false);

		$("#CARI").attr("readonly", true);	
	    $("#SEARCHX").attr("disabled", true);
		
	    $("#PLUSX").attr("hidden", false)
		   
			$("#NO_BUKTI").attr("readonly", true);		   
			$("#TGL").attr("readonly", false);
			$("#KODES").attr("readonly", true);
			$("#NAMAS").attr("readonly", true);			
			$("#ALAMAT").attr("readonly", true);
			$("#KOTA").attr("readonly", true);
			
			$("#NO_FAKTUR").attr("readonly", false);
			$("#TGL_FAKTUR").attr("readonly", false);
			$("#JTEMPO").attr("readonly", true);
			
			$("#NOTES").attr("readonly", false);
			$("#TYPE").attr("readonly", false);
	        $("#PKP").attr("disabled", true);

			$("#TDPP").attr("readonly", true);			
			$("#TPPN").attr("readonly", true);
			$("#NETT").attr("readonly", true);	
			$("#TTOTAL").attr("readonly", true);	

		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#REC" + i.toString()).attr("readonly", true);
			$("#KD_BRG" + i.toString()).attr("readonly", false);
			$("#NA_BRG" + i.toString()).attr("readonly", true);
			$("#SATUAN_PO" + i.toString()).attr("readonly", true);
			$("#XQTY" + i.toString()).attr("readonly", false );
			$("#QTY_PO" + i.toString()).attr("readonly", true );
			$("#KALI" + i.toString()).attr("readonly", false );
			$("#SATUAN" + i.toString()).attr("readonly", true);
			$("#QTY" + i.toString()).attr("readonly", true);
			$("#HARGA" + i.toString()).attr("readonly", false);
			$("#TOTAL" + i.toString()).attr("readonly", true);
			$("#DPP" + i.toString()).attr("readonly", true);
			$("#PPN" + i.toString()).attr("readonly", true);
			$("#DISK" + i.toString()).attr("readonly", true);
			$("#KET" + i.toString()).attr("readonly", false);
			$("#DELETEX" + i.toString()).attr("hidden", false);

			$tipx = $('#tipx').val();
		
			
			if ( $tipx != 'new' )
			{
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
		$("#KODES").attr("readonly", true);
		$("#NAMAS").attr("readonly", true);
		$("#ALAMAT").attr("readonly", true);
		$("#KOTA").attr("readonly", true);
	    $("#PKP").attr("disabled", true);

			$("#NO_FAKTUR").attr("readonly", true);
			$("#TGL_FAKTUR").attr("readonly", true);
			$("#JTEMPO").attr("readonly", true);
			$("#TYPE").attr("readonly", true);

		$("#TDPP").attr("readonly", true);			
		$("#TPPN").attr("readonly", true);
		$("#NETT").attr("readonly", true);		
		$("#TTOTAL").attr("readonly", true);		
		
		$("#NOTES").attr("readonly", true);

		
		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#REC" + i.toString()).attr("readonly", true);
			$("#KD_BRG" + i.toString()).attr("readonly", true);
			$("#NA_BRG" + i.toString()).attr("readonly", true);
			$("#SATUAN_PO" + i.toString()).attr("readonly", true);
			$("#XQTY" + i.toString()).attr("readonly", true);
			$("#QTY_PO" + i.toString()).attr("readonly", true);
			$("#KALI" + i.toString()).attr("readonly", true);
			$("#SATUAN" + i.toString()).attr("readonly", true);
			$("#QTY" + i.toString()).attr("readonly", true);
			$("#HARGA" + i.toString()).attr("readonly", true);
			$("#TOTAL" + i.toString()).attr("readonly", true);
			$("#DPP" + i.toString()).attr("readonly", true);
			$("#PPN" + i.toString()).attr("readonly", true);
			$("#DISK" + i.toString()).attr("readonly", true);
			$("#KET" + i.toString()).attr("readonly", true);

			$("#DELETEX" + i.toString()).attr("hidden", true);
		}


		
	}


	function kosong() {
				
		 $('#NO_BUKTI').val("+");		
		 $('#KODES').val("");	
		 $('#NAMAS').val("");	
		 $('#ALAMAT').val("");	
		 $('#KOTA').val("");	
		 $('#NOTES').val("");	
		 $('#NO_PO').val("");
		 $('#PPN').val("0.00");
		 $('#NETT').val("0.00");
		 $('#DPP').val("0.00");
		 $('#PPNX').val("0.00");		 
	
		 $('#THARGAJL').val("0.00");
		 $('#TTOTAL').val("0.00");		 
		 
		 
		var html = '';
		$('#detailx').html(html);	
		
	}
	
	function hapusTrans() {
		let text = "Hapus Transaksi "+$('#NO_BUKTI').val()+"?";
		if (confirm(text) == true) 
		{
			window.location ="{{url('/harga/delete/'.$header->NO_ID .'/?flagz='.$flagz.'' )}}";
			//return true;
		} 
		return false;
	}

	// sweetalert untuk tombol hapus dan close
	
	function hapusTrans() {
		let text = "Hapus Transaksi "+$('#NO_BUKTI').val()+"?";

		var loc ='';
		var flagz = "{{ $flagz }}";
		
		Swal.fire({
			title: 'Are you sure?',
			text: text,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			if (result.isConfirmed) {
				// Show a success message before redirecting to delete the data
				Swal.fire({
					title: 'Deleted!',
					text: 'Data has been deleted.',
					icon: 'success',
					confirmButtonText: 'OK'
				}).then(() => {
					// Redirect to delete the data after user confirms the success message
	            	loc = "{{ url('/harga/delete/'.$header->NO_ID) }}" + '?flagz=' + encodeURIComponent(flagz) ;

		            // alert(loc);
	            	window.location = loc;
		
				});
			}
		});
	}
	
	function closeTrans() {
		var loc ='';
		var flagz = "{{ $flagz }}";
		
		Swal.fire({
			title: 'Are you sure?',
			text: 'Do you really want to close this page? Unsaved changes will be lost.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Yes, close it',
			cancelButtonText: 'No, stay here'
		}).then((result) => {
			if (result.isConfirmed) {
	        	loc = "{{ url('/harga/') }}" + '?flagz=' + encodeURIComponent(flagz);
				window.location = loc ;
			} else {
				Swal.fire({
					icon: 'info',
					title: 'Cancelled',
					text: 'You stayed on the page'
				});
			}
		});
	}

	// tutupannya
	

	function CariBukti() {
		
		var flagz = "{{ $flagz }}";
		var cari = $("#CARI").val();
		var loc = "{{ url('/harga/edit/') }}" + '?idx={{ $header->NO_ID}}&tipx=search&flagz=' + encodeURIComponent(flagz) + '&buktix=' +encodeURIComponent(cari);
		window.location = loc;
		
	}


    function tambah() {

        var x = document.getElementById('datatable').insertRow(baris + 1);
 
		html=`<tr>

                <td>
 					<input name='NO_ID[]' id='NO_ID${idrow}' type='hidden' class='form-control NO_ID' value='new' readonly> 
					<input name='REC[]' id='REC${idrow}' type='text' class='REC form-control' onkeypress='return tabE(this,event)' readonly>
	            </td>
				
				<td>
				    <input name='KD_BRG[]' data-rowid=${idrow} onblur='browseBarang(${idrow})' id='KD_BRG${idrow}' type='text' class='form-control  KD_BRG' >
                </td>
				
                <td {{ $flagz == 'HG' ? 'hidden' : '' }}>
				    <input name='BARCODE[]' id='BARCODE${idrow}' type='text' class='form-control  BARCODE' required readonly>
                </td>

                <td>
				    <input name='NA_BRG[]' id='NA_BRG${idrow}' type='text' class='form-control  NA_BRG' required readonly>
                </td>

                <td>
				    <input name='JNS[]' id='JNS${idrow}' type='text' class='form-control  JNS' readonly>
                </td>
				
				<td {{ $flagz == 'HT' ? 'hidden' : '' }}>
		            <input name='HARGAJL[]' onclick ='select()' value='0' id='HARGAJL${idrow}' type='text' style='text-align: right' class='form-control HARGAJL text-primary' readonly>
                </td>

				<td {{ $flagz == 'HT' ? 'hidden' : '' }}>
				    <input name='HARGAKSR[]' onclick ='select()' value='0'  id='HARGAKSR${idrow}' type='text' style='text-align: right' class='form-control  HARGAKSR'readonly>
                </td>

				<td>
					<input name='HARGA[]' onclick ='select()' value='0'  id='HARGA${idrow}' type='text' style='text-align: right' class='form-control  HARGA'>
				</td>

				<td {{ $flagz == 'HG' ? 'hidden' : ''}}>
					<input name='SISA[]' onclick ='select()' value='0'  id='SISA${idrow}' type='text' style='text-align: right' class='form-control  SISA' readonly>
				</td>

				<td {{ $flagz == 'HG' ? 'hidden' : ''}}>
					<input name='DTH[]' onclick ='select()' value='0'  id='DTH${idrow}' type='text' style='text-align: right' class='form-control  DTH'>
				</td>

				<td>
					<input name='KET[]'   id='KET${idrow}' type='text' class='form-control  KET' required >
				</td>
				
                <td>
					<button type='button' id='DELETEX${idrow}'  class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button>
                </td>				
         </tr>`;
				
        x.innerHTML = html;
        var html='';
		
		
		
		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#HARGAJL" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});

			$("#HARGAKSR" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});

			
			$("#HARGA" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});

			$("#SISA" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});

			$("#DTH" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});
			
		}

        idrow++;
        baris++;
        nomor();
		
		$(".ronly").on('keydown paste', function(e) {
             e.preventDefault();
             e.currentTarget.blur();
         });
     }
</script>

<script src="autonumeric.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
<script src="https://unpkg.com/autonumeric"></script>
@endsection