@extends('layouts.plain')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .card {

    }

    .form-control:focus {
        background-color: #E0FFFF !important;
    }

	/* perubahan tab warna di form edit  */
	.nav-item .nav-link.active {
		background-color: red !important; /* Use !important to ensure it overrides */
		color: white !important;
	}


	/* query LOADX */

	.loader {
      position: fixed;
        top: 50%;
        left: 50%;
      width: 100px;
      aspect-ratio: 1;
      background:
        radial-gradient(farthest-side,#ffa516 90%,#0000) center/16px 16px,
        radial-gradient(farthest-side,green   90%,#0000) bottom/12px 12px;
      background-repeat: no-repeat;
      animation: l17 1s infinite linear;
      position: relative;
    }
    .loader::before {    
      content:"";
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
      100%{transform: rotate(1turn)}
    }

	/* penutup LOADX */

    /* menghilangkan padding */
    .content-header {
        padding: 0 !important;
    }

	.uppercase {
		text-transform: uppercase;
	}

	.scrollable table {
		width: 100%;
		table-layout: fixed;
		border-collapse: collapse;
	}

	/* .scrollable tbody {
		display: block;
		height: 350px;
		overflow-y: auto;
	}
	.scrollable thead, .scrollable tbody tr {
		display: table;
		width: 100%;
		table-layout: fixed;
	} */



    .table th, .table td {
        white-space: nowrap; /* Prevents text from wrapping in columns */
        overflow: hidden; /* Hide overflow text that doesn't fit */
        text-overflow: ellipsis; /* Optionally show ellipsis when the content overflows */
    }
</style>

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">

        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="content">
        <div class="container-fluid">
        <div class="row">
            <div class="col-12">
            <div class="card">
                <div class="card-body">

                   <form action="{{($tipx=='new')? url('/so/store?flagz='.$flagz.'&golz='.$golz.'') : url('/so/update/'.$header->NO_ID.'&flagz='.$flagz.'&golz='.$golz.'' ) }}" method="POST" name ="entri" id="entri" >
  
	    			      @csrf
						  
						<ul class="nav nav-tabs">
                            <li class="nav-item active">
                                <a class="nav-link active" href="#main" data-toggle="tab">SO</a>
                            </li>
                            <li {{( $golz =='D') ? '' : 'hidden' }} class="nav-item">
                                <a class="nav-link" href="#dropship" data-toggle="tab" >Dropship</a>
                            </li>
                        </ul>

        
                        <div class="tab-content mt-3">
							

							<div id="main" class="tab-pane active">

								<!-- style text box model baru -->

								<style>
									/* Ensure specificity with class targeting */
									.form-group.special-input-label {
										position: relative;
										margin-left: 5px ;
									}
							
									/* Ensure only bottom border for input */
									.form-group.special-input-label input {
										width: 100%;
										padding: 10px 0;
										border: none !important;
										border-bottom: 2px solid #ccc !important;
										outline: none !important;
										font-size: 16px !important;
										background: transparent !important; /* Remove any background color */
									}
							
									/* Bottom border color change on focus */
									.form-group.special-input-label input:focus {
										border-bottom: 2px solid #007BFF !important; /* Change color on focus */
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
									.form-group.special-input-label input:focus + label,
									.form-group.special-input-label input:not(:placeholder-shown) + label {
										top: -10px !important;
										font-size: 12px !important;
										color: #007BFF !important;
									}
								</style>

								<!-- tutupannya -->
								
								<div class="form-group row">
									<div class="col-md-1">
										<label for="NO_BUKTI" class="form-label">Bukti#</label>
									</div>
									

									<input type="text" class="form-control NO_ID" id="NO_ID" name="NO_ID"
										placeholder="Masukkan NO_ID" value="{{$header->NO_ID ?? ''}}" hidden readonly>

										
										<input name="tipx" class="form-control tipx" id="tipx" value="{{$tipx}}" hidden>
										<input name="flagz" class="form-control flagz" id="flagz" value="{{$flagz}}" hidden>
										<input name="golz" class="form-control golz" id="golz" value="{{$golz}}" hidden>

									
									<div class="col-md-2">
										<input type="text" class="form-control NO" id="NO_BUKTI" name="NO_BUKTI"
										placeholder="Masukkan Bukti#" value="{{$header->NO_BUKTI}}" readonly >
									</div>
			
									<div class="col-md-1">
										<label for="TGL" class="form-label">Tgl</label>
									</div>
									<div class="col-md-2">
									
									<input class="form-control date" id="TGL" onchange="jtempo()" name="TGL" data-date-format="dd-mm-yyyy" type="text" autocomplete="off" value="{{date('d-m-Y',strtotime($header->TGL))}}">
									
									</div>
			
									<div class="col-md-1">
										<label for="JTEMPO" class="form-label">Perkiraan Kirim</label>
									</div>
									<div class="col-md-2">
									
									<input class="form-control date" id="JTEMPO" name="JTEMPO" data-date-format="dd-mm-yyyy" type="text" autocomplete="off" value="{{date('d-m-Y',strtotime($header->JTEMPO))}}" readonly >
									
									</div>
								</div>
			
								
								<div class="form-group row">
									<div class="col-md-1">	
										<label for="KODEC" class="form-label">Customer#</label>
									</div>
									
									<div class="col-md-3" >
									<select id="KODEC"  onchange="ambil_hari()" name="KODEC" style="width: 100%" ></select>        							 
									
										<input type="text" hidden class="form-control NAMAC" id="NAMAC" name="NAMAC" placeholder="" value="{{$header->NAMAC}}" readonly>
										<input type="text" hidden class="form-control HARI" id="HARI" name="HARI" value="{{$header->HARI}}" placeholder="Masukkan Hari" >
										<input type="text" hidden class="form-control LIM" id="LIM" name="LIM" value="{{$header->LIM}}" placeholder="" >
										<input type="text" hidden class="form-control AK12" id="AK12" name="AK12" value="{{$header->AK12}}" placeholder="" >

									</div>
			
									<div class="col-md-1" >
										<input type="checkbox" class="form-check-input" id="PKP" name="PKP" readonly value="{{$header->PKP}}" {{ ($header->PKP == 1) ? 'checked' : '' }}>
										<label for="PKP" class="form-label">Pkp</label>
										
								        <input type="text" hidden class="form-control ZPKP" id="ZPKP" name="ZPKP" value="{{$header->PKP}}" placeholder="Masukkan Pkp" >
									</div>
			
									<div class="col-md-1">								
										<label for="KODEP" class="form-label">Sales</label>
									</div>
									<div class="col-md-2 input-group" >
										<input type="text" hidden class="form-control KODEP" id="KODEP" name="KODEP" placeholder=""value="{{$header->KODEP}}" style="text-align: left" readonly >
										<input type="text" class="form-control NAMAP" id="NAMAP" name="NAMAP" placeholder="" value="{{$header->NAMAP}}" readonly>
										<input hidden type="text" onclick="select()" class="form-control KOM" id="KOM" name="KOM" placeholder="Masukkan KOM" 
										value="{{ number_format( $header->KOM, 0, '.', ',') }}" style="text-align: right" >
									</div>

									<div class="col-md-1">								
										<label for="RING" class="form-label">Ring</label>
									</div>
									<div class="col-md-1" >
										<input type="text" class="form-control RING" id="RING" name="RING" placeholder=""value="{{$header->RING}}" style="text-align: left" readonly >
									</div>
									
			
								</div>
								

								<!-- loader tampil di modal  -->
								<div class="loader" style="z-index: 1055;" id='LOADX' ></div>
								
							
								
								<div class="form-group row">

									<!-- code text box baru -->
									<div class="col-md-5 form-group row special-input-label">

										<input type="text" class="NOTES" id="NOTES" name="NOTES" 
											value="{{$header->NOTES}}" placeholder=" " >
										<label for="NOTES">Notes</label>
									</div>
									<!-- tutupannya -->

								</div>
								
								
							
								<div class="scrollable" style="height:400px; overflow-y:scroll;">
	
									<table id="datatable" class="table table-striped table-border">
										<thead>
											<tr>
												<th width="50px" style="text-align:center">No.</th>
												<th {{( $golz =='J' || $golz =='D' ) ? '' : 'hidden' }} width="150px">
													<label style="color:red;font-size:20px">*</label>
													<label for="KD_BRG" class="form-label">Barang</label>
												</th>
												<th {{( $golz =='J' || $golz =='D' ) ? '' : 'hidden' }} width="300px" style="text-align:center">Nama</th>
												<th width="150px">
													<label style="color:red;font-size:20px">*</label>
													<label for="SATUAN" class="form-label">Satuan</label>
												</th>
												<th width="150px" style="text-align:center">Qty X</th> 
												<th width="150px" style="text-align:center">X</th> 
												<th width="150px" style="text-align:center">Qty</th> 
												<th width="150px" style="text-align:center">Sedia(-SO)</th> 
												<th width="150px" style="text-align:center">Harga</th>    
												<th width="150px" style="text-align:center">Total</th>                            
												<th width="150px" style="text-align: center;">Disk 1</th>
												<th width="150px" style="text-align: center;">Disk 2</th>
												<th width="150px" style="text-align: center;">Disk 3</th>
												<th width="150px" style="text-align: center;">Disk 4</th>
												<th width="150px" style="text-align: center;">Disk 5</th>
												<th width="150px" style="text-align: center;">Disk (Rp)</th>
												<th></th>                                  
											</tr>
										</thead>
										<tbody>
										
										<?php $no=0 ?>
										@foreach ($detail as $detail)
											<tr>
												<td>
													<input type="hidden" name="NO_ID[]" id="NO_ID{{$no}}" type="text" value="{{$detail->NO_ID}}" 
													class="form-control NO_ID" onkeypress="return tabE(this,event)" readonly>
													
													<input name="REC[]" id="REC{{$no}}" type="text" value="{{$detail->REC}}" class="form-control REC" onkeypress="return tabE(this,event)" readonly>
												</td>

												<td {{( $golz =='J' || $golz =='D' ) ? '' : 'hidden' }}>
													<input name="KD_BRG[]" id="KD_BRG{{$no}}" type="text" class="form-control KD_BRG " 
													value="{{$detail->KD_BRG}}" onblur="browseBarangx({{$no}})">
													<input hidden name="KD_GRUP[]" id="KD_GRUP{{$no}}" type="text" class="form-control KD_GRUP " 
													value="{{$detail->KD_GRUP}}" >
												</td>

												<td {{( $golz =='J' || $golz =='D' ) ? '' : 'hidden' }}>
													<input name="NA_BRG[]" id="NA_BRG{{$no}}" type="text" class="form-control NA_BRG " value="{{$detail->NA_BRG}}">
												</td>
												<td>
													<input name="SATUAN[]" onclick="browseBarangdz({{$no}})" id="SATUAN{{$no}}" type="text" value="{{$detail->SATUAN}}" class="form-control SATUAN">
													<input name="AHARGA[]" hidden onclick='select()' onblur="hitung()" value="{{$detail->AHARGA}}" id="AHARGA{{$no}}" type="text" style="text-align: right"  class="form-control AHARGA text-primary" >
													<input name="BHARGA[]" hidden onclick='select()' onblur="hitung()" value="{{$detail->BHARGA}}" id="BHARGA{{$no}}" type="text" style="text-align: right"  class="form-control BHARGA text-primary" >
													<input name="CHARGA[]" hidden onclick='select()' onblur="hitung()" value="{{$detail->CHARGA}}" id="CHARGA{{$no}}" type="text" style="text-align: right"  class="form-control CHARGA text-primary" >
													<input name="DHARGA[]" hidden onclick='select()' onblur="hitung()" value="{{$detail->DHARGA}}" id="DHARGA{{$no}}" type="text" style="text-align: right"  class="form-control DHARGA text-primary" >
													<input name="EHARGA[]" hidden onclick='select()' onblur="hitung()" value="{{$detail->EHARGA}}" id="EHARGA{{$no}}" type="text" style="text-align: right"  class="form-control EHARGA text-primary" >
													<input name="FHARGA[]" hidden onclick='select()' onblur="hitung()" value="{{$detail->FHARGA}}" id="FHARGA{{$no}}" type="text" style="text-align: right"  class="form-control FHARGA text-primary" >
													<input name="GHARGA[]" hidden onclick='select()' onblur="hitung()" value="{{$detail->GHARGA}}" id="GHARGA{{$no}}" type="text" style="text-align: right"  class="form-control GHARGA text-primary" >

													<input name="TYPE_KOM[]" hidden id="TYPE_KOM{{$no}}" type="text" value="{{$detail->TYPE_KOM}}" class="form-control TYPE_KOM" >
													<input name="KOM[]" hidden onclick='select()' onblur="hitung()" value="{{$detail->KOM}}" id="KOM{{$no}}" type="text" style="text-align: right"  class="form-control KOM text-primary" >
													<input name="TKOM[]" hidden onclick='select()' onblur="hitung()" value="{{$detail->TKOM}}" id="TKOM{{$no}}" type="text" style="text-align: right"  class="form-control TKOM text-primary" >
													<input name="LOKASI[]" hidden id="LOKASI{{$no}}" type="text" value="{{$detail->LOKASI}}" class="form-control LOKASI">
												
													<input name="BERAT[]" hidden onclick='select()' onblur="hitung()" value="{{$detail->BERAT}}" id="BERAT{{$no}}" type="text" style="text-align: right"  class="form-control BERAT text-primary" >
													<input name="TBERAT[]" hidden onclick='select()' onblur="hitung()" value="{{$detail->TBERAT}}" id="TBERAT{{$no}}" type="text" style="text-align: right"  class="form-control TBERAT text-primary" >
												
													<input name="XSO[]" hidden onclick='select()' onblur="hitung()" value="{{$detail->XSO}}" id="XSO{{$no}}" type="text" style="text-align: right"  class="form-control XSO text-primary" >
												</td>
												
												<td>
													<input name="XQTY[]"  onblur="hitung()" value="{{$detail->QTY2}}" id="XQTY{{$no}}" type="text" style="text-align: right"  class="form-control XQTY text-primary" >
												</td>
					
												<td>
													<input name="KALI[]"  onblur="hitung()" value="{{$detail->KALI}}" id="KALI{{$no}}" type="text" style="text-align: right"  class="form-control KALI text-primary" >
												</td>
												<td>
													<input name="QTY[]"  onblur="hitung()" value="{{$detail->QTY}}" id="QTY{{$no}}" type="text" style="text-align: right"  class="form-control QTY text-primary" >
												</td>
												<td>
													<input name="SEDIA[]" onclick='select()' onblur="hitung()" value="{{$detail->SEDIA}}" id="SEDIA{{$no}}" type="text" style="text-align: right"  class="form-control SEDIA text-primary" >
												</td>
												<td>
													<input name="HARGA[]" onclick='select()' onblur="hitung()" value="{{$detail->HARGA}}" id="HARGA{{$no}}" type="text" style="text-align: right"  class="form-control HARGA text-primary"  {{ ($golz == 'D') ? '' : 'readonly' }} >
												</td>

												<td><input name="TOTAL[]" onclick='select()' onblur="hitung()" value="{{$detail->TOTAL}}" id="TOTAL{{$no}}" type="text" style="text-align: right"  class="form-control TOTAL text-primary"  >
													<input name="PPNX[]" hidden onblur="hitung()" value="{{$detail->PPN}}" id="PPNX{{$no}}" type="text" style="text-align: right"  class="form-control PPNX text-primary"  >
													<input name="DPP[]" hidden onblur="hitung()" value="{{$detail->DPP}}" id="DPP{{$no}}" type="text" style="text-align: right"  class="form-control DPP text-primary"  >
												</td> 

												<td>
													<input name="DISKA[]" onblur="hitung()"  value="{{$detail->DISK1}}" id="DISKA{{$no}}" 
													type="text" style="text-align: right"  class="form-control DISKA">
												</td>
												<td>
													<input name="DISKB[]" onblur="hitung()"  value="{{$detail->DISK2}}" id="DISKB{{$no}}" 
													type="text" style="text-align: right"  class="form-control DISKB">
												</td>
												<td>
													<input name="DISKC[]" onblur="hitung()"  value="{{$detail->DISK3}}" id="DISKC{{$no}}" 
													type="text" style="text-align: right"  class="form-control DISKC">
												</td>
												<td>
													<input name="DISKD[]" onblur="hitung()"  value="{{$detail->DISK4}}" id="DISKD{{$no}}" 
													type="text" style="text-align: right"  class="form-control DISKD">
												</td>
												<td>
													<input name="DISKE[]" onblur="hitung()"  value="{{$detail->DISK5}}" id="DISKE{{$no}}" 
													type="text" style="text-align: right"  class="form-control DISKE">
												</td>
												
												<td>
													<input name="DISK[]" onblur="hitung()"  value="{{$detail->DISK}}" id="DISK{{$no}}" type="text" style="text-align: right"  class="form-control DISK">
													<input name="KET[]" hidden id="KET{{$no}}" type="text" value="{{$detail->KET}}" class="form-control KET" >
												</td>      
													
												<td>
													<button type='button' id='DELETEX{{$no}}'  class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button>
												</td>    
											</tr>
											
										<?php $no++; ?>		
										@endforeach
										</tbody>
										<tfoot>
											<td></td>
											<td {{( $golz =='J' || $golz =='D' ) ? '' : 'hidden' }}></td>
											<td {{( $golz =='J' || $golz =='D' ) ? '' : 'hidden' }}></td>
											<td></td>	
											<td></td>
											<td></td>
											<!-- <td><input class="form-control TTOTAL  text-primary font-weight-bold" style="text-align: right"  id="TTOTAL" name="TTOTAL" value="{{$header->TOTAL}}" readonly></td> -->
											<td></td>
										</tfoot>
									</table>				
								</div>
										
								<div class="tab-content mt-6">

									<div class="form-group row">
										
										<div class="col-md-1" align="right">
											<a type="button" id='PLUSX' onclick="tambah()" class="fas fa-plus fa-sm md-3" style="font-size: 20px" ></a>
										</div>

										<div class="col-md-3" align="right"  >
											<label for="TTOTAL_QTY2" class="form-label">Total Qty X</label>
										</div>
										<div class="col-md-1"  >
											<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TTOTAL_QTY2" id="TTOTAL_QTY2" name="TTOTAL_QTY2" placeholder="" value="{{$header->TOTAL_QTY2}}" style="text-align: right" readonly>
										</div>

										<div class="col-md-1" align="right" >
											<label for="TTOTAL_QTY" class="form-label">Total Qty</label>
										</div>
										<div class="col-md-1"  >
											<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TTOTAL_QTY" id="TTOTAL_QTY" name="TTOTAL_QTY" placeholder="" value="{{$header->TOTAL_QTY}}" style="text-align: right" readonly>
										</div>
										
										<div class="col-md-2" align="right">
											<label for="TTOTAL" class="form-label">Total</label>
										</div>
										<div class="col-md-2">
											<input type="text" onclick="select()" onkeyup="hitung()" class="form-control TTOTAL" id="TTOTAL" name="TTOTAL" placeholder="" value="{{$header->TOTAL}}" style="text-align: right" readonly>
			
										</div>
									</div>
									
									<div class ="form-group row">
									    <div class="col-md-9" align="right">
											<label for="TDPP" class="form-label">DPP</label>
										</div>
										<div class="col-md-2">
											<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TTOTAL" id="TDPP" name="TDPP" placeholder="" value="{{$header->TDPP}}" style="text-align: right" readonly>
			
										</div>
									</div>

									<div class="form-group row">


										<div class="col-md-1" align="right" hidden >
										</div>

										<div class="col-md-2" align="right" hidden>
											<label for="TOTAL_TKOM" class="form-label">Total Komisi</label>
										</div>
										<div class="col-md-2" hidden>
											<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TOTAL_TKOM" id="TOTAL_TKOM" name="TOTAL_TKOM" placeholder="" value="{{$header->TOTAL_TKOM}}" style="text-align: right" readonly>
										</div>

										<div class="col-md-9" align="right" hidden>
											<label for="TDISK" class="form-label">Total Diskon</label>
										</div>
										<div class="col-md-2" hidden>
											<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TDISK" id="TDISK" name="TDISK" placeholder="" value="{{$header->TDISK}}" style="text-align: right" readonly>
										</div>
									</div>
									

									<div class="form-group row">
										<div class="col-md-9" align="right">
											<label for="DISK_GLOBAL" class="form-label">Disk Global</label>
										</div>
										<div class="col-md-2">
											<input type="text"  onclick="select()" onblur="hitung()" class="form-control DISK_GLOBAL" id="DISK_GLOBAL" name="DISK_GLOBAL" placeholder="" value="{{$header->DISK_GLOBAL}}" style="text-align: right" >
										</div>
									</div>
									
									<div class="form-group row">

									
										<div class="col-md-1" align="right" hidden >
										</div>

										<div class="col-md-1" align="right">								
											<label for="NOTES2" class="form-label">Notes 2</label>
										</div>
										<div class="col-md-3 input-group" >
											<input type="text" class="form-control uppercase NOTES2" id="NOTES2" name="NOTES2" placeholder=""value="{{$header->NOTES2}}" style="text-align: left" >
										</div>

										<div class="col-md-5" align="right">
											<label for="TPPN" class="form-label">Ppn</label>
										</div>
										<div class="col-md-2">
											<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TPPN" id="TPPN" name="TPPN" placeholder="" value="{{$header->TPPN}}" style="text-align: right" readonly>
										</div>
										
									</div>
									
									<div class="form-group row">
										<div class="col-md-9" align="right">
											<label for="NETT" class="form-label">Nett</label>
										</div>
										<div class="col-md-2">
											<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control NETT" id="NETT" name="NETT" placeholder="" value="{{$header->NETT}}" style="text-align: right" readonly>
										</div>
									</div>
									
								</div>
									
							</div>
							
							<!----tutup page so--->	

								<div {{( $golz =='D' ) ? '' : 'hidden' }} id="dropship" class="tab-pane">

									<div {{( $golz =='D' ) ? '' : 'hidden' }}class="form-group row">

										<!-- code text box baru -->
										<div class="col-md-5 form-group row special-input-label">

											<input type="text" class="NAMAC_2" id="NAMAC_2" name="NAMAC_2" 
												value="{{$header->NAMAC_2}}" placeholder=" " >
											<label for="NAMAC_2">Nama</label>
										</div>
										<!-- tutupannya -->

										<div class="col-md-1" align="left">
										</div>

										<!-- code text box baru -->
										<div class="col-md-5 form-group row special-input-label">

											<input type="text" class="ALAMAT_2" id="ALAMAT_2" name="ALAMAT_2" 
												value="{{$header->ALAMAT_2}}" placeholder=" " >
											<label for="ALAMAT_2">Alamat</label>
										</div>
										<!-- tutupannya -->

										<div class="col-md-1" align="left">
										</div>

										<!-- code text box baru -->
										<div class="col-md-5 form-group row special-input-label">

											<input type="text" class="KOTA_2" id="KOTA_2" name="KOTA_2" 
												value="{{$header->KOTA_2}}" placeholder=" " >
											<label for="KOTA_2">Kota</label>
										</div>
										<!-- tutupannya -->
									</div>
											
								</div>
										
								<div class="mt-3 col-md-12 form-group row">
									<div class="col-md-4">
										<button hidden type="button" id='TOPX'  onclick="location.href='{{url('/so/edit/?idx=' .$idx. '&tipx=top&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-outline-primary">Top</button>
										<button hidden type="button" id='PREVX' onclick="location.href='{{url('/so/edit/?idx='.$header->NO_ID.'&tipx=prev&flagz='.$flagz.'&golz='.$golz.'&buktix='.$header->NO_BUKTI )}}'" class="btn btn-outline-primary">Prev</button>
										<button hidden type="button" id='NEXTX' onclick="location.href='{{url('/so/edit/?idx='.$header->NO_ID.'&tipx=next&flagz='.$flagz.'&golz='.$golz.'&buktix='.$header->NO_BUKTI )}}'" class="btn btn-outline-primary">Next</button>
										<button hidden type="button" id='BOTTOMX' onclick="location.href='{{url('/so/edit/?idx=' .$idx. '&tipx=bottom&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-outline-primary">Bottom</button>
									</div>
									<div class="col-md-5">
										<button hidden type="button" id='NEWX' onclick="location.href='{{url('/so/edit/?idx=0&tipx=new&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-warning">New</button>
										<button hidden type="button" id='EDITX' onclick='hidup()' class="btn btn-secondary">Edit</button>                    
										<button hidden type="button" id='UNDOX' onclick="location.href='{{url('/so/edit/?idx=' .$idx. '&tipx=undo&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-info">Undo</button>  
										<button type="button" id='SAVEX' onclick='simpan()'   class="btn btn-success" class="fa fa-save"></i>Save</button>

									</div>
									<div class="col-md-3">
										<button hidden type="button" id='HAPUSX'  onclick="hapusTrans()" class="btn btn-outline-danger">Hapus</button>
										
										<!-- <button type="button" id='CLOSEX'  onclick="location.href='{{url('/so?flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-outline-secondary">Close</button> -->
										
										<!-- tombol close sweet alert -->
										<button type="button" id='CLOSEX' onclick="closeTrans()" class="btn btn-outline-secondary">Close</button></div>
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
	

 	<div class="modal fade" id="browseCustModal" tabindex="-1" role="dialog" aria-labelledby="browseCustModalLabel" aria-hidden="true">
	  <div class="modal-dialog mw-100 w-75" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseCustModalLabel">Cari Customer</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bcust">
				<thead>
					<tr>
						<th>Customer</th>
						<th>Nama</th>
						<th>Alamat</th>
						<th>Kota</th>
						<th>Status PKP</th>
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
	

	<div class="modal fade" id="browseBarangxModal" tabindex="-1" role="dialog" aria-labelledby="browseBarangxModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseBarangxModalLabel">Cari Item</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bbarangx">
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

	<div class="modal fade" id="browseBarangdzModal" tabindex="-1" role="dialog" aria-labelledby="browseBarangdzModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseBarangdzModalLabel">Cari Satuan</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bbarangdz">
				<thead>
					<tr>
						<th>Item</th>
						<th>Nama</th>
						<th>Satuan</th>
						<th>Kali</th>
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



	<div class="modal fade" id="browseBahanModal" tabindex="-1" role="dialog" aria-labelledby="browseBahanModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseBahanModalLabel">Cari Item</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bbahan">
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
	
	
@endsection

@section('footer-scripts')
<!-- TAMBAH 1 -->

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="{{ asset('js/autoNumerics/autoNumeric.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- tambahan untuk sweetalert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- tutupannya -->

<script>

	var idrow = 1;
	var baris = 1;
    function numberWithCommas(x) {
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

// TAMBAH HITUNG
	$(document).ready(function() {

		setTimeout(function(){

		$("#LOADX").hide();

		},500);
		
		idrow=<?php echo $no; ?>;
		baris=<?php echo $no; ?>;

     $('#KODEC').select2({
		
		placeholder:'Pilih Customer',
		allowClear: true,
        ajax: {
			url: '{{url('cust/browse')}}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term // Search term
                };
            },
            processResults: function(data) {
                return {
                    results: data.map(item => ({
                        id: item.KODEC, // The ID of the user
                        text: item.NAMAC // The text to display
                    }))
                };
            },
            cache: true
        },
		
		
		
	});
	

		$('body').on('keydown', 'input, select', function(e) {
			if (e.key === "Enter") {
				var self = $(this), form = self.parents('form:eq(0)'), focusable, next;
				focusable = form.find('input,select,textarea').filter(':visible');
				next = focusable.eq(focusable.index(this)+1);
				console.log(next);
				if (next.length) {
					next.focus().select();
				} else {
					tambah();
					// var nomer = idrow-1;
					// console.log("REC"+nomor);
					// document.getElementById("REC"+nomor).focus();
					// form.submit();
				}
				return false;
			}
		});

		$tipx = $('#tipx').val();
		$searchx = $('#CARI').val();
		
		
        if ( $tipx == 'new' )
		{
			 baru();	
             tambah();				 
		}

        if ( $tipx != 'new' )
		{
			 ganti();			
			 
			
                
		    	var initkode1 ="{{ $header->KODEC }}";                
			    var initcombo1 ="{{ $header->NAMAC }}";
				var defaultOption1 = { id: initkode1, text: initcombo1 }; // Set your default option ID and text
                var newOption1 = new Option(defaultOption1.text, defaultOption1.id, true, true);
                $('#KODEC').append(newOption1).trigger('change');
			 
			 
		}    
		
		$("#TTOTAL_QTY2").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TTOTAL_QTY").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TTOTAL").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TDISK").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TPPN").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TDPP").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#NETT").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#DISK_GLOBAL").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TOTAL_TKOM").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#LIM").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#AK12").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});

		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#XQTY" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#KALI" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#QTY" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#HARGA" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#TOTAL" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#PPNX" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#DPP" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#DISKA" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#DISKB" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#DISKC" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#DISKD" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#DISKE" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#DISK" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#TKOM" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#KOM" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#BERAT" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#TBERAT" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#SEDIA" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#XSO" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});

		}
		
		$('body').on('click', '.btn-delete', function() {
			var val = $(this).parents("tr").remove();
			baris--;
			hitung();
			nomor();
		});
		
		$(".date").datepicker({
			'dateFormat': 'dd-mm-yy',
		})
		
		
