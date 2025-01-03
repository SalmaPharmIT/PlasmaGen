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

    <!-- Tour Planner - Visible to Company Admin (role_id == 2) and Role ID 6 -->
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
        {{-- <li>
          <a href="#">
            <i class="bi bi-circle"></i><span>Manage Tour Planner</span>
          </a>
        </li> --}}
      </ul>
    </li><!-- End Forms Nav -->
    @endif

    
     <!-- Adding masters for admin -->
     @if (Auth::check() && in_array(Auth::user()->role_id, [1, 2]))
    <li class="nav-heading">Master Settings</li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="{{ route('citymaster.index') }}" >
        <i class="bi bi-geo-alt"></i>
        <span>City Master</span>
      </a>
    </li>
    @endif
 

   <!-- Add more sidebar items as needed -->

    


    <li class="nav-heading">Settings</li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="/user/{{ Auth::user()->id }}" >
        <i class="bi bi-person"></i>
        <span>Profile</span>
      </a>
    </li><!-- End Profile Page Nav -->

    <!-- Add more page links as needed -->

  </ul>

</aside><!-- End Sidebar-->
