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

                    <form action="{{($tipx=='new')? url('/lbharga/store?flagz='.$flagz.'&golz='.$golz.'') : url('/lbharga/update/'.$header->NO_ID.'&flagz='.$flagz.'&golz='.$golz.'' ) }}" method="POST" name ="entri" id="entri" >
  
                        @csrf
                        <div class="tab-content mt-3">
                            <div class="form-group row">
                                <div class="col-md-1" align="left">
                                    <label for="NO_BUKTI" class="form-label">Bukti#z</label>
                                </div>
								

                                   <input type="text" class="form-control NO_ID" id="NO_ID" name="NO_ID"
                                    placeholder="Masukkan NO_ID" value="{{$header->NO_ID ?? ''}}" hidden readonly>

									<input name="tipx" class="form-control tipx" id="tipx" value="{{$tipx}}" hidden>
									<input name="flagz" class="form-control flagz" id="flagz" value="{{$flagz}}" hidden>
									<input name="golz" class="form-control golz" id="golz" value="{{$golz}}" hidden>

								
								
                                <div class="col-md-2">
                                    <input type="text" class="form-control NO_BUKTI" id="NO_BUKTI" name="NO_BUKTI"
                                    placeholder="Masukkan Bukti#" value="{{$header->NO_BUKTI}}" readonly>
                                </div>
								
								<div class="col-md-1" align="left">								
									<label for="ACNO" class="form-label">Acno</label>
								</div>

								<div class="col-md-2 input-group" >
									<input type="text" class="form-control ACNO" id="ACNO" name="ACNO" placeholder="Pilih"value="{{$header->ACNO}}" style="text-align: left" readonly >
									<button type="button" class="btn btn-primary" onclick="browseAcno()"><i class="fa fa-search"></i></button>
								</div>
								
								<div class="col-md-3">
									<input type="text" class="form-control NACNO" id="NACNO" name="NACNO" placeholder=""  value="{{$header->NACNO}}"  readonly >
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
										<button type="button" class="btn btn-primary" onclick="browseSupplier()"><i class="fa fa-search"></i></button>
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
											<th width="150px" style="text-align:center">Tgl. Cetak</th>
											<th width="200px" style="text-align:center">Jam. Cetak</th>
											<th width="300px" style="text-align:center">No Nota</th>
											<th width="200px" style="text-align:center">Tgl Nota</th>
											<th width="200px" style="text-align:center">Besar</th>
											<th width="200px" style="text-align:center">Kecil</th>

											<th width="200px" style="text-align:center">Harga</th>
											<th width="200px" style="text-align:center">Total</th>  

											<th></th>
																
										</tr>
									<tbody id="detailPod">
			
									<tbody>
									<?php $no=0 ?>
									@foreach ($detail as $detail)		
										<tr>
											<td>
												<input type="hidden" name="NO_ID[]{{$no}}" id="NO_ID" type="text" value="{{$detail->NO_ID}}" 
												class="form-control NO_ID" onkeypress="return tabE(this,event)" readonly>
												
												<input name="REC[]" id="REC{{$no}}" type="text" value="{{$detail->REC}}" class="form-control REC" onkeypress="return tabE(this,event)" readonly style="text-align:center">
											</td>									 

											<td>
												<input name="TGL_CTK[]" id="TGL_CTK{{$no}}" type="text" class="form-control TGL_CTK date" data-date-format="dd-mm-yyyy"
												autocomplete="off" value="{{date('d-m-Y',strtotime($detail->TGL_CTK))}}">
											</td>

											<td>
												<input name="JAM_CTK[]" id="JAM_CTK{{$no}}" type="text" class="form-control JAM_CTK " value="{{$detail->JAM_CTK}}">
											</td>

											<td>
												<input name="NO_TRM[]" id="NO_TRM{{$no}}" type="text" class="form-control NO_TRM " value="{{$detail->NO_TRM}}">
											</td>

											<td>
												<input name="TGL_TRM[]" id="TGL_TRM{{$no}}" type="text" class="form-control TGL_TRM" value="{{$detail->TGL_TRM}}">
											</td>									
											<td>
												<input name="QTYB[]" onclick="select()" onblur="hitung()" value="{{$detail->QTYB}}" id="QTYB{{$no}}" type="text" style="text-align: right"  class="form-control QTYB" >
											</td>									
											<td>
												<input name="QTYK[]" onclick="select()" onblur="hitung()" value="{{$detail->QTYK}}" id="QTYK{{$no}}" type="text" style="text-align: right"  class="form-control QTYK" >
											</td>										
											<td>
												<input name="HARGA[]" onclick="select()" onblur="hitung()" value="{{$detail->HARGA}}" id="HARGA{{$no}}" type="text" style="text-align: right"  class="form-control HARGA" >
											</td>
																				
											<td>
												<input name="TOTAL[]" onclick="select()" onblur="hitung()" value="{{$detail->TOTAL}}" id="TOTAL{{$no}}" type="text" style="text-align: right"  class="form-control TOTAL" >
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
										<td></td>
										<td></td>									
										<td></td>
										<td><input class="form-control TTOTAL  text-primary" style="text-align: right"  id="TTOTAL" name="TTOTAL" value="{{$header->total}}" readonly></td>
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
								<button hidden type="button" id='TOPX'  onclick="location.href='{{url('/beli/edit/?idx=' .$idx. '&tipx=top&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-outline-primary">Top</button>
								<button hidden type="button" id='PREVX' onclick="location.href='{{url('/beli/edit/?idx='.$header->NO_ID.'&tipx=prev&flagz='.$flagz.'&golz='.$golz.'&buktix='.$header->NO_BUKTI )}}'" class="btn btn-outline-primary">Prev</button>
								<button hidden type="button" id='NEXTX' onclick="location.href='{{url('/beli/edit/?idx='.$header->NO_ID.'&tipx=next&flagz='.$flagz.'&golz='.$golz.'&buktix='.$header->NO_BUKTI )}}'" class="btn btn-outline-primary">Next</button>
								<button hidden type="button" id='BOTTOMX' onclick="location.href='{{url('/beli/edit/?idx=' .$idx. '&tipx=bottom&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-outline-primary">Bottom</button>
							</div>
							<div class="col-md-5">
								<button hidden type="button" id='NEWX' onclick="location.href='{{url('/beli/edit/?idx=0&tipx=new&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-warning">New</button>
								<button hidden type="button" id='EDITX' onclick='hidup()' class="btn btn-secondary">Edit</button>                    
								<button hidden type="button" id='UNDOX' onclick="location.href='{{url('/beli/edit/?idx=' .$idx. '&tipx=undo&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-info">Undo</button>  
								<button type="button" id='SAVEX' onclick='simpan()'   class="btn btn-success" class="fa fa-save"></i>Save</button>

							</div>
							<div class="col-md-3">
								<button hidden type="button" id='HAPUSX'  onclick="hapusTrans()" class="btn btn-outline-danger">Hapus</button>

								<!-- <button type="button" id='CLOSEX'  onclick="location.href='{{url('/beli?flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-outline-secondary">Close</button> -->
							
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


 	<div class="modal fade" id="browseAcnoModal" tabindex="-1" role="dialog" aria-labelledby="browseAcnoModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseAcnoModalLabel">Cari Acno</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bacno">
				<thead>
					<tr>
						<th>Acno#</th>
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
	
	<div class="modal fade" id="browseSupplierModal" tabindex="-1" role="dialog" aria-labelledby="browseSupplierModalLabel" aria-hidden="true">
	  <div class="modal-dialog mw-100 w-75" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseSupplierModalLabel">Cari Suplier</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bsupplier">
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
		
		$("#TTOTAL").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});



		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#QTYB" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#QTYK" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#HARGA" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#TOTAL" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
		}	


		
        $('body').on('click', '.btn-delete', function() {
			var val = $(this).parents("tr").remove();
			baris--;
			hitung();
			nomor();
			
		});

		$('.date').datepicker({  
            dateFormat: 'dd-mm-yyyy'
		});
		
		
 	
		////////////////////////////////////////////////////

		// BROWSE ACNO
		var dTableBAcno;
		loadDataBAcno = function(){
			$.ajax(
			{
				type: 'GET',    
				url: '{{url('account/browse')}}',
				success: function( response )
				{
					resp = response;
					if(dTableBAcno){
						dTableBAcno.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBAcno.row.add([
							'<a href="javascript:void(0);" onclick="chooseAcno(\''+resp[i].ACNO+'\',  \''+resp[i].NAMA+'\')">'+resp[i].ACNO+'</a>',
							resp[i].NAMA,
						]);
					}
					dTableBAcno.draw();
				}
			});
		}
		
		dTableBAcno = $("#table-bacno").DataTable({
			
		});
		
		browseAcno = function(){
			loadDataBAcno();
			$("#browseAcnoModal").modal("show");
		}
		
		chooseAcno = function(ACNO,NAMA){
			$("#ACNO").val(ACNO);
			$("#NACNO").val(NAMA);
			$("#browseAcnoModal").modal("hide");

		}
		
		$("#ACNO").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseAcno();
			}
		}); 

		var dTableBSupplier;
		loadDataBSupplier = function(){
			$.ajax(
			{
				type: 'GET',    
				url: '{{url('sup/browse')}}',
				success: function( response )
				{
					resp = response;
					if(dTableBSupplier){
						dTableBSupplier.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBSupplier.row.add([
							'<a href="javascript:void(0);" onclick="chooseSupplier(\''+resp[i].NO_SUPL+'\',  \''+resp[i].NAMA+'\')">'+resp[i].NO_SUPL+'</a>',
							resp[i].NAMA,
							resp[i].ALAMAT,
							resp[i].KOTA,
						]);
					}
					dTableBSupplier.draw();
				}
			});
		}
		
		dTableBSupplier = $("#table-bsupplier").DataTable({
			
		});
		
		browseSupplier = function(){
			loadDataBSupplier();
			$("#browseSupplierModal").modal("show");
		}
		
		chooseSupplier = function(NO_SUPL,NAMA){
			$("#KODES").val(NO_SUPL);
			$("#NAMAS").val(NAMA);
			$("#browseSupplierModal").modal("hide");

		}
		
		$("#KODES").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseSupplier();
			}
		}); 

	});


