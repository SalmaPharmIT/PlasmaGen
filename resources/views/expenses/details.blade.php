@extends('include.dashboardLayout')

@section('title', 'View Expenses')

@section('content')

<div class="pagetitle d-flex justify-content-between align-items-center">
    <div>
        <h1>View Expense Details</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('expenses.clearance') }}">Expenses</a></li>
            <li class="breadcrumb-item active">View</li>
          </ol>
        </nav>
    </div>
    <div>
    </div>
  </div>

<section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">

              <div class="row">
                <div class="col-md-12">

                       <!-- Display Success Message -->
                      @if(session('success'))
                      <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                          <i class="bi bi-check-circle me-1"></i>
                          {{ session('success') }}
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                      @endif
          
        
                    <!-- Display Error Messages -->
                      @if($errors->any())
                      <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mt-2" role="alert">
                          <i class="bi bi-exclamation-octagon me-2"></i> <!-- Error icon -->
                          <ul class="mb-0">
                              @foreach($errors->all() as $error)
                                  <li>{{ $error }}</li>
                              @endforeach
                          </ul>
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                      @endif

          
                     <!-- Displaying Blood Bank Name and Visit Date -->
                     <div class="row">
                      <div class="col-md-6 mt-2">
                          <strong>Blood Bank Name:</strong>
                          <p id="bloodBankName">Loading...</p> <!-- This will be dynamically updated -->
                      </div>
                      <div class="col-md-6 mt-2">
                          <strong>Visit Date:</strong>
                          <p id="visitDate">Loading...</p> <!-- This will be dynamically updated -->
                      </div>
                  </div>
                </div>

                <!-- Modified By (Hidden or Pre-filled) -->
                <div class="col-md-12">
                    <input type="hidden" name="tour_plan_id" id="tour_plan_id" value="{{ request()->query('dcr_id') }}">
                    <input type="hidden" name="visit_date_temp" id="visit_date_temp" value="{{ request()->query('date') }}">
                </div>

                {{-- <div class="col-md-12">
                     <!-- Move status display here -->
                  
                    <div id="statusDisplay" class="ms-4 mt-2">
                        <span id="expenseMGRStatusDisplay" class="bg-warning p-2 rounded text-white fw-bold me-2"></span>
                        <span id="expenseCAStatusDisplay" class="bg-info p-2 rounded text-white fw-bold"></span>
                    </div>
                    <br/>
                      <span class="ms-4 mt-2" id="remarksDisplay">Remarks</span>
                </div>

                <div class="d-flex align-items-center mt-3">
                    <div>
                        <form method="POST" action="{{ route('expenses.dcr.updateStatus',['id' => 'PLACEHOLDER']) }}" id="statusForm">
                            @csrf
                            <button type="submit" name="status" value="cleared" id="clearBtn" class="btn btn-success me-3">Cleared</button>
                            <button type="submit" name="status" value="rejected" id="rejectBtn" class="btn btn-danger">Rejected</button>
                        
                        </form>
                    </div>
                </div> --}}

                <div class="col-md-12 mt-3">
                    <div class="card border shadow-sm">
                        <div class="card-body py-3">

                            <h4 class="mb-3 d-flex align-items-center">
                                <strong class="me-2">Travel Information</strong>
                                <i class="bi bi-info-circle-fill text-primary"
                                id="travelInfoIcon"
                                style="cursor:pointer;font-size:18px;"
                                title="View Check-in / Location Info"></i>
                            </h4>

                            {{-- Display-only row --}}
                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <strong>Travel Mode:</strong>
                                    <span id="travelModeDisplay">N/A</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>KM Travelled:</strong>
                                    <span id="kmTravelledDisplay">N/A</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Remarks:</strong>
                                    <span id="travelRemarksDisplay">N/A</span>
                                </div>
                            </div>

                            {{-- KM limit & price --}}
                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <strong>KM Assigned:</strong>
                                    <span id="kmAssignedDisplay">N/A</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Price Per Kilometer:</strong>
                                    <span id="pricePerKmDisplay">N/A</span>
                                </div>
                            </div>

                            {{-- Approved values --}}
                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <strong>Approved KM:</strong>
                                    <span id="approvedKmDisplay">N/A</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Approved Price:</strong>
                                    <span id="approvedPriceDisplay">N/A</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Approved Remarks:</strong>
                                    <span id="approvedRemarksDisplay">N/A</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-12 mt-3">
                    <div class="card border shadow-sm">
                        <div class="card-body py-3">

                            <h4 class="mb-3"><strong>Update Travel Details</strong></h4>

                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label mb-1"><strong>Approved KM Travelled</strong></label>
                                    <input type="number"
                                        step="0.01"
                                        class="form-control"
                                        id="approvedKmTravelled"
                                        name="approved_km_travel">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label mb-1"><strong>Approved Total Price</strong></label>
                                    <input type="number"
                                        step="0.01"
                                        class="form-control"
                                        id="approvedTravelCost"
                                        name="approved_travel_cost">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label mb-1"><strong>Approved Travel Remarks</strong></label>
                                    <input type="text"
                                        class="form-control"
                                        id="approvedTravelRemarks"
                                        name="approved_travel_remarks">
                                </div>

                            </div>

                        </div>
                    </div>
                </div>



                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-start mt-3">

                        <!-- LEFT: status & remarks -->
                        <div>
                        <div id="statusDisplay" class="mb-1">
                            <span id="expenseMGRStatusDisplay" class="bg-warning p-2 rounded text-white fw-bold me-2"></span>
                            <span id="expenseCAStatusDisplay"  class="bg-info    p-2 rounded text-white fw-bold"></span>
                        </div>
                        <div id="remarksDisplay" class="text-muted">
                            {{-- JS will fill this --}}
                        </div>
                        </div>

                        <!-- RIGHT: buttons -->
                        <div>
                        <form method="POST" action="{{ route('expenses.dcr.updateStatus',['id' => 'PLACEHOLDER']) }}" id="statusForm" class="d-flex">
                            @csrf
                            <button type="submit" name="status" value="cleared"  id="clearBtn"  class="btn btn-success me-2">Cleared</button>
                            <button type="submit" name="status" value="rejected" id="rejectBtn" class="btn btn-danger">Rejected</button>
                        </form>
                        </div>

                    </div>
                </div>
            </div>
           
          </div>
        </div>
      </div>

      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">

            <div class="row">
              <div class="col-12">
                  <h5 class="card-title">View Expenses</h5>

                  <div class="table-responsive">
                    <table id="expensesTable" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>SI. No.</th> <!-- New column for serial number -->
                            <th>Date</th>
                            <th>Description</th>
                            <th>Food</th>
                            <th>Conveyance</th>
                            <th>Tel/Fax</th>
                            <th>Lodging</th>
                            <th>Sundry</th>
                            <th>Total Price</th>
                            <th>Remarks</th>
                            {{-- <th>Attachments</th> <!-- New column for attachments --> --}}
                              {{-- New attachment columns --}}
                            <th>Food Attachments</th>
                            <th>Conveyance Attachments</th>
                            <th>Tel/Fax Attachments</th>
                            <th>Lodging Attachments</th>
                            <th>Sundry Attachments</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody id="expensesList">
                        <!-- Expenses will be dynamically added here -->
                        </tbody>
                    </table>
                  </div>
        
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>



