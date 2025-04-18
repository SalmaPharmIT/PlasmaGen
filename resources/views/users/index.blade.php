@extends('include.dashboardLayout')

@section('title', 'View Users')

@section('content')

<div class="pagetitle">
    <h1>View Users</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
        <li class="breadcrumb-item active">View</li>
      </ol>
    </nav>

  </div><!-- End Page Title -->

  <section class="section">

    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
           
           <!-- Updated Header with Button -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title">View Users</h5>
                <a href="{{ route('user.register') }}" class="btn btn-primary">
                    <i class="bi bi-plus me-1"></i> Add User
                </a>
            </div>

                <!-- Display Success Message -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
    
                <!-- Display Error Messages -->
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-octagon me-1"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
    

            
            <!-- Entities DataTable -->
            <table id="entitiesTable" class="table table-striped table-bordered col-lg-12">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Entity</th>
                  <th>Role</th>
                  <th>Mobile No</th>
                  {{-- <th>Email</th> --}}
                  <th>Country</th>
                  <th>State</th>
                  <th>City</th>
                  {{-- <th>Logo</th> --}}
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                {{-- Data will be populated by DataTables via AJAX --}}
              </tbody>
            </table>
            <!-- End Entities DataTable -->

          </div>
        </div>

      </div>
    </div>
    
  </section>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#entitiesTable').DataTable({
                // DataTables options
                responsive: true,
                processing: true,
                serverSide: false, // Set to true if implementing server-side processing
                ajax: {
                    url: "{{ route('api.users') }}",
                    type: 'GET',
                    dataSrc: function(json) {
                        if(json.success) {
                            return json.data;
                        } else {
                            Swal.fire('Error', json.message, 'error');
                            return [];
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching entities:", error);
                        Swal.fire('Error', 'An error occurred while fetching the data.', 'error');
                    }
                },
                columns: [
                    { data: 'id' },
                    { data: 'name' },
                    { 
                        data: 'entity',
                        render: function(data, type, row) {
                            return data?.entity_name || 'N/A';
                        }
                    },
                    {  data: 'role',
                        render: function(data, type, row) {
                            return data?.role_name || 'N/A';
                        } },
                    { data: 'mobile', defaultContent: 'N/A' },
                    // { data: 'email', defaultContent: 'N/A' },
                    { 
                        data: 'country',
                        render: function(data, type, row) {
                            return data?.name || 'N/A';
                        }
                    },
                    { 
                        data: 'state',
                        render: function(data, type, row) {
                            return data?.name || 'N/A';
                        }
                    },
                    { 
                        data: 'city',
                        render: function(data, type, row) {
                            return data?.name || 'N/A';
                        }
                    },
                    // { 
                    //     data: 'profile_pic',
                    //     render: function(data, type, row) {
                    //         if(data) {
                    //             return '<img src="' + data + '" alt="profile_pic" width="50" height="50">';
                    //         } else {
                    //             return 'N/A';
                    //         }
                    //     },
                    //     orderable: false,
                    //     searchable: false
                    // },
                    { data: 'account_status', defaultContent: 'N/A' },
                    { 
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="actionMenu${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-gear"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="actionMenu${row.id}">
                                        <li><a class="dropdown-item" href="{{ route('user.edit', '') }}/${row.id}">Edit</a></li>
                                       
                                    </ul>
                                </div>
                            `;
                        },
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [[0, 'desc']], // Sort by the first column (ID) in descending order
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                language: {
                    // Customize language options if needed
                }
            });
        });
    </script>
@endpush
