@extends('layouts.plain')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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



	/* perubahan tab warna di form edit  */
	.nav-item .nav-link.active {
		background-color: red !important; /* Use !important to ensure it overrides */
		color: white !important;
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


    /* style tambahan baru */
    .form-control:disabled,
    .form-control[readonly] {
        background-color: #f7d8b4 !important;
        opacity: 1;
    }

    .row {
        margin-bottom: 8px !important;
    }

    /* menghilangkan padding */
    .content-header {
        padding: 0 !important;
    }

	.uppercase {
		text-transform: uppercase;
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

                    <form action="{{($tipx=='new')? url('/bintang/store') : url('/bintang/update/'.$header->NO_ID.'' ) }}" method="POST" name ="entri" id="entri" >

                        @csrf


						<!-- <ul class="nav nav-tabs">
                            <li class="nav-item active">
                                <a class="nav-link active" href="#main" data-toggle="tab">PO</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#dropship" data-toggle="tab" >Dropship</a>
                            </li>
                        </ul> -->


                        <div class="tab-content mt-3">


							<!-- <div id="main" class="tab-pane active"> -->

									<div class="form-group row">
										<div class="col-md-1" align="left">
											<label for="NO_BUKTI" class="form-label">No Bukti</label>
										</div>


											<input type="text" class="form-control NO_ID" id="NO_ID" name="NO_ID"
												placeholder="Masukkan NO_ID" value="{{$header->NO_ID ?? ''}}" hidden readonly>

											<input name="tipx" class="form-control tipx" id="tipx" value="{{$tipx}}" hidden>

										<div class="col-md-2">
											<input type="text" class="form-control NO_BUKTI" id="NO_BUKTI" name="NO_BUKTI"
											placeholder="Masukkan Bukti#" value="{{$header->NO_BUKTI}}" readonly>
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
											<label for="NO_SUPL" class="form-label">Kode Suplier</label>
										</div>
										<div class="col-md-2 input-group" >
											<input type="text" class="form-control NO_SUPL" id="NO_SUPL" name="NO_SUPL" placeholder="Pilih Suplier" value="{{$header->NO_SUPL}}" style="text-align: left" readonly >
											<button type="button" class="btn btn-primary" onclick="browseSuplier()"><i class="fa fa-search"></i></button>
										</div>
										
										<div class="col-md-2">
											<input type="text" class="form-control NAMA" id="NAMA" name="NAMA" placeholder="Nama Suplier"  value="{{$header->NAMA}}"  readonly >
										</div>

										<div class="col-md-2">
											<input type="text" style="text-align: right" class="form-control BUDGET_AWL" id="BUDGET_AWL" name="BUDGET_AWL" placeholder="Budget Awal"  value="{{$header->BUDGET_AWL}}"  readonly >
										</div>

									</div>



									<!-- loader tampil di modal  -->
									<div class="loader" style="z-index: 1055;" id='LOADX' ></div>
									<!-- batas load -->

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
										<!-- code text box baru -->
										<div class="col-md-3 form-group row special-input-label">

											<input type="text" class="NOTES" id="NOTES" name="NOTES"
												value="{{$header->NOTES}}" placeholder=" " >
											<label for="NOTES">Keterangan</label>
										</div>
										<!-- tutupannya -->

									</div>

									<div style="overflow-y:scroll; height:200px;" class="col-md-12 scrollable" align="right">
									<!-- <div style="overflow-y:scroll; " class="col-md-12 scrollable" align="right"> -->

									<!-- <table id="datatable" class="table table-striped table-border table-scrollable">                 -->
									<table id="datatable" class="table table-striped table-border">

										<thead>
											<tr>
												<th width="50px" style="text-align:center">No.</th>
												<th width="200px" style="text-align:center">Kode Barang</th>
												<th width="400px" style="text-align:center">Nama Barang</th>
												<th width="200px" style="text-align:center">Kode Suplier</th>
												<th width="400px" style="text-align:center">Nama Suplier</th>
												<th width="75px" style="text-align:center">CEK</th>

												<th></th>

											</tr>
										</thead>

										<tbody id="detailPod">
										<?php $no=0 ?>
										@foreach ($detail as $detail)
											<tr>
												<td>
													<input type="hidden" name="NO_ID[]{{$no}}" id="NO_ID" type="text" value="{{$detail->NO_ID}}"
													class="form-control NO_ID" onkeypress="return tabE(this,event)" style="text-align:center" readonly>

													<input name="REC[]" id="REC{{$no}}" type="text" value="{{$detail->REC}}" class="form-control REC" onkeypress="return tabE(this,event)" readonly style="text-align:center">
												</td>


												<td>
													<input name="KDBAR[]" id="KDBAR{{$no}}" type="text" class="form-control KDBAR "
													value="{{$detail->KDBAR}}" onblur="browseBarang({{$no}})">
												</td>

												<td>
													<input name="NMBAR[]" id="NMBAR{{$no}}" type="text" class="form-control NMBAR " value="{{$detail->NMBAR}}">
												</td>

												<td>
													<input name="NO_SUPLD[]" id="NO_SUPLD{{$no}}" type="text" class="form-control NO_SUPLD " value="{{$detail->NO_SUPL}}">
												</td>

												<td>
													<input name="NAMAD[]" id="NAMAD{{$no}}" type="text" class="form-control NAMAD " value="{{$detail->NAMA}}">
												</td>

												<td>
													<input type="checkbox" name="CEK[{{$no}}]" value="1" {{ $detail->CEK ? 'checked' : '' }} class="form-control CEK">
													{{-- <input type="checkbox" name="CEK[{{$no}}]" value="1" {{ $detail->CEK ? 'checked disabled' : '' }} class="form-control CEK"> --}}
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
											{{-- <td><input class="form-control TTOTAL_QTY  text-primary" style="text-align: right"  id="TTOTAL_QTY" name="TTOTAL_QTY" value="{{$header->Q_SALDO}}" readonly></td> --}}
											<td></td>
											{{-- <td><input class="form-control TTOTAL  text-primary" style="text-align: right"  id="TTOTAL" name="TTOTAL" value="{{$header->R_SALDO}}" readonly></td> --}}
											<td></td>
											<td></td>
											<td></td>
										</tfoot>
									</table>
									<!-- scroll -->

									</div>

									<!-- batas -->


								<div class="tab-content mt-6">

									<div class="form-group row">

										<div class="col-md-1" align="center">
											<a type="button" id='PLUSX' onclick="tambah()" class="fas fa-plus fa-sm md-3" style="font-size: 20px" ></a>
										</div>

									</div>

								</div>


							<!-- </div>  -->





							<div class="mt-3 col-md-12 form-group row">
								<div class="col-md-4">
									<button hidden type="button" id='TOPX'  onclick="location.href='{{url('/bintang/edit/?idx=' .$idx. '&tipx=top' )}}'" class="btn btn-outline-primary">Top</button>
									<button hidden type="button" id='PREVX' onclick="location.href='{{url('/bintang/edit/?idx='.$header->NO_ID.'&tipx=prev&buktix='.$header->NO_SP )}}'" class="btn btn-outline-primary">Prev</button>
									<button hidden type="button" id='NEXTX' onclick="location.href='{{url('/bintang/edit/?idx='.$header->NO_ID.'&tipx=next&buktix='.$header->NO_SP )}}'" class="btn btn-outline-primary">Next</button>
									<button hidden type="button" id='BOTTOMX' onclick="location.href='{{url('/bintang/edit/?idx=' .$idx. '&tipx=bottom' )}}'" class="btn btn-outline-primary">Bottom</button>
								</div>
								<div class="col-md-5">
									<button hidden type="button" id='NEWX' onclick="location.href='{{url('/bintang/edit/?idx=0&tipx=new' )}}'" class="btn btn-warning">New</button>
									<button hidden type="button" id='EDITX' onclick='hidup()' class="btn btn-secondary">Edit</button>
									<button hidden type="button" id='UNDOX' onclick="location.href='{{url('/bintang/edit/?idx=' .$idx. '&tipx=undo' )}}'" class="btn btn-info">Undo</button>
									<button type="button" id='SAVEX' onclick='simpan()'   class="btn btn-success" class="fa fa-save"></i>Save</button>

								</div>
								<div class="col-md-3">
									<button hidden type="button" id='HAPUSX'  onclick="hapusTrans()" class="btn btn-outline-danger">Hapus</button>
									<!-- tombol close sweet alert -->
										<button type="button" id='CLOSEX' onclick="closeTrans()" class="btn btn-outline-secondary">Close</button></div>
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


 	<div class="modal fade" id="browseSuplierModal" tabindex="-1" role="dialog" aria-labelledby="browseSuplierModalLabel" aria-hidden="true">
	  <div class="modal-dialog mw-100 w-75" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseSuplierModalLabel">Cari Suplier</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bsuplier">
				<thead>
					<tr>
						<th>Suplier</th>
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


	<div class="modal fade" id="browseBarangdzModal" tabindex="-1" role="dialog" aria-labelledby="browseBarangdzModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseBarangdzModalLabel">Cari Satuan</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bbarangdz">
				<thead>
					<tr>
						<th>Item</th>
						<th>Nama</th>
						<th>Satuan</th>
						<th>Kali</th>
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

	<div class="modal fade" id="browseGdgModal" tabindex="-1" role="dialog" aria-labelledby="browseGdgModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseMklModalLabel">Cari Gudang</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bgdg">
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


	<div class="modal fade" id="browseSoModal" tabindex="-1" role="dialog" aria-labelledby="browseSoModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseSoModalLabel">Cari SO# Dropship</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bso">
				<thead>
					<tr>
						<th>SO#</th>
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

	<div class="modal fade" id="browseBarangdzModal" tabindex="-1" role="dialog" aria-labelledby="browseBarangdzModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseBarangdzModalLabel">Cari Satuan</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bbarangdz">
				<thead>
					<tr>
						<th>Item</th>
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

@section('footer-scripts')


<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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

		$("#BUDGET_AWL").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});


        $('body').on('click', '.btn-delete', function() {
			var val = $(this).parents("tr").remove();
			baris--;
			hitung();
			nomor();

		});

		$('.date').datepicker({
            dateFormat: 'dd-mm-yy'
		});




