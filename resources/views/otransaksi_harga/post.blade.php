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
				
				<form id="entri" action="{{url('harga/posting')}}" method="POST">																					
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
											<th scope="col" style="text-align: center">KD BRG</th>
											<th scope="col" style="text-align: center">Barcode</th>
											<th scope="col" style="text-align: center">Nama Barang</th>
											<th scope="col" style="text-align: center">Kode Conter</th>
											<th scope="col" style="text-align: center">Nama Conter</th>
											<th scope="col" style="text-align: center">Harga</th>
								
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
												<input name="KD_BRGX[]" id="KD_BRGX0" type="text" value=""
												class="form-control KD_BRGX" readonly >
											</td>
									
											<td>
												<input name='BARCODEX[]'  id='BARCODEX0' value="" type='text' class='form-control  BARCODEX' 
												required readonly>
											</td>

											<td>
												<input name="NA_BRGX[]" id="NA_BRGX0" type="text" value=""
												class="form-control NA_BRGX" readonly >
											</td>

											<td>
												<input name="CNTX[]" id="CNTX0" type="text" value=""
												class="form-control CNTX" readonly >
											</td>

											<td>
												<input name="NCNTX[]" id="NCNTX0" type="text" value=""
												class="form-control NCNTX" readonly >
											</td>
											
											<td>
												<input name='HJUALX[]' onblur="hitung()" id='HJUALX0' value="0" type='text' style='text-align: right'
												class='form-control HJUALX text-primary' readonly required>
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
				url: "{{url('harga/browse_posting')}}",
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
									<td><input name='KD_BRGX[]' data-rowid=${i} id='KD_BRGX${i}' value="${resp[i].KD_BRG}" type='text' class='form-control KD_BRGX' readonly ></td>
                                    <td><input name='BARCODEX[]' data-rowid=${i} id='BARCODEX${i}' value="${resp[i].BARCODE}" type='text' class='date form-control text_input' data-date-format='dd-mm-yyyy' required readonly></td>
                                    <td><input name='NA_BRGX[]' data-rowid=${i} id='NA_BRGX${i}' value="${resp[i].NA_BRG}" type='text' class='form-control  NA_BRGX' required readonly></td>
                                    <td><input name='CNTX[]' data-rowid=${i} id='CNTX${i}' value="${resp[i].CNT}" type='text' class='form-control  CNTX' required readonly></td>
                                    <td><input name='NCNTX[]' data-rowid=${i} id='NCNTX${i}' value="${resp[i].NCNT}" type='text' class='form-control  NCNTX' required readonly></td>
                                    <td><input name='HJUALX[]' onblur="hitung()" id='HJUALX${i}' value="${resp[i].HJUAL}" type='text' style='text-align: right' class='form-control HJUALX text-primary' readonly required></td>
                                   
								</tr>`;
					}
					$('#detailPosting').html(html);
					
					$(".HJUALX").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".HJUALX").autoNumeric('update');

					
					$(".date").datepicker({
						'dateFormat': 'dd-mm-yy',
					});

					idrow=resp.length;
					baris=resp.length;

				}
			});
		
					jumlahdata = 100;
					for (i = 0; i <= jumlahdata; i++) {
                       
						$("#KD_BRGX" + i.toString()).attr("readonly", true);
						$("#BARCODEX" + i.toString()).attr("readonly", true);
						$("#NA_BRGSX" + i.toString()).attr("readonly", true);
						$("#CNTX" + i.toString()).attr("readonly", true);
						$("#NCNTX" + i.toString()).attr("readonly", true);
						$("#HJUALX" + i.toString()).attr("readonly", true);
						
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
