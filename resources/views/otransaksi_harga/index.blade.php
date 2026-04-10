@extends('layouts.plain')
@section('styles')
<!-- <link rel="stylesheet" href="{{url('http://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css') }}"> -->
<link rel="stylesheet" href="{{asset('foxie_js_css/jquery.dataTables.min.css')}}" />

@endsection

<style>
    .card {
        padding: 5px 10px !important;
    }

    .table thead {
        background-color: #FFFFFF;
        color: #000000;
    }


    .datatable tbody td {
        padding: 5px !important;
    }

    .datatable {
        border-right: solid 2px #000;
        border-left: solid 2px #000;
    }
	

    .btn-secondary {
        background-color: #42047e !important;
    }
    
    th { font-size: 13px; }
    td { font-size: 13px; }

    /* menghilangkan padding */
    .content-header {
        padding: 0 !important;
    }
</style>


@section('content')
<!-- Sweetalert delete -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!--  -->
<div class="content-wrapper">


    <!-- Status -->
    @if (session('status'))
        <div class="alert alert-success">
            {{session('status')}}
        </div>

        <script>
            Swal.fire({
                    title: 'Berhasil !',
                    text: '{{session('status')}}',
                    icon: 'success',
                    confirmButtonText: 'OK'
                })
        </script>

    @endif

    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">

			  <input name="flagz"  class="form-control flagz" id="flagz" value="{{$flagz}}" hidden >
            
            {{-- <form method="POST" action="{{ url('harga/batal_post?flagz='.$flagz.'') }}" id="form-batal-post"> --}}
            @csrf


                <table class="table table-fixed table-striped table-border table-hover nowrap datatable" id="datatable">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" style="text-align: center">#</th>						
                            <th scope="col" style="text-align: center">-</th>							
                            <th scope="col" style="text-align: center">No Bukti</th>
                            <th scope="col" style="text-align: center">Tgl</th>
                            <th scope="col" style="text-align: center">Suplier#</th>
                            <th scope="col" style="text-align: center">User</th>
                            <th scope="col" style="text-align: center">Pst</th>
                        </tr>
                    </thead>
    
                    <tbody>
                    </tbody> 
                </table>

            </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
@endsection

@section('javascripts')

<!-- filter kolom di index -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- batas filter  -->

<script>
    $(document).ready(function() {
	  

			  
        var dataTable = $('.datatable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            // 'scrollX': true,
            // 'scrollY': '400px',
            "order": [[ 0, "asc" ]],
            ajax: {
                url: "{{ route('get-harga') }}",
                data: function (d) {
                    d.flagz = $('#flagz').val();
                }
            },

            columns: 
            [
                { data: 'DT_RowIndex', orderable: false, searchable: false },

                // {
                //     data: null,
                //     orderable: false,
                //     searchable: false,
                //     render: function(data, type, row, meta) {
                //         return `<input type="checkbox" name="batal_post[]" value="${row.NO_ID}" class="form-control batal-post">`;
                //     }
                // },

			    { data: 'action', name: 'action'},
                { data: 'NO_BUKTI', name: 'NO_BUKTI'},
                { data: 'TGL', name: 'TGL'},
                { data: 'NAMAS', name: 'NAMAS',
                  render : function ( data, type, row, meta )
                  {
                    return ' <h5><span class="badge badge-pill badge-warning">' + data + '</span></h5>';
                  }
                },
                { data: 'USRNM', name: 'USRNM'},
                { data: 'POSTED', name: 'POSTED',
                  render : function(data, type, row, meta) {
                    if(row['POSTED']=="0"){
                        return '';
                    }else{
                        return '<input type="checkbox" checked style="pointer-events: none;">';
                    }
                  }
                },
            ],
            columnDefs: 
            [
                {
                    "className": "dt-center", 
                    "targets": [0,1,2,3,4,5,6],
                },			
                {
                  targets: 3,
                  render: $.fn.dataTable.render.moment( 'DD-MM-YYYY' )
                }
            ],
            lengthMenu: 
            [
                [8, 10, 20, 50, 100, -1],
                [8, 10, 20, 50, 100, "All"]
            ],
            dom: "<'row'<'col-md-6'><'col-md-6'>>" +
                "<'row'<'col-md-2'l><'col-md-6 test_btn m-auto'><'col-md-4'f>>" +
                "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",

        });
        
        // batas filter
		
        // $("div.test_btn").html('<a class="btn btn-lg btn-md btn-success" href="{{url('harga/edit?flagz='.$flagz.'&idx=0&tipx=new')}}"> <i class="fas fa-plus fa-sm md-3" ></i></a');
            $("div.test_btn").html(`
                <div class="d-flex align-items-center" style="gap: 1rem;">
                    
                    <!-- tombol tambah -->
                    <a class="btn btn-success btn-md" id="btnTambah">
                        <i class="fas fa-plus fa-sm"></i>
                    </a>

                    <!-- radio HG -->
                    <div class="form-check">
                        <input class="form-check-input pilih-flagz" type="radio" name="flagz_option" value="HG" id="flagHG"
                            ${$('#flagz').val() == 'HG' ? 'checked' : ''}>
                        <label class="form-check-label" for="flagHG">Ganti Harga</label>
                    </div>

                    <!-- radio HT -->
                    <div class="form-check">
                        <input class="form-check-input pilih-flagz" type="radio" name="flagz_option" value="HT" id="flagHT"
                            ${$('#flagz').val() == 'HT' ? 'checked' : ''}>
                        <label class="form-check-label" for="flagHT">Turun Harga</label>
                    </div>

                </div>
            `);

            $('#btnTambah').on('click', function() {
                let flagz = $('#flagz').val();
                window.location.href = `{{ url('harga/edit') }}?flagz=${flagz}&idx=0&tipx=new`;
            });

            $('body').on('change', '.pilih-flagz', function() {
                let val = $(this).val();
                $('#flagz').val(val);

                dataTable.ajax.reload(); // pakai ini
            });
    });

    function deleteRow(link) {
        console.log('Masuk');
        Swal.fire({
            title: 'Are you sure?',
            text: "Are you sure?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = link;
            }
        });
    }
	
</script>
@endsection