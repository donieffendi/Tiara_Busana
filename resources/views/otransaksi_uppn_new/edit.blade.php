@extends('layouts.plain')

<style>
    .card {}

    .form-control:focus {
        background-color: #b5e5f9 !important;
    }

    .scrollable table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
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

                                <form
                                    action="{{ $tipx == 'new' ? url('/uppn-new/store?flagz=' . $flagz . '') : url('/uppn-new/update/' . $header->NO_ID . '&flagz=' . $flagz . '') }}"
                                    method="POST" name ="entri" id="entri">

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

                                        <div class="form-group row">

                                            <!-- NO_BUKTI -->
                                            <div class="col-md-2">
                                                <div class="form-group special-input-label">
                                                    <input type="text" class="form-control NO_BUKTI" id="NO_BUKTI"
                                                        name="NO_BUKTI" placeholder=" " value="{{ $header->NO_BUKTI }}"
                                                        readonly>
                                                    <label for="NO_BUKTI">Bukti#</label>
                                                </div>
                                            </div>

                                            <!-- TGL -->
                                            <div class="col-md-2">
                                                <div class="form-group special-input-label">
                                                    <input type="text" class="form-control date" id="TGL"
                                                        name="TGL" data-date-format="dd-mm-yyyy" placeholder=" "
                                                        autocomplete="off"
                                                        value="{{ date('d-m-Y', strtotime($header->TGL)) }}">
                                                    <label for="TGL">Tanggal</label>
                                                </div>
                                            </div>

                                            <!-- JTEMPO -->
                                            <div class="col-md-2">
                                                <div class="form-group special-input-label">
                                                    <input type="text" class="form-control date" id="JTEMPO"
                                                        name="JTEMPO" data-date-format="dd-mm-yyyy" placeholder=" "
                                                        autocomplete="off"
                                                        value="{{ $header->JTEMPO ? date('d-m-Y', strtotime($header->JTEMPO)) : date('d-m-Y', strtotime('+7 days')) }}">
                                                    <label for="JTEMPO">Jatuh Tempo</label>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- Hidden Inputs -->
                                        <input type="hidden" class="form-control NO_ID" id="NO_ID" name="NO_ID"
                                            value="{{ $header->NO_ID ?? '' }}" readonly>
                                        <input type="hidden" name="tipx" class="form-control tipx" id="tipx"
                                            value="{{ $tipx }}">
                                        <input type="hidden" name="flagz" class="form-control flagz" id="flagz"
                                            value="{{ $flagz }}">


                                        <div class="form-group row">

                                            <!-- NO SP -->
                                            <div class="col-md-2">
                                                <div class="form-group special-input-label">

                                                    <input type="text" class="form-control NO_PO" id="NO_PO"
                                                        name="NO_PO" placeholder=" " value="{{ $header->NO_PO }}"
                                                        style="text-align:left" required>

                                                    <label for="NO_PO">
                                                        <span style="color:red">*</span> No. Nota
                                                    </label>

                                                </div>
                                            </div>

                                            <!-- GOLONGAN -->
                                            <div class="col-md-1">
                                                <div class="form-group special-input-label">
                                                    <input type="text" class="form-control GOLONGAN" id="GOLONGAN"
                                                        name="GOLONGAN" placeholder=" " value="{{ $header->GOLONGAN }}"
                                                        readonly>
                                                    <label for="GOLONGAN">Golongan</label>
                                                </div>
                                            </div>

                                            <!-- TYPE -->
                                            <div class="col-md-1">
                                                <div class="form-group special-input-label">
                                                    <input type="text" class="form-control TYPE" id="TYPE"
                                                        name="TYPE" placeholder=" " value="{{ $header->TYPE }}" readonly>
                                                    <label for="TYPE">Type</label>
                                                </div>
                                            </div>
											 <!-- CBG -->
											<div class="col-md-1">
												<div class="form-group special-input-label">
													<input type="text" class="form-control CBG" id="CBG"
														name="CBG" placeholder=" " >
													<label for="CBG">CBG</label>
												</div>
											</div>

											<!-- CBG DARI -->
											<div class="col-md-2">
												<div class="form-group special-input-label">
													<input type="text" class="form-control CBG_DARI" id="CBG_DARI"
														name="CBG_DARI" placeholder=" " >
													<label for="CBG_DARI">CBG Dari</label>
												</div>
											</div>

                                            <!-- HIDDEN -->
                                            <input type="hidden" class="form-control disc_ps" id="disc_ps"
                                                name="disc_ps">
                                            <input type="hidden" class="form-control nppn" id="nppn"
                                                name="nppn" value="{{ $header->ppn }}">

                                        </div>


                                        <div class="form-group row">

                                            <!-- <div class="col-md-1"></div> -->

                                            <!-- KODE SUPPLIER -->
                                            <div class="col-md-2">
                                                <div class="form-group special-input-label">
                                                    <input type="text" class="form-control KODES" id="KODES"
                                                        name="KODES" placeholder=" " value="{{ $header->KODES }}"
                                                        readonly>
                                                    <label for="KODES">Kode Supplier</label>
                                                </div>
                                            </div>

                                            <!-- NAMA SUPPLIER -->
                                            <div class="col-md-3">
                                                <div class="form-group special-input-label">
                                                    <input type="text" class="form-control NAMAS" id="NAMAS"
                                                        name="NAMAS" placeholder=" " value="{{ $header->NAMAS }}"
                                                        readonly>
                                                    <label for="NAMAS">Nama Supplier</label>
                                                </div>
                                            </div>

                                            <!-- HARI -->
                                            <div class="col-md-1">
                                                <div class="form-group special-input-label">
                                                    <input type="text" class="form-control HARI" id="HARI"
                                                        name="HARI" placeholder=" " value="{{ $header->HARI }}"
                                                        readonly>
                                                    <label for="HARI">Hari</label>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="form-group row">
                                            <!-- <div class="col-md-2"></div> -->

                                            <div class="col-md-4">
                                                <div class="form-group special-input-label">
                                                    <input type="text" class="form-control alamat" id="alamat"
                                                        name="alamat" placeholder=" " value="{{ $header->alamat }}"
                                                        readonly>
                                                    <label for="alamat">Alamat</label>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <!-- <div class="col-md-2"></div> -->

                                            <div class="col-md-4">
                                                <div class="form-group special-input-label">
                                                    <input type="text" class="form-control kota" id="kota"
                                                        name="kota" placeholder=" " value="{{ $header->kota }}"
                                                        readonly>
                                                    <label for="kota">Kota</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">

                                            <div class="col-md-4">
                                                <!-- <div class="col-md-5 offset-md-1"> -->
                                                <div class="form-group special-input-label">
                                                    <input type="text" class="form-control NO_PJK" id="NO_PJK"
                                                        name="NO_PJK" placeholder=" " value="{{ $header->NO_PJK }}">
                                                    <label for="NO_PJK">Faktur Pajak</label>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="form-group row">
                                            <!-- code text box baru -->
                                            <div class="col-md-4 form-group row special-input-label">

                                                <input type="text" class="notes" id="notes" name="notes"
                                                    value="{{ $header->notes }}" placeholder=" ">
                                                <label for="notes">Notes</label>
                                            </div>
                                            <!-- tutupannya -->

                                        </div>

                                        <!-- loader tampil di modal  -->
                                        <div class="loader" style="z-index: 1055;" id='LOADX'></div>


                                        <div class="scrollable" style="height:400px; overflow-y:scroll;">

                                            <table id="datatable" class="table table-striped table-border"
                                                style="min-width: 1000px">
                                                <thead>
                                                    <tr>
                                                        <th width="50px" style="text-align: center;">No.</th>
                                                        <th width="150px" style="text-align: center; width: 100px">
                                                            <label style="color:red;font-size:20px">* </label>
                                                            <label for="KD_BRG" class="form-label">Kode</label>
                                                        </th>
                                                        <th width="300px" style="text-align: center; width: 200px">Uraian
                                                        </th>
                                                        <th width="100px" style="text-align: center;">Pesan</th>
                                                        <th width="100px" style="text-align: center;">Terima</th>
                                                        {{-- <th width="100px" style="text-align: center;">Kemasan</th> --}}
                                                        <th width="150px" style="text-align: center;">Harga</th>
                                                        {{-- <th width="100px" style="text-align: center;">DIS 1</th>
                                                        <th width="100px" style="text-align: center;">DIS 2</th>
                                                        <th width="100px" style="text-align: center;">DIS 3</th>
                                                        <th width="100px" style="text-align: center;">DIS 4</th> --}}
                                                        <th width="150px" style="text-align: center;">Total</th>
                                                        <th width="300px" style="text-align: center;">Ket</th>
                                                        <th width="150px" style="text-align: center;">Harga</th>
                                                        <th width="100px" style="text-align: center;">qty</th>
                                                        {{-- <th width="100px" style="text-align: center;">Kode Laku</th>
                                                        <th width="150px" style="text-align: center;">Tanggal Expired
                                                        </th> --}}

                                                        <th></th>

                                                    </tr>
                                                <tbody id="detailPod">

                                                <tbody>
                                                    <?php $no = 0; ?>
                                                    @foreach ($detail as $detail)
                                                        <tr>
                                                            <td>
                                                                <input type="hidden" name="NO_ID[]{{ $no }}"
                                                                    id="NO_ID" type="text"
                                                                    value="{{ $detail->NO_ID }}"
                                                                    class="form-control NO_ID"
                                                                    onkeypress="return tabE(this,event)" readonly>

                                                                <input name="REC[]" id="REC{{ $no }}"
                                                                    type="text" value="{{ $detail->REC }}"
                                                                    class="form-control REC"
                                                                    onkeypress="return tabE(this,event)" readonly
                                                                    style="text-align:center">
                                                            </td>


                                                            <td>
                                                                <input name="KD_BRG[]" id="KD_BRG{{ $no }}"
                                                                    type="text" class="form-control KD_BRG "
                                                                    value="{{ $detail->KD_BRG }}"
                                                                    onblur="browseBarang({{ $no }})">
                                                            </td>

                                                            <td>
                                                                <input name="NA_BRG[]" id="NA_BRG{{ $no }}"
                                                                    type="text" class="form-control NA_BRG "
                                                                    value="{{ $detail->NA_BRG }}" readonly>
                                                            </td>

                                                            {{-- <td>
                                                                <input name="SISA[]" id="SISA{{ $no }}"
                                                                    type="text" value="{{ $detail->QTY_PO }}"
                                                                    class="form-control SISA" readonly required>
                                                            </td> --}}

                                                            <td>
                                                                <input name="qtyk[]" onclick="select()"
                                                                    onblur="hitung()" value="{{ $detail->QTY }}"
                                                                    id="qtyk{{ $no }}" type="text"
                                                                    style="text-align: right"
                                                                    class="form-control qtyk text-primary">
                                                            </td>
                                                            <td>
                                                                <input name="qty[]" onblur="hitung()"
                                                                    value="{{ $detail->QTY }}"
                                                                    id="qty{{ $no }}" type="text"
                                                                    style="text-align: right" class="form-control qty"
                                                                    readonly>
                                                            </td>

                                                            <td>
                                                                <input name="hargak[]" onclick="select()"
                                                                    onblur="hitung()" value="{{ $detail->HARGA }}"
                                                                    id="hargak{{ $no }}" type="text"
                                                                    style="text-align: right" class="form-control hargak">
                                                            </td>

                                                            {{-- <td>
                                                                <input name="DISKON1[]" onclick="select()"
                                                                    onblur="hitung()" value="{{ $detail->DISK }}"
                                                                    id="DISKON1{{ $no }}" type="text"
                                                                    style="text-align: right"
                                                                    class="form-control DISKON1">
                                                            </td>

                                                            <td>
                                                                <input name="DISKON2[]" onclick="select()"
                                                                    onblur="hitung()" value="{{ $detail->DISK2 }}"
                                                                    id="DISKON2{{ $no }}" type="text"
                                                                    style="text-align: right"
                                                                    class="form-control DISKON2">
                                                            </td>

                                                            <td>
                                                                <input name="DISKON3[]" onclick="select()"
                                                                    onblur="hitung()" value="{{ $detail->DISK3 }}"
                                                                    id="DISKON3{{ $no }}" type="text"
                                                                    style="text-align: right"
                                                                    class="form-control DISKON3">
                                                            </td>

                                                            <td>
                                                                <input name="DISKON4[]" onclick="select()"
                                                                    onblur="hitung()" value="{{ $detail->DISK4 }}"
                                                                    id="DISKON4{{ $no }}" type="text"
                                                                    style="text-align: right"
                                                                    class="form-control DISKON4">
                                                            </td> --}}

                                                            <td>
                                                                <input name="total[]" onblur="hitung()"
                                                                    value="{{ $detail->TOTAL }}"
                                                                    id="total{{ $no }}" type="text"
                                                                    style="text-align: right" class="form-control total"
                                                                    readonly>
                                                            </td>

                                                            <td>
                                                                <input name="ket[]" id="ket{{ $no }}"
                                                                    type="text" class="form-control ket"
                                                                    value="{{ $detail->KET }}" required>
                                                            </td>

                                                            <td>
                                                                <input name="harga[]" onblur="hitung()"
                                                                    value="{{ $detail->HARGA }}"
                                                                    id="harga{{ $no }}" type="text"
                                                                    style="text-align: right" class="form-control harga"
                                                                    readonly>
                                                            </td>

                                                            {{-- <td>
                                                                <input name="kdlaku[]" id="kdlaku{{ $no }}"
                                                                    type="text" value="{{ $detail->kdlaku }}"
                                                                    class="form-control kdlaku" readonly>
                                                            </td> --}}

                                                            {{-- <td>
                                                                <input name="TGL_EXP[]"
                                                                    value="{{ \Carbon\Carbon::parse($detail->TGL_EXP)->format('d-m-Y') }}"
                                                                    id="TGL_EXP{{ $no }}" type="text"
                                                                    style="text-align: right"
                                                                    class="form-control date TGL_EXP">
                                                            </td> --}}


                                                            <td>
                                                                <button type='button' id='DELETEX{{ $no }}'
                                                                    class='btn btn-sm btn-circle btn-outline-danger btn-delete'
                                                                    onclick=''> <i class='fa fa-fw fa-trash'></i>
                                                                </button>
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
                                                    <!-- <td></td>
                                                <td></td> -->
                                                    <!-- <td><input class="form-control TTOTAL_QTY  text-primary font-weight-bold" style="text-align: right"  id="TTOTAL_QTY" name="TTOTAL_QTY" value="{{ $header->TOTAL_QTY }}" readonly></td> -->
                                                    <td></td>
                                                    <td></td>
                                                </tfoot>
                                            </table>

                                            <!-- <div class="col-md-2 row">
                                           <a type="button" id='PLUSX' onclick="tambah()" class="fas fa-plus fa-sm md-3" style="font-size: 20px" ></a>

                   </div>		 -->

                                        </div>

                                        <hr style="margin-top: 30px; margin-buttom: 30px">

                                        <div class="tab-content mt-6">

                                            <div class="form-group row">
                                                <div class="col-md-4" align="right">
                                                    <label for="TTOTAL" class="form-label">Total Qty</label>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" onclick="select()" onblur="hitung()"
                                                        class="form-control TTOTAL_QTY" id="TTOTAL_QTY" name="TTOTAL_QTY"
                                                        placeholder="TTOTAL_QTY" value="{{ $header->TOTAL_QTY }}"
                                                        style="text-align: right" readonly>
                                                </div>

                                                <div class="col-md-2" align="right">
                                                    <label for="BRUTO" class="form-label">Bruto</label>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" onclick="select()" onblur="hitung()"
                                                        class="form-control BRUTO" id="BRUTO" name="BRUTO"
                                                        placeholder="BRUTO" value="{{ $header->BRUTO }}"
                                                        style="text-align: right" readonly>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-md-8" align="right">
                                                    <label for="PROM" class="form-label">Promosi</label>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" onclick="select()" onblur="hitung()"
                                                        class="form-control PROM" id="PROM" name="PROM"
                                                        placeholder="PROM" value="{{ $header->PROM }}"
                                                        style="text-align: right" readonly>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-md-8" align="right">
                                                    <label for="DPP" class="form-label">DPP</label>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" onclick="select()" onblur="hitung()"
                                                        class="form-control DPP" id="DPP" name="DPP"
                                                        placeholder="DPP" value="{{ $header->DPP }}"
                                                        style="text-align: right" readonly>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-md-8" align="right">
                                                    <label for="TPPN" class="form-label">Ppn</label>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" onclick="select()" onblur="hitung()"
                                                        class="form-control TPPN" id="TPPN" name="TPPN"
                                                        placeholder="TPPN" value="{{ $header->ppn }}"
                                                        style="text-align: right" readonly>
                                                </div>
                                            </div>


                                            <div class="form-group row">
                                                <div class="col-md-8" align="right">
                                                    <label for="NETT" class="form-label">Nett</label>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" onclick="select()" onblur="hitung()"
                                                        class="form-control NETT" id="NETT" name="NETT"
                                                        placeholder="NETT" value="{{ $header->NETT }}"
                                                        style="text-align: right" readonly>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="mt-3 col-md-12 form-group row">
                                            <div class="col-md-4">
                                                <button hidden type="button" id='TOPX'
                                                    onclick="location.href='{{ url('/uppn-new/edit/?idx=' . $idx . '&tipx=top&flagz=' . $flagz . '') }}'"
                                                    class="btn btn-outline-primary">Top</button>
                                                <button hidden type="button" id='PREVX'
                                                    onclick="location.href='{{ url('/uppn-new/edit/?idx=' . $header->NO_ID . '&tipx=prev&flagz=' . $flagz . '&buktix=' . $header->NO_BUKTI) }}'"
                                                    class="btn btn-outline-primary">Prev</button>
                                                <button hidden type="button" id='NEXTX'
                                                    onclick="location.href='{{ url('/uppn-new/edit/?idx=' . $header->NO_ID . '&tipx=next&flagz=' . $flagz . '&buktix=' . $header->NO_BUKTI) }}'"
                                                    class="btn btn-outline-primary">Next</button>
                                                <button hidden type="button" id='BOTTOMX'
                                                    onclick="location.href='{{ url('/uppn-new/edit/?idx=' . $idx . '&tipx=bottom&flagz=' . $flagz . '') }}'"
                                                    class="btn btn-outline-primary">Bottom</button>
                                            </div>
                                            <div class="col-md-5">
                                                <button hidden type="button" id='NEWX'
                                                    onclick="location.href='{{ url('/uppn-new/edit/?idx=0&tipx=new&flagz=' . $flagz . '') }}'"
                                                    class="btn btn-warning">New</button>
                                                <button hidden type="button" id='EDITX' onclick='hidup()'
                                                    class="btn btn-secondary">Edit</button>
                                                <button hidden type="button" id='UNDOX'
                                                    onclick="location.href='{{ url('/uppn-new/edit/?idx=' . $idx . '&tipx=undo&flagz=' . $flagz . '') }}'"
                                                    class="btn btn-info">Undo</button>
                                                <button type="button" id='SAVEX' onclick='simpan()'
                                                    class="btn btn-success" class="fa fa-save"></i>Save</button>

                                            </div>
                                            <div class="col-md-3">
                                                <button hidden type="button" id='HAPUSX' onclick="hapusTrans()"
                                                    class="btn btn-outline-danger">Hapus</button>

                                                <!-- <button type="button" id='CLOSEX'  onclick="location.href='{{ url('/beli?flagz=' . $flagz . '') }}'" class="btn btn-outline-secondary">Close</button> -->

                                                <!-- tombol close sweet alert -->
                                                <button type="button" id='CLOSEX' onclick="closeTrans()"
                                                    class="btn btn-outline-secondary">Close</button>
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



        <div class="modal fade" id="browsePoModal" tabindex="-1" role="dialog" aria-labelledby="browsePoModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="browsePoModalLabel">Cari Po</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-stripped table-bordered" id="table-bpo">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th style="width: 50px">Tgl</th>
                                    <!-- <th>Kode Supplier</th>
                                    <th>Nama Supplier</th> -->
                                    <!-- <th>Barang</th> -->
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <!-- <th>Sisa</th> -->
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


        <div class="modal fade" id="browseBeliModal" tabindex="-1" role="dialog"
            aria-labelledby="browseBeliModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="browseBeliModalLabel">Cari Pembelian</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-stripped table-bordered" id="table-bbeli">
                            <thead>
                                <tr>
                                    <th>No Beli</th>
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

                setTimeout(function() {

                    $("#LOADX").hide();

                }, 500);

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

                        }
                        return false;
                    }
                });

                // Tangkap tombol Enter di input NO_PO
                $("#NO_PO").on("keydown", function(e) {
                    if (e.key === "Enter" || e.keyCode === 13) {
                        e.preventDefault();

                        // Panggil fungsi untuk load data PO
                        loadDataBPo();

                        // Panggil fungsi getPod jika memang perlu
                        // Misalnya ambil data dari input
                        var noPo = $("#NO_PO").val();
                        var kodes = $("#KODES").val(); // atau ambil dari hasil loadData

                        getPod(noPo, kodes);
                    }
                });

                $("#NO_PO").keypress(function(e){
                    if(e.keyCode == 46){
                        e.preventDefault();
                        console.log('masuk ini ya');
                        browsePo();
                    }
                });

                browsePo = function(){
                    loadDataBPo();

                    if ( $("#NO_PO").val() == '' ) {
                            $("#browsePoModal").modal("show");
                    }
                }


                $tipx = $('#tipx').val();
                $searchx = $('#CARI').val();


                if ($tipx == 'new') {
                    baru();
                    //  tambah();
                }

                if ($tipx != 'new') {
                    ganti();
                }

                $("#TTOTAL_QTY").autoNumeric('init', {
                    aSign: '<?php echo ''; ?>',
                    vMin: '-999999999.9'
                });
                $("#PROM").autoNumeric('init', {
                    aSign: '<?php echo ''; ?>',
                    vMin: '-999999999.9'
                });
                $("#TPPN").autoNumeric('init', {
                    aSign: '<?php echo ''; ?>',
                    vMin: '-999999999.9'
                });
                $("#DPP").autoNumeric('init', {
                    aSign: '<?php echo ''; ?>',
                    vMin: '-999999999.9'
                });
                $("#NETT").autoNumeric('init', {
                    aSign: '<?php echo ''; ?>',
                    vMin: '-999999999.9'
                });
                $("#BRUTO").autoNumeric('init', {
                    aSign: '<?php echo ''; ?>',
                    vMin: '-999999999.9'
                });


                jumlahdata = 100;
                for (i = 0; i <= jumlahdata; i++) {
                    $("#qty" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.9'
                    });
                    $("#qtyk" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.9'
                    });
                    $("#harga" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.9'
                    });
                    $("#hargak" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.9'
                    });
                    $("#total" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.9'
                    });
                    $("#kemasan" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.9'
                    });
                    $("#DISKON1" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.9'
                    });
                    $("#DISKON2" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.9'
                    });
                    $("#DISKON3" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.9'
                    });
                    $("#DISKON4" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.9'
                    });
                }

                hitung();

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

                var dTableBPo;
                loadDataBPo = function() {

                    $.ajax({
                        type: 'GET',
                        url: "{{ url('uppn-new/browse') }}",
                        async: false,
                        data: {

                            'no_po': $("#NO_PO").val(),
                        },
                        success: function(resp) {

                            // if (!resp || resp.length === 0) {
                            //     Swal.fire({
                            //         icon: 'warning',
                            //         title: 'No Bukti tidak bisa dipanggil',
                            //         text: 'Nomor scan barcode sudah pernah digunakan atau tidak ditemukan.',
                            //         confirmButtonText: 'OK'
                            //     });
                            //     return;
                            // }
                            console.log('ini hasil data po : ', resp);

                            if (resp.length > 0) {

                                if (dTableBPo) {
                                    dTableBPo.clear();
                                }

                               for (let i = 0; i < resp.length; i++) {
									dTableBPo.row.add([
										'<a href="javascript:void(0);" onclick="choosePo(\'' +
										resp[i].NO_BUKTI + '\', \'' +
										resp[i].KODES + '\', \'' +
										resp[i].NAMAS + '\', \'' +
										resp[i].alamat + '\', \'' +
										resp[i].kota + '\', \'' +
										resp[i].HARI + '\', \'' +
										resp[i].GOLONGAN + '\', \'' +
										resp[i].JTEMPO + '\', \'' +
										resp[i].TYPE + '\', \'' +
										resp[i].notes + '\', \'' +
										resp[i].disc_ps + '\', \'' +
										resp[i].nppn + '\', \'' +
										resp[i].CBG + '\', \'' +
										resp[i].CBG_TUJU +
										'\')">' +
										resp[i].NO_BUKTI + '</a>',

										resp[i].TGL,
										resp[i].TOTAL_QTY,
										resp[i].TOTAL,

										// kalau mau tampilkan juga di tabel:
										resp[i].CBG,
										resp[i].CBG_TUJU
									]);
								}

                                dTableBPo.draw();

                            } else {

                                $("#KODES").val(resp[0].KODES);
                                $("#NAMAS").val(resp[0].NAMAS);
                                $("#alamat").val(resp[0].alamat);
                                $("#kota").val(resp[0].kota);
                                $("#HARI").val(resp[0].HARI);
                                $("#GOLONGAN").val(resp[0].GOLONGAN);
                                $("#JTEMPO").val(resp[0].JTEMPO);
                                $("#TYPE").val(resp[0].TYPE);
                                $("#notes").val(resp[0].notes);
								$("#CBG").val(resp[0].CBG_TUJU);
								$("#CBG_DARI").val(resp[0].CBG);
                            }
                        }

                    });
                }

                dTableBPo = $("#table-bpo").DataTable({

                });

                choosePo = choosePo = function(
    NO_BUKTI,
    KODES,
    NAMAS,
    alamat,
    kota,
    HARI,
    GOLONGAN,
    JTEMPO,
    TYPE,
    notes,
    disc_ps,
    nppn,
    CBG,
    CBG_TUJU
) {

                    // $("#").val(NO_BUKTI);
                    $("#NO_PO").val(NO_BUKTI);
                    $("#KODES").val(KODES);
                    $("#NAMAS").val(NAMAS);
                    $("#alamat").val(alamat);
                    $("#kota").val(kota);
                    // $("#HARI").val(HARI);
                    // $("#GOLONGAN").val(GOLONGAN);
                    // $("#JTEMPO").val(JTEMPO);
                    // $("#TYPE").val(TYPE);
                    // $("#notes").val(notes);
                    $("#disc_ps").val(disc_ps);
                    $("#nppn").val(nppn);
					$("#CBG").val(CBG_TUJU);
					$("#CBG_DARI").val(CBG);

                    getPod(NO_BUKTI, KODES);
                    hitung();

                    $("#browsePoModal").modal("hide");


                }

                $("#NO_PO").on("keydown", function(e) {
                    if (e.which == 13) { // 13 = Enter
                        e.preventDefault();
                        var no_po = $(this).val().trim();

                        if (no_po === "") return;

                        $.ajax({
                            type: "GET",
                            url: "{{ url('po/get_po_by_no') }}",
                            data: {
                                no_po: no_po
                            },
                            beforeSend: function() {
                                $("#LOADX").show();
                            },
                            success: function(resp) {
                                console.log(resp);
                                if (resp.length > 0) {
                                    var row = resp[0]; // ambil data pertama
                                    $("#NO_PO").val(row.NO_BUKTI);
                                    $("#KODES").val(row.KODES);
                                    $("#NAMAS").val(row.NAMAS);
                                    $("#alamat").val(row.alamat);
                                    $("#kota").val(row.kota);
                                    $("#HARI").val(row.HARI);
                                    $("#GOLONGAN").val(row.GOLONGAN);
                                    $("#JTEMPO").val(row.JTEMPO);
                                    $("#TYPE").val(row.TYPE);
                                    $("#notes").val(row.notes);
                                    $("#disc_ps").val(row.disc_ps);
                                    $("#nppn").val(row.nppn);

                                    getPod(row.NO_BUKTI, row.KODES);
                                    hitung();
                                } else {
                                    alert("Nomor PO tidak ditemukan!");
                                }
                            },
                            error: function() {
                                $("#LOADX").hide();

                            }
                        });
                    }
                });



                ////////////////////////////////////////////////////




                //////////////////////////////////////////////////////////////////

                function getPod(bukti, kodes) {
                    console.log(bukti);
                    var mulai = (idrow == baris) ? idrow - 1 : idrow;

                    $.ajax({
                        type: 'GET',
                        url: "{{ url('uppn-new/browse_spd') }}",
                        data: {
                            nobukti: bukti,
                            kodes: kodes
                        },
                        success: function(resp) {


                            $('#KODES').val(resp[0].CBG);
                            $('#NAMAS').val(resp[0].NAMA);
                            $('#alamat').val(resp[0].ALAMAT);
                            $('#kota').val(resp[0].KOTA);

                            var html = '';
                            for (i = 0; i < resp.length; i++) {
                                html += `<tr>
                                    <td><input name='REC[]' id='REC${i}' value=${resp[i].REC+1} type='text' class='REC form-control' onkeypress='return tabE(this,event)' readonly></td>

									<td>
										<input name='KD_BRG[]' id='KD_BRG${i}' value="${resp[i].KD_BRG}" type='text' class='form-control KD_BRG' readonly>
						            </td>
						            <td>
						 			    <input name='NA_BRG[]' id='NA_BRG${i}' value="${resp[i].NA_BRG}" type='text' class='form-control  NA_BRG' readonly>
						            </td>

									<td>
										<input name='SISA[]' id='SISA${i}' onclick='select()' style="text-align: right" value="${resp[i].QTY}" type='text' class='form-control  SISA' readonly>
									</td>

									<td>
										<input name='QTY[]' onblur='hitung()' onclick='select()' style="text-align: right" id='QTY${i}' value="${resp[i].QTY}" type='text' class='form-control  QTY' readonly>
									</td>

									<td>
										<input name='hargak[]' onclick='select()' style="text-align: right" onblur='hitung()' id='hargak${i}' value="${resp[i].HARGA}" type='text' class='form-control  hargak' readonly>
									</td>



									<td>
										<input name='total[]' onclick='select()' style="text-align: right" onblur='hitung()' id='total${i}' value="${resp[i].TOTAL}" type='text' class='form-control  total' readonly>
									</td>

									<td>
										<input name='ket[]' onclick='select()' id='ket${i}' value="" type='text' class='form-control  ket'>
									</td>

									<td>
										<input name='harga[]' onclick='select()' style="text-align: right" onblur='hitung()' id='harga${i}' value="0" type='text' class='form-control  harga' readonly>
									</td>

									<td>
										<input name='qty[]' onclick='select()' style="text-align: right" onblur='hitung()' id='qty${i}' value="0" type='text' class='form-control  qty' readonly>
									</td>

									<td><button type='button' class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button></td>
                                </tr>`;
                            }
                            $('#detailPod').html(html);

                            $(".qty").autoNumeric('init', {
                                aSign: '<?php echo ''; ?>',
                                vMin: '-999999999.9'
                            });
                            $(".qty").autoNumeric('update');

                            $(".qtyk").autoNumeric('init', {
                                aSign: '<?php echo ''; ?>',
                                vMin: '-999999999.9'
                            });
                            $(".qtyk").autoNumeric('update');

                            $(".harga").autoNumeric('init', {
                                aSign: '<?php echo ''; ?>',
                                vMin: '-999999999.9'
                            });
                            $(".harga").autoNumeric('update');

                            $(".hargak").autoNumeric('init', {
                                aSign: '<?php echo ''; ?>',
                                vMin: '-999999999.9'
                            });
                            $(".hargak").autoNumeric('update');

                            $(".total").autoNumeric('init', {
                                aSign: '<?php echo ''; ?>',
                                vMin: '-999999999.9'
                            });
                            $(".total").autoNumeric('update');

                            $(".kemasan").autoNumeric('init', {
                                aSign: '<?php echo ''; ?>',
                                vMin: '-999999999.9'
                            });
                            $(".kemasan").autoNumeric('update');

                            $(".DISKON1").autoNumeric('init', {
                                aSign: '<?php echo ''; ?>',
                                vMin: '-999999999.9'
                            });
                            $(".DISKON1").autoNumeric('update');

                            $(".DISKON2").autoNumeric('init', {
                                aSign: '<?php echo ''; ?>',
                                vMin: '-999999999.9'
                            });
                            $(".DISKON2").autoNumeric('update');

                            $(".DISKON3").autoNumeric('init', {
                                aSign: '<?php echo ''; ?>',
                                vMin: '-999999999.9'
                            });
                            $(".DISKON3").autoNumeric('update');

                            $(".DISKON4").autoNumeric('init', {
                                aSign: '<?php echo ''; ?>',
                                vMin: '-999999999.9'
                            });
                            $(".DISKON4").autoNumeric('update');


                            idrow = resp.length;
                            baris = resp.length;

                            nomor();
                            hitung();

                            $(".date").datepicker({
                                'dateFormat': 'dd-mm-yy',
                            })
                        }
                    });
                }

                //////////////////////////////////////////////////////////////////

                //////////////////////////////////////////////////////

                var dTableBBarang;
                var rowidBarang;
                loadDataBBarang = function() {

                    $.ajax({
                        type: 'GET',
                        url: "{{ url('vbrg/browse_koreksi') }}",

                        beforeSend: function() {
                            $("#LOADX").show();
                        },

                        async: false,
                        data: {
                            'KD_BRG': $("#KD_BRG" + rowidBarang).val(),

                        },

                        success: function(response)

                        {

                            $("#LOADX").hide();

                            resp = response;


                            if (resp.length > 1) {
                                if (dTableBBarang) {
                                    dTableBBarang.clear();
                                }
                                for (i = 0; i < resp.length; i++) {

                                    dTableBBarang.row.add([
                                        '<a href="javascript:void(0);" onclick="chooseBarang(\'' +
                                        resp[i].KD_BRG + '\', \'' + resp[i].NA_BRG + '\' , \'' +
                                        resp[i].SATUAN + '\' )">' + resp[i].KD_BRG + '</a>',
                                        resp[i].NA_BRG,
                                        resp[i].SATUAN,
                                    ]);
                                }
                                dTableBBarang.draw();

                            } else {
                                $("#KD_BRG" + rowidBarang).val(resp[0].KD_BRG);
                                $("#NA_BRG" + rowidBarang).val(resp[0].NA_BRG);
                                $("#SATUAN" + rowidBarang).val(resp[0].SATUAN);
                            }
                        }
                    });
                }

                dTableBBarang = $("#table-bbarang").DataTable({

                });

                browseBarang = function(rid) {
                    rowidBarang = rid;
                    $("#NA_BRG" + rowidBarang).val("");
                    loadDataBBarang();


                    if ($("#NA_BRG" + rowidBarang).val() == '') {
                        $("#browseBarangModal").modal("show");
                    }
                }

                chooseBarang = function(KD_BRG, NA_BRG, SATUAN) {
                    $("#KD_BRG" + rowidBarang).val(KD_BRG);
                    $("#NA_BRG" + rowidBarang).val(NA_BRG);
                    $("#SATUAN" + rowidBarang).val(SATUAN);
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




            function cekDetail() {
                var cekBarang = '';
                $(".KD_BRG").each(function() {

                    let z = $(this).closest('tr');
                    var KD_BRGX = z.find('.KD_BRG').val();

                    if (KD_BRGX == "") {
                        cekBarang = '1';

                    }
                });

                return cekBarang;
            }


            function simpan() {
                hitung();

                var tgl = $('#TGL').val();
                var bulanPer = {{ session()->get('periode')['bulan'] }};
                var tahunPer = {{ session()->get('periode')['tahun'] }};

                var check = '0';

                if (baris == 0) {
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

                if ($('#KD_BRG').val() == '') {
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
                var PROMX = 0;
                var DPPX = 0;
                var TPPNX = 0;
                var NETTX = 0;
                var BRUTOX = 0;
                var DISC_PSX = parseFloat(($(".disc_ps").val() || "0").replace(/,/g, ''));
                var NPPNX = parseFloat(($(".nppn").val() || "0").replace(/,/g, ''));



                $(".SISA").each(function() {

                    let z = $(this).closest('tr');
                    var SISAX = parseFloat(z.find('.SISA').val().replace(/,/g, ''));
                    var hargakx = parseFloat(z.find('.hargak').val().replace(/,/g, ''));
                    // var kemasan = parseFloat(z.find('.kemasan').val().replace(/,/g, ''));
                    // var DISK1X = parseFloat(z.find('.DISKON1').val().replace(/,/g, ''));
                    // var DISK2X = parseFloat(z.find('.DISKON2').val().replace(/,/g, ''));
                    // var DISK3X = parseFloat(z.find('.DISKON3').val().replace(/,/g, ''));
                    // var DISK4X = parseFloat(z.find('.DISKON4').val().replace(/,/g, ''));
                    var qtykx = parseFloat(z.find('.QTY').val().replace(/,/g, ''));
                    // var qtyx = (qtykx * kemasan);
                    var qtyx = (qtykx);
                    // var hargax = (hargakx / kemasan);
                    var hargax = (hargakx);
                    var totalx = (qtyx * hargax);
                    // var totalx = Math.floor((((nilaix * (100 - DISK1X) / 100) * (100 - DISK2X) / 100) * (100 - DISK3X) /
                    //     100) * (100 - DISK4X) / 100);


                    z.find('.total').val(totalx);
                    z.find('.qty').val(qtyx);
                    z.find('.harga').val(hargax);


                    z.find('.harga').autoNumeric('update');
                    z.find('.hargak').autoNumeric('update');
                    z.find('.qty').autoNumeric('update');
                    z.find('.qtyk').autoNumeric('update');
                    z.find('.total').autoNumeric('update');

                    TTOTAL_QTY += qtykx;
                    BRUTOX = BRUTOX + totalx;

                });

                if (isNaN(TTOTAL_QTY)) TTOTAL_QTY = 0;
                $('#TTOTAL_QTY').val(numberWithCommas(TTOTAL_QTY));
                $("#TTOTAL_QTY").autoNumeric('update');

                if (isNaN(BRUTOX)) BRUTOX = 0;
                $('#BRUTO').val(numberWithCommas(BRUTOX));
                $("#BRUTO").autoNumeric('update');

                PROMX = Math.floor(BRUTOX * DISC_PSX / 100);
                if (isNaN(PROMX)) PROMX = 0;
                $('#PROM').val(numberWithCommas(PROMX));
                $("#PROM").autoNumeric('update');

                DPPX = BRUTOX - PROMX;
                if (isNaN(DPPX)) DPPX = 0;
                $('#DPP').val(numberWithCommas(DPPX));
                $("#DPP").autoNumeric('update');

                if ($('#GOLONGAN').val() == 'P1') {
                    TPPNX = Math.floor(DPPX * NPPNX / 100);
                }
                if (isNaN(TPPNX)) TPPNX = 0;
                $('#TPPN').val(numberWithCommas(TPPNX));
                $("#TPPN").autoNumeric('update');

                NETTX = DPPX + TPPNX;
                if (isNaN(NETTX)) NETTX = 0;
                $('#NETT').val(numberWithCommas(NETTX));
                $("#NETT").autoNumeric('update');
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
                $("#TTOTAL_QTY").attr("readonly", true);
                $("#PKP").attr("disabled", true);


                jumlahdata = 100;
                for (i = 0; i <= jumlahdata; i++) {
                    $("#REC" + i.toString()).attr("readonly", true);
                    $("#KD_BRG" + i.toString()).attr("readonly", false);
                    $("#NA_BRG" + i.toString()).attr("readonly", true);
                    $("#SATUAN" + i.toString()).attr("readonly", true);
                    $("#QTY" + i.toString()).attr("readonly", false);
                    $("#KET" + i.toString()).attr("readonly", false);
                    $("#harga" + i.toString()).attr("readonly", true);
                    $("#hargak" + i.toString()).attr("readonly", true);
                    $("#TOTAL" + i.toString()).attr("readonly", true);
                    $("#DPP" + i.toString()).attr("readonly", true);
                    $("#PPN" + i.toString()).attr("readonly", true);
                    $("#DELETEX" + i.toString()).attr("hidden", false);

                    $tipx = $('#tipx').val();


                    if ($tipx != 'new') {

                        $("#NO_PO").attr("readonly", true);
                        // $("#NO_PO").removeAttr("onblur");

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
                $("#NOTES").attr("readonly", true);
                $("#TTOTAL_QTY").attr("readonly", true);
                $("#NAMAS").attr("readonly", true);
                $("#PKP").attr("disabled", true);


                jumlahdata = 100;
                for (i = 0; i <= jumlahdata; i++) {
                    $("#REC" + i.toString()).attr("readonly", true);
                    $("#KD_BRG" + i.toString()).attr("readonly", true);
                    $("#NA_BRG" + i.toString()).attr("readonly", true);
                    $("#SATUAN" + i.toString()).attr("readonly", true);
                    // $("#QTYC" + i.toString()).attr("readonly", true);
                    // $("#QTYR" + i.toString()).attr("readonly", true);
                    $("#QTY" + i.toString()).attr("readonly", true);
                    $("#KET" + i.toString()).attr("readonly", true);
                    $("#harga" + i.toString()).attr("readonly", true);
                    $("#hargak" + i.toString()).attr("readonly", true);
                    $("#TOTAL" + i.toString()).attr("readonly", true);
                    $("#DPP" + i.toString()).attr("readonly", true);
                    $("#PPN" + i.toString()).attr("readonly", true);

                    $("#DELETEX" + i.toString()).attr("hidden", true);
                }



            }


            function kosong() {

                $('#NO_BUKTI').val("+");
                $('#NOTES').val("");
                $('#NETT').val("0.00");
                $('#DPP').val("0.00");
                $('#BRUTO').val("0.00");
                $('#PROM').val("0.00");

                $('#TTOTAL_QTY').val("0.00");
                $('#TTOTAL').val("0.00");
                $('#TPPN').val("0.00");

                var html = '';
                $('#detailx').html(html);

            }

            // function hapusTrans() {
            // 	let text = "Hapus Transaksi "+$('#NO_BUKTI').val()+"?";
            // 	if (confirm(text) == true)
            // 	{
            // 		window.location ="{{ url('/beli/delete/' . $header->NO_ID . '/?flagz=' . $flagz . '') }}";
            // 		//return true;
            // 	}
            // 	return false;
            // }

            // sweetalert untuk tombol hapus dan close

            function hapusTrans() {
                let text = "Hapus Transaksi " + $('#NO_BUKTI').val() + "?";

                var loc = '';
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
                            loc = "{{ url('/beli/delete/' . $header->NO_ID) }}" + '?flagz=' +
                                encodeURIComponent(
                                    flagz);

                            // alert(loc);
                            window.location = loc;

                        });
                    }
                });
            }

            function closeTrans() {
                console.log("beli");
                var loc = '';
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
                        loc = "{{ url('/uppn/') }}" + '?flagz=' + encodeURIComponent(flagz);
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

                var flagz = "{{ $flagz }}";
                var cari = $("#CARI").val();
                var loc = "{{ url('/beli/edit/') }}" + '?idx={{ $header->NO_ID }}&tipx=search&flagz=' + encodeURIComponent(
                    flagz) + '&buktix=' + encodeURIComponent(cari);
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
				    <input name='KD_BRG[]' data-rowid=${idrow} onblur='browseBarang(${idrow})' id='KD_BRG${idrow}' type='text' class='form-control  KD_BRG' >
                </td>

                <td>
				    <input name='NA_BRG[]'   id='NA_BRG${idrow}' type='text' class='form-control  NA_BRG' required readonly>
                </td>
                <td>
				    <input name='SISA[]'   id='SISA${idrow}' type='text' class='form-control  SISA' readonly required>
                </td>
				<td>
		            <input name='qtyk[]' onclick='select()' onblur='hitung()' value='0' id='qtyk${idrow}' type='text' style='text-align: right' class='form-control qtyk text-primary' >
                </td>

				<td>
		            <input name='kemasan[]' onclick='select()' onblur='hitung()' value='0' id='kemasan${idrow}' type='text' style='text-align: right' class='form-control kemasan text-primary' >
                </td>

				<td>
		            <input name='hargak[]' onclick='select()' onblur='hitung()' value='0' id='hargak${idrow}' type='text' style='text-align: right' class='form-control hargak text-primary' >
                </td>


				<td>
		            <input name='total[]' onclick='select()' onblur='hitung()' value='0' id='total${idrow}' type='text' style='text-align: right' class='form-control total text-primary' >
                </td>

                <td>
				    <input name='KET[]'   id='KET${idrow}' type='text' class='form-control  KET' required>
                </td>

				<td>
		            <input name='harga[]' onclick='select()' onblur='hitung()' value='0' id='harga${idrow}' type='text' style='text-align: right' class='form-control harga text-primary' >
                </td>

				<td>
		            <input name='qty[]' onclick='select()' onblur='hitung()' value='0' id='qty${idrow}' type='text' style='text-align: right' class='form-control qty text-primary' readonly>
                </td>

				<td>
		            <input name='kdlaku[]' onclick='select()' onblur='hitung()' value='' id='kdlaku${idrow}' type='text' style='text-align: right' class='form-control kdlaku text-primary' readonly>
                </td>

				<td>
		            <input name='TGL_EXP[]' value='' id='TGL_EXP${idrow}' type='text' style='text-align: left' class='form-control date TGL_EXP text-primary' >
                </td>

                <td>
					<button type='button' id='DELETEX${idrow}'  class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button>
                </td>
         </tr>`;

                x.innerHTML = html;
                var html = '';



                jumlahdata = 100;
                for (i = 0; i <= jumlahdata; i++) {
                    $("#qty" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.99'
                    });


                    $("#qtyk" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.99'
                    });

                    $("#harga" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.99'
                    });

                    $("#hargak" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.99'
                    });

                    $("#total" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.99'
                    });

                    $("#kemasan" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.99'
                    });

                    $("#DISKON1" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.99'
                    });

                    $("#DISKON2" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.99'
                    });

                    $("#DISKON3" + i.toString()).autoNumeric('init', {
                        aSign: '<?php echo ''; ?>',
                        vMin: '-999999999.99'
                    });

                    $("#DISKON4" + i.toString()).autoNumeric('init', {
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