//		CHOOSE Supplier
 		var dTableBSuplier;
		loadDataBSuplier = function(){

			$.ajax(
			{
				type: 'GET',
				url: '{{url('bintang/browse_sup')}}',

			    beforeSend: function(){
					$("#LOADX").show();
				},


				success: function( response )
				{
					$("#LOADX").hide();

					resp = response;
					if(dTableBSuplier){
						dTableBSuplier.clear();
					}
					for(i=0; i<resp.length; i++){

						dTableBSuplier.row.add([
							'<a href="javascript:void(0);" onclick="chooseSuplier(\''+resp[i].NO_SUPL+'\',  \''+resp[i].NAMA+'\', \''+resp[i].BUDGET_AWL+'\')">'+resp[i].NO_SUPL+'</a>',
							resp[i].NAMA,
						]);
					}
					dTableBSuplier.draw();
				}
			});
		}

		dTableBSuplier = $("#table-bsuplier").DataTable({

		});

		browseSuplier = function(){
			loadDataBSuplier();
			$("#browseSuplierModal").modal("show");
		}

		chooseSuplier = function(NO_SUPL,NAMA,BUDGET_AWL){
			$("#NO_SUPL").val(NO_SUPL);
			$("#NAMA").val(NAMA);
			$("#BUDGET_AWL").autoNumeric('set', BUDGET_AWL || 0);
			$("#browseSuplierModal").modal("hide");
		}

		//////////////////////////////////////////////////////

		var dTableBBarang;
		var rowidBarang;
		loadDataBBarang = function(){

			$.ajax(
			{
				type: 'GET',
				url: "{{url('bintang/browse_brg')}}",
				async : false,
				data: {
						'NO_SUPL': $("#NO_SUPL").val()
				},
				success: function( response )

				{
					resp = response;


					if ( resp.length > 1 )
					{
							if(dTableBBarang){
								dTableBBarang.clear();
							}
							for(i=0; i<resp.length; i++){

								dTableBBarang.row.add([
									`<a href="javascript:void(0);" 
										onclick='chooseBarang(${JSON.stringify(resp[i].KDBAR)}, 
															${JSON.stringify(resp[i].NMBAR)}, 
															${JSON.stringify(resp[i].NO_SUPL)}, 
															${JSON.stringify(resp[i].NAMA)})'>
										${resp[i].KDBAR}
									</a>`,
									resp[i].NMBAR,
									resp[i].NO_SUPL,
									resp[i].NAMA,
								]);
							}
							dTableBBarang.draw();

					}
					else
					{
						$("#KDBAR"+rowidBarang).val(resp[0].KDBAR);
						$("#NMBAR"+rowidBarang).val(resp[0].NMBAR);
					}
				}
			});
		}

		dTableBBarang = $("#table-bbarang").DataTable({

		});

		browseBarang = function(rid){
			rowidBarang = rid;
			$("#NMBAR"+rowidBarang).val("");
			loadDataBBarang();


			if ( $("#NMBAR"+rowidBarang).val() == '' ) {
					$("#browseBarangModal").modal("show");
			}
		}

		chooseBarang = function(KDBAR,NMBAR,NO_SUPL,NAMA){
			$("#KDBAR"+rowidBarang).val(KDBAR);
			$("#NMBAR"+rowidBarang).val(NMBAR);
			$("#NO_SUPLD"+rowidBarang).val(NO_SUPL);
			$("#NAMAD"+rowidBarang).val(NAMA);
			$("#browseBarangModal").modal("hide");
		}

		
	});



