@extends('layouts.plain')

<style>
    .card {

    }

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

                    <form action="{{($tipx=='new')? url('/kirim/store?flagz='.$flagz.'&golz='.$golz.'') : url('/kirim/update/'.$header->NO_ID.'&flagz='.$flagz.'&golz='.$golz.'' ) }}" method="POST" name ="entri" id="entri" >
  
                        @csrf
                        <div class="tab-content mt-3">
                            <div class="form-group row">
                                <div class="col-md-1" align="left">
                                    <label for="NO_BUKTI" class="form-label">No Bukti</label>
                                </div>
								

                                   <input type="text" class="form-control NO_ID" id="NO_ID" name="NO_ID"
                                    placeholder="Masukkan NO_ID" value="{{$header->NO_ID ?? ''}}" hidden readonly>

									<input name="tipx" class="form-control tipx" id="tipx" value="{{$tipx}}" hidden>
									<input name="flagz" class="form-control flagz" id="flagz" value="{{$flagz}}" hidden>
									<input name="golz" class="form-control golz" id="golz" value="{{$golz}}" hidden>

								
								
                                <div class="col-md-2">
                                    <input type="text" class="form-control NO_BUKTI" id="NO_BUKTI" name="NO_BUKTI"
                                    placeholder="Masukkan Bukti#" value="{{$header->NO_BUKTI}}" readonly>
                                </div>

								<div class="col-md-1" align="left">	
									<label for="CNT" class="form-label">Counter#</label>
								</div>
								
								<div class="col-md-2" >
									<select id="CNT" name="CNT" style="width: 100%" ></select>        							      
									<input type="text" hidden class="form-control NCNT" id="NCNT" name="NCNT" value="{{$header->NCNT}}" placeholder="" >
								</div>

								<div class="col-md-1" >
									<label for="CBG" class="form-label">Cabang Tujuan</label>
								</div>
								<div class="col-md-2">
									<select id="CBG" class="form-control"  name="CBG">
										<option value="TGZ" {{ ($header->CBG == 'TGZ') ? 'selected' : '' }}>TGZ</option>
										<option value="TMM" {{ ($header->CBG == 'TMM') ? 'selected' : '' }}>TMM</option>
									</select>
								</div>


								<!-- <div class="col-md-2" >
									<input type="checkbox" class="form-check-input" id="POSTED" name="POSTED" readonly  value="{{$header->POSTED}}" {{ ($header->POSTED == 1) ? 'checked' : '' }}>
									<label for="POSTED" class="form-label">Posted</label>
									<input type="text" hidden class="form-control ZPOSTED" id="ZPOSTED" name="ZPOSTED" value="{{$header->POSTED}}" placeholder="" >

								</div> -->

								<div class="col-md-1">
									<input type="checkbox" class="form-check-input" id="POSTED" name="POSTED" value="1" {{ ($header->POSTED == 1) ? 'checked' : '' }}>
									<label for="POSTED">Posted</label>
								</div>
								
								
                            </div>

							<div class="form-group row">

								<div class="col-md-1" align="left">							
                                    <label for="NO_PO" class="form-label">No Beli</label>
                                </div>
                               	<div class="col-md-2 input-group" >
                                  <input type="text" class="form-control NO_PO" id="NO_PO" name="NO_PO" placeholder="Pilih PO"value="{{$header->NO_PO}}" style="text-align: left" readonly >
        						  <button type="button" class="btn btn-primary" onclick="browsePo()"><i class="fa fa-search"></i></button>
                                </div>
								
								<div class="col-md-1" align="left">							
                                    <label  class="form-label">Supplier</label>
                                </div>		
								<div class="col-md-3" >
									<select id="KODES" name="KODES" style="width: 100%" ></select>        							      
									<input type="text" hidden class="form-control NAMAS" id="NAMAS" name="NAMAS" value="{{$header->NAMAS}}" placeholder="" >
								</div>

                            </div>

							<div class="form-group row">


                                <div class="col-md-1" align="left">
                                    <label for="TGL" class="form-label">Tgl</label>
                                </div>
                                <div class="col-md-2">
								  <input class="form-control date" id="TGL" name="TGL" data-date-format="dd-mm-yyyy" type="text" autocomplete="off" value="{{date('d-m-Y',strtotime($header->TGL))}}">
                                </div>

								<div class="col-md-1" align="left">
                                    <label for="JTEMPO" class="form-label">Jatuh Tempo</label>
                                </div>
                                <div class="col-md-2">
								  <input class="form-control date" id="JTEMPO" name="JTEMPO" data-date-format="dd-mm-yyyy" type="text" autocomplete="off" value="{{date('d-m-Y',strtotime($header->JTEMPO))}}">
                                </div>
								
                            </div>


                            <div class="form-group row">

								<div class="col-md-1" align="left">								
                                    <label class="form-label">Referensi</label>
                                </div>
                               	<div class="col-md-2 input-group" >
                                  <input type="text" class="form-control REF" id="REF" name="REF" placeholder="" value="{{$header->REF}}" style="text-align: left" readonly >
        						</div>

								<div class="col-md-1" align="left">								
									<label class="form-label">Margin</label>
								</div>
								<div class="col-md-2 input-group" >
									<input type="text" class="form-control MARGIN" id="MARGIN" name="MARGIN" placeholder="" value="{{$header->MARGIN}}" style="text-align: left" readonly >
								</div>

								<div class="col-md-1" align="left">								
									<label class="form-label">Sts. Nota</label>
								</div>
								<div class="col-md-2 input-group" >
									<input type="text" class="form-control ST_NOTA" id="ST_NOTA" name="ST_NOTA" placeholder="" value="{{$header->ST_NOTA}}" style="text-align: left" readonly >
								</div>

								<div class="col-md-1" align="left">								
									<label class="form-label">Sistem</label>
								</div>
								<div class="col-md-2 input-group" >
									<input type="text" class="form-control ST_CNT" id="ST_CNT" name="ST_CNT" placeholder="" value="{{$header->ST_CNT}}" style="text-align: left" readonly >
								</div>
                                
                            </div>
							
                            <div class="form-group row">
								<div class="col-md-1" align="left">
									<label for="TGL" class="form-label">Tgl</label>
								</div>
								<div class="col-md-2">
									<input class="form-control date" id="TGL" name="TGL" data-date-format="dd-mm-yyyy" type="text" autocomplete="off" value="{{date('d-m-Y',strtotime($header->TGL))}}">
								</div>

								<div class="col-md-1" align="left">								
									<label class="form-label">Dis. Promosi</label>
								</div>
								<div class="col-md-2 input-group" >
									<input type="text" class="form-control POT_PROM" id="POT_PROM" name="POT_PROM" placeholder="" value="{{$header->POT_PROM}}" style="text-align: left" readonly >
								</div>

								<div class="col-md-1" align="left">								
									<label class="form-label">Kupon krd</label>
								</div>
								<div class="col-md-2 input-group" >
									<input type="text" class="form-control KK_STS" id="KK_STS" name="KK_STS" placeholder="" value="{{$header->KK_STS}}" style="text-align: left" readonly >
								</div>


								<div class="col-md-1" align="left">								
									<label class="form-label">Basic</label>
								</div>
								<div class="col-md-2 input-group" >
									<input type="text" class="form-control BASIC" id="BASIC" name="BASIC" placeholder="" value="{{$header->BASIC}}" style="text-align: left" readonly >
								</div>
                            </div>

							<div class="form-group row">
								<div class="col-md-1" align="left">
									<label for="JTEMPO" class="form-label">Jtempo</label>
								</div>
								<div class="col-md-2">
									<input class="form-control date" id="JTEMPO" name="JTEMPO" data-date-format="dd-mm-yyyy" type="text" autocomplete="off" value="{{date('d-m-Y',strtotime($header->JTEMPO))}}">
								</div>

								<div class="col-md-1" align="left">								
									<label class="form-label">Sts. Pajak</label>
								</div>
								<div class="col-md-2 input-group" >
									<input type="text" class="form-control ST_PJK" id="ST_PJK" name="ST_PJK" placeholder="" value="{{$header->ST_PJK}}" style="text-align: left" readonly >
								</div>

								<div class="col-md-1" align="left">								
									<label class="form-label">Formalitas</label>
								</div>
								<div class="col-md-2 input-group" >
									<input type="text" class="form-control FORMAL" id="FORMAL" name="FORMAL" placeholder="" value="{{$header->FORMAL}}" style="text-align: left" readonly >
								</div>

								<div class="col-md-1" align="left">								
									<label class="form-label">Nota Khs</label>
								</div>
								<div class="col-md-2 input-group" >
									<input type="text" class="form-control NOTA_KHS" id="NOTA_KHS" name="NOTA_KHS" placeholder="" value="{{$header->NOTA_KHS}}" style="text-align: left" readonly >
								</div>
                            </div>

							<div class="form-group row">

								<div class="col-md-1" align="left">
                                    <label  class="form-label">Notes</label>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control NOTES" id="NOTES" name="NOTES" placeholder=""  value="{{$header->NOTES}}"  readonly >
                                </div>

								<!-- <div class="col-md-2" >
									<input type="checkbox" class="form-check-input" id="BAYAR" name="BAYAR" readonly  value="{{$header->BAYAR}}" {{ ($header->BAYAR == 1) ? 'checked' : '' }}>
									<input type="text" hidden class="form-control ZBAYAR" id="ZBAYAR" name="ZBAYAR" value="{{$header->BAYAR}}" placeholder="" >
								</div> -->
								
								<div class="col-md-1">
									<input type="checkbox" class="form-check-input" id="BAYAR" name="BAYAR" value="1" {{ ($header->BAYAR == 1) ? 'checked' : '' }}>
									<label for="BAYAR"></label>
								</div>
                            </div>

						<!-- loader tampil di modal  -->
						<div class="loader" style="z-index: 1055;" id='LOADX' ></div>
						<!-- tutupan load -->

						<!-- style text box model baru -->

							<style>
								/* Ensure specificity with class targeting */
								.form-group.special-input-label {
									position: relative;

									/* geser kanan kirinya di atur disini */
									margin-left: 50px ;
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
									/* buat label di inputan */
									color: #888 !important;
									font-size: 16px !important;
									transition: 0.3s ease all;
									pointer-events: none;
								}
						
								/* Move label above input when focused or has content */
								.form-group.special-input-label input:focus + label,
								.form-group.special-input-label input:not(:placeholder-shown) + label {
									top: -10px !important;
									font-size: 14px !important;
									/* buat label diatas */
									color: #007BFF !important;
								}
							</style>

							<!-- tutupannya -->
							
							
                            <hr style="margin-top: 30px; margin-buttom: 30px">
							
							<div style="overflow-y:scroll;" class="col-md-12 scrollable" align="right">

								<table id="datatable" class="table table-striped table-border">

									<thead>
										<tr>
											<th width="100px" style="text-align:center">No.</th>
											<th width="100px" style="text-align:center">Kode</th>
											<th width="200px" style="text-align:center">Barcode</th>
											<th width="120px" style="text-align:center">Uraian</th>
											<th width="100px" style="text-align:center">Jenis</th>
											<th width="100px" style="text-align:center">Qty</th>
											<th width="150px" style="text-align:center">Harga Beli</th>
											<th width="150px" style="text-align:center">Margin</th>
											<th width="150px" style="text-align:center">Diskon 1</th>
											<th width="150px" style="text-align:center">Diskon 2</th>
											<th width="150px" style="text-align:center">Diskon 3</th>
											<th width="150px" style="text-align:center">Diskon 4</th>
											<th width="150px" style="text-align:center">Total</th>
											<th width="150px" style="text-align:center">Harga Jual</th>
											<th width="150px" style="text-align:center">+ / -</th>

											<th></th>
																
										</tr>
									<tbody id="detailPod">
			
									<tbody>
									<?php $no=0 ?>
									@foreach ($detail as $detail)		
										<tr>
											<td>
												<input type="hidden" name="NO_ID[]{{$no}}" id="NO_ID" type="text" value="{{$detail->NO_ID}}" 
												class="form-control NO_ID" onkeypress="return tabE(this,event)" readonly>
												
												<input name="REC[]" id="REC{{$no}}" type="text" value="{{$detail->REC}}" class="form-control REC" onkeypress="return tabE(this,event)" readonly style="text-align:center">
											</td>								

											<td>
												<input name="KD_BRG[]" id="KD_BRG{{$no}}" type="text" class="form-control KD_BRG " 
												value="{{$detail->KD_BRG}}" onblur="browseBarang({{$no}})">
											</td>

											<td>
												<input name="BARCODE[]" id="BARCODE{{$no}}" type="text" class="form-control BARCODE " value="{{$detail->BARCODE}}">
											</td>

											<td>
												<input name="NA_BRG[]" id="NA_BRG{{$no}}" type="text" class="form-control NA_BRG " value="{{$detail->NA_BRG}}">
											</td>

											<td>
												<input name="JNS[]" id="JNS{{$no}}" type="text" class="form-control JNS" value="{{$detail->JNS}}">
											</td>										
											<td>
												<input name="QTY[]" onclick="select()" onblur="hitung()" value="{{$detail->QTY}}" id="QTY{{$no}}" type="text" style="text-align: right"  class="form-control QTY" >
											</td>
																																	
											<td >
												<input name="HARGA[]" onclick="select()" onblur="hitung()" value="{{$detail->HARGA}}" id="HARGA{{$no}}" type="text" style="text-align: right"  class="form-control HARGA">
											</td>
		
											<td >
												<input name="MARGIN[]" onblur="hitung()"  value="{{$detail->MARGIN}}" id="MARGIN{{$no}}" type="text" style="text-align: right"  class="form-control MARGIN" readonly>
											</td>
											<td >
												<input name="DIKSON1[]" onblur="hitung()"  value="{{$detail->DISKON1}}" id="DIKSON1{{$no}}" type="text" style="text-align: right"  class="form-control DIKSON1" readonly>
											</td>
											<td >
												<input name="DISKON2[]" onblur="hitung()"  value="{{$detail->DISKON2}}" id="DISKON2{{$no}}" type="text" style="text-align: right"  class="form-control DISKON2" readonly>
											</td>
											<td>
												<input name="DISKON3[]" onblur="hitung()"  value="{{$detail->DISKON3}}" id="DISKON3{{$no}}" type="text" style="text-align: right"  class="form-control DISKON3" readonly>
											</td>
											<td>
												<input name="DISKON4[]" onblur="hitung()"  value="{{$detail->DISKON4}}" id="DISKON4{{$no}}" type="text" style="text-align: right"  class="form-control DISKON4" readonly>
											</td>
											<td>
												<input name="TOTAL[]" onblur="hitung()"  value="{{$detail->TOTAL}}" id="TOTAL{{$no}}" type="text" style="text-align: right"  class="form-control TOTAL" readonly>
											</td>
											<td>
												<input name="HARGA_JL[]" onblur="hitung()"  value="{{$detail->HARGA_JL}}" id="HARGA_JL{{$no}}" type="text" style="text-align: right"  class="form-control HARGA_JL" readonly>
											</td>
											<td>
												<input name="BLT[]" onblur="hitung()"  value="{{$detail->BLT}}" id="BLT{{$no}}" type="text" style="text-align: right"  class="form-control BLT" readonly>
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
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>									
										<td></td>
										<td></td>
										<!-- <td><input class="form-control TTOTAL  text-primary" style="text-align: right"  id="TTOTAL" name="TTOTAL" value="{{$header->TOTAL}}" readonly></td> -->
										<td></td>
									</tfoot>
								</table>
							</div>

                            <div class="col-md-2 row">
                               <a type="button" id='PLUSX' onclick="tambah()" class="fas fa-plus fa-sm md-3" style="font-size: 20px" ></a>
					
							</div>	
							
                        </div> 

                        <hr style="margin-top: 30px; margin-buttom: 30px">
                        
                        <div class="tab-content mt-6">
						
							<div class="form-group row">
                                
                                <div class="col-md-6" align="right">
                                     <label for="TJUMLAH" class="form-label">Bruto</label>
                                </div>

                                <div class="col-md-2">
                                    <input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TJUMLAH" id="TJUMLAH" name="TJUMLAH" placeholder="" value="{{$header->JUMLAH}}" style="text-align: right" readonly>
                                </div>
                                
                                <div class="col-md-1" align="right">
                                    <label for="TPROM" class="form-label">Promosi</label>
                                </div>
                                <div class="col-md-2">
                                    <input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TPROM" id="TPROM" name="TPROM" placeholder="" value="{{$header->PROM}}" style="text-align: right" readonly>
                                </div>
							</div>

							<div class="form-group row">

								<div class="col-md-6" align="right">
									<label for="TDPP" class="form-label">Netto (DPP)</label>
								</div>
								<div class="col-md-2">
									<input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TDPP" id="TDPP" name="TDPP" placeholder="" value="{{$header->DPP}}" style="text-align: right" readonly>
								</div>
							</div>

							<div class="form-group row">

								<div class="col-md-6" align="right">
                                    <label for="TPPN" class="form-label">Ppn</label>
                                </div>
                                <div class="col-md-2">
                                    <input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TPPN" id="TPPN" name="TPPN" placeholder="" value="{{$header->PPN}}" style="text-align: right" readonly>
                                </div>
							</div>
							
                            <div class="form-group row">
                                <div class="col-md-6" align="right">
                                    <label for="TNETT" class="form-label">Nett</label>
                                </div>
                                <div class="col-md-2">
                                    <input type="text"  onclick="select()" onkeyup="hitung()" class="form-control TNETT" id="TNETT" name="TNETT" placeholder="" value="{{$header->NETT}}" style="text-align: right" readonly>
                                </div>
							</div>
							
						</div>
						
						   
						<div class="mt-3 col-md-12 form-group row">
							<div class="col-md-4">
								<button hidden type="button" id='TOPX'  onclick="location.href='{{url('/kirim/edit/?idx=' .$idx. '&tipx=top&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-outline-primary">Top</button>
								<button hidden type="button" id='PREVX' onclick="location.href='{{url('/kirim/edit/?idx='.$header->NO_ID.'&tipx=prev&flagz='.$flagz.'&golz='.$golz.'&buktix='.$header->NO_BUKTI )}}'" class="btn btn-outline-primary">Prev</button>
								<button hidden type="button" id='NEXTX' onclick="location.href='{{url('/kirim/edit/?idx='.$header->NO_ID.'&tipx=next&flagz='.$flagz.'&golz='.$golz.'&buktix='.$header->NO_BUKTI )}}'" class="btn btn-outline-primary">Next</button>
								<button hidden type="button" id='BOTTOMX' onclick="location.href='{{url('/kirim/edit/?idx=' .$idx. '&tipx=bottom&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-outline-primary">Bottom</button>
							</div>
							<div class="col-md-5">
								<button hidden type="button" id='NEWX' onclick="location.href='{{url('/kirim/edit/?idx=0&tipx=new&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-warning">New</button>
								<button hidden type="button" id='EDITX' onclick='hidup()' class="btn btn-secondary">Edit</button>                    
								<button hidden type="button" id='UNDOX' onclick="location.href='{{url('/kirim/edit/?idx=' .$idx. '&tipx=undo&flagz='.$flagz.'&golz='.$golz.'' )}}'" class="btn btn-info">Undo</button>  
								<button type="button" id='SAVEX' onclick='simpan()'   class="btn btn-success" class="fa fa-save"></i>Save</button>

							</div>
							<div class="col-md-3">
								<button hidden type="button" id='HAPUSX'  onclick="hapusTrans()" class="btn btn-outline-danger">Hapus</button>
								<button type="button" id='CLOSEX' onclick="closeTrans()" class="btn btn-outline-secondary">Close</button></div>   
							</div>
						</div>
						
						
                    </form>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>


 	<div class="modal fade" id="browsePoModal" tabindex="-1" role="dialog" aria-labelledby="browsePoModalLabel" aria-hidden="true">
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
						<th>PO#</th>
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

	<div class="modal fade" id="browseBeliModal" tabindex="-1" role="dialog" aria-labelledby="browseBeliModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="browseBeliModalLabel">Cari Pemkiriman</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<table class="table table-stripped table-bordered" id="table-bkirim">
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


	<div class="modal fade" id="browseBarangModal" tabindex="-1" role="dialog" aria-labelledby="browseBarangModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-xl" role="document">
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
	  <div class="modal-dialog modal-xl" role="document">
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
<script src="{{ asset('js/autoNumerics/autoNumeric.min.js') }}"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script> -->
<script src="{{asset('foxie_js_css/bootstrap.bundle.min.js')}}"></script>

