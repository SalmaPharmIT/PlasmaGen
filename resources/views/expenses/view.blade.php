@extends('include.dashboardLayout')

@section('title', 'View Expenses')

@section('content')

<div class="pagetitle d-flex justify-content-between align-items-center">
    <div>
        <h1>Add & View Expenses</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
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

              
                <div class="col-md-12">
                  <h5 class="card-title">Add Expenses</h5>

                  <!-- Add Expenses Form -->
                  <form class="row g-3" id="expenseForm" action="{{ route('expenses.submit') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Date -->
                    <div class="col-md-3">
                      <label for="date" class="form-label">Date <span style="color:red">*</span></label>
                      <div class="input-group">
                        <input type="text" class="form-control" id="date" name="date" value="" required>
                        <span class="input-group-text" id="calendar-icon">
                          <i class="bi bi-calendar"></i>  <!-- Calendar icon -->
                        </span>
                      </div>
                    </div>
                    

                    <!-- Description -->
                    <div class="col-md-9">
                      <label for="description" class="form-label">Description/Narration <span style="color:red">*</span></label>
                      <input type="text" class="form-control" id="description" name="description" value="" required>
                    </div>

                    <!-- Extra Fields for Part Prices -->
                    <div class="col-md-2">
                      <label for="foodPrice" class="form-label">Food</label>
                      <input type="number" class="form-control" id="foodPrice" name="foodPrice" step="0.01" value="0">
                    </div>

                    <div class="col-md-2">
                      <label for="conventionPrice" class="form-label">Convention</label>
                      <input type="number" class="form-control" id="conventionPrice" name="conventionPrice" step="0.01" value="0">
                    </div>

                    <div class="col-md-2">
                      <label for="TelFaxPrice" class="form-label">Tel/Fax</label>
                      <input type="number" class="form-control" id="TelFaxPrice" name="TelFaxPrice" step="0.01" value="0">
                    </div>

                    <div class="col-md-2">
                      <label for="lodgingPrice" class="form-label">Lodging</label>
                      <input type="number" class="form-control" id="lodgingPrice" name="lodgingPrice" step="0.01" value="0">
                    </div>

                    <div class="col-md-2">
                      <label for="sundryPrice" class="form-label">Sundry</label>
                      <input type="number" class="form-control" id="sundryPrice" name="sundryPrice" step="0.01" value="0">
                    </div>

                    <!-- Total Price (Read-Only) -->
                    <div class="col-md-2">
                      <label for="totalPrice" class="form-label">TotalPrice</label>
                      <input type="number" class="form-control" id="totalPrice" name="totalPrice" step="0.01" value="0" readonly>
                    </div>

                    <!-- Attach Documents -->
                    <div class="col-md-12">
                      <label for="documents" class="form-label">Attach Documents (If any)</label>
                      <input type="file" class="form-control" id="documents" name="documents[]" multiple>
                      <small class="form-text text-muted">You can upload multiple files (DOC, PDF, Images, etc.).</small>
                    </div>

                     <!-- Existing Documents -->
                  @if(!empty($entity['documents']) && is_array($entity['documents']))
                  <div class="col-md-12 mt-3">
                      <h6>Existing Documents</h6>
                      <div id="existing-document-preview" class="d-flex flex-wrap">
                          @foreach($entity['documents'] as $doc)
                              <div class="existing-preview-item position-relative me-2 mb-2">
                                  <a href="{{ config('auth_api.base_image_url') . $doc }}" target="_blank" class="d-block">
                                      @php
                                          $fileExtension = pathinfo($doc, PATHINFO_EXTENSION);
                                          $iconClass = '';
                                          switch(strtolower($fileExtension)) {
                                              case 'pdf':
                                                  $iconClass = 'bi-file-earmark-pdf-fill text-danger';
                                                  break;
                                              case 'doc':
                                              case 'docx':
                                                  $iconClass = 'bi-file-earmark-word-fill text-primary';
                                                  break;
                                              case 'xls':
                                              case 'xlsx':
                                                  $iconClass = 'bi-file-earmark-excel-fill text-success';
                                                  break;
                                              case 'txt':
                                                  $iconClass = 'bi-file-earmark-text-fill text-secondary';
                                                  break;
                                              case 'jpg':
                                              case 'jpeg':
                                              case 'png':
                                              case 'gif':
                                              case 'svg':
                                                  $iconClass = 'bi-file-earmark-image-fill text-primary';
                                                  break;
                                              case 'csv':
                                                  $iconClass = 'bi-file-earmark-bar-graph-fill text-info'; // Using bar graph icon for CSV
                                                  break;
                                              default:
                                                  $iconClass = 'bi-file-earmark-fill text-info';
                                          }
                                      @endphp
                                      <i class="{{ $iconClass }}" style="font-size: 2rem;"></i>
                                      <span class="d-block text-truncate" style="max-width: 80px;">{{ basename($doc) }}</span>
                                  </a>
                                  <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 delete-existing-doc" data-doc="{{ $doc }}">
                                      &times;
                                  </button>
                              </div>
                          @endforeach
                      </div>
                  </div>
                  @endif

                  <!-- Preview Section for New Documents -->
                  <div class="col-md-12">
                    <h6>New Documents Preview</h6>
                    <div id="new-document-preview" class="d-flex flex-wrap">
                        <!-- Previews will be appended here -->
                    </div>
                  </div>

                  <!-- Created By (Hidden or Pre-filled) -->
                  <input type="hidden" name="created_by" value="{{ Auth::id() }}">

                  <!-- Modified By (Hidden or Pre-filled) -->
                  <input type="hidden" name="modified_by" value="{{ Auth::id() }}">

                  <!-- Modified By (Hidden or Pre-filled) -->
                  <input type="hidden" name="tour_plan_id" id="tour_plan_id" value="">

                    <div class="text-end">
                      <button type="reset" class="btn btn-secondary">Reset</button>
                      <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
                   
                  </form>
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

                  <table id="expensesTable" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>SI. No.</th> <!-- New column for serial number -->
                        <th>Date</th>
                        <th>Description</th>
                        <th>Food</th>
                        <th>Convention</th>
                        <th>Tel/Fax</th>
                        <th>Lodging</th>
                        <th>Sundry</th>
                        <th>Total Price</th>
                        <th>Attachments</th> <!-- New column for attachments -->
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

