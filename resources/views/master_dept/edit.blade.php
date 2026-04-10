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

                    <form action="{{($tipx=='new')? url('/dept/store/') : url('/dept/update/'.$header->NO_ID ) }}" method="POST" name ="entri" id="entri" >

                        @csrf

						<ul class="nav nav-tabs">
                            <li class="nav-item active">
                                <a class="nav-link active" href="#counterInfo" data-toggle="tab">Main</a>
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

							<div id="counterInfo" class="tab-pane active">

								<div class="form-group row">

										<input type="text" class="form-control NO_ID" id="NO_ID" name="NO_ID"
										placeholder="Masukkan NO_ID" value="{{$header->NO_ID ?? ''}}" hidden readonly>

										<input name="tipx" class="form-control flagz" id="tipx" value="{{$tipx}}" hidden>


									<!-- code text box baru -->
									<div class="col-md-2 form-group row special-input-label">

										<input type="text" class="kd_dept" id="kd_dept" name="kd_dept"
											value="{{$header->kd_dept}}" placeholder=" " >
										<label for="kd_dept">Kode</label>
									</div>
								</div>

								<div class="form-group row">

									<div class="col-md-4 form-group row special-input-label">
										<input type="text" class="nama" id="nama" name="nama"
											value="{{$header->nama}}" placeholder=" " >
										<label for="nama">Nama</label>
									</div>
								</div>



							</div>






							</div>

						</div>

						<div class="mt-3 col-md-12 form-group row">
							<div class="col-md-4">
								<button hidden type="button" id='TOPX'  onclick="location.href='{{url('/dept/edit/?idx=' .$idx. '&tipx=top')}}'" class="btn btn-outline-primary">Top</button>
								<button hidden type="button" id='PREVX' onclick="location.href='{{url('/dept/edit/?idx='.$header->NO_ID.'&tipx=prev&kodex='.$header->CNT )}}'" class="btn btn-outline-primary">Prev</button>
								<button hidden type="button" id='NEXTX' onclick="location.href='{{url('/dept/edit/?idx='.$header->NO_ID.'&tipx=next&kodex='.$header->CNT )}}'" class="btn btn-outline-primary">Next</button>
								<button hidden type="button" id='BOTTOMX' onclick="location.href='{{url('/dept/edit/?idx=' .$idx. '&tipx=bottom')}}'" class="btn btn-outline-primary">Bottom</button>
							</div>
							<div class="col-md-5">
								<button hidden type="button" id='NEWX' onclick="location.href='{{url('/dept/edit/?idx=0&tipx=new')}}'" class="btn btn-warning">New</button>
								<button hidden type="button" id='EDITX' onclick='hidup()' class="btn btn-secondary">Edit</button>
								<button hidden type="button" id='UNDOX' onclick="location.href='{{url('/dept/edit/?idx=' .$idx. '&tipx=undo' )}}'" class="btn btn-info">Undo</button>
								<button type="button" id='SAVEX' onclick='simpan()' class="btn btn-success" class="fa fa-save"></i>Save</button>

							</div>
							<div class="col-md-3">
								<button hidden type="button" id='HAPUSX' hidden onclick="hapusTrans()" class="btn btn-outline-danger">Hapus</button>

								<!-- <button type="button" id='CLOSEX'  onclick="location.href='{{url('/dept' )}}'" class="btn btn-outline-secondary">Close</button> -->

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
	            	loc = "{{ url('/dept/delete/'.$header->NO_ID) }}"  ;

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
	        	loc = "{{ url('/dept/') }}" ;
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
		var loc = "{{ url('/dept/edit/') }}" + '?idx={{ $header->NO_ID}}&tipx=search&kodex=' +encodeURIComponent(cari);
		window.location = loc;

	}



    var hasilCek;
	function cekSup(kodes) {
		$.ajax({
			type: "GET",
			url: "{{url('dept/cekcounter')}}",
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