<!-- tambahan untuk sweetalert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- tutupannya -->

<script>
	var idrow = 1;
	var baris = 1;

	function numberWithCommas(x) {
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	
    $(document).ready(function () {

		setTimeout(function(){

		$("#LOADX").hide();

		},500);

    idrow=<?=$no?>;
    baris=<?=$no?>;

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
		}    
		
		$("#TTOTAL_QTY").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TTOTAL").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TDISK").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TPPN").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#TDPP").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});
		$("#NETT").autoNumeric('init', {aSign: '<?php echo ''; ?>',vMin: '-999999999.99'});



		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#QTY_PO" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#SISA" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#HARGA" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#PPNX" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#DPP" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#DISK" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#XQTY" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			
			$("#TOTAL" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#KALI" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
			$("#QTY" + i.toString()).autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
		}	


		
        $('body').on('click', '.btn-delete', function() {
			var val = $(this).parents("tr").remove();
			baris--;
			hitung();
			nomor();
			
		});

		$('.date').datepicker({  
            dateFormat: 'dd-mm-yy'
		});
		
		
 	
		
//		CHOOSE Po
 		var dTableBPo;
		loadDataBPo = function(){
		
			$.ajax(
			{
				type: 'GET', 		
				url: '{{url('po/browse')}}',
				data: {
					'GOL': "{{$golz}}",
					// 'KODES' : $("#KODES").val(),
				},

				beforeSend: function(){
					$("#LOADX").show();
				},

				success: function( response )
				{
					$("#LOADX").hide();

					resp = response;
					if(dTableBPo){
						dTableBPo.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBPo.row.add([
							'<a href="javascript:void(0);" onclick="choosePo(\''+resp[i].NO_BUKTI+'\' ,\''+resp[i].KODES+'\',  \''+resp[i].NAMAS+'\', \''+resp[i].ALAMAT+'\',  \''+resp[i].KOTA+'\',  \''+resp[i].PKP+'\',  \''+resp[i].GUDANG+'\',  \''+resp[i].JTEMPO+'\',  \''+resp[i].NOTES+'\')">'+resp[i].NO_BUKTI+'</a>',
							resp[i].KODES,
							resp[i].NAMAS,
							resp[i].ALAMAT,
							resp[i].KOTA,
						]);
					}
					dTableBPo.draw();
				}
			});
		}
		
		dTableBPo = $("#table-bpo").DataTable({
			
		});
		
		browsePo = function(){
			loadDataBPo();
			$("#browsePoModal").modal("show");

		}
		
		choosePo = function( NO_BUKTI,KODES,NAMAS, ALAMAT, KOTA, PKP, GUDANG, JTEMPO, NOTES){

			$("#NO_PO").val(NO_BUKTI);
			$("#KODES").val(KODES);
			$("#NAMAS").val(NAMAS);
			$("#ALAMAT").val(ALAMAT);
			$("#KOTA").val(KOTA);			
			$("#PKP").val(PKP);			
			$("#GUDANG").val(GUDANG);			
			$("#JTEMPO").val(JTEMPO);			
			$("#NOTES").val(NOTES);			
			
			
				if ( $("#PKP").val() == '1' )
        		{

                     document.getElementById("PKP").checked = true;
                    	
        		}
        
                else
                {
                     document.getElementById("PKP").checked = false;
                    
                }
			
			
			
			$("#browsePoModal").modal("hide");
			
			getPod(NO_BUKTI);
		}
		
		$("#NO_PO").keypress(function(e){

			if(e.keyCode == 46){
				 e.preventDefault();
				 browsePo();
			}
		}); 

