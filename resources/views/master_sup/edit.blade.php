@extends('layouts.plain')

<style>
    .card {

    }

    .form-control:focus {
        background-color: #E0FFFF !important;
    }

	/* perubahan tab warna di form edit  */
	.nav-item .nav-link.active {
		background-color: red !important; /* Use !important to ensure it overrides */
		color: white !important;
	}

    /* menghilangkan padding */
    .content-header {
        padding: 0 !important;
    }

	.form-group.row {
        margin-bottom: 8px; /* ubah sesuai kebutuhan */
    }

</style>


@section('content')


<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- /.content-header -->

    <div class="content">
        <div class="container-fluid">
        <div class="row">
            <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <form action="{{($tipx=='new')? url('/sup/store/') : url('/sup/update/'.$header->NO_ID ) }}" method="POST" name ="entri" id="entri" >
  
                        @csrf

						<ul class="nav nav-tabs">
                            <li class="nav-item active">
                                <a class="nav-link active" href="#suppInfo" data-toggle="tab">Main</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#bankInfo" data-toggle="tab">Bank Info</a>
                            </li>
                        </ul>
        
                        <div class="tab-content mt-3">
							<!-- style text box model baru -->

							<style>
								/* Ensure specificity with class targeting */
								.form-group.special-input-label {
									position: relative;
									margin-left: 5px ;
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
									color: #888 !important;
									font-size: 16px !important;
									transition: 0.3s ease all;
									pointer-events: none;
								}
						
								/* Move label above input when focused or has content */
								.form-group.special-input-label input:focus + label,
								.form-group.special-input-label input:not(:placeholder-shown) + label {
									top: -10px !important;
									font-size: 12px !important;
									color: #007BFF !important;
								}
							</style>

							<!-- tutupannya -->

							<div id="suppInfo" class="tab-pane active">	
							
								<div class="form-group row">

										<input type="text" class="form-control NO_ID" id="NO_ID" name="NO_ID"
										placeholder="Masukkan NO_ID" value="{{$header->NO_ID ?? ''}}" hidden readonly>

										<input name="tipx" class="form-control flagz" id="tipx" value="{{$tipx}}" hidden>
								

									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="KODES" id="KODES" name="KODES" 
											value="{{$header->KODES}}" placeholder=" " >
										<label for="KODES">Kode</label>
									</div>

									<div class="col-md-1">
									</div>

									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="NAMAS" id="NAMAS" name="NAMAS" 
											value="{{$header->NAMAS}}" placeholder=" " >
										<label for="NAMAS">Nama</label>
									</div>
									<!-- tutupannya -->

									<div class="col-md-1" align="right">
										<label for="TYPE" class="form-label">Tipe</label>
									</div>
									<div class="col-md-1">
										<select id="TYPE" class="form-control"  name="TYPE">
											<option value="A" {{ ($header->TYPE == 'A') ? 'selected' : '' }}>A</option>
											<option value="N" {{ ($header->TYPE == 'N') ? 'selected' : '' }}>N</option>
										</select>
									</div>	

									<div class="col-md-1 form-group row special-input-label">

										<input type="text" class="GSUP" id="GSUP" name="GSUP" 
											value="{{$header->GSUP}}" placeholder=" " >
										<label for="GSUP">Golongan</label>
									</div>		
								</div>
			
								<div class="form-group row">
									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="PEMILIK" id="PEMILIK" name="PEMILIK" 
											value="{{$header->PEMILIK}}" placeholder=" " >
										<label for="PEMILIK">Pemilik</label>
									</div>
									<!-- tutupannya --> 

									<div class="col-md-1">
									</div>

									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="P_TELP" id="P_TELP" name="P_TELP" 
											value="{{$header->P_TELP}}" placeholder=" " >
										<label for="P_TELP">Telp</label>
									</div>
									<!-- tutupannya --> 

									<div class="col-md-1">
									</div>

									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="EMAIL" id="EMAIL" name="EMAIL" 
											value="{{$header->EMAIL}}" placeholder=" " >
										<label for="EMAIL">Email</label>
									</div>
									<!-- tutupannya --> 

								</div>
			
								<div class="form-group row">

									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="P_ALMT" id="P_ALMT" name="P_ALMT" 
											value="{{$header->P_ALMT}}" placeholder=" " >
										<label for="P_ALMT">Alamat Kantor</label>
									</div>
									<!-- tutupannya -->

									<div class="col-md-1">
									</div>

									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="P_KOTA" id="P_KOTA" name="P_KOTA" 
											value="{{$header->P_KOTA}}" placeholder=" " >
										<label for="P_KOTA">Kota</label>
									</div>
									<!-- tutupannya --> 

									<div class="col-md-1">
									</div>

									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="G_ALMT" id="G_ALMT" name="G_ALMT" 
											value="{{$header->G_ALMT}}" placeholder=" " >
										<label for="G_ALMT">Alamat Gudang</label>
									</div>
									<!-- tutupannya --> 
								</div>

								<div class="form-group row">
									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="R_ALMT" id="R_ALMT" name="R_ALMT" 
											value="{{$header->R_ALMT}}" placeholder=" " >
										<label for="R_ALMT">Alamat Rumah</label>
									</div>
									<!-- tutupannya -->

									<div class="col-md-1">
									</div>

									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="P_TLP" id="P_TLP" name="P_TLP" 
											value="{{$header->P_TLP}}" placeholder=" " >
										<label for="P_TLP">Telp.Kantor</label>
									</div>
									<!-- tutupannya -->

									<div class="col-md-1">
									</div>

									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="P_FAX" id="P_FAX" name="P_FAX" 
											value="{{$header->P_FAX}}" placeholder=" " >
										<label for="P_FAX">Fax</label>
									</div>
									<!-- tutupannya -->
								</div>


								<div class="form-group row">
									
									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">
										<input type="text" class="R_TLP" id="R_TLP" name="R_TLP" 
											value="{{$header->R_TLP}}" placeholder=" " >
										<label for="R_TLP">Telp.Rumah</label>
									</div>
									<!-- tutupannya -->

									<div class="col-md-1">
									</div>

									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">
										<input type="text" class="P_POS" id="P_POS" name="P_POS" 
											value="{{$header->P_POS}}" placeholder=" " >
										<label for="P_POS">Kode Pos</label>
									</div>
									<!-- tutupannya -->
								</div>

								<div class="form-group row">
								</div>
								<div class="form-group row">
								</div>

								<div class="form-group row">
									
									<!-- code text box baru -->
									<div class="col-md-2 form-group row special-input-label">
										<input class="form-control date" id="TGL_M" name="TGL_M" 
											data-date-format="dd-mm-yyyy" type="text" autocomplete="off" 
											value="{{ $header->TGL_M ? date('d-m-Y', strtotime($header->TGL_M)) : date('d-m-Y') }}">
										<label for="TGL_M">Tgl Mulai</label>
									</div>
									<!-- tutupannya -->


									<!-- code text box baru -->
									<div class="col-md-2 form-group row special-input-label">
										<input class="form-control date" id="UPD_TGL" name="UPD_TGL" 
											data-date-format="dd-mm-yyyy" type="text" autocomplete="off" 
											value="{{ $header->UPD_TGL ? date('d-m-Y', strtotime($header->UPD_TGL)) : date('d-m-Y') }}">
										<label for="UPD_TGL">Tgl Update Terakhir</label>
									</div>
									<!-- tutupannya -->

									<div class="col-md-1">
									</div>

									<!-- code text box baru -->
									<div class="col-md-2 form-group row special-input-label">
										<input class="form-control date" id="TGL_PNG" name="TGL_PNG" 
											data-date-format="dd-mm-yyyy" type="text" autocomplete="off" 
											value="{{ $header->TGL_PNG ? date('d-m-Y', strtotime($header->TGL_PNG)) : date('d-m-Y') }}">
										<label for="TGL_PNG">Tgl Non Aktif</label>
									</div>
									<!-- tutupannya -->


									<!-- code text box baru -->
									<div class="col-md-2 form-group row special-input-label">
										<input class="form-control date" id="TGL_K" name="TGL_K" 
											data-date-format="dd-mm-yyyy" type="text" autocomplete="off" 
											value="{{ $header->TGL_K ? date('d-m-Y', strtotime($header->TGL_K)) : date('d-m-Y') }}">
										<label for="TGL_K">Tgl Pengajuan</label>
									</div>
									<!-- tutupannya -->
								</div>

								<div class="form-group row">
									
									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">
										<input type="text" class="KET_PRB" id="KET_PRB" name="KET_PRB" 
											value="{{$header->KET_PRB}}" placeholder=" " >
										<label for="KET_PRB">Keterangan</label>
									</div>
									<!-- tutupannya -->
								</div>

							</div>

							
							<!--------------------------------------------------->
							
							<div id="bankInfo" class="tab-pane">
				
								<div class="form-group row">
									
									<div class="col-md-3 form-group row special-input-label">
										<input type="text" class="B_BANK" id="B_BANK" name="B_BANK" 
											value="{{$header->B_BANK}}" placeholder=" " >
										<label for="B_BANK">Bank</label>
									</div>

									<div class="col-md-1">
									</div>   

									<!-- code text box baru -->
									<div class="col-md-2 form-group row special-input-label">
										<input type="text" class="NPWP" id="NPWP" name="NPWP" 
											value="{{$header->NPWP}}" placeholder=" " >
										<label for="NPWP">NPWP</label>
									</div>
								</div>
								
								<div class="form-group row">
									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="B_KOTA" id="B_KOTA" name="B_KOTA" 
											value="{{$header->B_KOTA}}" placeholder=" " >
										<label for="B_KOTA">Kota</label>
									</div>
									<!-- tutupannya -->	

									<div class="col-md-1">
									</div>

									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="NM_NPWP" id="NM_NPWP" name="NM_NPWP" 
											value="{{$header->NM_NPWP}}" placeholder=" " >
										<label for="NM_NPWP">Nama NPWP</label>
									</div>
									<!-- tutupannya -->     

								</div>

								<div class="form-group row">
									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="B_NAMA" id="B_NAMA" name="B_NAMA" 
											value="{{$header->B_NAMA}}" placeholder=" " >
										<label for="B_NAMA">A/n</label>
									</div>
									<!-- tutupannya -->	

									<div class="col-md-1">
									</div>

									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="NPPKPP" id="NPPKPP" name="NPPKPP" 
											value="{{$header->NPPKPP}}" placeholder=" " >
										<label for="NPPKPP">No NPWP</label>
									</div>
									<!-- tutupannya -->     

								</div>

								<div class="form-group row">
									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="B_ACC" id="B_ACC" name="B_ACC" 
											value="{{$header->B_ACC}}" placeholder=" " >
										<label for="B_ACC">Rek #</label>
									</div>
									<!-- tutupannya -->	

									<div class="col-md-1">
									</div>

									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="AL_NPWP" id="AL_NPWP" name="AL_NPWP" 
											value="{{$header->AL_NPWP}}" placeholder=" " >
										<label for="AL_NPWP">Alamat NPWP</label>
									</div>
									<!-- tutupannya -->     

								</div>
								
								<div class="form-group row">
									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="CARA" id="CARA" name="CARA" 
											value="{{$header->CARA}}" placeholder=" " >
										<label for="CARA">Cara</label>
									</div>
									<!-- tutupannya -->     

									<div class="col-md-1">
									</div>
									
									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="KT_NPWP" id="KT_NPWP" name="KT_NPWP" 
											value="{{$header->KT_NPWP}}" placeholder=" " >
										<label for="KT_NPWP">Kota</label>
									</div>
									<!-- tutupannya -->  
								</div>

								<div class="form-group row">
									<!-- code text box baru -->
									<div class="col-md-3 form-group row special-input-label">

										<input type="text" class="ADA_CNTP" id="ADA_CNTP" name="ADA_CNTP" 
											value="{{$header->ADA_CNTP}}" placeholder=" " >
										<label for="ADA_CNTP">Counter</label>
									</div>
									<!-- tutupannya --> 
								</div>
								
								<div class="form-group row">	
									<div class="col-md-2">
										<label>Biaya Angkut</label>
									</div>
									<div class="col-md-1">
										<input type="checkbox" class="form-check-input" id="BAY" name="BAY" value="1" {{ ($header->BAY == 1) ? 'checked' : '' }}>
										<label for="BAY">Ya</label>
									</div>	
									<div class="col-md-1">
										<input type="checkbox" class="form-check-input" id="BAN" name="BAN" value="1" {{ ($header->BAN == 1) ? 'checked' : '' }}>
										<label for="BAN">Tidak</label>
									</div>	
								</div>

								<div class="form-group row">
									<div class="col-md-1" >
										<label for="C_SP" class="form-label">SP</label>
									</div>
									<div class="col-md-2">
										<select id="C_SP" class="form-control"  name="C_SP">
											<option value="A" {{ ($header->C_SP == 'A') ? 'selected' : '' }}>A</option>
											<option value="N" {{ ($header->C_SP == 'N') ? 'selected' : '' }}>N</option>
										</select>
									</div>	
								</div>	
								
							</div>


							

							</div>

						</div>
        
						<div class="mt-3 col-md-12 form-group row">
							<div class="col-md-4">
								<button hidden type="button" id='TOPX'  onclick="location.href='{{url('/sup/edit/?idx=' .$idx. '&tipx=top')}}'" class="btn btn-outline-primary">Top</button>
								<button hidden type="button" id='PREVX' onclick="location.href='{{url('/sup/edit/?idx='.$header->NO_ID.'&tipx=prev&kodex='.$header->KODES )}}'" class="btn btn-outline-primary">Prev</button>
								<button hidden type="button" id='NEXTX' onclick="location.href='{{url('/sup/edit/?idx='.$header->NO_ID.'&tipx=next&kodex='.$header->KODES )}}'" class="btn btn-outline-primary">Next</button>
								<button hidden type="button" id='BOTTOMX' onclick="location.href='{{url('/sup/edit/?idx=' .$idx. '&tipx=bottom')}}'" class="btn btn-outline-primary">Bottom</button>
							</div>
							<div class="col-md-5">
								<button hidden type="button" id='NEWX' onclick="location.href='{{url('/sup/edit/?idx=0&tipx=new')}}'" class="btn btn-warning">New</button>
								<button hidden type="button" id='EDITX' onclick='hidup()' class="btn btn-secondary">Edit</button>                    
								<button hidden type="button" id='UNDOX' onclick="location.href='{{url('/sup/edit/?idx=' .$idx. '&tipx=undo' )}}'" class="btn btn-info">Undo</button> 
								<button type="button" id='SAVEX' onclick='simpan()' class="btn btn-success" class="fa fa-save"></i>Save</button>

							</div>
							<div class="col-md-3">
								<button hidden type="button" id='HAPUSX' hidden onclick="hapusTrans()" class="btn btn-outline-danger">Hapus</button>
								
								<!-- <button type="button" id='CLOSEX'  onclick="location.href='{{url('/sup' )}}'" class="btn btn-outline-secondary">Close</button> -->

								<!-- tombol close sweet alert -->
								<button type="button" id='CLOSEX' onclick="closeTrans()" class="btn btn-outline-secondary">Close</button></div>
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

	<div class="modal fade" id="browseKotaModal" tabindex="-1" role="dialog" aria-labelledby="browseKotaModalLabel" aria-hidden="true">
	 <div class="modal-dialog mw-100 w-75" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseKotaModalLabel">Cari Kota</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-kota">
				<thead>
					<tr>
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
    var target;
	var idrow = 1;

    $(document).ready(function () {

		$('body').on('keydown', 'input, select', function(e) {
			if (e.key === "Enter") {
				var self = $(this), form = self.parents('form:eq(0)'), focusable, next;
				focusable = form.find('input,select,textarea').filter(':visible');
				next = focusable.eq(focusable.index(this)+1);
				console.log(next);
				if (next.length) {
					next.focus().select();
				} else {
					// tambah();
					// var nomer = idrow-1;
					// console.log("REC"+nomor);
					// document.getElementById("REC"+nomor).focus();
					// form.submit();
				}
				return false;
			}
		});

		

		$('.date').datepicker({  
            dateFormat: 'dd-mm-yy'
		});

 		$tipx = $('#tipx').val();
				
        if ( $tipx == 'new' )
		{
			 baru();			
		}

        if ( $tipx != 'new' )
		{
			 //mati();	
    		 ganti();
		}    

		// $("#TELPON1").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999'});
		// $("#HP").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999'});
		
    });

