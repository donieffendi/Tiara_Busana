@extends('layouts.plain')

<style>
    .card {

    }

    .form-control:focus {
        background-color: #E0FFFF !important;
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

                    <form action="{{($tipx=='new')? url('/jual/store?flagz='.$flagz.'&golz='.$golz.'') : url('/jual/update/'.$header->NO_ID.'&flagz='.$flagz.'&golz='.$golz.'' ) }}" method="POST" name ="entri" id="entri" >
  
	    			      @csrf
        
                        <div class="tab-content mt-3">

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
								<div class="col-md-1" align="right">
                                    <label for="NO_BUKTI" class="form-label">Bukti#</label>
                                </div>
								

                                   <input type="text" class="form-control NO_ID" id="NO_ID" name="NO_ID"
                                    placeholder="Masukkan NO_ID" value="{{$header->NO_ID ?? ''}}" hidden readonly>

									<input name="tipx" class="form-control tipx" id="tipx" value="{{$tipx}}" hidden >
									<input name="flagz" class="form-control flagz" id="flagz" value="{{$flagz}}" hidden >
									<input name="golz" class="form-control golz" id="golz" value="{{$golz}}" hidden >
			
								
                                <div class="col-md-2">
                                    <input type="text" class="form-control NO" id="NO_BUKTI" name="NO_BUKTI"
                                    placeholder="Masukkan Bukti#" value="{{$header->NO_BUKTI}}" readonly >
                                </div>
        
                                <div class="col-md-1" align="right">
                                    <label for="TGL" class="form-label">Tgl</label>
                                </div>
                                <div class="col-md-2">
								
								  <input class="form-control date" id="TGL" name="TGL" data-date-format="dd-mm-yyyy" type="text" autocomplete="off" value="{{date('d-m-Y',strtotime($header->TGL))}}">
								
                                </div>
								
								<div class="col-md-1">
									<label for="JTEMPO" class="form-label">Jatuh tempo</label>
								</div>
								<div class="col-md-2">
								
								<input class="form-control date" id="JTEMPO" name="JTEMPO" data-date-format="dd-mm-yyyy" type="text" autocomplete="off" value="{{date('d-m-Y',strtotime($header->JTEMPO))}}" readonly >
								
								</div>
                            </div>

							

							<div class="form-group row">
								<div {{( $flagz =='JL') ? '' : 'hidden' }} class="col-md-1" align="right">
									<label style="color:red">*</label>									
                                    <label for="NO_SURATS" class="form-label">Surats#</label>
                                </div>
                               	<div {{( $flagz =='JL') ? '' : 'hidden' }} class="col-md-2 input-group" >
                                  <input type="text" class="form-control NO_SURATS" id="NO_SURATS" name="NO_SURATS" placeholder="Pilih Surats"value="{{$header->NO_SURATS}}" style="text-align: left" readonly >
        						  <button type="button" class="btn btn-primary" onclick="browseSurats()"><i class="fa fa-search"></i></button>
                                </div>

								<div {{( $flagz =='AJ') ? '' : 'hidden' }} class="col-md-1" align="right">
									<label style="color:red">*</label>									
                                    <label for="NO_JUAL" class="form-label">Jual#</label>
                                </div>
                               	<div {{( $flagz =='AJ') ? '' : 'hidden' }} class="col-md-2 input-group" >
                                  <input type="text" class="form-control NO_JUAL" id="NO_JUAL" name="NO_JUAL" placeholder="Pilih Jual"value="{{$header->NO_JUAL}}" style="text-align: left" readonly >
        						  <button type="button" class="btn btn-primary" onclick="browseJual()"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
							

                            <div class="form-group row">
								<div class="col-md-1" align="right">
                                    <label for="KODEC" class="form-label">Customer#</label>
                                </div>

								<div class="col-md-4">
                                    <input type="text" hidden class="form-control KODEC" id="KODEC" name="KODEC" placeholder="-" value="{{$header->KODEC}}" readonly>
									
                                    <input type="text" class="form-control NAMAC" id="NAMAC" name="NAMAC" placeholder="-" value="{{$header->NAMAC}}" readonly>
									
									
									<input hidden type="text" class="form-control KODEP" id="KODEP" name="KODEP" placeholder="-" value="{{$header->KODEP}}" readonly>
									<input hidden type="text" class="form-control NAMAP" id="NAMAP" name="NAMAP" placeholder="-" value="{{$header->NAMAP}}" readonly>
                                    <input type="text" hidden class="form-control ALAMAT" id="ALAMAT" name="ALAMAT" value="{{$header->ALAMAT}}"placeholder="Alamat" readonly >
                                    <input type="text" hidden  class="form-control KOTA" id="KOTA" name="KOTA" value="{{$header->KOTA}}"placeholder="Kota" readonly>
                                    
									<input hidden type="text" class="form-control RING" id="RING" name="RING" placeholder="-" value="{{$header->RING}}" readonly>
									<input hidden type="text" class="form-control KOM" onclick="select()"  id="KOM" name="KOM" placeholder="KOM" value="{{ number_format($header->KOM, 2, '.', ',') }}" style="text-align: right; width:140px" readonly>
                                
									<input hidden type="text" onclick="select()" onblur="hitung()" class="form-control HARI" id="HARI" name="HARI" placeholder="Masukkan HARI" 
									value="{{ number_format( $header->HARI, 0, '.', ',') }}" style="text-align: right" >
								   
								   
								</div>
								
								<div class="col-md-1" >
                                  	<input type="checkbox" class="form-check-input" id="PKP" name="PKP" readonly  value="{{$header->PKP}}" {{ ($header->PKP == 1) ? 'checked' : '' }}>
                                    
                                    <label for="PKP" class="form-label">Pkp</label>
                                    <input type="text" hidden class="form-control ZPKP" id="ZPKP" name="ZPKP" value="{{$header->PKP}}" placeholder="Masukkan Pkp" >
                                </div>

								

								<!-- code text box baru -->
								<div class="col-md-2 form-group row special-input-label">

									<input type="text" class="NO_FP" id="NO_FP" name="NO_FP" 
										value="{{$header->NO_FP}}" placeholder=" " >
									<label for="NO_FP">No Faktur</label>
								</div>
								<!-- tutupannya -->

								<div class="col-md-1" align="right">
                                    <label for="TGL_FP" class="form-label">Tgl Faktur</label>
                                </div>
                                <div class="col-md-2">
								
								  <input class="form-control date" id="TGL_FP" name="TGL_FP" data-date-format="dd-mm-yyyy" type="text" autocomplete="off" value="{{date('d-m-Y',strtotime($header->TGL_FP))}}">
								
                                </div>

                                
                            </div>
							

							<div class="form-group row">
                                <!-- code text box baru -->
								<div class="col-md-5 form-group row special-input-label">

									<input type="text" class="NOTES" id="NOTES" name="NOTES" 
										value="{{$header->NOTES}}" placeholder=" " >
									<label for="NOTES">Notes</label>
								</div>
								<!-- tutupannya -->

								<div class="col-md-1">
									<label for="TYPE" class="form-label">Type</label>
								</div>
								<div class="col-md-1">
									<select id="TYPE" class="form-control"  name="TYPE">
										<option value="CASH" {{ ($header->TYPE == 'CASH') ? 'selected' : '' }}>Cash</option>
										<option value="KREDIT" {{ ($header->TYPE == 'KREDIT') ? 'selected' : '' }}>Kredit</option>
									</select>
								</div> 
        

                            </div>
							
							<!-- loader tampil di modal  -->
							<div class="loader" style="z-index: 1055;" id='LOADX' ></div>

							
							<div style="overflow-y:scroll;" class="col-md-12 scrollable" align="right">
							
								<table id="datatable" class="table table-striped table-border">

									<thead>
										<tr>
											<th width="100px" style="text-align:center">No.</th>

											<th {{( $golz =='B') ? '' : 'hidden' }} width="100px">
												<label style="color:red;font-size:20px">*</label>
												<label for="KD_BHN" class="form-label">Bahan</label>
											</th>
											<th {{( $golz =='B') ? '' : 'hidden' }} width="200px" style="text-align:center">Nama</th>
											<th {{( $golz =='J' || $golz =='D') ? '' : 'hidden' }} width="200px">
                                                No SO
											</th>
											<th {{( $golz =='J' || $golz =='D') ? '' : 'hidden' }} width="100px">
                                                Barang
											</th>
											<th {{( $golz =='J' || $golz =='D') ? '' : 'hidden' }} width="400px" style="text-align:center">Nama</th>
											
											<th width="100px" style="text-align: center;">Satuan</th>
											<th width="150px" style="text-align: center;">Qty X</th>
											<th width="150px" style="text-align: center;">X</th>
											<th width="150px" style="text-align: center;">Qty</th>
											<th width="150px" style="text-align: center;">Harga</th>
											<th width="150px" style="text-align: center;">Total</th>								
											<th width="150px" style="text-align: center;">Disk 1</th>								
											<th width="150px" style="text-align: center;">Disk 2</th>								
											<th width="150px" style="text-align: center;">Disk 3</th>								
											<th width="150px" style="text-align: center;">Disk 4</th>								
											<th width="150px" style="text-align: center;">Disk 5</th>								
											<th width="150px" style="text-align: center;">Disk (Rp)</th>								
											<th width="150px" style="text-align: center;">Ket</th>								
											<th></th>										
										</tr>
										
									</thead>
									<tbody id="detailJuald">
									
									<?php $no=0 ?>
									@foreach ($detail as $detail)
										<tr>
											<td>
												<input type="hidden" name="NO_ID[]" id="NO_ID{{$no}}" type="text" value="{{$detail->NO_ID}}" 
												class="form-control NO_ID" onkeypress="return tabE(this,event)" readonly>
												
												<input name="REC[]" id="REC{{$no}}" type="text" value="{{$detail->REC}}" class="form-control REC" onkeypress="return tabE(this,event)" readonly>
											</td>
											
											<td {{( $golz =='B') ? '' : 'hidden' }}>
												<input name="KD_BHN[]" id="KD_BHN{{$no}}" type="text" value="{{$detail->KD_BHN}}"
												class="form-control KD_BHN "  onblur="browseBahan({{$no}})" >
											</td>
											<td {{( $golz =='B') ? '' : 'hidden' }}>
												<input name="NA_BHN[]" id="NA_BHN{{$no}}" type="text" class="form-control KD_BHN" value="{{$detail->NA_BHN}}" readonly required>
											</td>

											<td {{( $golz =='J' || $golz =='D') ? '' : 'hidden' }}>
												<input name="NO_SO[]" id="NO_SO{{$no}}" type="text" class="form-control NO_SO " 
												value="{{$detail->NO_SO}}" >
											</td>

											<td {{( $golz =='J' || $golz =='D') ? '' : 'hidden' }}>
												<input name="KD_BRG[]" id="KD_BRG{{$no}}" type="text" class="form-control KD_BRG " 
												value="{{$detail->KD_BRG}}" onblur="browseBarang({{$no}})">
											</td>

											<td {{( $golz =='J' || $golz =='D') ? '' : 'hidden' }}>
												<input name="NA_BRG[]" id="NA_BRG{{$no}}" type="text" class="form-control NA_BRG " value="{{$detail->NA_BRG}}">
											</td>

											<td>
												<input name="SATUAN[]" id="SATUAN{{$no}}" type="text" value="{{$detail->SATUAN}}" class="form-control SATUAN" >
											</td>
											
											<td>
												<input name="XQTY[]" onclick='select()' onkeyup="hitung()" id="XQTY{{$no}}" type="text" style="text-align: right"  class="form-control XQTY text-primary" value="{{$detail->QTY2}}">
											</td>
											<td>
												<input name="KALI[]" onclick='select()' onkeyup="hitung()" id="KALI{{$no}}" type="text" style="text-align: right"  class="form-control KALI text-primary" value="{{$detail->KALI}}">
											</td>
											
											<td>
												<input name="QTY[]"  onblur="hitung()" value="{{$detail->QTY}}" id="QTY{{$no}}" type="text" style="text-align: right"  class="form-control QTY text-primary" >
												<input name="QTY_KIRIM[]" hidden onblur="hitung()" value="{{$detail->QTY_KIRIM}}" id="QTY_KIRIM{{$no}}" type="text" style="text-align: right"  class="form-control QTY_KIRIM text-primary" >
											</td>
											
											<td>
												<input name="HARGA[]"  onblur="hitung()" value="{{$detail->HARGA}}" id="HARGA{{$no}}" type="text" style="text-align: right"  class="form-control HARGA text-primary" >
											</td>
											<td>
												<input name="TOTAL[]" onblur="hitung()" value="{{$detail->TOTAL}}" id="TOTAL{{$no}}" type="text" style="text-align: right"  class="form-control TOTAL text-primary"  >
												<input name="PPNX[]" hidden onblur="hitung()" value="{{$detail->PPN}}" id="PPNX{{$no}}" type="text" style="text-align: right"  class="form-control PPNX text-primary"  >
												<input name="DPP[]"  hidden onblur="hitung()" value="{{$detail->DPP}}" id="DPP{{$no}}" type="text" style="text-align: right"  class="form-control DPP text-primary"  >
												<input name="TYPE_KOM[]" hidden id="TYPE_KOM{{$no}}" type="text" value="{{$detail->TYPE_KOM}}" class="form-control TYPE_KOM" >
												<input name="KOM[]"  hidden onblur="hitung()" value="{{$detail->KOM}}" id="KOM{{$no}}" type="text" style="text-align: right"  class="form-control KOM text-primary"  >
												<input name="TKOM[]"  hidden onblur="hitung()" value="{{$detail->TKOM}}" id="TKOM{{$no}}" type="text" style="text-align: right"  class="form-control TKOM text-primary"  >
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
												<input name="DISK[]"  onblur="hitung()" value="{{$detail->DISK}}" id="DISK{{$no}}" type="text" style="text-align: right"  class="form-control DISK text-primary" readonly >
											</td>
											<td>
												<input name="KET[]" id="KET{{$no}}" type="text" value="{{$detail->KET}}" class="form-control KET" readonly>
												<!-- <button hidden type='button' id='DELETEX{{$no}}'  class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button> -->
											</td>   
										</tr>
										
									<?php $no++; ?>		
									@endforeach
									</tbody>
									<tfoot>
										<td></td>
										<td {{( $golz =='B') ? '' : 'hidden' }}></td>
										<td {{( $golz =='B') ? '' : 'hidden' }}></td>
										<td {{( $golz =='J' || $golz =='D') ? '' : 'hidden' }}></td>
										<td {{( $golz =='J' || $golz =='D') ? '' : 'hidden' }}></td>
										<td {{( $golz =='J' || $golz =='D') ? '' : 'hidden' }}></td>
										<td></td>		
										<td></td>
										<td>
											<input hidden class="form-control TOTAL_TKOM  text-primary font-weight-bold" style="text-align: right"  id="TOTAL_TKOM" name="TOTAL_TKOM" value="{{$header->TOTAL_TKOM}}" readonly>

										</td>
									</tfoot>
								</table>					
							</div>

							<!-- <div class="col-md-2 row">
								<a type="button" id='PLUSX' onclick="tambah()" class="fas fa-plus fa-sm md-3" style="font-size: 20px" ></a>

							</div>			 -->
							
                        </div> 
						
						<hr style="margin-top: 30px; margin-buttom: 30px">		
                                 
						<div class="tab-content mt-6">

							<div class="form-group row">

								<div class="col-md-3" align="right"  hidden>
									<label for="TOTAL_QTY2" class="form-label">Total Qty X</label>
								</div>
								<div class="col-md-1"  hidden>
									<input type="text" onclick="select()" onkeyup="hitung()" class="form-control TOTAL_QTY2" id="TOTAL_QTY2" name="TOTAL_QTY2" placeholder="" value="{{$header->TOTAL_QTY2}}" style="text-align: right" readonly>
								</div>
								
                                <div class="col-md-7" align="right">
                                    <label for="TTOTAL_QTY" class="form-label">Total QTY</label>
                                </div>

                                <div class="col-md-1">
                                    <input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TTOTAL_QTY" id="TTOTAL_QTY" name="TTOTAL_QTY" placeholder="" value="{{$header->TOTAL_QTY}}" style="text-align: right" readonly>
                                </div>
                                
                                <div class="col-md-1" align="right">
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
                                    <input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TDPP" id="TDPP" name="TDPP" placeholder="" value="{{$header->TDPP}}" style="text-align: right" readonly>

                                </div>
							</div>

							<div class="form-group row" hidden>
								<div class="col-md-8" align="right">
									<label for="TDISK" class="form-label">Diskon</label>
								</div>
								<div class="col-md-2">
									<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TDISK" id="TDISK" name="TDISK" placeholder="" value="{{$header->TDISK}}" style="text-align: right" readonly>
								</div>
							</div>
							
							<!--<div class="form-group row">-->
							<!--	<div class="col-md-8" align="right">-->
							<!--		<label for="DISKG" class="form-label">Diskon Global</label>-->
							<!--	</div>-->
							<!--	<div class="col-md-2">-->
							<!--		<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control DISKG" id="DISKG" name="DISKG" placeholder="" value="{{$header->DISKG}}" style="text-align: right" >-->
							<!--	</div>-->
							<!--</div>-->

							<div class="form-group row">
								<div class="col-md-9" align="right">
									<label for="DISK_GLOBAL" class="form-label">Disk Global</label>
								</div>
								<div class="col-md-2">
									<input type="text"  onclick="select()" onblur="hitung()" class="form-control DISK_GLOBAL" id="DISK_GLOBAL" name="DISK_GLOBAL" placeholder="" value="{{$header->DISK_GLOBAL}}" style="text-align: right" >
								</div>
							</div>

                            <div class="form-group row">
                                <div class="col-md-9" align="right">
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
						
						<div class="mt-3 col-md-12 form-group row">
							<div class="col-md-4">
								<button hidden type="button" id='TOPX'  onclick="location.href='{{url('/jual/edit/?idx=' .$idx. '&tipx=top&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-outline-primary">Top</button>
								<button hidden type="button" id='PREVX' onclick="location.href='{{url('/jual/edit/?idx='.$header->NO_ID.'&tipx=prev&flagz='.$flagz.'&golz='.$golz.'&buktix='.$header->NO_BUKTI )}}'" class="btn btn-outline-primary">Prev</button>
								<button hidden type="button" id='NEXTX' onclick="location.href='{{url('/jual/edit/?idx='.$header->NO_ID.'&tipx=next&flagz='.$flagz.'&golz='.$golz.'&buktix='.$header->NO_BUKTI )}}'" class="btn btn-outline-primary">Next</button>
								<button hidden type="button" id='BOTTOMX' onclick="location.href='{{url('/jual/edit/?idx=' .$idx. '&tipx=bottom&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-outline-primary">Bottom</button>
							</div>
							<div class="col-md-5">
								<button hidden type="button" id='NEWX' onclick="location.href='{{url('/jual/edit/?idx=0&tipx=new&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-warning">New</button>
								<button hidden type="button" id='EDITX' onclick='hidup()' class="btn btn-secondary">Edit</button>                    
								<button hidden type="button" id='UNDOX' onclick="location.href='{{url('/jual/edit/?idx=' .$idx. '&tipx=undo&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-info">Undo</button>  
								<button type="button" id='SAVEX' onclick='simpan()'   class="btn btn-success" class="fa fa-save"></i>Save</button>

							</div>
							<div class="col-md-3">
								<button hidden type="button" id='HAPUSX'  onclick="hapusTrans()" class="btn btn-outline-danger">Hapus</button>
								
								<!-- <button type="button" id='CLOSEX'  onclick="location.href='{{url('/jual?flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-outline-secondary">Close</button> -->
							
								<!-- tombol close sweet alert -->
								<button type="button" id='CLOSEX' onclick="closeTrans()" class="btn btn-outline-secondary">Close</button></div>
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
	


	<div class="modal fade" id="browseSuratsModal" tabindex="-1" role="dialog" aria-labelledby="browseSuratsModalLabel" aria-hidden="true">
	  <div class="modal-dialog mw-100 w-75" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseSuratsModalLabel">Cari Surat Jalan</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bsurats">
				<thead>
					<tr>
						<th>No Surat Jalan</th>
						<th>SO</th>
						<th>Customer</th>
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

	<div class="modal fade" id="browseJualModal" tabindex="-1" role="dialog" aria-labelledby="browseJualModalLabel" aria-hidden="true">
	  <div class="modal-dialog mw-100 w-75" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseJualModalLabel">Cari Penjualan</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bjual">
				<thead>
					<tr>
						<th>No Jual</th>
						<th>SO</th>
						<th>Customer</th>
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


	<div class="modal fade" id="browseBarangModal" tabindex="-1" role="dialog" aria-labelledby="browseBarangModalLabel" aria-hidden="true">
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
						<th>Qty</th>
						<th>Kirim</th>
						<th>Sisa</th>
						<th>Harga</th>	
						
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
						<th>Qty</th>
						<th>Kirim</th>
						<th>Sisa</th>
						<th>Harga</th>	
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

		$('body').on('keydown', 'input, select', function(e) {
			if (e.key === "Enter") {
				var self = $(this), form = self.parents('form:eq(0)'), focusable, next;
				focusable = form.find('input,select,textarea').filter(':visible');
				next = focusable.eq(focusable.index(this)+1);
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
		$searchx = $('#CARI').val();
		
		
        if ( $tipx == 'new' )
		{
			 baru();	
            //  tambah();				 
		}

        if ( $tipx != 'new' )
		{
			 ganti();			
		}    
		
		$("#TTOTAL_QTY").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TOTAL_QTY2").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TTOTAL").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TDISK").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#DISKG").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TPPN").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TDPP").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#NETT").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#DISK_GLOBAL").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TOTAL_TKOM").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});


		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#XQTY" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#KALI" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#QTY" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#QTY_KIRIM" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
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
			$("#KOM" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#TKOM" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});

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
		
		

//////////////////////////////////////////////////

		var dTableBSurats;
		loadDataBSurats = function(){
		
			$.ajax(
			{
				type: 'GET', 		
				url: '{{url('surats/browse')}}',
				data: {
					'GOL': "{{$golz}}",
				},

				beforeSend: function(){
					$("#LOADX").show();
				},

				success: function( response )
				{
					$("#LOADX").hide();

					resp = response;
					if(dTableBSurats){
						dTableBSurats.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBSurats.row.add([
							'<a href="javascript:void(0);" onclick="chooseSurats(\''+resp[i].NO_BUKTI+'\' , \''+resp[i].KODEC+'\',  \''+resp[i].NAMAC+'\', \''+resp[i].ALAMAT+'\',  \''+resp[i].KOTA+'\', \''+resp[i].HARI+'\',  \''+resp[i].KODEP+'\',  \''+resp[i].NAMAP+'\',  \''+resp[i].RING+'\',  \''+resp[i].PKP+'\', \''+resp[i].TOTAL_QTY2+'\' )">'+resp[i].NO_BUKTI+'</a>',
							resp[i].NO_SO,
							resp[i].KODEC,
							resp[i].NAMAC,
							resp[i].ALAMAT,
							resp[i].KOTA,
						]);
					}
					dTableBSurats.draw();
				}
			});
		}
		
		dTableBSurats = $("#table-bsurats").DataTable({
			
		});
		
		browseSurats = function(){
			loadDataBSurats();
			$("#browseSuratsModal").modal("show");
		}
		
		chooseSurats = function(NO_BUKTI, KODEC,NAMAC, ALAMAT, KOTA, HARI,  KODEP, NAMAP, RING, PKP, TOTAL_QTY2 ){
			$("#NO_SURATS").val(NO_BUKTI);
			$("#KODEC").val(KODEC);
			$("#NAMAC").val(NAMAC);
			$("#ALAMAT").val(ALAMAT);
			$("#KOTA").val(KOTA);			
			$("#HARI").val(HARI);
			$("#KODEP").val(KODEP);			
			$("#NAMAP").val(NAMAP);		
			$("#RING").val(RING);			
			$("#PKP").val(PKP);	
			$("#TOTAL_QTY2").val(TOTAL_QTY2);	
			$("#browseSuratsModal").modal("hide");

			// var PKP=$("#PKP").val();	
			
			// if (PKP == 1 ) 
			// {
			// $("#PKP").prop('checked', true)
			// } 
			// else 
			// {
			// $("#PKP").prop('checked', false)
			// }

			if ( $("#PKP").val() == '1' )
			{

				document.getElementById("PKP").checked = true;
					
			}
	
			else
			{
				document.getElementById("PKP").checked = false;
				
			}
            		
            jtempo();
            
			getSuratsd(NO_BUKTI);

		}
		
		$("#NO_SURATS").keypress(function(e){

			if(e.keyCode == 46){
				 e.preventDefault();
				 browseSurats();
			}
		}); 
////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////


function getJuald(bukti)
	{
		
		var mulai = (idrow==baris) ? idrow-1 : idrow;

		$.ajax(
			{
				type: 'GET',    
				url: "{{url('jual/browse_juald')}}",
				data: {
					nobukti: bukti,
				},
				success: function( resp )
				{
					var html = '';
					for(i=0; i<resp.length; i++){
						html+=`<tr>
									<td><input name='REC[]' id='REC${i}' value=${resp[i].REC+1} type='text' class='REC form-control' onkeypress='return tabE(this,event)' readonly></td>
                                    <td {{($golz == 'B') ? 'hidden' : '' }} ><input name='NO_SO[]' data-rowid=${i} id='NO_SO${i}' value="${resp[i].NO_SO}" type='text' class='form-control NO_SO' readonly></td>
                                    <td {{($golz == 'B') ? 'hidden' : '' }} ><input name='KD_BRG[]' data-rowid=${i} id='KD_BRG${i}' value="${resp[i].KD_BRG}" type='text' class='form-control KD_BRG' readonly></td>
                                    <td {{($golz == 'B') ? 'hidden' : '' }}><input name='NA_BRG[]' data-rowid=${i} id='NA_BRG${i}' value="${resp[i].NA_BRG}" type='text' class='form-control  NA_BRG' readonly></td>
                                    <td {{($golz == 'J' || $golz =='D') ? 'hidden' : '' }} ><input name='KD_BHN[]' data-rowid=${i} id='KD_BHN${i}' value="${resp[i].KD_BHN}" type='text' class='form-control KD_BHN' readonly></td>
                                    <td {{($golz == 'J' || $golz =='D') ? 'hidden' : '' }} ><input name='NA_BHN[]' data-rowid=${i} id='NA_BHN${i}' value="${resp[i].NA_BHN}" type='text' class='form-control  NA_BHN' readonly></td>
                                    <td><input name='SATUAN[]' data-rowid=${i} id='SATUAN${i}' value="${resp[i].SATUAN}" type='text' class='form-control  SATUAN' placeholder="Satuan"  readonly></td>
                                    <td>
										<input name='XQTY[]' onclick='select()' onkeyup='hitung()' id='XQTY${i}' value="${resp[i].QTY2}" type='text' style='text-align: right' class='form-control XQTY text-primary' >
									</td>
									<td>
										<input name='KALI[]' onclick='select()' onkeyup='hitung()' id='KALI${i}' value="${resp[i].KALI}" type='text' style='text-align: right' class='form-control KALI text-primary' readonly >
									</td>
									<td>
										<input name='QTY[]' onclick='select()' onkeyup='hitung()' id='QTY${i}' value="${resp[i].QTY}" type='text' style='text-align: right' class='form-control QTY text-primary' readonly >
										<input name='QTY_KIRIM[]' hidden onclick='select()' onkeyup='hitung()' id='QTY_KIRIM${i}' value="${resp[i].QTY_KIRIM}" type='text' style='text-align: right' class='form-control QTY_KIRIM text-primary' readonly >
									</td>
									<td>
										<input name='HARGA[]' onclick='select()' onkeyup='hitung()' id='HARGA${i}' value="${resp[i].HARGA}" type='text' style='text-align: right' class='form-control HARGA text-primary' readonly> 
									</td>
									<td>
										<input name='TOTAL[]' onclick='select()' onkeyup='hitung()' id='TOTAL${i}' value="${resp[i].TOTAL}" type='text' style='text-align: right' class='form-control TOTAL text-primary' readonly> 
										<input name='PPNX[]'hidden  onclick='select()' onkeyup='hitung()' id='PPNX${i}' value="${resp[i].PPN}" type='text' style='text-align: right' class='form-control PPNX text-primary' readonly> 
										<input name='DPP[]' hidden onclick='select()' onkeyup='hitung()' id='DPP${i}' value="${resp[i].DPP}" type='text' style='text-align: right' class='form-control DPP text-primary' readonly> 
                                        <input name='TYPE_KOM[]' hidden id='TYPE_KOM${i}' value="${resp[i].TYPE_KOM}" type='text' class='form-control  TYPE_KOM' readonly>
										<input name='KOM[]' hidden onclick='select()' onkeyup='hitung()' id='KOM${i}' value="${resp[i].KOM}" type='text' style='text-align: right' class='form-control KOM text-primary' readonly> 
										<input name='TKOM[]' hidden onclick='select()' onkeyup='hitung()' id='TKOM${i}' value="${resp[i].TKOM}" type='text' style='text-align: right' class='form-control TKOM text-primary' readonly> 
									</td>
									<td>
										<input name='DISKA[]' onclick='select()' onkeyup='hitung()' id='DISKA${i}' value="${resp[i].DISK1}" type='text' style='text-align: right' class='form-control DISKA text-primary' readonly> 
                                    </td>
									<td>
										<input name='DISKB[]' onclick='select()' onkeyup='hitung()' id='DISKB${i}' value="${resp[i].DISK2}" type='text' style='text-align: right' class='form-control DISKB text-primary' readonly> 
                                    </td>
									<td>
										<input name='DISKC[]' onclick='select()' onkeyup='hitung()' id='DISKC${i}' value="${resp[i].DISK3}" type='text' style='text-align: right' class='form-control DISKC text-primary' readonly> 
                                    </td>
									<td>
										<input name='DISKD[]' onclick='select()' onkeyup='hitung()' id='DISKD${i}' value="${resp[i].DISK4}" type='text' style='text-align: right' class='form-control DISKD text-primary' readonly> 
                                    </td>
									<td>
										<input name='DISKE[]' onclick='select()' onkeyup='hitung()' id='DISKE${i}' value="${resp[i].DISK5}" type='text' style='text-align: right' class='form-control DISKE text-primary' readonly> 
                                    </td>
									<td>
										<input name='DISK[]' onclick='select()' onkeyup='hitung()' id='DISK${i}' value="${resp[i].DISK}" type='text' style='text-align: right' class='form-control DISK text-primary' readonly> 
                                    </td>
									<td>
                                        <input name='KET[]' id='KET${i}' value="${resp[i].KET}" type='text' class='form-control  KET' readonly>
										<button hidden type='button' class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button>
									</td>
								</tr>`;
					}
					$('#detailJuald').html(html);

					$(".XQTY").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".XQTY").autoNumeric('update');

					$(".KALI").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".KALI").autoNumeric('update');

					$(".QTY").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".QTY").autoNumeric('update');

					$(".QTY_KIRIM").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".QTY_KIRIM").autoNumeric('update');

					$(".HARGA").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".HARGA").autoNumeric('update');

					$(".TOTAL").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".TOTAL").autoNumeric('update');

					$(".PPNX").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".PPNX").autoNumeric('update');

					$(".DPP").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".DPP").autoNumeric('update');

					$(".DISKA").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".DISKA").autoNumeric('update');

					$(".DISKB").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".DISKB").autoNumeric('update');

					$(".DISKC").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".DISKC").autoNumeric('update');

					$(".DISKD").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".DISKD").autoNumeric('update');

					$(".DISKE").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".DISKE").autoNumeric('update');

					$(".DISK").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".DISK").autoNumeric('update');

					$(".KOM").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".KOM").autoNumeric('update');

					$(".TKOM").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".TKOM").autoNumeric('update');
					
					idrow=resp.length;
					baris=resp.length;

					nomor();
					hitung();
				}
			});
	}

