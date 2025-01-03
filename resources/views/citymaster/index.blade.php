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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCityModal">
                    <i class="bi bi-plus me-1"></i> Add City
                </button>
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
                  <th>State</th>
                  <th>Pin Code</th>
                  <th>Latitude</th>
                  <th>Longitude</th>
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

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
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
                { 
                    data: 'state',
                    render: function(data, type, row) {
                        return data?.name || 'N/A'; // Adjust based on your API response
                    }
                },
                { data: 'pin_code', defaultContent: 'N/A' },
                { data: 'latitude', defaultContent: 'N/A' },
                { data: 'longitude', defaultContent: 'N/A' },
                // { data: 'created_by', defaultContent: 'N/A' },
                // { data: 'modified_by', defaultContent: 'N/A' }
            ],
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            language: {
                emptyTable: "No cities available.",
                // Customize language options if needed
            }
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
    });
</script>
@endpush
