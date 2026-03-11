@extends('layouts.main')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0">Lihat Order Kerja</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{url('/po')}}">Transaksi Order Kerja</a></li>
                <li class="breadcrumb-item active">{{$header->NO_PO}}</li>
            </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="content">
        <div class="container-fluid">
        <div class="row">
            <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="store" method="POST">
                        @csrf
                        {{-- <ul class="nav nav-tabs">
                            <li class="nav-item active">
                                <a class="nav-link active" href="#data" data-toggle="tab">Data</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#dokumen" data-toggle="tab">Dokumen</a>
                            </li>
                        </ul> --}}
        
                        <div class="tab-content mt-3">
        
                            <div class="form-group row">
								<div class="col-md-2" align="left">
                                    <label for="NO_BUKTI" class="form-label">No Bukti</label>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control NO_BUKTI" id="NO_BUKTI" name="NO_BUKTI" placeholder="Masukkan Bukti" value="{{$header->NO_BUKTI}}" disabled>
                                </div>
                                <div class="col-md-2">
                                </div>
                                <div class="col-md-2" align="right">
                                    <label class="form-label">Customer</label>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control KODEC" id="KODEC" name="KODEC" placeholder="No Customer" value="{{$header->KODEC}}" disabled>
                                </div>
                            </div>
							
                            <div class="form-group row">
                                <div class="col-md-2" align="left">
                                    <label for="TGL" class="form-label">Tgl</label>
                                </div>
                                <div class="col-md-2">
                                   <input class="form-control date" id="TGL" name="TGL" data-date-format="dd-mm-yyyy" type="text" autocomplete="off" value="{{date('d-m-Y',strtotime($header->TGL))}}" disabled>
                                </div>
                                <div class="col-md-1" align="right">
                                    <label for="JTEMPO" class="form-label">Jtempo</label>
                                </div>
                                <div class="col-md-2">
                                   <input class="form-control date" id="JTEMPO" name="JTEMPO" data-date-format="dd-mm-yyyy" type="text" autocomplete="off" value="{{date('d-m-Y',strtotime($header->JTEMPO))}}" disabled>
                                </div>
                                <div class="col-md-1">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control NAMAC" id="NAMAC" name="NAMAC" placeholder="Nama Customer" value="{{$header->NAMAC}}" disabled>
                                </div>
                            </div>
							
                            <div class="form-group row">
                                <div class="col-md-2" align="left">
									<label style="color:red;font-size:20px">* </label>								
                                    <label class="form-label">SO#</label>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control NO_SO" id="NO_SO" name="NO_SO" placeholder="No Sales Order" value="{{$header->NO_SO}}" disabled>
                                </div>
                                <div class="col-md-3">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control ALAMAT" id="ALAMAT" name="ALAMAT" placeholder="Alamat Customer" value="{{$header->ALAMAT}}" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-2" align="left">
                                    <label class="form-label">Barang</label>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control KD_BRG" id="KD_BRG" name="KD_BRG" placeholder="Barang#" value="{{$header->KD_BRG}}" disabled>
                                </div>
                                <div class="col-md-1" align="right">
									<label style="color:red;font-size:20px">* </label>								
                                    <label class="form-label">FO#</label>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control NO_FO" id="NO_FO" name="NO_FO" placeholder="No Formula" value="{{$header->NO_FO}}" disabled>
                                </div>
                                <div class="col-md-1">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control KOTA" id="KOTA" name="KOTA" placeholder="Kota Customer" value="{{$header->KOTA}}" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-2" align="right">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control NA_BRG" id="NA_BRG" name="NA_BRG" placeholder="Nama Barang" value="{{$header->NA_BRG}}" disabled>
                                </div>
                            </div>
							
                            <div class="form-group row">
                                <div class="col-md-2" align="left">
                                    <label class="form-label">Qty</label>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control QTYH" id="QTYH" name="QTYH" value="{{$header->QTY}}" disabled>
                                </div>
                                <div class="col-md-1" align="left">
                                    <label class="form-label">Satuan</label>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control SATUANH" id="SATUANH" name="SATUANH" placeholder="Satuan" value="{{$header->SATUAN}}" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-2" align="left">
                                    <label class="form-label">Seri#</label>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control NO_SERI" id="NO_SERI" name="NO_SERI" placeholder="No Seri" value="{{$header->NO_SERI}}" disabled>
                                </div>
                            </div>

							<div class="form-group row">
                                <div class="col-md-2" align="left">
                                    <label for="NOTES" class="form-label">Notes</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control NOTES" id="NOTES" name="NOTES" placeholder="Masukkan Notes" value="{{$header->NOTES}}" disabled>
                                </div>
                            </div>
                            
                            <table id="datatable" class="table table-striped table-border">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">No.</th>
                                        <th style="text-align: center;">
									       <label style="color:red;font-size:20px">* </label>									
                                           <label class="form-label">Kode</label></th>
                                        <th style="text-align: center;">Proses</th>
                                        <th style="text-align: center;">
									       <label style="color:red;font-size:20px">* </label>									
                                           <label class="form-label">Kode</label></th>
                                        <th style="text-align: center;">Bahan</th>
                                        <th style="text-align: center;">Stn</th>
                                        <th style="text-align: center;">Qtya</th>
										<th style="text-align: center;">Qty</th>
										<th style="text-align: center;">Ket</th>
										<th style="text-align: center;"></th>
                                    </tr>
                                </thead>
								
                                <tbody id="detailOrderk">
								<?php $no=0 ?>
								@foreach ($detail as $orderkd)
                                    <tr>
                                        <td>
                                            <input type="hidden" name="NO_ID[]" id="NO_ID{{$no}}" type="text" value="{{$orderkd->NO_ID}}" 
                                            class="form-control NO_ID" onkeypress="return tabE(this,event)" disabled>
                                            <input name="REC[]" id="REC{{$no}}" type="text" value="{{$orderkd->REC}}" class="form-control REC" onkeypress="return tabE(this,event)" disabled>
                                        </td>
                                        <td>
                                            <input name="KD_PRS[]" id="KD_PRS{{$no}}" type="text" class="form-control KD_PRS" placeholder="Proses#" value="{{$orderkd->KD_PRS}}" disabled required>
                                        </td>
                                        <td>
                                            <input name="NA_PRS[]" id="NA_PRS{{$no}}" type="text" class="form-control NA_PRS" value="{{$orderkd->NA_PRS}}" disabled required>
                                        </td>
                                        <td>
                                            <input name="KD_BHN[]" id="KD_BHN{{$no}}" type="text" class="form-control KD_BHN" placeholder="Bahan#" value="{{$orderkd->KD_BHN}}" disabled required>
                                        </td>
                                        <td>
                                            <input name="NA_BHN[]" id="NA_BHN{{$no}}" type="text" class="form-control NA_BHN" value="{{$orderkd->NA_BHN}}" disabled required>
                                        </td>
                                        <td>
                                            <input name="SATUAN[]" id="SATUAN{{$no}}" type="text" class="form-control SATUAN" placeholder="Satuan" value="{{$orderkd->SATUAN}}" disabled required>
                                        </td>
										<td>
											<input name="QTYA[]" onclick="select()" onkeyup="hitung()" id="QTYA{{$no}}" type="text" style="text-align: right"  class="form-control QTYA text-primary" value="{{$orderkd->QTYA}}" disabled>
										</td>      
										<td>
											<input name="QTY[]" onclick="select()" onkeyup="hitung()" id="QTY{{$no}}" type="text" style="text-align: right"  class="form-control QTY text-primary" value="{{$orderkd->QTY}}" disabled>
										</td>   
                                        
										<td>
                                            <input name="KET[]" id="KET{{$no}}" type="text" class="form-control KET" placeholder="Ket" value="{{$orderkd->KET}}" disabled required>
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
                                    <td><input class="form-control TTOTAL_QTY  text-primary font-weight-bold" style="text-align: right"  id="TTOTAL_QTY" name="TTOTAL_QTY" value="{{$header->TOTAL_QTY}}" disabled></td>
                                    <td><input class="form-control TTOTAL_QTYA  text-primary font-weight-bold" style="text-align: right"  id="TTOTAL_QTYA" name="TTOTAL_QTYA" value="{{$header->TOTAL_QTYA}}" disabled></td>
                                    <td></td>
                                </tfoot>
                            </table>   	
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
	
	
	
	
@endsection

@section('footer-scripts')
<!-- TAMBAH 1 -->

<script src="{{ asset('js/autoNumerics/autoNumeric.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<script>

	var idrow = 1;
    function numberWithCommas(x) {
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

// TAMBAH HITUNG
	$(document).ready(function() {

		$("#TTOTAL_QTY").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TTOTAL").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});


		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#HARGA" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#TOTAL" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#QTY" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
		}
		
		$('body').on('click', '.btn-delete', function() {
			var val = $(this).parents("tr").remove();
			idrow--;
			nomor();
		});
		
		$(".date").datepicker({
			'dateFormat': 'dd-mm-yy',
		})
		

		
//////////////////////////////////////////////////////////////////

	}




   
</script>


<script src="autonumeric.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
<script src="https://unpkg.com/autonumeric"></script>

@endsection