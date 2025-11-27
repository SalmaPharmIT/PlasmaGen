<!-- resources/views/citymaster/index.blade.php -->

@extends('include.dashboardLayout')

@section('title', 'View Cities')

@section('content')

<div class="pagetitle">
    <h1>View Cities</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('citymaster.index') }}">Cities</a></li>
        <li class="breadcrumb-item active">View</li>
      </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">

    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
           
           <!-- Header with Button -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title">View Cities</h5>
                {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCityModal">
                    <i class="bi bi-plus me-1"></i> Add City
                </button> --}}

                 <div class="d-flex gap-2">
                  <button id="exportButton" class="btn btn-info">
                      <i class="bi bi-download me-1"></i> Export
                  </button>
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCityModal">
                      <i class="bi bi-plus me-1"></i> Add City
                  </button>
              </div>
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

            <!-- Cities DataTable -->
            <table id="citiesTable" class="table table-striped table-bordered col-lg-12">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>State ID</th>
                  <th>State</th>
                  <th>Pin Code</th>
                  <th>Latitude</th>
                  <th>Longitude</th>
                  <th>Action</th>
                  {{-- <th>Created By</th>
                  <th>Modified By</th> --}}
                </tr>
              </thead>
              <tbody>
                {{-- Data will be populated by DataTables via AJAX --}}
              </tbody>
            </table>
            <!-- End Cities DataTable -->

          </div>
        </div>

      </div>
    </div>
    
</section>


<!-- Add City Modal -->
<div class="modal fade" id="addCityModal" tabindex="-1" aria-labelledby="addCityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Adjust size as needed -->
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addCityModalLabel">Add New City</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Add City Form -->
          <form class="row g-3 needs-validation" action="{{ route('citymaster.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf <!-- CSRF Token -->
            <div class="row g-3">
              <!-- City Name -->
              <div class="col-md-6">
                <label for="city_name" class="form-label">City Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="city_name" name="name" value="{{ old('name') }}" required>
                <div class="invalid-feedback">
                  Please enter the city name.
                </div>
              </div>

              <!-- State -->
              <div class="col-md-6">
                <label for="state_id" class="form-label">State <span class="text-danger">*</span></label>
                <select id="state_id" name="state_id" class="form-select select2" required>
                  <option value="">Choose State</option>
                  @foreach($states as $state)
                    <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>
                        {{ $state->name }}
                    </option>
                  @endforeach
                </select>
                <div class="invalid-feedback">
                  Please select a state.
                </div>
              </div>

              <!-- Pin Code -->
              <div class="col-md-6">
                <label for="pin_code" class="form-label">Pin Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="pin_code" name="pin_code" value="{{ old('pin_code') }}" required>
                <div class="invalid-feedback">
                  Please enter the pin code.
                </div>
              </div>

              <!-- Latitude -->
              <div class="col-md-6">
                <label for="latitude" class="form-label">Latitude</label>
                <input type="number" step="0.000001" class="form-control" id="latitude" name="latitude" value="{{ old('latitude') }}">
                <div class="invalid-feedback">
                  Please enter a valid latitude.
                </div>
              </div>

              <!-- Longitude -->
              <div class="col-md-6">
                <label for="longitude" class="form-label">Longitude</label>
                <input type="number" step="0.000001" class="form-control" id="longitude" name="longitude" value="{{ old('longitude') }}">
                <div class="invalid-feedback">
                  Please enter a valid longitude.
                </div>
              </div>
              
              <!-- Add More Fields as Needed -->
              
              <!-- Submit and Reset Buttons -->
              <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
              </div>
            </div>
          </form>
          <!-- End Add City Form -->
        </div>
      </div>
    </div>
  </div>
  <!-- End Add City Modal -->



