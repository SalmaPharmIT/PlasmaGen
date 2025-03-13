@extends('include.dashboardLayout')

@section('title', 'Collection Requests')

@section('content')

<div class="pagetitle">
    <h1>Collection Requests</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('collectionrequest.index') }}">Collection Requests</a></li>
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
                  <th>Agent</th>
                  <th>Blood Bank</th>
                  <th>Visit Date</th>
                  <th>Visit Time</th>
                  <th>Quantity</th>
                  <th>Status</th>
                  <th>Pending Documents</th> 
                  <th>Remarks</th>
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

<!-- Vehicle Details Modal -->
<div class="modal fade" id="vehicleDetailsModal" tabindex="-1" aria-labelledby="vehicleDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="vehicleDetailsForm">
        @csrf <!-- CSRF Token for security -->
        <div class="modal-header">
          <h5 class="modal-title" id="vehicleDetailsModalLabel">Add Vehicle Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Hidden Field for Collection Request ID -->
          <input type="hidden" id="collectionRequestId" name="collection_request_id" value="">
          
          <!-- Vehicle Number -->
          <div class="mb-3">
            <label for="vehicleNumber" class="form-label">Vehicle Number</label>
            <input type="text" class="form-control" id="vehicleNumber" name="vehicle_number" required>
          </div>
          
          <!-- Driver Name -->
          <div class="mb-3">
            <label for="driverName" class="form-label">Driver Name</label>
            <input type="text" class="form-control" id="driverName" name="driver_name" required>
          </div>
          
          <!-- Contact Number -->
          <div class="mb-3">
            <label for="contactNumber" class="form-label">Contact Number</label>
            <input type="text" class="form-control" id="contactNumber" name="contact_number" required>
          </div>
          
          <!-- Email ID -->
          <div class="mb-3">
            <label for="emailId" class="form-label">Email ID</label>
            <input type="email" class="form-control" id="emailId" name="email_id">
          </div>
          
          <!-- Alternate Mobile No -->
          <div class="mb-3">
            <label for="alternateMobile" class="form-label">Alternate Mobile No</label>
            <input type="text" class="form-control" id="alternateMobile" name="alternate_mobile_no">
          </div>
          
          <!-- Remarks -->
          <div class="mb-3">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View Collection Request Modal -->
