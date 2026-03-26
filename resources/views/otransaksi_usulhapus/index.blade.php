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
        background-color: #FFFFFF;
    }

    .datatable {
        border-right: solid 2px #000;
        border-left: solid 2px #000;
    }


    .btn-secondary {
        background-color: #42047e !important;
    }

    th { font-size: 12px; }
    td { font-size: 12px; }

    /* menghilangkan padding */
    .content-header {
        padding: 0 !important;
    }

    .badge-warning {
        background-color: #5a01d5 !important; /* Warna default badge-warning (kuning) */
        color: white !important; /* Warna teks putih */
    }

    .badge-success {
        background-color: #068f3f !important; /* Warna default badge-warning (kuning) */
        color: white !important; /* Warna teks putih */
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

        <!-- tambahan notifikasinya untuk delete di index -->
        <script>
            Swal.fire({
                title: 'Sukses!',
                text: '{{session('status')}}',
                icon: 'success',
                confirmButtonText: 'OK'
            })
        </script>
        <!-- tutupannya -->

    @endif

    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <table class="table table-fixed table-striped table-border table-hover nowrap datatable" id="datatable">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" style="text-align: center">#</th>
				     		<th scope="col" style="text-align: center">-</th>							
                             <th scope="col" style="text-align: center">No. Bukti</th>
                             <th scope="col" style="text-align: left">Tanggal</th>
                            <th scope="col" style="text-align: left">Notes</th>
                            <th scope="col" style="text-align: center">Posted</th>
                        </tr>
                    </thead>

                    <tbody>
                    </tbody>
                </table>
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
            'scrollY': '400px',
            "order": [[ 0, "asc" ]],
            ajax:
            {
                url: "{{ route('get-usulhapus') }}"
            },

            columns: 
            [

                { data: 'DT_RowIndex', orderable: false, searchable: false },
			    { data: 'action', name: 'action'},
                {data: 'NO_BUKTI', name: 'NO_BUKTI',
                  render : function ( data, type, row, meta )
                  {
                    return ' <h5><span class="badge badge-pill badge-warning">' + data + '</span></h5>';
                  }
                },
                {data: 'TGL', name: 'TGL'},
                {data: 'NOTES', name: 'NOTES'},
                {
                    data: 'POSTED',
                    name: 'POSTED',
                    render: function(data, type, row, meta) {
                        if (row['POSTED'] == "0") {
                            return '';
                        } else {
                            return '<input type="checkbox" checked style="pointer-events: none; transform: scale(2);">';
                        }
                    }
                },
            ],
            columnDefs: 
            [
                {
                    "className": "dt-center", 
                    "targets": [0,1,2,5]
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

        $("div.test_btn").html(
            '<a class="btn btn-lg btn-success me-2" href="{{ url('usulhapus/edit?idx=0&tipx=new') }}">' +
                '<i class="fas fa-plus"></i> MANUAL' +
            '</a>' +

            '<form action="{{ route('usulhapus/proses') }}" method="POST" style="display:inline;" id="formProses">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                '<button type="submit" class="btn btn-lg btn-primary">' +
                    '<i class="fas fa-cogs"></i> PROSES OTOMATIS' +
                '</button>' +
            '</form>'
        );

        $(document).on('submit', '#formProses', function(e){
            e.preventDefault();

            let form = this;

            Swal.fire({
                title: 'Proses Otomatis?',
                text: 'Data akan digenerate otomatis!',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, proses!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
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

    function postingData(id) {

        Swal.fire({
            title: 'Posting Data?',
            text: "Data akan diposting!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, posting!',
            cancelButtonText: 'Batal'
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({
                    url: "{{ route('usulhapus.posting', ':id') }}".replace(':id', id),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {

                        if (res.success) {
                            Swal.fire('Sukses!', res.message, 'success');

                            // reload datatable
                            $('.datatable').DataTable().ajax.reload(null, false);

                        } else {
                            Swal.fire('Error!', res.message, 'error');
                        }
                    }
                });

            }
        });
    }
</script>
@endsection