{{-- <script>
  document.addEventListener('DOMContentLoaded', function() {
   const form = document.querySelector('form');  // Make sure the form is correctly targeted
   const documentsInput = document.getElementById('documents');
   const newDocumentPreview = document.getElementById('new-document-preview');
   const documentsToDeleteContainer = document.getElementById('documents_to_delete_container');
   const existingDocumentPreview = document.getElementById('existing-document-preview');

   
   // Variable to store selected files
   let allSelectedFiles = [];

   // Handle existing document deletion
   if (existingDocumentPreview) {
       existingDocumentPreview.addEventListener('click', function(e) {
           if (e.target && e.target.classList.contains('delete-existing-doc')) {
               const docPath = e.target.getAttribute('data-doc');

               // Create a new hidden input for the document to delete
               const hiddenInput = document.createElement('input');
               hiddenInput.type = 'hidden';
               hiddenInput.name = 'documents_to_delete[]';
               hiddenInput.value = docPath;

               // Append the hidden input to the container
               documentsToDeleteContainer.appendChild(hiddenInput);

               // Remove the preview item from the DOM
               e.target.parentElement.remove();
           }
       });
   }

     // Handle new document selection and preview
     documentsInput.addEventListener('change', function(e) {
       const files = Array.from(e.target.files);
       let filesProcessed = 0;

       if (files.length === 0) {
           return;
       }

       // Append the new files to the existing ones
       allSelectedFiles = allSelectedFiles.concat(files);
       console.log('allSelectedFiles', allSelectedFiles.length);

       files.forEach(file => {
           const fileReader = new FileReader();

           fileReader.onload = function(e) {
               const fileURL = e.target.result;
               let fileType = file.type;
               const fileName = file.name;
               const fileExtension = fileName.split('.').pop().toLowerCase();

               // If fileType is empty, determine based on extension
               if (!fileType) {
                   switch(fileExtension) {
                       case 'pdf':
                           fileType = 'application/pdf';
                           break;
                       case 'doc':
                       case 'docx':
                           fileType = 'application/msword';
                           break;
                       case 'xls':
                       case 'xlsx':
                           fileType = 'application/vnd.ms-excel';
                           break;
                       case 'csv':
                           fileType = 'text/csv';
                           break;
                       case 'txt':
                           fileType = 'text/plain';
                           break;
                       default:
                           fileType = 'application/octet-stream';
                   }
               }

               const previewItem = document.createElement('div');
               previewItem.classList.add('preview-item');

               // Create delete button
               const deleteBtn = document.createElement('button');
               deleteBtn.classList.add('delete-btn');
               deleteBtn.innerHTML = '&times;';
               deleteBtn.title = 'Remove this file';

               // Event listener for delete button
               deleteBtn.addEventListener('click', function() {
                   // Remove the preview item
                   newDocumentPreview.removeChild(previewItem);

                   // Remove the file from the input
                   removeFileFromInput(file);
               });

               // Append delete button
               previewItem.appendChild(deleteBtn);

               if (fileType.startsWith('image/')) {
                   const img = document.createElement('img');
                   img.src = fileURL;
                   img.alt = file.name;
                   previewItem.appendChild(img);
               } else {
                   // For non-image files, display an icon based on file type
                   const icon = document.createElement('i');
                   icon.classList.add('bi', ...getIconClass(fileExtension));
                   icon.classList.add('file-icon');
                   previewItem.appendChild(icon);

                   // Display the file name
                   const fileNameSpan = document.createElement('span');
                   fileNameSpan.classList.add('file-name');
                   fileNameSpan.textContent = file.name;
                   previewItem.appendChild(fileNameSpan);
               }

               newDocumentPreview.appendChild(previewItem);

               filesProcessed++;
               if (filesProcessed === files.length) {
                   // All files processed, reset the input
                   // Comment out or remove the following line
                   // documentsInput.value = '';
               }
           };

           fileReader.onerror = function() {
               console.error("Error reading file: " + file.name);
               filesProcessed++;
               if (filesProcessed === files.length) {
                   // All files processed, reset the input
                   // Comment out or remove the following line
                   // documentsInput.value = '';
               }
           };

           fileReader.readAsDataURL(file);
       });
   });

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

       // Remove the file from the allSelectedFiles array
       allSelectedFiles = allSelectedFiles.filter(file => file !== fileToRemove);
   }

     // Add event listener for form submission
   if (form) {
    console.log('form inside');  // This should be logged when form is about to submit
       form.addEventListener('submit', function(event) {
           console.log('form submit triggered');  // This should be logged when form is about to submit
           event.preventDefault();

           // Clear the existing files in the documents input before appending new files
           const dataTransfer = new DataTransfer();
           allSelectedFiles.forEach(file => {
               dataTransfer.items.add(file);
           });

           // Update the input field with the newly selected files
           documentsInput.files = dataTransfer.files;

           // Now submit the form manually
           form.submit();
       });
   }
});

</script> --}}