///////////////////////////////////////




	function cekDetail(){
		var cekBarang = '';
		$(".KDBAR").each(function() {

			let z = $(this).closest('tr');
			var KDBARX = z.find('.KDBAR').val();

			if( KDBARX =="" )
			{
					cekBarang = '1';

			}
		});

		return cekBarang;
	}


 	function simpan() {
		// hitung();

		var tgl = $('#TGL').val();
		var bulanPer = {{session()->get('periode')['bulan']}};
		var tahunPer = {{session()->get('periode')['tahun']}};

        var check = '0';

		if ( $('#NO_SUPL').val()=='' )
		{
			check = '1';
			Swal.fire({
				icon: 'warning',
				title: 'Warning',
				text: 'Suplier Harus Dipilih.'
			});
			return; // Stop function execution
		}

		if (cekDetail() == '1')
		{
			check = '1';
			Swal.fire({
				icon: 'warning',
				title: 'Warning',
				text: 'Ada Barang# Kosong Didetail.'
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
			return; // Stop function execution
		}

		if (tgl.substring(3, 5) != bulanPer) {
			check = '1';
			Swal.fire({
				icon: 'warning',
				title: 'Warning',
				text: 'Bulan tidak sama dengan Periode'
			});
			return; // Stop function execution
		}

		if (tgl.substring(tgl.length - 4) != tahunPer) {
			check = '1';
			Swal.fire({
				icon: 'warning',
				title: 'Warning',
				text: 'Tahun tidak sama dengan Periode'
			});
			return; // Stop function execution
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
		$("#JTEMPO").attr("readonly", false);
		$("#KODES").attr("disabled", false);
		$("#PKP").attr("disabled", true);
		$("#ZPKP").attr("disabled", true);

		$("#NOTES").attr("readonly", false);
		$("#NOTES").attr("disabled", false);
		$("#NOTES2").attr("readonly", false);

		$("#NO_SO").attr("disabled", false);
		$("#KODEC").attr("disabled", true);
		$("#NAMAC").attr("disabled", true);

		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#REC" + i.toString()).attr("readonly", true);
			$("#KDBAR" + i.toString()).attr("readonly", false);
			$("#NMBAR" + i.toString()).attr("readonly", true);
			$("#NO_SUPLD" + i.toString()).attr("readonly", true);
			$("#NAMAD" + i.toString()).attr("readonly", true);
			$("#DELETEX" + i.toString()).attr("hidden", false);
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
		$("#JTEMPO").attr("readonly", true);
		$("#KODES").attr("disabled", true);
		$("#NOTES").attr("readonly", true);
		$("#NOTES").attr("disabled", true);
		$("#NOTES2").attr("readonly", true);

		$("#NO_SO").attr("disabled", true);
		$("#KODEC").attr("disabled", true);
		$("#NAMAC").attr("disabled", true);

		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#REC" + i.toString()).attr("readonly", true);
			$("#KD_BHN" + i.toString()).attr("readonly", true);
			$("#KDBAR" + i.toString()).attr("readonly", true);
			$("#NMBAR" + i.toString()).attr("readonly", true);
			$("#NO_SUPLD" + i.toString()).attr("readonly", true);
			$("#NAMAD" + i.toString()).attr("readonly", true);

			$("#DELETEX" + i.toString()).attr("hidden", true);
		}



	}


	function kosong() {

		$('#NO_BUKTI').val("+");
		$('#NOTES').val("");

		var html = '';
		$('#detailx').html(html);

	}


	// sweetalert untuk tombol hapus dan close

	function hapusTrans() {
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
	            	loc = "{{ url('/bintang/delete/'.$header->NO_ID) }}";

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
	        	loc = "{{ url('/bintang/') }}";
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
		var loc = "{{ url('/bintang/edit/') }}" + '?idx={{ $header->NO_ID}}&tipx=search&buktix=' +encodeURIComponent(cari);
		window.location = loc;

	}


    function tambah() {

        var x = document.getElementById('datatable').insertRow(baris + 1);

		html=`<tr>

                <td>
 					<input name='NO_ID[]' id='NO_ID${idrow}' type='hidden' class='form-control NO_ID' value='new' style="text-align:center" readonly>
					<input name='REC[]' id='REC${idrow}' type='text' class='REC form-control' onkeypress='return tabE(this,event)' style="text-align:center" readonly>
	            </td>

				<td >
				    <input name='KDBAR[]' data-rowid=${idrow} onblur='browseBarang(${idrow})' id='KDBAR${idrow}' type='text' class='form-control  KDBAR' >
				</td>

				<td >
				    <input name='NMBAR[]'   id='NMBAR${idrow}' type='text' class='form-control  NMBAR' required readonly>
                </td>

                <td >
				    <input name='NO_SUPLD[]'   id='NO_SUPLD${idrow}' type='text' class='form-control  NO_SUPLD' required readonly>
                </td>

				<td >
				    <input name='NAMAD[]'   id='NAMAD${idrow}' type='text' class='form-control  NAMAD' required readonly>
                </td>

                <td>
				    <input name='CEK[${idrow}]' id='CEK${idrow}' type='checkbox' value='1' class='form-control CEK'>
                </td>

                <td>
					<button type='button' id='DELETEX${idrow}'  class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button>
                </td>
         </tr>`;

        x.innerHTML = html;
        var html='';


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
