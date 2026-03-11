<!-- Main Sidebar Container -->

<style>
    /* General sidebar styling */
    .vertical-menu {
      width: 50px;
      height: 100vh;
      background-color: #343a40;
      position: relative;
    }
	
	.content-box {
	  flex-grow: 1; /* This makes the child content fill the height */
	  background-color: lightgray; /* Just for demonstration */
	  padding: 20px;
	}

    /* Main menu items */
    .vertical-menu a {
      color: white;
      padding: 10px;
      text-decoration: none;
      display: block;
    }

    /*.vertical-menu a:hover {
      background-color: #495057;
      color: white;
    }*/

    /* Mega menu container */
    .mega-menu {
      position: absolute;
      /* top: 800; */
      /* top: 50; */
      left: 100px;
      width: 900px;
     
      background-color: white;
      display: none;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 2px;
      z-index: 9999 !important;
    }

    #a {
      top:10;
    }

    #b {
      top:40;
    }

    #c {
      top:50;
    }

    #d {
      top:200;
    }

    #e {
      top:160;
    }

    #f {
      top:180;
    }

    #g {
      top:300;
    }

    #h {
      top:310;
    }

    #i {
      top:320;
    }

    #j {
      top:400;
    }

    #k {
      top:120;
    }

    /* Display mega menu on hover */
    .vertical-menu a:hover + .mega-menu,
    .mega-menu:hover {
      /* display: block; */
    }

    /* Sub-menu styling */
    .mega-menu .row {
      padding: 5px;
    }

    .mega-menu h5 {
      color: #343a40;
    }

    .mega-menu ul {
      list-style: none;
      padding: 0;
    }

    .mega-menu ul li a {
      text-decoration: none;
      color: #343a40;
      padding: 5px 0;
      display: block;
    }

    .mega-menu ul li a:hover {
      color: #007bff;
    }

    .menu-card {
	
      text-align: center;
      padding: 5px;	 
      border-radius: 5px;
      box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
      transition: background-color 0.3s ease;
    }

    .menu-card:hover {
      background-color: #f8f9fa;
    }

    .menu-card h6 {
      margin-top: 8px;
	    color:black;
	  
    }

    .menu-card i {
      font-size: 30px;
      margin-bottom: 10px;
    }

    .font-size {
      font-size: large; /* or you can use a specific size like 16px, 1.5em, etc. */
    }

  </style>

  <!-- pengaturan lebar sidebar, block hitam, space kesamping, bayangan putih (all in)-->

  <style>

    /* untuk block hitam */
    .main-sidebar, .main-sidebar::before {
      width: 80px !important;
    }

    .main-sidebar, .main-sidebar:hover {
      width: 80px !important;
    }

    /* bayangan putih yg ada panahnya di atur disini */
    .sidebar-mini .main-sidebar .nav-link, .sidebar-mini-md .main-sidebar .nav-link, .sidebar-mini-xs .main-sidebar .nav-link {
      width: calc(80px - 0.5rem * 2);
      transition: width ease-in-out 0.3s;
    }

    /* batas */

    /* untuk space ke samping setelahnya */
    @media (min-width: 768px) {
      body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .content-wrapper, body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-footer, body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-header {
        transition: margin-left 0.3s ease-in-out;
        margin-left: 80px;
      }
    }

    /* batas */

    /* icon bergerak */

    @keyframes wiggle {
        0%, 100% {
            transform: rotate(0deg);
        }
        25% {
            transform: rotate(-20deg);
        }
        50% {
            transform: rotate(20deg);
        }
        75% {
            transform: rotate(-20deg);
        }
    }
    .nav-item a:hover .nav-icon {
        animation: wiggle 0.6s ease-in-out infinite;
    }

    /* batas */

  </style>

  <!-- tutupannya -->

  <aside class="main-sidebar sidebar-dark-primary elevation-4" style="overflow-y: visible;">
    <!-- Brand Logo -->
    <a href="{{url('/')}}" class="brand-link" style="text-align: center">
      <img src="{{url('/img/company.jpg')}}" alt="LookmanDjaja Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light"></span>
    </a>

    <!-- Sidebar -->
    <div class="vertical-menu">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        {{-- <div class="image">
          <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="test">
        </div> --}}
        <!-- <div class="info">
          <a href="#" class="d-block">{{ Auth::user()->name }}</a>
        </div> -->
      </div>

      <!-- SidebarSearch Form -->
      {{-- <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div> --}}

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="#" class="nav-link" data-bs-toggle="tooltip" title="Home">
              <i class="nav-icon fas fa-home"></i>
              <p>
              </p>
            </a>
          </li>

          <li class="nav-item">
            <!-- <a href="#" class="nav-link" data-bs-toggle="tooltip" title="Home"> -->

            <!-- tammbahan untuk dashboard -->
            <a href="#" onclick="javascript:addTab('Dashboard', '{{ url('dashboard') }}')" class="nav-link" data-bs-toggle="tooltip" title="Dashboard">
            <!-- batas dashboard (jangan lupa web dan controller)-->
              <i class="nav-icon fas fa-home"></i>
              <p>
              </p>
            </a>
          </li>


          
          <li class="nav-header"></li>
          <li class="nav-item">
            <a href="#" class="nav-link"   data-bs-toggle="tooltip" title="Master" >
              <i class="nav-icon fas fa-database icon-white fa-beat" ></i>
              <p>
              </p>
            </a>