<!-- Edit City Modal -->
<div class="modal fade" id="editCityModal" tabindex="-1" aria-labelledby="editCityModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editCityModalLabel">Edit City</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editCityForm" class="row g-3 needs-validation" method="POST" enctype="multipart/form-data" novalidate>
          @csrf
          @method('PUT')
          <input type="hidden" name="city_id" id="edit_city_id">
          <div class="row g-3">
            <!-- City Name -->
            <div class="col-md-6">
              <label for="edit_city_name" class="form-label">City Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="edit_city_name" name="name" required>
              <div class="invalid-feedback">
                Please enter the city name.
              </div>
            </div>
            <!-- State -->
            <div class="col-md-6">
              <label for="edit_state_id" class="form-label">State <span class="text-danger">*</span></label>
              <select id="edit_state_id" name="state_id" class="form-select select2" required>
                  <option value="">Choose State</option>
                  @foreach($states as $state)
                      <option value="{{ $state->id }}">{{ $state->name }}</option>
                  @endforeach
              </select>
              <div class="invalid-feedback">
                Please select a state.
              </div>
            </div>
            <!-- Pin Code -->
            <div class="col-md-6">
              <label for="edit_pin_code" class="form-label">Pin Code <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="edit_pin_code" name="pin_code" required>
              <div class="invalid-feedback">
                Please enter the pin code.
              </div>
            </div>
            <!-- Latitude -->
            <div class="col-md-6">
              <label for="edit_latitude" class="form-label">Latitude</label>
              <input type="number" step="0.000001" class="form-control" id="edit_latitude" name="latitude">
              <div class="invalid-feedback">
                Please enter a valid latitude.
              </div>
            </div>
            <!-- Longitude -->
            <div class="col-md-6">
              <label for="edit_longitude" class="form-label">Longitude</label>
              <input type="number" step="0.000001" class="form-control" id="edit_longitude" name="longitude">
              <div class="invalid-feedback">
                Please enter a valid longitude.
              </div>
            </div>
            <!-- Location Action Buttons -->
            <div class="col-md-12 mt-2">
                <button type="button" id="editViewLocationBtn" class="btn btn-info me-2">
                    <i class="bi bi-geo-alt"></i> View Location
                </button>

                <button type="button" id="editClearLocationBtn" class="btn btn-danger">
                    <i class="bi bi-x-circle"></i> Clear Location
                </button>
            </div>
            <!-- Submit and Cancel Buttons -->
            <div class="col-12 text-center">
              <button type="submit" class="btn btn-primary">Update</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- End Edit City Modal -->


@endsection

@push('scripts')

<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/2.3.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.5/js/buttons.bootstrap5.min.js"></script>

<!-- JSZip for Excel/CSV -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- HTML5 export -->
<script src="https://cdn.datatables.net/buttons/2.3.5/js/buttons.html5.min.js"></script>

<!-- Print (optional) -->
<script src="https://cdn.datatables.net/buttons/2.3.5/js/buttons.print.min.js"></script>