//////////////////////////////////////////////////////////////////

	function getSuratsd(bukti)
	{
		
		var mulai = (idrow==baris) ? idrow-1 : idrow;

		$.ajax(
			{
				type: 'GET',    
				url: "{{url('surats/browse_suratsd')}}",
				data: {
					nobukti: bukti,
				},
				success: function( resp )
				{
					var html = '';
					for(i=0; i<resp.length; i++){
						html+=`<tr>
                                    <td><input name='REC[]' id='REC${i}' value=${resp[i].REC+1} type='text' class='REC form-control' onkeypress='return tabE(this,event)' readonly></td>
                                    <td {{($golz == 'B') ? 'hidden' : '' }} ><input name='NO_SO[]' data-rowid=${i} id='NO_SO${i}' value="${resp[i].NO_SO}" type='text' class='form-control NO_SO' readonly></td>
                                    <td {{($golz == 'B') ? 'hidden' : '' }} ><input name='KD_BRG[]' data-rowid=${i} id='KD_BRG${i}' value="${resp[i].KD_BRG}" type='text' class='form-control KD_BRG' readonly></td>
                                    <td {{($golz == 'B') ? 'hidden' : '' }}><input name='NA_BRG[]' data-rowid=${i} id='NA_BRG${i}' value="${resp[i].NA_BRG}" type='text' class='form-control  NA_BRG' readonly></td>
                                    <td {{($golz == 'J' || $golz =='D') ? 'hidden' : '' }} ><input name='KD_BHN[]' data-rowid=${i} id='KD_BHN${i}' value="${resp[i].KD_BHN}" type='text' class='form-control KD_BHN' readonly></td>
                                    <td {{($golz == 'J' || $golz =='D') ? 'hidden' : '' }} ><input name='NA_BHN[]' data-rowid=${i} id='NA_BHN${i}' value="${resp[i].NA_BHN}" type='text' class='form-control  NA_BHN' readonly></td>
                                    <td><input name='SATUAN[]' data-rowid=${i} id='SATUAN${i}' value="${resp[i].SATUAN}" type='text' class='form-control  SATUAN' placeholder="Satuan"  readonly></td>
                                    
									<td>
										<input name='XQTY[]' onclick='select()' onkeyup='hitung()' id='XQTY${i}' value="${resp[i].QTY2}" type='text' style='text-align: right' class='form-control XQTY text-primary' readonly> 
									</td>
									
									<td>
										<input name='KALI[]' onclick='select()' onkeyup='hitung()' id='KALI${i}' value="${resp[i].KALI}" type='text' style='text-align: right' class='form-control KALI text-primary' readonly> 
									</td>
									<td>
										<input name='QTY[]' onclick='select()' onkeyup='hitung()' id='QTY${i}' value="${resp[i].QTY}" type='text' style='text-align: right' class='form-control QTY text-primary' readonly >
										<input name='QTY_KIRIM[]' hidden onclick='select()' onkeyup='hitung()' id='QTY_KIRIM${i}' value="${resp[i].QTY_KIRIM}" type='text' style='text-align: right' class='form-control QTY_KIRIM text-primary' readonly >
									</td>
									<td>
										<input name='HARGA[]' onclick='select()' onkeyup='hitung()' id='HARGA${i}' value="${resp[i].HARGA}" type='text' style='text-align: right' class='form-control HARGA text-primary' readonly> 
									</td>
									<td>
										<input name='TOTAL[]' onclick='select()' onkeyup='hitung()' id='TOTAL${i}' value="${resp[i].TOTAL}" type='text' style='text-align: right' class='form-control TOTAL text-primary' readonly> 
										<input name='PPNX[]'hidden  onclick='select()' onkeyup='hitung()' id='PPNX${i}' value="${resp[i].PPN}" type='text' style='text-align: right' class='form-control PPNX text-primary' readonly> 
										<input name='DPP[]' hidden onclick='select()' onkeyup='hitung()' id='DPP${i}' value="${resp[i].DPP}" type='text' style='text-align: right' class='form-control DPP text-primary' readonly> 
                                        <input name='TYPE_KOM[]' hidden id='TYPE_KOM${i}' value="${resp[i].TYPE_KOM}" type='text' class='form-control  TYPE_KOM' readonly>
										<input name='KOM[]' hidden onclick='select()' onkeyup='hitung()' id='KOM${i}' value="${resp[i].KOM}" type='text' style='text-align: right' class='form-control KOM text-primary' readonly> 
										<input name='TKOM[]' hidden onclick='select()' onkeyup='hitung()' id='TKOM${i}' value="0" type='text' style='text-align: right' class='form-control TKOM text-primary' readonly> 
									</td>
									<td>
										<input name='DISKA[]' onclick='select()' onkeyup='hitung()' id='DISKA${i}' value="${resp[i].DISK1}" type='text' style='text-align: right' class='form-control DISKA text-primary' readonly> 
                                    </td>
									<td>
										<input name='DISKB[]' onclick='select()' onkeyup='hitung()' id='DISKB${i}' value="${resp[i].DISK2}" type='text' style='text-align: right' class='form-control DISKB text-primary' readonly> 
                                    </td>
									<td>
										<input name='DISKC[]' onclick='select()' onkeyup='hitung()' id='DISKC${i}' value="${resp[i].DISK3}" type='text' style='text-align: right' class='form-control DISKC text-primary' readonly> 
                                    </td>
									<td>
										<input name='DISKD[]' onclick='select()' onkeyup='hitung()' id='DISKD${i}' value="${resp[i].DISK4}" type='text' style='text-align: right' class='form-control DISKD text-primary' readonly> 
                                    </td>
									<td>
										<input name='DISKE[]' onclick='select()' onkeyup='hitung()' id='DISKE${i}' value="${resp[i].DISK5}" type='text' style='text-align: right' class='form-control DISKE text-primary' readonly> 
                                    </td>
									<td>
										<input name='DISK[]' onclick='select()' onkeyup='hitung()' id='DISK${i}' value="${resp[i].DISK}" type='text' style='text-align: right' class='form-control DISK text-primary' readonly> 
                                    </td>
									<td>
                                        <input name='KET[]' id='KET${i}' value="${resp[i].KET}" type='text' class='form-control  KET' readonly>
										<button hidden type='button' class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button>
									</td>
                                </tr>`;
					}
					$('#detailJuald').html(html);

					$(".XQTY").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".XQTY").autoNumeric('update');

					$(".KALI").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".KALI").autoNumeric('update');

					$(".QTY").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".QTY").autoNumeric('update');

					$(".QTY_KIRIM").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".QTY_KIRIM").autoNumeric('update');

					$(".HARGA").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".HARGA").autoNumeric('update');

					$(".TOTAL").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".TOTAL").autoNumeric('update');

					$(".PPNX").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".PPNX").autoNumeric('update');

					$(".DPP").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".DPP").autoNumeric('update');

					$(".DISKA").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".DISKA").autoNumeric('update');

					$(".DISKB").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".DISKB").autoNumeric('update');

					$(".DISKC").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".DISKC").autoNumeric('update');

					$(".DISKD").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".DISKD").autoNumeric('update');

					$(".DISKE").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".DISKE").autoNumeric('update');

					$(".DISK").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".DISK").autoNumeric('update');

					$(".KOM").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".KOM").autoNumeric('update');

					$(".TKOM").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".TKOM").autoNumeric('update');



					/*
					$(".KD_BHN").each(function() {
						var getid = $(this).attr('id');
						var noid = getid.substring(6,11);

						$("#KD_BHN"+noid).keypress(function(e){
							if(e.keyCode == 46){
								e.preventDefault();
								browseBhn(noid);
							}
						}); 
					});*/

					idrow=resp.length;
					baris=resp.length;

					nomor();
					hitung();
				}
			});
	}