//////////////////////////////////////////////

 		var dTableBCust;
		loadDataBCust = function(){
		
			$.ajax(
			{
				type: 'GET', 		
				url: '{{url('cust/browse')}}',
				// data: {
				// 	'GOL': 'Y',
				// },

				beforeSend: function(){
					$("#LOADX").show();
				},

				success: function( response )
				{

					$("#LOADX").hide();

					resp = response;
					if(dTableBCust){
						dTableBCust.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBCust.row.add([
							'<a href="javascript:void(0);" onclick="chooseCustomer(\''+resp[i].KODEC+'\',  \''+resp[i].NAMAC+'\',  \''+resp[i].HARI+'\', \''+resp[i].ALAMAT+'\',  \''+resp[i].KOTA+'\',  \''+resp[i].PKP+'\',  \''+resp[i].KODEP+'\',  \''+resp[i].NAMAP+'\',  \''+resp[i].RING+'\',  \''+resp[i].KOM+'\',  \''+resp[i].LIM+'\',  \''+resp[i].AK12+'\')">'+resp[i].KODEC+'</a>',
							resp[i].NAMAC,
							resp[i].ALAMAT,
							resp[i].KOTA,
							resp[i].PKP2,
						]);
					}
					dTableBCust.draw();
				}
			});
		}
		
		dTableBCust = $("#table-bcust").DataTable({
			
		});
		
		browseCust = function(){
			loadDataBCust();
			$("#browseCustModal").modal("show");
		}
		
		chooseCustomer = function(KODEC,NAMAC, HARI, ALAMAT, KOTA, PKP, KODEP, NAMAP, RING, KOM, LIM, AK12){
			$("#KODEC").val(KODEC);
			$("#NAMAC").val(NAMAC);
			$("#HARI").val(HARI);
			$("#ALAMAT").val(ALAMAT);
			$("#KOTA").val(KOTA);			
			$("#PKP").val(PKP);			
			$("#KODEP").val(KODEP);			
			$("#NAMAP").val(NAMAP);			
			$("#RING").val(RING);			
			$("#KOM").val(KOM);				
			$("#LIM").val(LIM);				
			$("#AK12").val(AK12);			
			$("#browseCustModal").modal("hide");
		}
		
		var PKP=$("#PKP").val();	
		
		if (PKP == 1 ) 
		{
		$("#PKP").prop('checked', true)
		} 
		else 
		{
		$("#PKP").prop('checked', false)
		}

		$("#KODEC").keypress(function(e){

			if(e.keyCode == 46){
				 e.preventDefault();
				 browseCust();
			}
		}); 
		

