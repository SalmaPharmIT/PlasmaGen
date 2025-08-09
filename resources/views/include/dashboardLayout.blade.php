<!DOCTYPE html>
<!-- resources/views/layout.blade.php -->
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'PlasmaGen')</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="{{ asset('assets/img/favicon.ico') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files - Load CSS BEFORE JavaScript -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">

  <!-- DataTables CSS via CDN -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

  <!-- Template Main CSS File -->
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

  <!-- Select2 CSS (Load in the CORRECT ORDER) -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  
  <!-- Select2 Bootstrap Theme -->
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
  
  <!-- Custom Styles -->
  <style>
    .action-btn {
      cursor: pointer;
      color: #dc3545; /* Bootstrap danger color */
      font-size: 18px;
    }

    .table td,
    .table th {
      vertical-align: middle;
    }
    
    /* Emergency Select2 Styles */
    .emergency-select2-container {
      margin-bottom: 10px;
    }
    
    /* Extra styles to ensure dropdowns are visible */
    #emergency-select2-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 0;
      overflow: visible;
      z-index: 999999999 !important;
    }
    
    .emergency-select2-dropdown {
      box-shadow: 0 4px 16px rgba(0,0,0,0.2) !important;
    }
    
    /* Make sure ALL selects have correct styling */
    select.select2 {
      width: 100% !important;
    }
    
    /* Force select2 dropdowns to always be visible */
    .select2-container--open .select2-dropdown {
      z-index: 999999999 !important;
      display: block !important;
      opacity: 1 !important;
      visibility: visible !important;
    }
  </style>

  <!-- Stack for Additional Styles -->
  @stack('styles')
  
  <!-- jQuery (required for DataTables and Select2) - PROPER PLACEMENT -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.7.4/lottie.min.js"></script>

<body>

  @include('include.header') <!-- Include Header -->

  @include('include.sidebar') <!-- Include Sidebar -->

  <main id="main" class="main">
    @yield('content') <!-- Main Content -->
  </main><!-- End #main -->

  @include('include.footer') <!-- Optional: Include Footer if you have one -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
  <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/quill/quill.js') }}"></script>
  <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>

  <!-- DataTables JS via CDN -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

  <!-- Template Main JS File -->
  <script src="{{ asset('assets/js/main.js') }}"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <!-- Select2 JS - Load AFTER jQuery but BEFORE custom scripts -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- Initialize Select2 -->
  <script>
    $(document).ready(function() {
      // Make sure Select2 is properly loaded
      if (typeof $.fn.select2 === 'function') {
        console.log("Select2 is properly loaded and ready to use");
        
        // Initialize Select2 on all elements with the select2 class
        try {
          $('select.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
          });
          
          // Special handling for modals
          $(document).on('shown.bs.modal', function() {
            $('select.select2').select2({
              theme: 'bootstrap-5',
              width: '100%',
              dropdownParent: $(this).closest('.modal')
            });
          });
        } catch (e) {
          console.error("Error initializing Select2:", e);
        }
      } else {
        console.error("Select2 is not loaded correctly!");
      }
    });
  </script>

  <!-- Bootstrap Validation Script -->
  <script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function() {
      'use strict'

      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      var forms = document.querySelectorAll('.needs-validation')

      // Loop over them and prevent submission
      Array.prototype.slice.call(forms)
        .forEach(function(form) {
          form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
              event.preventDefault()
              event.stopPropagation()
            }

            form.classList.add('was-validated')
          }, false)
        })
    })()
  </script>

  <!-- Stack for Additional Scripts -->
  @stack('scripts')
</body>

</html>
