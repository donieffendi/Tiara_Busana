@extends('layouts.plain')


<style>
    .card {

    }

    .form-control:focus {
        background-color: #E0FFFF !important;
    }
	
	.NAMA_KET {
        background-color: #FFFACD !important;
		
    }
	

	.table-scrollable {
		margin: 0;
		padding: 0;
	}

	table {
		table-layout: fixed !important;
	}
	
</style>

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <!-- /.col -->
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
				
				<form id="entri" action="{{url('beli/posting')}}" method="POST">																					
                        @csrf
        
                        	<div class="tab-content mt-3">
					
								<div class="col-md-1" align="left">
                                    <label class="form-label">Cari</label>
                                </div>

								<div class="col-md-3 input-group">

									<input type="text" class="form-control CARI" id="CARI" name="CARI"
                                    placeholder="Cari Bukti#" value="" >
									<button type="button" id='SEARCHX'  onclick="getPost()" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>

								</div>
								
							</div>
							

							
							<div style="overflow-y:scroll;" class="col-md-12 scrollable" align="right">
						
								<table id="datatable" class="table table-striped table-border">
									<thead>
										<tr>
											
											<th width="50px" style="text-align:center">#</th>
											<th scope="col" style="text-align: center">No Bukti</th>
											<th scope="col" style="text-align: center">Tgl</th>
											<th scope="col" style="text-align: center">Suplier</th>
											<th scope="col" style="text-align: center">Total Qty</th>
											<th scope="col" style="text-align: center">Total/Bruto</th>
											<th scope="col" style="text-align: center">Total Nett</th>
											<th scope="col" style="text-align: center">Notes</th>
											<th scope="col" style="text-align: center">Type</th>
											<th scope="col" style="text-align: center">Cek</th>
								
										</tr>
									</thead>
									<tbody id="detailPosting">
										<tr>
											 <td>
												<input type="hidden" name="NO_ID[]" id="NO_ID0" type="text" value="1" 
												class="form-control NO_ID" onkeypress="return tabE(this,event)" readonly>
												
												<input name="REC[]" id="REC0" type="text" value="1" 
												class="form-control REC" onkeypress="return tabE(this,event)" readonly>
											</td>
				
											<td>
												<input name="NO_BUKTIX[]" id="NO_BUKTIX0" type="text" value=""
												class="form-control NO_BUKTIX" readonly >
											</td>
									
											<td>
												<input name='TGLX[]'  id='TGLX0' value="" type='text' class='form-control  TGLX' 
												required readonly>
											</td>

											<td>
												<input name="NAMASX[]" id="NAMASX0" type="text" value=""
												class="form-control NAMASX" readonly >
											</td>
											
											<td>
												<input name='TOTAL_QTYX[]' onblur="hitung()" id='TOTAL_QTYX0' value="0" type='text' style='text-align: right'
												class='form-control TOTAL_QTYX text-primary' readonly required>
											</td>
											
											<td>
												<input name='TOTALX[]' onblur="hitung()" id='TOTALX0' value="0" type='text' style='text-align: right'
												class='form-control TOTALX text-primary' readonly required>
											</td>
											
											<td>
												<input name='NETTX[]' onblur="hitung()" id='NETTX0' value="0" type='text' style='text-align: right'
												class='form-control NETTX text-primary' readonly required>
											</td>

											<td>
												<input name="NOTESX[]" id="NOTESX0" type="text" value=""
												class="form-control NOTESX" readonly >
											</td>

											<td>
												<input name="TYPEX[]" id="TYPEX0" type="text" value=""
												class="form-control TYPEX" readonly >
											</td>

											<td>
												<input name="CEKX[]" hidden value="0" id="CEKX0" type="text" style="text-align: right"  class="form-control CEKX text-primary">
												<input name="CEK[]" onchange="rubah(0)" id="CEK0" type="checkbox" value="1" class="form-control CEK"  >
											</td>
											
										</tr>
										
									</tbody>
									<tfoot>
										<td></td>
										<td></td>
										<td></td>
									</tfoot>
								</table>     
							                           
							</div>


						   
						   
                            <div class="col-md-2 row">
									<button type="button" id='simpan'  onclick="simpanx()" class="btn btn-outline-primary"></i>Proses</button>
							</div>
						
						
                    </form>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>
	
	

	

@endsection

@section('footer-scripts')
<!-- TAMBAH 1 -->

<script src="{{ asset('js/autoNumerics/autoNumeric.min.js') }}"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script> -->
<script src="{{asset('foxie_js_css/bootstrap.bundle.min.js')}}"></script>

