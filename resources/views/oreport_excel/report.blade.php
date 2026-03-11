@extends('layouts.plain')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Import Excel</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active">Import Excel </li>
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
               
                <form method="POST" action="{{url('import_excel/import_excel')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group nowrap">
                        <label><strong>Periode :</strong></label>
                         <select name="perio" id="perio" class="form-control perio" style="width: 200px">
                            <option value="">--Pilih Periode--</option>
                            @foreach($per as $perD)
                                <option value="{{$perD->PERIO}}" {{ session()->get('filter_per')== $perD->PERIO ? 'selected' : '' }}>{{$perD->PERIO}}</option>
                            @endforeach
                        </select> 
                    
                    </div>
      
                   
                    <button type="button" class="btn btn-primary mr-5" data-toggle="modal" data-target="#importExcel">
        IMPORT EXCELL
    </button>

    <!-- Import Excel -->
    <div class="modal fade" id="importExcel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Import Excel</h5>
                    </div>
                    <div class="modal-body">

                        {{ csrf_field() }}

                        <label>Pilih file excel</label>
                        <div class="form-group">
                            <input type="file" name="file" required="required">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


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

</script>
@endsection