<!------- penambahan tampilan baru ------->


  <div class="mega-menu" id="a">

  <!-- penambahan judul di menu -->
      <div class="row">
        <div class="col-md-12">
          <h3>MASTER</h3>
          <hr style=" height: 5px;
            background-color: #333; 
            border: none; 
            margin: 20px 0; "/>
        </div>
      </div>

  <!-- batas -->

      <div class="row d-flex">
        <div class="col-md-3">

            <!-- kalau sub menu, di kasih warna pinggirannya. style:"border" -->
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#e3f1fc;">
            <!-- batas -->
              <a href="javascript:addTab('Supplier Busana', '{{url('sup')}}')">
                  <!-- <i class="nav-icon far fa-user fa-10x icon-purple"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-user icon-blue"></i>
                  <h6>Supplier Busana</h6>
              </a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#e3f1fc;">
              <a href="javascript:addTab('Counter Busana', '{{url('counter')}}')">
                  <!-- <i class="nav-icon fas fa-users icon-yellow" style="text-align: center;"></i> -->
                  <i style="margin-left:-25px;font-size: 40px;" class="nav-icon fas fa-warehouse icon-blue"></i>
                <h6>Counter Busana</h6>
              </a>
			      </div>
        </div>
		    <div class="col-md-3">
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#e3f1fc;">
              <a href="javascript:addTab('Barang Busana', '{{url('brg')}}')">
                  <!-- <i class="nav-icon fas fa-layer-group icon-green" style="text-align: center;"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-cube icon-blue"></i>
                <h6>Barang Busana</h6>
              </a>
			      </div>
        </div>


    </div>

	  
      
      
  </li>
<!-----------------batas ------------------------>


          <li class="nav-item">
          @if ( (Auth::user()->divisi=="programmer") || (Auth::user()->divisi=="owner") || (Auth::user()->divisi=="purchase") || (Auth::user()->divisi=="gudang"))
			      <a href="#" class="nav-link"  data-bs-toggle="tooltip" title="Transaksi A" >
              <i class="nav-icon fas fa-hand-holding-heart icon-pink"></i>
              <p>
              </p>
            </a>

