@extends('layouts.plain')

@section('content')
<div class="content-wrapper">
	<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0">Proses Stock Ideal</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item active">Proses Stock Ideal</li>
			</ol>
		</div>
		</div>
	</div>
	</div>
	
	<div class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">
							<form method="POST" action="{{url('proses-stokideal')}}">
								@csrf
								
								<button class="btn btn-danger" type="submit" id="proses">Proses</button>
							</form>
							<div style="margin-bottom: 15px;"></div>
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
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: "{{ session('success') }}",
            confirmButtonText: 'OK'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: "{{ session('error') }}",
            confirmButtonText: 'OK'
        });
    @endif

	$('#proses').click(function(e) {
		e.preventDefault();

		Swal.fire({
			title: 'Yakin?',
			text: 'Proses Stock Ideal akan dijalankan',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Ya, proses!',
			cancelButtonText: 'Batal'
		}).then((result) => {
			if (result.isConfirmed) {

				Swal.fire({
					title: 'Sedang diproses...',
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
					}
				});

				$(this).closest('form').submit();
			}
		});
	});
</script>
@endsection