<script>
    $(document).ready(function() {

       // Create a URL template for updating a city with a placeholder for the id
      var updateUrlTemplate = "{{ route('citymaster.update', ['id' => ':id']) }}";
      var destroyUrlTemplate = "{{ route('citymaster.destroy', ['id' => ':id']) }}";

        // Initialize DataTable
        var table = $('#citiesTable').DataTable({
            responsive: true,
            processing: true,
            serverSide: false, // Set to true if implementing server-side processing
            ajax: {
                url: "{{ route('api.cities') }}",
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
                    console.error("Error fetching cities:", error);
                    Swal.fire('Error', 'An error occurred while fetching the data.', 'error');
                }
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'state_id' },
                { 
                    data: 'state',
                    render: function(data, type, row) {
                        return data?.state_name  || 'N/A'; // Adjust based on your API response
                    }
                },
                { data: 'pin_code', defaultContent: 'N/A' },
                { data: 'latitude', defaultContent: 'N/A' },
                { data: 'longitude', defaultContent: 'N/A' },
                { // Action column
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                           <button class="btn btn-warning btn-sm editCity" 
                                   data-id="${row.id}" 
                                   data-name="${row.name}" 
                                   data-state-id="${row.state_id}"
                                   data-pin-code="${row.pin_code}" 
                                   data-latitude="${row.latitude}" 
                                   data-longitude="${row.longitude}">
                                <i class="bi bi-pencil-square"></i> Edit
                           </button>
                           <button class="btn btn-danger btn-sm deleteCity" data-id="${row.id}">
                                <i class="bi bi-trash"></i> Delete
                           </button>
                        `;
                    }
                }
                // { data: 'created_by', defaultContent: 'N/A' },
                // { data: 'modified_by', defaultContent: 'N/A' }
            ],
            order: [[0, 'desc']], // Sort by the first column (ID) in descending order
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            language: {
                emptyTable: "No cities available.",
                // Customize language options if needed
            },
            //  dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'City Master',
                    exportOptions: {
                        columns: ':not(:last-child)' // ⬅️ Exclude the last column (Action)
                    }
                }
            ]
        });

        // Bootstrap's custom validation
        (function () {
          'use strict'

          // Fetch all the forms we want to apply custom Bootstrap validation styles to
          var forms = document.querySelectorAll('.needs-validation')

          // Loop over them and prevent submission
          Array.prototype.slice.call(forms)
            .forEach(function (form) {
              form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                  event.preventDefault()
                  event.stopPropagation()
                }

                form.classList.add('was-validated')
              }, false)
            })
        })()

          // Initialize Select2 for elements within the modal with dropdownParent set to the modal
          $('#addCityModal').on('shown.bs.modal', function () {
            $('#state_id').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Choose State',
                allowClear: true,
                dropdownParent: $('#addCityModal')
            });
        });

        // Optional: Destroy Select2 when modal is hidden to prevent duplicate initialization
         // Destroy Select2 and reset the form when the modal is hidden
         $('#addCityModal').on('hidden.bs.modal', function () {
            // Destroy Select2 to prevent duplication
            $('#state_id').select2('destroy');

            // Reset the form fields
            $(this).find('form')[0].reset();

            // Remove validation classes
            $(this).find('form').removeClass('was-validated');

            // Optionally, remove any additional validation feedback
            $(this).find('.invalid-feedback').hide();
        });



         // Initialize Select2 for Edit City Modal when shown
         $('#editCityModal').on('shown.bs.modal', function () {
            $('#edit_state_id').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Choose State',
                allowClear: true,
                dropdownParent: $('#editCityModal')
            });
        });
        $('#editCityModal').on('hidden.bs.modal', function () {
            $('#edit_state_id').select2('destroy');
            $(this).find('form')[0].reset();
            $(this).find('form').removeClass('was-validated');
        });

        // Edit City button click event
        $('#citiesTable').on('click', '.editCity', function() {
            let btn = $(this);

            let cityId = btn.data('id');

            // Replace the placeholder with the actual id
            let updateUrl = updateUrlTemplate.replace(':id', cityId);

            // Set the form action to the dynamically generated URL
            $('#editCityForm').attr('action', updateUrl);

            // Fill in the form fields from data attributes
            $('#edit_city_id').val(btn.data('id'));
            $('#edit_city_name').val(btn.data('name'));
            $('#edit_state_id').val(btn.data('state-id')).trigger('change');
            $('#edit_pin_code').val(btn.data('pin-code'));
            $('#edit_latitude').val(btn.data('latitude'));
            $('#edit_longitude').val(btn.data('longitude'));
            // Set form action (assuming a named route 'citymaster.update')
            // $('#editCityForm').attr('action', '/citymaster/' + btn.data('id'));

            // Show the edit modal
            $('#editCityModal').modal('show');
        });

        // Delete City button click event with confirmation
        $('#citiesTable').on('click', '.deleteCity', function() {
            let cityId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to delete this city?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {

                    // Replace the placeholder in the template with the actual id
                    let deleteUrl = destroyUrlTemplate.replace(':id', cityId);

                    // AJAX call to delete the city (assuming DELETE route 'citymaster.destroy')
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if(response.success) {
                                Swal.fire('Deleted!', 'City has been deleted.', 'success');
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Error', 'An error occurred while deleting the city.', 'error');
                        }
                    });
                }
            });
        });

         $('#exportButton').on('click', function() {
            table.button(0).trigger(); // Triggers the first button (Excel export)
        });

        // ==========================
        // VIEW LOCATION BUTTON
        // ==========================
        $("#editViewLocationBtn").click(function () {
            let lat = $("#edit_latitude").val();
            let lng = $("#edit_longitude").val();

            let latNum = parseFloat(lat);
            let lngNum = parseFloat(lng);

            // Validate empty or zero values
            if (!lat || !lng || latNum === 0 || lngNum === 0 || isNaN(latNum) || isNaN(lngNum)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Location',
                    text: 'Latitude and Longitude must be valid and non-zero to view the location.'
                });
                return;
            }

            const url = `https://www.google.com/maps?q=${lat},${lng}`;
            window.open(url, "_blank");
        });


        // ==========================
        // CLEAR LOCATION BUTTON
        // ==========================
        $("#editClearLocationBtn").click(function () {
            Swal.fire({
                title: "Are you sure?",
                text: "This will clear both latitude and longitude. Click Submit to save the changes.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, clear it"
            }).then((result) => {
                if (result.isConfirmed) {

                    $("#edit_latitude").val("");
                    $("#edit_longitude").val("");

                    Swal.fire({
                        icon: "success",
                        title: "Cleared",
                        text: "Latitude and Longitude cleared. Click Submit/Update to save."
                    });
                }
            });
        });


    });
</script>
@endpush