//////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////

		var dTableBJual;
		loadDataBJual = function(){
		
			$.ajax(
			{
				type: 'GET', 		
				url: '{{url('jual/browse')}}',
				data: {
					'GOL': "{{$golz}}",
				},

				beforeSend: function(){
					$("#LOADX").show();
				},

				success: function( response )
				{

					$("#LOADX").hide();

					resp = response;
					if(dTableBJual){
						dTableBJual.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBJual.row.add([
							'<a href="javascript:void(0);" onclick="chooseJual(\''+resp[i].NO_BUKTI+'\' , \''+resp[i].NO_SO+'\', \''+resp[i].KODEC+'\',  \''+resp[i].NAMAC+'\', \''+resp[i].ALAMAT+'\',  \''+resp[i].KOTA+'\',  \''+resp[i].PKP+'\')">'+resp[i].NO_BUKTI+'</a>',
							resp[i].NO_SO,
							resp[i].KODEC,
							resp[i].NAMAC,
							resp[i].ALAMAT,
							resp[i].KOTA,
						]);
					}
					dTableBJual.draw();
				}
			});
		}
		
		dTableBJual = $("#table-bjual").DataTable({
			
		});
		
		browseJual = function(){
			loadDataBJual();
			$("#browseJualModal").modal("show");
		}
		
		chooseJual = function(NO_BUKTI, NO_SO, KODEC,NAMAC, ALAMAT, KOTA, PKP){
			$("#NO_JUAL").val(NO_BUKTI);
			$("#NO_SO").val(NO_SO);
			$("#KODEC").val(KODEC);
			$("#NAMAC").val(NAMAC);
			$("#ALAMAT").val(ALAMAT);
			$("#KOTA").val(KOTA);			
			$("#PKP").val(PKP);			
			$("#browseJualModal").modal("hide");

			

			if ( $("#PKP").val() == '1' )
			{

				document.getElementById("PKP").checked = true;
					
			}
	
			else
			{
				document.getElementById("PKP").checked = false;
				
			}

			getJuald(NO_BUKTI);
		}
		
		$("#NO_JUAL").keypress(function(e){

			if(e.keyCode == 46){
				 e.preventDefault();
				 browseJual();
			}
		}); 

