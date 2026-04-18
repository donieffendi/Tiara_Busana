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

                            <form action="{{($tipx=='new')? url('/tandaretur/store/') : url('/tandaretur/update/'.$header->NO_ID ) }}" method="POST" name ="entri" id="entri" >


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
                                                                <label for="NO_SUPL" class="form-label">Kode Barang</label>
                                                            </th>
                                                            <th style="width: 550px;text-align: center;white-space: nowrap;">Nama Barang</th>
                                                            <th style="width: 250px; white-space: nowrap;">Ukuran</th>
                                                            <th style="width: 250px; white-space: nowrap;">Kemasan</th>
                                                            <th style="width: 75px; white-space: nowrap;">Tanda Retur Lama</th>
                                                            <th style="width: 75px; white-space: nowrap;">Tanda Retur Baru</th>
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
                                                                    <input name="KDBAR[]" id="KDBAR{{ $no }}"
                                                                        type="text" value="{{ $detail->KDBAR }}"
                                                                        class="form-control KDBAR" readonly required>
                                                                </td>
                                                                <td>
                                                                    <input name="NMBAR[]" id="NMBAR{{ $no }}"
                                                                        type="text" value="{{ $detail->NMBAR }}"
                                                                        class="form-control NMBAR" readonly required>
                                                                </td>

                                                                <td>
                                                                    <input name="KET_UK[]" id="KET_UK{{ $no }}"
                                                                        type="text" value="{{ $detail->KET_UK }}"
                                                                        class="form-control KET_UK" readonly required>
                                                                </td>

                                                                <td>
                                                                    <input name="KET_KEM[]" id="KET_KEM{{ $no }}"
                                                                        type="text" value="{{ $detail->KET_KEM }}"
                                                                        class="form-control KET_KEM" readonly required>
                                                                </td>

                                                                <td>
                                                                    <input name="RETUR[]" id="RETUR{{ $no }}"
                                                                        type="text" value="{{ $detail->RETUR }}"
                                                                        class="form-control RETUR" readonly required>
                                                                </td>

                                                                <td>
                                                                    <input name="RETUR_B[]" id="RETUR_B{{ $no }}"
                                                                        type="text" value="{{ $detail->RETUR_B }}"
                                                                        class="form-control RETUR_B" required>
                                                                </td>

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
                                            <button hidden type="button" id='TOPX'  onclick="location.href='{{url('/tandaretur/edit/?idx=' .$idx. '&tipx=top')}}'" class="btn btn-outline-primary">Top</button>
                                            <button hidden type="button" id='PREVX' onclick="location.href='{{url('/tandaretur/edit/?idx='.$header->NO_ID.'&tipx=prev&buktix='.$header->NO_BUKTI )}}'" class="btn btn-outline-primary">Prev</button>
                                            <button hidden type="button" id='NEXTX' onclick="location.href='{{url('/tandaretur/edit/?idx='.$header->NO_ID.'&tipx=next&buktix='.$header->NO_BUKTI )}}'" class="btn btn-outline-primary">Next</button>
                                            <button hidden type="button" id='BOTTOMX' onclick="location.href='{{url('/tandaretur/edit/?idx=' .$idx. '&tipx=bottom')}}'" class="btn btn-outline-primary">Bottom</button>
                                        </div>
                                        <div class="col-md-5">
                                            <button hidden type="button" id='NEWX' onclick="location.href='{{url('/tandaretur/edit/?idx=0&tipx=new')}}'" class="btn btn-warning">New</button>
                                            <button hidden type="button" id='EDITX' onclick='hidup()' class="btn btn-secondary">Edit</button>
                                            <button hidden type="button" id='UNDOX' onclick="location.href='{{url('/tandaretur/edit/?idx=' .$idx. '&tipx=undo' )}}'" class="btn btn-info">Undo</button>
                                            <button type="button" id='SAVEX' onclick='simpan()' class="btn btn-success" class="fa fa-save"></i>Save</button>

                                        </div>
                                        <div class="col-md-3">
                                            <button hidden type="button" id='HAPUSX' hidden onclick="hapusTrans()" class="btn btn-outline-danger">Hapus</button>

                                            <!-- <button type="button" id='CLOSEX'  onclick="location.href='{{url('/tandaretur' )}}'" class="btn btn-outline-secondary">Close</button> -->

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
	  <div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseBarangModalLabel">Cari Barang</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bbarang">
				<thead>
					<tr>
						<th>No. Barang</th>
						<th>Nama Barang</th>
						<th>Supplier</th>
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

                var dTableBBarang;
                var rowidBarang;
                loadDataBBarang = function(){
                
                    $.ajax(
                    {
                        type: 'GET',    
                        url: "{{url('tandaretur/browse_brg')}}",
                        async : false,

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
                                            '<a href="javascript:void(0);" onclick=\'chooseBarang(' 
                                                + JSON.stringify(resp[i].KDBAR) + ',' 
                                                + JSON.stringify(resp[i].NMBAR) + ',' 
                                                + JSON.stringify(resp[i].KET_UK) + ','
                                                + JSON.stringify(resp[i].KET_KEM) + ','
                                                + JSON.stringify(resp[i].RETUR) +
                                            ')\'>' + resp[i].KDBAR + '</a>',
                                            resp[i].NMBAR,
                                            resp[i].RETUR,
                                        ]);
                                    }
                                    dTableBBarang.draw();
                            
                            }
                            else
                            {
                                $("#KDBAR"+rowidBarang).val(resp[0].KDBAR);
                                $("#NMBAR"+rowidBarang).val(resp[0].NMBAR);
                                $("#RETUR"+rowidBarang).val(resp[0].RETUR);
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
                
                chooseBarang = function(KDBAR,NMBAR,KET_UK,KET_KEM,RETUR){
                    $("#KDBAR"+rowidBarang).val(KDBAR);
                    $("#NMBAR"+rowidBarang).val(NMBAR);
                    $("#KET_UK"+rowidBarang).val(KET_UK);
                    $("#KET_KEM"+rowidBarang).val(KET_KEM);
                    $("#RETUR"+rowidBarang).val(RETUR);
                    $("#browseBarangModal").modal("hide");
                }
                ////////////////////////////////////////////////////
            });



            ///////////////////////////////////////




            function cekDetail() {
                var cekBarang = '';
                $(".KDBAR").each(function() {

                    let z = $(this).closest('tr');
                    var KDBARX = z.find('.KDBAR').val();

                    if (KDBARX == "") {
                        cekBarang = '1';

                    }
                });

                return cekBarang;
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
                        text: 'Barang# Harus Diisi.'
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
                    $("#KDBAR" + i.toString()).attr("readonly", false);
                    $("#NMBAR" + i.toString()).attr("readonly", true);
                    $("#KET_UK" + i.toString()).attr("readonly", true);
                    $("#KET_KEM" + i.toString()).attr("readonly", true);
                    $("#RETUR" + i.toString()).attr("readonly", true);
                    $("#RETUR_B" + i.toString()).attr("readonly", false);
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
                            loc = "{{ url('/tandaretur/delete/' . $header->NO_ID) }}";

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
	        	        loc = "{{ url('/tandaretur/') }}";
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
		        var loc = "{{ url('/tandaretur/edit/') }}" + '?idx={{ $header->NO_ID}}&tipx=search&buktix=' +encodeURIComponent(cari);
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
                            <input name='KDBAR[]' data-rowid=${idrow} onblur='browseBarang(${idrow})' id='KDBAR${idrow}' type='text' class='form-control  KDBAR' >
                        </td>
                        <td>
                            <input name='NMBAR[]'   id='NMBAR${idrow}' type='text' class='form-control  NMBAR' required readonly>
                        </td>
                        <td>
                            <input name='KET_UK[]'   id='KET_UK${idrow}' type='text' class='form-control  KET_UK' readonly required>
                        </td>
                        <td>
                            <input name='KET_KEM[]'   id='KET_KEM${idrow}' type='text' class='form-control  KET_KEM' readonly required>
                        </td>
                        <td>
                            <input name='RETUR[]'   id='RETUR${idrow}' type='text' class='form-control  RETUR' readonly required>
                        </td>
                        <td>
                            <input name='RETUR_B[]'   id='RETUR_B${idrow}' type='text' class='form-control  RETUR_B' required>
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
