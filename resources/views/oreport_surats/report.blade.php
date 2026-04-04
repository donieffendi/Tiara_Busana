@extends('layouts.plain')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Laporan Stok Barang </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">Laporan Stok Barang </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">

                <div class="card">
                    <div class="card-body">

                        <form method="POST" action="{{ url('jasper-surats-report') }}">
                            @csrf
                            <input type="hidden" name="mode" id="mode">

                            <!-- ================= FILTER GLOBAL ================= -->
                            <div class="form-group row">

                                <div class="col-md-2 mr-4">
                                    <label>Periode</label>
                                    <select name="per" id="per" class="form-control per" style="width: 150px">
                                        <option value="">--Pilih Periode--</option>
                                        @foreach ($per as $perD)
                                            <option value="{{ $perD->PERIO }}"
                                                {{ session()->get('filter_periode') == $perD->PERIO ? 'selected' : '' }}>
                                                {{ $perD->PERIO }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Cabang</label>
                                    <select name="cbg" class="form-control">
                                        <option value="">--Pilih--</option>
                                        @foreach ($cbg as $c)
                                            <option value="{{ $c->CBG }}"
                                                {{ session('cbg') == $c->CBG ? 'selected' : '' }}>
                                                {{ $c->CBG }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label>Urut</label>
                                    <select name="urut" class="form-control">
                                        <option value="kode_brg">Kode Brg</option>
                                        <option value="na_brg">Nama Brg</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-2">
                                    <label>Sub 1</label>
                                    <input type="text" name="sub1" class="form-control" value="{{ session('sub1') }}">
                                </div>

                                <div class="col-md-2">
                                    <label>s/d</label>
                                    <input type="text" name="sub2" class="form-control" value="{{ session('sub2') }}">
                                </div>
                                <div class="col-md-2">
                                    <label>Suplier</label>
                                    <input type="text" id="sup" name="sup1" class="form-control"
                                        value="{{ session('sup1') }}" readonly>
                                </div>

                                <div class="col-md-2">
                                    <label>s/d</label>
                                    <input type="text" id="sup2" name="sup2" class="form-control"
                                        value="{{ session('sup2') }}" readonly>
                                </div>

                            </div>
                            <hr>

                            <!-- ================= TAB ================= -->
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#periode">Periode</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#card">Card</a>
                                </li>
                            </ul>

                            <div class="tab-content">

                                <!-- ================= TAB PERIODE ================= -->
                                <div class="tab-pane fade show active" id="periode">

                                    <br>

                                    <!-- FILTER KHUSUS PERIODE -->
                                    <div class="form-group row">
                                        <div class="col-md-2">
                                            <label>Kode 1</label>
                                            <input type="text" name="kode1" value="{{ session('kode1') }}" class="form-control">
                                        </div>

                                        <div class="col-md-2">
                                            <label>Kode 2</label>
                                            <input type="text" name="kode2" value="{{ session('kode2') }}" class="form-control">
                                        </div>
                                    </div>

                                    <button class="btn btn-success" type="submit" onclick="setMode('periode', true)">Filter
                                        Periode</button>

                                    <button class="btn btn-warning" type="submit" formtarget="_blank"
                                        onclick="setMode('periode', false)">Cetak</button>
                                    <br><br>

                                    <!-- TABLE PERIODE -->
                                    {{-- <div style="overflow-x:auto;">
                                        <script>
                                            let dataHasil = @json($hasil);
                                            console.log(dataHasil);
                                        </script>
                                        <?php
                                        if ($hasil) {
                                            \koolreport\datagrid\DataTables::create([
                                                'dataSource' => collect($hasil),
                                                'columns' => [
                                                    'CNT' => 'Sub',
                                                    'KD_BRG' => 'Kode Barang',
                                                    'BARCODE' => 'Barcode',
                                                    'NA_BRG' => 'Nama Barang',

                                                    'TGL_TRM' => 'Tgl Beli',
                                                    'TGL_JUAL' => 'Tgl Jual',

                                                    'AW' => [
                                                        'label' => 'Awal',
                                                        'type' => 'number',
                                                        'footer' => 'sum',
                                                    ],
                                                    'MA' => [
                                                        'label' => 'Masuk',
                                                        'type' => 'number',
                                                        'footer' => 'sum',
                                                    ],
                                                    'KE' => [
                                                        'label' => 'Keluar',
                                                        'type' => 'number',
                                                        'footer' => 'sum',
                                                    ],
                                                    'LN' => [
                                                        'label' => 'Lain',
                                                        'type' => 'number',
                                                        'footer' => 'sum',
                                                    ],
                                                    'AK' => [
                                                        'label' => 'Akhir',
                                                        'type' => 'number',
                                                        'footer' => 'sum',
                                                    ],
                                                ],
                                                'options' => [
                                                    'paging' => true,
                                                    'searching' => true,
                                                    'dom' => 'Blfrtip',
                                                    'buttons' => ['copy', 'excel', 'csv', 'pdf', 'print'],
                                                ],
                                            ]);
                                        }
                                        ?>
                                    </div> --}}
                                    <div class="report-content" col-md-12 style="max-width: 100%; overflow-x: scroll;">
                                        <?php
                                        use koolreport\datagrid\DataTables;

                                        if ($hasil) {
                                            DataTables::create([
                                                'dataSource' => $hasil,
                                                'name' => 'example',
                                                'fastRender' => true,
                                                'fixedHeader' => true,
                                                'scrollX' => true,
                                                'showFooter' => true,
                                                'showFooter' => 'bottom',
                                                'columns' => [
                                                    'CNT' => [
                                                        'label' => 'CNT',
                                                    ],
                                                    'KD_BRG' => [
                                                        'label' => 'Kode Barang',
                                                    ],
                                                    'NA_BRG' => [
                                                        'label' => 'Nama Barang',
                                                    ],
                                                    'BARCODE' => [
                                                        'label' => 'Barcode',
                                                    ],
                                                    'TGL_TRM' => [
                                                        'label' => 'Tgl Terima',
                                                    ],
                                                    'TGL_JUAL' => [
                                                        'label' => 'Tgl Jual',
                                                    ],
                                                    'AW' => [
                                                        'label' => 'AW',
                                                        'type' => 'number',
                                                        'decimals' => 2,
                                                        'decimalPoint' => '.',
                                                        'thousandSeparator' => ',',
                                                        'footer' => 'sum',
                                                        'footerText' => '<b>@value</b>',
                                                    ],
                                                    'MA' => [
                                                        'label' => 'MA',
                                                        'type' => 'number',
                                                        'decimals' => 2,
                                                        'decimalPoint' => '.',
                                                        'thousandSeparator' => ',',
                                                        'footer' => 'sum',
                                                        'footerText' => '<b>@value</b>',
                                                    ],
                                                    'KE' => [
                                                        'label' => 'KE',
                                                        'type' => 'number',
                                                        'decimals' => 2,
                                                        'decimalPoint' => '.',
                                                        'thousandSeparator' => ',',
                                                        'footer' => 'sum',
                                                        'footerText' => '<b>@value</b>',
                                                    ],

                                                    'LN' => [
                                                        'label' => 'LN',
                                                        'type' => 'number',
                                                        'decimals' => 2,
                                                        'decimalPoint' => '.',
                                                        'thousandSeparator' => ',',
                                                        'footer' => 'sum',
                                                        'footerText' => '<b>@value</b>',
                                                    ],
                                                    'AK' => [
                                                        'label' => 'AK',
                                                        'type' => 'number',
                                                        'decimals' => 2,
                                                        'decimalPoint' => '.',
                                                        'thousandSeparator' => ',',
                                                        'footer' => 'sum',
                                                        'footerText' => '<b>@value</b>',
                                                    ],
                                                ],
                                                'cssClass' => [
                                                    'table' => 'table table-hover table-striped table-bordered compact',
                                                    'th' => 'label-title',
                                                    'td' => 'detail',
                                                    'tf' => 'footerCss',
                                                ],
                                                'options' => [
                                                    'columnDefs' => [
                                                        [
                                                            'className' => 'dt-right',
                                                            'targets' => [8, 9, 10],
                                                        ],
                                                    ],
                                                    'order' => [],
                                                    'paging' => true,
                                                    // "pageLength" => 12,
                                                    'searching' => true,
                                                    'colReorder' => true,
                                                    'select' => true,
                                                    'dom' => 'Blfrtip', // B e dilangi
                                                    // "dom" => '<"row"<col-md-6"B><"col-md-6"f>> <"row"<"col-md-12"t>><"row"<"col-md-12">>',
                                                    'buttons' => [
                                                        [
                                                            'extend' => 'collection',
                                                            'text' => 'Export',
                                                            'buttons' => ['copy', 'excel', 'csv', 'pdf', 'print'],
                                                        ],
                                                    ],
                                                ],
                                            ]);
                                        }
                                        ?>
                                    </div>

                                </div>

                                <!-- ================= TAB CARD ================= -->
                                <div class="tab-pane fade" id="card">

                                    <br>

                                    <!-- FILTER KHUSUS CARD -->
                                    <div class="form-group row">
                                        <div class="col-md-3">
                                            <label>Kode</label>
                                            <input type="text" name="kode_card" value="{{ session('kode_card') }}"" class="form-control">
                                        </div>

                                        <div class="col-md-3">
                                            <label>Tanggal</label>
                                            <input type="date" name="tgl_card" class="form-control">
                                        </div>
                                    </div>

                                    <button class="btn btn-success" type="submit" onclick="setMode('card', true)">Filter
                                        Card</button>

                                    <button class="btn btn-warning" type="submit" formtarget="_blank"
                                        onclick="setMode('card', false)">Cetak</button>
                                    <br><br>

                                    <!-- TABLE CARD -->
                                    <div style="overflow-x:auto;">
                                        <?php
                                       if ($hasil2) {
                                            DataTables::create([
                                                'dataSource' => $hasil2,
                                                'name' => 'example2',
                                                'fastRender' => true,
                                                'fixedHeader' => true,
                                                'scrollX' => true,
                                                'showFooter' => true,
                                                'showFooter' => 'bottom',
                                                'columns' => [
                                                    'kd_brg' => [
                                                        'label' => 'Kode',
                                                    ],
                                                    'NA_BRG' => [
                                                        'label' => 'Nama',
                                                    ],
                                                    'tgl' => [
                                                        'label' => 'Tanggal',
                                                    ],
                                                    'no_bukti' => [
                                                        'label' => 'Faktur',
                                                    ],
                                                    'awal' => [
                                                        'label' => 'Awal',
                                                        'type' => 'number',
                                                        'decimals' => 2,
                                                        'decimalPoint' => '.',
                                                        'thousandSeparator' => ',',
                                                        'footer' => 'sum',
                                                        'footerText' => '<b>@value</b>',
                                                    ],
                                                    'masuk' => [
                                                        'label' => 'Masuk',
                                                        'type' => 'number',
                                                        'decimals' => 2,
                                                        'decimalPoint' => '.',
                                                        'thousandSeparator' => ',',
                                                        'footer' => 'sum',
                                                        'footerText' => '<b>@value</b>',
                                                    ],
                                                    'keluar' => [
                                                        'label' => 'Keluar',
                                                        'type' => 'number',
                                                        'decimals' => 2,
                                                        'decimalPoint' => '.',
                                                        'thousandSeparator' => ',',
                                                        'footer' => 'sum',
                                                        'footerText' => '<b>@value</b>',
                                                    ],

                                                    'lain' => [
                                                        'label' => 'Lain',
                                                        'type' => 'number',
                                                        'decimals' => 2,
                                                        'decimalPoint' => '.',
                                                        'thousandSeparator' => ',',
                                                        'footer' => 'sum',
                                                        'footerText' => '<b>@value</b>',
                                                    ],
                                                    'SALDO' => [
                                                        'label' => 'Saldo',
                                                        'type' => 'number',
                                                        'decimals' => 2,
                                                        'decimalPoint' => '.',
                                                        'thousandSeparator' => ',',
                                                        'footer' => 'sum',
                                                        'footerText' => '<b>@value</b>',
                                                    ],
                                                ],
                                                'cssClass' => [
                                                    'table' => 'table table-hover table-striped table-bordered compact',
                                                    'th' => 'label-title',
                                                    'td' => 'detail',
                                                    'tf' => 'footerCss',
                                                ],
                                                'options' => [
                                                    'columnDefs' => [
                                                        [
                                                            'className' => 'dt-right',
                                                            'targets' => [8, 9, 10],
                                                        ],
                                                    ],
                                                    'order' => [],
                                                    'paging' => true,
                                                    // "pageLength" => 12,
                                                    'searching' => true,
                                                    'colReorder' => true,
                                                    'select' => true,
                                                    'dom' => 'Blfrtip', // B e dilangi
                                                    // "dom" => '<"row"<col-md-6"B><"col-md-6"f>> <"row"<"col-md-12"t>><"row"<"col-md-12">>',
                                                    'buttons' => [
                                                        [
                                                            'extend' => 'collection',
                                                            'text' => 'Export',
                                                            'buttons' => ['copy', 'excel', 'csv', 'pdf', 'print'],
                                                        ],
                                                    ],
                                                ],
                                            ]);
                                        }
                                        ?>
                                    </div>

                                </div>

                            </div> <!-- tab content -->

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="browseSupModal" tabindex="-1" role="dialog" aria-labelledby="browseSupModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="browseSupModalLabel">Cari Suplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-stripped table-bordered" id="table-sup">
                        <thead>
                            <tr>
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

    <div class="modal fade" id="browseSup2Modal" tabindex="-1" role="dialog" aria-labelledby="browseSup2ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="browseSup2ModalLabel">Cari Suplier 2</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-stripped table-bordered" id="table-sup2">
                        <thead>
                            <tr>
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


    <div class="modal fade" id="browseBrgModal" tabindex="-1" role="dialog" aria-labelledby="browseBrgModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="browseBrgModalLabel">Cari Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-stripped table-bordered" id="table-brg">
                        <thead>
                            <tr>
                                <th>Barang#</th>
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

@section('javascripts')
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script> -->
    <script src="{{ asset('foxie_js_css/bootstrap.bundle.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            $('.date').datepicker({
                dateFormat: 'dd-mm-yy'
            });

        });

        var dTableSup;
        loadDataSup = function() {

            $.ajax({
                type: 'GET',
                url: "{{ url('sup/browse') }}",
                data: {

                },
                success: function(response) {
                    resp = response;
                    if (dTableSup) {
                        dTableSup.clear();
                    }
                    for (i = 0; i < resp.length; i++) {

                        dTableSup.row.add([
                            '<a href="javascript:void(0);" onclick="chooseSup(\'' + resp[i]
                            .NO_SUPL +
                            '\')">' + resp[i].NO_SUPL + '</a>',
                            resp[i].NAMA,
                            resp[i].ALAMAT,
                            resp[i].KOTA,
                        ]);
                    }
                    dTableSup.draw();
                }
            });
        }

        dTableSup = $("#table-sup").DataTable({

        });

        browseSup = function() {
            loadDataSup();
            $("#browseSupModal").modal("show");
        }

        chooseSup = function(NO_SUPL) {
            $("#sup").val(NO_SUPL);
            // $("#NAMAC").val(NAMAC);
            $("#browseSupModal").modal("hide");
        }

        $("#sup").keypress(function(e) {
            if (e.keyCode == 46) {
                e.preventDefault();
                browseSup();
            }
        });

        /////////////////////////////////////////////////////////////////////


        var dTableSup2;
        loadDataSup2 = function() {

            $.ajax({
                type: 'GET',
                url: "{{ url('sup/browse') }}",
                data: {

                },
                success: function(response) {
                    resp = response;
                    if (dTableSup2) {
                        dTableSup2.clear();
                    }
                    for (i = 0; i < resp.length; i++) {

                        dTableSup2.row.add([
                            '<a href="javascript:void(0);" onclick="chooseSup2(\'' + resp[i]
                            .NO_SUPL + '\')">' + resp[i].NO_SUPL + '</a>',
                            resp[i].NAMA,
                            resp[i].ALAMAT,
                            resp[i].KOTA,
                        ]);
                    }
                    dTableSup2.draw();
                }
            });
        }

        dTableSup2 = $("#table-sup2").DataTable({

        });

        browseSup2 = function() {
            loadDataSup2();
            $("#browseSup2Modal").modal("show");
        }

        chooseSup2 = function(NO_SUPL) {
            $("#sup2").val(NO_SUPL);
            // $("#NAMAC").val(NAMAC);
            $("#browseSup2Modal").modal("hide");
        }

        $("#sup2").keypress(function(e) {
            if (e.keyCode == 46) {
                e.preventDefault();
                browseSup2();
            }
        });

        ///////////////////////////////////////////////////////////////////

        var dTableBTujuan;
        var rowidTujuan;
        loadDataBTujuan = function() {
            $.ajax({
                type: 'GET',
                url: "{{ url('tujuan/browse') }}",
                data: {
                    'GOL': 'Z',
                },
                success: function(resp) {
                    if (dTableBTujuan) {
                        dTableBTujuan.clear();
                    }
                    for (i = 0; i < resp.length; i++) {

                        dTableBTujuan.row.add([
                            '<a href="javascript:void(0);" onclick="chooseTujuan(\'' + resp[i]
                            .KODET + '\',  \'' + resp[i].NAMAT + '\',   \'' + resp[i].ALAMAT +
                            '\', \'' + resp[i].KOTA + '\' )">' + resp[i].KODET + '</a>',
                            resp[i].NAMAT,
                            resp[i].ALAMAT,
                            resp[i].KOTA,

                        ]);
                    }
                    dTableBTujuan.draw();
                }
            });
        }

        dTableBTujuan = $("#table-btujuan").DataTable({

        });

        browseTujuan = function() {
            loadDataBTujuan();
            $("#browseTujuanModal").modal("show");
        }

        chooseTujuan = function(KODET, NAMAT, ALAMAT, KOTA) {
            $("#kodet").val(KODET);
            $("#NAMAT").val(NAMAT);
            $("#browseTujuanModal").modal("hide");
        }

        $("#kodet").keypress(function(e) {
            if (e.keyCode == 46) {
                e.preventDefault();
                browseTujuan();
            }
        });


        var dTableBrg;
        loadDataBrg = function(indeks) {

            $.ajax({
                type: 'GET',
                url: "{{ url('brg/browse') }}",
                data: {
                    'GOL': 'Y',
                },
                success: function(response) {
                    resp = response;
                    if (dTableBrg) {
                        dTableBrg.clear();
                    }
                    for (i = 0; i < resp.length; i++) {

                        dTableBrg.row.add([
                            '<a href="javascript:void(0);" onclick="chooseBrg(\'' + resp[i].KD_BRG +
                            '\',  \'' + resp[i].NA_BRG + '\', \'' + indeks + '\')">' + resp[i]
                            .KD_BRG + '</a>',
                            resp[i].NA_BRG,
                            resp[i].SATUAN,
                        ]);
                    }
                    dTableBrg.draw();
                }
            });
        }

        dTableBrg = $("#table-brg").DataTable({

        });

        browseBrg = function(indeks) {
            loadDataBrg(indeks);
            $("#browseBrgModal").modal("show");
        }

        chooseBrg = function(KD_BRG, NA_BRG, indeks) {
            $("#brg" + indeks).val(KD_BRG);
            $("#nabrg" + indeks).val(NA_BRG);
            $("#browseBrgModal").modal("hide");
        }

        $("#brg1").keypress(function(e) {
            if (e.keyCode == 46) {
                e.preventDefault();
                browseBrg(1);
            }
        });

        function setMode(mode, isFilter) {
            document.getElementById('mode').value = mode;

            if (isFilter) {
                // tambahin flag filter
                let input = document.createElement("input");
                input.type = "hidden";
                input.name = "filter";
                input.value = "1";
                document.forms[0].appendChild(input);
            }
        }
    </script>
@endsection