<!------- penambahan tampilan baru ------->


    <div class="mega-menu" id="b">

      <!-- penambahan judul di menu -->
          <div class="row">
            <div class="col-md-12">
              <h3>TRANSAKSI A</h3>
              <hr style=" height: 5px;
                background-color: #333; 
                border: none; 
                margin: 20px 0; "/>
            </div>
          </div>

      <!-- batas -->

      <div class="row d-flex">
        <div class="col-md-3">
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ebd9ff;">
              <a href="javascript:addTab('Purchase Order', '{{url('po?flagz=PO&golz=PB')}}')">
                <!-- <i class="nav-icon fas fa-cart-plus icon-yellow"></i>  -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-cart-plus icon-purple"></i>
                <h6>Purchase Order</h6>
              </a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="menu-card"  style="border:1px solid #aabbcc; background-color:#ebd9ff;">
                <a href="javascript:addTab('PO Outlet', '{{url('po?flagz=PO&golz=PZ')}}')">
                  <!-- <i class="nav-icon fas fa-store icon-white"></i> -->
                  <i style="margin-left:-8px;font-size: 40px;" class="nav-icon fas fa-cart-plus icon-purple"></i>
                  <h6>PO Outlet</h6>
                </a>
			      </div>
        </div>
		    <div class="col-md-3">
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ebd9ff;">
                <a href="javascript:addTab('Pembelian', '{{url('beli?flagz=BL&golz=BS')}}')">
                  <!-- <i class="nav-icon fas fa-store icon-white"></i> -->
                  <i style="margin-left:-15px;font-size: 40px;" class="nav-icon fas fa-store icon-purple"></i>
                  <h6>Pembelian</h6>
                </a>
			      </div>
        </div>
        <div class="col-md-3">
          <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ebd9ff;">
              <a href="javascript:addTab('Pelayanan Outlet', '{{url('kirim?flagz=KR&golz=KO')}}')">
                <!-- <i class="nav-icon fas fa-crop icon-orange"></i> -->
                  <i style="margin-left:-15px;font-size: 40px;" class="nav-icon fas fa-money-bill icon-purple"></i>
                <h6>Pelayanan Outlet</h6>
              </a>
			    </div>
        </div>
      </div>

	    <div class="row">
		    <div class="col-md-3">
          <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ebd9ff;">
              <a href="javascript:addTab('Stock Opname', '{{url('stockb?flagz=KB')}}')">
                <!-- <i class="nav-icon fas fa-crop icon-orange"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-hand-holding-heart icon-purple"></i>
                <h6>Stock Opname</h6>
              </a>
			    </div>
        </div>
        <div class="col-md-3">
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ebd9ff;">
              <a href="javascript:addTab('Terima Barang TGZ', '{{url('beli?flagz=BL&golz=BO')}}')">
                <!-- <i class="nav-icon fas fa-anchor icon-blue" style="text-align: center;"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-cash-register icon-purple"></i>
                <h6>Terima Barang TGZ</h6>
              </a>
			      </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-3">
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ffe0ba;">
              <a href="javascript:addTab('Rencana Retur', '{{url('retur?flagz=RS')}}')">
                <!-- <i class="nav-icon fas fa-cart-plus icon-yellow"></i>  -->
                  <i style="margin-left:-30px;font-size: 40px;" class="nav-icon fas fa-cart-plus icon-orange"></i>
                <h6>Rencana Retur</h6>
              </a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ffe0ba;">
                <a href="javascript:addTab('Retur Pembelian', '{{url('beli?flagz=BL&golz=RX')}}')">
                  <!-- <i class="nav-icon fas fa-store icon-white"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-retweet icon-orange"></i>
                  <h6>Retur Pembelian</h6>
                </a>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ffe0ba;">
                <a href="javascript:addTab('Retur ke TGZ', '{{url('retur?flagz=RO')}}')">                
                  <!-- <i class="nav-icon fas fa-store icon-white"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-retweet icon-orange"></i>
                  <h6>Retur ke TGZ</h6>
                </a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ffe0ba;">
                <a href="javascript:addTab('Terima Retur Outlet', '{{url('terima?flagz=TR&golz=RM')}}')">
                <!-- <i class="nav-icon fas fa-anchor icon-blue" style="text-align: center;"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-retweet icon-orange"></i>
                  <h6>Terima Retur Outlet</h6>
                </a>
            </div>
        </div>

      </div>

	  
    </div>

<!----- batas ----->
			
           @endif			 		
          </li>  

          <li class="nav-item">
          @if ( (Auth::user()->divisi=="programmer") || (Auth::user()->divisi=="owner") || (Auth::user()->divisi=="sales") || (Auth::user()->divisi=="gudang"))
            <a href="#" class="nav-link"  data-bs-toggle="tooltip" title="Transaksi B" >
			      <!-- <a href="#" class="nav-link"> -->
              <i class="nav-icon fas fa-cash-register icon-orange"></i>
              <p>
              </p>
            </a>
			
