@extends('layouts.plain')

<style>
    .card {

    }

    .form-control:focus {
        background-color: #b5e5f9 !important;
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

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dropdown with Select2</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
</head>


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

                    <form action="{{($tipx=='new')? url('/stockb/store?flagz='.$flagz.'') : url('/stockb/update/'.$header->NO_ID.'&flagz='.$flagz.'' ) }}" method="POST" name ="entri" id="entri" >
  
                        @csrf

						<ul class="nav nav-tabs">
                            <li class="nav-item active">
                                <a class="nav-link active" href="#main" data-toggle="tab">Main</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#bayar" data-toggle="tab" >*</a>
                            </li>
                        </ul>


                        <div class="tab-content mt-3">
							
								<div id="main" class="tab-pane active">

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

										<div class="col-md-2">
											<input type="text" class="form-control NOTRANS" id="NOTRANS" name="NOTRANS"
											placeholder="" value="{{$header->NOTRANS}}" readonly>
										</div>

										<div class="col-md-1">
										</div>

										<!-- code text box baru -->
										<div class="col-md-3 form-group row special-input-label">

											<input type="text" class="NOREK" id="NOREK" name="NOREK" 
												value="{{$header->NOREK}}" placeholder=" " >
											<label for="NOREK">Transfer Ke</label>
										</div>
										<!-- tutupannya -->
									</div>


									<div class="form-group row">

										<div class="col-md-1" align="left">
											<label for="TGL" class="form-label">Tgl</label>
										</div>
										<div class="col-md-2">
										<input class="form-control date" id="TGL" name="TGL" data-date-format="dd-mm-yyyy" type="text" autocomplete="off" value="{{date('d-m-Y',strtotime($header->TGL))}}">
										</div>

										
										<div class="col-md-1" align="left">
											<label for="TYPE" class="form-label">Type</label>
										</div>
										
										<div class="col-md-1">
											<select id="TYPE" class="form-control"  name="TYPE">
												<option value="CASH" {{ ($header->TYPE == 'CASH') ? 'selected' : '' }}>Cash</option>
												<option value="KREDIT" {{ ($header->TYPE == 'KREDIT') ? 'selected' : '' }}>Kredit</option>
											</select>
										</div>

										<div class="col-md-1">
										</div>

										<!-- code text box baru -->
										<div class="col-md-2 form-group row special-input-label">

											<input type="text" class="NMBANK" id="NMBANK" name="NMBANK" 
												value="{{$header->NMBANK}}" placeholder=" " >
											<label for="NMBANK">Bank</label>
										</div>
										<!-- tutupannya -->
										<div class="col-md-2">
										<input class="form-control date" id="TGTF" name="TGTF" data-date-format="dd-mm-yyyy" type="text" autocomplete="off" value="{{date('d-m-Y',strtotime($header->TGTF))}}">
										</div>
										
									</div>

									<div class="form-group row">
										<!-- code text box baru -->
										<div class="col-md-4 form-group row special-input-label">

											<input type="text" class="NOTES" id="NOTES" name="NOTES" 
												value="{{$header->NOTES}}" placeholder=" " >
											<label for="NOTES">Notes</label>
										</div>
										<!-- tutupannya -->
									</div>
									
									<!-- loader tampil di modal  -->
									<div class="loader" style="z-index: 1055;" id='LOADX' ></div>


								<!-- <div class="tab-content mt-3"> -->
									
								<div style="overflow-y:scroll; height:200px;" class="col-md-12 scrollable" align="right">
									<!-- <div style="overflow-y:scroll; " class="col-md-12 scrollable" align="right"> -->
									
									<!-- <table id="datatable" class="table table-striped table-border table-scrollable">                 -->
									<table id="datatable" class="table table-striped table-border">   

										<thead>
											<tr>
												<th width="50px" style="text-align:center">No.</th>
												<th width="200px" style="text-align:center">No.Ag</th>
												<th width="200px" style="text-align:center">CNT</th>
												<th width="200px" style="text-align:center">SPJ</th>
												<th width="150px" style="text-align:center">Tgl</th>
												<th width="200px" style="text-align:center">No.Sp</th>
												<th width="150px" style="text-align:center">Nilai Nota</th>
												<th width="150px" style="text-align:center">Ppn</th>
												<th width="150px" style="text-align:center">Nilai Terima</th>
												<th width="150px" style="text-align:center">Acno</th>
												<th width="200px" style="text-align:center">Nama Acno</th>
												<th width="200px" style="text-align:center">Ket</th>

												<th></th>
																	
											</tr>
										</thead>
				
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
													<input name="NO_TRM[]" id="NO_TRM{{$no}}" type="text" class="form-control NO_TRM " 
													value="{{$detail->NO_TRM}}" onblur="browseBarang({{$no}})">
												</td>

												<td>
													<input name="CNT[]" id="CNT{{$no}}" type="text" class="form-control CNT " value="{{$detail->CNT}}">
												</td>
												<td>
													<input name="ST_PJK[]" id="ST_PJK{{$no}}" type="text" value="{{$detail->ST_PJK}}" class="form-control ST_PJK" readonly required>
												</td>
												<td>
													<input name="TGL_TRM[]" id	="TGL_TRM{{$no}}" type="text" class="date form-control text_input TGL_TRM" data-date-format="dd-mm-yyyy" value="{{($detail->TGL_TRM=='0000-00-00')?'00-00-0000':date('d-m-Y',strtotime($detail->TGL_TRM));}}">
												</td>
												<td>
													<input name="NO_SP[]" id="NO_SP{{$no}}" type="text" value="{{$detail->NO_SP}}" class="form-control NO_SP" readonly required>
												</td>
												
												<td><input name="TOTAL[]" onclick="select()" onkeyup="hitung()" value="{{$detail->TOTAL}}" id="TOTAL{{$no}}" type="text" style="text-align: right"  class="form-control TOTAL text-primary"></td>                         
												<td><input name="PPN[]" onclick="select()" onkeyup="hitung()" value="{{$detail->PPN}}" id="PPN{{$no}}" type="text" style="text-align: right"  class="form-control PPN text-primary"></td>
												<td><input name="TOTALX[]" onclick="select()" onkeyup="hitung()" value="{{$detail->TOTALX}}" id="TOTALX{{$no}}" type="text" style="text-align: right"  class="form-control TOTALX text-primary" readonly></td>
												
												<td>
													<input name="ACNO[]" id="ACNO{{$no}}" type="text" value="{{$detail->ACNO}}" class="form-control ACNO" readonly required>
												</td>
												<td>
													<input name="NACNO[]" id="NACNO{{$no}}" type="text" value="{{$detail->NACNO}}" class="form-control NACNO" readonly required>
												</td>
												<td>
													<input name="KET[]" id="KET{{$no}}" type="text" class="form-control KET" value="{{$detail->KET}}" required>
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
											<!-- <td><input class="form-control TTOTAL_QTY  text-primary font-weight-bold" style="text-align: right"  id="TTOTAL_QTY" name="TTOTAL_QTY" value="{{$header->TOTAL_QTY}}" readonly></td> -->
											<td></td>
											<td></td>
										</tfoot>
									</table>

									

								</div>
												
								<!-- batas -->

									
									<!-- <div class="col-md-2 row">
									<a type="button" id='PLUSX' onclick="tambah()" class="fas fa-plus fa-sm md-3" style="font-size: 20px" ></a>
							
									</div>		 -->

								<!-- <hr style="margin-top: 30px; margin-buttom: 30px"> -->

								<div class="tab-content mt-6">
								
									<div class="form-group row">

										<div class="col-md-6">
										<a type="button" id='PLUSX' onclick="tambah()" class="fas fa-plus fa-sm md-3" style="font-size: 20px" ></a>
								
										</div>		

									</div>		

									<div class="form-group row">
										
										<div class="col-md-3 input-group" >
											<input type="text" class="form-control uppercase KOTA" id="KOTA" name="KOTA" placeholder=""value="{{$header->KOTA}}" style="text-align: left" >
										</div>

										<div class="col-md-1">
										</div>

										<div class="col-md-2" align="right">
											<label for="TRETUR" class="form-label">Retur</label>
										</div>

										<div class="col-md-2">
											<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TRETUR" id="TRETUR" name="TRETUR" placeholder="" value="{{$header->RETUR}}" style="text-align: right" readonly>
										</div>

										<div class="col-md-2" align="right">
											<label for="TTOTALX" class="form-label">Total Nota</label>
										</div>

										<div class="col-md-2">
											<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TTOTALX" id="TTOTALX" name="TTOTALX" placeholder="" value="{{$header->TOTALX}}" style="text-align: right" readonly>
										</div>
									</div>

									<div class="form-group row">
										
										<div class="col-md-3 input-group" >
											<input type="text" class="form-control uppercase ALAMAT" id="ALAMAT" name="ALAMAT" placeholder=""value="{{$header->ALAMAT}}" style="text-align: left" >
										</div>

										<div class="col-md-1">
										</div>

										<div class="col-md-2" align="right">
											<label for="TBLAIN" class="form-label">Transaksi Lain2</label>
										</div>

										<div class="col-md-2">
											<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TBLAIN" id="TBLAIN" name="TBLAIN" placeholder="" value="{{$header->BLAIN}}" style="text-align: right" readonly>
										</div>

										<div class="col-md-2" align="right">
											<label for="TLAIN" class="form-label">--------------</label>
										</div>

										<div class="col-md-2">
											<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TLAIN" id="TLAIN" name="TLAIN" placeholder="" value="{{$header->LAIN}}" style="text-align: right" readonly>
										</div>

										
									</div>
									
									<div class="form-group row">
										<div class="col-md-6" align="right">
											<label for="TBADM" class="form-label">Administrasi</label>
										</div>
										<div class="col-md-2">
											<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TBADM" id="TBADM" name="TBADM" placeholder="" value="{{$header->BADM}}" style="text-align: right" readonly>
										</div>

										<div class="col-md-2" align="right">
											<label for="TPPN" class="form-label">PPN</label>
										</div>

										<div class="col-md-2">
											<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TPPN" id="TPPN" name="TPPN" placeholder="" value="{{$header->PPN}}" style="text-align: right" readonly>
										</div>

									</div>
									
									<div class="form-group row">
										<div class="col-md-6" align="right">
											<label for="TREFUND" class="form-label">Refund Faktur</label>
										</div>
										<div class="col-md-2">
											<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TREFUND" id="TREFUND" name="TREFUND" placeholder="" value="{{$header->REFUND}}" style="text-align: right" readonly>
										</div>

										<div class="col-md-2" align="right">
											<label for="TTOTAL" class="form-label">Grand Total</label>
										</div>

										<div class="col-md-2">
											<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TTOTAL" id="TTOTAL" name="TTOTAL" placeholder="" value="{{$header->TOTAL}}" style="text-align: right" readonly>
										</div>

									</div>
									
									<div class="form-group row">
										<div class="col-md-6" align="right">
											<label for="TPROMOS" class="form-label">Promosi</label>
										</div>
										<div class="col-md-2">
											<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TPROMOS" id="TPROMOS" name="TPROMOS" placeholder="" value="{{$header->PROMOS}}" style="text-align: right" readonly>
										</div>
									</div>
									
								</div>
						</div>

						

						<div id="bayar" class="tab-pane">

							<div class="form-group row">
                                <div class="col-md-1">							
                                    <label for="KODES" class="form-label">Supplier</label>
                                </div>
                                <div class="col-md-2 input-group" >
                                  <input type="text" class="form-control KODES" id="KODES" name="KODES" placeholder="" value= "{{$header->KODES}}" style="text-align: left" readonly >
                                </div>

								<div class="col-md-1">
                                    <input type="text" class="form-control GOLONGAN" id="GOLONGAN" name="GOLONGAN"
                                    placeholder="" value="{{$header->GOLONGAN}}" readonly >
                                </div>

								<div class="col-md-1">
                                </div>

								<div class="col-md-1">
									<input type="checkbox" class="form-check-input" id="POSTED" name="POSTED" value="1" {{ ($header->POSTED == 1) ? 'checked' : '' }}>
									<label for="POSTED">Posted</label>
                            	</div>
                            </div>

							<div class="form-group row">
								<div class="col-md-1">							
                                    <label class="form-label"></label>
                                </div>	
								<div class="col-md-4">
                                    <input type="text" class="form-control NAMAS" id="NAMAS" name="NAMAS"
                                    placeholder="" value="{{$header->NAMAS}}" readonly >
                                </div>
								
							</div>	

							<div class="form-group row">
								<div class="col-md-1">							
                                    <label for="CARA" class="form-label">Cara Bayar</label>
                                </div>	
								<div class="col-md-2">
                                    <input type="text" class="form-control CARA" id="CARA" name="CARA"
                                    placeholder="" value="{{$header->CARA}}"  >
                                </div>

								<div class="col-md-1">
									<input type="checkbox" class="form-check-input" id="EMAIL" name="EMAIL" value="1" {{ ($header->EMAIL == 1) ? 'checked' : '' }}>
									<label for="EMAIL">Biaya Adm.</label>
                            	</div>
								
							</div>	

							<div class="form-group row">
								<div class="col-md-1">							
                                    <label for="KLB" class="form-label">KLB</label>
                                </div>	
								<div class="col-md-2">
                                    <input type="text" class="form-control KLB" id="KLB" name="KLB"
                                    placeholder="" value="{{$header->KLB}}"  >
                                </div>

								<div class="col-md-2">
                                    <input type="text" class="form-control ANB" id="ANB" name="ANB"
                                    placeholder="" value="{{$header->ANB}}"  >
                                </div>

								
								
							</div>	

						</div>
						   
						<div class="mt-3 col-md-12 form-group row">
							<div class="col-md-4">
								<button hidden type="button" id='TOPX'  onclick="location.href='{{url('/stockb/edit/?idx=' .$idx. '&tipx=top&flagz='.$flagz.'' )}}'" class="btn btn-outline-primary">Top</button>
								<button hidden type="button" id='PREVX' onclick="location.href='{{url('/stockb/edit/?idx='.$header->NO_ID.'&tipx=prev&flagz='.$flagz.'&buktix='.$header->NO_BUKTI )}}'" class="btn btn-outline-primary">Prev</button>
								<button hidden type="button" id='NEXTX' onclick="location.href='{{url('/stockb/edit/?idx='.$header->NO_ID.'&tipx=next&flagz='.$flagz.'&buktix='.$header->NO_BUKTI )}}'" class="btn btn-outline-primary">Next</button>
								<button hidden type="button" id='BOTTOMX' onclick="location.href='{{url('/stockb/edit/?idx=' .$idx. '&tipx=bottom&flagz='.$flagz.'' )}}'" class="btn btn-outline-primary">Bottom</button>
							</div>
							<div class="col-md-5">
								<button hidden type="button" id='NEWX' onclick="location.href='{{url('/stockb/edit/?idx=0&tipx=new&flagz='.$flagz.'' )}}'" class="btn btn-warning">New</button>
								<button hidden type="button" id='EDITX' onclick='hidup()' class="btn btn-secondary">Edit</button>                    
								<button hidden type="button" id='UNDOX' onclick="location.href='{{url('/stockb/edit/?idx=' .$idx. '&tipx=undo&flagz='.$flagz.'' )}}'" class="btn btn-info">Undo</button>  
								<button type="button" id='SAVEX' onclick='simpan()'   class="btn btn-success" class="fa fa-save"></i>Save</button>

							</div>
							<div class="col-md-3">
								<button hidden type="button" id='HAPUSX'  onclick="hapusTrans()" class="btn btn-outline-danger">Hapus</button>
								
								<!-- <button type="button" id='CLOSEX'  onclick="location.href='{{url('/stockb?flagz='.$flagz.'' )}}'" class="btn btn-outline-secondary">Close</button> -->
							
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


	<div class="modal fade" id="browseBarangModal" tabindex="-1" role="dialog" aria-labelledby="browseBarangModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
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
						<th>Item#</th>
						<th>Nama</th>
						<th>Satuan</th>
						
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

<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script> -->

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
					var nomer = idrow-1;
					console.log("KD_BRG"+nomor);
					document.getElementById("KD_BRG"+nomor).focus();
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
		
		$("#TRETUR").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TTOTALX").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TBLAIN").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TLAIN").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TBADM").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TPPN").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TREFUND").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TTOTAL").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TPROMOS").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});


		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#TOTAL" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#PPN" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#TOTALX" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
		}	
				
		
        $('body').on('click', '.btn-delete', function() {
			var val = $(this).parents("tr").remove();
			baris--;
			hitung();
			nomor();
			
		});

		$('.date').datepicker({  
            dateFormat: 'dd-mm-yy'
		});
		
		


		//////////////////////////////////////////////////////

		var dTableBBarang;
		var rowidBarang;
		loadDataBBarang = function(){
		
			$.ajax(
			{
				type: 'GET',    
				url: "{{url('brg/browse_koreksi')}}",

				beforeSend: function(){
					$("#LOADX").show();
				},

				async : false,
				data: {
						'KD_BRG': $("#KD_BRG"+rowidBarang).val(),
					
				},

				success: function( response )

				{

					$("#LOADX").hide();

					resp = response;
					
					
					if ( resp.length > 1 )
					{	
							if(dTableBBarang){
								dTableBBarang.clear();
							}
							for(i=0; i<resp.length; i++){
								
								dTableBBarang.row.add([
									'<a href="javascript:void(0);" onclick="chooseBarang(\''+resp[i].KD_BRG+'\', \''+resp[i].NA_BRG+'\' , \''+resp[i].SATUAN+'\' )">'+resp[i].KD_BRG+'</a>',
									resp[i].NA_BRG,
									resp[i].SATUAN,
								]);
							}
							dTableBBarang.draw();
					
					}
					else
					{
						$("#KD_BRG"+rowidBarang).val(resp[0].KD_BRG);
						$("#NA_BRG"+rowidBarang).val(resp[0].NA_BRG);
						$("#SATUAN"+rowidBarang).val(resp[0].SATUAN);
					}
				}
			});
		}
		
		dTableBBarang = $("#table-bbarang").DataTable({
			
		});

		browseBarang = function(rid){
			rowidBarang = rid;
			$("#NA_BRG"+rowidBarang).val("");			
			loadDataBBarang();
	
			
			if ( $("#NA_BRG"+rowidBarang).val() == '' ) {				
					$("#browseBarangModal").modal("show");
			}	
		}
		
		chooseBarang = function(KD_BRG,NA_BRG,SATUAN){
			$("#KD_BRG"+rowidBarang).val(KD_BRG);
			$("#NA_BRG"+rowidBarang).val(NA_BRG);	
			$("#SATUAN"+rowidBarang).val(SATUAN);
			$("#browseBarangModal").modal("hide");
		}
		
		
		/* $("#RAK0").onblur(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseRak(0);
			}
		});  */

		////////////////////////////////////////////////////
	});