//////////////////////////////////////////////////////

		var dTableBBarangx;
		var rowidBarangx;
		loadDataBBarangx = function(){
		
			$.ajax(
			{
				type: 'GET',    
				url: "{{url('brg/browse_beli')}}",
				async : false,
				data: {
						'KD_BRG': $("#KD_BRG"+rowidBarangx).val(),	
					    'GOL': "{{$golz}}",		
						PKP : $("#PKP").val(), 		
					
				},
				success: function( response )

				{
					resp = response;
					
					
					if ( resp.length > 1 )
					{	
							if(dTableBBarangx){
								dTableBBarangx.clear();
							}
							for(i=0; i<resp.length; i++){
								
								dTableBBarangx.row.add([
									'<a href="javascript:void(0);" onclick="chooseBarangx(\''+resp[i].KD_BRG+'\', \''+resp[i].KD_GRUP+'\' , \''+resp[i].NA_BRG+'\' , \''+resp[i].TYPE_KOM+'\', \''+resp[i].KOM+'\', \''+resp[i].LOKASI+'\', \''+resp[i].XSO+'\' )">'+resp[i].KD_BRG+'</a>',
									resp[i].NA_BRG,
								]);
							}
							dTableBBarangx.draw();
					
					}
					else
					{
						$("#KD_BRG"+rowidBarangx).val(resp[0].KD_BRG);
						$("#KD_GRUP"+rowidBarangx).val(resp[0].KD_GRUP);
						$("#NA_BRG"+rowidBarangx).val(resp[0].NA_BRG);
						$("#TYPE_KOM"+rowidBarangx).val(resp[0].TYPE_KOM);
						$("#KOM"+rowidBarangx).val(resp[0].KOM);
						$("#LOKASI"+rowidBarangx).val(resp[0].LOKASI);
						$("#XSO"+rowidBarangx).val(resp[0].XSO);
					}
				}
			});
		}
		
		dTableBBarangx = $("#table-bbarangx").DataTable({
			
		});

		browseBarangx = function(rid){
			rowidBarangx = rid;
			$("#NA_BRG"+rowidBarangx).val("");			
			loadDataBBarangx();
	
			
			if ( $("#NA_BRG"+rowidBarangx).val() == '' ) {				
					$("#browseBarangxModal").modal("show");
			}	
		}
		
		chooseBarangx = function(KD_BRG,KD_GRUP,NA_BRG, TYPE_KOM, KOM, LOKASI, XSO ){

			$("#KD_BRG"+rowidBarangx).val(KD_BRG);
			$("#KD_GRUP"+rowidBarangx).val(KD_GRUP);
			$("#NA_BRG"+rowidBarangx).val(NA_BRG);
			$("#TYPE_KOM"+rowidBarangx).val(TYPE_KOM);
			$("#KOM"+rowidBarangx).val(KOM);
			$("#LOKASI"+rowidBarangx).val(LOKASI);
			$("#XSO"+rowidBarangx).val(XSO);

			$("#browseBarangxModal").modal("hide");
		}
		