////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////

	function getPod(bukti)
	{
		
		var mulai = (idrow==baris) ? idrow-1 : idrow;

		$.ajax(
			{
				type: 'GET',    
				url: "{{url('po/browse_pod')}}",
				data: {
					nobukti: bukti,
					'GOL': "{{$golz}}",
				},
				success: function( resp )
				{
					var html = '';
					for(i=0; i<resp.length; i++){
						html+=`<tr>
                                    <td><input name='REC[]' id='REC${i}' value=${resp[i].REC+1} type='text' class='REC form-control' onkeypress='return tabE(this,event)' readonly></td>
                                    

									<td >
										<input name='KD_BRG[]' id='KD_BRG${i}' value="${resp[i].KD_BRG}" type='text' class='form-control KD_BRG' readonly>
						            </td>
						            <td >
						 			    <input name='NA_BRG[]' id='NA_BRG${i}' value="${resp[i].NA_BRG}" type='text' class='form-control  NA_BRG' readonly>
						            </td>
									
									<td><input name='SATUAN_PO[]' id='SATUAN_PO${i}' value="${resp[i].SATUAN_PO}" type='text' class='form-control  SATUAN_PO' readonly></td>
                                    <td>
										<input name='XQTY[]' onclick='select()' onblur='hitung()' id='XQTY${i}' value="${resp[i].XQTY}" type='text' style='text-align: right' class='form-control XQTY text-primary' >
									</td>
									<td>
										<input name='KALI[]' onclick='select()' onblur='hitung()' id='KALI${i}' value="${resp[i].KALI}" type='text' style='text-align: right' class='form-control KALI text-primary' >
									</td>
									<td>
										<input name='QTY_PO[]' onclick='select()' onblur='hitung()' id='QTY_PO${i}' value="${resp[i].SISA}" type='text' style='text-align: right' class='form-control QTY_PO text-primary' readonly>
										<input hidden name='SISA[]' onclick='select()' onblur='hitung()' id='SISA{i}' value="${resp[i].SISA}" type='text' style='text-align: right' class='form-control SISA text-primary' readonly>
									</td>
									<td>
										<input name='SATUAN[]' id='SATUAN${i}' value="${resp[i].SATUAN}" type='text' class='form-control  SATUAN' readonly>
									</td>
                                    <td>
										<input name='QTY[]' onclick='select()' onblur='hitung()' id='QTY${i}' value="${resp[i].QTY}" type='text' style='text-align: right' class='form-control QTY text-primary' readonly >
									</td>
									<td >
										<input name='HARGA[]' onclick='select()' onblur='hitung()' id='HARGA${i}' value="${resp[i].HARGA}" type='text' style='text-align: right' class='form-control HARGA text-primary' readonly >
									</td>
									<td >
										<input name='TOTAL[]' onclick='select()' onblur='hitung()' id='TOTAL${i}' value="${resp[i].TOTAL}" type='text' style='text-align: right' class='form-control TOTAL text-primary' readonly >
									</td>
									<td >
										<input name='PPNX[]' onclick='select()' onblur='hitung()' id='PPNX${i}' value="${resp[i].PPN}" type='text' style='text-align: right' class='form-control PPNX text-primary' readonly >
									</td>
									<td >
										<input name='DPP[]' onclick='select()' onblur='hitung()' id='DPP${i}' value="${resp[i].DPP}" type='text' style='text-align: right' class='form-control DPP text-primary' readonly >
									</td>
									<td>
										<input name='DISK[]' onclick='select()' onblur='hitung()' id='DISK${i}' value="${resp[i].DISK}" type='text' style='text-align: right' class='form-control DISK text-primary' readonly >
									</td>
									<td><input name='KET[]' id='KET${i}' value="" type='text' class='form-control  KET'></td>
                                    

									<td><button type='button' class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button></td>
                                </tr>`;
					}
					$('#detailPod').html(html);

					$(".XQTY").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".XQTY").autoNumeric('update');
					
					$(".QTY_PO").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".QTY_PO").autoNumeric('update');

					$(".QTY").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".QTY").autoNumeric('update');
					
					$(".HARGA").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".HARGA").autoNumeric('update');
					
					$(".KALI").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".KALI").autoNumeric('update');
					
					$(".TOTAL").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".TOTAL").autoNumeric('update');
					
					$(".PPNX").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".PPNX").autoNumeric('update');
					
					$(".DPP").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".DPP").autoNumeric('update');

					$(".DISK").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".DISK").autoNumeric('update');


					idrow=resp.length;
					baris=resp.length;

					nomor();
					hitung();
				}
			});
	}

