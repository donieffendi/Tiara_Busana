@extends('layouts.plain')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .card {}


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
            radial-gradient(farthest-side, #ffa516 90%, #0000) center/16px 16px,
            radial-gradient(farthest-side, green 90%, #0000) bottom/12px 12px;
        background-repeat: no-repeat;
        animation: l17 1s infinite linear;
        position: relative;
    }

    .loader::before {
        content: "";
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
        100% {
            transform: rotate(1turn)
        }
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
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@section('content')
    <div class="content-wrapper">

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">

                                <form action="{{ url('/vbrgdw-harga-pemula/store/') }}" method="POST" name="entri"
                                    id="entri">

                                    @csrf

                                    <div class="tab-content mt-3">

                                        <!-- style textbox model baru -->
                                        <style>
                                            /* Ensure specificity with class targeting */
                                            .form-group.special-input-label {
                                                position: relative;
                                                margin-left: 5px;
                                            }

                                            /* Ensure only bottom border for input */
                                            .form-group.special-input-label input {
                                                width: 100%;
                                                padding: 10px 0;
                                                border: none !important;
                                                border-bottom: 2px solid #ccc !important;
                                                outline: none !important;
                                                font-size: 16px !important;
                                                background: transparent !important;
                                                /* Remove any background color */
                                            }

                                            /* Bottom border color change on focus */
                                            .form-group.special-input-label input:focus {
                                                border-bottom: 2px solid #007BFF !important;
                                                /* Change color on focus */
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
                                            .form-group.special-input-label input:focus+label,
                                            .form-group.special-input-label input:not(:placeholder-shown)+label {
                                                top: -10px !important;
                                                font-size: 12px !important;
                                                color: #007BFF !important;
                                            }
                                        </style>
                                        <!-- tutupannya -->
                                        @php
                                            $hdr = $header;
                                        @endphp


                                        <div class="tab-content mt-3">
                                            <!-- <div class="form-group row">
                                                <div class="col-md-1">
                                                    <label for="KODES" class="form-label">No.Bukti</label>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="NO_BUKTI"
                                                            name="NO_BUKTI" value="{{ $hdr->NO_BUKTI ?? '' }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <label for="TGL" class="form-label">Tanggal</label>
                                                </div>
                                                <div class="col-md-4">
                                                    <input class="form-control date" id="TGL" name="TGL"
                                                        data-date-format="dd-mm-yyyy" type="text" autocomplete="off"
                                                        value="{{ optional($hdr)->TGL ? date('d-m-Y', strtotime($hdr->TGL)) : date('d-m-Y') }}">

                                                </div>

                                            </div> -->

                                            <div class="form-group row">
                                                <!-- No Bukti -->
                                                <div class="col-md-3">
                                                    <div class="form-group special-input-label">
                                                        <input type="text"
                                                            class="form-control"
                                                            id="NO_BUKTI"
                                                            name="NO_BUKTI"
                                                            value="{{ $hdr->NO_BUKTI ?? '' }}"
                                                            placeholder=" "
                                                            readonly>
                                                        <label for="KODES">No. Bukti</label>
                                                    </div>
                                                </div>

                                                <!-- Tanggal -->
                                                <div class="col-md-2">
                                                    <div class="form-group special-input-label">
                                                        <input type="text"
                                                            class="form-control date"
                                                            id="TGL"
                                                            name="TGL"
                                                            data-date-format="dd-mm-yyyy"
                                                            autocomplete="off"
                                                            value="{{ optional($hdr)->TGL ? date('d-m-Y', strtotime($hdr->TGL)) : date('d-m-Y') }}"
                                                            placeholder=" ">
                                                        <label for="TGL">Tanggal</label>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="form-group row">

                                                <!-- KODES (SELECT) -->
                                                <div class="col-md-3">
                                                    <div class="form-group special-input-label">
                                                        <select id="KODES"
                                                                name="KODES"
                                                                class="form-control"
                                                                style="width:100%; border:none; border-bottom:2px solid #ccc; background:transparent;"
                                                                required>
                                                            <option value="" disabled selected hidden></option>
                                                            @if ($hdr)
                                                                <option value="{{ $hdr->KODES }}" selected>
                                                                    {{ $hdr->KODES }}
                                                                </option>
                                                            @endif
                                                        </select>
                                                        <!-- <label for="KODES" style="top:-10px;font-size:12px;color:#007BFF;">
                                                            KODES
                                                        </label> -->
                                                    </div>
                                                </div>

                                                <!-- Kode Supplier -->
                                                {{-- <div class="col-md-3">
                                                    <div class="form-group special-input-label">
                                                        <input type="text"
                                                            id="KODES2"
                                                            class="form-control KODES2"
                                                            name="KODES2"
                                                            value="{{ $hdr->KODES ?? '' }}"
                                                            placeholder=" "
                                                            readonly>
                                                        <label for="KODES2">Kode Supplier</label>
                                                    </div>
                                                </div> --}}

                                                <!-- Nama Supplier -->
                                                <div class="col-md-3">
                                                    <div class="form-group special-input-label">
                                                        <input type="text"
                                                            class="form-control NAMAS"
                                                            id="NAMAS"
                                                            name="NAMAS"
                                                            value="{{ $hdr->NAMAS ?? '' }}"
                                                            placeholder=" ">
                                                        <label for="NAMAS">Nama Supplier</label>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="form-group row">

                                                <div class="col-md-3">
                                                    <div class="form-group special-input-label">
                                                        <input type="text"
                                                            class="form-control NOTES"
                                                            id="NOTES"
                                                            name="NOTES"
                                                            value="{{ $hdr->NOTES ?? '' }}"
                                                            placeholder=" ">
                                                        <label for="NOTES">Notes</label>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- loader tampil di modal  -->
                                        <div class="loader" style="z-index: 1055;" id='LOADX'></div>
                                        <div style="margin-bottom: 2y0px;"></div>
                                        <table id="datatable" class="table table-striped table-border table-responsive">

                                            <thead>
                                                <tr>
                                                    <th width="50px" style="text-align:center">No.</th>
                                                    <th width="200px" style="text-align:center">Kode Barang</th>
                                                    <th width="550px" style="text-align:center">Nama Barang</th>
                                                    <th width="200px" style="text-align:center">Harga</th>
                                                    <th width="200px" style="text-align:center">Diskon</th>
                                                    <th width="200px" style="text-align:center">Diskon 2</th>
                                                    <th width="200px" style="text-align:center">Diskon 3</th>
                                                    <th width="200px" style="text-align:center">Diskon 4</th>
                                                    <th></th>
                                                </tr>
                                            <tbody id="detail">
                                                @if (!empty($detail))
                                                    @foreach ($detail as $i => $row)
                                                        <tr>
                                                            <td>
                                                                {{ $i + 1 }}
                                                                <input type="hidden"
                                                                    name="detail[{{ $i }}][NO_ID]"
                                                                    value="{{ $i + 1 }}">
                                                            </td>

                                                            <td>
                                                                <input type="text"
                                                                    name="detail[{{ $i }}][KD_BRG]"
                                                                    class="form-control form-control-sm"
                                                                    value="{{ $row->KD_BRG ?? '' }}" readonly>
                                                            </td>

                                                            <td>
                                                                <input type="text"
                                                                    name="detail[{{ $i }}][NA_BRG]"
                                                                    class="form-control form-control-sm"
                                                                    value="{{ $row->NA_BRG ?? '' }}" readonly>
                                                            </td>

                                                            <td>
                                                                <input type="number"
                                                                    name="detail[{{ $i }}][HARGA]"
                                                                    class="form-control form-control-sm text-end"
                                                                    value="{{ $row->HARGA ?? 0 }}" step="0.01">
                                                            </td>

                                                            <td>
                                                                <input type="number"
                                                                    name="detail[{{ $i }}][DISC]"
                                                                    class="form-control form-control-sm text-end"
                                                                    value="{{ $row->DISC ?? 0 }}" step="0.01">
                                                            </td>

                                                            <td>
                                                                <input type="number"
                                                                    name="detail[{{ $i }}][DISC2]"
                                                                    class="form-control form-control-sm text-end"
                                                                    value="{{ $row->DISC2 ?? 0 }}" step="0.01">
                                                            </td>

                                                            <td>
                                                                <input type="number"
                                                                    name="detail[{{ $i }}][DISC3]"
                                                                    class="form-control form-control-sm text-end"
                                                                    value="{{ $row->DISC3 ?? 0 }}" step="0.01">
                                                            </td>

                                                            <td>
                                                                <input type="number"
                                                                    name="detail[{{ $i }}][DISC4]"
                                                                    class="form-control form-control-sm text-end"
                                                                    value="{{ $row->DISC4 ?? 0 }}" step="0.01">
                                                            </td>

                                                            <td>
                                                                <input type="number"
                                                                    name="detail[{{ $i }}][PPN]"
                                                                    class="form-control form-control-sm text-end"
                                                                    value="{{ $row->PPN ?? 0 }}" step="0.01">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>


                                        </table>
                                        <a type="button" id='PLUSX' class="fas fa-plus fa-sm md-3"
                                            style="font-size: 20px"></a>

                                        <div class="mt-3 col-md-12 form-group row">
                                            <div class="col-md-5">
                                                <button type="button" id='SAVEX' onclick='simpan()'
                                                    class="btn btn-success" class="fa fa-save"></i>Save</button>
                                            </div>
                                            <div class="col-md-3">
                                                <button hidden type="button" id='HAPUSX' hidden
                                                    onclick="hapusTrans()" class="btn btn-outline-danger">Hapus</button>
                                                <!-- <button type="button" id='CLOSEX'  onclick="location.href='{{ url('/vbrgdw') }}'" class="btn btn-outline-secondary">Close</button> -->
                                                <!-- tombol close sweet alert -->
                                                <button type="button" id='CLOSEX' onclick="closeTrans()"
                                                    class="btn btn-outline-secondary">Close</button>
                                            </div>
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


        <div class="modal fade" id="browseBarangModal" tabindex="-1" role="dialog"
            aria-labelledby="browseBarangModalLabel" aria-hidden="true">
            <div class="modal-dialog mw-100 w-75" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="browseBarangModalLabel">Cari Barang</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-stripped table-bordered" id="table-bBarang">
                            <thead>
                                <tr>
                                    <th>Kd Barang</th>
                                    <th>Nama Barang</th>
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
        <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script> -->
        <script src="{{ asset('foxie_js_css/bootstrap.bundle.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <!-- tambahan untuk sweetalert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- tutupannya -->

        <script>
            var idrow = 0;
            var baris = 0;

            function numberWithCommas(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }


            $(document).ready(function() {

                setTimeout(function() {

                    $("#LOADX").hide();

                }, 500);
                $('body').on('click', '#PLUSX', function() {
                    if ($('#KODES2').val() == '') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: 'Silahkan Pilih Suplier Terlebih Dahulu.'
                        });
                        return; // Stop function execution
                    }
                    tambah();
                });

                $('#KODES').select2({

                    placeholder: 'Pilih Suplier',
                    allowClear: true,
                    ajax: {
                        url: "{{ url('vbrgdw-harga-pemula/browse_kodes') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term,
                                'CBG': 'DC'
                            };
                        },
                        processResults: function(data) {
                            console.log(data);
                            return {
                                results: data.map(item => ({
                                    id: item.KODES, // The ID of the user
                                    text: item.KODES + ' (' + item.NAMAS + ')',
                                    namas: item.NAMAS
                                }))
                            };
                        },
                        cache: true
                    },
                });
                $('#KODES').on('select2:select', function(e) {
                    var data = e.params.data;

                    $("#NAMAS").val(data.namas);
                    $("#KODES2").val(data.id);
                });

                $('#KODES').on('select2:unselect', function() {
                    $("#NAMAS").val('');
                    $("#KODES").val('');
                    $("#KODES2").val('');
                });


                function getVbrgdw(id) {

                    var mulai = (idrow == baris) ? idrow - 1 : idrow;

                    $.ajax({
                        type: 'GET',
                        url: "{{ url('vbrgdw-harga-pemula/browse') }}",
                        data: {
                            KODES: id
                        },
                        success: function(resp) {

                            var html = '';
                            for (i = 0; i < resp.length; i++) {
                                html += `<tr>
									<td>
										<input name='NO_ID[]' id='NO_ID${i}' value="${i+1}" type='text' class='form-control NO_ID' readonly>
									</td>
									<td>
										<input name='KD_BRG[]' id='KD_BRG${i}' value="${resp[i].KDBAR}" type='text' class='form-control KD_BRG' readonly>
						            </td>
						            <td>
						 			    <input name='NA_BRG[]' id='NA_BRG${i}' value="${resp[i].NMBAR}" type='text' class='form-control  NA_BRG' readonly>
						            </td>
									<td>
						 			    <input name='HARGAAWAL[]' id='HARGAAWAL${i}' value="${resp[i].HB}" hidden type='text' class='form-control  HARGAAWAL'>
						 			    <input name='HARGA[]' id='HARGA${i}' value="${resp[i].HB}" type='text' class='form-control  HARGA'>
						            </td>
									<td>
						 			    <input name='DISCAWAL[]' id='DISCAWAL${i}' value="${resp[i].DISC}" hidden type='text' class='form-control DISCAWAL'>
						 			    <input name='DISC[]' id='DISC${i}' value="${resp[i].DISC}" type='text' class='form-control DISC'>
						            </td>
									<td>
						 			    <input name='DISCAWAL2[]' id='DISCAWAL2${i}' value="${resp[i].DISC2}" hidden type='text' class='form-control DISCAWAL2'>
						 			    <input name='DISC2[]' id='DISC2${i}' value="${resp[i].DISC2}" type='text' class='form-control DISC2'>
						            </td>
									<td>
						 			    <input name='DISCAWAL3[]' id='DISCAWAL3${i}' value="${resp[i].DISC3}" hidden type='text' class='form-control DISCAWAL3'>
						 			    <input name='DISC3[]' id='DISC3${i}' value="${resp[i].DISC3}" type='text' class='form-control DISC3'>
						            </td>
									<td>
						 			    <input name='DISCAWAL4[]' id='DISCAWAL4${i}' value="${resp[i].DISC4}" hidden type='text' class='form-control DISCAWAL4'>
						 			    <input name='DISC4[]' id='DISC4${i}' value="${resp[i].DISC4}" type='text' class='form-control DISC4'>
						            </td>
									<td hidden>
						 			    <input name='STATUS[]' id='DONE${i}' value="DONE" type='text' class='form-control  STATUS'>
									</td>
                                </tr>`;

                            }
                            $('#detail').html(html);
                            for (i = 0; i <= resp.length; i++) {
                                $(`#HARGA${i}`).autoNumeric('init', {
                                    aSign: '<?php echo ''; ?>',
                                    vMin: '-999999999.99'
                                });


                                $(`#DISC${i}`).autoNumeric('init', {
                                    aSign: '<?php echo ''; ?>',
                                    vMin: '-999999999.99'
                                });
                            }

                            idrow = resp.length;
                            baris = resp.length;

                            nomor();
                        }
                    });
                }




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
                            // tambah();
                            // var nomer = idrow-1;
                            // console.log("REC"+nomor);
                            // document.getElementById("REC"+nomor).focus();
                            // form.submit();
                        }
                        return false;
                    }
                });

                $tipx = $('#tipx').val();

                if ($tipx == 'new') {
                    baru();
                    tambah();

                    $("#RING0").val('LOKAL');
                    tambah();
                    $("#RING1").val('1');
                    tambah();
                    $("#RING2").val('2');
                    tambah();
                    $("#RING3").val('3');

                }

                if ($tipx != 'new') {
                    ganti();
                }

                $('body').on('click', '.del', function() {
                    var val = $(this).parents("tr").remove();
                    baris--;
                    nomor();

                });

                $('.date').datepicker({
                    dateFormat: 'dd-mm-yy'
                });




            });

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




                $("#KD_BRG").attr("readonly", true);




                $("#NA_BRG").attr("readonly", true);
                $("#KODES").attr("readonly", true);
                $("#NAMAS").attr("readonly", true);

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

                $("#KD_BRG").attr("readonly", true);
                $("#NA_BRG").attr("readonly", true);

                $("#KODES").attr("readonly", true);
                $("#NAMAS").attr("readonly", true)

            }


            function kosong() {

                $('#KODES').val("+");
                $('#NAMAS').val("");
                $('#KD_BRG').val("");
                $('#NA_BRG').val("");
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
                        loc = "{{ url('/vbrgdw-harga-pemula/') }}";
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


            var hasilCek;

            function cekBarang(kdbrg) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('vbrgdw/cekbarang') }}",
                    async: false,
                    data: ({
                        KODES: kdbrg,
                    }),
                    success: function(data) {
                        // hasilCek=data;
                        if (data.length > 0) {
                            $.each(data, function(i, item) {
                                hasilCek = data[i].ADA;
                            });
                        }
                    },
                    error: function() {
                        alert('Error cekBarang occured');
                    }
                });
                return hasilCek;
            }
            //////////////////////////////////////////////////////

            var dTableBarang;
            var rowidBarang;
            // loadDataBarang = function() {

            //     $.ajax({
            //         type: 'GET',
            //         url: "{{ url('vbrgdw-harga-pemula/browse') }}",
            //         async: false,
            //         data: {
            //             'KODES': $("#KODES").val(),

            //         },
            //         success: function(response)

            //         {
            //             resp = response;


            //             if (resp.length > 1) {
            //                 if (dTableBarang) {
            //                     dTableBarang.clear();
            //                 }
            //                 for (i = 0; i < resp.length; i++) {

            //                     dTableBarang.row.add([
            //                         '<a href="javascript:void(0);" onclick="chooseBarang(' +
            //                         '\'' + resp[i].KD_BRG + '\',' +
            //                         '\'' + resp[i].NA_BRG + '\',' +
            //                         ')">' + resp[i].KD_BRG + '</a>',
            //                         resp[i].NA_BRG
            //                     ]);
            //                 }
            //                 dTableBarang.draw();

            //             } else {
            //                 $("#KD_BRG" + rowidBarang).val(resp[0].KD_BRG);
            //                 $("#NA_BRG" + rowidBarang).val(resp[0].NA_BRG);
            //             }
            //         }
            //     });
            // }
            loadDataBarang = function(fromInput = false) {

                $.ajax({
                    type: 'GET',
                    url: "{{ url('vbrgdw-harga-pemula/browse') }}",
                    data: {
                        'KD_BRG': $("#KD_BRG" + rowidBarang).val(),
                    },
                    success: function(resp) {

                        // JIKA DATA DITEMUKAN SATU
                        if (resp.length === 1) {
                            $("#KD_BRG" + rowidBarang).val(resp[0].KDBAR);
                            $("#NA_BRG" + rowidBarang).val(resp[0].NMBAR);
                            return;
                        }

                        // JIKA BANYAK DATA → TAMPILKAN MODAL
                        if (resp.length > 1) {

                            if (dTableBarang) {
                                dTableBarang.clear();
                            }

                            for (let i = 0; i < resp.length; i++) {
                                dTableBarang.row.add([
                                    '<a href="javascript:void(0);" onclick="chooseBarang(' +
                                    '\'' + resp[i].KDBAR + '\',' +
                                    '\'' + resp[i].NMBAR + '\'' +
                                    ')">' + resp[i].KDBAR + '</a>',
                                    resp[i].NMBAR
                                ]);
                            }

                            dTableBarang.draw();
                            $("#browseBarangModal").modal("show");
                            return;
                        }

                        // JIKA TIDAK ADA DATA → BARU MUNCUL MODAL
                        if (resp.length === 0 && fromInput) {
                            $("#browseBarangModal").modal("show");
                        }
                    }
                });
            }

            dTableBarang = $("#table-bBarang").DataTable({

            });

            // browseBrg = function(rid) {
            //     rowidBarang = rid;
            //     $("#NA_BRG" + rowidBarang).val("");
            //     loadDataBarang();


            //     if ($("#NA_BRG" + rowidBarang).val() == '') {
            //         $("#browseBarangModal").modal("show");
            //     }
            // }

            browseBrg = function(e, rid) {
                if (e.key !== "Enter") return;

                e.preventDefault();

                rowidBarang = rid;
                $("#NA_BRG" + rowidBarang).val("");

                loadDataBarang(true);
            }


            chooseBarang = function(KDBAR, NMBAR, HARGA, DISC, DISC2, DISC3, DISC4) {
                $("#KD_BRG" + rowidBarang).val(KDBAR);
                $("#NA_BRG" + rowidBarang).val(NMBAR);
                $("#browseBarangModal").modal("hide");
            }


            function tambah() {

                var x = document.getElementById('datatable').insertRow(baris + 1);

                html = `<tr>
                        <td>
                            <input
                                name="detail[${baris}][NO_ID]"
                                 value="${baris + 1}"
                                class="form-control">
                        </td>

                        <td>
                            <input name="detail[${baris}][KD_BRG]"
                                onkeydown="browseBrg(event, ${baris})"
                                id="KD_BRG${baris}"
                                type="text"
                                class="form-control">
                        </td>


                        <td>
                            <input name="detail[${baris}][NA_BRG]"
                                id="NA_BRG${baris}"
                                type="text"
                                class="form-control"
                                readonly>
                        </td>

                        <td>
                            <input name="detail[${baris}][HARGA]"
                                id="HARGA${baris}"
                                type="text"
                                class="form-control">
                        </td>

                        <td>
                            <input name="detail[${baris}][DISC]"
                                id="DISC${baris}"
                                type="text"
                                class="form-control">
                        </td>

                        <td>
                            <input name="detail[${baris}][DISC2]"
                                id="DISC2${baris}"
                                type="text"
                                class="form-control">
                        </td>

                        <td>
                            <input name="detail[${baris}][DISC3]"
                                id="DISC3${baris}"
                                type="text"
                                class="form-control">
                        </td>

                        <td>
                            <input name="detail[${baris}][DISC4]"
                                id="DISC4${baris}"
                                type="text"
                                class="form-control">
                        </td>

                        <td>
                            <button type="button"
                                class="btn btn-sm btn-danger btn-delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;

                x.innerHTML = html;

                baris++;
            }




            function simpan() {
                hasilCek = 0;
                if ($('#KODES').val() == '') {
                    hasilCek = '1';
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Kode Barang# Harus Diisi.'
                    });
                    return; // Stop function execution
                }
                if ($('#NAMAS').val() == '') {
                    hasilCek = '1';
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Nama Barang# Harus Diisi.'
                    });
                    return; // Stop function execution
                }

                (hasilCek == 0) ? document.getElementById("entri").submit(): alert('Barang ' + $('#KODES').val() +
                    ' sudah ada!');


                $("#LOADX").hide();
            }
        </script>
    @endsection