////////////////////////////////////////////////////
////////////////////////////////////////////////////////

		var dTableBBarangdz;
		var rowidBarangdz;
		loadDataBBarangdz = function(){
		
			$.ajax(
			{
				type: 'GET',    
				url: "{{url('brgdz/browsedz')}}",
				async : false,
				data: {
						'KD_BRG': $("#KD_BRG"+rowidBarangdz).val(),
					
				},
				success: function( response )

				{
					resp = response;
					
					
					if ( resp.length > 1 )
					{	
							if(dTableBBarangdz){
								dTableBBarangdz.clear();
							}
							for(i=0; i<resp.length; i++){
								
								dTableBBarangdz.row.add([
									'<a href="javascript:void(0);" onclick="chooseBarangdz(\''+resp[i].SATUAN+'\', \''+resp[i].KALI+'\', \''+resp[i].BERAT+'\'  )">'+resp[i].KD_BRG+'</a>',
									resp[i].NA_BRG,
									resp[i].SATUAN,
									resp[i].KALI,
								]);
							}
							dTableBBarangdz.draw();
					
					}
					else
					{
						$("#SATUAN"+rowidBarangdz).val(resp[0].SATUAN);
						$("#KALI"+rowidBarangdz).val(resp[0].KALI);
						$("#BERAT"+rowidBarangdz).val(resp[0].BERAT);
						
						harga(rowidBarangdz);
						cari_lokasi(rowidBarangdz);
				
					}
				}
			});
		}
		
		dTableBBarangdz = $("#table-bbarangdz").DataTable({
			
		});

		browseBarangdz = function(rid){
			rowidBarangdz = rid;
			$("#KALI"+rowidBarangdz).val("");			
			loadDataBBarangdz();
	
			
			if ( $("#KALI"+rowidBarangdz).val() == '' ) {				
					$("#browseBarangdzModal").modal("show");
			}	
		}
		
		chooseBarangdz = function(SATUAN, KALI, BERAT){
			$("#SATUAN"+rowidBarangdz).val(SATUAN);
			$("#KALI"+rowidBarangdz).val(KALI);
			$("#BERAT"+rowidBarangdz).val(BERAT);
	        	harga(rowidBarangdz);
				cari_lokasi(rowidBarangdz);
			$("#browseBarangdzModal").modal("hide");
		}
		

		////////////////////////////////////////////////////

		//////////////////////////////////////////////////////

		var dTableBBahan;
		var rowidBahan;
		loadDataBBahan = function(){
		
			$.ajax(
			{
				type: 'GET',    
				url: "{{url('bhn/browse')}}",
				async : false,
				data: {
						'KD_BHN': $("#KD_BHN"+rowidBahan).val(),
					
				},
				success: function( response )

				{
					resp = response;
					
					
					if ( resp.length > 1 )
					{	
							if(dTableBBahan){
								dTableBBahan.clear();
							}
							for(i=0; i<resp.length; i++){
								
								dTableBBahan.row.add([
									'<a href="javascript:void(0);" onclick="chooseBahan(\''+resp[i].KD_BHN+'\', \''+resp[i].NA_BHN+'\' , \''+resp[i].SATUAN+'\' )">'+resp[i].KD_BHN+'</a>',
									resp[i].NA_BHN,
									resp[i].SATUAN,
								]);
							}
							dTableBBahan.draw();
					
					}
					else
					{
						$("#KD_BHN"+rowidBahan).val(resp[0].KD_BHN);
						$("#NA_BHN"+rowidBahan).val(resp[0].NA_BHN);
						$("#SATUAN"+rowidBahan).val(resp[0].SATUAN);
					}
				}
			});
		}
		
		dTableBBahan = $("#table-bbahan").DataTable({
			
		});

		browseBahan = function(rid){
			rowidBahan = rid;
			$("#NA_BHN"+rowidBahan).val("");			
			loadDataBBahan();
	
			
			if ( $("#NA_BHN"+rowidBahan).val() == '' ) {				
					$("#browseBahanModal").modal("show");
			}	
		}
		
		chooseBahan = function(KD_BHN,NA_BHN,SATUAN){
			$("#KD_BHN"+rowidBahan).val(KD_BHN);
			$("#NA_BHN"+rowidBahan).val(NA_BHN);	
			$("#SATUAN"+rowidBahan).val(SATUAN);
			$("#browseBahanModal").modal("hide");
		}
		
		
		/* $("#RAK0").onblur(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseRak(0);
			}
		});  */

		////////////////////////////////////////////////////


	});