//////////////////////////////////////////////////////////////////

//		CHOOSE Beli
		var dTableBBeli;
		loadDataBBeli = function(){
		
			$.ajax(
			{
				type: 'GET', 		
				url: '{{url('kirim/browse')}}',
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
					if(dTableBBeli){
						dTableBBeli.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBBeli.row.add([
							'<a href="javascript:void(0);" onclick="chooseBeli(\''+resp[i].NO_BUKTI+'\', \''+resp[i].NO_PO+'\' ,\''+resp[i].KODES+'\',  \''+resp[i].NAMAS+'\', \''+resp[i].ALAMAT+'\',  \''+resp[i].KOTA+'\',  \''+resp[i].PKP+'\',  \''+resp[i].GUDANG+'\')">'+resp[i].NO_BUKTI+'</a>',
							resp[i].KODES,
							resp[i].NAMAS,
							resp[i].ALAMAT,
							resp[i].KOTA,
						]);
					}
					dTableBBeli.draw();
				}
			});
		}
		
		dTableBBeli = $("#table-bkirim").DataTable({
			
		});
		
		browseBeli = function(){
			loadDataBBeli();
			$("#browseBeliModal").modal("show");
		}
		
		chooseBeli = function( NO_BUKTI, NO_PO , KODES,NAMAS, ALAMAT, KOTA, PKP, GUDANG){

			$("#NO_BELI").val(NO_BUKTI);
			$("#NO_PO").val(NO_PO);
			$("#KODES").val(KODES);
			$("#NAMAS").val(NAMAS);
			$("#ALAMAT").val(ALAMAT);
			$("#KOTA").val(KOTA);			
			$("#PKP").val(PKP);			
			$("#GUDANG").val(GUDANG);			
			$("#browseBeliModal").modal("hide");
			
			if ( $("#PKP").val() == '1' )
			{

				document.getElementById("PKP").checked = true;
					
			}
	
			else
			{
				document.getElementById("PKP").checked = false;
				
			}

			getBelid(NO_BUKTI);
		}
		
		$("#NO_BELI").keypress(function(e){

			if(e.keyCode == 46){
				 e.preventDefault();
				 browseBeli();
			}
		}); 