////////////////////////////////////////////////////////////////////

		//CHOOSE Bahan
		var dTableBBahan;
		var rowidBahan;
		loadDataBBahan = function(){
			$.ajax(
			{
				type: 'GET',    
				url: "{{url('so/browse_detail')}}",
				data: 
				{
                    KD_BHN : $("#KD_BHN"+rowidBahan).val(),
                    NO_SO : $("#NO_SO").val(), 					
				},
				success: function( response )
				{
					resp = response;
					if(dTableBBahan){
						dTableBBahan.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBBahan.row.add([
							'<a href="javascript:void(0);" onclick="chooseBahan(\''+resp[i].KD_BHN+'\',\''+resp[i].NA_BHN+'\', \''+resp[i].SATUAN+'\' , \''+resp[i].QTY+'\', \''+resp[i].KIRIM+'\' , \''+resp[i].SISA+'\' , \''+resp[i].HARGA+'\', \''+resp[i].DPP+'\', \''+resp[i].PPN+'\' )">'+resp[i].KD_BHN+'</a>',
							resp[i].NA_BHN,
							resp[i].SATUAN,
							resp[i].QTY,
							resp[i].KIRIM,
							resp[i].SISA,
							resp[i].HARGA,
							
						]);
					}
					dTableBBahan.draw();
				}
			});
		}
		
		dTableBBahan = $("#table-bbahan").DataTable({
			
		});
		
		browseBahan = function(rid){
			rowidBahan = rid;
			loadDataBBahan();
			$("#browseBahanModal").modal("show");
		}
		
		chooseBahan = function(KD_BHN, NA_BHN, SATUAN, QTY, KIRIM, SISA, HARGA, DPP, PPN ){
			$("#KD_BHN"+rowidBahan).val(KD_BHN);
			$("#NA_BHN"+rowidBahan).val(NA_BHN);
			$("#SATUAN"+rowidBahan).val(SATUAN);
			$("#QTY"+rowidBahan).val(SISA);
			$("#HARGA"+rowidBahan).val(HARGA);
			$("#DPP"+rowidBahan).val(DPP);
			$("#PPN"+rowidBahan).val(PPN);
			hitung();
			
			
			$("#browseBahanModal").modal("hide");
		}
		
		
		$("#KD_BHN0").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseBahan(0);
			}
		}); 