///////////////////////////////////////		
    



	function cekDetail(){
		var cekBarang = '';
		$(".KD_BRG").each(function() {
			
			let z = $(this).closest('tr');
			var KD_BRGX = z.find('.KD_BRG').val();
			
			if( KD_BRGX =="" )
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

			if (baris==0)
			{
				check = '1';
				Swal.fire({
					icon: 'warning',
					title: 'Warning',
					text: 'Data detail kosong (Tambahkan 1 baris kosong jika ingin mengosongi detail)'
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
				alert("Bulan tidak sama dengan Periode");
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

			if ( $('#KD_BRG').val()=='' ) 
            {				
			    check = '1';
				Swal.fire({
					icon: 'warning',
					title: 'Warning',
					text: 'Barang# Harus Diisi.'
				});
				return; // Stop function execution
			}

			// if ( $('#KD_BHN').val()=='' ) 
            // {				
			//     check = '1';
			// 	Swal.fire({
			// 		icon: 'warning',
			// 		title: 'Warning',
			// 		text: 'Bahan# Harus Diisi.'
			// 	});
			// 	return; // Stop function execution
			// }

        
			// if ( $('#NO_BUKTI').val()=='' ) 
            // {				
			//     check = '1';
			// 	Swal.fire({
			// 		icon: 'warning',
			// 		title: 'Warning',
			// 		text: 'Bukti# Harus Diisi.'
			// 	});
			// 	return; // Stop function execution
			// }
		
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
		var TTOTAL_QTY = 0;


		$(".QTY").each(function() {
			
			let z = $(this).closest('tr');
			var QTYRX = parseFloat(z.find('.QTYR').val().replace(/,/g, ''));
			var QTYCX = parseFloat(z.find('.QTYC').val().replace(/,/g, ''));
		
            var QTYX  = QTYRX - QTYCX;
			z.find('.QTY').val(QTYX);
			
		    z.find('.QTY').autoNumeric('update');
		
            TTOTAL_QTY +=QTYX;				
		
		});
		
		
		if(isNaN(TTOTAL_QTY)) TTOTAL_QTY = 0;

		$('#TTOTAL_QTY').val(numberWithCommas(TTOTAL_QTY));		
		$("#TTOTAL_QTY").autoNumeric('update');
		
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
			$("#NOTRANS").attr("readonly", false);
			$("#NOREK").attr("readonly", false);
			$("#TYPE").attr("readonly", false);
			$("#NMBANK").attr("readonly", false);
			$("#TGTF").attr("readonly", false);
			$("#NOTES").attr("readonly", false);

			$("#KOTA").attr("readonly", false);
			$("#ALAMAT").attr("readonly", false);
			$("#TRETUR").attr("readonly", true);
			$("#TTOTALX").attr("readonly", true);
			$("#TBLAIN").attr("readonly", true);
			$("#TLAIN").attr("readonly", true);
			$("#TBADM").attr("readonly", true);
			$("#TPPN").attr("readonly", true);
			$("#TREFUND").attr("readonly", true);
			$("#TTOTAL").attr("readonly", true);
			$("#TPROMOS").attr("readonly", true);

			$("#KODES").attr("readonly", true);
			$("#NAMAS").attr("readonly", true);
			$("#GOLONGAN").attr("readonly", true);
			$("#POSTED").attr("disabled", false);
			$("#CARA").attr("disabled", false);
			$("#EMAIL").attr("readonly", false);
			$("#KLB").attr("readonly", false);
			$("#ANB").attr("readonly", false);
				

		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#REC" + i.toString()).attr("readonly", true);
			$("#NO_TRM" + i.toString()).attr("readonly", false);
			$("#CNT" + i.toString()).attr("readonly", false);
			$("#ST_PJK" + i.toString()).attr("readonly", false);
			$("#TGL_TRM" + i.toString()).attr("disabled", false);
			$("#NO_SP" + i.toString()).attr("readonly", false);
			$("#TOTAL" + i.toString()).attr("readonly", false);
			$("#PPN" + i.toString()).attr("readonly", false);
			$("#TOTALX" + i.toString()).attr("readonly", false);
			$("#ACNO" + i.toString()).attr("readonly", false);
			$("#NACNO" + i.toString()).attr("readonly", false);
			$("#KET" + i.toString()).attr("readonly", false);
			$("#DELETEX" + i.toString()).attr("hidden", false);

			// $tipx = $('#tipx').val();
		
			
			// if ( $tipx != 'new' )
			// {
			// 	$("#KD_BRG" + i.toString()).attr("readonly", true);	
			// 	$("#KD_BRG" + i.toString()).removeAttr('onblur');
			// } 
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
		
			$("#NO_BUKTI").attr("readonly", true);		   
			$("#TGL").attr("readonly", true);
			$("#NOTRANS").attr("readonly", true);
			$("#NOREK").attr("readonly", true);
			$("#TYPE").attr("readonly", true);
			$("#NMBANK").attr("readonly", true);
			$("#TGTF").attr("readonly", true);
			$("#NOTES").attr("readonly", true);

			$("#KOTA").attr("readonly", true);
			$("#ALAMAT").attr("readonly", true);
			$("#TRETUR").attr("readonly", true);
			$("#TTOTALX").attr("readonly", true);
			$("#TBLAIN").attr("readonly", true);
			$("#TLAIN").attr("readonly", true);
			$("#TBADM").attr("readonly", true);
			$("#TPPN").attr("readonly", true);
			$("#TREFUND").attr("readonly", true);
			$("#TTOTAL").attr("readonly", true);
			$("#TPROMOS").attr("readonly", true);

			$("#KODES").attr("readonly", true);
			$("#NAMAS").attr("readonly", true);
			$("#GOLONGAN").attr("readonly", true);
			$("#POSTED").attr("disabled", true);
			$("#CARA").attr("disabled", true);
			$("#EMAIL").attr("readonly", true);
			$("#KLB").attr("readonly", true);
			$("#ANB").attr("readonly", true);

		
		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#REC" + i.toString()).attr("readonly", true);
			$("#NO_TRM" + i.toString()).attr("readonly", true);
			$("#CNT" + i.toString()).attr("readonly", true);
			$("#ST_PJK" + i.toString()).attr("readonly", true);
			$("#TGL_TRM" + i.toString()).attr("disabled", true);
			$("#NO_SP" + i.toString()).attr("readonly", true);
			$("#TOTAL" + i.toString()).attr("readonly", true);
			$("#PPN" + i.toString()).attr("readonly", true);
			$("#TOTALX" + i.toString()).attr("readonly", true);
			$("#ACNO" + i.toString()).attr("readonly", true);
			$("#NACNO" + i.toString()).attr("readonly", true);
			$("#KET" + i.toString()).attr("readonly", true);
			
			$("#DELETEX" + i.toString()).attr("hidden", true);
		}


		
	}


	function kosong() {
				
		 $('#NO_BUKTI').val("+");	
		 $('#NOTES').val("");	
		 $('#TTOTAL_QTY').val("0.00");	
		 
		var html = '';
		$('#detailx').html(html);	
		
	}
	
	// function hapusTrans() {
	// 	let text = "Hapus Transaksi "+$('#NO_BUKTI').val()+"?";
	// 	if (confirm(text) == true) 
	// 	{
	// 		window.location ="{{url('/stockb/delete/'.$header->NO_ID .'/?flagz='.$flagz.'' )}}";
	// 		//return true;
	// 	} 
	// 	return false;
	// }

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
	            	loc = "{{ url('/stockb/delete/'.$header->NO_ID) }}" + '?flagz=' + encodeURIComponent(flagz) ;

		            // alert(loc);
	            	window.location = loc;
		
				});
			}
		});
	}
	
	function closeTrans() {
		console.log("masuk");
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
	        	loc = "{{ url('/stockb/') }}" + '?flagz=' + encodeURIComponent(flagz) ;
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
		var loc = "{{ url('/stockb/edit/') }}" + '?idx={{ $header->NO_ID}}&tipx=search&flagz=' + encodeURIComponent(flagz) + '&buktix=' +encodeURIComponent(cari);
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
				    <input name='NO_TRM[]' data-rowid=${idrow} onblur='browseBarang(${idrow})' id='NO_TRM${idrow}' type='text' class='form-control  NO_TRM' >
                </td>
                <td>
				    <input name='CNT[]'   id='CNT${idrow}' type='text' class='form-control  CNT' required>
                </td>

                <td>
				    <input name='ST_PJK[]'   id='ST_PJK${idrow}' type='text' class='form-control  ST_PJK' required>
                </td>

				<td>
					<input name="TGL_TRM[]" ocnlick='select()' id="TGL_TRM${idrow}" type="text" class="date form-control text_input TGL_TRM" data-date-format="dd-mm-yyyy" value="<?php if (isset($_POST["tampilkan"])) {
																																										} else echo 											date('00-00-0000'); ?>" onclick="select()">
				</td>

				<td>
					<input name='NO_SP[]'   id='NO_SP${idrow}' type='text' class='form-control  NO_SP' required>
				</td>

				<td>
		            <input name='TOTAL[]' onclick='select()' onblur='hitung()' value='0' id='TOTAL${idrow}' type='text' style='text-align: right' class='form-control TOTAL text-primary' required >
                </td>

				<td>
		            <input name='PPN[]' onclick='select()' onblur='hitung()' value='0' id='PPN${idrow}' type='text' style='text-align: right' class='form-control PPN text-primary' required >
                </td>

				<td>
		            <input name='TOTALX[]' onclick='select()' onblur='hitung()' value='0' id='TOTALX${idrow}' type='text' style='text-align: right' class='form-control TOTALX text-primary'  >
                </td>		
					
				<td>
					<input name='ACNO[]'   id='ACNO${idrow}' type='text' class='form-control  ACNO' required>
				</td>		
				
				<td>
					<input name='NACNO[]'   id='NACNO${idrow}' type='text' class='form-control  NACNO' required>
				</td>		
					
                <td>
				    <input name='KET[]'   id='KET${idrow}' type='text' class='form-control  KET' required>
                </td>
				
                <td>
					<button type='button' id='DELETEX${idrow}'  class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button>
                </td>				
         </tr>`;
				
        x.innerHTML = html;
        var html='';
		
		
		
		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#TOTAL" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});


			$("#PPN" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});

			$("#TOTALX" + i.toString()).autoNumeric('init', {
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
<!-- 
<script src="autonumeric.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
<script src="https://unpkg.com/autonumeric"></script> -->
@endsection