<script>

	var idrow = 1;
	var baris = 1;
    function numberWithCommas(x) {
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

// TAMBAH HITUNG
	$(document).ready(function() {
		
	});


	function simpanx()
    { 
	        
		
			document.getElementById("entri").submit();
		
	}
		
		
	function getPost()
	{
		
		
		var mulai = (idrow==baris) ? idrow-1 : idrow;
		$.ajax(
			{
				type: 'GET',    
				url: "{{url('beli/browse_posting')}}",
				data: {
					CARI: $('#CARI').val(),	
				},
				success: function( resp )
				{
///////////////////////////////////////
                   
					var html = '';
					for(i=0; i<resp.length; i++){
						html+=`<tr>
                                    <td>
										<input name='NO_ID[]' hidden id='NO_ID${i}' value="${resp[i].NO_ID}" type='text' class='NO_ID form-control' onkeypress='return tabE(this,event)' hidden readonly>
										<input name='REC[]' id='REC${i}' value="${i+1}" type='text' class='REC form-control' onkeypress='return tabE(this,event)' readonly>
									</td>
									<td><input name='NO_BUKTIX[]' data-rowid=${i} id='NO_BUKTIX${i}' value="${resp[i].NO_BUKTI}" type='text' class='form-control NO_BUKTIX' readonly ></td>
                                    <td><input name='TGLX[]' data-rowid=${i} id='TGLX${i}' value="${resp[i].TGL}" type='text' class='date form-control text_input' data-date-format='dd-mm-yyyy' required readonly></td>
                                    <td><input name='NAMASX[]' data-rowid=${i} id='NAMASX${i}' value="${resp[i].NAMAS}" type='text' class='form-control  NAMASX' required readonly></td>
                                    <td><input name='TOTAL_QTYX[]' onblur="hitung()" id='TOTAL_QTYX${i}' value="${resp[i].TOTAL_QTY}" type='text' style='text-align: right' class='form-control TOTAL_QTYX text-primary' readonly required></td>
                                    <td><input name='TOTALX[]' onblur="hitung()" id='TOTALX${i}' value="${resp[i].TOTAL}" type='text' style='text-align: right' class='form-control TOTALX text-primary' readonly required></td>
                                    <td><input name='NETTX[]' onblur="hitung()" id='NETTX${i}' value="${resp[i].NETT}" type='text' style='text-align: right' class='form-control NETTX text-primary' readonly required></td>
                                    <td><input name='NOTESX[]' data-rowid=${i} id='NOTESX${i}' value="${resp[i].NOTES}" type='text' class='form-control  NOTESX' required readonly></td>
                                    <td><input name='TYPEX[]' data-rowid=${i} id='TYPEX${i}' value="${resp[i].TYPE}" type='text' class='form-control  TYPEX' required readonly></td>
                                    
                                    <td>
										<input name='CEKX[]' hidden id='CEKX${i}' type='text' value='0' class='form-control  CEKX' required>
										<input name='CEK[]' onchange="rubah(${i})" id='CEK${i}' type='checkbox' value='0' class='form-control  CEK' required>
									</td>
								</tr>`;
					}
					$('#detailPosting').html(html);
					
					$(".TOTAL_QTYX").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".TOTAL_QTYX").autoNumeric('update');
					$(".TOTALX").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".TOTALX").autoNumeric('update');
					$(".NETTX").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".NETTX").autoNumeric('update');

					
					$(".date").datepicker({
						'dateFormat': 'dd-mm-yy',
					});

					idrow=resp.length;
					baris=resp.length;

				}
			});
		
					jumlahdata = 100;
					for (i = 0; i <= jumlahdata; i++) {
                       
						$("#NO_BUKTIX" + i.toString()).attr("readonly", true);
						$("#TGLX" + i.toString()).attr("readonly", true);
						$("#NAMASSX" + i.toString()).attr("readonly", true);
						$("#TOTAL_QTYX" + i.toString()).attr("readonly", true);
						$("#TOTALX" + i.toString()).attr("readonly", true);
						$("#NETTX" + i.toString()).attr("readonly", true);
						$("#NOTESX" + i.toString()).attr("readonly", true);
						$("#TYPEX" + i.toString()).attr("readonly", true);
						
					}

		
		
	}
	
	
	function rubah(no){

		var cek = document.getElementById("CEK"+no);

		if (cek.checked){
			$('#CEKX'+no).val(1);
		}else{
			$('#CEKX'+no).val(0);
		}
		

		
	}
	
	
	function hitung() {
		

	}







//////////////////////////////////////////////////////////////////

	
</script>
@endsection