<!------- penambahan tampilan baru ------->


    <div class="mega-menu" id="c">

      <!-- penambahan judul di menu -->
          <div class="row">
            <div class="col-md-12">
              <h3>TRANSAKSI B</h3>
              <hr style=" height: 5px;
                background-color: #333; 
                border: none; 
                margin: 20px 0; "/>
            </div>
          </div>

      <!-- batas -->

      

      <div class="row">

        <div class="col-md-3">
          <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ffe0ba;">
              <a href="javascript:addTab('Posting Beli', '{{url('beli/post')}}')">
                <!-- <i class="nav-icon fas fa-crop icon-orange"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-file icon-orange"></i>
                <h6>Posting Beli</h6>
              </a>
          </div>
        </div>

        <div class="col-md-3">
          <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ffe0ba;">
              <a href="javascript:addTab('Posting Retur ke TGZ', '{{url('retur/post')}}')">
                <!-- <i class="nav-icon fas fa-crop icon-orange"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-file icon-orange"></i>
                <h6>Post Retur ke TGZ</h6>
              </a>
          </div>
        </div>

        <div class="col-md-3">
          <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ffe0ba;">
              <a href="javascript:addTab('Posting Terima Retur Outlet', '{{url('retur/post')}}')">
                <!-- <i class="nav-icon fas fa-crop icon-orange"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-file icon-orange"></i>
                <h6>Post Terima Retur Outlet</h6>
              </a>
          </div>
        </div>

        <div class="col-md-3">
          <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ffe0ba;">
              <a href="javascript:addTab('Posting Penjualan', '{{url('jual/post')}}')">
                <!-- <i class="nav-icon fas fa-crop icon-orange"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-file icon-orange"></i>
                <h6>Posting Penjualan (belum bisa)</h6>
              </a>
          </div>
        </div>

      </div>

      <div class="row">

        <div class="col-md-3">
          <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ffe0ba;">
              <a href="javascript:addTab('Posting Retur', '{{url('beli/post')}}')">
                <!-- <i class="nav-icon fas fa-crop icon-orange"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-file icon-orange"></i>
                <h6>Posting Retur</h6>
              </a>
          </div>
        </div>

        <div class="col-md-3">
          <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ffe0ba;">
              <a href="javascript:addTab('Posting Stock Opname', '{{url('stockb/post')}}')">
                <!-- <i class="nav-icon fas fa-crop icon-orange"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-file icon-orange"></i>
                <h6>Posting Stock Opname</h6>
              </a>
          </div>
        </div>

        <div class="col-md-3">
          <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ffe0ba;">
              <a href="javascript:addTab('Posting Order Outlet', '{{url('stockb/post')}}')">
                <!-- <i class="nav-icon fas fa-crop icon-orange"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-file icon-orange"></i>
                <h6>Posting Order Outlet</h6>
              </a>
          </div>
        </div>

        <div class="col-md-3">
          <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ffe0ba;">
              <a href="javascript:addTab('Posting Terima TGZ', '{{url('beli/post')}}')">
                <!-- <i class="nav-icon fas fa-crop icon-orange"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-file icon-orange"></i>
                <h6>Posting Terima TGZ</h6>
              </a>
          </div>
        </div>

      </div>

      <div class="row">

        <div class="col-md-3">
          <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ffe0ba;">
              <a href="javascript:addTab('Posting Harga Jual', '{{url('harga/post')}}')">
                <!-- <i class="nav-icon fas fa-crop icon-orange"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-file icon-orange"></i>
                <h6>Posting Harga Jual</h6>
              </a>
          </div>
        </div>

      </div>
	
      @endif			 		
      </li> 

<!----------------------------------------->

     <li class="nav-item">
     @if ( (Auth::user()->divisi=="programmer") || (Auth::user()->divisi=="owner") || (Auth::user()->divisi=="sales") || (Auth::user()->divisi=="gudang"))
       <a href="#" class="nav-link" data-bs-toggle="tooltip" title="Transaksi C">
         <i class="nav-icon fas fa-pen-nib icon-green"></i>
         <p>
         </p>
       </a>

