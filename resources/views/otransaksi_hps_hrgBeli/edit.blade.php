@extends('layouts.plain')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .card {}

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

                                <form action="{{ url('/hps_hrgBeli/store') }}" method="POST" name="entri" id="entri">

                                    @csrf
                                    <div class="tab-content mt-3">

                                        <!-- style text box model baru -->

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

                                        {{-- =======================
                                            BARIS : BUKTI
                                        ======================= --}}
                                        <div class="row">
                                            {{-- NO_ID (HIDDEN) --}}
                                            <input type="hidden" id="NO_ID" name="NO_ID"
                                                value="{{ $header->NO_ID ?? '' }}" readonly>

                                            {{-- TIPX (HIDDEN) --}}
                                            <input type="hidden" id="tipx" name="tipx" value="{{ $tipx }}">

                                            <div class="col-md-3">
                                                <div class="form-group special-input-label">
                                                    <input type="text" class="form-control NO_BUKTI" id="NO_BUKTI"
                                                        name="NO_BUKTI" placeholder=" "
                                                        value="{{ $header->NO_BUKTI ?? '' }}" readonly>

                                                    <label>Bukti #</label>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- =======================
                                            BARIS : KODES / BELI
                                        ======================= --}}
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group special-input-label">

                                                    <!-- INPUT UTAMA -->
                                                    <input type="text" class="form-control" id="NO_BELI_DISPLAY"
                                                        placeholder=" " autocomplete="off" readonly
                                                        value="{{ $tipx == 'edit' ? $header->NO_BELI ?? '' : '' }}"
                                                        onclick="$('#NO_BELI').select2('open')">

                                                    <label for="NO_BELI_DISPLAY">Pilih Supplier (Wajib Diisi Dahulu)</label>

                                                    <!-- SELECT ASLI (TAK TERLIHAT, TAPI SEJAJAR) -->
                                                    <select id="NO_BELI" name="NO_BELI" class="form-control"
                                                        style="
                                                                position: absolute;
                                                                left: 0;
                                                                top: 0;
                                                                width: 100%;
                                                                height: 100%;
                                                                opacity: 0;
                                                                pointer-events: none;
                                                            ">
                                                    </select>

                                                </div>

                                                <input type="hidden" id="KET_HEADER" name="KET_HEADER"
                                                    value="{{ $header->KET ?? '' }}">
                                            </div>
                                        </div>



                                        {{-- =======================
                                            BARIS : SUPPLIER
                                        ======================= --}}
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group special-input-label">
                                                    <input type="text" class="form-control KODES" id="KODES"
                                                        name="KODES" placeholder=" " value="{{ $header->KODES ?? '' }}"
                                                        readonly>

                                                    <label>Kode Supplier</label>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group special-input-label">
                                                    <input type="text" class="form-control NAMAS" id="NAMAS"
                                                        name="NAMAS" placeholder=" " value="{{ $header->NAMAS ?? '' }}"
                                                        readonly>

                                                    <label>Nama Supplier</label>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <!-- code text box baru -->
                                            <div class="col-md-5 form-group row special-input-label">

                                                <input type="text" class="NOTES" id="NOTES" name="NOTES"
                                                    value="{{ $header->NOTES ?? '' }}" placeholder=" ">
                                                <label for="NOTES">Notes</label>
                                            </div>
                                            <!-- tutupannya -->


                                        </div>

                                        <!-- loader tampil di modal  -->
                                        <div class="loader" style="z-index: 1055;" id='LOADX'></div>


                                        <div class="tab-content mt-3">

                                            <table id="datatable" class="table table-striped table-bordered">
                                                {{-- <thead>
												<tr>
													<th rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid #ddd;">No.</th>
													<th rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid #ddd;">
														<label style="color:red;font-size:20px">* </label>
														<label for="KD_BRG" class="form-label">Kode Barang</label>
													</th>
													<th rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid #ddd;">Nama Barang</th>
													<th rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid #ddd;">Qty</th>
													<th colspan="5" style="text-align: center; border: 1px solid #ddd;">LAMA</th>
													<th colspan="5" style="text-align: center; border: 1px solid #ddd;">BARU</th>
													<th rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid #ddd;">Ket</th>
													<th rowspan="2" style="border: 1px solid #ddd;"></th>
												</tr>
												<tr>
													<th style="text-align: center; border: 1px solid #ddd;">Harga</th>
													<th style="text-align: center; border: 1px solid #ddd;">Diskon 1</th>
													<th style="text-align: center; border: 1px solid #ddd;">Diskon 2</th>
													<th style="text-align: center; border: 1px solid #ddd;">Diskon 3</th>
													<th style="text-align: center; border: 1px solid #ddd;">Diskon 4</th>
													<th style="text-align: center; border: 1px solid #ddd;">Harga</th>
													<th style="text-align: center; border: 1px solid #ddd;">Diskon 1</th>
													<th style="text-align: center; border: 1px solid #ddd;">Diskon 2</th>
													<th style="text-align: center; border: 1px solid #ddd;">Diskon 3</th>
													<th style="text-align: center; border: 1px solid #ddd;">Diskon 4</th>
												</tr>
											</thead> --}}

                                                {{-- <tbody>
												<?php $no = 0; ?>
												@if ($tipx != 'new')
												@foreach ($detail as $detail)
												<tr>
													<td>

														<input name="REC[]" id="REC{{$no}}" type="text" value="{{$detail->REC}}" class="form-control REC" onkeypress="return tabE(this,event)" readonly style="text-align:center">
													</td>

													<td>
														<input name="KD_BRG[]" id="KD_BRG{{$no}}" type="text" class="form-control KD_BRG "
															value="{{$detail->KD_BRG}}" readonly>
													</td>

													<td>
														<input name="NA_BRG[]" id="NA_BRG{{$no}}" type="text" class="form-control NA_BRG " value="{{$detail->NA_BRG}}" readonly>
													</td>


													<td><input name="QTY[]" readonly value="{{$detail->QTY}}" id="QTY{{$no}}" type="text" style="text-align: right" class="form-control QTY text-primary"></td>

													<!-- LAMA - Harga Lama -->
													<td><input name="HARGALAMA[]" id="HARGALAMA{{$no}}" type="text" style="text-align: right" class="form-control HARGALAMA" value="{{$detail->HARGALAMA}}" readonly></td>

													<!-- LAMA - Diskon Lama 1 -->
													<td><input name="DISKLAMA[]" id="DISKLAMA{{$no}}" type="text" style="text-align: right" class="form-control DISKLAMA" value="{{$detail->DISKLAMA}}" readonly></td>

													<!-- LAMA - Diskon Lama 2 -->
													<td><input name="DISKLAMA2[]" id="DISKLAMA2{{$no}}" type="text" style="text-align: right" class="form-control DISKLAMA2" value="{{$detail->DISKLAMA2}}" readonly></td>

													<!-- LAMA - Diskon Lama 3 -->
													<td><input name="DISKLAMA3[]" id="DISKLAMA3{{$no}}" type="text" style="text-align: right" class="form-control DISKLAMA3" value="{{$detail->DISKLAMA3}}" readonly></td>

													<!-- LAMA - Diskon Lama 4 -->
													<td><input name="DISKLAMA4[]" id="DISKLAMA4{{$no}}" type="text" style="text-align: right" class="form-control DISKLAMA4" value="{{$detail->DISKLAMA4}}" readonly></td>

													<!-- BARU - Harga Baru -->
													<td><input name="HARGA[]" id="HARGA{{$no}}" type="text" style="text-align: right" class="form-control HARGA" value="{{$detail->HARGA}}" readonly></td>

													<!-- BARU - Diskon Baru 1 -->
													<td><input name="DISK[]" id="DISK{{$no}}" type="text" style="text-align: right" class="form-control DISK" value="{{$detail->DISK}}" readonly></td>

													<!-- BARU - Diskon Baru 2 -->
													<td><input name="DISK2[]" id="DISK2{{$no}}" type="text" style="text-align: right" class="form-control DISK2" value="{{$detail->DISK2}}" readonly></td>

													<!-- BARU - Diskon Baru 3 -->
													<td><input name="DISK3[]" id="DISK3{{$no}}" type="text" style="text-align: right" class="form-control DISK3" value="{{$detail->DISK3}}" readonly></td>

													<!-- BARU - Diskon Baru 4 -->
													<td><input name="DISK4[]" id="DISK4{{$no}}" type="text" style="text-align: right" class="form-control DISK4" value="{{$detail->DISK4}}" readonly></td>

													<td>
														<input name="KET[]" id="KET{{$no}}" type="text" class="form-control KET" value="{{$detail->KET}}" required>
													</td>

													<td>
														<button type='button' id='DELETEX{{$no}}' class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button>
													</td>

												</tr>

												<?php $no++; ?>
												@endforeach
												@endif
											</tbody> --}}
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Kode Barang</th>
                                                        <th>Nama Barang</th>
                                                        <!-- <th>Qty</th> -->
                                                        <th>Status</th>
                                                        <th>Harga</th>
                                                        <th>Diskon 1</th>
                                                        <th>Diskon 2</th>
                                                        <th>Diskon 3</th>
                                                        <th>Diskon 4</th>
                                                        <!-- <th>Ket</th> -->
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>


                                                <tbody>
                                                    @php $no = 0; @endphp
                                                    @if ($tipx != 'new')
                                                        @foreach ($detail as $detail)
                                                            <!-- ================= BARIS LAMA ================= -->
                                                            <tr>
                                                                <!-- NO -->
                                                                <td rowspan="2">
                                                                    <input name="REC[]" id="REC{{ $no }}"
                                                                        type="text" value="{{ $no + 1 }}"
                                                                        class="form-control REC" readonly>
                                                                </td>

                                                                <!-- KODE BARANG -->
                                                                <td rowspan="2">
                                                                    <input name="KD_BRG[]" id="KD_BRG{{ $no }}"
                                                                        type="text" class="form-control KD_BRG"
                                                                        value="{{ $detail->KD_BRG }}">
                                                                </td>

                                                                <!-- NAMA BARANG -->
                                                                <td rowspan="2">
                                                                    <input name="NA_BRG[]" id="NA_BRG{{ $no }}"
                                                                        type="text" class="form-control NA_BRG"
                                                                        value="{{ $detail->NA_BRG }}" readonly>
                                                                </td>


                                                                <!-- STATUS -->
                                                                <td class="text-center fw-bold text-danger">LAMA</td>

                                                                <!-- HARGA LAMA -->
                                                                <td>
                                                                    <input name="HARGALAMA[]"
                                                                        id="HARGALAMA{{ $no }}" type="text"
                                                                        class="form-control HARGALAMA"
                                                                        value="{{ $detail->HARGA }}" readonly
                                                                        style="text-align:right">
                                                                </td>

                                                                <td><input name="DISCLAMA[]" value="{{ $detail->DISC }}"
                                                                        class="form-control" readonly
                                                                        style="text-align:right"></td>
                                                                <td><input name="DISCLAMA2[]"
                                                                        value="{{ $detail->DISC2 }}" class="form-control"
                                                                        readonly style="text-align:right"></td>
                                                                <td><input name="DISCLAMA3[]"
                                                                        value="{{ $detail->DISC3 }}" class="form-control"
                                                                        readonly style="text-align:right"></td>
                                                                <td><input name="DISCLAMA4[]"
                                                                        value="{{ $detail->DISC4 }}" class="form-control"
                                                                        readonly style="text-align:right"></td>



                                                                <!-- AKSI -->
                                                                <td rowspan="2" class="text-center">
                                                                    <button type="button"
                                                                        id="DELETEX{{ $no }}"
                                                                        class="btn btn-sm btn-circle btn-outline-danger btn-delete">
                                                                        <i class="fa fa-fw fa-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>

                                                            <!-- ================= BARIS BARU ================= -->
                                                            <tr>
                                                                <!-- STATUS -->
                                                                <td class="text-center fw-bold text-success">BARU</td>

                                                                <!-- HARGA BARU -->
                                                                <td>
                                                                    <input name="HARGA[]" id="HARGA{{ $no }}"
                                                                        type="text" class="form-control HARGA"
                                                                        value="{{ $detail->HARGA }}"
                                                                        style="text-align:right">
                                                                </td>

                                                                <td><input name="DISC[]" value="{{ $detail->DISC }}"
                                                                        class="form-control" style="text-align:right">
                                                                </td>
                                                                <td><input name="DISC2[]" value="{{ $detail->DISC2 }}"
                                                                        class="form-control" style="text-align:right">
                                                                </td>
                                                                <td><input name="DISC3[]" value="{{ $detail->DISC3 }}"
                                                                        class="form-control" style="text-align:right">
                                                                </td>
                                                                <td><input name="DISC4[]" value="{{ $detail->DISC4 }}"
                                                                        class="form-control" style="text-align:right">
                                                                </td>
                                                            </tr>

                                                            @php $no++; @endphp
                                                        @endforeach
                                                    @endif
                                                </tbody>

                                            </table>
                                            <div class="col-md-2 row">
                                                <a type="button" id='PLUSX' onclick="tambah()"
                                                    class="fas fa-plus fa-sm md-3" style="font-size: 20px"></a>

                                            </div>

                                        </div>
                                    </div>

                                    <hr style="margin-top: 30px; margin-bottom: 30px">
                                    <!-- dari sini shelvi-->

                                    <div class="form-group row">
                                        <div class="col-md-12" align="right">
                                            <button type="button" class="btn btn-primary"
                                                onclick="simpan()">Simpan</button>
                                            <a href="{{ url('/hps_hrgBeli') }}" class="btn btn-secondary">Batal</a>
                                        </div>


                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for selecting deleted data to add back -->
        <div class="modal fade" id="deletedDataModal" tabindex="-1" role="dialog"
            aria-labelledby="deletedDataModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deletedDataModalLabel">Pilih Data yang Akan Ditambahkan Kembali</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered" id="deletedDataTable">
                            <thead>
                                <tr>
                                    <th>Pilih</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody id="deletedDataTableBody">
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" onclick="addSelectedData()">Tambahkan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="browseBarangModal" tabindex="-1" role="dialog"
            aria-labelledby="browseBarangModalLabel" aria-hidden="true">
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
    @endsection

    @section('footer-scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
        <script src="{{ asset('js/autoNumerics/autoNumeric.min.js') }}"></script>
        <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script> -->
        <script src="{{ asset('foxie_js_css/bootstrap.bundle.min.js') }}"></script>

        <!-- tambahan untuk sweetalert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- tutupannya -->

        <script>
            var idrow = 1;
            var baris = 1;

            // Variables to store original and deleted data
            var originalDetails = [];
            var deletedDetails = [];
            var currentVisibleRows = [];

            function numberWithCommas(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            $(document).ready(function() {
                makeFieldsReadonly();

                setTimeout(function() {

                    $("#LOADX").hide();

                }, 500);

                idrow = <?= $no ?>;
                baris = <?= $no ?>;

                // Initialize Select2 only for 'new' mode
                if ($("#tipx").val() == 'new') {
                    $('#NO_BELI').select2({
                        //placeholder: 'Pilih Kode Supplier',
                        allowClear: true,
                        ajax: {
                            url: "{{ url('zsup/browse') }}",
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    KODES: params.term
                                };
                            },

                            processResults: function(data) {
                                return {
                                    results: data.map(item => ({
                                        id: item.KODES,
                                        text: item.KODES + ' - ' + item.NAMAS,
                                        namas: item.NAMAS
                                    }))
                                };
                            },
                            cache: true
                        }
                    });

                    $('#NO_BELI').on('select2:select', function(e) {
                        var data = e.params.data;
                        var kodes = data.id;

                        // Load detail data
                        $.ajax({
                            url: "{{ url('hps_hrgBeli/get_detail_by_kodes') }}",
                            type: 'GET',
                            data: {
                                kodes: kodes
                            },
                            success: function(response) {
                                console.log('hasil brg : ', response);
                                $("#NO_BUKTI").val(response.header.NO_BUKTI == '' ? '+' : response
                                    .header.NO_BUKTI);
                                $("#NO_ID").val(response.header.NO_ID);
                                $("#KODES").val(response.header.KODES);
                                $("#NAMAS").val(response.header.NAMAS);
                                $("#KET_HEADER").val(response.header.KET);
                                $("#TGL").val(moment(response.header.TGL).format('DD-MM-YYYY'));

                                // Store original data
                                // originalDetails = response.details.slice(); // Create a copy
                                // deletedDetails = []; // Reset deleted details
                                // currentVisibleRows = [];

                                // // Clear existing table rows
                                // $('#datatable tbody tr').remove();

                                // // Add detail rows
                                // var html = '';
                                // baris = 0;
                                // idrow = 1;

                                // response.details.forEach(function(detail, index) {
                                //     html += generateDetailRow(detail, index);
                                //     currentVisibleRows.push(detail);
                                //     baris++;
                                //     idrow++;
                                // });

                                // $('#datatable tbody').html(html);

                                // // Initialize autoNumeric for new rows
                                // initializeAutoNumeric();



                                // // Show delete buttons if more than 1 row
                                // updateDeleteButtonsVisibility();

                                nomor();
                                hitung();
                            }
                        });
                    });

                    $('#NO_BELI').on('select2:unselect', function(e) {
                        $("#NO_BELI").val('');
                        $("#KODES").val('');
                        $("#NAMAS").val('');
                        $("#KET_HEADER").val('');
                        $('#datatable tbody tr').remove();
                        baris = 0;
                        idrow = 1;
                    });
                }



                //menangani tombol enter agar pindah ke bawah
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
                            // var nomer = idrow-1;
                            // console.log("KD_BRG"+nomor);
                            // document.getElementById("KD_BRG"+nomor).focus();
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

                $("#TTOTAL_QTY").autoNumeric('init', {
                    aSign: '<?php echo ''; ?>',
                    vMin: '-999999999.99'
                });


                jumlahdata = 100;
                for (i = 0; i <= jumlahdata; i++) {
                    $("#QTY" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.99'
                    });
                }


                $('body').on('click', '.btn-delete', function() {
                    // Don't allow deletion if only 1 row remains
                    if ($('#datatable tbody tr').length <= 1) {
                        alert('Minimal harus ada 1 baris data');
                        return;
                    }

                    // Get the row data before deleting
                    var $row = $(this).closest('tr');
                    var rowIndex = $row.index();

                    // Move the visible row data to deleted array
                    if (currentVisibleRows[rowIndex]) {
                        deletedDetails.push(currentVisibleRows[rowIndex]);
                        currentVisibleRows.splice(rowIndex, 1);
                    }

                    // Remove the row
                    $row.remove();
                    baris--;

                    // Update delete buttons visibility
                    updateDeleteButtonsVisibility();
                    hitung();
                    nomor();
                });

                $('.date').datepicker({
                    dateFormat: 'dd-mm-yy'
                });



                //////////////////////////////////////////////////////

                $(".KD_BRG").each(function() {
                    var getid = $(this).attr('id');
                    var noid = getid.substring(4, 11);



                    $("#KD_BRG" + noid).keypress(function(e) {
                        if (e.key === '?') {
                            e.preventDefault();
                            console.log('masuk oi');
                            browseBrg(noid);
                        }
                    });
                });

                $(document).on("keypress", ".KD_BRG", function(e) {
                    if (e.key === ".") {
                        console.log('masuk oi');
                        e.preventDefault();
                        const rowid = $(this).data("rowid");
                        browseBrg(rowid);
                    }
                });

                //////////////////////////////////////////////////////

                var dTableBBarang;
                var rowidBarang;
                loadDataBBrg = function() {

                    let kodes = $('#KODES').val();
                    $.ajax({
                        type: 'GET',
                        url: "{{ url('hps_hrgBeli/browseBrg') }}",
                        data: {
                            kodes: kodes
                        },
                        success: function(response) {
                            resp = response;
                            console.log('data : ', resp);
                            console.log(resp);
                            if (resp.length > 1) {
                                if (dTableBBarang) {
                                    dTableBBarang.clear();
                                }
                                for (i = 0; i < resp.length; i++) {

                                    dTableBBarang.row.add([
                                        '<a href="javascript:void(0);" onclick="chooseBrg(\'' +
                                        resp[i].KD_BRG + '\', \'' + resp[i].NA_BRG + '\', \'' +
                                        resp[i].HARGA + '\', \'' + resp[i].DISC + '\', \'' +
                                        resp[i].DISC2 + '\', \'' + resp[i].DISC3 + '\', \'' +
                                        resp[i].DISC4 + '\', \'' + resp[i].PPN + '\'  )">' +
                                        resp[i].KD_BRG + '</a>',
                                        resp[i].NA_BRG,
                                        resp[i].HARGA,
                                        resp[i].DISC,
                                        resp[i].DISC2,
                                        resp[i].DISC3,
                                        resp[i].DISC4,
                                        resp[i].PPN,
                                    ]);
                                }
                                dTableBBarang.draw();
                                $("#browseBarangModal").modal("show");

                            } else {
                                $("#KD_BRG" + rowidBarang).val(resp[0].KD_BRG);
                                $("#NA_BRG" + rowidBarang).val(resp[0].NA_BRG);
                                $("#HARGALAMA" + rowidBarang).val(numberWithCommas(resp[0].HARGA));
                                $("#DISKLAMA" + rowidBarang).val(numberWithCommas(resp[0].DISC));
                                $("#DISKLAMA2" + rowidBarang).val(numberWithCommas(resp[0].DISC2));
                                $("#DISKLAMA3" + rowidBarang).val(numberWithCommas(resp[0].DISC3));
                                $("#DISKLAMA4" + rowidBarang).val(numberWithCommas(resp[0].DISC4));
                                $("#PPN" + rowidBarang).val(numberWithCommas(resp[0].PPN));
                                $("#browseBarangModal").modal("hide");
                                hitung();
                            }
                        }
                    });
                }

                dTableBBarang = $("#table-bbarang").DataTable({});

                browseBrg = function(rid) {
                    console.log('masuk');
                    rowidBarang = rid;

                    loadDataBBrg();
                }

                chooseBrg = function(KD_BRG, NA_BRG, HARGA, DISK, DISK2, DISK3, DISK4, PPN) {
                    console.log('masuk');
                    $("#KD_BRG" + rowidBarang).val(KD_BRG);
                    $("#NA_BRG" + rowidBarang).val(NA_BRG);
                    $("#HARGALAMA" + rowidBarang).val(numberWithCommas(HARGA));
                    $("#DISKLAMA" + rowidBarang).val(numberWithCommas(DISK));
                    $("#DISKLAMA2" + rowidBarang).val(numberWithCommas(DISK2));
                    $("#DISKLAMA3" + rowidBarang).val(numberWithCommas(DISK3));
                    $("#DISKLAMA4" + rowidBarang).val(numberWithCommas(DISK4));
                    $("#PPN" + rowidBarang).val(numberWithCommas(PPN));
                    $("#browseBarangModal").modal("hide");

                    hitung();
                }


                /* $("#RAK0").onblur(function(e){
                	if(e.keyCode == 46){
                		e.preventDefault();
                		browseRak(0);
                	}
                });  */

                ////////////////////////////////////////////////////


            });

            // Helper function to generate detail row
            // function generateDetailRow(detail, index) {
            //     return `<tr>
    // 	<td>
    // 		<input name="REC[]" id="REC${index}" type="text" value="${detail.REC}"
    // 			class="form-control REC" readonly style="text-align:center">
    // 	</td>
    // 	<td>
    // 		<input name="KD_BRG[]" id="KD_BRG${index}" type="text" class="form-control KD_BRG"
    // 			value="${detail.KD_BRG}" readonly>
    // 	</td>
    // 	<td>
    // 		<input name="NA_BRG[]" id="NA_BRG${index}" type="text" class="form-control NA_BRG"
    // 			value="${detail.NA_BRG}" readonly>
    // 	</td>

    // 	<td>
    // 		<input name="QTY[]" id="QTY${index}" type="text" style="text-align: right"
    // 			class="form-control QTY text-primary" value="${detail.QTY}" readonly>
    // 	</td>
    // 	<td>
    // 		<input name="HARGALAMA[]" id="HARGALAMA${index}" type="text" style="text-align: right"
    // 			class="form-control HARGALAMA" value="${detail.HARGALAMA || 0}" readonly>
    // 	</td>
    // 	<td>
    // 		<input name="DISKLAMA[]" id="DISKLAMA${index}" type="text" style="text-align: right"
    // 			class="form-control DISKLAMA" value="${detail.DISKLAMA || 0}" readonly>
    // 	</td>
    // 	<td>
    // 		<input name="DISKLAMA2[]" id="DISKLAMA2${index}" type="text" style="text-align: right"
    // 			class="form-control DISKLAMA2" value="${detail.DISKLAMA2 || 0}" readonly>
    // 	</td>
    // 	<td>
    // 		<input name="DISKLAMA3[]" id="DISKLAMA3${index}" type="text" style="text-align: right"
    // 			class="form-control DISKLAMA3" value="${detail.DISKLAMA3 || 0}" readonly>
    // 	</td>
    // 	<td>
    // 		<input name="DISKLAMA4[]" id="DISKLAMA4${index}" type="text" style="text-align: right"
    // 			class="form-control DISKLAMA4" value="${detail.DISKLAMA4 || 0}" readonly>
    // 	</td>
    // 	<td>
    // 		<input name="HARGA[]" id="HARGA${index}" type="text" style="text-align: right"
    // 			class="form-control HARGA" value="${detail.HARGA || 0}" readonly>
    // 	</td>
    // 	<td>
    // 		<input name="DISK[]" id="DISK${index}" type="text" style="text-align: right"
    // 			class="form-control DISK" value="${detail.DISK || 0}" readonly>
    // 	</td>
    // 	<td>
    // 		<input name="DISK2[]" id="DISK2${index}" type="text" style="text-align: right"
    // 			class="form-control DISK2" value="${detail.DISK2 || 0}" readonly>
    // 	</td>
    // 	<td>
    // 		<input name="DISK3[]" id="DISK3${index}" type="text" style="text-align: right"
    // 			class="form-control DISK3" value="${detail.DISK3 || 0}" readonly>
    // 	</td>
    // 	<td>
    // 		<input name="DISK4[]" id="DISK4${index}" type="text" style="text-align: right"
    // 			class="form-control DISK4" value="${detail.DISK4 || 0}" readonly>
    // 	</td>
    // 	<td>
    // 		<input name="KET[]" id="KET${index}" type="text" class="form-control KET"
    // 			value="${detail.KET || ''}" required>
    // 	</td>
    // 	<td>
    // 		<button type='button' id='DELETEX${index}' class='btn btn-sm btn-circle btn-outline-danger btn-delete'
    // 			onclick='' style='display:none;'> <i class='fa fa-fw fa-trash'></i> </button>
    // 	</td>
    // </tr>`;

            // }
            function generateDetailRow(detail, index) {
                return `
						<!-- ================= BARIS LAMA ================= -->
						<tr>
							<td rowspan="2">
								<input name="REC[]" id="REC${index}" type="text" value="${detail.REC}"
									class="form-control REC" readonly style="text-align:center">
							</td>

							<td rowspan="2">
								<input name="KD_BRG[]" id="KD_BRG${index}" data-rowid=${index} type="text"
									class="form-control KD_BRG" value="${detail.KD_BRG}" >
							</td>

							<td rowspan="2">
								<input name="NA_BRG[]" id="NA_BRG${index}" type="text"
									class="form-control NA_BRG" value="${detail.NA_BRG}" readonly>
							</td>

							<!-- <td rowspan="2">
								<input name="QTY[]" id="QTY${index}" type="text"
									style="text-align: right"
									class="form-control QTY text-primary"
									value="${detail.QTY}" readonly>
							</td> -->

							<!-- STATUS -->
							<td class="text-center fw-bold text-danger">LAMA</td>

							<td>
								<input name="HARGALAMA[]" id="HARGALAMA${index}" type="text"
									style="text-align: right"
									class="form-control HARGALAMA"
									value="${detail.HARGA || 0}" readonly>
							</td>

							<td>
								<input name="DISKLAMA[]" id="DISKLAMA${index}" type="text"
									style="text-align: right"
									class="form-control DISKLAMA"
									value="${detail.DISC || 0}" readonly>
							</td>

							<td>
								<input name="DISKLAMA2[]" id="DISKLAMA2${index}" type="text"
									style="text-align: right"
									class="form-control DISKLAMA2"
									value="${detail.DISC2 || 0}" readonly>
							</td>

							<td>
								<input name="DISKLAMA3[]" id="DISKLAMA3${index}" type="text"
									style="text-align: right"
									class="form-control DISKLAMA3"
									value="${detail.DISC3 || 0}" readonly>
							</td>

							<td>
								<input name="DISKLAMA4[]" id="DISKLAMA4${index}" type="text"
									style="text-align: right"
									class="form-control DISKLAMA4"
									value="${detail.DISC4 || 0}" readonly>
							</td>

							<!-- <td rowspan="2">
								<input name="KET[]" id="KET${index}" type="text"
									class="form-control KET"
									value="${detail.KET || ''}" required>
							</td> -->

							<td rowspan="2">
								<button type="button" id="DELETEX${index}"
									class="btn btn-sm btn-circle btn-outline-danger btn-delete"
									onclick="" style="display:none;">
									<i class="fa fa-fw fa-trash"></i>
								</button>
							</td>
						</tr>

						<!-- ================= BARIS BARU ================= -->
						<tr>
							<!-- STATUS -->
							<td class="text-center fw-bold text-success">BARU</td>

							<td>
								<input name="HARGA[]" id="HARGA${index}" type="text"
									style="text-align: right"
									class="form-control HARGA"
									value="${detail.HARGA || 0}" >
							</td>

							<td>
								<input name="DISK[]" id="DISK${index}" type="text"
									style="text-align: right"
									class="form-control DISK"
									value="${detail.DISC || 0}" >
							</td>

							<td>
								<input name="DISK2[]" id="DISK2${index}" type="text"
									style="text-align: right"
									class="form-control DISK2"
									value="${detail.DISC2 || 0}" >
							</td>

							<td>
								<input name="DISK3[]" id="DISK3${index}" type="text"
									style="text-align: right"
									class="form-control DISK3"
									value="${detail.DISC3 || 0}" >
							</td>

							<td>
								<input name="DISK4[]" id="DISK4${index}" type="text"
									style="text-align: right"
									class="form-control DISK4"
									value="${detail.DISC4 || 0}" >
							</td>

							<!-- <td>
								<input name="KET[]" type="text"
									class="form-control KET"
									value="${detail.KET || ''}" required>
							</td> -->

							<td>
								<button type="button"
									class="btn btn-sm btn-circle btn-outline-danger btn-delete">
									<i class="fa fa-fw fa-trash"></i>
								</button>
							</td>

						</tr>
						`;

                $("#KD_BRG" + idrow).keypress(function(e) {
                    if (e.keyCode == 46) {
                        e.preventDefault();
                        browseBrg(eval($(this).data("rowid")));
                    }
                });


                idrow++;
                baris++;
                nomor();

                $(".ronly").on('keydown paste', function(e) {
                    e.preventDefault();
                    e.currentTarget.blur();
                });

            }


            // Helper function to initialize autoNumeric
            function initializeAutoNumeric() {
                for (let i = 0; i <= 100; i++) {
                    // Initialize QTY fields
                    if ($("#QTY" + i).length) {
                        $("#QTY" + i).autoNumeric('init', {
                            aSign: '<?php echo ''; ?>',
                            vMin: '-999999999.99'
                        });
                    }
                    // Initialize HARGA fields
                    if ($("#HARGA" + i).length) {
                        $("#HARGA" + i).autoNumeric('init', {
                            aSign: '<?php echo ''; ?>',
                            vMin: '0.00'
                        });
                    }
                    if ($("#HARGALAMA" + i).length) {
                        $("#HARGALAMA" + i).autoNumeric('init', {
                            aSign: '<?php echo ''; ?>',
                            vMin: '0.00'
                        });
                    }
                    // Initialize DISK fields
                    if ($("#DISK" + i).length) {
                        $("#DISK" + i).autoNumeric('init', {
                            aSign: '<?php echo ''; ?>',
                            vMin: '0.00'
                        });
                    }
                    if ($("#DISKLAMA" + i).length) {
                        $("#DISKLAMA" + i).autoNumeric('init', {
                            aSign: '<?php echo ''; ?>',
                            vMin: '0.00'
                        });
                    }
                    if ($("#DISK2" + i).length) {
                        $("#DISK2" + i).autoNumeric('init', {
                            aSign: '<?php echo ''; ?>',
                            vMin: '0.00'
                        });
                    }
                    if ($("#DISKLAMA2" + i).length) {
                        $("#DISKLAMA2" + i).autoNumeric('init', {
                            aSign: '<?php echo ''; ?>',
                            vMin: '0.00'
                        });
                    }
                    if ($("#DISK3" + i).length) {
                        $("#DISK3" + i).autoNumeric('init', {
                            aSign: '<?php echo ''; ?>',
                            vMin: '0.00'
                        });
                    }
                    if ($("#DISKLAMA3" + i).length) {
                        $("#DISKLAMA3" + i).autoNumeric('init', {
                            aSign: '<?php echo ''; ?>',
                            vMin: '0.00'
                        });
                    }
                    if ($("#DISK4" + i).length) {
                        $("#DISK4" + i).autoNumeric('init', {
                            aSign: '<?php echo ''; ?>',
                            vMin: '0.00'
                        });
                    }
                    if ($("#DISKLAMA4" + i).length) {
                        $("#DISKLAMA4" + i).autoNumeric('init', {
                            aSign: '<?php echo ''; ?>',
                            vMin: '0.00'
                        });
                    }
                }
            }

            // Helper function to make fields readonly except KET
            function makeFieldsReadonly() {
                var tipx = $('#tipx').val();

                $('.KD_BRG, .NA_BRG, .HARGALAMA, .DISKLAMA, .DISKLAMA2, .DISKLAMA3, .DISKLAMA4, .HARGA, .DISK, .DISK2, .DISK3, .DISK4')
                    .attr('readonly', true);

                // QTY always readonly for edit mode
                if (tipx == 'edit') {
                    $('.QTY').attr('readonly', true);
                    $('.btn-delete').hide();
                    $('#PLUSX').show();
                } else {
                    $('.QTY').attr('readonly', true);
                    $('.btn-delete').hide();
                    $('#PLUSX').show(); // Show the plus button for adding deleted data back in new mode
                }
            }

            // Function to update delete buttons visibility
            function updateDeleteButtonsVisibility() {
                var totalRows = $('#datatable tbody tr').length;
                if (totalRows <= 1) {
                    $('.btn-delete').hide();
                } else {
                    $('.btn-delete').show();
                }
            }

            // Function to add back deleted data
            function addDeletedData() {
                if (deletedDetails.length === 0) {
                    alert('Tidak ada data yang bisa ditambahkan kembali');
                    return;
                }

                // Show modal with deleted items to select
                showDeletedDataModal();
            }

            ///////////////////////////////////////




            function cekDetail() {
                var cekBarang = '';
                var cekNilai = '';

                $(".KD_BRG").each(function() {
                    let z = $(this).closest('tr');
                    var KD_BRGX = z.find('.KD_BRG').val();
                    var HARGA = parseFloat(z.find('.HARGA').val()) || 0;
                    var DISC = parseFloat(z.find('.DISC').val()) || 0;
                    var DISC2 = parseFloat(z.find('.DISC2').val()) || 0;
                    var DISC3 = parseFloat(z.find('.DISC3').val()) || 0;
                    var DISC4 = parseFloat(z.find('.DISC4').val()) || 0;
                    var PPN = parseFloat(z.find('.PPN').val()) || 0;

                    if (KD_BRGX == "") {
                        cekBarang = '1';
                    }

                    // Cek jika salah satu kolom bernilai lebih dari 0
                    if (HARGA > 0 || DISC > 0 || DISC2 > 0 || DISC3 > 0 || DISC4 > 0 || PPN > 0) {
                        cekNilai = '1';
                    }
                });

                return {
                    barang: cekBarang,
                    nilai: cekNilai
                };
            }


            function simpan() {
                // hitung();

                // var tgl = $('#TGL').val();
                // var bulanPer = <?php echo session()->get('periode')['bulan']; ?>;
                // var tahunPer = <?php echo session()->get('periode')['tahun']; ?>;
                var check = '0';

                if ($('#NO_BELI').val() == '') {
                    check = '1';
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Kode Supplier# Harus Diisi.'
                    });
                    return;
                }

                if (baris == 0) {
                    check = '1';
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Data detail kosong (Tambahkan 1 baris kosong jika ingin mengosongi detail)'
                    });
                    return;
                }

                // Check KET detail
                var detailCheck = cekDetail();
                if (detailCheck.nilai == '1') {
                    check = '1';
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Periksa kembali Harga, Disc, dan PPN tidak boleh > 0!'
                    });
                    return;
                }

                // if (tgl.substring(3, 5) != bulanPer) {
                //     check = '1';
                //     Swal.fire({
                //         icon: 'warning',
                //         title: 'Warning',
                //         text: 'Bulan tidak sama dengan Periode'
                //     });
                //     return;
                // }

                // if (tgl.substring(tgl.length - 4) != tahunPer) {
                //     check = '1';
                //     Swal.fire({
                //         icon: 'warning',
                //         title: 'Warning',
                //         text: 'Tahun tidak sama dengan Periode'
                //     });
                //     return;
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
                    var QTYX = parseFloat(z.find('.QTY').val().replace(/,/g, ''));

                    TTOTAL_QTY += QTYX;

                });


                if (isNaN(TTOTAL_QTY)) TTOTAL_QTY = 0;

                $('#TTOTAL_QTY').val(numberWithCommas(TTOTAL_QTY));
                $("#TTOTAL_QTY").autoNumeric('update');

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

                $tipx = $('#tipx').val();

                // Show PLUSX only for new mode, hide for edit
                if ($tipx == 'new') {
                    $("#PLUSX").attr("hidden", false);
                } else {
                    $("#PLUSX").attr("hidden", false);
                }

                $("#NO_BUKTI").attr("readonly", true);
                $("#TGL").attr("readonly", false);
                $("#NOTES").attr("readonly", false);
                $("#TTOTAL_QTY").attr("readonly", true);

                $("#NA_BRG").attr("readonly", true);
                $("#NA_BRG").attr("disabled", true);
                $("#ALAMAT").attr("readonly", true);
                $("#KOTA").attr("readonly", true);
                $("#PKP").attr("disabled", true);


                jumlahdata = 100;
                for (i = 0; i <= jumlahdata; i++) {
                    $("#REC" + i.toString()).attr("readonly", true);
                    $("#NA_BRG" + i.toString()).attr("readonly", true);

                    // QTY readonly for edit mode, editable for new mode
                    if ($tipx == 'edit') {
                        $("#QTY" + i.toString()).attr("readonly", true);
                    } else {
                        $("#QTY" + i.toString()).attr("readonly", false);
                    }

                    $("#KET" + i.toString()).attr("readonly", false);

                    // Hide delete buttons for edit mode
                    if ($tipx == 'edit') {
                        $("#DELETEX" + i.toString()).attr("hidden", true);
                    } else {
                        $("#DELETEX" + i.toString()).attr("hidden", true);
                    }

                    $posted = $('#POSTED').val();

                    if ($posted == '1') {
                        $("#REC" + i.toString()).attr("readonly", true);
                        $("#KD_BRG" + i.toString()).attr("readonly", false);
                        $("#NA_BRG" + i.toString()).attr("readonly", true);
                        $("#QTY" + i.toString()).attr("readonly", true);
                        $("#KET" + i.toString()).attr("readonly", true);
                        $("#DELETEX" + i.toString()).attr("hidden", true);
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
                $("#TTOTAL_QTY").attr("readonly", true);


                $("#NAMAC").attr("readonly", true);
                $("#NAMAC").attr("disabled", true);

                $("#ALAMAT").attr("readonly", true);
                $("#KOTA").attr("readonly", true);
                $("#PKP").attr("disabled", true);

                jumlahdata = 100;
                for (i = 0; i <= jumlahdata; i++) {
                    $("#REC" + i.toString()).attr("readonly", true);
                    $("#KD_BRG" + i.toString()).attr("readonly", false);
                    $("#NA_BRG" + i.toString()).attr("readonly", true);
                    $("#QTY" + i.toString()).attr("readonly", true);
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
                            var no_id = $('#NO_ID').val() || '0';
                            var loc = "{{ url('/ubbrgdw/delete/') }}/" + no_id;

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
                        loc = "{{ url('/ubbrgdw') }}";
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





            // function tambah() {
            //     if ($('#NO_BELI').val() && deletedDetails.length > 0) {
            //         showDeletedDataModal();
            //         return;
            //     }



            //     var html = '';

            //     jumlahdata = 100;
            //     for (i = 0; i <= jumlahdata; i++) {
            //         $("#QTY" + i.toString()).autoNumeric('init', {
            //             aSign: '<?php echo ''; ?>',
            //             vMin: '-999999999.99'
            //         });
            //     }

            //     idrow++;
            //     baris++;
            //     nomor();

            //     $(".ronly").on('keydown paste', function(e) {
            //         e.preventDefault();
            //         e.currentTarget.blur();
            //     });
            // }

            function tambah() {
                // Siapkan objek kosong untuk baris baru
                var detailBaru = {
                    KD_BRG: '',
                    NA_BRG: '',
                    SATUAN: '',
                    QTY: 0,
                    HARGA: 0,
                    TOTAL: 0,
                    KET: ''
                };

                // Ambil baris saat ini (pastikan baris & idrow dideklarasikan secara global di atas)
                var index = $('#datatable tbody tr').length;

                // Generate HTML row baru
                var html = generateDetailRow(detailBaru, index);

                // Tambahkan ke tabel
                $('#datatable tbody').append(html);

                // Re-inisialisasi plugins untuk baris baru
                initializeAutoNumeric();
                nomor();
                updateDeleteButtonsVisibility();
            }

            // Function to show modal with deleted data
            function showDeletedDataModal() {
                var tbody = $('#deletedDataTableBody');
                tbody.empty();

                deletedDetails.forEach(function(detail, index) {
                    var row = `
					<tr>
						<td><input type="checkbox" class="deleted-item-checkbox" value="${index}"></td>
						<td>${detail.KD_BRG}</td>
						<td>${detail.NA_BRG}</td>
						<td>${detail.QTY}</td>
					</tr>
				`;
                    tbody.append(row);
                });

                $('#deletedDataModal').modal('show');
            }

            // Function to add selected deleted data back
            function addSelectedData() {
                var selectedIndexes = [];
                $('.deleted-item-checkbox:checked').each(function() {
                    selectedIndexes.push(parseInt($(this).val()));
                });

                if (selectedIndexes.length === 0) {
                    alert('Pilih minimal 1 data untuk ditambahkan');
                    return;
                }

                // Add selected items back to visible rows
                var html = '';
                var newCurrentRows = currentVisibleRows.slice(); // Copy current visible rows

                selectedIndexes.forEach(function(index) {
                    var detail = deletedDetails[index];
                    newCurrentRows.push(detail);
                    html += generateDetailRow(detail, baris);
                    baris++;
                    idrow++;
                });

                // Remove selected items from deleted array (reverse order to maintain indexes)
                selectedIndexes.sort((a, b) => b - a);
                selectedIndexes.forEach(function(index) {
                    deletedDetails.splice(index, 1);
                });

                // Update current visible rows
                currentVisibleRows = newCurrentRows;

                // Append new rows to table
                $('#datatable tbody').append(html);

                // Initialize autoNumeric for new rows
                initializeAutoNumeric();

                // Update delete buttons visibility
                updateDeleteButtonsVisibility();

                // Close modal
                $('#deletedDataModal').modal('hide');

                nomor();
                hitung();
            }
        </script>
        <!--
                <script src="autonumeric.min.js" type="text/javascript"></script>
                <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
                <script src="https://unpkg.com/autonumeric"></script> -->
    @endsection