//////////////////////////////////////////////////////////////////////////////////////////////////


		//CHOOSE Kota
		var dTableBKota;
		loadDataBKota = function(){
			$.ajax(
			{
				type: 'GET',    
				url: '{{url('kota/browse')}}',

				success: function( response )
				{
			
					resp = response;
					if(dTableBKota){
						dTableBKota.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBKota.row.add([
							'<a href="javascript:void(0);" onclick="chooseKota(\''+resp[i].KOTA+'\')">'+resp[i].KOTA+'</a>',
						]);
					}
					dTableBKota.draw();
				}
			});
		}
		
		dTableBKota = $("#table-kota").DataTable({
			
		});
		
		browseKota = function(){
			loadDataBKota();
			$("#browseKotaModal").modal("show");
		}
		
		chooseKota = function(KOTA){
			$("#KOTA").val(KOTA);
			$("#browseKotaModal").modal("hide");
		}
		
		$("#KOTA").keypress(function(e){

			if(e.keyCode == 46){
				e.preventDefault();
				browseKota();
			}
		}); 
		
		
		//////////////////////////////////////////////////////////////////////////////////////////////////


	function baru() {
		
		 kosong();
		 hidup();
		 
	}
	
	function ganti() {
		
		// mati();
		hidup();
	
	}
	
	
	function batal() {
			
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
		
		
 		$tipx = $('#tipx').val();
		
        if ( $tipx == 'new' )		
		{	
		  	
			$("#KODES").attr("readonly", false);	

		   }
		else
		{
	     	$("#KODES").attr("readonly", true);	

		   }
		   
		$("#PLH").attr("readonly", false);	
		$("#ALAMAT").attr("readonly", false);			
		$("#KOTA").attr("readonly", true);		
		$("#TELPON1").attr("readonly", false);			
		$("#FAX").attr("readonly", false);	
		$("#HP").attr("readonly", false);			
		$("#AKT").attr("readonly", false);		
		$('#KONTAK').attr("readonly", false);

		 $('#EMAIL').attr("readonly", false);	
		 $('#NPWP').attr("readonly", false);	
		 $('#KET').attr("readonly", false);


		 $('#BANK').attr("readonly", false);	
		 $('#BANK_CAB').attr("readonly", false);	
		 $('#BANK_KOTA').attr("readonly", false);	
		 $('#BANK_NAMA').attr("readonly", false);		
		 $('#BANK_REK').attr("readonly", false);
		 $('#HARI').attr("readonly", false);
		 $('#LIM').attr("readonly", false);	
	
	
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
		
		$("#KODES").attr("readonly", true);			
		$("#PLH").attr("readonly", true);	
		$("#ALAMAT").attr("readonly", true);			
		$("#KOTA").attr("readonly", true);		
		$("#TELPON1").attr("readonly", true);			
		$("#FAX").attr("readonly", true);	
		$("#HP").attr("readonly", true);			
		$("#AKT").attr("readonly", true);		
		$('#KONTAK').attr("readonly", true);

		 $('#EMAIL').attr("readonly", true);	
		 $('#NPWP').attr("readonly", true);	
		 $('#KET').attr("readonly", true);


		 $('#BANK').attr("readonly", true);	
		 $('#BANK_CAB').attr("readonly", true);	
		 $('#BANK_KOTA').attr("readonly", true);	
		 $('#BANK_NAMA').attr("readonly", true);		
		 $('#BANK_REK').attr("readonly", true);
		 $('#HARI').attr("readonly", true);
		 $('#LIM').attr("readonly", true);	
		
		
	

		
	}


	function kosong() {
				
		 $('#KODES').val("");	
		 $('#NAMAS').val("");	
		 $('#ALAMAT').val("");	
		 $('#KOTA').val("");		

		 $('#TELPON1').val("");	
		 $('#FAX').val("");	
		 $('#HP').val("");	
		 $('#AKT').val("0");		
		 $('#KONTAK').val("");

		 $('#EMAIL').val("");	
		 $('#NPWP').val("");	
		 $('#KET').val("");	


		 $('#BANK').val("");	
		 $('#BANK_CAB').val("");	
		 $('#BANK_KOTA').val("");	
		 $('#BANK_NAMA').val("");		
		 $('#BANK_REK').val("");
		 $('#HARI').val("0");
		 $('#LIM').val("0");		


		 
	}
	
	function hapusTrans() {
    }

	
	function hapusTransx() {
		let text = "Hapus Transaksi "+$('#NO_BUKTI').val()+"?";

		var loc ='';
		
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
	            	loc = "{{ url('/sup/delete/'.$header->NO_ID) }}"  ;

		            // alert(loc);
	            	window.location = loc;
		
				});
			}
		});
	}
	
	function closeTrans() {
		console.log("masuk");
		var loc ='';
		
		Swal.fire({
			title: 'Are you sure?',
			text: 'Do you really want to close this page? Unsaved changes will be lost.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Yes, close it',
			cancelButtonText: 'No, stay here'
		}).then((result) => {
			if (result.isConfirmed) {
	        	loc = "{{ url('/sup/') }}" ;
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
		
		var cari = $("#CARI").val();
		var loc = "{{ url('/sup/edit/') }}" + '?idx={{ $header->NO_ID}}&tipx=search&kodex=' +encodeURIComponent(cari);
		window.location = loc;
		
	}

     
     
    var hasilCek;
	function cekSup(kodes) {
		$.ajax({
			type: "GET",
			url: "{{url('sup/ceksup')}}",
            async: false,
			data: ({ KODES: kodes, }),
			success: function(data) {
                if (data.length > 0) {
                    $.each(data, function(i, item) {
                        hasilCek=data[i].ADA;
                    });
                }
			},
			error: function() {
				alert('Error cekSup occured');
			}
		});
		return hasilCek;
	}
    
	function simpan() {
        hasilCek=0;
		$tipx = $('#tipx').val();
				
        if ( $tipx == 'new' )
		{
			cekSup($('#KODES').val());		
		}
		

        (hasilCek==0) ? document.getElementById("entri").submit() : alert('Suplier '+$('#KODES').val()+' sudah ada!');
	}
</script>
@endsection

