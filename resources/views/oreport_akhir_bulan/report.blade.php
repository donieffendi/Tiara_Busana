@extends('layouts.plain')

@section('content')
<div class="content-wrapper">
	
	<div class="content">
		<div class="container-fluid">
		<div class="row">
			<div class="col-12">
			<div class="card">
				<div class="card-body">
					<form method="POST" action="{{url('get-akhir-bulan')}}">
					@csrf

					<div class="form-group row">
                        <div class="col-md-1">						
							<label class="form-label">Periode</label>
						</div> 
						<div class="col-md-2">						
							<select name="perio" id="perio" class="form-control perio" style="width: 200px">
								<option value="">--Pilih Periode--</option>
								@foreach($per as $perD)
									<option value="{{$perD->PERIO}}"  {{ (session()->get('filter_periode') == $perD->PERIO) ? 'selected' : '' }}>{{$perD->PERIO}}</option>
								@endforeach
							</select>
						</div> 
					</div>
					
					<!-- <button class="btn btn-primary" type="submit" id="filter" class="filter" name="filter">Proses</button> -->
					<button class="btn btn-primary" type="button" id="btnProses">
                        Proses
                    </button>
                    <button class="btn btn-danger" type="button" id="resetfilter" class="resetfilter" onclick="window.location='{{url("akhir-bulan")}}'">Reset</button>
					<!-- <button class="btn btn-warning" type="submit" id="cetak" class="cetak" formtarget="_blank">Cetak</button> -->
					</form>
					<div style="margin-bottom: 15px;"></div>
					
				<!-- PASTE DIBAWAH INI -->
				<!-- DISINI BATAS AWAL KOOLREPORT-->
				
				<!-- DISINI BATAS AKHIR KOOLREPORT-->
				</div>
			</div>
			</div>
		</div>
		</div>
	</div>
</div>

@endsection

@section('javascripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

    $('#btnProses').on('click', function () {

        let perio = $('#perio').val();

        if (perio === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Periode belum dipilih!'
            });
            return;
        }

        Swal.fire({
            title: 'Sedang Memproses',
            text: 'Mohon tunggu...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ url('get-akhir-bulan') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                perio: perio
            },
            success: function (res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message
                }).then(() => {
                    location.reload(); // optional
                });
            },
            error: function (xhr) {
                let msg = 'Terjadi kesalahan sistem';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: msg
                });
            }
        });
    });

</script>
@endsection
