@extends('include.dashboardLayout')

@section('title', 'Manage Collections')

@section('content')

<div class="pagetitle">
    <h1>View All Collections</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('collections.manage') }}">Manage Collections</a></li>
        <li class="breadcrumb-item active">View</li>
      </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">

            <!-- Filters Row -->
            <div class="row mb-4 mt-2 align-items-end">
              <!-- Collecting Agent Dropdown -->
              <div class="col-md-4">
                  <label for="collectingAgentDropdown" class="form-label">Collecting Agent</label>
                  <select id="collectingAgentDropdown" class="form-select select2">
                      <option value="">Choose Collecting Agent</option>
                      <!-- Options will be populated via AJAX -->
                  </select>
              </div>

              <!-- Month Picker -->
              <div class="col-md-4">
                  <label for="monthPicker" class="form-label">Select Month</label>
                  <input type="month" id="monthPicker" class="form-control" value="{{ date('Y-m') }}"/>
              </div>

              <!-- Submit and Reset Buttons -->
              <div class="col-md-4 d-flex align-items-end">
                  <button id="filterButton" class="btn btn-success me-2">
                      <i class="bi bi-filter me-1"></i> Submit
                  </button>
                  <button id="resetFilterButton" class="btn btn-secondary">
                      <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                  </button>
              </div>
            </div>
            <!-- End Filters Row -->
           
         
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
                  <th>ID</th>
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
            <input type="email" class="form-control" id="emailId" name="email_id" required>
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

            <!-- Transport Details Section -->
            <hr />
            <h5 class="card-title">Transport Details</h5> 

            <div class="row mb-3">
              <div class="col-md-4"><strong>Vehicle Number:</strong></div>
              <div class="col-md-8" id="viewVehicleNumber">N/A</div>
            </div>
            <div class="row mb-3">
              <div class="col-md-4"><strong>Driver Name:</strong></div>
              <div class="col-md-8" id="viewDriverName">N/A</div>
            </div>
            <div class="row mb-3">
              <div class="col-md-4"><strong>Contact Number:</strong></div>
              <div class="col-md-8" id="viewContactNumber">N/A</div>
            </div>
            <div class="row mb-3">
              <div class="col-md-4"><strong>Alternate Contact Number:</strong></div>
              <div class="col-md-8" id="viewAlternativeContactNumber">N/A</div>
            </div>
            <div class="row mb-3">
              <div class="col-md-4"><strong>Email:</strong></div>
              <div class="col-md-8" id="viewEmail">N/A</div>
            </div>
            <div class="row mb-3">
              <div class="col-md-4"><strong>Transport Remarks:</strong></div>
              <div class="col-md-8" id="viewTransportRemarks">N/A</div>
            </div>
            <div class="row mb-3">
              <div class="col-md-4"><strong>Added On:</strong></div>
              <div class="col-md-8" id="viewTransportCreatedAt">N/A</div>
            </div>
            <!-- End Transport Details Section -->

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
                $('#viewCollectingAgentName').text(rowData.extendedProps.collecting_agent_name || '-');
                $('#viewBloodBankName').text(rowData.extendedProps.blood_bank_name || '-');
                $('#viewVisitDate').text(rowData.start ? new Date(rowData.start).toLocaleDateString() : '-');
                $('#viewVisitTime').text(rowData.time ? rowData.time.substring(0,5) : '-');
                $('#viewQuantity').text(rowData.extendedProps.quantity || '-');
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

                $('#viewRemarks').text(rowData.extendedProps.remarks || '-');

                // Handle Transport Details
                if(rowData.extendedProps.transport_details) {
                    var transport = rowData.extendedProps.transport_details;
                    $('#viewVehicleNumber').text(transport.vehicle_number || '-');
                    $('#viewDriverName').text(transport.driver_name || '-');
                    $('#viewContactNumber').text(transport.contact_number || '-');
                    $('#viewAlternativeContactNumber').text(transport.alternative_contact_number || '-');
                    $('#viewEmail').text(transport.email || '-');
                    $('#viewTransportRemarks').text(transport.remarks || '-');
                    $('#viewTransportCreatedAt').text(transport.created_at ? new Date(transport.created_at).toLocaleString() : 'N/A');
                } else {
                    // If no transport details, set all transport fields to 'N/A'
                    $('#viewVehicleNumber').text('N/A');
                    $('#viewDriverName').text('N/A');
                    $('#viewContactNumber').text('N/A');
                    $('#viewAlternativeContactNumber').text('N/A');
                    $('#viewEmail').text('N/A');
                    $('#viewTransportRemarks').text('N/A');
                    $('#viewTransportCreatedAt').text('N/A');
                }

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
                    url: "{{ route('collections.submitted') }}",
                    type: 'GET',
                    data: function(d) {
                        // Append filter parameters to the AJAX request
                        d.agent_id = $('#collectingAgentDropdown').val();
                        d.month = $('#monthPicker').val();
                    },
                    dataSrc: function(json) {
                      console.log('submitted', json);
                        if(json.success) {
                            return json.data;
                        } else {
                            Swal.fire('Error', json.message, 'error');
                            return [];
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching collection submitted:", error);
                        Swal.fire('Error', 'An error occurred while fetching the data.', 'error');
                    }
                },
                columns: [
                    { data: 'id' },
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
                          if (data) {
                              if (data.toLowerCase() === 'submitted') {
                                  return 'Vehicle details added';
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
                                        <li><a class="dropdown-item" href="#" onclick="viewCollectionRequest(${row.id})">View</a></li>
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


                // Function to populate Collecting Agents Dropdown
                function loadCollectingAgents() {
                $.ajax({
                    url: "{{ route('tourplanner.getCollectingAgents') }}",
                    type: 'GET',
                    success: function(response) {
                        if(response.success) {
                            var agents = response.data;
                            var dropdown = $('#collectingAgentDropdown');
                            var modalDropdown = $('#tourPlanCollectingAgent');
                            dropdown.empty().append('<option value="">Choose Collecting Agent</option>');
                            modalDropdown.empty().append('<option value="">Choose Collecting Agent</option>');
                            $.each(agents, function(index, agent) {
                                var option = '<option value="' + agent.id + '">' + agent.name + '</option>';
                                dropdown.append(option);
                                modalDropdown.append(option);
                            });
                            // Trigger Select2 to reinitialize with new options
                            dropdown.trigger('change');
                            modalDropdown.trigger('change');
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching collecting agents:", error);
                        Swal.fire('Error', 'An error occurred while fetching collecting agents.', 'error');
                    }
                });
            }

            // Load Collecting Agents on page load
            loadCollectingAgents();

            // Event listener for the Submit Filter button
            $('#filterButton').on('click', function() {
                table.ajax.reload();
            });

            // Event listener for the Reset Filter button
            $('#resetFilterButton').on('click', function() {
                // Clear the filter inputs
                $('#collectingAgentDropdown').val('').trigger('change');
                $('#monthPicker').val('{{ date('Y-m') }}');
                // Reload the DataTable without filters
                table.ajax.reload();
            });
        });

    </script>
@endpush