//////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////

	function getBelid(bukti)
	{
		
		var mulai = (idrow==baris) ? idrow-1 : idrow;

		$.ajax(
			{
				type: 'GET',    
				url: "{{url('kirim/browse_kirimd')}}",
				data: {
					nobukti: bukti,
				},
				success: function( resp )
				{
					var html = '';
					for(i=0; i<resp.length; i++){
						html+=`<tr>
                                    <td><input name='REC[]' id='REC${i}' value=${resp[i].REC+1} type='text' class='REC form-control' onkeypress='return tabE(this,event)' readonly></td>
                                    

									<td >
										<input name='KD_BRG[]' id='KD_BRG${i}' value="${resp[i].KD_BRG}" type='text' class='form-control KD_BRG' readonly>
						            </td>
						            <td >
						 			    <input name='NA_BRG[]' id='NA_BRG${i}' value="${resp[i].NA_BRG}" type='text' class='form-control  NA_BRG' readonly>
						            </td>
									
									<td><input name='SATUAN_PO[]' id='SATUAN_PO${i}' value="${resp[i].SATUAN_PO}" type='text' class='form-control  SATUAN_PO' readonly></td>
                                    <td>
										<input name='XQTY[]' onclick='select()' onblur='hitung()' id='XQTY${i}' value="${resp[i].XQTY}" type='text' style='text-align: right' class='form-control XQTY text-primary' >
									</td>
									<td>
										<input name='KALI[]' onclick='select()' onblur='hitung()' id='KALI${i}' value="${resp[i].KALI}" type='text' style='text-align: right' class='form-control KALI text-primary' >
									</td>
									<td>
										<input name='QTY_PO[]' onclick='select()' onblur='hitung()' id='QTY_PO${i}' value="${resp[i].SISA}" type='text' style='text-align: right' class='form-control QTY_PO text-primary' readonly>
										<input hidden name='SISA[]' onclick='select()' onblur='hitung()' id='SISA{i}' value="${resp[i].SISA}" type='text' style='text-align: right' class='form-control SISA text-primary' readonly>
									</td>
									<td>
										<input name='SATUAN[]' id='SATUAN${i}' value="${resp[i].SATUAN}" type='text' class='form-control  SATUAN' readonly>
									</td>
                                    <td>
										<input name='QTY[]' onclick='select()' onblur='hitung()' id='QTY${i}' value="${resp[i].QTY}" type='text' style='text-align: right' class='form-control QTY text-primary' readonly >
									</td>
									<td >
										<input name='HARGA[]' onclick='select()' onblur='hitung()' id='HARGA${i}' value="${resp[i].HARGA}" type='text' style='text-align: right' class='form-control HARGA text-primary' readonly >
									</td>
									<td >
										<input name='TOTAL[]' onclick='select()' onblur='hitung()' id='TOTAL${i}' value="${resp[i].TOTAL}" type='text' style='text-align: right' class='form-control TOTAL text-primary' readonly >
									</td>
									<td >
										<input name='PPNX[]' onclick='select()' onblur='hitung()' id='PPNX${i}' value="${resp[i].PPN}" type='text' style='text-align: right' class='form-control PPNX text-primary' readonly >
									</td>
									<td >
										<input name='DPP[]' onclick='select()' onblur='hitung()' id='DPP${i}' value="${resp[i].DPP}" type='text' style='text-align: right' class='form-control DPP text-primary' readonly >
									</td>
									<td>
										<input name='DISK[]' onclick='select()' onblur='hitung()' id='DISK${i}' value="${resp[i].DISK}" type='text' style='text-align: right' class='form-control DISK text-primary' readonly >
									</td>
									<td><input name='KET[]' id='KET${i}' value="" type='text' class='form-control  KET'></td>
                                    

									<td><button type='button' class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button></td>
                                </tr>`;
					}
					$('#detailPod').html(html);

					$(".XQTY").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".XQTY").autoNumeric('update');
					
					$(".QTY_PO").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".QTY_PO").autoNumeric('update');

					$(".QTY").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".QTY").autoNumeric('update');
					
					$(".HARGA").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".HARGA").autoNumeric('update');
					
					$(".KALI").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".KALI").autoNumeric('update');
					
					$(".TOTAL").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".TOTAL").autoNumeric('update');
					
					$(".PPNX").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".PPNX").autoNumeric('update');
					
					$(".DPP").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".DPP").autoNumeric('update');

					$(".DISK").autoNumeric('init', {aSign: '<?php echo ''; ?>', vMin: '-999999999.99'});
					$(".DISK").autoNumeric('update');


					idrow=resp.length;
					baris=resp.length;

					nomor();
					hitung();
				}
			});
	}

