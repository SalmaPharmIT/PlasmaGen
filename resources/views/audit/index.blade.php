@extends('include.dashboardLayout')

@push('styles')
<style>
  /* Fix for Select2 dropdowns */
  .select2-dropdown {
    z-index: 9999 !important;
  }
  
  /* Full width container */
  .select2-container {
    width: 100% !important;
  }
  
  /* Increase dropdown height */
  .select2-results__options {
    max-height: 250px !important;
  }
  
  /* Override Bootstrap's dropdown handling */
  .dropdown-toggle::after {
    display: none !important;
  }
  
  /* Styles for native dropdown elements */
  select.form-control {
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
    padding-right: 2.5rem;
  }
  
  select.form-control:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
  }
  
  /* Increase dropdown height */
  select.form-control[size], 
  select.form-control[multiple] {
    height: auto;
  }
  
  /* Enhance dropdown appearance */
  select.form-control option {
    padding: 0.5rem;
  }
  
  select.form-control option:hover {
    background-color: #f8f9fa;
  }
  
  /* Compact Filter Styles */
  .compact-filters .card-body {
    padding: 0.75rem;
  }
  
  .compact-filters .form-label {
    margin-bottom: 0.25rem;
    font-size: 0.875rem;
    font-weight: 500;
  }
  
  .compact-filters .form-control {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    height: calc(1.5em + 0.5rem + 2px);
  }
  
  .compact-filters select.form-control {
    padding-right: 1.75rem;
    background-size: 12px 10px;
    background-position: right 0.5rem center;
  }
  
  .compact-filters .mb-3 {
    margin-bottom: 0.5rem !important;
  }
  
  .compact-filters .card-title {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    padding-bottom: 0.25rem;
    border-bottom: 1px solid #dee2e6;
  }
  
  .compact-filters .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
  }
  
  .compact-filters .row {
    margin-left: -0.25rem;
    margin-right: -0.25rem;
  }
  
  .compact-filters [class*="col-"] {
    padding-left: 0.25rem;
    padding-right: 0.25rem;
  }
</style>
@endpush

@section('content')
<div class="pagetitle">
  <h1>Audit Trail</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
      <li class="breadcrumb-item active">Audit Trail</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          {{-- <h5 class="card-title">Audit Trail Report</h5> --}}
          
          <!-- Filter Form -->
          <div class="row mb-3">
            <div class="col-md-12">
              <div class="card compact-filters">
                <div class="card-body">
                  <h5 class="card-title">Filters</h5>
                  <form id="filterForm">
                    <div class="row">
                      <div class="col-md-2 mb-2">
                        <label for="module" class="form-label">Module</label>
                        <select class="form-control" id="module" name="module">
                          <option value="">All Modules</option>
                          @foreach($modules as $module)
                            <option value="{{ $module }}">{{ $module }}</option>
                          @endforeach
                        </select>
                      </div>
                      
                      <div class="col-md-2 mb-2">
                        <label for="action" class="form-label">Action</label>
                        <select class="form-control" id="action" name="action">
                          <option value="">All Actions</option>
                          @foreach($actions as $action)
                            <option value="{{ $action }}">{{ ucfirst($action) }}</option>
                          @endforeach
                        </select>
                      </div>
                      
                      <div class="col-md-2 mb-2">
                        <label for="user_id" class="form-label">User</label>
                        <select class="form-control" id="user_id" name="user_id">
                          <option value="">All Users</option>
                          @foreach($users as $user)
                            <option value="{{ $user->user_id }}">{{ $user->user_name }}</option>
                          @endforeach
                        </select>
                      </div>
                      
                      <div class="col-md-2 mb-2">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from">
                      </div>
                      
                      <div class="col-md-2 mb-2">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to">
                      </div>
                      
                      <div class="col-md-2 mb-2">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Search...">
                      </div>
                    </div>
                    
                    <div class="row">
                      <div class="col-12 mt-2 text-end">
                        <button type="button" id="applyFilters" class="btn btn-primary btn-sm me-1">Apply</button>
                        <button type="button" id="resetFilters" class="btn btn-secondary btn-sm me-1">Reset</button>
                        <a href="{{ route('audit.export') }}" id="exportBtn" class="btn btn-success btn-sm">Export CSV</a>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Audit Trail Table -->
          <div class="table-responsive">
            <table class="table table-striped table-hover" id="auditTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User</th>
                  <th>Role</th>
                  <th>Module</th>
                  <th>Action</th>
                  <th>Record ID</th>
                  <th>Description</th>
                  <th>Date/Time</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <!-- Data will be loaded here via AJAX -->
                <tr>
                  <td colspan="9" class="text-center">Loading data...</td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Pagination -->
          <div id="pagination" class="mt-3">
            <!-- Pagination will be loaded here via AJAX -->
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script type="text/javascript">
  jQuery(document).ready(function($) {
    // Initialize DataTable
    var auditTable = $('#auditTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('audit.data') }}",
        data: function(d) {
          d.module = $('#module').val();
          d.action = $('#action').val();
          d.user_id = $('#user_id').val();
          d.search = $('#search').val();
          d.date_from = $('#date_from').val();
          d.date_to = $('#date_to').val();
        }
      },
      columns: [
        {data: 'id', name: 'id'},
        {data: 'user_name', name: 'user_name', render: function(data) {
          return data || 'System';
        }},
        {data: 'user_role', name: 'user_role', render: function(data) {
          return data || '-';
        }},
        {data: 'module', name: 'module', render: function(data) {
          return data || '-';
        }},
        {data: 'action', name: 'action', render: function(data) {
          return '<span class="badge bg-' + getActionBadgeColor(data) + '">' + ucfirst(data) + '</span>';
        }},
        {data: 'record_id', name: 'record_id', render: function(data) {
          return data || '-';
        }},
        {data: 'description', name: 'description', render: function(data) {
          return data || '-';
        }},
        {data: 'created_at', name: 'created_at', render: function(data) {
          return formatDateTime(data);
        }},
        {data: 'id', name: 'actions', orderable: false, searchable: false, render: function(data) {
          return '<a href="{{ url("audit-trail") }}/' + data + '" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> View</a>';
        }}
      ]
    });
    
    // Event listeners
    $('#applyFilters').on('click', function() {
      auditTable.ajax.reload();
    });
    
    $('#resetFilters').on('click', function() {
      $('#filterForm')[0].reset();
      auditTable.ajax.reload();
    });
    
    // Update export button URL with current filters
    $('#exportBtn').on('click', function(e) {
      e.preventDefault();
      var filterParams = $.param(getFilterParams());
      window.location.href = "{{ route('audit.export') }}?" + filterParams;
    });
    
    function getFilterParams() {
      return {
        module: $('#module').val(),
        action: $('#action').val(),
        user_id: $('#user_id').val(),
        search: $('#search').val(),
        date_from: $('#date_from').val(),
        date_to: $('#date_to').val()
      };
    }
    
    // Helper functions
    function ucfirst(str) {
      return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
    }
    
    function formatDateTime(dateTime) {
      if (!dateTime) return '-';
      var date = new Date(dateTime);
      return date.toLocaleString();
    }
    
    function getActionBadgeColor(action) {
      switch (action) {
        case 'create':
          return 'success';
        case 'update':
          return 'warning';
        case 'delete':
          return 'danger';
        case 'login':
          return 'info';
        case 'logout':
          return 'secondary';
        case 'test':
          return 'primary';
        case 'view':
          return 'info';
        default:
          return 'primary';
      }
    }
  });
</script>
@endpush 