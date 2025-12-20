@extends('include.dashboardLayout')

@section('title', 'User Report Mapping')

@section('content')
<div class="pagetitle">
    <h1>User KiloMeter Mapping</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('users.kmMapping') }}">KiloMeter Mapping</a></li>
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
              <h5 class="card-title">KiloMeter & Price Mapping</h5>
              <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWorkLocationModal">
                  <i class="bi bi-plus me-1"></i> Add KM
              </a>
          </div>

          <!-- Success & Error Messages -->
          @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="bi bi-check-circle me-1"></i>
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif
          @if($errors->any())
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="bi bi-exclamation-octagon me-1"></i>
              {{ implode(' ', $errors->all()) }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          <!-- Aggregated Mapping Data Table (Raw Data from API) -->
          <table id="mappingTable" class="table table-striped table-bordered col-lg-12">
            <thead>
              <tr>
                <th>ID</th>
                <th>Employee</th>
                <th>Role</th>
                <th>Max KM Allocated</th>
                <th>Price Per KM</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {{-- Data populated via AJAX --}}
            </tbody>
          </table>
          <!-- End Mapping Data Table -->
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Add KM Modal -->
<div class="modal fade" id="addWorkLocationModal" tabindex="-1" aria-labelledby="addWorkLocationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addWorkLocationModalLabel">Add KiloMeter & Price Mapping</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form class="row g-3 needs-validation" action="{{ route('users.kmMapping.submit') }}" method="POST" novalidate>
          @csrf

          <div class="row g-3">

           <!-- Employee Role (Single Select) -->
            <div class="col-md-6 mb-3">
                <label for="role" class="form-label">Employee Role <span class="text-danger">*</span></label>
                <select name="role" id="role" class="form-select select2" required>
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Please select employee role.</div>
            </div>

            <!-- Employee Name (Multi-Select With Select All) -->
            <div class="col-md-6 mb-3">
                <label for="manager" class="form-label">Employee Name <span style="color:red">*</span></label>
                <select name="manager[]" id="manager" class="form-select select2" multiple required>
                    <option value="all">Select All</option>
                    {{-- Will load via AJAX on role change --}}
                </select>
                <div class="invalid-feedback">Please select employee name(s).</div>
            </div>

             <!-- Max KM Allocated -->
            <div class="col-md-6 mb-3">
                <label for="max_km" class="form-label">Max KM Allocated <span class="text-danger">*</span></label>
                <input type="number" id="max_km" name="max_km" class="form-control" required>
                <div class="invalid-feedback">Enter valid KM value.</div>
            </div>

            <!-- Price Per KM -->
            <div class="col-md-6 mb-3">
                <label for="price_per_km" class="form-label">Price Per KM <span class="text-danger">*</span></label>
                <input type="number" step="0.01" id="price_per_km" name="price_per_km" class="form-control" required>
                <div class="invalid-feedback">Enter valid price.</div>
            </div>

            <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>

          </div>
        </form>
      </div>

    </div>
  </div>
</div>


<!-- EDIT KM MODAL -->
<div class="modal fade" id="editKmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit KM Mapping</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editKmForm" method="POST" action="{{ route('users.kmMapping.editSubmit') }}">
                @csrf

                <input type="hidden" id="edit_id" name="id">
                <input type="hidden" id="edit_entity_id" name="entity_id">

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Max KM</label>
                        <input type="number" class="form-control" id="edit_max_km" name="max_km" required>
                    </div>

                    <div class="mb-3">
                        <label>Price Per KM</label>
                        <input type="number" step="0.01" class="form-control" id="edit_price_per_km" name="price_per_km" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>

            </form>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
    $(document).ready(function() {

        var urlGetStatesByIdTemplate = "{{ route('api.states', ['countryId' => '__COUNTRY_ID__']) }}";
        var urlcityByStateIdTemplate = "{{ route('api.citiesById', ['stateId' => '__STATE_ID__']) }}";
        var urlBloodbanksByCityTemplate = "{{ url('users/workLocationMapping/bloodbanks') }}/__CITY_ID__";

        // Initialize DataTable for raw mapping data
        var mappingTable = $('#mappingTable').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('getUseKMMapping') }}",
                type: 'GET',
                dataSrc: function(json) {
                    if (json.success) {
                        return json.data;
                    } else {
                        Swal.fire('Error', json.message, 'error');
                        return [];
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching mapping data:", error);
                    Swal.fire('Error', 'An error occurred while fetching the mapping data.', 'error');
                }
            },
            columns: [
                { 
                    data: null,
                    title: 'SI. No',
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'user_name', title: 'Employee' },
                { data: 'role_name', title: 'Role' },
                { data: 'max_km', title: 'Max KM Allocated' },
                { data: 'price_per_km', title: 'Price Per KM' },
                { 
                    data: null,
                    title: 'Action',
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-sm btn-primary editKm" 
                                data-id="${row.id}"
                                data-entity="${row.entity_id}"
                                data-maxkm="${row.max_km}"
                                data-price="${row.price_per_km}">
                                Edit
                            </button>

                            <button class="btn btn-sm btn-danger deleteKm" 
                                data-id="${row.id}" 
                                data-entity="${row.entity_id}">
                                Delete
                            </button>
                        `;
                    }
                }
            ],
            order: [[0, 'asc']], // Sort by the first column (ID) in descending order
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100]
        });

        // Bootstrap's custom validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
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
        })();

        // Initialize Select2 for modal elements
        $('#addWorkLocationModal').on('shown.bs.modal', function () {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: $('#addWorkLocationModal')
            });

            // Set default country to India (ID = 1) and trigger change event to fetch states
            $('#country_id').val(1).trigger('change');
        });

        // $('#editWorkLocationModal').on('shown.bs.modal', function () {
        //     $('.select2').select2({
        //         theme: 'bootstrap-5',
        //         width: '100%',
        //         dropdownParent: $('#editWorkLocationModal')
        //     });
        // });

        // Destroy Select2 and reset form on modal hide
        $('#addWorkLocationModal').on('hidden.bs.modal', function () {
            $('.select2').select2('destroy');
            $(this).find('form')[0].reset();
            $(this).find('form').removeClass('was-validated');
        });

        // When Manager Role changes, update Employee dropdown
        $('#role').on('change', function(){
            var roleId = $(this).val();
            if(roleId) {
                $.ajax({
                    url: "{{ route('getEmployeeByRoleId', ['roleId' => ':roleId']) }}".replace(':roleId', roleId),
                    type: 'GET',
                    success: function(response){
                        if(response.success){
                            var managerSelect = $('#manager');
                            // Reset dropdown
                            managerSelect.empty();

                            // ALWAYS add Select All option
                            managerSelect.append('<option value="all">Select All</option>');

                            // Add employees
                            $.each(response.data, function(index, employee){
                                managerSelect.append('<option value="'+employee.id+'">'+employee.name+'</option>');
                            });

                            // Refresh Select2
                            managerSelect.trigger('change');
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error){
                        console.error("Error fetching employees for manager role:", error);
                    }
                });
            } else {
                 $('#manager').empty().append('<option value="all">Select All</option>');
            }
        });

        // Define dropdown variables for Country, State and City
        var countryDropdown = $('#country_id');
        var stateDropdown = $('#state_id');
        var cityDropdown = $('#city_id');

        // When Country changes, update State dropdown and reset City dropdown
        countryDropdown.on('change', function() {
            var countryId = $(this).val();
            console.log("Fetching states for countryId:", countryId);
            var urlGetStates = urlGetStatesByIdTemplate.replace('__COUNTRY_ID__', countryId);
            if (countryId) {
                $.ajax({
                    url: urlGetStates,
                    type: 'GET',
                    success: function(data) {
                        console.log("States fetched:", data);
                        stateDropdown.empty().append('<option value="">Choose State</option>');
                        cityDropdown.empty().append('<option value="">Choose City</option>');
                        if (data.success) {
                            data.data.forEach(function(state) {
                                stateDropdown.append(`<option value="${state.id}">${state.name}</option>`);
                            });
                        } else {
                            alert(data.message || 'No states available for the selected country.');
                        }
                    },
                    error: function(error) {
                        console.error("Error fetching states:", error);
                        alert('Failed to fetch states. Please check the server logs for details.');
                    }
                });
            } else {
                stateDropdown.empty().append('<option value="">Choose State</option>');
                cityDropdown.empty().append('<option value="">Choose City</option>');
            }
        });

        // When State changes, update City dropdown
        stateDropdown.on('change', function() {
            var stateId = $(this).val();
            console.log("Fetching cities for stateId:", stateId);
            
            var cityUrl = urlcityByStateIdTemplate.replace('__STATE_ID__', stateId);
            if (stateId) {
                $.ajax({
                    url: cityUrl,
                    type: 'GET',
                    success: function(data) {
                      cityDropdown.empty().append('<option value="all">Select All</option>');
                        if (data.success) {
                            data.data.forEach(function(city) {
                                cityDropdown.append(`<option value="${city.id}">${city.name}</option>`);
                            });
                        } else {
                            alert(data.message || 'No cities available for the selected state.');
                        }
                    },
                    error: function(error) {
                        console.error("Error fetching cities:", error);
                        alert('Failed to fetch cities. Please try again.');
                    }
                });
            } else {
                cityDropdown.empty().append('<option value="">Choose City</option>');
            }
        });


        // Edit Mapping Modal section
        // Define dropdown variables for Edit modal
        var editCountryDropdown = $('#edit_country');
        var editStateDropdown = $('#edit_state');
        var editCityDropdown = $('#edit_city');

        editCountryDropdown.on('change', function() {
            var countryId = $(this).val();
            var urlGetStates = urlGetStatesByIdTemplate.replace('__COUNTRY_ID__', countryId);
            if (countryId) {
                $.ajax({
                    url: urlGetStates,
                    type: 'GET',
                    success: function(data) {
                        editStateDropdown.empty().append('<option value="">Choose State</option>');
                       editCityDropdown.empty().append('<option value="all">Select All</option>');
                        if (data.success) {
                            data.data.forEach(function(state) {
                                editStateDropdown.append(`<option value="${state.id}">${state.name}</option>`);
                            });
                        } else {
                            alert(data.message || 'No states available for the selected country.');
                        }
                    },
                    error: function(error) {
                        console.error("Error fetching states:", error);
                        alert('Failed to fetch states.');
                    }
                });
            } else {
                editStateDropdown.empty().append('<option value="">Choose State</option>');
                editCityDropdown.empty().append('<option value="">Choose City</option>');
            }
        });

        editStateDropdown.on('change', function() {
            var stateId = $(this).val();
            var cityUrl = urlcityByStateIdTemplate.replace('__STATE_ID__', stateId);
            if (stateId) {
                $.ajax({
                    url: cityUrl,
                    type: 'GET',
                    success: function(data) {
                        editCityDropdown.empty().append('<option value="">Choose City</option>');
                        if (data.success) {
                            data.data.forEach(function(city) {
                                editCityDropdown.append(`<option value="${city.id}">${city.name}</option>`);
                            });
                        } else {
                            alert(data.message || 'No cities available for the selected state.');
                        }
                    },
                    error: function(error) {
                        console.error("Error fetching cities:", error);
                        alert('Failed to fetch cities.');
                    }
                });
            } else {
                editCityDropdown.empty().append('<option value="">Choose City</option>');
            }
        });
 
        // ------------------ SELECT ALL HANDLER ---------------------

        function enableSelectAll(selectElementId) {
            $(document).on('change', selectElementId, function () {
                let selectedValues = $(this).val();

                if (selectedValues && selectedValues.includes("all")) {
                    // Remove "all"
                    let allOptions = [];
                    $(`${selectElementId} option`).each(function () {
                        let val = $(this).val();
                        if (val !== "all") {
                            allOptions.push(val);
                        }
                    });

                    // Select everything
                    $(this).val(allOptions).trigger("change");
                }
            });
        }

        // Enable for both ADD & EDIT dropdowns
        enableSelectAll('#city_id');
        enableSelectAll('#edit_city');


        // When an Edit button is clicked, populate the edit modal with row data
       $('#mappingTable').on('click', '.editKm', function () {
            let id      = $(this).data('id');
            let entity  = $(this).data('entity');
            let maxkm   = $(this).data('maxkm');
            let price   = $(this).data('price');

            $('#edit_id').val(id);
            $('#edit_entity_id').val(entity);
            $('#edit_max_km').val(maxkm);
            $('#edit_price_per_km').val(price);

            $('#editKmModal').modal('show');
        });

            // For the role change, update the employee dropdown as before:
        $('#edit_role').on('change', function(){
                var roleId = $(this).val();
                if(roleId) {
                    $.ajax({
                        url: "{{ route('getEmployeeByRoleId', ['roleId' => ':roleId']) }}".replace(':roleId', roleId),
                        type: 'GET',
                        success: function(response){
                            console.log("Employee AJAX response:", response);
                            if(response.success){
                                var managerSelect = $('#edit_manager');
                                managerSelect.empty().append('<option value="">Select Employee</option>');
                                $.each(response.data, function(index, employee){
                                    managerSelect.append('<option value="'+employee.id+'">'+employee.name+'</option>');
                                });
                                // Set the employee dropdown value if it was stored from the mapping data
                                if(window.editMappingEmployeeId) {
                                    managerSelect.val(window.editMappingEmployeeId).trigger('change');
                                }
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function(xhr, status, error){
                            console.error("Error fetching employees for role:", error);
                        }
                    });
                } else {
                    $('#edit_manager').empty().append('<option value="">Select Employee</option>');
                }
        });


        // Delete KM Mapping handler
        $('#mappingTable').on('click', '.deleteKm', function () {

            let id = $(this).data('id');
            let entity = $(this).data('entity');

            Swal.fire({
                title: "Are you sure?",
                text: "Delete this KM mapping?",
                icon: "warning",
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {

                    $.post("{{ route('users.kmMapping.delete') }}", {
                        id: id,
                        entity_id: entity,
                        _token: "{{ csrf_token() }}"
                    }, function (resp) {

                        if (resp.success) {
                            Swal.fire("Deleted!", resp.message, "success");
                            $('#mappingTable').DataTable().ajax.reload();
                        } else {
                            Swal.fire("Error!", resp.message, "error");
                        }
                    });
                }
            });
        });
    
        // KiloMeter Assign
       // SELECT ALL logic only for employee multi select
        function enableSelectAllForEmployees() {
            $(document).on("change", "#manager", function () {
                let selected = $(this).val();

                if (selected && selected.includes("all")) {
                    let all = [];
                    $("#manager option").each(function () {
                        if ($(this).val() !== "all") all.push($(this).val());
                    });

                    $("#manager").val(all).trigger("change");
                }
            });
        }
        enableSelectAllForEmployees();


    });
</script>
@endpush