<!------- penambahan tampilan baru ------->


    <div class="mega-menu" id="k">

      <!-- penambahan judul di menu -->
          <div class="row">
            <div class="col-md-12">
              <h3>TRANSAKSI C</h3>
              <hr style=" height: 5px;
                background-color: #333; 
                border: none; 
                margin: 20px 0; "/>
            </div>
          </div>

      <!-- batas -->

      <div class="row d-flex">
        
        <div class="col-md-3">
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#e3f1fc;">
              <a href="javascript:addTab('Diskon Penjualan', '{{url('diskon?flagz=DS')}}')" >
                <!-- <i class="nav-icon fas fa-anchor icon-blue" style="text-align: center;"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-pen icon-blue"></i>
                <h6>Dikson</h6>
                <h6>Penjualan</h6>
              </a>
			      </div>
        </div>

        <div class="col-md-3">
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#e3f1fc;">
              <a href="javascript:addTab('Form Bayar Busana', '{{url('tagi?flagz=BS')}}')" >
                <!-- <i class="nav-icon fas fa-anchor icon-blue" style="text-align: center;"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-paste icon-blue"></i>
                <h6>Form Bayar</h6>
                <h6>Busana</h6>
              </a>
			      </div>
        </div>

        
        <div class="col-md-3">
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#e3f1fc;">
              <a href="javascript:addTab('Pengajuan Harga Jual', '{{url('harga?flagz=HG&golz=BS')}}')" >
                <!-- <i class="nav-icon fas fa-anchor icon-blue" style="text-align: center;"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-paste icon-blue"></i>
                <h6>Pengajuan</h6>
                <h6>Harga Jual</h6>
              </a>
			      </div>
        </div>
        
        <div class="col-md-3">
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#e3f1fc;">
              <a href="javascript:addTab('Usulan Barang Busana Turun Harga', '{{url('rusulanth')}}')" >
                <!-- <i class="nav-icon fas fa-anchor icon-blue" style="text-align: center;"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-paste icon-blue"></i>
                <h6>Usulan Barang Busana</h6>
                <h6>Turun Harga</h6>
              </a>
			      </div>
        </div>
      </div>

      <div class="row d-flex">
        
        <div class="col-md-3">
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#e3f1fc;">
              <a href="javascript:addTab('Pencetakan Label Harga', '{{url('harga?flagz=HG&golz=LB')}}')" >
                <!-- <i class="nav-icon fas fa-anchor icon-blue" style="text-align: center;"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-pen icon-blue"></i>
                <h6>Pencetakan Label Harga</h6>
              </a>
			      </div>
        </div>

        <div class="col-md-3">
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#e3f1fc;">
              <a href="javascript:addTab('Transaksi Lain-Lain', '{{url('lain?flagz=TL')}}')" >
                <!-- <i class="nav-icon fas fa-anchor icon-blue" style="text-align: center;"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-paste icon-blue"></i>
                <h6>Transaksi Lain-Lain</h6>
              </a>
			      </div>
        </div>

        
        <div class="col-md-3">
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#e3f1fc;">
              <a href="javascript:addTab('Budget Order Lebih', '{{url('budget?flagz=BO')}}')" >
                <!-- <i class="nav-icon fas fa-anchor icon-blue" style="text-align: center;"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-paste icon-blue"></i>
                <h6>Budget Order Lebih</h6>
              </a>
			      </div>
        </div>
        
        <div class="col-md-3">
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#e3f1fc;">
              <a href="javascript:addTab('Conter Pembelian', '{{url('counter?flagz=CT')}}')" >
                <!-- <i class="nav-icon fas fa-anchor icon-blue" style="text-align: center;"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-paste icon-blue"></i>
                <h6>Conter Pembelian</h6>
              </a>
			      </div>
        </div>
      </div>

      <div class="row d-flex">
        
        <div class="col-md-3">
            <div class="menu-card" style="border:1px solid #aabbcc; background-color:#e3f1fc;">
              <a href="javascript:addTab('Buat Pajak Retur', '{{url('rfakturpj')}}')" >
                <!-- <i class="nav-icon fas fa-anchor icon-blue" style="text-align: center;"></i> -->
                  <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-pen icon-blue"></i>
                <h6>Buat Pajak Retur (belum)</h6>
              </a>
			      </div>
        </div>

        
      </div>

    </div>

      @endif			 		
      </li> 

     

          <li class="nav-item">          
            <a href="#" class="nav-link" data-bs-toggle="tooltip" title="Lain-Lain">
              <i class="nav-icon fas fa-book icon-yellow"></i>
              <p>
              </p>
            </a>

