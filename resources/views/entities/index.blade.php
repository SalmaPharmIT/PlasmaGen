@extends('include.dashboardLayout')

@section('title', 'View Entities')

@section('content')

<div class="pagetitle">
    <h1>View Entities</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('entities.index') }}">Entity</a></li>
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
                <h5 class="card-title">View Entities</h5>
                <a href="{{ route('entity.register') }}" class="btn btn-primary">
                    <i class="bi bi-plus me-1"></i> Add Entity
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
                  <th>Entity Type</th>
                  <th>Mobile No</th>
                  <th>Email</th>
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
                    url: "{{ route('api.entities') }}",
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
                        data: 'entityType',
                        render: function(data, type, row) {
                            return data?.entity_name || 'N/A';
                        }
                    },
                    { data: 'mobile_no', defaultContent: 'N/A' },
                    { data: 'email', defaultContent: 'N/A' },
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
                    //     data: 'logo',
                    //     render: function(data, type, row) {
                    //         if(data) {
                    //             return '<img src="' + data + '" alt="Logo" width="50" height="50">';
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
                                        <li><a class="dropdown-item" href="{{ route('bloodbank.edit', '') }}/${row.id}">Edit</a></li>
                                       
                                    </ul>
                                </div>
                            `;
                        },
                        orderable: false,
                        searchable: false
                    }
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                language: {
                    // Customize language options if needed
                }
            });

            // // Handle Delete Action using Event Delegation
            // <li><a class="dropdown-item delete-entity" href="#" data-id="${row.id}">Delete</a></li>  // this line included in ui part
            // $('#entitiesTable tbody').on('click', 'a.delete-entity', function(e) {
            //     e.preventDefault();
            //     var entityId = $(this).data('id');

            //     Swal.fire({
            //         title: 'Are you sure?',
            //         text: "Do you really want to delete this entity?",
            //         icon: 'warning',
            //         showCancelButton: true,
            //         confirmButtonColor: '#d33', // Red color for delete
            //         cancelButtonColor: '#3085d6', // Blue color for cancel
            //         confirmButtonText: 'Yes, delete it!'
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             // Send AJAX request to delete the entity
            //             $.ajax({
            //                 url: `/entities/${entityId}`,
            //                 method: 'POST',
            //                 headers: {
            //                     'X-CSRF-TOKEN': '{{ csrf_token() }}',
            //                     'Accept': 'application/json',
            //                 },
            //                 data: {
            //                     _method: 'DELETE'
            //                 },
            //                 success: function(data) {
            //                     if(data.success) {
            //                         Swal.fire(
            //                             'Deleted!',
            //                             'Entity has been deleted.',
            //                             'success'
            //                         );
            //                         // Remove the deleted row from the table
            //                         table.row($(e.target).parents('tr')).remove().draw();
            //                     } else {
            //                         Swal.fire(
            //                             'Error!',
            //                             data.message || 'An error occurred.',
            //                             'error'
            //                         );
            //                     }
            //                 },
            //                 error: function(xhr, status, error) {
            //                     console.error("Error deleting entity:", error);
            //                     Swal.fire(
            //                         'Error!',
            //                         'An error occurred while deleting the entity.',
            //                         'error'
            //                     );
            //                 }
            //             });
            //         }
            //     });
            // });
        });
    </script>
@endpush
