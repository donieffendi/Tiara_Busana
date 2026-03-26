@extends('layouts.plain')

<style>
    .card {}

    .form-control:focus {
        background-color: #b5e5f9 !important;
    }

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

                            <form action="{{($tipx=='new')? url('/usulnosup/store/') : url('/usulnosup/update/'.$header->NO_ID ) }}" method="POST" name ="entri" id="entri" >


                                    @csrf
                                    <div class="tab-content mt-3">
                                        <div class="form-group row">
                                            <div class="col-md-1" align="right">
                                                <label for="NO_BUKTI" class="form-label">No. Bukti#</label>
                                            </div>


                                            <input type="text" class="form-control NO_ID" id="NO_ID" name="NO_ID"
                                                placeholder="Masukkan NO_ID" value="{{ $header->NO_ID ?? '' }}" hidden
                                                readonly>

                                            <input name="tipx" class="form-control tipx" id="tipx"
                                                value="{{ $tipx }}" hidden>


                                            <div class="col-md-2">
                                                <input type="text" class="form-control NO_BUKTI" id="NO_BUKTI"
                                                    name="NO_BUKTI" placeholder="Masukkan Bukti#"
                                                    value="{{ $header->NO_BUKTI }}" readonly>
                                            </div>

                                            <div class="col-md-1" align="right">
                                                <label for="TGL" class="form-label">Tgl</label>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-control date" id="TGL" name="TGL"
                                                    data-date-format="dd-mm-yyyy" type="text" autocomplete="off"
                                                    value="{{ date('d-m-Y', strtotime($header->TGL)) }}">
                                            </div>

                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-1" align="right">
                                                <label for="NOTES" class="form-label">Notes</label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control NOTES" id="NOTES"
                                                    name="NOTES" value="{{ $header->NOTES }}"
                                                    placeholder="Masukkan Notes">
                                            </div>

                                        </div>



                                        <div class="tab-content mt-3">
                                            <div class="table-responsive">
                                                <table id="datatable" class="table table-striped table-bordered"
                                                    style="min-width: 1200px;">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th style="width: 50px; white-space: nowrap;">No.</th>
                                                            <th style="width: 120px; white-space: nowrap;">
                                                                <label style="color:red;font-size:20px">* </label>
                                                                <label for="NO_SUPL" class="form-label">No. Suplier</label>
                                                            </th>
                                                            <th style="width: 550px;text-align: center;white-space: nowrap;">Nama Suplier</th>
                                                            <th style="width: 550px; white-space: nowrap;">No. Suplier Baru</th>
                                                            {{-- <th style="width: 75px; white-space: nowrap;">Hapus</th> --}}
                                                            <th></th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <?php $no = 0; ?>
                                                        @foreach ($detail as $detail)
                                                            <tr>
                                                                <td>
                                                                    <input type="hidden"
                                                                        name="NO_ID[]{{ $no }}" id="NO_ID"
                                                                        type="text" value="{{ $detail->NO_ID }}"
                                                                        class="form-control NO_ID"
                                                                        onkeypress="return tabE(this,event)" readonly>

                                                                    <input name="REC[]" id="REC{{ $no }}"
                                                                        type="text" value="{{ $detail->REC }}"
                                                                        class="form-control REC"
                                                                        onkeypress="return tabE(this,event)" readonly
                                                                        style="text-align:center">
                                                                </td>


                                                                <td>
                                                                    <input name="NO_SUPL[]" id="NO_SUPL{{ $no }}"
                                                                        type="text" value="{{ $detail->NO_SUPL }}"
                                                                        class="form-control NO_SUPL" readonly required>
                                                                </td>
                                                                <td>
                                                                    <input name="NAMA[]" id="NAMA{{ $no }}"
                                                                        type="text" value="{{ $detail->NAMA }}"
                                                                        class="form-control NAMA" readonly required>
                                                                </td>
                                                                <td>
                                                                    <input name="NO_BARU[]" id="NO_BARU{{ $no }}"
                                                                        type="text" value="{{ $detail->NO_BARU }}"
                                                                        class="form-control NO_BARU" required>
                                                                </td>


                                                                {{-- <td>
                                                                    <input type="hidden" name="HPS[{{ $no }}]" value="0">
                                                                    <input type="checkbox" 
                                                                        name="HPS[{{ $no }}]" 
                                                                        value="1" 
                                                                        id="HPS{{ $no }}"
                                                                        {{ $detail->HPS == 1 ? 'checked' : '' }}>
                                                                </td> --}}

                                                                <td>
                                                                    <button type='button'
                                                                        id='DELETEX{{ $no }}'
                                                                        class='btn btn-sm btn-circle btn-outline-danger btn-delete'
                                                                        onclick=''> <i class='fa fa-fw fa-trash'></i>
                                                                    </button>
                                                                </td>

                                                            </tr>

                                                            <?php $no++; ?>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-md-2 row">
                                                <a type="button" id='PLUSX' onclick="tambah()"
                                                    class="fas fa-plus fa-sm md-3" style="font-size: 20px"></a>

                                            </div>

                                        </div>
                                    </div>

                                    <hr style="margin-top: 30px; margin-buttom: 30px">
                                    <!-- dari sini shelvi-->

                                    <!-- sampai sini shelvi-->

                                    <div class="mt-3 col-md-12 form-group row">
                                        <div class="col-md-4">
                                            <button hidden type="button" id='TOPX'  onclick="location.href='{{url('/usulnosup/edit/?idx=' .$idx. '&tipx=top')}}'" class="btn btn-outline-primary">Top</button>
                                            <button hidden type="button" id='PREVX' onclick="location.href='{{url('/usulnosup/edit/?idx='.$header->NO_ID.'&tipx=prev&buktix='.$header->NO_BUKTI )}}'" class="btn btn-outline-primary">Prev</button>
                                            <button hidden type="button" id='NEXTX' onclick="location.href='{{url('/usulnosup/edit/?idx='.$header->NO_ID.'&tipx=next&buktix='.$header->NO_BUKTI )}}'" class="btn btn-outline-primary">Next</button>
                                            <button hidden type="button" id='BOTTOMX' onclick="location.href='{{url('/usulnosup/edit/?idx=' .$idx. '&tipx=bottom')}}'" class="btn btn-outline-primary">Bottom</button>
                                        </div>
                                        <div class="col-md-5">
                                            <button hidden type="button" id='NEWX' onclick="location.href='{{url('/usulnosup/edit/?idx=0&tipx=new')}}'" class="btn btn-warning">New</button>
                                            <button hidden type="button" id='EDITX' onclick='hidup()' class="btn btn-secondary">Edit</button>
                                            <button hidden type="button" id='UNDOX' onclick="location.href='{{url('/usulnosup/edit/?idx=' .$idx. '&tipx=undo' )}}'" class="btn btn-info">Undo</button>
                                            <button type="button" id='SAVEX' onclick='simpan()' class="btn btn-success" class="fa fa-save"></i>Save</button>

                                        </div>
                                        <div class="col-md-3">
                                            <button hidden type="button" id='HAPUSX' hidden onclick="hapusTrans()" class="btn btn-outline-danger">Hapus</button>

                                            <!-- <button type="button" id='CLOSEX'  onclick="location.href='{{url('/usulnosup' )}}'" class="btn btn-outline-secondary">Close</button> -->

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
	  <div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseSupModalLabel">Cari Suplier</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bsup">
				<thead>
					<tr>
						<th>No. Suplier</th>
						<th>Nama Suplier</th>
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
        <script src="{{ asset('foxie_js_css/bootstrap.bundle.min.js') }}"></script>

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

            $(document).ready(function() {
                idrow = <?= $no ?>;
                baris = <?= $no ?>;

                $('body').on('keydown', 'input, select', function(e) {
                    if (e.key === "Enter") {
                        var self = $(this),
                            form = self.parents('form:eq(0)'),
                            focusable, next;
                        focusable = form.find('input,select,textarea').filter(':visible');
                        next = focusable.eq(focusable.index(this) + 1);
                        console.log(next);
                        if (next.length) {
                            next.focus().select();
                        } else {
                            tambah();
                            // var nomer = idrow - 1;
                            // console.log("KD_BRG" + nomor);
                            // document.getElementById("KD_BRG" + nomor).focus();
                            // form.submit();
                        }
                        return false;
                    }
                });


                $tipx = $('#tipx').val();
                $searchx = $('#CARI').val();


                if ($tipx == 'new') {
                    baru();
                    tambah();
                }

                if ($tipx != 'new') {
                    ganti();
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

                var dTableBSup;
                var rowidSup;
                loadDataBSup = function(){
                
                    $.ajax(
                    {
                        type: 'GET',    
                        url: "{{url('usulnosup/browse_sup')}}",
                        async : false,

                        success: function( response )

                        {
                            resp = response;
                            
                            
                            if ( resp.length > 1 )
                            {	
                                    if(dTableBSup){
                                        dTableBSup.clear();
                                    }
                                    for(i=0; i<resp.length; i++){
                                        
                                        dTableBSup.row.add([
                                            '<a href="javascript:void(0);" onclick="chooseSup(\''+resp[i].NO_SUPL+'\', \''+resp[i].NAMA+'\',)">'+resp[i].NO_SUPL+'</a>',
                                            resp[i].NAMA,
                                        ]);
                                    }
                                    dTableBSup.draw();
                            
                            }
                            else
                            {
                                $("#NO_SUPL"+rowidSup).val(resp[0].NO_SUPL);
                                $("#NAMA"+rowidSup).val(resp[0].NAMA);
                            }
                        }
                    });
                }
                
                dTableBSup = $("#table-bsup").DataTable({
                    
                });

                browseSup = function(rid){
                    rowidSup = rid;
                    $("#NAMA"+rowidSup).val("");			
                    loadDataBSup();
            
                    
                    if ( $("#NAMA"+rowidSup).val() == '' ) {				
                            $("#browseSupModal").modal("show");
                    }	
                }
                
                chooseSup = function(NO_SUPL,NAMA){
                    $("#NO_SUPL"+rowidSup).val(NO_SUPL);
                    $("#NAMA"+rowidSup).val(NAMA);
                    $("#browseSupModal").modal("hide");
                }
                ////////////////////////////////////////////////////
            });



            ///////////////////////////////////////




            function cekDetail() {
                var cekSup = '';
                $(".NO_SUPL").each(function() {

                    let z = $(this).closest('tr');
                    var NO_SUPLX = z.find('.NO_SUPL').val();

                    if (NO_SUPLX == "") {
                        cekSup = '1';

                    }
                });

                return cekSup;
            }

            function simpan() {
                // hitung();

                var tgl = $('#TGL').val();
                var bulanPer = {{ session()->get('periode')['bulan'] }};
                var tahunPer = {{ session()->get('periode')['tahun'] }};

                var check = '0';

                if(cekDetail() == '1'){
                    check = '1';
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'No. Supplier# Harus Diisi.'
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
                    alert("Bulan tidak sama dengan Periode");
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

                if (baris == 0) {
                    check = '1';
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Data detail kosong (Tambahkan 1 baris kosong jika ingin mengosongi detail)'
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

                //	hitung();

            }

            function baru() {

                kosong();
                hidup();

            }

            function ganti() {

                //  mati();
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
                $("#NOTES").attr("readonly", false);


                jumlahdata = 100;
                for (i = 0; i <= jumlahdata; i++) {
                    $("#REC" + i.toString()).attr("readonly", true);
                    $("#KODES" + i.toString()).attr("readonly", false);
                    $("#NAMAS" + i.toString()).attr("readonly", true);
                    $("#KOTA" + i.toString()).attr("readonly", true);
                    $("#KET" + i.toString()).attr("readonly", false);
                    $("#DELETEX" + i.toString()).attr("hidden", false);

                    $tipx = $('#tipx').val();


                    if ($tipx != 'new') {
                        $("#KODES" + i.toString()).attr("readonly", true);
                        $("#KODES" + i.toString()).removeAttr('onblur');
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
                $("#NOTES").attr("readonly", true);


                jumlahdata = 100;
                for (i = 0; i <= jumlahdata; i++) {
                    $("#REC" + i.toString()).attr("readonly", true);
                    $("#NO_SUPL" + i.toString()).attr("readonly", false);
                    $("#NAMA" + i.toString()).attr("readonly", true);
                    $("#NO_BARU" + i.toString()).attr("readonly", false);
                    $("#DELETEX" + i.toString()).attr("hidden", false);
                }
            }


            function kosong() {

                $('#NO_BUKTI').val("+");
                $('#NOTES').val("");

                var html = '';
                $('#detailx').html(html);

            }

            function hapusTrans() {
                let text = "Hapus Transaksi " + $('#NO_BUKTI').val() + "?";

                var loc = '';

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
                            loc = "{{ url('/usulnosup/delete/' . $header->NO_ID) }}";

                            // alert(loc);
                            window.location = loc;

                        });
                    }
                });
            }

            function closeTrans() {
                console.log("masuk");
                var loc = '';

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you really want to close this page? Unsaved changes will be lost.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, close it',
                    cancelButtonText: 'No, stay here'
                }).then((result) => {
                    if (result.isConfirmed) {
	        	        loc = "{{ url('/usulnosup/') }}";
                        window.location = loc;
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
		        var loc = "{{ url('/usulnosup/edit/') }}" + '?idx={{ $header->NO_ID}}&tipx=search&buktix=' +encodeURIComponent(cari);
                window.location = loc;

            }


            function tambah() {

                var x = document.getElementById('datatable').insertRow(baris + 1);

                html = `<tr>

                        <td>
                            <input name='NO_ID[]' id='NO_ID${idrow}' type='hidden' class='form-control NO_ID' value='new' readonly>
                            <input name='REC[]' id='REC${idrow}' type='text' class='REC form-control' onkeypress='return tabE(this,event)' readonly>
                        </td>

                        <td>
                            <input name='NO_SUPL[]' data-rowid=${idrow} onblur='browseSup(${idrow})' id='NO_SUPL${idrow}' type='text' class='form-control  NO_SUPL' >
                        </td>
                        <td>
                            <input name='NAMA[]'   id='NAMA${idrow}' type='text' class='form-control  NAMA' required readonly>
                        </td>
                        <td>
                            <input name='NO_BARU[]'   id='NO_BARU${idrow}' type='text' class='form-control  NO_BARU' required>
                        </td>
                        <td>
                            <button type='button' id='DELETEX${idrow}'  class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button>
                        </td>
                </tr>`;

                x.innerHTML = html;
                var html = '';

                idrow++;
                baris++;
                nomor();

                $(".ronly").on('keydown paste', function(e) {
                    e.preventDefault();
                    e.currentTarget.blur();
                });
            }
        </script>
    @endsection
