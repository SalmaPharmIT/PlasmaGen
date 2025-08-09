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
     @if (Auth::check() && (Auth::user()->role_id == 2 || Auth::user()->role_id == 18))
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


       <!-- Registration Menu - Visible Only to Factory Admin (role_id == 12) -->
       @if (Auth::check() && Auth::user()->role_id == 12)
       <!-- Warehouse Section -->
       <li class="nav-heading" style="background-color: #0c4c90; color: #fff; padding: 13px 10px; border-radius: 4px;">Warehouse</li>
      <!-- PLASMA Menu Section -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#plasma-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-droplet"></i><span>Plasma Management</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="plasma-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{ route('plasma.entry') }}">
                <i class="bi bi-circle"></i><span>Plasma Inward Entry</span>
              </a>
            </li>
            <li>
              <a href="{{ route('plasma.generate-ar-no') }}">
                <i class="bi bi-circle"></i><span>AR.No Generation</span>
              </a>
            </li>
            <li>
              <a href="{{ route('plasma.dispensing') }}">
                <i class="bi bi-circle"></i><span>Plasma Despense Summary</span>
              </a>
            </li>
            <li>
              <a href="{{ route('plasma.rejection') }}">
                <i class="bi bi-circle"></i><span>Plasma Rejection Summary</span>
              </a>
            </li>
            <li>
              <a href="{{ route('plasma.ar-list') }}">
                <i class="bi bi-circle"></i><span>AR.No List</span>
              </a>
            </li>
            <li>
              <a href="{{ route('plasma.destruction-list') }}">
                <i class="bi bi-circle"></i><span>Destruction List</span>
              </a>
            </li>
            <li>
                <a href="{{ route('factory.report.plasma_despense') }}">
                  <i class="bi bi-circle"></i><span>Plasma Generate</span>
                </a>
              </li>
          </ul>
        </li><!-- End Plasma Management Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="{{ route('barcode.generate') }}">
            <i class="bi bi-upc-scan"></i>
            <span>Generate Barcode</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-nav-bag-entry" data-bs-toggle="collapse" href="#">
            <i class="bi bi-card-list"></i><span>Bag Entry</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-nav-bag-entry" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{ route('newBag.index') }}">
                <i class="bi bi-circle"></i><span>Manual Bag Entry</span>
              </a>
            </li>
            <li>
              <a href="#">
                <i class="bi bi-circle"></i><span>Mass File Upload Bag Entry</span>
              </a>
            </li>
            <li>
              <a href="{{ route('factory.newbagentry.sub_mini_pool_bag_entry') }}">
                <i class="bi bi-circle"></i><span>ELISA Sub Mini Pool Entry</span>
              </a>
            </li>
            <li>
              <a href="{{ route('factory.newbagentry.sub_mini_pool_bag_entry') }}">
                <i class="bi bi-circle"></i><span>NAT Re-test Mega Pool Entry</span>
              </a>
            </li>
             <li>
              <a href="{{ route('rejectPlasmaBagEntry') }}">
                <i class="bi bi-circle"></i><span>Generate Destruction No</span>
              </a>
            </li>
          </ul>
        </li><!-- End Components Nav -->
        <!-- PPT Section -->
        <li class="nav-heading" style="background-color: #0c4c90; color: #fff; padding: 13px 10px; border-radius: 4px;">PPT</li>
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#report-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-upload"></i><span>Log Report Upload</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="report-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{ route('report.upload', ['type' => 'elisa']) }}">
                <i class="bi bi-circle"></i><span>ELISA</span>
              </a>
            </li>
            <li>
              <a href="{{ route('nat-report.index') }}">
                <i class="bi bi-circle"></i><span>NAT</span>
              </a>
            </li>
            <li>
              <a href="{{ route('factory.report.sub_minipool_entry') }}">
                <i class="bi bi-circle"></i><span>Sub Mini Pool Entry</span>
              </a>
            </li>

            <li>
              <a href="{{ route('factory.report.plasma_rejection') }}">
                <i class="bi bi-circle"></i><span>Plasma Rejection</span>
              </a>
            </li>
            <li>
              <a href="{{ route('factory.report.plasma_release') }}">
                <i class="bi bi-circle"></i><span>Plasma Release</span>
              </a>
            </li>
          </ul>
        </li>
      <!-- Admin Section -->
       <li class="nav-heading" style="background-color: #0c4c90; color: #fff; padding: 13px 10px; border-radius: 4px;">Admin</li>
        <!-- Report Menu Section -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#report-menu-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-file-text"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="report-menu-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{ route('factory.generate_report.mega_pool_mini_pool') }}">
                <i class="bi bi-circle"></i><span>Mega Pool Mini Pool</span>
              </a>
            </li>
            <li>
              <a href="{{ route('factory.generate_report.tail_cutting') }}">
                <i class="bi bi-circle"></i><span>Tail Cutting Report</span>
              </a>
            </li>
            <li>
              <a href="{{ route('audit.index') }}">
                <i class="bi bi-circle"></i><span>Audit Trail Report</span>
              </a>
            </li>
            {{-- <li>
              <a href="{{ route('factory.generate_report.plasma_dispensing') }}">
                <i class="bi bi-circle"></i><span>Plasma Despense List</span>
              </a>
            </li> --}}
          </ul>
        </li><!-- End Report Menu Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-card-list"></i><span>User Management</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
           <li>
             <a href="{{ route('users.index') }}">
               <i class="bi bi-circle"></i><span>User Master</span>
             </a>
           </li>
           <li>
             <a href="#">
               <i class="bi bi-circle"></i><span>Role Master</span>
             </a>
           </li>
           <li>
             <a href="#">
               <i class="bi bi-circle"></i><span>Group Master</span>
             </a>
           </li>
          </ul>
        </li><!-- End Components Nav -->
        <li class="nav-item">
          <a class="nav-link collapsed" href="{{ route('entities.settings') }}">
            <i class="bi bi-gear"></i>
            <span>Entity Settings</span>
          </a>
        </li><!-- End Entity Settings Nav -->
        @endif
        @if (Auth::check() && Auth::user()->role_id == 16)
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#report-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-upload"></i><span>Log Report Upload</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="report-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{ route('report.upload', ['type' => 'elisa']) }}">
                <i class="bi bi-circle"></i><span>ELISA</span>
              </a>
            </li>
            <li>
              <a href="{{ route('nat-report.index') }}">
                <i class="bi bi-circle"></i><span>NAT</span>
              </a>
            </li>
            <li>
              <a href="{{ route('factory.report.sub_minipool_entry') }}">
                <i class="bi bi-circle"></i><span>Sub Mini Pool Entry</span>
              </a>
            </li>
            {{-- <li>
              <a href="{{ route('factory.report.plasma_despense') }}">
                <i class="bi bi-circle"></i><span>Plasma Despense</span>
              </a>
            </li> --}}
            <li>
              <a href="{{ route('factory.report.plasma_rejection') }}">
                <i class="bi bi-circle"></i><span>Plasma Rejection</span>
              </a>
            </li>
            <li>
                <a href="{{ route('factory.report.plasma_release') }}">
                  <i class="bi bi-circle"></i><span>Plasma Release</span>
                </a>
              </li>
          </ul>
        </li>

        @endif
        <!-- Registration Menu - Visible Only to Factory Admin (role_id == 12) -->
        @if (Auth::check() && Auth::user()->role_id == 17)
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#plasma-nav" data-bs-toggle="collapse" href="#">
              <i class="bi bi-droplet"></i><span>Plasma Management</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="plasma-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
              <li>
                <a href="{{ route('plasma.entry') }}">
                  <i class="bi bi-circle"></i><span>Plasma Inward Entry</span>
                </a>
              </li>
              <li>
                <a href="{{ route('plasma.generate-ar-no') }}">
                  <i class="bi bi-circle"></i><span>AR.No Generation</span>
                </a>
              </li>
              <li>
                <a href="{{ route('plasma.dispensing') }}">
                  <i class="bi bi-circle"></i><span>Plasma Despense Summary</span>
                </a>
              </li>
              <li>
                <a href="{{ route('plasma.rejection') }}">
                  <i class="bi bi-circle"></i><span>Plasma Rejection Summary</span>
                </a>
              </li>
              <li>
                <a href="{{ route('plasma.ar-list') }}">
                  <i class="bi bi-circle"></i><span>AR.No List</span>
                </a>
              </li>
              <li>
                <a href="{{ route('plasma.destruction-list') }}">
                  <i class="bi bi-circle"></i><span>Destruction List</span>
                </a>
              </li>
              <li>
                <a href="{{ route('factory.report.plasma_despense') }}">
                  <i class="bi bi-circle"></i><span>Plasma Generate</span>
                </a>
              </li>
            </ul>
        </li><!-- End Plasma Management Nav -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#components-nav-bag-entry" data-bs-toggle="collapse" href="#">
              <i class="bi bi-card-list"></i><span>Bag Entry</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components-nav-bag-entry" class="nav-content collapse " data-bs-parent="#sidebar-nav">
              <li>
                <a href="{{ route('newBag.index') }}">
                  <i class="bi bi-circle"></i><span>Manual Bag Entry</span>
                </a>
              </li>
              <li>
                <a href="#">
                  <i class="bi bi-circle"></i><span>Mass File Upload Bag Entry</span>
                </a>
              </li>
              <li>
                <a href="{{ route('factory.newbagentry.sub_mini_pool_bag_entry') }}">
                  <i class="bi bi-circle"></i><span>ELISA Sub Mini Pool Entry</span>
                </a>
              </li>
              <li>
                <a href="{{ route('factory.newbagentry.sub_mini_pool_bag_entry') }}">
                  <i class="bi bi-circle"></i><span>NAT Re-test Mega Pool Entry</span>
                </a>
              </li>
               <li>
                <a href="{{ route('rejectPlasmaBagEntry') }}">
                  <i class="bi bi-circle"></i><span>Generate Destruction No</span>
                </a>
              </li>
            </ul>
        </li><!-- End Components Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="{{ route('barcode.generate') }}">
            <i class="bi bi-upc-scan"></i>
            <span>Generate Barcode</span>
          </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#report-menu-nav" data-bs-toggle="collapse" href="#">
              <i class="bi bi-file-text"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="report-menu-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
              <li>
                <a href="{{ route('factory.generate_report.mega_pool_mini_pool') }}">
                  <i class="bi bi-circle"></i><span>Mega Pool Mini Pool</span>
                </a>
              </li>
            </ul>
        </li>
        @endif

    <!-- Tour Planner - Visible to Company Admin (role_id == 2) and Role ID 6 RBE-->
    @if (Auth::check() && in_array(Auth::user()->role_id, [2, 6, 18, 19]))
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
         <!-- Blood Bank Reports - Visible to Role ID 1 and 2 -->
         @if (in_array(Auth::user()->role_id, [1, 2, 6, 19]))
         <li>
          <a href="{{ route('tourplanner.finalDCR') }}">
            <i class="bi bi-circle"></i><span>DCR Approvals</span>
          </a>
        </li>
        @endif

        <li>
          <a href="{{ route('tourplanner.collectionIncomingRequests') }}">
            <i class="bi bi-circle"></i><span>TP Collection Requests</span>
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
      @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 18)
        <li class="nav-heading" style="background-color: #0c4c90; color: #fff; padding: 13px 10px; border-radius: 4px;">Reports</li>

        <!-- Reports - Visible to Role ID 1 and 2 -->
        @if (in_array(Auth::user()->role_id, [1, 2, 18]))
          <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('reports.reports_work_summary') }}" >
              <i class="bi bi-graph-up"></i>
              <span>Periodic Work Summary</span>
            </a>
          </li>
        @endif

        <!-- Blood Bank Reports - Visible to Role ID 1 and 2 -->
        @if (in_array(Auth::user()->role_id, [1, 2, 18]))
         <li class="nav-item">
           <a class="nav-link collapsed" href="{{ route('reports.blood_banks_summary') }}" >
             <i class="bi bi-droplet-half"></i>
             <span>Blood Banks</span>
           </a>
         </li>
        @endif


        <!-- Blood Bank Reports - Visible to Role ID 1 and 2 -->
        @if (in_array(Auth::user()->role_id, [1, 2, 18]))
         <li class="nav-item">
           <a class="nav-link collapsed" href="{{ route('reports.user_wise_collection_summary') }}" >
             <i class="bi bi-person-circle"></i>
             <span>User Collections</span>
           </a>
         </li>
        @endif

        <!-- Blood Bank Wise Colletion Reports - Visible to Role ID 1 and 2 -->
        @if (in_array(Auth::user()->role_id, [1, 2, 18]))
          <li class="nav-item">
           <a class="nav-link collapsed" href="{{ route('reports.bloodbank_wise_collection_summary') }}" >
             <i class="bi bi-geo-alt"></i>
             <span>Blood Bank / City Collections</span>
           </a>
          </li>
        @endif

        <!-- Tour Planner Datewise Reports - Visible to Role ID 1 and 2 -->
        @if (in_array(Auth::user()->role_id, [1, 2, 18]))
        <li class="nav-item">
          <a class="nav-link collapsed" href="{{ route('reports.tour_palnner_datewise_summary') }}" >
            <i class="bi bi-reception-4"></i>
            <span>Tour Planner Datewise</span>
          </a>
         </li>
       @endif

        <!-- User Expenses Reports - Visible to Role ID 1 and 2 -->
        @if (in_array(Auth::user()->role_id, [1, 2, 18]))
        <li class="nav-item">
          <a class="nav-link collapsed" href="{{ route('reports.user_expenses_summary') }}" >
            <i class="bi bi-currency-rupee"></i>
            <span>Uses Expenses</span>
          </a>
         </li>
       @endif

        <!-- DCR Reports - Visible to Role ID 1 and 2 -->
        @if (in_array(Auth::user()->role_id, [1, 2, 18]))
        <li class="nav-item">
          <a class="nav-link collapsed" href="{{ route('reports.dcr_summary') }}" >
            <i class="bi bi-currency-rupee"></i>
            <span>DCR Summary</span>
          </a>
         </li>
       @endif

      @endif
    @endif

    <!-- Adding masters for admin -->
    <!-- Master Settings - Visible to Admin Roles -->
    @if (Auth::check())
      @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 18)
        <li class="nav-heading" style="background-color: #0c4c90; color: #fff; padding: 13px 10px; border-radius: 4px;">Master Settings</li>

        <!-- City Master - Visible to Role ID 1 and 2 -->
        @if (in_array(Auth::user()->role_id, [1, 2, 18]))
          <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('citymaster.index') }}" >
              <i class="bi bi-geo-alt"></i>
              <span>City Master</span>
            </a>
          </li>
        @endif

        <!-- Entity Features - Visible Only to Role ID 2 -->
        @if (Auth::user()->role_id == 2 || Auth::user()->role_id == 18)
          <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('entities.features') }}" >
              <i class="bi bi-gear"></i>
              <span>Entity Features</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('users.reportMapping') }}" >
              <i class="bi bi-person-gear"></i>
              <span>User Report Mapping</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('users.workLocationMapping') }}" >
              <i class="bi bi-geo"></i>
              <span>Assign User Work Location</span>
            </a>
          </li>
        @endif
      @endif
    @endif

     <!-- Tour Planner - Add Tour Plan for Sourcing Agents-->
     @if (Auth::check() && in_array(Auth::user()->role_id, [8]))
     <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-journal-text"></i><span>Tour Planner</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{ route('tourplanner.sourcingCreateTourPlan') }}">
            <i class="bi bi-circle"></i><span>Create Tour Planner</span>
          </a>
        </li>
      </ul>
    </li><!-- End Forms Nav -->
    @endif

    <!-- Role ID 9 Collecting/Sourcing Agents-->
    @if (Auth::user()->role_id == 9 || Auth::user()->role_id == 6 || Auth::user()->role_id == 8 || Auth::user()->role_id == 19)
       <li class="nav-item">
         <a class="nav-link collapsed" href="{{ route('visits.index') }}" >
           <i class="bi bi-calendar2-week"></i>
           <span>Report Visits</span>
         </a>
       </li>
     @endif


    <!-- Role ID 9, 8, 68 Collecting/Sourcing Agents, Managers-->
    @if (Auth::user()->role_id == 9 || Auth::user()->role_id == 6 || Auth::user()->role_id == 8 || Auth::user()->role_id == 19)
      <!-- Add Expenses -->
      <li class="nav-heading" style="background-color: #0c4c90; color: #fff; padding: 13px 10px; border-radius: 4px;">Expenses</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('expenses.index') }}" >
          <i class="bi bi-currency-rupee"></i>
          <span>Add Expenses</span>
        </a>
      </li>
      <!-- End Add Expenses -->
    @endif

   <!-- Add more sidebar items as needed -->
   @if (Auth::check() && Auth::user()->role_id == 15)
   <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#report-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-upload"></i><span>Log Report Upload</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="report-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
      <li>
        <a href="{{ route('report.upload', ['type' => 'elisa']) }}">
          <i class="bi bi-circle"></i><span>ELISA</span>
        </a>
      </li>
      <li>
        <a href="{{ route('nat-report.index') }}">
          <i class="bi bi-circle"></i><span>NAT</span>
        </a>
      </li>
      <li>
        <a href="{{ route('factory.report.sub_minipool_entry') }}">
          <i class="bi bi-circle"></i><span>Sub Mini Pool Entry</span>
        </a>
      </li>
      <li>
        <a href="{{ route('factory.report.plasma_despense') }}">
          <i class="bi bi-circle"></i><span>Plasma Despense</span>
        </a>
      </li>
      <li>
        <a href="{{ route('factory.report.plasma_rejection') }}">
          <i class="bi bi-circle"></i><span>Plasma Rejection</span>
        </a>
      </li>
    </ul>
  </li>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#report-menu-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-file-text"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="report-menu-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{ route('factory.generate_report.mega_pool_mini_pool') }}">
            <i class="bi bi-circle"></i><span>Mega Pool Mini Pool</span>
          </a>
        </li>
        <li>
          <a href="{{ route('factory.generate_report.tail_cutting') }}">
            <i class="bi bi-circle"></i><span>Tail Cutting Report</span>
          </a>
        </li>

        {{-- <li>
          <a href="{{ route('factory.generate_report.plasma_dispensing') }}">
            <i class="bi bi-circle"></i><span>Plasma Despense List</span>
          </a>
        </li> --}}
      </ul>
    </li><!-- End Report Menu Nav -->
   @endif
    <li class="nav-heading" style="background-color: #0c4c90; color: #fff; padding: 13px 10px; border-radius: 4px;">Settings</li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="{{ route('user.edit', Auth::user()->id) }}" >
        <i class="bi bi-person"></i>
        <span>Profile</span>
      </a>
    </li><!-- End Profile Page Nav -->

    <!-- Add more page links as needed -->

  </ul>

</aside><!-- End Sidebar-->