///////////////////////////////////////		
    

    function cekDetail(){
		var cekBarang = '';
		$(".KD_BRG").each(function() {
			
			let z = $(this).closest('tr');
			var KD_BRGX = z.find('.KD_BRG').val();
			var QTY_POX = parseFloat(z.find('.QTY_PO').val().replace(/,/g, ''));
			var SISAX = parseFloat(z.find('.SISA').val().replace(/,/g, ''));

			// alert(QTYX);
			// alert(SEDIAX);
			
			if( KD_BRGX =="" )
			{
					cekBarang = '1';
					
			}	

			if( QTY_POX > SISAX )
			{
					cekBarang = '1';
					
			}	
		});
		
		return cekBarang;
	}
	

 	function simpan() {
		hitung();
		
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


////////////////////////////////////////////////////////////////////////////////////////
		$tipx = $('#tipx').val();
		
        if ( $tipx != 'new' )
		{
		    
		    $pkp00 = $('#PKP').val();
		    $pkp11 = $('#ZPKP').val();
		    
		    
		    
			if ( $pkp00 != $pkp11   ) 
            {
               
                check = '1';
				Swal.fire({
					icon: 'warning',
					title: 'Warning',
					text: 'Type PKP beda dengan Type PKP awal.'
				});
				return;
                
            }			 
		}



////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////

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

   function hitung() {
		var TTOTAL_QTYB = 0;
		var TTOTAL = 0;
		var TTOTAL_QTYK = 0;

		
		$(".QTYB").each(function() {
			
			let z = $(this).closest('tr');
			var QTYBX = parseFloat(z.find('.QTYB').val().replace(/,/g, ''));
			var QTYKX = parseFloat(z.find('.QTYK').val().replace(/,/g, ''));
			var HARGAX = parseFloat(z.find('.HARGA').val().replace(/,/g, ''));	

            
            var TOTALX  =  ( QTYBX + QTYKX ) * HARGAX;
            
			z.find('.TOTAL').val(TOTALX);

		    z.find('.HARGA').autoNumeric('update');			
		    z.find('.QTYB').autoNumeric('update');			
		    z.find('.QTYK').autoNumeric('update');	
		    z.find('.TOTAL').autoNumeric('update');		

            TTOTAL_QTYB +=QTYBX;
			TTOTAL_QTYK +=QTYKX;		
            TTOTAL +=TOTALX;			
		
		});
		
		if(isNaN(TTOTAL)) TTOTAL = 0;

		$('#TTOTAL').val(numberWithCommas(TTOTAL));		
		$("#TTOTAL").autoNumeric('update');

	}
	

	
  
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
			$("#HARGA" + i.toString()).attr("readonly", true);
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
	
		 $('#TTOTAL_QTY').val("0.00");
		 $('#TTOTAL').val("0.00");		 
		 
		 
		var html = '';
		$('#detailx').html(html);	
		
	}
	
	function hapusTrans() {
		let text = "Hapus Transaksi "+$('#NO_BUKTI').val()+"?";
		if (confirm(text) == true) 
		{
			window.location ="{{url('/beli/delete/'.$header->NO_ID .'/?flagz='.$flagz.'&golz=' .$golz.'' )}}";
			//return true;
		} 
		return false;
	}

	// sweetalert untuk tombol hapus dan close
	
	function hapusTrans() {
		let text = "Hapus Transaksi "+$('#NO_BUKTI').val()+"?";

		var loc ='';
		var flagz = "{{ $flagz }}";
		var golz = "{{ $golz }}";
		
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
	            	loc = "{{ url('/beli/delete/'.$header->NO_ID) }}" + '?flagz=' + encodeURIComponent(flagz) + 
						  '&golz=' + encodeURIComponent(golz) ;

		            // alert(loc);
	            	window.location = loc;
		
				});
			}
		});
	}
	
	function closeTrans() {
		var loc ='';
		var flagz = "{{ $flagz }}";
		var golz = "{{ $golz }}";
		
		Swal.fire({
			title: 'Are you sure?',
			text: 'Do you really want to close this page? Unsaved changes will be lost.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Yes, close it',
			cancelButtonText: 'No, stay here'
		}).then((result) => {
			if (result.isConfirmed) {
	        	loc = "{{ url('/beli/') }}" + '?flagz=' + encodeURIComponent(flagz) + '&golz=' + encodeURIComponent(golz) ;
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
		var golz = "{{ $golz }}";
		var cari = $("#CARI").val();
		var loc = "{{ url('/beli/edit/') }}" + '?idx={{ $header->NO_ID}}&tipx=search&flagz=' + encodeURIComponent(flagz) + '&golz=' + encodeURIComponent(golz) + '&buktix=' +encodeURIComponent(cari);
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
				    <input name='TGL_CTK[]' data-rowid=${idrow} id='TGL_CTK${idrow}' type='text' class='form-control TGL_CTK date' data-date-format="dd-mm-yyyy" autocomplete="off">
                </td>
				
                <td>
				    <input name='JAM_CTK[]' id='JAM_CTK${idrow}' type='text' class='form-control  JAM_CTK' required >
                </td>

                <td>
				    <input name='NO_TRM[]' id='NO_TRM${idrow}' type='text' class='form-control  NO_TRM' required >
                </td>

                <td>
				    <input name='TGL_TRM[]' id='TGL_TRM${idrow}' type='text' class='form-control TGL_TRM date' data-date-format="dd-mm-yyyy" autocomplete="off">
                </td>
				
				<td>
		            <input name='QTYB[]' onclick ='select()' onblur='hitung()' value='0' id='QTYB${idrow}' type='text' style='text-align: right' class='form-control QTYB text-primary' >
                </td>

				<td>
				    <input name='QTYK[]' onclick ='select()' value='0' onblur='hitung()'  id='QTYK${idrow}' type='text' style='text-align: right' class='form-control  QTYK'>
                </td>

				<td>
					<input name='HARGA[]' onclick ='select()' value='0' onblur='hitung()'  id='HARGA${idrow}' type='text' style='text-align: right' class='form-control  HARGA'>
				</td>

				<td>
					<input name='TOTAL[]'   id='TOTAL${idrow}' onclick ='select()' value='0' onblur='hitung()' type='text' class='form-control  TOTAL' required >
				</td>
				
                <td>
					<button type='button' id='DELETEX${idrow}'  class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button>
                </td>				
         </tr>`;
				
        x.innerHTML = html;
        var html='';
		
		
		
		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#QTYB" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});

			$("#QTYK" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});

			
			$("#HARGA" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});
			$("#TOTAL" + i.toString()).autoNumeric('init', {
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