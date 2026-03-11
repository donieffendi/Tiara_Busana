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
				
				<form id="entri" action="{{url('jual/posting_faktur')}}" method="POST">																					
                        @csrf
        
                        	<div class="tab-content mt-3">
					
								<div class="col-md-1" align="left">
                                    <label class="form-label">Cari No Jual</label>
                                </div>

								<div class="col-md-3 input-group">

									<input type="text" class="form-control CARI" id="CARI" name="CARI"
                                    placeholder="Cari Bukti#" value="" >
									<button type="button" id='SEARCHX'  onclick="getFaktur()" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>

								</div>
								
							</div>
							

							
							<div style="overflow-y:scroll;" class="col-md-12 scrollable" align="right">
						
								<table id="datatable" class="table table-striped table-border">
									<thead>
										<tr>
											
											<th width="50px" style="text-align:center">#</th>
											<th scope="col" style="text-align: center">Pilih</th>
											<th scope="col" style="text-align: center">No Jual</th>
											<th scope="col" style="text-align: center">Tgl</th>
											<th scope="col" style="text-align: center">No Surats</th>
											<th scope="col" style="text-align: center">Customer</th>
											<th scope="col" style="text-align: center">Nett</th>
											<th scope="col" style="text-align: center">No Faktur</th>
											<th scope="col" style="text-align: center">Tgl Faktur</th>
								
										</tr>
									</thead>
									<tbody id="detailFaktur">
										<tr>
											 <td>
												<input type="hidden" name="NO_ID[]" id="NO_ID0" type="text" value="1" 
												class="form-control NO_ID" onkeypress="return tabE(this,event)" readonly>
												
												<input name="REC[]" id="REC0" type="text" value="1" 
												class="form-control REC" onkeypress="return tabE(this,event)" readonly>
											</td>

											<td>
												<input name="CEKX[]" hidden value="0" id="CEKX0" type="text" style="text-align: right"  class="form-control CEKX text-primary">
												<input name="CEK[]" onchange="rubah(0)" id="CEK0" type="checkbox" value="1" class="form-control CEK"  >
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
												<input name="NO_SURATSX[]" id="NO_SURATSX0" type="text" value=""
												class="form-control NO_SURATSX" readonly >
											</td>

											<td>
												<input name="NAMACX[]" id="NAMACX0" type="text" value=""
												class="form-control NAMACX" readonly >
											</td>
											
											<td>
												<input name='NETTX[]' onblur="hitung()" id='NETTX0' value="0" type='text' style='text-align: right'
												class='form-control NETTX text-primary' readonly required>
											</td>

											<td>
												<input name="NO_FPX[]" id="NO_FPX0" type="text" value=""
												class="form-control NO_FPX" required >
											</td>	
											
											<td>
												<input name='TGL_FPX[]' id='TGL_FPX0' value="" type='text' 
												class='form-control  TGL_FPX' required >
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
		
		
	function getFaktur()
	{
		
		
		var mulai = (idrow==baris) ? idrow-1 : idrow;
		$.ajax(
			{
				type: 'GET',    
				url: "{{url('jual/browse_faktur')}}",
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
                                    <td>
										<input name='CEKX[]' hidden id='CEKX${i}' type='text' value='0' class='form-control  CEKX' required>
										<input name='CEK[]' onchange="rubah(${i})" id='CEK${i}' type='checkbox' value='0' class='form-control  CEK' required>
									</td>
									<td><input name='NO_BUKTIX[]' data-rowid=${i} id='NO_BUKTIX${i}' value="${resp[i].NO_BUKTI}" type='text' class='form-control NO_BUKTIX' readonly ></td>
                                    <td><input name='TGLX[]' data-rowid=${i} id='TGLX${i}' value="${resp[i].TGL}" type='text' class='date form-control text_input' data-date-format='dd-mm-yyyy' required readonly></td>
                                    <td><input name='NO_SURATSX[]' data-rowid=${i} id='NO_SURATSX${i}' value="${resp[i].NO_SURATS}" type='text' class='form-control  NO_SURATSX' required readonly></td>
                                    <td><input name='NAMACX[]' data-rowid=${i} id='NAMACX${i}' value="${resp[i].NAMAC}" type='text' class='form-control  NAMACX' required readonly></td>
                                    <td><input name='NETTX[]' onblur="hitung()" id='NETTX${i}' value="${resp[i].NETT}" type='text' style='text-align: right' class='form-control NETTX text-primary' readonly required></td>
                                    <td><input name='NO_FPX[]' data-rowid=${i} id='NO_FPX${i}' value="${resp[i].NO_FP}" type='text' class='form-control  NO_FPX' placeholder="" required ></td>
                                    <td><input name='TGL_FPX[]' data-rowid=${i} id='TGL_FPX${i}' value="${resp[i].TGL_FP}" type='text' class='date form-control text_input' data-date-format='dd-mm-yyyy' required ></td>
                                    
									</tr>`;
					}
					$('#detailFaktur').html(html);
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
						$("#NO_SURATSX" + i.toString()).attr("readonly", true);
						$("#NAMACX" + i.toString()).attr("readonly", true);
						$("#NETTX" + i.toString()).attr("readonly", true);
						$("#NO_FPX" + i.toString()).attr("readonly", false);
						$("#TGL_FPX" + i.toString()).attr("readonly", false);
						
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