@endsection


@push('styles')
<style>
    /* Style for existing document previews */
    .existing-preview-item {
        width: 100px;
        height: 100px;
        border: 1px solid #ddd;
        border-radius: 5px;
        overflow: hidden;
        background-color: #f9f9f9;
        position: relative;
        text-align: center;
        padding-top: 10px;
    }

    /* Style for delete button on existing documents */
    .existing-preview-item .delete-existing-doc {
        background-color: rgba(255, 0, 0, 0.7);
        border: none;
        color: #fff;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        padding: 0;
        line-height: 1;
        text-align: center;
        cursor: pointer;
    }

    /* Style for new document previews */
    .preview-item {
        position: relative;
        width: 100px;
        height: 100px;
        border: 1px solid #ddd;
        border-radius: 5px;
        overflow: hidden;
        background-color: #f9f9f9;
        margin-right: 10px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Style for images in new previews */
    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Style for icons in new previews */
    .preview-item .file-icon {
        font-size: 2rem;
    }

    /* Style for file name in new previews */
    .preview-item .file-name {
        position: absolute;
        bottom: 0;
        width: 100%;
        background: rgba(255, 255, 255, 0.8);
        text-align: center;
        font-size: 0.8rem;
        padding: 2px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Style for delete button on new documents */
    .preview-item .delete-btn {
        position: absolute;
        top: 2px;
        right: 2px;
        background-color: rgba(0, 0, 0, 0.6);
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    /* Hover effect for delete buttons */
    .existing-preview-item .delete-existing-doc:hover,
    .preview-item .delete-btn:hover {
        background-color: rgba(255, 0, 0, 0.8);
    }
    #expensesTable td {
        width: 10%;  /* Adjust the width to suit your table */
        text-align: center; /* Optional: Align content to the center */
    }
</style>

<!-- Bootstrap Datepicker CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endpush

@push('scripts')
<!-- Bootstrap Datepicker JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
    // Declare a global variable to store the selected TP ID
    let selectedGlobalTPId = $('#tour_plan_id').val();  // This value should be assigned dynamically

    $(document).ready(function() {
        // const selectedDate = "{{ $visitDate }}";  // Use the correct variable passed from controller
        const selectedTPId = $('#tour_plan_id').val();  // This can remain as is
         const selectedDate = $('#visit_date_temp').val();
        

        // Get the server's current date in 'YYYY-MM-DD' format
        const currentDate = "{{ \Carbon\Carbon::now()->toDateString() }}";
        const visitsListEl = $('#visitsList');
        const visitDetailsContentEl = $('#visitDetailsContent');

        console.log('selectedDate: ', selectedDate);
        console.log('selectedTPId: ', selectedTPId);
        console.log('currentDate: '+currentDate);

        $('#tour_plan_id').val(selectedTPId); // Set the tour plan ID field
        $('#visitDate').text(selectedDate);

         // Initialize DataTable with equal column width
          $('#expensesTable').DataTable({
                responsive: true,
                autoWidth: false,
                "columnDefs": [
                    { "width": "10%", "targets": "_all" } // Equal width for all columns
                ]
          });

         // Construct the URL correctly for the API
      //  const apiUrl = `{{ route('expenses.fetchVisits', ['date' => ':date']) }}`;
        var apiUrl = "{{ route('expenses.fetchVisits', ['date' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', selectedDate);
        
        // Replace :date with selectedDate
        const finalUrl = apiUrl + `&dcr_id=${selectedTPId}`;

        console.log('finalUrl:', finalUrl);  // Log to see if the URL is correct


        // Function to fetch visits data
        function fetchVisits() {
            $.ajax({
                url:  finalUrl,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('AJAX Response:', response); // Debugging

                    if (response.success && response.events.length > 0) {
                        // Process each event (if multiple)
                        response.events.forEach(function(event) {
                            let bloodBankNames = '';
                            var bankNameDisplay = event.visit_to ? event.visit_to : '-';
                            
                            // Update the UI with the blood bank name and visit date
                            $('#bloodBankName').text(bankNameDisplay);
                            $('#date').val(event.visit_date); // Set the form date field with the visit date
                            $('#tour_plan_id').val(selectedTPId); // Set the tour plan ID field
                            $('#visitDate').val(selectedDate);
                            
                        });
                    } else {
                        console.log('No events found or unsuccessful response.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching visits:", error);
                    Swal.fire('Error', 'An error occurred while fetching the data.', 'error');
                }
            });
        }

        fetchVisits(); // Call the function on page load

        // Call the function to fetch expenses list on page load
        fetchExpenses(selectedTPId);
        

         // Initialize the Bootstrap datepicker
          $('#date').datepicker({
            format: 'yyyy-mm-dd',  // Set the format for the date
            todayHighlight: true,  // Highlight today's date
            autoclose: true,       // Close the date picker when a date is selected
            maxViewMode: 2,        // Set the calendar to the month view
            todayBtn: "linked"     // Display a button to select today's date
          });

          // Trigger the date picker when the calendar icon is clicked
          $('#calendar-icon').on('click', function() {
            $('#date').datepicker('show');  // Show the date picker
          });

          // Function to update the Total Price based on input changes
          function updateTotalPrice() {
              const food = parseFloat($('#foodPrice').val()) || 0;
              const convention = parseFloat($('#conventionPrice').val()) || 0;
              const telFax = parseFloat($('#TelFaxPrice').val()) || 0;
              const lodging = parseFloat($('#lodgingPrice').val()) || 0;
              const sundry = parseFloat($('#sundryPrice').val()) || 0;

              const totalPrice = food + convention + telFax + lodging + sundry;
              $('#totalPrice').val(totalPrice.toFixed(2)); // Set total price with 2 decimals
            }

            // Trigger total price calculation on change of any relevant field
            $('#foodPrice, #conventionPrice, #TelFaxPrice, #lodgingPrice, #sundryPrice').on('input', function() {
              updateTotalPrice(); // Update the total price when any field changes
            });

            // Initially update total price based on existing field values
            updateTotalPrice();


    const form = document.querySelector('#expenseForm');  // Make sure the form is correctly targeted
    console.log('Form element:', form);  // Log to check if the correct form is being selected
   const documentsInput = document.getElementById('documents');
   const newDocumentPreview = document.getElementById('new-document-preview');
   const documentsToDeleteContainer = document.getElementById('documents_to_delete_container');
   const existingDocumentPreview = document.getElementById('existing-document-preview');
   
   console.log('form inside');  // This should be logged now

         let allSelectedFiles = [];  // Variable to store selected files

        // Function to get Bootstrap Icon class based on file extension
        function getIconClass(fileExtension) {
            switch(fileExtension) {
                case 'pdf':
                    return ['bi-file-earmark-pdf-fill', 'text-danger'];
                case 'doc':
                case 'docx':
                    return ['bi-file-earmark-word-fill', 'text-primary'];
                case 'xls':
                case 'xlsx':
                    return ['bi-file-earmark-excel-fill', 'text-success'];
                case 'csv':
                    return ['bi-file-earmark-bar-graph-fill', 'text-info']; // Using bar graph icon for CSV
                case 'txt':
                    return ['bi-file-earmark-text-fill', 'text-secondary'];
                default:
                    return ['bi-file-earmark-fill', 'text-info'];
            }
        }

        // Function to remove a file from the file input
        function removeFileFromInput(fileToRemove) {
            const dt = new DataTransfer();
            const files = Array.from(documentsInput.files);

            files.forEach(file => {
                if (file !== fileToRemove) {
                    dt.items.add(file);
                }
            });

            documentsInput.files = dt.files;

            allSelectedFiles = allSelectedFiles.filter(file => file !== fileToRemove);
        }

            // Function to fetch expenses
        function fetchExpenses(tpId) {
            const expensesListEl = document.getElementById('expensesList'); // Container for displaying expenses

            $.ajax({
                url: `{{ route('expenses.fetchExpenses', ['tp_id' => ':tpId']) }}`.replace(':tpId', tpId), // Assuming you have an API route to fetch expenses
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        const expenses = response.data;
                        let expensesHTML = '';
                        let serialNumber = 1; // Initialize serial number

                    
                        expenses.forEach(function(expense) {
                        // 1) prepare five buckets for each attachment‐type
                        const previews = {
                            1: '', // Food
                            2: '', // Conveyance
                            3: '', // Tel/Fax
                            4: '', // Lodging
                            5: ''  // Sundry
                        };


                        //   // Prepare document previews
                        //   let documentPreviews = '';
                        //   expense.documents.forEach(function(doc) {
                        //       const docUrl = "{{ config('auth_api.base_image_url') }}" + doc.attachments;

                        //       // Show preview for image files and a download link for others
                        //       if (doc.attachments.match(/\.(jpg|jpeg|png|gif|svg)$/)) {
                        //           documentPreviews += `
                        //               <div class="preview-item">
                        //                   <a href="${docUrl}" target="_blank">
                        //                       <img src="${docUrl}" alt="Document Preview" class="img-thumbnail" style="width: 50px; height: 50px;">
                        //                   </a>
                        //               </div>
                        //           `;
                        //       } else {
                        //           documentPreviews += `
                        //               <div class="preview-item">
                        //                   <a href="${docUrl}" target="_blank">
                        //                       <i class="bi bi-file-earmark-text-fill" style="font-size: 2rem;"></i>
                        //                   </a>
                        //               </div>
                        //           `;
                        //       }
                        //   });

                        // 2) fill them
                        expense.documents.forEach(function(doc) {
                            const url = "{{ config('auth_api.base_image_url') }}" + doc.attachments;
                            let html = '';

                            if (/\.(jpe?g|png|gif|svg)$/i.test(doc.attachments)) {
                            html = `
                                <div class="preview-item">
                                <a href="${url}" target="_blank">
                                    <img src="${url}" alt="Attachment" class="img-thumbnail" style="width:50px;height:50px;">
                                </a>
                                </div>`;
                            } else {
                            html = `
                                <div class="preview-item">
                                <a href="${url}" target="_blank">
                                    <i class="bi bi-file-earmark-text-fill" style="font-size:2rem;"></i>
                                </a>
                                </div>`;
                            }

                            // append into correct bucket
                            previews[doc.type] += html;
                        });

                            expensesHTML += `
                                <tr>
                                    <td>${serialNumber++}</td> <!-- Serial number for each row -->
                                    <td>${expense.date}</td>
                                    <td>${expense.description}</td>
                                    <td>${expense.food}</td>
                                    <td>${expense.convention}</td>
                                    <td>${expense.tel_fax}</td>
                                    <td>${expense.lodging}</td>
                                    <td>${expense.sundry}</td>
                                    <td>${expense.total_price}</td>
                                    <td>${expense.remarks}</td>
                                    <td>${previews[1] || '-'}</td>
                                    <td>${previews[2] || '-'}</td>
                                    <td>${previews[3] || '-'}</td>
                                    <td>${previews[4] || '-'}</td>
                                    <td>${previews[5] || '-'}</td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm delete-expense" data-expense-id="${expense.id}">
                                            <i class="bi bi-trash"></i> <!-- Trash icon for delete -->
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        expensesListEl.innerHTML = expensesHTML;

                        // Initialize DataTable after populating the data
                        $('#expensesTable').DataTable();
                    } else {
                        expensesListEl.innerHTML = '<tr><td colspan="11">No expenses found for this Tour Plan.</td></tr>';
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching expenses:", error);
                    Swal.fire('Error', 'An error occurred while fetching expenses.', 'error');
                }
            });
        }

             if (selectedTPId) {
                // Get the update URL with the placeholder
                var updateUrl = "{{ route('expenses.dcr.updateStatus', ['id' => 'PLACEHOLDER']) }}";
                // Replace the placeholder with the actual id
                updateUrl = updateUrl.replace('PLACEHOLDER', selectedTPId);
                // Set the form's action attribute to the updated URL
                $('#statusForm').attr('action', updateUrl);
            }
     
          // Only proceed if all required query parameters exist
            if (selectedTPId && selectedDate) {

                $.ajax({
                    url: "{{ route('expenses.expenseStatus') }}",
                    type: 'GET',
                    dataType: 'json', // ensures the response is parsed as JSON
                    data: {
                        id: selectedTPId,
                        visit_date: selectedDate
                    },
                    success: function(response) {
                        console.log("dcr status result", response);
                        if (response.success) {
                            if (response.data) {
                              // var dcr = response.data[0];
                               var dcr = response.data; // API now returns a single object
                               window.currentExpenseStatus = dcr; // store globally for info popup
                                $('#expenseMGRStatusDisplay').text("Manager Status: " + capitalizeFirstLetter(dcr.expense_mgr_status));
                                $('#expenseCAStatusDisplay').text("CA Status: " + capitalizeFirstLetter(dcr.expense_ca_status));
                                $('#remarksDisplay').text(function() {
                                    // Initialize the display text
                                    var remarksText = "";

                                    // Check if manager status remarks are available
                                    if (dcr.expense_mgr_remarks && dcr.expense_mgr_remarks.trim() !== "") {
                                        remarksText += "Mgr Remarks: " + capitalizeFirstLetter(dcr.expense_mgr_remarks);
                                    }

                                    // Check if CA status remarks are available
                                    if (dcr.expense_ca_remarks && dcr.expense_ca_remarks.trim() !== "") {
                                        // If there's already text, add a separator
                                        if (remarksText) {
                                            remarksText += " | ";
                                        }
                                        remarksText += "CA Remarks: " + capitalizeFirstLetter(dcr.expense_ca_remarks);
                                    }

                                    // Display the final remarks text (if any)
                                    return remarksText || ""; // Default message if no remarks
                                });

                                // --------------------------------
                                // TRAVEL DISPLAY FIELDS (read-only)
                                // --------------------------------
                                $('#travelModeDisplay').text(
                                    dcr.travel_mode ? capitalizeFirstLetter(dcr.travel_mode) : 'N/A'
                                );

                                $('#kmTravelledDisplay').text(
                                    dcr.km_travelled ? dcr.km_travelled : 'N/A'
                                );

                                $('#travelRemarksDisplay').text(
                                    dcr.travel_remarks ? dcr.travel_remarks : 'N/A'
                                );

                                $('#kmAssignedDisplay').text(
                                    dcr.travel_kms_limit ? dcr.travel_kms_limit : 'N/A'
                                );

                                $('#pricePerKmDisplay').text(
                                    dcr.price_per_km ? dcr.price_per_km : 'N/A'
                                );

                                // Approved values display
                                $('#approvedKmDisplay').text(
                                    dcr.approved_km_travel ? dcr.approved_km_travel : 'N/A'
                                );

                                $('#approvedPriceDisplay').text(
                                    dcr.approved_travel_cost ? dcr.approved_travel_cost : 'N/A'
                                );

                                $('#approvedRemarksDisplay').text(
                                    dcr.approved_travel_remarks ? dcr.approved_travel_remarks : 'N/A'
                                );


                                // --------------------------------
                                // DEFAULT VALUES FOR APPROVAL FIELDS
                                // --------------------------------
                                const kmTravelled = dcr.km_travelled ? parseFloat(dcr.km_travelled) : 0;
                                const travelKmsLimit = dcr.travel_kms_limit ? parseFloat(dcr.travel_kms_limit) : null;
                                const pricePerKm = dcr.price_per_km ? parseFloat(dcr.price_per_km) : null;

                                let defaultApprovedKm = null;
                                let defaultApprovedCost = null;

                                // If already approved earlier, show those values
                                if (dcr.approved_km_travel !== null && dcr.approved_km_travel !== undefined) {
                                    defaultApprovedKm = parseFloat(dcr.approved_km_travel);
                                } else {
                                    // Rule:
                                    // If km_travelled <= travel_kms_limit => use km_travelled
                                    // Else => use travel_kms_limit
                                    if (travelKmsLimit !== null && !isNaN(kmTravelled)) {
                                        defaultApprovedKm = (kmTravelled <= travelKmsLimit)
                                            ? kmTravelled
                                            : travelKmsLimit;
                                    } else {
                                        // if no limit configured, just use travelled kms
                                        defaultApprovedKm = kmTravelled;
                                    }
                                }

                                if (dcr.approved_travel_cost !== null && dcr.approved_travel_cost !== undefined) {
                                    defaultApprovedCost = parseFloat(dcr.approved_travel_cost);
                                } else if (
                                    defaultApprovedKm !== null &&
                                    !isNaN(defaultApprovedKm) &&
                                    pricePerKm !== null &&
                                    !isNaN(pricePerKm)
                                ) {
                                    // Approved Total Price = Approved KM * Price Per KM
                                    defaultApprovedCost = defaultApprovedKm * pricePerKm;
                                }

                                // Set defaults in inputs (user can override)
                                $('#approvedKmTravelled').val(
                                    defaultApprovedKm !== null && !isNaN(defaultApprovedKm)
                                        ? defaultApprovedKm.toFixed(2)
                                        : ''
                                );

                                $('#approvedTravelCost').val(
                                    defaultApprovedCost !== null && !isNaN(defaultApprovedCost)
                                        ? defaultApprovedCost.toFixed(2)
                                        : ''
                                );

                                $('#approvedTravelRemarks').val(dcr.approved_travel_remarks || '');

                            } else {
                                $('#expenseMGRStatusDisplay').text("Manager Status: N/A");
                                $('#expenseCAStatusDisplay').text("CA Status: N/A");
                                $('#remarksDisplay').text("");
                            }
                        } else {
                            $('#expenseMGRStatusDisplay').text("Manager Status: N/A");
                            $('#expenseCAStatusDisplay').text("CA Status: N/A");
                            $('#remarksDisplay').text("");
                            $('#travelModeDisplay').text('N/A');
                            $('#kmTravelledDisplay').text('N/A');
                            $('#travelRemarksDisplay').text('N/A');
                            $('#kmAssignedDisplay').text('N/A');
                            $('#pricePerKmDisplay').text('N/A');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching expense status:", error);
                    }
                });
            
            }

            // Utility function: Capitalize first letter
            function capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }


            // Update Approve, Reject Status
            const clearBtn = $('#clearBtn');
            const rejectBtn = $('#rejectBtn');
            const statusForm = $('#statusForm');

            // clearBtn.on('click', function (e) {
            //     e.preventDefault(); // Prevent the default form submission

            //     Swal.fire({
            //         title: 'Are you sure?',
            //         //text: "Do you really want to approve this DCR?",
            //         html: `<p>Do you really want to clear this expense?</p><textarea class="form-control" id="remarks" class="swal2-input" placeholder="Enter your remarks here..." rows="3"></textarea>`,     
            //         icon: 'warning',
            //         showCancelButton: true,
            //         confirmButtonColor: '#28a745', // Green color for approve
            //         cancelButtonColor: '#6c757d', // Gray color for cancel
            //         confirmButtonText: 'Yes, clear it!',
            //         preConfirm: () => {
            //             const remarks = document.getElementById('remarks').value;
            //             // if (!remarks) {
            //             //     Swal.showValidationMessage('Remarks cannot be empty');
            //             //     return false;
            //             // }
            //             return remarks; // Return remarks to be included in the form submission
            //         }
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //              // Add remarks to the form and submit
            //             $('<input>').attr({
            //                 type: 'hidden',
            //                 name: 'remarks',
            //                 value: result.value
            //             }).appendTo(statusForm);

            //             // Set the status value and submit the form
            //             $('<input>').attr({
            //                 type: 'hidden',
            //                 name: 'status',
            //                 value: 'cleared'
            //             }).appendTo(statusForm);
            //             statusForm.submit();
            //         }
            //     });
            // });

            // rejectBtn.on('click', function (e) {
            //     e.preventDefault(); // Prevent the default form submission

            //     Swal.fire({
            //         title: 'Are you sure?',
            //        // text: "Do you really want to reject this DCR?",
            //         html: `<p>Do you really want to reject this Expense?</p><textarea class="form-control"  id="remarks" class="swal2-input" placeholder="Enter your remarks here..." rows="3"></textarea>`,
            //         icon: 'warning',
            //         showCancelButton: true,
            //         confirmButtonColor: '#dc3545', // Red color for reject
            //         cancelButtonColor: '#6c757d', // Gray color for cancel
            //         confirmButtonText: 'Yes, reject it!',
            //         preConfirm: () => {
            //             const remarks = document.getElementById('remarks').value;
            //             if (!remarks) {
            //                 Swal.showValidationMessage('Remarks cannot be empty');
            //                 return false;
            //             }
            //             return remarks; // Return remarks to be included in the form submission
            //         }
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             // Add remarks to the form and submit
            //             $('<input>').attr({
            //                 type: 'hidden',
            //                 name: 'remarks',
            //                 value: result.value
            //             }).appendTo(statusForm);

            //             // Set the status value and submit the form
            //             $('<input>').attr({
            //                 type: 'hidden',
            //                 name: 'status',
            //                 value: 'rejected'
            //             }).appendTo(statusForm);
            //             statusForm.submit();
            //         }
            //     });
            // });

            clearBtn.on('click', function (e) {
                e.preventDefault(); // Prevent the default form submission

                Swal.fire({
                    title: 'Are you sure?',
                    html: `<p>Do you really want to clear this expense?</p>
                        <textarea class="form-control" id="remarks" class="swal2-input"
                                    placeholder="Enter your remarks here..." rows="3"></textarea>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, clear it!',
                    preConfirm: () => {
                        const remarks = document.getElementById('remarks').value;
                        return remarks; // remarks can be optional here
                    }
                }).then((result) => {
                    if (result.isConfirmed) {

                        // Remove any existing hidden fields so we don't duplicate
                        statusForm.find('input[name="remarks"], input[name="status"], input[name="approved_km_travel"], input[name="approved_travel_cost"], input[name="approved_travel_remarks"]').remove();

                        // Pull values from the inputs
                        const approvedKm = $('#approvedKmTravelled').val();
                        const approvedCost = $('#approvedTravelCost').val();
                        const approvedRemarks = $('#approvedTravelRemarks').val();

                        // Add remarks to the form
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'remarks',
                            value: result.value || ''
                        }).appendTo(statusForm);

                        // Add status
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'status',
                            value: 'cleared'
                        }).appendTo(statusForm);

                        // Add approved travel fields
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'approved_km_travel',
                            value: approvedKm
                        }).appendTo(statusForm);

                        $('<input>').attr({
                            type: 'hidden',
                            name: 'approved_travel_cost',
                            value: approvedCost
                        }).appendTo(statusForm);

                        $('<input>').attr({
                            type: 'hidden',
                            name: 'approved_travel_remarks',
                            value: approvedRemarks
                        }).appendTo(statusForm);

                        statusForm.submit();
                    }
                });
            });


            rejectBtn.on('click', function (e) {
                e.preventDefault(); // Prevent the default form submission

                Swal.fire({
                    title: 'Are you sure?',
                    html: `<p>Do you really want to reject this Expense?</p>
                        <textarea class="form-control" id="remarks" class="swal2-input"
                                    placeholder="Enter your remarks here..." rows="3"></textarea>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, reject it!',
                    preConfirm: () => {
                        const remarks = document.getElementById('remarks').value;
                        if (!remarks) {
                            Swal.showValidationMessage('Remarks cannot be empty');
                            return false;
                        }
                        return remarks;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {

                        // Remove existing hidden fields
                        statusForm.find('input[name="remarks"], input[name="status"], input[name="approved_km_travel"], input[name="approved_travel_cost"], input[name="approved_travel_remarks"]').remove();

                        const approvedKm = $('#approvedKmTravelled').val();
                        const approvedCost = $('#approvedTravelCost').val();
                        const approvedRemarks = $('#approvedTravelRemarks').val();

                        // Add remarks
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'remarks',
                            value: result.value
                        }).appendTo(statusForm);

                        // Add status
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'status',
                            value: 'rejected'
                        }).appendTo(statusForm);

                        // Even for rejection you might want to keep what they had keyed in
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'approved_km_travel',
                            value: approvedKm
                        }).appendTo(statusForm);

                        $('<input>').attr({
                            type: 'hidden',
                            name: 'approved_travel_cost',
                            value: approvedCost
                        }).appendTo(statusForm);

                        $('<input>').attr({
                            type: 'hidden',
                            name: 'approved_travel_remarks',
                            value: approvedRemarks
                        }).appendTo(statusForm);

                        statusForm.submit();
                    }
                });
            });


            // ---------------------------------------------
            // INFO ICON → SHOW LIVE CHECK-IN DETAILS
            // ---------------------------------------------
            $('#travelInfoIcon').on('click', function () {

                const dcr = window.currentExpenseStatus;

                if (!dcr) {
                    return Swal.fire("Info", "Live location data not available.", "info");
                }

                const empName = $('#bloodBankName').text() || 'N/A'; // or from another source
                const visitDate = dcr.visit_date || 'N/A';

                const formatVal = (v) => (v !== null && v !== undefined && v !== '' ? v : 'N/A');

                const html = `
                    <div style="text-align:left">
            
                        <span><strong>Visit Date:</strong> ${formatVal(visitDate)}</span>
                        <hr>
                        <p><strong>Check In Time:</strong> ${formatVal(dcr.check_in_time)}</p>
                        <p><strong>Check Out Time:</strong> ${formatVal(dcr.check_out_time)}</p>

                        <p><strong>Working Duration:</strong> ${formatVal(dcr.working_duration)}</p>

                        <p><strong>Total Location Pings:</strong> ${formatVal(dcr.total_pings)}</p>
                        <p><strong>Reporting Pings:</strong> ${formatVal(dcr.reporting_points)}</p>
                        <p><strong>Non-Reporting Pings:</strong> ${formatVal(dcr.non_reporting_points)}</p>

                        <p><strong>Check-In Missing:</strong>
                            ${dcr.check_in_missing == 1
                                ? '<span class="text-danger fw-bold">Yes</span>'
                                : '<span class="text-success fw-bold">No</span>'}
                        </p>

                        <p><strong>Check-Out Missing:</strong>
                            ${dcr.check_out_missing == 1
                                ? '<span class="text-danger fw-bold">Yes</span>'
                                : '<span class="text-success fw-bold">No</span>'}
                        </p>
                    </div>
                `;

                Swal.fire({
                    title: 'Check-In / Location Summary',
                    html: html,
                    width: 500,
                    confirmButtonText: 'Close'
                });
            });


    });


    

</script>


     <!-- SweetAlert for Session Messages -->
     @if(session('success'))
     <script>
         document.addEventListener('DOMContentLoaded', function () {
             Swal.fire({
                 icon: 'success',
                 title: 'Success',
                 text: "{!! session('success') !!}",
                 confirmButtonText: 'OK'
             });
         });
     </script>
     @endif

     @if(session('error'))
     <script>
         document.addEventListener('DOMContentLoaded', function () {
             Swal.fire({
                 icon: 'warning',
                 title: 'Error',
                 text: "{!! session('error') !!}",
                 confirmButtonText: 'OK'
             });
         });
     </script>
     @endif

     @if ($errors->any())
     <script>
         document.addEventListener('DOMContentLoaded', function () {
             Swal.fire({
                 icon: 'warning',
                 title: 'Validation Errors',
                 html: `
                     <ul>
                         @foreach ($errors->all() as $error)
                             <li>{{ $error }}</li>
                         @endforeach
                     </ul>
                 `,
                 confirmButtonText: 'OK'
             });
         });
     </script>
     @endif
@endpush
