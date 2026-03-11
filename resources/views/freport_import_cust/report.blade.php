	@extends('layouts.plain')

	@section('content')
	<div class="content-wrapper">
		<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0">Import Customer</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item active">Import Customer</li>
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
						<form method="POST" action="{{url('ImportCustProses')}}" enctype="multipart/form-data">
						@csrf
						<div class="form-group nowrap">
								<input type="file" name="file" required>
						</div>
						
						  <!-- Filter Tanggal -->
						
						 
						<button class="btn btn-primary" type="submit" id="filter" class="filter" name="filter">Proses</button>
						</form>
						<div style="margin-bottom: 15px;"></div>

					
					</div>
				</div>
				</div>
			</div>
			</div>
		</div>
	</div>
	@endsection

	@section('javascripts')
	<script>
		$(document).ready(function() {
			$('.date').datepicker({  
				dateFormat: 'dd-mm-yy'
			}); 

	</script>
	@endsection