<!------- penambahan tampilan baru ------->


          <div class="mega-menu" id="d">

            <!-- penambahan judul di menu -->
                <div class="row">
                  <div class="col-md-12">
                    <h3>Lain-Lain</h3>
                    <hr style=" height: 5px;
                      background-color: #333; 
                      border: none; 
                      margin: 20px 0; "/>
                  </div>
                </div>

            <!-- batas -->

            <div class="row d-flex">
              <div class="col-md-3">
                  <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ffd9d9;">
                    <a href="javascript:addTab('Report Barang', '{{url('rbrg')}}')">
                      <!-- <i class="nav-icon fas fa-cart-plus icon-yellow"></i>  -->
                        <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-cubes icon-red"></i>
                      <h6>Jackpot</h6>
                    </a>
                  </div>
              </div>
            </div>

          
          </div>

<!----- batas ----->

          </li> 

<!--------------Laporan Pembelian---------------->          


          <li class="nav-item">          
            <a href="#" class="nav-link" data-bs-toggle="tooltip" title="Tools">
              <i class="nav-icon fas fa-plus icon-yellow"></i>
              <p>
              </p>
            </a>
			
<!------- penambahan tampilan baru ------->


          <div class="mega-menu" id="e">

            <!-- penambahan judul di menu -->
                <div class="row">
                  <div class="col-md-12">
                    <h3>Tools</h3>
                    <hr style=" height: 5px;
                      background-color: #333; 
                      border: none; 
                      margin: 20px 0; "/>
                  </div>
                </div>

            <!-- batas -->

            <div class="row d-flex">
              <div class="col-md-3">
                  <div class="menu-card" style="">
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#periodeModal" id="periode">
                      <!-- <i class="nav-icon fas fa-cart-plus icon-yellow"></i> -->
                        <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-calendar icon-purple"></i>
                      <h6>Ganti Periode</h6>
                    </a>
                  </div>
              </div>
              <div class="col-md-3">
                  <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ffe6ff;">
                    <a href="javascript:addTab('Cetak Barcode', '{{url('rpo')}}')">
                      <!-- <i class="nav-icon fas fa-cart-plus icon-yellow"></i>  -->
                        <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-cart-plus icon-pink"></i>
                      <h6>Cetak Barcode</h6>
                    </a>
                  </div>
              </div>
              <div class="col-md-3">
                  <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ffe6ff;">
                      <a href="javascript:addTab('cetak Barcode Rak', '{{url('rbeli')}}')">
                        <!-- <i class="nav-icon fas fa-store icon-white"></i> -->
                          <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-store icon-pink"></i>
                        <h6>Cetak Barcode Rak</h6>
                      </a>
                  </div>
              </div>
              <div class="col-md-3">
                <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ffe6ff;">
                    <a href="javascript:addTab('Cabang', '{{url('rthut')}}')" >
                      <!-- <i class="nav-icon fas fa-crop icon-orange"></i> -->
                        <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-briefcase icon-pink"></i>
                      <h6>Cabang</h6>
                    </a>
                  </div>
              </div>
              <div class="col-md-3">
                <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ffe6ff;">
                    <a href="javascript:addTab('Cek Barcode Double', '{{url('rum')}}')" >
                      <!-- <i class="nav-icon fas fa-crop icon-orange"></i> -->
                        <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-wallet icon-pink"></i>
                      <h6>Cek Barcode Double</h6>
                    </a>
                </div>
              </div>
            </div>
            
          
          </div>

<!----- batas ----->

          </li> 

<!--------------Laporan Penjualan---------------->

          <li class="nav-item">          
            <a href="#" class="nav-link" data-bs-toggle="tooltip" title="Reports">
              <i class="nav-icon fas fa-book icon-yellow"></i>
              <p>
              </p>
            </a>

