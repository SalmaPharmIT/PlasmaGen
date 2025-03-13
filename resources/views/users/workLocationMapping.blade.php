@extends('include.dashboardLayout')

@section('title', 'User Report Mapping')

@section('content')
<div class="pagetitle">
    <h1>Work Location Mapping</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('users.workLocationMapping') }}">Work Location Mapping</a></li>
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
              <h5 class="card-title">Work Location Mapping</h5>
              <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWorkLocationModal">
                  <i class="bi bi-plus me-1"></i> Add Work Location
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
                <th>Cities</th>
                <th>State</th>
                <th>Country</th>
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

<!-- Add Work Location Mapping Modal -->
<div class="modal fade" id="addWorkLocationModal" tabindex="-1" aria-labelledby="addWorkLocationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Adjust size as needed -->
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addWorkLocationModalLabel">Add Work Location Mapping</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Add Work Location Mapping Form -->
          <form class="row g-3 needs-validation" action="{{ route('users.workLocationMapping.submit') }}" method="POST" novalidate>
            @csrf
            <div class="row g-3">
              <!-- Manager Role Dropdown -->
              <div class="col-md-6 mb-3">
                <label for="role" class="form-label">Employee Role <span style="color:red">*</span></label>
                <select name="role" id="role" class="form-select select2">
                  <option value="">Select Role</option>
                  @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                  @endforeach
                </select>
              </div>
              <!-- Employee Dropdown -->
              <div class="col-md-6 mb-3">
                <label for="manager" class="form-label">Employee Name<span style="color:red">*</span></label>
                <select name="manager" id="manager" class="form-select select2">
                  <option value="">Select Employee</option>
                  {{-- Options loaded dynamically based on role --}}
                </select>
              </div>
              <!-- Country Dropdown -->
              <div class="col-md-6">
                <label for="country_id" class="form-label">Country <span class="text-danger">*</span></label>
                <select id="country_id" name="country_id" class="form-select select2" required>
                  <option value="">Choose Country</option>
                  @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                      {{ $country->name }}
                    </option>
                  @endforeach
                </select>
                <div class="invalid-feedback">
                  Please select a country.
                </div>
              </div>
              <!-- State Dropdown -->
              <div class="col-md-6">
                <label for="state_id" class="form-label">State <span class="text-danger">*</span></label>
                <select id="state_id" name="state_id" class="form-select select2" required>
                  <option value="">Choose State</option>
                  {{-- State options loaded via AJAX --}}
                </select>
                <div class="invalid-feedback">
                  Please select a state.
                </div>
              </div>
              <!-- City Dropdown (Multi-select) -->
              <div class="col-md-12">
                <label for="city_id" class="form-label">City <span class="text-danger">*</span></label>
                <select id="city_id" name="city_id[]" class="form-select select2" multiple required>
                  <option value="">Choose City</option>
                  {{-- City options loaded via AJAX --}}
                </select>
                <div class="invalid-feedback">
                  Please select at least one city.
                </div>
              </div>
              <!-- Submit and Cancel Buttons -->
              <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              </div>
            </div>
          </form>
          <!-- End Add Work Location Mapping Form -->
        </div>
      </div>
    </div>
</div>