//////////////////////////////////////////////////////////////////

		var dTableBBarang;
		var rowidBarang;
		loadDataBBarang = function(){
			$.ajax(
			{
				type: 'GET',    
				url: "{{url('po/browse_detail2')}}",
				data: 
				{
                    KD_BRG : $("#KD_BRG"+rowidBarang).val(),
                    NO_PO : $("#NO_PO").val(), 					
				},				
				
				success: function( response )
				{
					resp = response;
					if(dTableBBarang){
						dTableBBarang.clear();
					}
					for(i=0; i<resp.length; i++){
						
						dTableBBarang.row.add([
							'<a href="javascript:void(0);" onclick="chooseBarang(\''+resp[i].KD_BRG+'\',\''+resp[i].NA_BRG+'\', \''+resp[i].SATUAN_PO+'\' , \''+resp[i].QTY_PO+'\' , \''+resp[i].KIRIM+'\' , \''+resp[i].SISA+'\' , \''+resp[i].HARGA+'\', \''+resp[i].SATUAN+'\', \''+resp[i].QTY+'\', \''+resp[i].KALI+'\'  )">'+resp[i].KD_BRG+'</a>',
							resp[i].NA_BRG,
							resp[i].SATUAN_PO,
							resp[i].QTY_PO,
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
		
		chooseBarang = function(KD_BRG, NA_BRG, SATUAN_PO, QTY_PO, KIRIM, SISA, HARGA, SATUAN, QTY, KALI ){
			$("#KD_BRG"+rowidBarang).val(KD_BRG);
			$("#NA_BRG"+rowidBarang).val(NA_BRG);
			$("#SATUAN_PO"+rowidBarang).val(SATUAN_PO);
			$("#QTY_PO"+rowidBarang).val(SISA);
			$("#HARGA"+rowidBarang).val(HARGA);
			$("#SATUAN"+rowidBarang).val(SATUAN);			
			$("#QTY"+rowidBarang).val(QTY);			
			$("#KALI"+rowidBarang).val(KALI);			
			$("#browseBarangModal").modal("hide");
			hitung();
		}
		
		
		$("#KD_BRG0").keypress(function(e){
			if(e.keyCode == 46){
				e.preventDefault();
				browseBarang(0);
			}
		}); 

	});


///////////////////////////////////////		
    

    function cekDetail(){
		var cekBarang = '';
		$(".KD_BRG").each(function() {
			
			let z = $(this).closest('tr');
			var KD_BRGX = z.find('.KD_BRG').val();
			var QTY_POX = parseFloat(z.find('.QTY_PO').val().replace(/,/g, ''));
			var SISAX = parseFloat(z.find('.SISA').val().replace(/,/g, ''));

			// alert(QTYX);
			// alert(SEDIAX);
			
			if( KD_BRGX =="" )
			{
					cekBarang = '1';
					
			}	

			if( QTY_POX > SISAX )
			{
					cekBarang = '1';
					
			}	
		});
		
		return cekBarang;
	}
	

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
    				text: '#Periksa Barang dan QTY PO, QTY PO tidak boleh melebihi SISA PO'
    			});
    			return;
    		}
			
			if ( $('#KODES').val()=='' ) 
            {				
			    check = '1';
				Swal.fire({
					icon: 'warning',
					title: 'Warning',
					text: 'Suplier# Harus Diisi.'
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
			
			if (baris==0)
			{
				check = '1';
				Swal.fire({
					icon: 'warning',
					title: 'Warning',
					text: 'Data detail kosong (Tambahkan 1 baris kosong jika ingin mengosongi detail)'
				});
				return;
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
		var TTOTAL = 0;
		var TDISK = 0;
		var TDPPX = 0;
		var TPPNX = 0;
		var NETTX = 0;

		
		$(".QTY_PO").each(function() {
			
			let z = $(this).closest('tr');
			var QTY_POX = parseFloat(z.find('.QTY_PO').val().replace(/,/g, ''));
			var XQTYX = parseFloat(z.find('.XQTY').val().replace(/,/g, ''));
			var XX = parseFloat(z.find('.KALI').val().replace(/,/g, ''));
			var HARGAX = parseFloat(z.find('.HARGA').val().replace(/,/g, ''));
			var PPN = parseFloat(z.find('.PPNX').val().replace(/,/g, ''));
			var DISKX = parseFloat(z.find('.DISK').val().replace(/,/g, ''));
	
			var PKPX  = $('#PKP').val();

			var FLAGZ = $('#flagz').val();
	
			if (FLAGZ == 'RB'){
				
				var XQTYX  = ( XQTYX * -1 ) ;
				var QTY_POX  = ( QTY_POX * -1 ) ;
				var DISKX  = ( DISKX * -1 ) ;
				
				z.find('.QTY_PO').autoNumeric('update');
				z.find('.DISKX').autoNumeric('update');
				z.find('.XQTY').autoNumeric('update');

			} 

			var QTYX  = ( XQTYX * XX );
			z.find('.QTY').val(QTYX);

		    z.find('.KALI').autoNumeric('update');	
		    z.find('.QTY').autoNumeric('update');	

            
            var TOTALX  =  ( XQTYX * HARGAX ) - DISKX;
            
			z.find('.TOTAL').val(TOTALX);


			var DPPX = 0 ;
			var PPNX = 0;
			
            DPPX = TOTALX;
	     	z.find('.DPP').val(DPPX);

			if (PKPX == '0' ) {
			    PPNX = 0;
			    
			} 

	     		
			if (PKPX == '1' ) {
			    DPPX = TOTALX * 100/111;
			    PPNX = TOTALX - DPPX;
	     	    z.find('.DPP').val(DPPX);
	     	
			} 


            
			z.find('.PPNX').val(PPNX);	

		    z.find('.HARGA').autoNumeric('update');			
		    z.find('.QTY').autoNumeric('update');	
		    z.find('.TOTAL').autoNumeric('update');				
		    z.find('.DPP').autoNumeric('update');			
		    z.find('.DISK').autoNumeric('update');			
		    z.find('.PPNX').autoNumeric('update');		

            TTOTAL_QTY +=QTYX;		
            TTOTAL +=TOTALX;				
            TPPNX +=PPNX;
            TDPPX +=DPPX;
            
            TDISK +=DISKX;				
		
		});

		
		NETTX = TTOTAL ;

		
		if(isNaN(TTOTAL_QTY)) TTOTAL_QTY = 0;

		$('#TTOTAL_QTY').val(numberWithCommas(TTOTAL_QTY));		
		$("#TTOTAL_QTY").autoNumeric('update');
		
		if(isNaN(TTOTAL)) TTOTAL = 0;

		$('#TTOTAL').val(numberWithCommas(TTOTAL));		
		$("#TTOTAL").autoNumeric('update');

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
			$("#KODES").attr("readonly", true);
			$("#NAMAS").attr("readonly", true);			
			$("#ALAMAT").attr("readonly", true);
			$("#KOTA").attr("readonly", true);
			$("#JTEMPO").attr("readonly", false);
			
			$("#NOTES").attr("readonly", false);
	        $("#PKP").attr("disabled", true);
	        $("#CNT").attr("disabled", false);
	        $("#NCNT").attr("readonly", true);
	        $("#NO_PO").attr("readonly", true);

			$("#TDPP").attr("readonly", true);			
			$("#TPPN").attr("readonly", true);
			$("#TNETT").attr("readonly", true);	
			$("#TJUMLAH").attr("readonly", true);	
			$("#TPROM").attr("readonly", true);	


			$("#REF").attr("readonly", false);	
			$("#MARGIN").attr("readonly", false);	
			$("#ST_NOTA").attr("readonly", false);	
			$("#ST_CNT").attr("readonly", false);	
			$("#POT_PROM").attr("readonly", false);	
			$("#KK_STS").attr("readonly", false);	
			$("#BASIC").attr("readonly", false);	
			$("#ST_PJK").attr("readonly", false);	
			$("#FORMAL").attr("readonly", false);	
			$("#NOTA_KHS").attr("readonly", false);	
			$("#BAYAR").attr("disabled", false);	
			$("#POSTED").attr("disabled", true);	
			$("#CBG").attr("disabled", false);	

		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#REC" + i.toString()).attr("readonly", true);
			$("#KD_BRG" + i.toString()).attr("readonly", false);
			$("#BARCODE" + i.toString()).attr("readonly", true);
			$("#NA_BRG" + i.toString()).attr("readonly", true);
			$("#JNS" + i.toString()).attr("readonly", true);
			$("#QTY" + i.toString()).attr("readonly", false);
			$("#HARGA" + i.toString()).attr("readonly", false);
			$("#MARGIN" + i.toString()).attr("readonly", false);
			$("#DISKON1" + i.toString()).attr("readonly", false);
			$("#DISKON2" + i.toString()).attr("readonly", false);
			$("#DISKON3" + i.toString()).attr("readonly", false);
			$("#DISKON4" + i.toString()).attr("readonly", false);
			$("#TOTAL" + i.toString()).attr("readonly", true);
			$("#HARGA_JL" + i.toString()).attr("readonly", false);
			$("#BLT" + i.toString()).attr("readonly", true);
			$("#DELETEX" + i.toString()).attr("hidden", false);

			$tipx = $('#tipx').val();
		
			
			if ( $tipx != 'new' )
			{
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
			$("#KODES").attr("readonly", true);
			$("#NAMAS").attr("readonly", true);			
			$("#ALAMAT").attr("readonly", true);
			$("#KOTA").attr("readonly", true);
			$("#JTEMPO").attr("readonly", true);
			
			$("#NOTES").attr("readonly", true);
	        $("#PKP").attr("disabled", true);
	        $("#CNT").attr("disabled", true);
	        $("#NCNT").attr("readonly", true);
	        $("#NO_PO").attr("readonly", true);

			$("#TDPP").attr("readonly", true);			
			$("#TPPN").attr("readonly", true);
			$("#TNETT").attr("readonly", true);	
			$("#TJUMLAH").attr("readonly", true);	
			$("#TPROM").attr("readonly", true);	


			$("#REF").attr("readonly", true);	
			$("#MARGIN").attr("readonly", true);	
			$("#ST_NOTA").attr("readonly", true);	
			$("#ST_CNT").attr("readonly", true);	
			$("#POT_PROM").attr("readonly", true);	
			$("#KK_STS").attr("readonly", true);	
			$("#BASIC").attr("readonly", true);	
			$("#ST_PJK").attr("readonly", true);	
			$("#FORMAL").attr("readonly", true);	
			$("#NOTA_KHS").attr("readonly", true);	
			$("#BAYAR").attr("disabled", true);	
			$("#POSTED").attr("disabled", true);

		
		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#REC" + i.toString()).attr("readonly", true);
			$("#KD_BRG" + i.toString()).attr("readonly", true);
			$("#BARCODE" + i.toString()).attr("readonly", true);
			$("#NA_BRG" + i.toString()).attr("readonly", true);
			$("#JNS" + i.toString()).attr("readonly", true);
			$("#QTY" + i.toString()).attr("readonly", true);
			$("#HARGA" + i.toString()).attr("readonly", true);
			$("#MARGIN" + i.toString()).attr("readonly", true);
			$("#DISKON1" + i.toString()).attr("readonly", true);
			$("#DISKON2" + i.toString()).attr("readonly", true);
			$("#DISKON3" + i.toString()).attr("readonly", true);
			$("#DISKON4" + i.toString()).attr("readonly", true);
			$("#TOTAL" + i.toString()).attr("readonly", true);
			$("#HARGA_JL" + i.toString()).attr("readonly", true);
			$("#BLT" + i.toString()).attr("readonly", true);

			$("#DELETEX" + i.toString()).attr("hidden", true);
		}


		
	}


	function kosong() {
				
		 $('#NO_BUKTI').val("+");		
		 $('#KODES').val("");	
		 $('#NAMAS').val("");	
		 $('#ALAMAT').val("");	
		 $('#KOTA').val("");	
		 $('#NOTES').val("");	
		 $('#NO_PO').val("");
		 $('#TJUMLAH').val("0.00");
		 $('#TPROM').val("0.00");
		 $('#TDPP').val("0.00");
		 $('#TNETT').val("0.00");		 
		 $('#TPPN').val("0.00");	 
		 
		 
		var html = '';
		$('#detailx').html(html);	
		
	}
	
	function hapusTrans() {
		let text = "Hapus Transaksi "+$('#NO_BUKTI').val()+"?";
		if (confirm(text) == true) 
		{
			window.location ="{{url('/kirim/delete/'.$header->NO_ID .'/?flagz='.$flagz.'&golz=' .$golz.'' )}}";
			//return true;
		} 
		return false;
	}

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
	            	loc = "{{ url('/kirim/delete/'.$header->NO_ID) }}" + '?flagz=' + encodeURIComponent(flagz) + 
						  '&golz=' + encodeURIComponent(golz) ;

		            // alert(loc);
	            	window.location = loc;
		
				});
			}
		});
	}
	
	function closeTrans() {
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
	        	loc = "{{ url('/kirim/') }}" + '?flagz=' + encodeURIComponent(flagz) + '&golz=' + encodeURIComponent(golz) ;
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
		var loc = "{{ url('/kirim/edit/') }}" + '?idx={{ $header->NO_ID}}&tipx=search&flagz=' + encodeURIComponent(flagz) + '&golz=' + encodeURIComponent(golz) + '&buktix=' +encodeURIComponent(cari);
		window.location = loc;
		
	}


    function tambah() {

        var x = document.getElementById('datatable').insertRow(baris + 1);
 
		html=`<tr>

                <td>
 					<input name='NO_ID[]' id='NO_ID${idrow}' type='hidden' class='form-control NO_ID' value='new' readonly> 
					<input name='REC[]' id='REC${idrow}' type='text' class='REC form-control' onkeypress='return tabE(this,event)' readonly>
	            </td>
				
				<td>
				    <input name='KD_BRG[]' data-rowid=${idrow} onblur='browseBarang(${idrow})' id='KD_BRG${idrow}' type='text' class='form-control  KD_BRG' >
                </td>
                <td>
				    <input name='BARCODE[]'   id='BARCODE${idrow}' type='text' class='form-control  BARCODE' readonly>
                </td>
                <td>
				    <input name='NA_BRG[]'   id='NA_BRG${idrow}' type='text' class='form-control  NA_BRG' readonly>
                </td>
                <td>
				    <input name='JNS[]'   id='JNS${idrow}' type='text' class='form-control  JNS' readonly>
                </td>
				
				<td>
		            <input name='QTY[]' onclick ='select()' onblur='hitung()' value='0' id='QTY${idrow}' type='text' style='text-align: right' class='form-control QTY text-primary' required >
                </td>

				<td>
		            <input name='HARGA[]' onclick ='select()' onblur='hitung()' value='0' id='HARGA${idrow}' type='text' style='text-align: right' class='form-control HARGA text-primary' required >
                </td>

				<td>
					<input name='MARGIN[]' onclick ='select()' onblur='hitung()' value='0' id='MARGIN${idrow}' type='text' style='text-align: right' class='form-control MARGIN text-primary' required >
				</td>

				<td>
					<input name='DISKON1[]' onclick ='select()' onblur='hitung()' value='0' id='DISKON1${idrow}' type='text' style='text-align: right' class='form-control DISKON1 text-primary' required >
				</td>

				<td>
					<input name='DISKON2[]' onclick ='select()' onblur='hitung()' value='0' id='DISKON2${idrow}' type='text' style='text-align: right' class='form-control DISKON2 text-primary' required >
				</td>

				<td>
					<input name='DISKON3[]' onclick ='select()' onblur='hitung()' value='0' id='DISKON3${idrow}' type='text' style='text-align: right' class='form-control DISKON3 text-primary' required >
				</td>

				<td>
					<input name='DISKON4[]' onclick ='select()' onblur='hitung()' value='0' id='DISKON4${idrow}' type='text' style='text-align: right' class='form-control DISKON4 text-primary' required >
				</td>

				<td>
		            <input name='TOTAL[]'  onblur='hitung()' value='0' id='TOTAL${idrow}' type='text' style='text-align: right' class='form-control TOTAL text-primary' readonly >
                </td>

				<td>
		            <input name='HARGA_JL[]'  onblur='hitung()' value='0' id='HARGA_JL${idrow}' type='text' style='text-align: right' class='form-control HARGA_JL text-primary' required >
                </td>

				<td>
					<input name='BLT[]'  onblur='hitung()' value='0' id='BLT${idrow}' type='text' style='text-align: right' class='form-control BLT text-primary' required >
				</td>	
				
                <td>
					<button type='button' id='DELETEX${idrow}'  class='btn btn-sm btn-circle btn-outline-danger btn-delete' onclick=''> <i class='fa fa-fw fa-trash'></i> </button>
                </td>				
         </tr>`;
				
        x.innerHTML = html;
        var html='';
		
		
		
		jumlahdata = 100;
		for (i = 0; i <= jumlahdata; i++) {
			$("#QTY" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});

			$("#HARGA" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});

			
			$("#MARGIN" + i.toString()).autoNumeric('init', {
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
			
			$("#TOTAL" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});
			
			$("#HARGA_JL" + i.toString()).autoNumeric('init', {
				aSign: '<?php echo ''; ?>',
				vMin: '-999999999.99'
			});
			
			$("#BLT" + i.toString()).autoNumeric('init', {
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

<script src="autonumeric.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
<script src="https://unpkg.com/autonumeric"></script>
@endsection