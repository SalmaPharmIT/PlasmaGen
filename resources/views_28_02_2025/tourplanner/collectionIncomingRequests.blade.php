@extends('include.dashboardLayout')

@section('title', 'Collection Incoming Requests')

@section('content')

<div class="pagetitle">
    <h1>Tour Plan Collection Requests</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('tourplanner.collectionIncomingRequests') }}">Tour Plan Collection Requests</a></li>
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
                <h5 class="card-title">View Collection Requests</h5>
                {{-- Uncomment and update the route if you have a button for adding new tour plans
                <a href="{{ route('tourplan.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus me-1"></i> Add Tour Plan
                </a>
                --}}
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

            <!-- Collection Requests DataTable -->
            <table id="collectionRequestTable" class="table table-striped table-bordered col-lg-12">
              <thead>
                <tr>
                  <th>SI. No.</th>
                  <th>Agent</th>
                  <th>Visit Date</th>
                  <th>Visit City</th>
                  <th>Edit Request</th>
                  <th>Comments</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                {{-- Data will be populated by DataTables via AJAX --}}
              </tbody>
            </table>
            <!-- End Collection Requests DataTable -->

          </div>
        </div>
      </div>
    </div>
</section>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Ensure SweetAlert is included -->
    <script>
        // Declare table variable globally
        var table;

        // Helper function to format date string
        function formatDate(dateStr) {
            if (!dateStr) return 'N/A';
            return new Date(dateStr).toLocaleDateString();
        }

        // Function to mark TP as added using AJAX after confirmation
        function markTPAdded(tp_id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you have added TP to that Agent?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, TP Added',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Call the controller endpoint via AJAX.
                    // Replace the URL with your actual endpoint route.
                    $.ajax({
                        url: "{{ route('tourplanner.markTPAdded') }}",
                        type: 'GET',
                        data: { tp_id: tp_id },
                        success: function(response) {
                            if(response.success) {
                                Swal.fire('Success', response.message, 'success');
                                // Optionally, refresh the table or update row status
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Error', 'An error occurred while updating the TP status.', 'error');
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            // Initialize DataTable and assign to the global 'table' variable
            table = $('#collectionRequestTable').DataTable({
                rowId: 'tour_plan_id', // Use tour_plan_id as the row identifier
                responsive: true,
                processing: true,
                serverSide: false, // Set to true if implementing server-side processing
                ajax: {
                    url: "{{ route('tourplanner.tpCollectionRequests') }}",
                    type: 'GET',
                    dataSrc: function(json) {
                        if(json.success) {
                            console.log("tpCollectionRequests: ", json.data);
                            return json.data;
                        } else {
                            Swal.fire('Error', json.message, 'error');
                            return [];
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching collection requests:", error);
                        Swal.fire('Error', 'An error occurred while fetching the data.', 'error');
                    }
                },
                columns: [
                    {
                        data: null,
                        title: 'SI. No.',
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        },
                        orderable: false
                    },
                    { 
                        data: 'name',
                        title: 'Agent',
                        render: function(data, type, row) {
                            return data ? data : 'N/A';
                        }
                    },
                    { 
                        data: 'visit_date',
                        title: 'Visit Date',
                        render: function(data, type, row) {
                            return data ? formatDate(data) : 'N/A';
                        }
                    },
                    { 
                        data: 'city_name',
                        title: 'City',
                        render: function(data, type, row) {
                            return data ? data : 'N/A';
                        }
                    },
                    { 
                        data: 'tp_collection_request',
                        title: 'Edit Request',
                        render: function(data, type, row) {
                            return data == 1 ? 'Requested' : 'Not Requested';
                        }
                    },
                    { 
                        data: 'tp_collection_request_message',
                        title: 'Comments',
                        render: function(data, type, row) {
                            return data ? data : 'N/A';
                        }
                    },
                    { 
                        data: null,
                        title: 'Actions',
                        render: function(data, type, row) {
                            return `<button class="btn btn-success btn-sm" onclick="markTPAdded(${row.tour_plan_id})">Update TP Status</button>`;
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
        });
    </script>
@endpush
