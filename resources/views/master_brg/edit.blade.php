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

                    <form action="{{($tipx=='new')? url('/brg/store/') : url('/brg/update/'.$header->NO_ID ) }}" method="POST" name ="entri" id="entri" >
  
                        @csrf

						<ul class="nav nav-tabs">
                            <li class="nav-item active">
                                <a class="nav-link active" href="#brgInfo" data-toggle="tab">Main</a>
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

							<div id="brgInfo" class="tab-pane active">	
							
								<div class="form-group row">

										<input type="text" class="form-control NO_ID" id="NO_ID" name="NO_ID"
										placeholder="Masukkan NO_ID" value="{{$header->NO_ID ?? ''}}" hidden readonly>

										<input name="tipx" class="form-control flagz" id="tipx" value="{{$tipx}}" hidden>
								

									<!-- code text box baru -->
									<div class="col-md-2 form-group row special-input-label">

										<input type="text" class="CNT" id="CNT" name="CNT" 
											value="{{$header->CNT}}" placeholder=" " >
										<label for="CNT">Counter</label>
									</div>

									<div class="col-md-1">
									</div>

									<div class="col-md-4 form-group row special-input-label">
										<input type="text" class="NCNT" id="NCNT" name="NCNT" 
											value="{{$header->NCNT}}" placeholder=" " >
										<!-- <label for="NCNT">Nama</label> -->
									</div>
								</div>

								<div class="form-group row">	

									<div class="col-md-4 form-group row special-input-label">
										<input type="text" class="KD_BRG" id="KD_BRG" name="KD_BRG" 
											value="{{$header->KD_BRG}}" placeholder=" " >
										<label for="KD_BRG">Barang</label>
									</div>

									<div class="col-md-1">
									</div>

									<div class="col-md-4 form-group row special-input-label">
										<input type="text" class="NA_BRG" id="NA_BRG" name="NA_BRG" 
											value="{{$header->NA_BRG}}" placeholder=" " >
									</div>
								</div>

								<div class="form-group row">
									
									<div class="col-md-2 form-group row special-input-label">

										<input type="text" class="BARCODE" id="BARCODE" name="BARCODE" 
											value="{{$header->BARCODE}}" placeholder=" " >
										<label for="BARCODE">Barcode</label>
									</div>

									<div class="col-md-1">
									</div>
									
									<!-- code text box baru -->
									<div class="col-md-2" >
										<label for="JNS" class="form-label">Jenis</label>
									</div>
									<div class="col-md-2">
										<select id="JNS" class="form-control"  name="JNS">
											<option value="A" {{ ($header->JNS == 'A') ? 'selected' : '' }}>A</option>
											<option value="N" {{ ($header->JNS == 'N') ? 'selected' : '' }}>N</option>
										</select>
									</div>	
									<!-- tutupannya -->

								</div>

								<div class="form-group row">
									
									<div class="col-md-2 form-group row special-input-label">

										<input type="text" class="SUP" id="SUP" name="SUP" 
											value="{{$header->SUP}}" placeholder=" " >
										<label for="SUP">Suplier</label>
									</div>
								</div>

								<div class="form-group row">
								</div>
								<div class="form-group row">
								</div>
								<div class="form-group row">
								</div>
			
								<div class="form-group row">
									
									<div class="col-md-2 form-group row special-input-label">

										<input type="text" class="DIS_STS" id="DIS_STS" name="DIS_STS" 
											value="{{$header->DIS_STS}}" placeholder=" " >
										<label for="DIS_STS">Status Diskon</label>
									</div>

									<div class="col-md-1">
									</div>

									<div class="col-md-3">
										<input type="checkbox" class="form-check-input" id="DIS_KHS" name="DIS_KHS" value="1" {{ ($header->DIS_KHS == 1) ? 'checked' : '' }}>
										<label for="DIS_KHS">Diskon Khusus Kosmetik</label>
									</div>
								</div>

								<div class="form-group row">
									
									<div class="col-md-2 form-group row special-input-label">

										<input type="text" class="HJUAL" id="HJUAL" name="HJUAL" 
											value="{{$header->HJUAL}}" placeholder=" " >
										<label for="HJUAL">Harga : Jual</label>
									</div>

									<div class="col-md-1">
									</div>

									<div class="col-md-2 form-group row special-input-label">

										<input type="text" class="BELI" id="BELI" name="BELI" 
											value="{{$header->BELI}}" placeholder=" " >
										<label for="BELI">Beli</label>
									</div>

									<div class="col-md-1">
									</div>

									<div class="col-md-2 form-group row special-input-label">

										<input type="text" class="HBNET" id="HBNET" name="HBNET" 
											value="{{$header->HBNET}}" placeholder=" " >
										<label for="HBNET">H.B Nett</label>
									</div>
								</div>
			
								<div class="form-group row">

									<!-- code text box baru -->
									<div class="col-md-1" >
										<label class="form-label">Diskon :</label>
									</div>
									<!-- tutupannya -->
									<div class="col-md-1 form-group row special-input-label">

										<input type="text" class="DIS1" id="DIS1" name="DIS1" 
											value="{{$header->DIS1}}" placeholder=" " >
										<label for="DIS1">D1</label>
									</div>
									
									<div class="col-md-1 form-group row special-input-label">

										<input type="text" class="DIS2" id="DIS2" name="DIS2" 
											value="{{$header->DIS2}}" placeholder=" " >
										<label for="DIS2">D2</label>
									</div>
									
									<div class="col-md-1 form-group row special-input-label">

										<input type="text" class="DIS3" id="DIS3" name="DIS3" 
											value="{{$header->DIS3}}" placeholder=" " >
										<label for="DIS3">D3</label>
									</div>
									
									<div class="col-md-1 form-group row special-input-label">

										<input type="text" class="DIS4" id="DIS4" name="DIS4" 
											value="{{$header->DIS4}}" placeholder=" " >
										<label for="DIS4">D4</label>
									</div>

									
									<div class="col-md-1">
									</div>

									<!-- code text box baru -->
									<div class="col-md-1" >
										<label for="ST_HRG" class="form-label">Status Harga</label>
									</div>
									<div class="col-md-1">
										<select id="ST_HRG" class="form-control"  name="ST_HRG">
											<option value="A" {{ ($header->ST_HRG == 'A') ? 'selected' : '' }}>A</option>
											<option value="N" {{ ($header->ST_HRG == 'N') ? 'selected' : '' }}>N</option>
										</select>
									</div>	
									<!-- tutupannya -->	
									
									<div class="col-md-2 form-group row special-input-label">

										<input type="text" class="DIS_PRO" id="DIS_PRO" name="DIS_PRO" 
											value="{{$header->DIS_PRO}}" placeholder=" " >
										<label for="DIS_PRO">Potongan Promosi</label>
									</div>

								</div>

								<div class="form-group row">

									<div class="col-md-1 form-group row special-input-label">

										<input type="text" class="LS_CNT" id="LS_CNT" name="LS_CNT" 
											value="{{$header->LS_CNT}}" placeholder=" " >
										<label for="LS_CNT">Luas Counter</label>
									</div>

									<div class="col-md-1">
									</div>

									<div class="col-md-2 form-group row special-input-label">

										<input type="text" class="DTH" id="DTH" name="DTH" 
											value="{{$header->DTH}}" placeholder=" " >
										<label for="DTH">Diskon Turun Harga</label>
									</div>
									
									<div class="col-md-1">
										<select id="TTH" class="form-control"  name="TTH">
											<option value="A" {{ ($header->TTH == 'A') ? 'selected' : '' }}>A</option>
											<option value="N" {{ ($header->TTH == 'N') ? 'selected' : '' }}>N</option>
										</select>
									</div>	
								</div>

								<div class="form-group row">
									
									<!-- code text box baru -->
									<div class="col-md-2" >
										<label for="JN_CNT" class="form-label">Jenis Counter</label>
									</div>
									<div class="col-md-2">
										<select id="JN_CNT" class="form-control"  name="JN_CNT">
											<option value="A" {{ ($header->JN_CNT == 'A') ? 'selected' : '' }}>A</option>
											<option value="N" {{ ($header->JN_CNT == 'N') ? 'selected' : '' }}>N</option>
										</select>
									</div>	
									<!-- tutupannya -->

									<div class="col-md-1">
									</div>

									<!-- code text box baru -->
									<div class="col-md-2 form-group row special-input-label">

										<input type="text" class="DIST_CUST" id="DIST_CUST" name="DIST_CUST" 
											value="{{$header->DIST_CUST}}" placeholder=" " >
										<label for="DIST_CUST">Diskon Customer</label>
									</div>
									<!-- tutupannya -->
									
									<!-- code text box baru -->
									<div class="col-md-1 form-group row special-input-label">

										<input type="text" class="DIS_CUSN" id="DIS_CUSN" name="DIS_CUSN" 
											value="{{$header->DIS_CUSN}}" placeholder=" " >
									</div>
									<!-- tutupannya -->
								</div>


								<div class="form-group row">
									
									
									<!-- code text box baru -->
									<div class="col-md-2" >
										<label for="ST_CNT" class="form-label">Status Counter</label>
									</div>
									<div class="col-md-2">
										<select id="ST_CNT" class="form-control"  name="ST_CNT">
											<option value="A" {{ ($header->ST_CNT == 'A') ? 'selected' : '' }}>A</option>
											<option value="N" {{ ($header->ST_CNT == 'N') ? 'selected' : '' }}>N</option>
										</select>
									</div>	
									<!-- tutupannya -->
									
									<div class="col-md-1">
									</div>

									<!-- code text box baru -->
									<div class="col-md-2 form-group row special-input-label">
										<input class="form-control date" id="DIS_TGLM" name="DIS_TGLM" 
											data-date-format="dd-mm-yyyy" type="text" autocomplete="off" 
											value="{{ $header->DIS_TGLM ? date('d-m-Y', strtotime($header->DIS_TGLM)) : date('d-m-Y') }}">
										<label for="DIS_TGLM">Tanggal Mulai</label>
									</div>
									<!-- tutupannya -->

									<!-- code text box baru -->
									<div class="col-md-1" align="center">
										<label>s/d</label>
									</div>
									<!-- tutupannya -->
									
									<!-- code text box baru -->
									<div class="col-md-2 form-group row special-input-label">
										<input class="form-control date" id="DIS_TGLS" name="DIS_TGLS" 
											data-date-format="dd-mm-yyyy" type="text" autocomplete="off" 
											value="{{ $header->DIS_TGLS ? date('d-m-Y', strtotime($header->DIS_TGLS)) : date('d-m-Y') }}">
									</div>
									<!-- tutupannya -->
								</div>

								<div class="form-group row">
									
									
									<!-- code text box baru -->
									<div class="col-md-2" >
										<label for="SC_CNT" class="form-label">SPG</label>
									</div>
									<div class="col-md-1">
										<select id="SC_CNT" class="form-control"  name="SC_CNT">
											<option value="A" {{ ($header->SC_CNT == 'A') ? 'selected' : '' }}>A</option>
											<option value="N" {{ ($header->SC_CNT == 'N') ? 'selected' : '' }}>N</option>
										</select>
									</div>	
									<!-- tutupannya -->
								</div>

								<div class="form-group row">
									
									
									<!-- code text box baru -->
									<div class="col-md-2" >
										<label for="ST_NOTA" class="form-label">Status Nota</label>
									</div>
									<div class="col-md-2">
										<select id="ST_NOTA" class="form-control"  name="ST_NOTA">
											<option value="A" {{ ($header->ST_NOTA == 'A') ? 'selected' : '' }}>A</option>
											<option value="N" {{ ($header->ST_NOTA == 'N') ? 'selected' : '' }}>N</option>
										</select>
									</div>	
									<!-- tutupannya -->

									<div class="col-md-1">
									</div>

									<!-- code text box baru -->
									<div class="col-md-1 form-group row special-input-label">
										<input type="text" class="MARGIN" id="MARGIN" name="MARGIN" 
											value="{{$header->MARGIN}}" placeholder=" " >
										<label for="MARGIN">Margin</label>
									</div>
									<!-- tutupannya -->
								</div>

								<div class="form-group row">

									<!-- code text box baru -->
									<div class="col-md-2" >
										<label for="ST_ORD" class="form-label">Status Order</label>
									</div>
									<div class="col-md-2">
										<select id="ST_ORD" class="form-control"  name="ST_ORD">
											<option value="A" {{ ($header->ST_ORD == 'A') ? 'selected' : '' }}>A</option>
											<option value="N" {{ ($header->ST_ORD == 'N') ? 'selected' : '' }}>N</option>
										</select>
									</div>	
									<!-- tutupannya -->

								</div>

								<div class="form-group row">

									<!-- code text box baru -->
									<div class="col-md-2" >
										<label for="ST_PJK" class="form-label">Status Pajak</label>
									</div>
									<div class="col-md-1">
										<select id="ST_PJK" class="form-control"  name="ST_PJK">
											<option value="A" {{ ($header->ST_PJK == 'A') ? 'selected' : '' }}>A</option>
											<option value="N" {{ ($header->ST_PJK == 'N') ? 'selected' : '' }}>N</option>
										</select>
									</div>	
									<!-- tutupannya -->

								</div>

								

								<div class="form-group row">

									<!-- code text box baru -->
									<div class="col-md-2" >
										<label for="CBAYAR" class="form-label">Cara Bayar</label>
									</div>
									<div class="col-md-2">
										<select id="CBAYAR" class="form-control"  name="CBAYAR">
											<option value="A" {{ ($header->CBAYAR == 'A') ? 'selected' : '' }}>A</option>
											<option value="N" {{ ($header->CBAYAR == 'N') ? 'selected' : '' }}>N</option>
										</select>
									</div>	
									<!-- tutupannya -->

									<div class="col-md-1">
									</div>
									
									<!-- code text box baru -->
									<div class="col-md-1 form-group row special-input-label">
										<input type="text" class="KEL_PT" id="KEL_PT" name="KEL_PT" 
											value="{{$header->KEL_PT}}" placeholder=" " >
										<label for="KEL_PT">Kelompok PT</label>
									</div>
									<!-- tutupannya -->

								</div>

								<div class="form-group row">

									<!-- code text box baru -->
									<div class="col-md-1 form-group row special-input-label">
										<input type="text" class="LBAYAR" id="LBAYAR" name="LBAYAR" 
											value="{{$header->LBAYAR}}" placeholder=" " >
										<label for="LBAYAR">Lama Bayar</label>
									</div>
									<!-- tutupannya -->

									<div class="col-md-1">
									</div>
									
									<!-- code text box baru -->
									<div class="col-md-2 form-group row special-input-label">
										<input type="text" class="KEL_BRG" id="KEL_BRG" name="KEL_BRG" 
											value="{{$header->KEL_BRG}}" placeholder=" " >
										<label for="KEL_BRG">Kelompok BRG</label>
									</div>
									<!-- tutupannya -->
								</div>


								<div class="form-group row">

									<!-- code text box baru -->
									<div class="col-md-2" >
										<label for="KW_RET" class="form-label">Kwitansi Retur</label>
									</div>
									<div class="col-md-2">
										<select id="KW_RET" class="form-control"  name="KW_RET">
											<option value="A" {{ ($header->KW_RET == 'A') ? 'selected' : '' }}>A</option>
											<option value="N" {{ ($header->KW_RET == 'N') ? 'selected' : '' }}>N</option>
										</select>
									</div>	
									<!-- tutupannya -->

									<div class="col-md-1">
									</div>
									
									<!-- code text box baru -->
									<div class="col-md-2" >
										<label for="BASIC" class="form-label">Basic</label>
									</div>
									<div class="col-md-1">
										<select id="BASIC" class="form-control"  name="BASIC">
											<option value="A" {{ ($header->BASIC == 'A') ? 'selected' : '' }}>A</option>
											<option value="N" {{ ($header->BASIC == 'N') ? 'selected' : '' }}>N</option>
										</select>
									</div>	
									<!-- tutupannya -->

								</div>

								<div class="form-group row">

									<!-- code text box baru -->
									<div class="col-md-2" >
										<label for="KW_LBL" class="form-label">Kwitansi Label</label>
									</div>
									<div class="col-md-2">
										<select id="KW_LBL" class="form-control"  name="KW_LBL">
											<option value="A" {{ ($header->KW_LBL == 'A') ? 'selected' : '' }}>A</option>
											<option value="N" {{ ($header->KW_LBL == 'N') ? 'selected' : '' }}>N</option>
										</select>
									</div>	
									<!-- tutupannya -->

									<div class="col-md-1">
									</div>
									
									<!-- code text box baru -->
									<div class="col-md-2" >
										<label for="KATEGORI" class="form-label">Kategori</label>
									</div>
									<div class="col-md-1">
										<select id="KATEGORI" class="form-control"  name="KATEGORI">
											<option value="A" {{ ($header->KATEGORI == 'A') ? 'selected' : '' }}>A</option>
											<option value="N" {{ ($header->KATEGORI == 'N') ? 'selected' : '' }}>N</option>
										</select>
									</div>	
									<!-- tutupannya -->

								</div>

								

							</div>

							
							

							

							</div>

						</div>
        
						<div class="mt-3 col-md-12 form-group row">
							<div class="col-md-4">
								<button hidden type="button" id='TOPX'  onclick="location.href='{{url('/brg/edit/?idx=' .$idx. '&tipx=top')}}'" class="btn btn-outline-primary">Top</button>
								<button hidden type="button" id='PREVX' onclick="location.href='{{url('/brg/edit/?idx='.$header->NO_ID.'&tipx=prev&kodex='.$header->CNT )}}'" class="btn btn-outline-primary">Prev</button>
								<button hidden type="button" id='NEXTX' onclick="location.href='{{url('/brg/edit/?idx='.$header->NO_ID.'&tipx=next&kodex='.$header->CNT )}}'" class="btn btn-outline-primary">Next</button>
								<button hidden type="button" id='BOTTOMX' onclick="location.href='{{url('/brg/edit/?idx=' .$idx. '&tipx=bottom')}}'" class="btn btn-outline-primary">Bottom</button>
							</div>
							<div class="col-md-5">
								<button hidden type="button" id='NEWX' onclick="location.href='{{url('/brg/edit/?idx=0&tipx=new')}}'" class="btn btn-warning">New</button>
								<button hidden type="button" id='EDITX' onclick='hidup()' class="btn btn-secondary">Edit</button>                    
								<button hidden type="button" id='UNDOX' onclick="location.href='{{url('/brg/edit/?idx=' .$idx. '&tipx=undo' )}}'" class="btn btn-info">Undo</button> 
								<button type="button" id='SAVEX' onclick='simpan()' class="btn btn-success" class="fa fa-save"></i>Save</button>

							</div>
							<div class="col-md-3">
								<button hidden type="button" id='HAPUSX' hidden onclick="hapusTrans()" class="btn btn-outline-danger">Hapus</button>
								
								<!-- <button type="button" id='CLOSEX'  onclick="location.href='{{url('/brg' )}}'" class="btn btn-outline-secondary">Close</button> -->

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
		  	
			$("#CNT").attr("readonly", false);	

		   }
		else
		{
	     	$("#CNT").attr("readonly", true);	

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
		
		$("#CNT").attr("readonly", true);			
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
				
		 $('#CNT').val("");	
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
	            	loc = "{{ url('/brg/delete/'.$header->NO_ID) }}"  ;

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
	        	loc = "{{ url('/brg/') }}" ;
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
		var loc = "{{ url('/brg/edit/') }}" + '?idx={{ $header->NO_ID}}&tipx=search&kodex=' +encodeURIComponent(cari);
		window.location = loc;
		
	}

     
     
    var hasilCek;
	function cekSup(kodes) {
		$.ajax({
			type: "GET",
			url: "{{url('brg/cekbrg')}}",
            async: false,
			data: ({ CNT: kodes, }),
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
			cekSup($('#CNT').val());		
		}
		

        (hasilCek==0) ? document.getElementById("entri").submit() : alert('Suplier '+$('#CNT').val()+' sudah ada!');
	}
</script>
@endsection

