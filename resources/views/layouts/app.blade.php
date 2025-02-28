<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
  <title>Warehouse Management System</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">

  <!-- CSS Libraries -->

  <!-- Template CSS -->
  
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">

  <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
  
  
  <!-- Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


  <!-- Datatable Jquery -->
  <link rel="stylesheet" href="//cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

  <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.4.1/css/dataTables.dateTime.min.css">

  <!-- Start GA -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-94034622-3');
  </script>

  
<!-- /END GA --></head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg bg-light"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars text-dark"></i></a></li>
          </ul>
        </form>
        <ul class="navbar-nav navbar-right">
          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
            <img alt="image" src="{{ asset('assets/img/avatar/avatar-1.png') }}" class="rounded-circle mr-1">
            <div class="d-sm-none d-lg-inline-block text-dark">Hi, {{ auth()->user()->name }}</div></a>
            <div class="dropdown-menu dropdown-menu-right">
                  <a class="dropdown-item" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                                Swal.fire({
                                    title: 'Konfirmasi Keluar',
                                    text: 'Apakah Anda yakin ingin keluar?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Ya, Keluar!'
                                  }).then((result) => {
                                    if (result.isConfirmed) {
                                      document.getElementById('logout-form').submit();
                                    }
                                  });">
                               <i class="fas fa-sign-out-alt"></i> {{ __('Keluar') }}
                              </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                  </a>
            </div>
          </li>
        </ul>
      </nav>
      <div class="main-sidebar sidebar-style-1 bg-dark">
        <aside id="sidebar-wrapper ">

          <div class="sidebar-brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo STEP" style="height: 35px; width: auto; margin-right: 5px; margin-top: 20px; margin-bottom: 25px">
            <a href="/" class="text-white">WAREHOUSE</a>
          </div>

          <ul class="sidebar-menu"> 
            @if (auth()->user()->role->role === 'admin divisi' || auth()->user()->role->role === 'kepala divisi')
                <li class="sidebar-item mt-4">
                    <a class="nav-link  {{ Request::is('dashboard') || Request::is('dashboard') ? 'active' : '' }}" href="/dashboard">
                        <i class="fas fa-fire "></i> <span class="align-middle">Dashboard</span>
                    </a>
                </li>

                <li class="menu-header">PERMINTAAN BARANG</li>
                <li><a class="nav-link {{ Request::is('orders') ? 'active' : '' }}" href="/orders"><i class="fa fa-sharp fa-solid fa-clipboard-list"></i> <span>Permintaan Barang</span></a></li>
                <li><a class="nav-link {{ Request::is('laporan-permintaan') ? 'active' : '' }}" href="laporan-permintaan"><i class="fa fa-sharp fa-reguler fa-file"></i><span>Laporan Permintaan</span></a></li>        
            @endif


            @if (auth()->user()->role->role === 'superadmin' || auth()->user()->role->role === 'kepala gudang' || auth()->user()->role->role === 'admin gudang') 
              <li class="sidebar-item mt-4">
                <a class="nav-link {{ Request::is('dashboard') || Request::is('dashboard') ? 'active' : '' }}" href="/dashboard">
                  <i class="fas fa-fire"></i> <span class="align-middle">Dashboard</span>
                </a>
              </li>

              <li class="menu-header">DATA MASTER</li>
                <li><a class="nav-link {{ Request::is('barang') ? 'active' : '' }}" href="/barang"><i class="fas fa-thin fa-cubes"></i><span>Data Barang</span></a></li>
                <li><a class="nav-link {{ Request::is('jenis-barang') ? 'active' : '' }}" href="/jenis-barang"><i class="fas fa-list"></i><span>Kategori</span></a></li>
                <li><a class="nav-link {{ Request::is('department') ? 'active' : '' }}" href="/department"><i class="fas fa-thin fa-building"></i><span>Departemen</span></a></li>

              <li class="menu-header">MANAJEMEN BARANG</li>
              <li><a class="nav-link {{ Request::is('barang-masuk') ? 'active' : '' }}" href="/barang-masuk"><i class="fa fa-solid fa-arrow-right"></i><span>Barang Masuk</span></a></li>
              <li><a class="nav-link {{ Request::is('barang-keluar') ? 'active' : '' }}" href="/barang-keluar"><i class="fa fa-sharp fa-solid fa-arrow-left"></i> <span>Barang Keluar</span></a></li>
              <li><a class="nav-link {{ Request::is('orders') ? 'active' : '' }}" href="/orders"><i class="fa fa-sharp fa-solid fa-clipboard-list"></i> <span>Permintaan Barang</span></a></li>
            
              <li class="menu-header">LAPORAN</li>
              <li><a class="nav-link {{ Request::is('laporan-stok') ? 'active' : '' }}" href="laporan-stok"><i class="fa fa-sharp fa-reguler fa-file"></i><span>Stok</span></a></li>
              <li><a class="nav-link {{ Request::is('laporan-barang-masuk') ? 'active' : '' }}" href="laporan-barang-masuk"><i class="fa fa-regular fa-file-import"></i><span>Barang Masuk</span></a></li>
              <li><a class="nav-link {{ Request::is('laporan-barang-keluar') ? 'active' : '' }}" href="laporan-barang-keluar"><i class="fa fa-sharp fa-regular fa-file-export"></i><span>Barang Keluar</span></a></li> 
              <li><a class="nav-link {{ Request::is('laporan-permintaan-department') ? 'active' : '' }}" href="laporan-permintaan-department"><i class="fa fa-sharp fa-regular fa-file-upload"></i><span>Permintaan</span></a></li> 

              
              @if (auth()->user()->role->role === 'superadmin') 
                <li class="menu-header">MANAJEMEN USER</li>
                <li><a class="nav-link {{ Request::is('data-pengguna') ? 'active' : '' }}" href="data-pengguna"><i class="fa fa-solid fa-users"></i><span>Daftar Pengguna</span></a></li>
                <li><a class="nav-link {{ Request::is('hak-akses') ? 'active' : '' }}" href="hak-akses"><i class="fa fa-solid fa-user-lock"></i><span>Hak Akses/Role</span></a></li>   
              @endif
            @endif  
          </ul>

        </aside>
      </div>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
            @yield('content')
          <div class="section-body">
          </div>
        </section>
      </div>
      <footer class="main-footer">
        <div class="footer-left">
          Copyright STEP &copy; 2025
        </div>
        <div class="footer-right">
          
        </div>
      </footer>
    </div>
  </div>


  
  <!-- General JS Scripts -->
  <script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/modules/popper.js') }}"></script>
  <script src="{{ asset('assets/modules/tooltip.js') }}"></script>
  <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
  <script src="{{ asset('assets/modules/moment.min.js') }}"></script>
  <script src="{{ asset('assets/js/stisla.js') }}"></script>

  <!-- JS Libraies -->
  
  <!-- Select2 Jquery -->
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>

  <!-- Page Specific JS File -->
  
  <!-- Template JS File -->
  <script src="{{ asset('assets/js/scripts.js') }}"></script>
  <script src="{{ asset('assets/js/custom.js') }}"></script>

  <!-- Datatables Jquery -->
  <script type="text/javascript" src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

  <!-- Sweet Alert -->
  @include('sweetalert::alert')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

  <!-- Day Js Format -->
  <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>

  
  @stack('scripts')

  
  <script>
    $(document).ready(function() {
      var currentPath = window.location.pathname;
  
      $('.nav-link a[href="' + currentPath + '"]').addClass('active');
    });

  </script>

  
</body>
</html>