<!-- Edit Work Location Mapping Modal -->
<div class="modal fade" id="editWorkLocationModal" tabindex="-1" aria-labelledby="editWorkLocationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg"> 
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="editWorkLocationModalLabel">Edit Work Location Mapping</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <form class="row g-3 needs-validation" id="editMappingForm" action="{{ route('users.workLocationMapping.editSubmit') }}" method="POST" novalidate>
                  @csrf
                  <!-- Hidden grouping keys -->
                  <input type="hidden" name="entity_id" id="edit_entity_id">
                  <input type="hidden" name="user_id" id="edit_user_id">
                  <input type="hidden" name="country_id" id="edit_country_id">
                  <input type="hidden" name="state_id" id="edit_state_id">
                  <div class="row g-3">
                      {{-- <!-- Display Employee (read-only) -->
                      <div class="col-md-6 mb-3">
                          <label for="edit_employee" class="form-label">Employee</label>
                          <input type="text" id="edit_employee" class="form-control" readonly>
                      </div>
                      <!-- Display Entity (read-only) -->
                      <div class="col-md-6 mb-3">
                          <label for="edit_entity_name" class="form-label">Entity</label>
                          <input type="text" id="edit_entity_name" class="form-control" readonly>
                      </div> --}}

                    <!-- Role Dropdown -->
                    <div class="col-md-6 mb-3">
                        <label for="edit_role" class="form-label">Employee Role <span style="color:red">*</span></label>
                        <select name="role" id="edit_role" class="form-select select2" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Employee Dropdown -->
                    <div class="col-md-6 mb-3">
                        <label for="edit_manager" class="form-label">Employee Name <span style="color:red">*</span></label>
                        <select name="manager" id="edit_manager" class="form-select select2" required>
                            <option value="">Select Employee</option>
                            {{-- Options will be loaded dynamically based on the selected role --}}
                        </select>
                    </div>
                      <!-- Country Dropdown -->
                      <div class="col-md-6">
                          <label for="edit_country" class="form-label">Country <span class="text-danger">*</span></label>
                          <select id="edit_country" name="country_id" class="form-select select2" required>
                              <option value="">Choose Country</option>
                              @foreach($countries as $country)
                              <option value="{{ $country->id }}">{{ $country->name }}</option>
                              @endforeach
                          </select>
                          <div class="invalid-feedback">Please select a country.</div>
                      </div>
                      <!-- State Dropdown -->
                      <div class="col-md-6">
                          <label for="edit_state" class="form-label">State <span class="text-danger">*</span></label>
                          <select id="edit_state" name="state_id" class="form-select select2" required>
                              <option value="">Choose State</option>
                              {{-- Loaded via AJAX --}}
                          </select>
                          <div class="invalid-feedback">Please select a state.</div>
                      </div>
                      <!-- City Dropdown (Multi-select) -->
                      <div class="col-md-12">
                          <label for="edit_city" class="form-label">City <span class="text-danger">*</span></label>
                          <select id="edit_city" name="city_id[]" class="form-select select2" multiple required>
                              <option value="">Choose City</option>
                              {{-- Loaded via AJAX --}}
                          </select>
                          <div class="invalid-feedback">Please select at least one city.</div>
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
  

@endsection