<!------- penambahan tampilan baru ------->


            <div class="mega-menu" id="f">

              <!-- penambahan judul di menu -->
                  <div class="row">
                    <div class="col-md-12">
                      <h3>Reports</h3>
                      <hr style=" height: 5px;
                        background-color: #333; 
                        border: none; 
                        margin: 20px 0; "/>
                    </div>
                  </div>

              <!-- batas -->

              <div class="row d-flex">
                <div class="col-md-3">
                    <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ebd9ff;">
                      <a href="javascript:addTab('Report Penjualan', '{{url('rso')}}')">
                        <!-- <i class="nav-icon fas fa-cart-plus icon-yellow"></i>  -->
                          <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-user-tie icon-purple"></i>
                        <h6>Report Penjualan</h6>
                      </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ebd9ff;">
                      <a href="javascript:addTab('Stok Barang', '{{url('rsurats')}}')">
                          <!-- <i class="nav-icon fas fa-store icon-white"></i> -->
                            <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-paste icon-purple"></i>
                          <h6>Stok Barang</h6>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ebd9ff;">
                      <a href="javascript:addTab('Kasir Bantu', '{{url('rjual')}}')">
                          <!-- <i class="nav-icon fas fa-store icon-white"></i> -->
                            <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-store icon-purple"></i>
                          <h6>Kasir Bantu</h6>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ebd9ff;">
                      <a href="javascript:addTab('Report Konsinasi', '{{url('rtpiu')}}')">
                        <!-- <i class="nav-icon fas fa-anchor icon-blue" style="text-align: center;"></i> -->
                          <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-cash-register icon-purple"></i>
                        <h6>Report Konsinasi</h6>
                      </a>
                    </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-3">
                    <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ccffd2;">
                      <a href="javascript:addTab('Turun Harga', '{{url('ruj')}}')" >
                        <!-- <i class="nav-icon fas fa-anchor icon-blue" style="text-align: center;"></i> -->
                          <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-coins icon-green"></i>
                        <h6>Turun Harga</h6>
                      </a>
                    </div>
                </div>
                <div class="col-md-3">
                  <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ccffd2;">
                      <a href="javascript:addTab('Report Pembelian', '{{url('rpiu')}}')">
                        <!-- <i class="nav-icon fas fa-crop icon-orange"></i> -->
                          <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-money-bill icon-green"></i>
                        <h6>Report Pembelian</h6>
                      </a>
                    </div>
                </div>
                <div class="col-md-3">
                  <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ccffd2;">
                      <a href="javascript:addTab('Diskon Per Counter', '{{url('rstockb')}}')" >
                        <!-- <i class="nav-icon fas fa-crop icon-orange"></i> -->
                          <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-pen icon-green"></i>
                        <h6>Diskon Per Counter</h6>
                      </a>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ccffd2;">
                      <a href="javascript:addTab('Report Amplop', '{{url('rsisapiu')}}')" >
                        <!-- <i class="nav-icon fas fa-crop icon-orange"></i> -->
                          <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-money-bill icon-green"></i>
                        <h6>Report Amplop</h6>
                      </a>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-3">
                    <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ccffd2;">
                      <a href="javascript:addTab('Barang Macet', '{{url('rtagih')}}')" >
                        <!-- <i class="nav-icon fas fa-anchor icon-blue" style="text-align: center;"></i> -->
                          <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-coins icon-green"></i>
                        <h6>Barang Macet</h6>
                      </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ccffd2;">
                      <a href="javascript:addTab('Discon Penjualan', '{{url('rkomisi')}}')" >
                        <!-- <i class="nav-icon fas fa-anchor icon-blue" style="text-align: center;"></i> -->
                          <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-coins icon-green"></i>
                        <h6>Discon Penjualan</h6>
                      </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="menu-card" style="border:1px solid #aabbcc; background-color:#ccffd2;">
                      <a href="javascript:addTab('Discon Penjualan', '{{url('rkomisi')}}')" >
                        <!-- <i class="nav-icon fas fa-anchor icon-blue" style="text-align: center;"></i> -->
                          <i style="margin-left:-5px;font-size: 40px;" class="nav-icon fas fa-coins icon-green"></i>
                        <h6>Brg Busana Turun Harga</h6>
                      </a>
                    </div>
                </div>
              </div>
            
            </div>

<!----- batas ----->	

          </li>


          


          @if (Auth::user()->hasRole('superadmin') || (Auth::user()->divisi=="gudang"))
          <li class="nav-header">File</li>
          <li class="nav-item" data-bs-toggle="tooltip" title="Change Password">
            <!-- href di ganti dengan onclick -->
            <a onclick="addTab('User', '{{url('/user/manage')}}')" href="#" class="nav-link">
              <i class="nav-icon fas fa-users icon-orange"></i>
              <p>
              </p>
            </a>
          </li>
          @endif
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