<script>
    // Declare a global variable to store the selected TP ID
    let selectedGlobalTPId = "{{ $tpId }}";  // This value should be assigned dynamically

    $(document).ready(function() {
        const selectedDate = "{{ $visitDate }}";  // Use the correct variable passed from controller
        const selectedTPId = "{{ $tpId }}";  // This can remain as is

        // Get the server's current date in 'YYYY-MM-DD' format
        const currentDate = "{{ \Carbon\Carbon::now()->toDateString() }}";
        const visitsListEl = $('#visitsList');
        const visitDetailsContentEl = $('#visitDetailsContent');

        console.log('selectedDate: ', selectedDate);
        console.log('selectedTPId: ', selectedTPId);
        console.log('currentDate: '+currentDate);

         // Initialize DataTable with equal column width
          $('#expensesTable').DataTable({
              "columnDefs": [
                  { "width": "10%", "targets": "_all" } // Equal width for all columns
              ]
          });

         // Construct the URL correctly for the API
      //  const apiUrl = `{{ route('expenses.fetchVisits', ['date' => ':date']) }}`;
        var apiUrl = "{{ route('expenses.fetchVisits', ['date' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', selectedDate);
        
        // Replace :date with selectedDate
        const finalUrl = apiUrl + `&tp_id=${selectedTPId}`;

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
                            
                            // Check tour_plan_type
                            if (event.extendedProps.tour_plan_type == 2) {
                                // Concatenate blood bank names from the tour_plan_visits array
                                if (event.extendedProps.tour_plan_visits && event.extendedProps.tour_plan_visits.length > 0) {
                                    const names = event.extendedProps.tour_plan_visits.map(function(visit) {
                                        return visit.sourcing_blood_bank_name;
                                    });
                                    bloodBankNames = names.join(', ');
                                }
                            } else if (event.extendedProps.tour_plan_type == 1) {
                                // Use the title for tour_plan_type 1
                                bloodBankNames = event.title;
                            }
                            
                            // Update the UI with the blood bank name and visit date
                            $('#bloodBankName').text(bloodBankNames);
                            $('#visitDate').text(event.visit_date);
                            $('#date').val(event.visit_date); // Set the form date field with the visit date
                            $('#tour_plan_id').val(selectedTPId); // Set the tour plan ID field
                            
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

   // Handle existing document deletion
   if (existingDocumentPreview) {
       existingDocumentPreview.addEventListener('click', function(e) {
           if (e.target && e.target.classList.contains('delete-existing-doc')) {
               const docPath = e.target.getAttribute('data-doc');
               const hiddenInput = document.createElement('input');
               hiddenInput.type = 'hidden';
               hiddenInput.name = 'documents_to_delete[]';
               hiddenInput.value = docPath;
               documentsToDeleteContainer.appendChild(hiddenInput);
               e.target.parentElement.remove();
           }
       });
   }

        // Handle new document selection and preview
        documentsInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            let filesProcessed = 0;

            if (files.length === 0) {
                return;
            }

            allSelectedFiles = allSelectedFiles.concat(files);
            console.log('allSelectedFiles', allSelectedFiles.length);

            files.forEach(file => {
                const fileReader = new FileReader();

                fileReader.onload = function(e) {
                    const fileURL = e.target.result;
                    let fileType = file.type;
                    const fileName = file.name;
                    const fileExtension = fileName.split('.').pop().toLowerCase();

                    if (!fileType) {
                        switch(fileExtension) {
                            case 'pdf':
                                fileType = 'application/pdf';
                                break;
                            case 'doc':
                            case 'docx':
                                fileType = 'application/msword';
                                break;
                            case 'xls':
                            case 'xlsx':
                                fileType = 'application/vnd.ms-excel';
                                break;
                            case 'csv':
                                fileType = 'text/csv';
                                break;
                            case 'txt':
                                fileType = 'text/plain';
                                break;
                            default:
                                fileType = 'application/octet-stream';
                        }
                    }

                    const previewItem = document.createElement('div');
                    previewItem.classList.add('preview-item');

                    const deleteBtn = document.createElement('button');
                    deleteBtn.classList.add('delete-btn');
                    deleteBtn.innerHTML = '&times;';
                    deleteBtn.title = 'Remove this file';
                    deleteBtn.addEventListener('click', function() {
                        newDocumentPreview.removeChild(previewItem);
                        removeFileFromInput(file);
                    });

                    previewItem.appendChild(deleteBtn);

                    if (fileType.startsWith('image/')) {
                        const img = document.createElement('img');
                        img.src = fileURL;
                        img.alt = file.name;
                        previewItem.appendChild(img);
                    } else {
                        const icon = document.createElement('i');
                        icon.classList.add('bi', ...getIconClass(fileExtension));
                        icon.classList.add('file-icon');
                        previewItem.appendChild(icon);

                        const fileNameSpan = document.createElement('span');
                        fileNameSpan.classList.add('file-name');
                        fileNameSpan.textContent = file.name;
                        previewItem.appendChild(fileNameSpan);
                    }

                    newDocumentPreview.appendChild(previewItem);

                    filesProcessed++;
                    if (filesProcessed === files.length) {
                        // Optionally clear the input value if needed
                        // documentsInput.value = '';
                    }
                };

                fileReader.onerror = function() {
                    console.error("Error reading file: " + file.name);
                    filesProcessed++;
                    if (filesProcessed === files.length) {
                        // Optionally clear the input value if needed
                        // documentsInput.value = '';
                    }
                };

                fileReader.readAsDataURL(file);
            });
        });

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

        // Add event listener for form submission
        if (form) {
            console.log('form inside');
            form.addEventListener('submit', function(event) {
                console.log('form submit triggered');  // This should now be logged
                event.preventDefault();

                // Clear the existing files in the documents input before appending new files
                const dataTransfer = new DataTransfer();
                allSelectedFiles.forEach(file => {
                    dataTransfer.items.add(file);
                });

                // Update the input field with the newly selected files
                documentsInput.files = dataTransfer.files;

                // Now submit the form manually
                form.submit();
            });
        }

    });


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
                              // Prepare document previews
                              let documentPreviews = '';
                              expense.documents.forEach(function(doc) {
                                  const docUrl = "{{ config('auth_api.base_image_url') }}" + doc.attachments;

                                  // Show preview for image files and a download link for others
                                  if (doc.attachments.match(/\.(jpg|jpeg|png|gif|svg)$/)) {
                                      documentPreviews += `
                                          <div class="preview-item">
                                              <a href="${docUrl}" target="_blank">
                                                  <img src="${docUrl}" alt="Document Preview" class="img-thumbnail" style="width: 50px; height: 50px;">
                                              </a>
                                          </div>
                                      `;
                                  } else {
                                      documentPreviews += `
                                          <div class="preview-item">
                                              <a href="${docUrl}" target="_blank">
                                                  <i class="bi bi-file-earmark-text-fill" style="font-size: 2rem;"></i>
                                              </a>
                                          </div>
                                      `;
                                  }
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
                                      <td>
                                          ${documentPreviews || '-'}
                                      </td>
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

            // Handle the delete button click event
        $(document).on('click', '.delete-expense', function() {
            const expenseId = $(this).data('expense-id');

            // Show SweetAlert2 confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with the delete request if the user confirms
                    deleteExpense(expenseId);
                }
            });
        });

        // Function to delete an expense
        function deleteExpense(expenseId) {
          // Generate the URL using route() function
          const deleteUrl = "{{ route('expenses.delete', ['id' => ':id']) }}".replace(':id', expenseId);

            $.ajax({
                url: deleteUrl, // Use the dynamically generated route URL
                type: 'DELETE',
                headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', 'The expense has been deleted.', 'success');
                        fetchExpenses(selectedGlobalTPId);
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error!', 'An error occurred while deleting the expense.', 'error');
                }
            });
        }
</script>

@endpush