@push('scripts')
<script>
    $(document).ready(function() {

        var urlGetStatesByIdTemplate = "{{ route('api.states', ['countryId' => '__COUNTRY_ID__']) }}";
        var urlcityByStateIdTemplate = "{{ route('api.cities', ['stateId' => '__STATE_ID__']) }}";

        // Initialize DataTable for raw mapping data
        var mappingTable = $('#mappingTable').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('getUserWorkLocationMapping') }}",
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
                {
                    data: 'cities',
                    title: 'Cities',
                    render: function(data) {
                        // Render array of cities as tags
                        if (data && data.length > 0) {
                            return data.map(function(city) {
                                return `<span class="badge bg-info text-dark me-1">${city}</span>`;
                            }).join('');
                        }
                        return 'N/A';
                    }
                },
                { data: 'state_name', title: 'State' },
                { data: 'country_name', title: 'Country' },
                { 
                    data: null,
                    title: 'Actions',
                    render: function(data, type, row) {
                       // Build an object with the grouping keys
                        var groupKeys = {
                            entity_id: row.entity_id,
                            user_id: row.user_id,
                            state_id: row.state_id,
                            country_id: row.country_id
                        };

                        return `
                           <div class="dropdown">
                               <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                   <i class="bi bi-gear"></i>
                               </button>
                               <ul class="dropdown-menu">
                                   <li><a href="#" class="dropdown-item editMapping" data-mapping='${JSON.stringify(row)}'>Edit</a></li>
                                   <li><a href="#" class="dropdown-item deleteMapping" data-group='${JSON.stringify(groupKeys)}'>Delete</a></li>
                               </ul>
                           </div>
                       `;
                    },
                    orderable: false,
                    searchable: false
                }
            ],
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
                            managerSelect.empty().append('<option value="">Select Employee</option>');
                            $.each(response.data, function(index, employee){
                                managerSelect.append('<option value="'+employee.id+'">'+employee.name+'</option>');
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error){
                        console.error("Error fetching employees for manager role:", error);
                    }
                });
            } else {
                $('#manager').empty().append('<option value="">Select Employee</option>');
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
                        cityDropdown.empty().append('<option value="">Choose City</option>');
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
                        editCityDropdown.empty().append('<option value="">Choose City</option>');
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

        // When an Edit button is clicked, populate the edit modal with row data
        $('#mappingTable').on('click', '.editMapping', function(e) {
                e.preventDefault();
                var mapping = $(this).data('mapping');
                console.log("edit mapping: ", mapping);
                
                // Populate hidden fields and read-only fields if needed
                $('#edit_entity_id').val(mapping.entity_id);
                $('#edit_user_id').val(mapping.user_id);
                $('#edit_employee').val(mapping.user_name);
                $('#edit_entity_name').val(mapping.entity_name);
                
                // Store values in variables for use in callbacks
                var selectedCountry = mapping.country_id;
                var selectedState   = mapping.state_id;
                var selectedCities  = mapping.city_ids; // Expecting an array of city IDs
                window.editMappingEmployeeId = mapping.user_id; // For employee dropdown
                
                // Set role dropdown (employee role) and trigger change to load employee options
                $('#edit_role').val(mapping.role_id).trigger('change');

                // For country -> state -> city, chain your AJAX calls:

                // Trigger country change to load states
                $('#edit_country').val(selectedCountry).trigger('change');
                var urlGetStates = urlGetStatesByIdTemplate.replace('__COUNTRY_ID__', selectedCountry);
                // Override the edit_country change handler to set the state value once states are loaded:
                $.ajax({
                    url: urlGetStates,
                    type: 'GET',
                    success: function(data) {
                        var editStateDropdown = $('#edit_state');
                        editStateDropdown.empty().append('<option value="">Choose State</option>');
                        if (data.success) {
                            $.each(data.data, function(index, state) {
                                editStateDropdown.append(`<option value="${state.id}">${state.name}</option>`);
                            });
                            // Set the state value and trigger change to load cities
                            editStateDropdown.val(selectedState).trigger('change');
                            var cityUrl = urlcityByStateIdTemplate.replace('__STATE_ID__', selectedState);
                            // Now fetch and populate cities for the selected state
                            $.ajax({
                                url: cityUrl,
                                type: 'GET',
                                success: function(cityData) {
                                    var editCityDropdown = $('#edit_city');
                                    editCityDropdown.empty().append('<option value="">Choose City</option>');
                                    if (cityData.success) {
                                        $.each(cityData.data, function(index, city) {
                                            editCityDropdown.append(`<option value="${city.id}">${city.name}</option>`);
                                        });
                                        // Finally, set the city dropdown with the mapping's city IDs
                                        editCityDropdown.val(selectedCities).trigger('change');
                                    } else {
                                        alert(cityData.message || 'No cities available for the selected state.');
                                    }
                                },
                                error: function(error) {
                                    console.error("Error fetching cities:", error);
                                    alert('Failed to fetch cities.');
                                }
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

                // Open the Edit modal (after the above asynchronous calls)
                $('#editWorkLocationModal').modal('show');
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



        // For the Edit modal, when the state dropdown changes, update the city dropdown
        $('#edit_state').on('change', function() {
            var stateId = $(this).val();
            var editCityDropdown = $('#edit_city');
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
                            // After cities are loaded, if we have stored city IDs, select them.
                            if(window.editMappingCityIds){
                                editCityDropdown.val(window.editMappingCityIds).trigger('change');
                            }
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



        // Delete Mapping handler
        $('#mappingTable').on('click', '.deleteMapping', function(e) {
            e.preventDefault();
            var groupData = $(this).data('group'); // This is our grouping keys object

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to delete this mapping?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('users.workLocationMapping.delete') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            entity_id: groupData.entity_id,
                            user_id: groupData.user_id,
                            state_id: groupData.state_id,
                            country_id: groupData.country_id
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', response.message, 'success');
                                $('#mappingTable').DataTable().ajax.reload();
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error deleting mapping:", error);
                            Swal.fire('Error!', 'An error occurred while deleting the mapping.', 'error');
                        }
                    });
                }
            });
        });

       


    });
</script>
@endpush
