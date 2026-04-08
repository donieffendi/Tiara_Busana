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
					title: 'Deleted!',
					text: 'Data has been deleted. {{session('status')}}',
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
                <div class="form-group row">
                    <div class="col-md-1" align="right">
                        <label class="form-label">Periode</label>
                    </div>
                    <div class="col-md-2">
                        <select name="per" id="per" class="form-control per" style="width: 200px">
                            <option value="">--Pilih Periode--</option>
                            @foreach($per as $perD)
                                <option value="{{$perD->PERIO}}"  {{ (session()->get('filter_periode') == $perD->PERIO) ? 'selected' : '' }}>{{$perD->PERIO}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button id="btn-proses" class="btn btn-primary">Proses</button>
                    </div>
                </div>

                <table class="table table-fixed table-striped table-border table-hover nowrap datatable" id="datatable">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" style="text-align: center">#</th>
                            <th scope="col" style="text-align: center">-</th>
                            <th scope="col" style="text-align: left">No. SP</th>
                            <th scope="col" style="text-align: left">No. BUKTI</th>
                            <th scope="col" style="text-align: center">Budget</th>
                            <th scope="col" style="text-align: left">Keterangan</th>
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
            deferLoading: 0,
            // 'scrollX': true,
            // 'scrollY': '400px',
            "order": [[ 0, "asc" ]],
            ajax:
            {
                url: "{{ route('get-budgetpk') }}",
                data:
                {
                    'per': $('#per').val(),
                },
            },

            columns:
            [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'action', name: 'action'},
                { data: 'NO_SP', name: 'NO_SP'},
                 { data: 'NO_BUKTI', name: 'NO_BUKTI'},
                { data: 'BUDGET', name: 'BUDGET', render: $.fn.dataTable.render.number( ',', '.', 2, '' )},
                { data: 'CAT', name: 'KODES'},
            ],
            columnDefs:
            [
                // {
                //     "className": "dt-center",
                //     "targets": [0,10]
                // },
                // {
                //   targets: 4,
                //   render: $.fn.dataTable.render.moment( 'DD-MM-YYYY' )
                // },

                {
                    "className": "dt-right",
                    "targets": 3
                },



            ],
            lengthMenu:
            [
                [8, 10, 20, 50, 100, -1],
                [8, 10, 20, 50, 100, "All"]
            ],
            dom: "<'row'<'col-md-6'><'col-md-6'>>" +
                "<'row'<'col-md-2'l><'col-md-6 test_btn m-auto'><'col-md-4'f>>" +
                "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
            stateSave: true,

        });

        // event tombol proses
        $('#btn-proses').on('click', function() {
            dataTable.ajax.reload();
        });

        $("div.test_btn").html(
        '  <a class="btn btn-lg btn-md btn-warning" href="{{url('budgetpk/budgetpk-otomatis')}}">Otomatis</a>'

        );
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


	function simpan() {
    var check = '0';
    var min = '0';


	document.getElementById("entri").submit();

	}
</script>
@endsection