//////////////////////////////////////////////////////////////////

	function cekDetail(){
		var cekBarang = '';
		$(".KD_BRG").each(function() {
			
			let z = $(this).closest('tr');
			var KD_BRGX = z.find('.KD_BRG').val();
			var QTYX = parseFloat(z.find('.QTY').val().replace(/,/g, ''));
			var SEDIAX = parseFloat(z.find('.SEDIA').val().replace(/,/g, ''));

			// alert(QTYX);
			// alert(SEDIAX);
			
			if( KD_BRGX =="" )
			{
					cekBarang = '1';
					
			}	

			if( QTYX > SEDIAX )
			{
					cekBarang = '1';
					
			}	
		});
		
		return cekBarang;
	}

////////////////////////////////////////////////////////////////////


	function simpan() {

		hitung();
		
		var tgl = $('#TGL').val();
		var bulanPer = {{session()->get('periode')['bulan']}};
		var tahunPer = {{session()->get('periode')['tahun']}};
		
        var check = '0';


			if (cekDetail())
			{	
			    check = '1';

				Swal.fire({
					icon: 'warning',
					title: 'Warning',
					text: '#Qty lebih dari Ketersediaan Stok.'
				});
				return;
			}

			if ( $('#NAMAC').val()=='' ) 
            {				
			    check = '1';
				Swal.fire({
					icon: 'warning',
					title: 'Warning',
					text: 'Customer# Harus Diisi.'
				});
				return; // Stop function execution
			}


////////////////////////////////////////////////////////////////////////////////////////
		$tipx = $('#tipx').val();
		
        if ( $tipx != 'new' )
		{
		    
		    $pkp00 = $('#PKP').val();
		    $pkp11 = $('#ZPKP').val();
		    
		    
		    
			if ( $pkp00 != $pkp11   ) 
            {
               
                check = '1';
				Swal.fire({
					icon: 'warning',
					title: 'Warning',
					text: 'Type PKP beda dengan Type PKP awal.'
				});
				return;
                
            }			 
		}



////////////////////////////////////////////////////////////////////////////////////////////

		
			if ( tgl.substring(3,5) != bulanPer ) 
			{
				
				check = '1';
				Swal.fire({
					icon: 'warning',
					title: 'Warning',
					text: 'Bulan tidak sama dengan Periode'
				});
				return; // Stop function execution
				alert("Bulan tidak sama dengan Periode");
			}	
			

			if ( tgl.substring(tgl.length-4) != tahunPer )
			{
				check = '1';
				Swal.fire({
					icon: 'warning',
					title: 'Warning',
					text: 'Tahun tidak sama dengan Periode'
				});
				return; // Stop function execution
				
		    }	 

			if ( $('#KD_BRG').val()=='' ) 
            {				
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
			
			
/////////////////////////////////////////////////////

    		var LIM = parseFloat($('#LIM').val().replace(/,/g, ''));
    		var AK12 = parseFloat($('#AK12').val().replace(/,/g, ''));
    		var NETT = parseFloat($('#NETT').val().replace(/,/g, ''));
    
    		// alert(MAXB);
    		// alert(TBERAT);
    		
    		if ( AK12 + NETT > LIM ) {
    			
    			// alert('halo');
    			check = '1'; 
    			Swal.fire({
    				icon: 'warning',
    				title: 'Warning',
    				text: 'Nilai Nett tidak boleh melebihi Limit'
    			});
    			return; 
    			
    		}

/////////////////////////////////////////////////////
		
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
	    

		var TTOTAL_QTY2 = 0;
		var TTOTAL_QTY = 0;
		var TTOTAL = 0;
		var TDISK = 0;
		var TPPNX = 0;
		var TDPPX = 0;
		var NETTX = 0;
		var TOTAL_TKOMX = 0;

		$(".QTY").each(function() {
			
			let z = $(this).closest('tr');
			var QTYX = parseFloat(z.find('.QTY').val().replace(/,/g, ''));
			var QTY2X = parseFloat(z.find('.XQTY').val().replace(/,/g, ''));
            TTOTAL_QTY +=QTYX;		
            TTOTAL_QTY2 +=QTY2X;	
		
		});
		
		
		$golx = $('#golz').val();
		
		$(".QTY").each(function() {
			
			let z = $(this).closest('tr');
			var QTY2X = parseFloat(z.find('.XQTY').val().replace(/,/g, ''));
			var KALIX = parseFloat(z.find('.KALI').val().replace(/,/g, ''));
			var QTYX = parseFloat(z.find('.QTY').val().replace(/,/g, ''));
			var HARGAX = parseFloat(z.find('.HARGA').val().replace(/,/g, ''));
			//var PPNX = parseFloat(z.find('.PPNX').val().replace(/,/g, ''));
			var DISKAX = parseFloat(z.find('.DISKA').val().replace(/,/g, ''));
			var DISKBX = parseFloat(z.find('.DISKB').val().replace(/,/g, ''));
			var DISKCX = parseFloat(z.find('.DISKC').val().replace(/,/g, ''));
			var DISKDX = parseFloat(z.find('.DISKD').val().replace(/,/g, ''));
			var DISKEX = parseFloat(z.find('.DISKE').val().replace(/,/g, ''));
			var DISKX = parseFloat(z.find('.DISK').val().replace(/,/g, ''));
			var BERATX = parseFloat(z.find('.BERAT').val().replace(/,/g, ''));
			var TBERATX = parseFloat(z.find('.TBERAT').val().replace(/,/g, ''));
			
			var KOMX = parseFloat(z.find('.KOM').val().replace(/,/g, ''));
			var TKOMX = 0;

			var PKP = parseFloat($('#PKP').val().replace(/,/g, ''));
			var KD_GRUPX = z.find('.KD_GRUP').val();

////////////////////////////////////////////////////////////////////////////

				QTYX = QTY2X * KALIX;
				
				z.find('.QTY').val(QTYX);

/////////////////////////////////////////////////////////////////////////////////////////	

                   var TTOTAL_QTY1 = 0;
				   
                   TTOTAL_QTY1 = hitung2(KD_GRUPX);
					
					//alert(TTOTAL_QTY1);
			
			
			if ( $golx != 'D' )
			{
            			var AHARGAX = parseFloat(z.find('.AHARGA').val().replace(/,/g, ''));
            			var BHARGAX = parseFloat(z.find('.BHARGA').val().replace(/,/g, ''));
            			var CHARGAX = parseFloat(z.find('.CHARGA').val().replace(/,/g, ''));
            			var DHARGAX = parseFloat(z.find('.DHARGA').val().replace(/,/g, ''));
            			var EHARGAX = parseFloat(z.find('.EHARGA').val().replace(/,/g, ''));
            			var FHARGAX = parseFloat(z.find('.FHARGA').val().replace(/,/g, ''));
            			var GHARGAX = parseFloat(z.find('.GHARGA').val().replace(/,/g, ''));
            			var HARGAX = 0;
            			 
                        if( TTOTAL_QTY1 > 0 )
            			{
            					if ( TTOTAL_QTY1 >= 200) 
            					{
            						var HARGAX = GHARGAX;
            
            					}
            
            					if ( ( TTOTAL_QTY1 < 200) && (TTOTAL_QTY1 >=150) ) 
            					{
            						var HARGAX = FHARGAX;
            
            					}
            
            					if ( ( TTOTAL_QTY1 < 150) && (TTOTAL_QTY1 >=100)) 
            					{
            						var HARGAX = EHARGAX;
            
            					}
            					
            					if ( ( TTOTAL_QTY1 < 100) && (TTOTAL_QTY1 >=50) )
            					{
            
            					var HARGAX = DHARGAX;
            					} 
            					
            					if ( ( TTOTAL_QTY1 <  50 ) && ( TTOTAL_QTY1 >=25 )  )
            					{
            						var HARGAX = CHARGAX;
            					}
            					
            					if ( ( TTOTAL_QTY1 <  25) && (TTOTAL_QTY1 >=6) )
            					{
            
            						var HARGAX = BHARGAX;
            					}                             
            					
            					if ( TTOTAL_QTY1 <  6  )
            					{
            
            						var HARGAX = AHARGAX;
            
            					}  			 
            			 
            			}
			
			}
			
			z.find('.HARGA').val(HARGAX);	

		    z.find('.HARGA').autoNumeric('update');				 
			 
			 


///////////////////////////////////////////////////////////////////////////////////////
              
			  
	        var TOTALDISKAX = 0;
	        var TOTALDISKBX = 0;
	        var TOTALDISKCX = 0;
	        var TOTALDISKDX = 0;
	        var TOTALDISKEX = 0;
	        var TOTALDISKX = 0;
			
			if( (DISKAX > 0) && (DISKAX < 100) )
			{
				TOTALDISKAX  = ( DISKAX / 100 * ( QTY2X * HARGAX) ) ;

			}
			else
			{
				TOTALDISKAX = DISKAX ;
			}
			
			
			
			if( (DISKBX > 0) && (DISKBX < 100) )
			{
				TOTALDISKBX = ( ( QTY2X * HARGAX ) - TOTALDISKAX )  * DISKBX / 100  ;

			}
			else
			{
			    TOTALDISKBX = DISKBX;	
			}
			
			
			
			
			if( (DISKCX > 0) && (DISKCX < 100) )
			{
				TOTALDISKCX = ( ( QTY2X * HARGAX ) - TOTALDISKAX  - TOTALDISKBX )  * DISKCX / 100  ;

			}
			else
			{
				 TOTALDISKCX = DISKCX;	
			}
			
			
			
			
			if( (DISKDX > 0) && (DISKDX < 100) )
			{
				TOTALDISKDX = ( ( QTY2X * HARGAX ) - TOTALDISKAX  - TOTALDISKBX - TOTALDISKCX )  * DISKDX / 100  ;

			}
			else
			{
				 TOTALDISKDX = DISKDX;	
			}
			
			
			if( (DISKEX > 0) && (DISKEX < 100) )
			{
				 TOTALDISKEX = ( ( QTY2X * HARGAX ) - TOTALDISKAX  - TOTALDISKBX - TOTALDISKCX - TOTALDISKDX )  * DISKEX / 100  ;

			}
			else
			{
				 TOTALDISKEX = DISKEX;	
			}			
			
			// if( (DISKX > 0) && (DISKX < 100) )
			// {
				// TOTALDISKX = DISKAX + DISKBX + DISKCX + DISKDX + DISKEX ;
				TOTALDISKX = TOTALDISKAX + TOTALDISKBX + TOTALDISKCX + TOTALDISKDX + TOTALDISKEX ;

			// }
			
			z.find('.DISKA').val(DISKAX);
			z.find('.DISKB').val(DISKBX);
			z.find('.DISKC').val(DISKCX);
			z.find('.DISKD').val(DISKDX);
			z.find('.DISKE').val(DISKEX);
			z.find('.DISK').val(TOTALDISKX);


///////////////////////////////////////////////////////////////////////////////////////

            //var TOTALX  =  ( QTYX * HARGAX );
            var TOTAL1X  =  ( QTY2X * HARGAX ) - TOTALDISKX;
			z.find('.TOTAL').val(TOTAL1X);

////////////////////////////////////////////////////////////////////////////


			TKOMX = KOMX * TOTAL1X / 100;
			z.find('.TKOM').val(TKOMX);

////////////////////////////////////////////////////////////////////////////

			TBERATX = QTY2X * BERATX;
			z.find('.TBERAT').val(TBERATX);	

////////////////////////////////////////////////////////////////////////////

			var DPPX = 0 ;
			var PPNX = 0;
			


			if (PKP == '1' ) {
			    DPPX = Math.floor(TOTAL1X / ((100+11)/100) );
	     		z.find('.DPP').val(DPPX);

			} else {
			    DPPX = TOTAL1X;
	     		z.find('.DPP').val(DPPX);
	     		
			}


            PPNX = TOTAL1X - DPPX;
            
			z.find('.PPNX').val(PPNX);
				
		    z.find('.QTY').autoNumeric('update');	
		    z.find('.TOTAL').autoNumeric('update');				
		    z.find('.DPP').autoNumeric('update');			
		    z.find('.DISKA').autoNumeric('update');			
		    z.find('.DISKB').autoNumeric('update');			
		    z.find('.DISKC').autoNumeric('update');			
		    z.find('.DISKD').autoNumeric('update');			
		    z.find('.DISKE').autoNumeric('update');			
		    z.find('.DISK').autoNumeric('update');			
		    z.find('.PPNX').autoNumeric('update');	
		    z.find('.TKOMX').autoNumeric('update');				
		    z.find('.TBERAT').autoNumeric('update');				

            // TTOTAL_QTY +=QTYX;		
            TTOTAL +=TOTAL1X;				
            TPPNX +=PPNX;				
            TDPPX +=DPPX;
            TDISK +=DISKX;
            TOTAL_TKOMX +=TKOMX;			
			
		
		});

		var DISK_GLOBALX = parseFloat($('#DISK_GLOBAL').val().replace(/,/g, ''));

		if (isNaN(DISK_GLOBALX)) DISK_GLOBALX = 0;

		if ((DISK_GLOBALX > 0) && (DISK_GLOBALX < 100)) {
			DISK_GLOBALX = (DISK_GLOBALX / 100) * TTOTAL;
		}

		TTOTALX = TTOTAL - DISK_GLOBALX;

		NETTX = TTOTALX;

		$('#DISK_GLOBAL').val(numberWithCommas(DISK_GLOBALX));
		$("#DISK_GLOBAL").autoNumeric('update');

	
		if(isNaN(TTOTAL_QTY2)) TTOTAL_QTY2 = 0;

		$('#TTOTAL_QTY2').val(numberWithCommas(TTOTAL_QTY2));		
		$("#TTOTAL_QTY2").autoNumeric('update');


		if(isNaN(TTOTAL_QTY)) TTOTAL_QTY = 0;

		$('#TTOTAL_QTY').val(numberWithCommas(TTOTAL_QTY));		
		$("#TTOTAL_QTY").autoNumeric('update');
		
		if(isNaN(TTOTAL)) TTOTAL = 0;

		$('#TTOTAL').val(numberWithCommas(TTOTAL));		
		$("#TTOTAL").autoNumeric('update');

		if(isNaN(TDISK)) TDISK = 0;

		$('#TDISK').val(numberWithCommas(TDISK));		
		$("#TDISK").autoNumeric('update');

		if(isNaN(TOTAL_TKOMX)) TOTAL_TKOMX = 0;

		$('#TOTAL_TKOM').val(numberWithCommas(TOTAL_TKOMX));		
		$("#TOTAL_TKOM").autoNumeric('update');


		$('#TDPP').val(numberWithCommas(TDPPX));		
		$("#TDPP").autoNumeric('update');
		
		$('#TPPN').val(numberWithCommas(TPPNX));		
		$("#TPPN").autoNumeric('update');

		$('#NETT').val(numberWithCommas(NETTX));		
		$("#NETT").autoNumeric('update');
	}

	function hitung2( KD_GRUP ) {

		var JUMLAHX = 0;
		
		
		$(".QTY").each(function() {
			
			let z = $(this).closest('tr');
			var XQTY = parseFloat(z.find('.XQTY').val().replace(/,/g, ''));
			var KD_GRUPX = z.find('.KD_GRUP').val();
			var KD_BRGX = z.find('.KD_BRG').val();
			
			if (KD_GRUP == KD_GRUPX){
				
				JUMLAHX +=XQTY;	
				
			}	
			
			
		
		});
	
	
		return JUMLAHX ;
		

	}
 
	function baru() {
		
		 kosong();
		 hidup();
	
	}
	
	function ganti() {
		
// 		 mati();
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
			$("#KODEC").attr("readonly", true);
			$("#KODEC").attr("disabled", false);
					
			$("#NAMAC").attr("readonly", true);
			$("#ALAMAT").attr("readonly", true);
			$("#KOTA").attr("readonly", true);			
			$("#NOTES").attr("readonly", false);
			$("#PKP").attr("disabled", true);
			$("#NAMAP").attr("readonly", true);
			$("#RING").attr("readonly", true);
			$("#LIM").attr("readonly", true);
			$("#AK12").attr("readonly", true);
			$("#NOTES2").attr("readonly", false);
			$("#DISK_GLOBAL").attr("readonly", false);
		
	
     
       
		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#REC" + i.toString()).attr("readonly", true);
			$("#KD_BHN" + i.toString()).attr("readonly", false);
			$("#KD_BRG" + i.toString()).attr("readonly", false);
			$("#NA_BHN" + i.toString()).attr("readonly", true);
			$("#NA_BRG" + i.toString()).attr("readonly", true);
			$("#SATUAN" + i.toString()).attr("readonly", true);

			$("#KALI" + i.toString()).attr("readonly", true);
			$("#XQTY" + i.toString()).attr("readonly", false);
			$("#QTY" + i.toString()).attr("readonly", true);
			$("#SEDIA" + i.toString()).attr("readonly", true);
			$("#TOTAL" + i.toString()).attr("readonly", true);

			$("#DISKA" + i.toString()).attr("readonly", false);
			$("#DISKB" + i.toString()).attr("readonly", false);
			$("#DISKC" + i.toString()).attr("readonly", false);
			$("#DISKD" + i.toString()).attr("readonly", false);
			$("#DISKE" + i.toString()).attr("readonly", false);

			$("#DISK" + i.toString()).attr("readonly", true);
			$("#KET" + i.toString()).attr("readonly", false);
			$("#HARGA" + i.toString()).attr("readonly", true);
			
			$("#AHARGA" + i.toString()).attr("readonly", true);
			$("#BHARGA" + i.toString()).attr("readonly", true);
			$("#CHARGA" + i.toString()).attr("readonly", true);
			$("#DHARGA" + i.toString()).attr("readonly", true);
			$("#EHARGA" + i.toString()).attr("readonly", true);
			$("#FHARGA" + i.toString()).attr("readonly", true);
			$("#GHARGA" + i.toString()).attr("readonly", true);
			$("#TYPE_KOM" + i.toString()).attr("readonly", true);
			$("#KOM" + i.toString()).attr("readonly", true);
			$("#TKOM" + i.toString()).attr("readonly", true);
			$("#LOKASI" + i.toString()).attr("readonly", true);
			$("#BERAT" + i.toString()).attr("readonly", true);
			$("#TBERAT" + i.toString()).attr("readonly", true);
			$("#XSO" + i.toString()).attr("readonly", true);
			$("#PPNX" + i.toString()).attr("readonly", true);
			$("#DPP" + i.toString()).attr("readonly", true);

			$("#DELETEX" + i.toString()).attr("hidden",false);


			$tipx = $('#tipx').val();
			
			if ( $tipx != 'new' )
			{
				$("#KD_BHN" + i.toString()).attr("readonly", true);	
				$("#KD_BHN" + i.toString()).removeAttr('onblur');
				
				$("#KD_BRG" + i.toString()).attr("readonly", true);	
				$("#KD_BRG" + i.toString()).removeAttr('onblur');	
			}
			
			
		    $golz = $('#golz').val();
			
			if ( $golz == 'D' )
			{
				$("#HARGA" + i.toString()).attr("readonly", false);
			} else
			{
				$("#HARGA" + i.toString()).attr("readonly", true);
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
		$("#JTEMPO").attr("readonly", true);
		$("#KODEC").attr("readonly", true);
		$("#KODEC").attr("disabled", true);
				
		$("#NAMAC").attr("readonly", true);
		$("#ALAMAT").attr("readonly", true);
		$("#KOTA").attr("readonly", true);			
		$("#NOTES").attr("readonly", true);
		$("#PKP").attr("disabled", true);
		$("#NAMAP").attr("readonly", true);
		$("#RING").attr("readonly", true);
		$("#LIM").attr("readonly", true);
		$("#AK12").attr("readonly", true);
		$("#NOTES2").attr("readonly", true);
	
		$("#DISK_GLOBAL").attr("readonly", true);

		
		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#REC" + i.toString()).attr("readonly", true);
			$("#KD_BHN" + i.toString()).attr("readonly", false);
			$("#KD_BRG" + i.toString()).attr("readonly", true);
			$("#NA_BHN" + i.toString()).attr("readonly", true);
			$("#NA_BRG" + i.toString()).attr("readonly", true);
			$("#SATUAN" + i.toString()).attr("readonly", true);
			$("#QTY" + i.toString()).attr("readonly", true);
			$("#XQTY" + i.toString()).attr("readonly", true);		
			$("#KALI" + i.toString()).attr("readonly", true);
	
			$("#SEDIA" + i.toString()).attr("readonly", true);
			$("#TOTAL" + i.toString()).attr("readonly", true);
			$("#DISK" + i.toString()).attr("readonly", true);

			$("#DISKA" + i.toString()).attr("readonly", true);
			$("#DISKB" + i.toString()).attr("readonly", true);
			$("#DISKC" + i.toString()).attr("readonly", true);
			$("#DISKD" + i.toString()).attr("readonly", true);
			$("#DISKE" + i.toString()).attr("readonly", true);

			$("#KET" + i.toString()).attr("readonly", true);
			$("#HARGA" + i.toString()).attr("readonly", true);
			
			
			$("#AHARGA" + i.toString()).attr("readonly", true);
			$("#BHARGA" + i.toString()).attr("readonly", true);
			$("#CHARGA" + i.toString()).attr("readonly", true);
			$("#DHARGA" + i.toString()).attr("readonly", true);
			$("#EHARGA" + i.toString()).attr("readonly", true);
			$("#FHARGA" + i.toString()).attr("readonly", true);
			$("#GHARGA" + i.toString()).attr("readonly", true);
			$("#TYPE_KOM" + i.toString()).attr("readonly", true);
			$("#KOM" + i.toString()).attr("readonly", true);
			$("#TKOM" + i.toString()).attr("readonly", true);
			$("#LOKASI" + i.toString()).attr("readonly", true);
			$("#BERAT" + i.toString()).attr("readonly", true);
			$("#TBERAT" + i.toString()).attr("readonly", true);
			$("#XSO" + i.toString()).attr("readonly", true);
			$("#PPNX" + i.toString()).attr("readonly", true);
			$("#DPP" + i.toString()).attr("readonly", true);
			
			$("#DELETEX" + i.toString()).attr("hidden", true);
		}


		
	}


	function kosong() {
				
		 $('#NO_BUKTI').val("+");	
	//	 $('#TGL').val("");		
		 $('#KODEC').val("");	
		 $('#NAMAC').val("");	
		 $('#ALAMAT').val("");	
		 $('#KOTA').val("");	
		 $('#HARI').val("0");
		 
		 $('#NOTES').val("");	
		 $('#TTOTAL_QTY').val("0.00");
		 $('#TTOTAL_QTY2').val("0.00");
		 $('#TTOTAL').val("0.00")
		 $('#TPPN').val("0.00")
		 $('#TDPP').val("0.00")
		 $('#NETT').val("0.00")
		 $('#TDISK').val("0.00")
		 $('#LIM').val("0.00")
		 $('#AK12').val("0.00")

		 
		var html = '';
		$('#detailx').html(html);	
		
	}
	
	// function hapusTrans() {
	// 	let text = "Hapus Transaksi "+$('#NO_BUKTI').val()+"?";
	// 	if (confirm(text) == true) 
	// 	{
	// 		window.location ="{{url('/so/delete/'.$header->NO_ID .'/?flagz='.$flagz.'&golz=' .$golz.'' )}}";

	// 		//return true;
	// 	} 
	// 	return false;
	// }

	// sweetalert untuk tombol hapus dan close
	
	function hapusTrans() {
		let text = "Hapus Transaksi "+$('#NO_BUKTI').val()+"?";

		var loc ='';
		var flagz = "{{ $flagz }}";
		var golz = "{{ $golz }}";
		
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
	            	loc = "{{ url('/so/delete/'.$header->NO_ID) }}" + '?flagz=' + encodeURIComponent(flagz) + 
						  '&golz=' + encodeURIComponent(golz)  ;

		            // alert(loc);
	            	window.location = loc;
		
				});
			}
		});
	}
	
	function closeTrans() {
		console.log("masuk");
		var loc ='';
		var flagz = "{{ $flagz }}";
		var golz = "{{ $golz }}";
		
		Swal.fire({
			title: 'Are you sure?',
			text: 'Do you really want to close this page? Unsaved changes will be lost.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Yes, close it',
			cancelButtonText: 'No, stay here'
		}).then((result) => {
			if (result.isConfirmed) {
	        	loc = "{{ url('/so/') }}" + '?flagz=' + encodeURIComponent(flagz) + '&golz=' + encodeURIComponent(golz)  ;
				window.location = loc ;
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
		var golz = "{{ $golz }}";
		var cari = $("#CARI").val();
		var loc = "{{ url('/so/edit/') }}" + '?idx={{ $header->NO_ID}}&tipx=search&flagz=' + encodeURIComponent(flagz) + '&golz=' + encodeURIComponent(golz) + '&buktix=' +encodeURIComponent(cari);
		window.location = loc;
		
	}

	function jtempo() {

		    
			$.ajax(
			{
				type: 'GET',    
				url: "{{url('so/jtempo')}}",
				async : false,
				data: {
						'TGL' : $("#TGL").val(),
						'HARI' : $("#HARI").val(),
				},
				success: function( response )

				{
					resp = response;
					$("#JTEMPO").val( resp );
					
				}
			});
		
	}


	function ambil_hari() {

		    
		$.ajax(
		{
			type: 'GET',    
			url: "{{url('cust/browse_hari')}}",
			data: {
					'KODEC' : $("#KODEC").val(),
			},
			
			success: function( response )

			{
				resp = response;
				$("#NAMAC").val( resp[0].NAMAC );
				$("#PKP").val( resp[0].PKP );
				$("#HARI").val( resp[0].HARI );
				$("#KODEP").val( resp[0].KODEP );
				$("#NAMAP").val( resp[0].NAMAP );
				$("#RING").val( resp[0].RING );
				$("#KOM").val( resp[0].KOM );
				$("#LIM").val( resp[0].LIM );
				$("#AK12").val( resp[0].AK12 );
				
	
	
        		if ( $("#PKP").val() == '1' )
        		{

                     document.getElementById("PKP").checked = true;
                    	
        		}
        
                else
                {
                     document.getElementById("PKP").checked = false;
                    
                }
        				
        	   jtempo();
        	   
			}
		});
		
		   
	
	}

	function harga(rowidBarangdz) {

		    
		$.ajax(
		{
			type: 'GET',    
			url: "{{url('brg/browse_harga')}}",
			data: {

				'KD_BRG': $("#KD_BRG"+rowidBarangdz).val(),				
				'SATUAN': $("#SATUAN"+rowidBarangdz).val(),
				'RING' : $("#RING").val(), 	
			},
			
			success: function( response )

			{
				resp = response;
				$("#AHARGA"+rowidBarangdz).val(resp[0].HARGA1);
				$("#BHARGA"+rowidBarangdz).val(resp[0].HARGA2);
				$("#CHARGA"+rowidBarangdz).val(resp[0].HARGA3);
				$("#DHARGA"+rowidBarangdz).val(resp[0].HARGA4);
				$("#EHARGA"+rowidBarangdz).val(resp[0].HARGA5);
				$("#FHARGA"+rowidBarangdz).val(resp[0].HARGA6);
				$("#GHARGA"+rowidBarangdz).val(resp[0].HARGA7);
				
			
			}
		});


	}
	
	
	function cari_lokasi(rowidBarangdz) {

		    

	
		$.ajax(
		{
			type: 'GET',    
			url: "{{url('brg/browsex')}}",
			data: {

				'KD_BRG': $("#KD_BRG"+rowidBarangdz).val(),				
			},
			
			success: function( response )

			{
				respx = response;
				$("#KD_GRUP"+rowidBarangdz).val(respx[0].KD_GRUP);
				$("#TYPE_KOM"+rowidBarangdz).val(respx[0].TYPE_KOM);
				$("#KOM"+rowidBarangdz).val(respx[0].KOM);
				$("#LOKASI"+rowidBarangdz).val(respx[0].LOKASI);
				$("#XSO"+rowidBarangdz).val(respx[0].XSO);
				$("#SEDIA"+rowidBarangdz).val(respx[0].SEDIA);
				
				
			
			}
		});
	
	
	}
	
    function tambah() {

        var x = document.getElementById('datatable').insertRow(baris + 1);
 
        $golz = $('#golz').val();
         		    
		html=`<tr>

                <td>
 					<input name='NO_ID[]' id='NO_ID${idrow}' type='hidden' class='form-control NO_ID' value='new' readonly> 
					<input name='REC[]' id='REC${idrow}' type='text' class='REC form-control' onkeypress='return tabE(this,event)' readonly>
	            </td>

				<td {{( $golz =='J' || $golz =='D' ) ? '' : 'hidden' }} >
				    <input name='KD_BRG[]' data-rowid=${idrow} onblur='browseBarangx(${idrow});' id='KD_BRG${idrow}' type='text' class='form-control  KD_BRG' >
				    <input hidden name='KD_GRUP[]' id='KD_GRUP${idrow}' type='text' class='form-control  KD_GRUP' >
                </td>
                <td {{( $golz =='J' || $golz =='D' ) ? '' : 'hidden' }} >
				    <input name='NA_BRG[]'   id='NA_BRG${idrow}' type='text' class='form-control  NA_BRG' readonly>
                </td>

                <td>
				    <input name='SATUAN[]'  onclick='browseBarangdz(${idrow})' id='SATUAN${idrow}' type='text' class='form-control  SATUAN' readonly>
		            <input name='AHARGA[]' hidden onclick='select()' onblur='hitung()' value='0' id='AHARGA${idrow}' type='text' style='text-align: right' class='form-control AHARGA text-primary' readonly >
		            <input name='BHARGA[]' hidden onclick='select()' onblur='hitung()' value='0' id='BHARGA${idrow}' type='text' style='text-align: right' class='form-control BHARGA text-primary' readonly>
		            <input name='CHARGA[]' hidden onclick='select()' onblur='hitung()' value='0' id='CHARGA${idrow}' type='text' style='text-align: right' class='form-control CHARGA text-primary' readonly >
		            <input name='DHARGA[]' hidden onclick='select()' onblur='hitung()' value='0' id='DHARGA${idrow}' type='text' style='text-align: right' class='form-control DHARGA text-primary' readonly >
		            <input name='EHARGA[]' hidden onclick='select()' onblur='hitung()' value='0' id='EHARGA${idrow}' type='text' style='text-align: right' class='form-control EHARGA text-primary' readonly >
		            <input name='FHARGA[]' hidden onclick='select()' onblur='hitung()' value='0' id='FHARGA${idrow}' type='text' style='text-align: right' class='form-control FHARGA text-primary' readonly >
		            <input name='GHARGA[]' hidden onclick='select()' onblur='hitung()' value='0' id='GHARGA${idrow}' type='text' style='text-align: right' class='form-control GHARGA text-primary' readonly >
		            
					<input name='TYPE_KOM[]' hidden id='TYPE_KOM${idrow}' type='text' class='form-control  TYPE_KOM' readonly >
					<input name='KOM[]' hidden onclick='select()' onblur='hitung()' value='0' id='KOM${idrow}' type='text' style='text-align: right' class='form-control KOM text-primary' readonly >
					<input name='TKOM[]' hidden onclick='select()' onblur='hitung()' value='0' id='TKOM${idrow}' type='text' style='text-align: right' class='form-control TKOM text-primary' readonly >
					<input name='LOKASI[]' hidden id='LOKASI${idrow}' type='text' class='form-control  LOKASI' >
		        
					<input name='BERAT[]' hidden onclick='select()' onblur='hitung()' value='0' id='BERAT${idrow}' type='text' style='text-align: right' class='form-control BERAT text-primary' readonly >
		            <input name='TBERAT[]' hidden onclick='select()' onblur='hitung()' value='0' id='TBERAT${idrow}' type='text' style='text-align: right' class='form-control TBERAT text-primary' readonly >

					<input name='XSO[]' hidden onclick='select()' onblur='hitung()' value='0' id='XSO${idrow}' type='text' style='text-align: right' class='form-control XSO text-primary' readonly >
		            
				</td>

				<td>
		            <input name='XQTY[]' onclick='select()' onblur='hitung()' value='1' id='XQTY${idrow}' type='text' style='text-align: right' class='form-control XQTY text-primary'  >
                </td>

				<td>
		            <input name='KALI[]' onclick='select()' onblur='hitung()' value='1' id='KALI${idrow}' type='text' style='text-align: right' class='form-control KALI text-primary' readonly >
                </td>

				<td>
		            <input name='QTY[]' onclick='select()' onblur='hitung()' value='1' id='QTY${idrow}' type='text' style='text-align: right' class='form-control QTY text-primary' readonly >
                </td>

				<td>
					<input name='SEDIA[]' onclick='select()' onblur='hitung()' value='0' id='SEDIA${idrow}' type='text' style='text-align: right' class='form-control SEDIA text-primary' readonly >
				</td>
        			
		 		<td>
            		    <input name='HARGA[]' onclick='select()' onblur='hitung()' value='0' id='HARGA${idrow}' type='text' style='text-align: right' class='form-control HARGA text-primary'   {{ ($golz == 'D') ? '' : 'readonly' }} >
                 </td>

				
				<td>
		            <input name='TOTAL[]' onclick='select()' onblur='hitung()' value='0' id='TOTAL${idrow}' type='text' style='text-align: right' class='form-control TOTAL text-primary' readonly  >
					<input name='PPNX[]'  hidden onblur='hitung()' value='0' id='PPNX${idrow}' type='text' style='text-align: right' class='form-control PPNX text-primary' readonly  >
					<input name='DPP[]'  hidden onblur='hitung()' value='0' id='DPP${idrow}' type='text' style='text-align: right' class='form-control DPP text-primary'  readonly >
				</td>
				
				
				
				<td>
					<input name='DISKA[]'  onclick='select()' onblur='hitung()' value='0' id='DISKA${idrow}' type='text' style='text-align: right' class='form-control DISKA text-primary' required>
				</td>
				
				<td>
					<input name='DISKB[]'  onclick='select()' onblur='hitung()' value='0' id='DISKB${idrow}' type='text' style='text-align: right' class='form-control DISKB text-primary' required>
				</td>
				
				<td>
					<input name='DISKC[]'  onclick='select()' onblur='hitung()' value='0' id='DISKC${idrow}' type='text' style='text-align: right' class='form-control DISKC text-primary' required>
				</td>
				
				<td>
					<input name='DISKD[]'  onclick='select()' onblur='hitung()' value='0' id='DISKD${idrow}' type='text' style='text-align: right' class='form-control DISKD text-primary' required>
				</td>
				
				<td>
					<input name='DISKE[]'  onclick='select()' onblur='hitung()' value='0' id='DISKE${idrow}' type='text' style='text-align: right' class='form-control DISKE text-primary' required>
				</td>

				<td>
					<input name='DISK[]'  onclick='select()' onblur='hitung()' value='0' id='DISK${idrow}' type='text' style='text-align: right' class='form-control DISK text-primary' readonly>
					<input name='KET[]'  hidden  id='KET${idrow}' type='text' class='form-control  KET' >
				</td>
				
                <td>
					<button type='button' id='DELETEX${idrow}'  class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button>
                </td>				
         </tr>`;
				
        x.innerHTML = html;
        var html='';
		
 

		jumlahdata = 100;
		
		for (i = 0; i <= jumlahdata; i++) {
			$("#XQTY" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#KALI" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#QTY" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#HARGA" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#TOTAL" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
		
			
			$("#DPP" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});
			
			$("#DISKA" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});
			
			$("#DISKB" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});
			
			$("#DISKC" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});
			
			$("#DISKD" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});
			
			$("#DISKE" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});
			
			$("#DISK" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});
			
			
			$("#PPNX" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});	
			
			$("#KOM" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});	
			
			$("#TKOM" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});	
			
			$("#BERAT" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});	
			
			$("#TBERAT" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});	
			
			$("#XSO" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});	
			
			$("#SEDIA" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});	
		}
		
		// $("#KD_BRG"+idrow).keypress(function(e){
		// 	if(e.keyCode == 46){
		// 		e.preventDefault();
		// 		browseBarang(eval($(this).data("rowid")));
		// 	}
		// }); 
		
		idrow++;
		baris++;
		nomor();
		// hitung();
		$(".ronly").on('keydown paste', function(e) {
			e.preventDefault();
			e.currentTarget.blur();
		});
     }
</script>



<!-- <script src="autonumeric.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
<script src="https://unpkg.com/autonumeric"></script> -->

@endsection