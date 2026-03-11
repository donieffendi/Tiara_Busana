<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Foxie</title>
       
<!-- Font Awesome 5 -->
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/fontawesome.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css" /> -->
  <link rel="stylesheet" href="{{asset('foxie_js_css/fontawesome.min.css')}}" />
  <link rel="stylesheet" href="{{asset('foxie_js_css/all.min.css')}}" />

  <!-- Bootstrap -->

  <!-- Date Picker -->
  <!-- <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->
  <link rel="stylesheet" href="{{asset('foxie_js_css/jquery-ui.css')}}" />
  
  <!-- Data Table -->
  <!-- <link rel="stylesheet" type="text/css" href="{{url('https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css') }}">  -->
  <link rel="stylesheet" href="{{asset('foxie_js_css/jquery.dataTables.min.css')}}" />
  
  <!-- Data Table Button -->
  <!-- <link rel="stylesheet" href="{{url('https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css') }}"> -->
  <link rel="stylesheet" href="{{asset('foxie_js_css/buttons.dataTables.min.css')}}" />

  <!-- Local Asset -->
  <link rel="stylesheet" href="{{asset('css/app.css')}}">

   {{-- LOADING BAR --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/ldbtn/ldbtn.min.css') }}" />
  
    @auth
    <style>
      p, label, span, div, a {
        font-family:{{Auth::user()->font_family}} !important;
        font-size:{{Auth::user()->font_size}}px !important;
      }
    </style>
    @endauth
  
  @yield('styles')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar ( Menu Atas ) -->
  @include('layouts.navigation')

  <!-- Sidebar ( Menu Samping ) -->
  @include('layouts.sidebar')

  @yield('content')
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-light">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5>Title</h5>
      <p>Sidebar content</p>
    </div>
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- Local Asset -->
<script src="{{asset('js/app.js')}}"></script>

<!-- jQuery -->
<!-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script> -->
<script src="{{asset('foxie_js_css/jquery-3.5.1.js')}}"></script>

<!-- Date Picker -->
<!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->
<script src="{{asset('foxie_js_css/jquery-ui.js')}}"></script>

<!-- Chart Js -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js" integrity="sha512-Wt1bJGtlnMtGP0dqNFH1xlkLBNpEodaiQ8ZN5JLA5wpc1sUlk/O5uuOMNgvzddzkpvZ9GLyYNa8w2s7rqiTk5Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<script src="{{asset('foxie_js_css/chart.min.js')}}"></script>

<!--
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.esm.js" integrity="sha512-IPqefcmFCuGcYxl/uIjvyCXwh5T9+EB2MFT7W9RUZd20d7PLfgdT975xdhyesvdXH6Au8SyXOw1236LY1lFl5Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.esm.min.js" integrity="sha512-2Vi/lCX8NaXlAhzc28RAoteYAiJVoz4y3Xq/IpHQCw7KU25I34fDqJSVSUml2tQRVYFnf3IMy6O59zKJh79hiw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
-->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.js" integrity="sha512-b3xr4frvDIeyC3gqR1/iOi6T+m3pLlQyXNuvn5FiRrrKiMUJK3du2QqZbCywH6JxS5EOfW0DY0M6WwdXFbCBLQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<script src="{{asset('foxie_js_css/chart.js')}}"></script>

<!-- Chart Js Datalabel -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.0.0/chartjs-plugin-datalabels.min.js" integrity="sha512-R/QOHLpV1Ggq22vfDAWYOaMd5RopHrJNMxi8/lJu8Oihwi4Ho4BRFeiMiCefn9rasajKjnx9/fTQ/xkWnkDACg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<script src="{{asset('foxie_js_css/chartjs-plugin-datalabels.min.js')}}"></script>

<!--
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.0.0/chartjs-plugin-datalabels.esm.js" integrity="sha512-cvx6au1OqNxsiOrnmN33ji5GSEeN5Du+jLqWFMfBJZUTy96TSOAFLnFWM3U7kNGbbjMYqZlA1BriRdpvAz5ztQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.0.0/chartjs-plugin-datalabels.esm.min.js" integrity="sha512-JfHwTRyhOmDIoQrVjZOj1hsoXZDFPsab+TxImdihb+8UfCsd3T1fy7d3VXSXfD6Y/wmc/jVZ/PWNK7fegBkpoA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
-->

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.0.0/chartjs-plugin-datalabels.js" integrity="sha512-+D6VhUjj9axDtT88udGqgxLxX+4S55cV8Awkl5yBidFVOMlWBuWjSigU3E9JDXVbRndKgLavMD04nycTJbmo/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<script src="{{asset('foxie_js_css/chartjs-plugin-datalabels.js')}}"></script>


<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script> -->
<script type="text/javascript" src="{{asset('foxie_js_css/moment.min.js')}}"></script>

<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.bootstrap4.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.25/dataRender/datetime.js"></script> -->

<script type="text/javascript" src="{{asset('foxie_js_css/jszip.js')}}"></script>
<script type="text/javascript" src="{{asset('foxie_js_css/pdfmake.js')}}"></script>
<script type="text/javascript" src="{{asset('foxie_js_css/vfs_fonts.js')}}"></script>
<script type="text/javascript" src="{{asset('foxie_js_css/jquery.dataTables.js')}}"></script>
<script type="text/javascript" src="{{asset('foxie_js_css/dataTables.bootstrap4.js')}}"></script>
<script type="text/javascript" src="{{asset('foxie_js_css/dataTables.buttons.js')}}"></script>
<script type="text/javascript" src="{{asset('foxie_js_css/buttons.bootstrap4.js')}}"></script>
<script type="text/javascript" src="{{asset('foxie_js_css/buttons.html5.js')}}"></script>
<script type="text/javascript" src="{{asset('foxie_js_css/buttons.print.js')}}"></script>
<script type="text/javascript" src="{{asset('foxie_js_css/datetime.js')}}"></script>

@yield('javascripts')
@yield('footer-scripts')
</body>

</html>