//////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////
		
 		var dTableBBarang;
		var rowidBarang;
		loadDataBBarang = function(){
			$.ajax(
			{
				type: 'GET',    
				url: "{{url('so/browse_detail2')}}",
				data: 
				{
                    KD_BRG : $("#KD_BRG"+rowidBarang).val(),
                    NO_SO : $("#NO_SO").val(), 					
				},				
				
				success: function( response )
				{
					resp = response;
					if(dTableBBarang){
						dTableBBarang.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBBarang.row.add([
							'<a href="javascript:void(0);" onclick="chooseBarang(\''+resp[i].KD_BRG+'\',\''+resp[i].NA_BRG+'\', \''+resp[i].SATUAN+'\' , \''+resp[i].SISA+'\' , \''+resp[i].HARGA+'\' , \''+resp[i].DPP+'\' , \''+resp[i].PPN+'\'  )">'+resp[i].KD_BRG+'</a>',
							resp[i].NA_BRG,
							resp[i].SATUAN,
							resp[i].QTY,
							resp[i].KIRIM,
							resp[i].SISA,
							resp[i].HARGA,							
						]);
					}
					dTableBBarang.draw();
				}
			});
		}
		
		dTableBBarang = $("#table-bbarang").DataTable({
			
		});
		
		browseBarang = function(rid){
			rowidBarang = rid;
			loadDataBBarang();
			$("#browseBarangModal").modal("show");
		}
		
		chooseBarang = function(KD_BRG, NA_BRG, SATUAN, SISA, HARGA, DPP, PPN){
			$("#KD_BRG"+rowidBarang).val(KD_BRG);
			$("#NA_BRG"+rowidBarang).val(NA_BRG);
			$("#SATUAN"+rowidBarang).val(SATUAN);
			$("#QTY"+rowidBarang).val(SISA);
			$("#HARGA"+rowidBarang).val(HARGA);			
			$("#DPP"+rowidBarang).val(DPP);			
			$("#PPNX"+rowidBarang).val(PPN);			
			$("#browseBarangModal").modal("hide");
			hitung();
		}
		
		
		// $("#KD_BRG0").keypress(function(e){
		// 	if(e.keyCode == 46){
		// 		e.preventDefault();
		// 		browseBarang(0);
		// 	}
		// }); 
	});