<div class="modal fade" id="viewCollectionRequestModal" tabindex="-1" aria-labelledby="viewCollectionRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Use modal-lg for a larger modal -->
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewCollectionRequestModalLabel">Collection Request Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Collection Request Details -->
          <div class="container-fluid">
            <div class="row mb-3">
              <div class="col-md-4"><strong>Collection Agent:</strong></div>
              <div class="col-md-8" id="viewCollectingAgentName">N/A</div>
            </div>
            <div class="row mb-3">
              <div class="col-md-4"><strong>Blood Bank:</strong></div>
              <div class="col-md-8" id="viewBloodBankName">N/A</div>
            </div>
            <div class="row mb-3">
              <div class="col-md-4"><strong>Visit Date:</strong></div>
              <div class="col-md-8" id="viewVisitDate">N/A</div>
            </div>
            <div class="row mb-3">
              <div class="col-md-4"><strong>Visit Time:</strong></div>
              <div class="col-md-8" id="viewVisitTime">N/A</div>
            </div>
            <div class="row mb-3">
              <div class="col-md-4"><strong>Quantity:</strong></div>
              <div class="col-md-8" id="viewQuantity">N/A</div>
            </div>
            <div class="row mb-3">
              <div class="col-md-4"><strong>Status:</strong></div>
              <div class="col-md-8" id="viewStatus">N/A</div>
            </div>
            <div class="row mb-3">
              <div class="col-md-4"><strong>Pending Documents:</strong></div>
              <div class="col-md-8" id="viewPendingDocuments">N/A</div>
            </div>
            <div class="row mb-3">
              <div class="col-md-4"><strong>Remarks:</strong></div>
              <div class="col-md-8" id="viewRemarks">N/A</div>
            </div>
            <!-- Add more fields as necessary -->
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <!-- Optionally, add more buttons like Edit if needed -->
        </div>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Ensure SweetAlert is included -->
    <script>
        // Declare table variable globally
        var table;

        // Function to open the Vehicle Details Modal
        function openVehicleDetailsModal(collectionRequestId) {
            // Set the hidden input value
            $('#collectionRequestId').val(collectionRequestId);
            
            // Clear previous form data
            $('#vehicleDetailsForm')[0].reset();
            
            // Show the modal
            var vehicleModal = new bootstrap.Modal(document.getElementById('vehicleDetailsModal'));
            vehicleModal.show();
        }

        // Function to view Collection Request Details
        function viewCollectionRequest(collectionRequestId) {
            console.log('viewCollectionRequest called with ID:', collectionRequestId);
            // Retrieve the row data using the row ID
            var rowData = table.row('#' + collectionRequestId).data();
            
            console.log('Retrieved rowData:', rowData);
            
            if(rowData) {
                // Populate the modal fields with the row data
                $('#viewCollectingAgentName').text(rowData.extendedProps.collecting_agent_name || 'N/A');
                $('#viewBloodBankName').text(rowData.extendedProps.blood_bank_name || 'N/A');
                $('#viewVisitDate').text(rowData.start ? new Date(rowData.start).toLocaleDateString() : 'N/A');
                $('#viewVisitTime').text(rowData.time ? rowData.time.substring(0,5) : 'N/A');
                $('#viewQuantity').text(rowData.extendedProps.quantity || 'N/A');
                $('#viewStatus').text(rowData.extendedProps.status ? capitalizeFirstLetter(rowData.extendedProps.status) : 'N/A');

                // Handle Pending Documents
                if(rowData.pending_document_names && rowData.pending_document_names.length > 0){
                    let badges = '';
                    rowData.pending_document_names.forEach(function(doc){
                        badges += `<span class="badge bg-info text-dark me-1">${doc}</span>`;
                    });
                    $('#viewPendingDocuments').html(badges);
                } else {
                    $('#viewPendingDocuments').text('N/A');
                }

                $('#viewRemarks').text(rowData.extendedProps.remarks || 'N/A');

                // Show the modal
                var viewModal = new bootstrap.Modal(document.getElementById('viewCollectionRequestModal'));
                viewModal.show();
            } else {
                Swal.fire('Error', 'Unable to retrieve collection request details.', 'error');
            }
        }

        // Helper function to capitalize the first letter
        function capitalizeFirstLetter(string) {
            if (!string) return '';
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        $(document).ready(function() {
            // Initialize DataTable and assign to the global 'table' variable
            table = $('#collectionRequestTable').DataTable({
                rowId: 'id', // Assign the 'id' from data as the row's ID
                responsive: true,
                processing: true,
                serverSide: false, // Set to true if implementing server-side processing
                ajax: {
                    url: "{{ route('collections.requests') }}",
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
                        console.error("Error fetching collection requests:", error);
                        Swal.fire('Error', 'An error occurred while fetching the data.', 'error');
                    }
                },
                columns: [
                    { 
                        data: 'extendedProps.collecting_agent_name',
                        title: 'Agent',
                        render: function(data, type, row) {
                            return data ? data : 'N/A';
                        }
                    },
                    { 
                        data: 'extendedProps.blood_bank_name',
                        title: 'Blood Bank',
                        render: function(data, type, row) {
                            return data ? data : 'N/A';
                        }
                    },
                    { 
                        data: 'start',
                        title: 'Visit Date',
                        render: function(data, type, row) {
                            return data ? new Date(data).toLocaleDateString() : 'N/A';
                        }
                    },
                    { 
                        data: 'time',
                        title: 'Visit Time',
                        render: function(data, type, row) {
                            return data ? data.substring(0,5) : 'N/A';
                        }
                    },
                    { 
                        data: 'extendedProps.quantity',
                        title: 'Quantity',
                        defaultContent: 'N/A',
                        render: function(data, type, row) {
                            return data ? data : 'N/A';
                        }
                    },
                    { 
                        data: 'extendedProps.status',
                        title: 'Status',
                        defaultContent: 'N/A',
                        render: function(data, type, row) {
                           // return data ? data.charAt(0).toUpperCase() + data.slice(1) : 'N/A';
                           if (data) {
                              if (data.toLowerCase() === 'initiated') {
                                  return 'TP added';
                              } else {
                                  return data.charAt(0).toUpperCase() + data.slice(1);
                              }
                          } else {
                              return 'N/A';
                          }
                        }
                    },
                    { 
                        data: 'pending_document_names',
                        title: 'Pending Documents',
                        render: function(data, type, row) {
                            if(data && data.length > 0){
                                var badges = '';
                                data.forEach(function(doc){
                                    badges += `<span class="badge bg-info text-dark me-1">${doc}</span>`;
                                });
                                return badges;
                            } else {
                                return 'N/A';
                            }
                        }
                    },
                    { 
                        data: 'extendedProps.remarks',
                        title: 'Remarks',
                        defaultContent: 'N/A',
                        render: function(data, type, row) {
                            return data ? data : 'N/A';
                        }
                    },
                    { 
                        data: null,
                        title: 'Actions',
                        render: function(data, type, row) {
                            return `
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="actionMenu${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-gear"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="actionMenu${row.id}">
                                        <li><a class="dropdown-item" href="#" onclick="openVehicleDetailsModal(${row.id})">Add Vehicle Details</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="viewCollectionRequest(${row.id})">View</a></li>
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

            // Handle form submission for Vehicle Details
            $('#vehicleDetailsForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                // Serialize form data
                var formData = $(this).serialize();

                // Disable the submit button to prevent multiple submissions
                $('#vehicleDetailsForm button[type="submit"]').prop('disabled', true);

                // Send AJAX request
                $.ajax({
                    url: "{{ route('collections.submitVehicleDetails') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if(response.success){
                            Swal.fire('Success', response.message, 'success');
                            // Hide the modal
                            var vehicleModalEl = document.getElementById('vehicleDetailsModal');
                            var vehicleModal = bootstrap.Modal.getInstance(vehicleModalEl);
                            vehicleModal.hide();

                            // Reload the DataTable to reflect any changes
                            table.ajax.reload();
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error submitting vehicle details:", error);
                        var errorMessage = 'An error occurred while submitting the vehicle details.';
                        if(xhr.responseJSON && xhr.responseJSON.message){
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', errorMessage, 'error');
                    },
                    complete: function() {
                        // Re-enable the submit button
                        $('#vehicleDetailsForm button[type="submit"]').prop('disabled', false);
                    }
                });
            });
        });

    </script>
@endpush
