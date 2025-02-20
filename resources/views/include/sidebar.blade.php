<!-- resources/views/include/sidebar.blade.php -->

<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('dashboard') ? '' : 'collapsed' }}" href="{{ route('dashboard') }}">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li><!-- End Dashboard Nav -->

     <!-- Registration Menu - Visible Only to Super Admin (role_id == 1) -->
     @if (Auth::check() && Auth::user()->role_id == 1)
     <li class="nav-item">
       <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
         <i class="bi bi-card-list"></i><span>Registration</span><i class="bi bi-chevron-down ms-auto"></i>
       </a>
       <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
         {{-- <li>
           <a href="{{ route('entity.register') }}">
             <i class="bi bi-circle"></i><span>Add Entity</span>
           </a>
         </li> --}}
         <li>
           <a href="{{ route('entities.index') }}">
             <i class="bi bi-circle"></i><span>Entities</span>
           </a>
         </li>
         <li>
          <a href="{{ route('users.index') }}">
            <i class="bi bi-circle"></i><span>Users</span>
          </a>
        </li>
         <!-- Add more component links as needed -->
       </ul>
     </li><!-- End Components Nav -->
   @endif


     <!-- Registration Menu - Visible Only to Comapny Admin (role_id == 2) -->
     @if (Auth::check() && Auth::user()->role_id == 2)
     <li class="nav-item">
       <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
         <i class="bi bi-card-list"></i><span>Onboard</span><i class="bi bi-chevron-down ms-auto"></i>
       </a>
       <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{ route('users.index') }}">
            <i class="bi bi-circle"></i><span>Users</span>
          </a>
        </li>
        <li>
          <a href="{{ route('bloodbank.index') }}">
            <i class="bi bi-circle"></i><span>Blood Banks</span>
          </a>
        </li>
        <li>
          <a href="{{ route('warehouse.index') }}">
            <i class="bi bi-circle"></i><span>Warehouses</span>
          </a>
        </li>
         <!-- Add more component links as needed -->
       </ul>
     </li><!-- End Components Nav -->
     @endif

    <!-- Tour Planner - Visible to Company Admin (role_id == 2) and Role ID 6 RBE-->
    @if (Auth::check() && in_array(Auth::user()->role_id, [2, 6]))
     <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-journal-text"></i><span>Tour Planner</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{ route('tourplanner.index') }}">
            <i class="bi bi-circle"></i><span>Create Tour Planner</span>
          </a>
        </li>
        <li>
          <a href="{{ route('tourplanner.manage') }}">
            <i class="bi bi-circle"></i><span>Manage Tour Planner</span>
          </a>
        </li>
        <li>
          <a href="{{ route('tourplanner.finalDCR') }}">
            <i class="bi bi-circle"></i><span>DCR Approvals</span>
          </a>
        </li>
      </ul>
    </li><!-- End Forms Nav -->
    @endif

       <!-- Tour Planner - Visible to Logistics Admin (role_id == 7) and Role ID 6 -->
       @if (Auth::check() && in_array(Auth::user()->role_id, [7]))
       <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>TP Collections</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
         
          <li>
            <a href="{{ route('collectionrequest.index') }}">
              <i class="bi bi-circle"></i><span>Collection Requests</span>
            </a>
          </li>
          <li>
            <a href="{{ route('collections.manage') }}">
              <i class="bi bi-circle"></i><span>Manage Collections</span>
            </a>
          </li>
        </ul>
      </li><!-- End Forms Nav -->
      @endif


    
      <!-- Reports - Visible to Admin & Manager Roles -->
      @if (Auth::check())
      @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
        <li class="nav-heading">Reports</li>

        <!-- Reports - Visible to Role ID 1 and 2 -->
        @if (in_array(Auth::user()->role_id, [1, 2]))
          <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('reports.reports_work_summary') }}" >
              <i class="bi bi-geo-alt"></i>
              <span>Periodic Work Summary</span>
            </a>
          </li>
        @endif
      @endif
    @endif

    
    <!-- Adding masters for admin -->
    <!-- Master Settings - Visible to Admin Roles -->
    @if (Auth::check())
      @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
        <li class="nav-heading">Master Settings</li>

        <!-- City Master - Visible to Role ID 1 and 2 -->
        @if (in_array(Auth::user()->role_id, [1, 2]))
          <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('citymaster.index') }}" >
              <i class="bi bi-geo-alt"></i>
              <span>City Master</span>
            </a>
          </li>
        @endif

        <!-- Entity Features - Visible Only to Role ID 2 -->
        @if (Auth::user()->role_id == 2)
          <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('entities.features') }}" >
              <i class="bi bi-gear"></i>
              <span>Entity Features</span>
            </a>
          </li>
        @endif
      @endif
    @endif


     <!-- Role ID 9 Collecting/Sourcing Agents-->
     @if (Auth::user()->role_id == 9)
     <li class="nav-item">
       <a class="nav-link collapsed" href="{{ route('visits.index') }}" >
         <i class="bi bi-calendar2-week"></i>
         <span>Report Visits</span>
       </a>
     </li>
   @endif
 

   <!-- Add more sidebar items as needed -->

    <li class="nav-heading">Settings</li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="{{ route('user.edit', Auth::user()->id) }}" >
        <i class="bi bi-person"></i>
        <span>Profile</span>
      </a>
    </li><!-- End Profile Page Nav -->

    <!-- Add more page links as needed -->

  </ul>

</aside><!-- End Sidebar-->