//////////////////////////////////////////////////////////////////


	function simpan() {

		hitung();
		
		var tgl = $('#TGL').val();
		var bulanPer = {{session()->get('periode')['bulan']}};
		var tahunPer = {{session()->get('periode')['tahun']}};
		
        var check = '0';
		
		
			if ( tgl.substring(3,5) != bulanPer ) 
			{
				
				check = '1';
				Swal.fire({
					icon: 'warning',
					title: 'Warning',
					text: 'Bulan tidak sama dengan Periode'
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
////////////////////////////////////////////////////////////

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

        
			if ( $('#KODEC').val()=='' ) 
            {				
			    check = '1';
				Swal.fire({
					icon: 'warning',
					title: 'Warning',
					text: 'Customer# Harus Diisi.'
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
 
	
	
	function hitung() {
		var TTOTAL_QTY = 0;
		var TOTAL_QTY2 = 0;
		var TTOTAL = 0;
		var TPPNX = 0;
		var TDPPX = 0;
		var NETTX = 0;
		var TDISK = 0;
		var TOTAL_TKOMX = 0;


		$(".QTY").each(function() {
			
			let z = $(this).closest('tr');
			var QTYX = parseFloat(z.find('.QTY').val().replace(/,/g, ''));
			var QTY2X = parseFloat(z.find('.XQTY').val().replace(/,/g, ''));
			var KALIX = parseFloat(z.find('.KALI').val().replace(/,/g, ''));
			var HARGAX = parseFloat(z.find('.HARGA').val().replace(/,/g, ''));
			var PPNX = parseFloat(z.find('.PPNX').val().replace(/,/g, ''));
			var DISKX = parseFloat(z.find('.DISK').val().replace(/,/g, ''));
			var DISKAX = parseFloat(z.find('.DISKA').val().replace(/,/g, ''));
			var DISKBX = parseFloat(z.find('.DISKB').val().replace(/,/g, ''));
			var DISKCX = parseFloat(z.find('.DISKC').val().replace(/,/g, ''));
			var DISKDX = parseFloat(z.find('.DISKD').val().replace(/,/g, ''));
			var DISKEX = parseFloat(z.find('.DISKE').val().replace(/,/g, ''));
			var KOMX = parseFloat(z.find('.KOM').val().replace(/,/g, ''));
			var TKOMX = 0;

			var FLAGZ = $('#flagz').val();
			var PKP = $('#PKP').val();
			
			
			if (FLAGZ == 'AJ'){
				
				// var QTY2X  = ( QTY2X * -1 ) ;	
				// var DISKX  = ( DISKX * -1 ) ;	

				QTYX = (QTY2X * KALIX) * -1;
				
				z.find('.QTY').val(QTYX);
				
				z.find('.QTY').autoNumeric('update');
				z.find('.DISK').autoNumeric('update');

			}

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
				 TOTALDISKEX = ( ( QTY2X * HARGAX ) - TOTALDISKAX  - TOTALDISKBX - TOTALDISKCX - TOTALDISKDX )  * DISKDX / 100  ;

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
		    z.find('.TKOM').autoNumeric('update');	

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
		
			z.find('.QTY').val(QTYX);			
		    z.find('.QTY').autoNumeric('update');

			z.find('.XQTY').val(QTY2X);			
		    z.find('.XQTY').autoNumeric('update');

			z.find('.HARGA').val(HARGAX);			
		    z.find('.HARGA').autoNumeric('update');

			z.find('.TOTAL').val(TOTAL1X);			
		    z.find('.TOTAL').autoNumeric('update');

			z.find('.PPN').val(PPNX);			
		    z.find('.PPN').autoNumeric('update');
		    
			z.find('.DPP').val(DPPX);			
		    z.find('.DPP').autoNumeric('update');
		    
            TTOTAL_QTY +=QTYX;				
            TOTAL_QTY2 +=QTY2X;				
            TTOTAL +=TOTAL1X;
			TPPNX += PPNX;	
			TDPPX += DPPX;
			TDISK += DISKX;	
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
		
		if(isNaN(TTOTAL_QTY)) TTOTAL_QTY = 0;

		$('#TTOTAL_QTY').val(numberWithCommas(TTOTAL_QTY));		
		$("#TTOTAL_QTY").autoNumeric('update');

		
		if(isNaN(TOTAL_QTY2)) TOTAL_QTY2 = 0;

		$('#TOTAL_QTY2').val(numberWithCommas(TOTAL_QTY2));		
		$("#TOTAL_QTY2").autoNumeric('update');
		

		if(isNaN(TOTAL_TKOMX)) TOTAL_TKOMX = 0;

		$('#TOTAL_TKOM').val(numberWithCommas(TOTAL_TKOMX));		
		$("#TOTAL_TKOM").autoNumeric('update');



		if(isNaN(TTOTAL)) TTOTAL_QTY = 0;

		$('#TTOTAL').val(numberWithCommas(TTOTAL));		
		$("#TTOTAL").autoNumeric('update');

		if(isNaN(TDISK)) TDISK = 0;

		$('#TDISK').val(numberWithCommas(TDISK));		
		$("#TDISK").autoNumeric('update');


		$('#TDPP').val(numberWithCommas(TDPPX));		
		$("#TDPP").autoNumeric('update');
		
		$('#TPPN').val(numberWithCommas(TPPNX));		
		$("#TPPN").autoNumeric('update');

		$('#NETT').val(numberWithCommas(NETTX));		
		$("#NETT").autoNumeric('update');

		
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
		    $("#JTEMPO").attr("readonly", false);
			$("#KODEC").attr("readonly", true);			
			$("#NAMAC").attr("readonly", true);
			$("#ALAMAT").attr("readonly", true);
			$("#KOTA").attr("readonly", true);			
			$("#NOTES").attr("readonly", false);			
			$("#DISKG").attr("readonly", false);
			$("#NO_FP").attr("readonly", false);
		
			$("#PKP").attr("disabled", true);
			$("#TGL_FP").attr("disabled", false);
			$("#DISK_GLOBAL").attr("readonly", false);
	

		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#REC" + i.toString()).attr("readonly", true);
			$("#KD_BHN" + i.toString()).attr("readonly", true);
			$("#KD_BRG" + i.toString()).attr("readonly", true);
			$("#NO_SO" + i.toString()).attr("readonly", true);
			$("#NA_BHN" + i.toString()).attr("readonly", true);
			$("#NA_BRG" + i.toString()).attr("readonly", true);
			$("#SATUAN" + i.toString()).attr("readonly", true);
			$("#XQTY" + i.toString()).attr("readonly", true);
			$("#KALI" + i.toString()).attr("readonly", true);
			$("#QTY" + i.toString()).attr("readonly", true);
			$("#QTY_KIRIM" + i.toString()).attr("readonly", true);
			$("#HARGA" + i.toString()).attr("readonly", true);
			$("#TOTAL" + i.toString()).attr("readonly", true);
			$("#DPP" + i.toString()).attr("readonly", true);
			$("#PPN" + i.toString()).attr("readonly", true);
			$("#KET" + i.toString()).attr("readonly", true);

			$("#DISKA" + i.toString()).attr("readonly", true);
			$("#DISKB" + i.toString()).attr("readonly", true);
			$("#DISKC" + i.toString()).attr("readonly", true);
			$("#DISKD" + i.toString()).attr("readonly", true);
			$("#DISKE" + i.toString()).attr("readonly", true);

			$("#DISK" + i.toString()).attr("readonly", true);
			$("#KET" + i.toString()).attr("readonly", true);

			$("#DELETEX" + i.toString()).attr("hidden", false);

			$tipx = $('#tipx').val();
		
			
			if ( $tipx != 'new' )
			{
				$("#KD_BHN" + i.toString()).attr("readonly", true);	
				$("#KD_BHN" + i.toString()).removeAttr('onblur');
				
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
		$("#JTEMPO").attr("readonly", true);
		$("#KODEC").attr("readonly", true);			
		$("#NAMAC").attr("readonly", true);
		$("#ALAMAT").attr("readonly", true);
		$("#KOTA").attr("readonly", true);	
		$("#NOTES").attr("readonly", true);
	    $("#PKP").attr("disabled", true);			
		$("#DISKG").attr("readonly", true);
		
		$("#NO_FP").attr("readonly", true);
		$("#TGL_FP").attr("disabled", true);
	
		$("#DISK_GLOBAL").attr("readonly", true);

		
		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#REC" + i.toString()).attr("readonly", true);
			$("#KD_BHN" + i.toString()).attr("readonly", true);
			$("#NA_BHN" + i.toString()).attr("readonly", true);
			$("#KD_BRG" + i.toString()).attr("readonly", true);
			$("#NO_SO" + i.toString()).attr("readonly", true);
			$("#NA_BRG" + i.toString()).attr("readonly", true);
			$("#SATUAN" + i.toString()).attr("readonly", true);
			$("#QTY" + i.toString()).attr("readonly", true);
			$("#XQTY" + i.toString()).attr("readonly", true);
			$("#KALI" + i.toString()).attr("readonly", true);
			$("#QTY_KIRIM" + i.toString()).attr("readonly", true);
			$("#HARGA" + i.toString()).attr("readonly", true);
			$("#TOTAL" + i.toString()).attr("readonly", true);
			$("#DPP" + i.toString()).attr("readonly", true);
			$("#PPNX" + i.toString()).attr("readonly", true);
			$("#KET" + i.toString()).attr("readonly", true);
			$("#DISKA" + i.toString()).attr("readonly", true);
			$("#DISKB" + i.toString()).attr("readonly", true);
			$("#DISKC" + i.toString()).attr("readonly", true);
			$("#DISKD" + i.toString()).attr("readonly", true);
			$("#DISKE" + i.toString()).attr("readonly", true);

			$("#DISK" + i.toString()).attr("readonly", true);
			$("#KET" + i.toString()).attr("readonly", true);
			$("#DELETEX" + i.toString()).attr("hidden", true);
		}


		
	}


	function kosong() {
				
		 $('#NO_BUKTI').val("+");	
		 $('#NO_SO').val("");
		 $('#KODEC').val("");
		 $('#NAMAC').val("");
		 $('#ALAMAT').val("");
		 $('#KOTA').val("");		 
	//	 $('#TGL').val("");	
		 $('#NOTES').val("");	
		 $('#TTOTAL_QTY').val("0.00");	
		 $('#TTOTAL').val("0.00");

		 $('#PPN').val("0.00");	
		 $('#NETT').val("0.00");
		 $('#DPP').val("0.00");
		 $('#PPNX').val("0.00");
		 
		var html = '';
		$('#detailx').html(html);	
		
	}
	
	// function hapusTrans() {
	// 	let text = "Hapus Transaksi "+$('#NO_BUKTI').val()+"?";
	// 	if (confirm(text) == true) 
	// 	{
	// 		window.location ="{{url('/jual/delete/'.$header->NO_ID .'/?flagz='.$flagz.'&golz=' .$golz.'' )}}";
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
	            	loc = "{{ url('/jual/delete/'.$header->NO_ID) }}" + '?flagz=' + encodeURIComponent(flagz) + 
						  '&golz=' + encodeURIComponent(golz) ;

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
	        	loc = "{{ url('/jual/') }}" + '?flagz=' + encodeURIComponent(flagz) + '&golz=' + encodeURIComponent(golz) ;
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
		var cari = $("#CARI").val();
		var loc = "{{ url('/jual/edit/') }}" + '?idx={{ $header->NO_ID}}&tipx=search&flagz=' + encodeURIComponent(flagz) + '&golz=' + encodeURIComponent(golz) + '&buktix=' +encodeURIComponent(cari);
		window.location = loc;
		
	}


    function jtempo() {

		    // alert('ho');
			
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



    function tambah() {

        var x = document.getElementById('datatable').insertRow(baris + 1);
 
		html=`<tr>

                <td>
 					<input name='NO_ID[]' id='NO_ID${idrow}' type='hidden' class='form-control NO_ID' value='new' readonly> 
					<input name='REC[]' id='REC${idrow}' type='text' class='REC form-control' onkeypress='return tabE(this,event)' readonly>
	            </td>
						       
                <td {{( $golz =='B') ? '' : 'hidden' }} >
				    <input name='KD_BHN[]' data-rowid=${idrow} onblur='browseBahan(${idrow})' id='KD_BHN${idrow}' type='text' class='form-control  KD_BHN' >
                </td>
                <td {{( $golz =='B') ? '' : 'hidden' }} >
				    <input name='NA_BHN[]'   id='NA_BHN${idrow}' type='text' class='form-control  NA_BHN' required readonly>
                </td>

				<td {{( $golz =='J' || $golz =='D') ? '' : 'hidden' }} >
				    <input name='KD_BRG[]' data-rowid=${idrow} onblur='browseBarang(${idrow})' id='KD_BRG${idrow}' type='text' class='form-control  KD_BRG' >
                </td>
                <td {{( $golz =='J' || $golz =='D') ? '' : 'hidden' }} >
				    <input name='NA_BRG[]'   id='NA_BRG${idrow}' type='text' class='form-control  NA_BRG' required readonly>
                </td>

                <td>
				    <input name='SATUAN[]'   id='SATUAN${idrow}' type='text' class='form-control  SATUAN' readonly required>
                </td>
				
				<td>
		            <input name='QTY[]'  onblur='hitung()' value='1' id='QTY${idrow}' type='text' style='text-align: right' class='form-control QTY text-primary' required >
                </td>

				<td>
		            <input name='HARGA[]'  onblur='hitung()' value='0' id='HARGA${idrow}' type='text' style='text-align: right' class='form-control HARGA text-primary' required >
                </td>
				
				<td>
		            <input name='TOTAL[]'  onblur='hitung()' value='0' id='TOTAL${idrow}' type='text' style='text-align: right' class='form-control TOTAL text-primary' required >
		            <input name='DPP[]' hidden  onblur='hitung()' value='0' id='DPP${idrow}' type='text' style='text-align: right' class='form-control DPP text-primary' readonly >
		            <input name='PPNX[]'  hidden onblur='hitung()' value='0' id='PPNX${idrow}' type='text' style='text-align: right' class='form-control PPNX text-primary' readonly >
                </td>
				
				<td>
		            <input name='DISK[]'  onblur='hitung()' value='0' id='DISK${idrow}' type='text' style='text-align: right' class='form-control DISK text-primary' readonly >
				    <input name='KET[]'  hidden  id='KET${idrow}' type='text' class='form-control  KET' required>
                </td>
				
                <td>
					<button hidden type='button' id='DELETEX${idrow}'  class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button>
                </td>				
         </tr>`;
				
        x.innerHTML = html;
        var html='';
		
 

		jumlahdata = 100;
		
		for (i = 0; i <= jumlahdata; i++) {
			$("#QTY" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#HARGA" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#TOTAL" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#PPNX" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#DPP" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			
			
			
			$("#DISK" + i.toString()).autoNumeric('init', {
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
		$(".ronly").on('keydown paste', function(e) {
			e.preventDefault();
			e.currentTarget.blur();
		});
     }
</script>



<script src="autonumeric.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
<script src="https://unpkg.com/autonumeric"></script>

@endsection