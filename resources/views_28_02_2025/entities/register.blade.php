@extends('include.dashboardLayout')

@section('title', 'Entity Registration')

@section('content')

<div class="pagetitle">
    <h1>Entity Registration</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('entities.index') }}">Entities</a></li>
        <li class="breadcrumb-item active">Regitration</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Add New Entity</h5>


            <!-- Display Success Message -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Display Error Messages -->
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Entity Registration Form -->
            <form class="row g-3" action="{{ route('entity.register.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Name -->
                <div class="col-md-6">
                    <label for="name" class="form-label">Entity Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                </div>

              <!-- Entity Type -->
            <div class="col-md-6">
                <label for="entity_type_id" class="form-label">Entity Type</label>
                <select id="entity_type_id" name="entity_type_id" class="form-select select2" required>
                    <option value="">Choose entity type</option>
                    @foreach($entityTypes as $type)
                        <option value="{{ $type->id }}" {{ old('entity_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->entity_name }}
                        </option>
                    @endforeach
                </select>
            </div>

             <!-- Parent Entity (Initially Hidden) -->
             <div class="col-md-6 d-none" id="parent_entity_div">
                <label for="parent_entity_id" class="form-label">Parent Entity</label>
                <select id="parent_entity_id" name="parent_entity_id" class="form-select select2">
                    <option value="">Choose Parent Entity</option>
                    <!-- Options will be populated via AJAX -->
                </select>
            </div>

                <!-- Entity Licence Number -->
                <div class="col-md-6">
                    <label for="entity_license_number" class="form-label">Entity Licence Number</label>
                    <input type="text" class="form-control" id="entity_license_number" name="entity_license_number" value="{{ old('entity_license_number') }}" required>
                </div>

                  <!-- Email -->
                  <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                </div>

                <!-- Mobile No -->
                <div class="col-md-6">
                    <label for="mobile_no" class="form-label">Mobile No</label>
                    <input type="text" class="form-control" id="mobile_no" name="mobile_no" value="{{ old('mobile_no') }}" required>
                </div>

                <!-- PAN ID -->
                <div class="col-md-6">
                    <label for="pan_id" class="form-label">PAN ID</label>
                    <input type="text" class="form-control" id="pan_id" name="pan_id" value="{{ old('pan_id') }}" required>
                </div>

                <!-- Country -->
                <div class="col-md-6">
                    <label for="country_id" class="form-label">Country</label>
                    <select id="country_id" name="country_id" class="form-select select2" required>
                        <option value="">Choose Country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- State -->
                <div class="col-md-6">
                    <label for="state_id" class="form-label">State</label>
                    <select id="state_id" name="state_id" class="form-select select2" required>
                        <option value="">Choose State</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                {{ $state->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- City -->
                <div class="col-md-6">
                    <label for="city_id" class="form-label">City</label>
                    <select id="city_id" name="city_id" class="form-select select2" required>
                        <option value="">Choose City</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Address -->
                <div class="col-md-6">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="2">{{ old('address') }}</textarea>
                </div>

                 <!-- Pincode -->
                 <div class="col-md-6">
                    <label for="pincode" class="form-label">Pincode</label>
                    <input type="text" class="form-control" id="pincode" name="pincode" value="{{ old('pincode') }}" required>
                </div>

                <!-- Fax Number -->
                <div class="col-md-6">
                    <label for="fax_number" class="form-label">Fax Number</label>
                    <input type="text" class="form-control" id="fax_number" name="fax_number" value="{{ old('fax_number') }}">
                </div>

                <!-- Bank Account Number -->
                <div class="col-md-6">
                    <label for="bank_account_number" class="form-label">Bank Account Number</label>
                    <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number') }}">
                </div>

                <!-- IFSC Code -->
                <div class="col-md-6">
                    <label for="ifsc_code" class="form-label">IFSC Code</label>
                    <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="{{ old('ifsc_code') }}">
                </div>

                <!-- Logo -->
                <div class="col-md-6">
                    <label for="logo" class="form-label">Logo</label>
                    <input type="file" class="form-control" id="logo" name="logo">
                </div>

                <!-- Entity Customer Care No -->
                <div class="col-md-6">
                    <label for="entity_customer_care_no" class="form-label">Entity Customer Care No</label>
                    <input type="text" class="form-control" id="entity_customer_care_no" name="entity_customer_care_no" value="{{ old('entity_customer_care_no') }}" required>
                </div>

                <!-- GSTIN -->
                <div class="col-md-6">
                    <label for="gstin" class="form-label">GSTIN</label>
                    <input type="text" class="form-control" id="gstin" name="gstin" value="{{ old('gstin') }}" required>
                </div>

                <!-- Billing Address -->
                <div class="col-md-6">
                    <label for="billing_address" class="form-label">Billing Address</label>
                    <textarea class="form-control" id="billing_address" name="billing_address" rows="2">{{ old('billing_address') }}</textarea>
                </div>

                <!-- License Validity -->
                <div class="col-md-6">
                    <label for="license_validity" class="form-label">License Validity</label>
                    <input type="date" class="form-control" id="license_validity" name="license_validity" value="{{ old('license_validity') }}">
                </div>

                <!-- Latitude -->
                <div class="col-md-6">
                    <label for="latitude" class="form-label">Latitude</label>
                    <input type="number" step="0.000001" class="form-control" id="latitude" name="latitude" value="{{ old('latitude') }}">
                </div>

                <!-- Longitude -->
                <div class="col-md-6">
                    <label for="longitude" class="form-label">Longitude</label>
                    <input type="number" step="0.000001" class="form-control" id="longitude" name="longitude" value="{{ old('longitude') }}">
                </div>

                <!-- Username -->
                <div class="col-md-6">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" required>
                </div>

                <!-- Password -->
                <div class="col-md-6">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>

                <!-- Created By (Hidden or Pre-filled) -->
                <input type="hidden" name="created_by" value="{{ Auth::user()->id ?? '' }}">

                <!-- Modified By (Hidden or Pre-filled) -->
                <input type="hidden" name="modified_by" value="{{ Auth::user()->id ?? '' }}">

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
            </form><!-- End Entity Registration Form -->

        </div>
    </div>
    
  </section>
@endsection

<!-- Add scripts section -->
@push('scripts')
<script>

console.log('bloodBankTypeId: {{ $bloodBankTypeId }}');
console.log('warehouseTypeId: {{ $warehouseTypeId }}');

    $(document).ready(function() {
        // Define the IDs for Blood Bank and Warehouse
        var bloodBankTypeId = '{{ $bloodBankTypeId }}';
        var warehouseTypeId = '{{ $warehouseTypeId }}';

        console.log('bloodBankTypeId: '+bloodBankTypeId);
        console.log('warehouseTypeId: '+warehouseTypeId);

        // Function to toggle Parent Entity dropdown
        function toggleParentEntityDropdown() {
            var selectedType = $('#entity_type_id').val();
            if (selectedType == bloodBankTypeId || selectedType == warehouseTypeId) {
                $('#parent_entity_div').removeClass('d-none');
                $('#parent_entity_id').attr('required', true);
                fetchParentEntities();
            } else {
                $('#parent_entity_div').addClass('d-none');
                $('#parent_entity_id').removeAttr('required').empty().append('<option value="">Choose Parent Entity...</option>');
            }
        }

        // Initial call to handle old input
        toggleParentEntityDropdown();

        // Event listener for Entity Type change
        $('#entity_type_id').change(function() {
            toggleParentEntityDropdown();
        });

        // Function to fetch Parent Entities via AJAX
        function fetchParentEntities() {
            // Prevent multiple AJAX calls if already loaded
            if ($('#parent_entity_id').children('option').length > 1) {
                return; // Already loaded
            }

            $.ajax({
                url: '{{ route("entities.getParentEntities") }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var parentEntitySelect = $('#parent_entity_id');
                        parentEntitySelect.empty();
                        parentEntitySelect.append('<option value="">Choose Parent Entity...</option>');
                        $.each(response.data, function(index, entity) {
                            parentEntitySelect.append('<option value="' + entity.id + '">' + entity.name + '</option>');
                        });

                        // Retain old input if available
                        @if(old('parent_entity_id'))
                            parentEntitySelect.val('{{ old("parent_entity_id") }}');
                        @endif
                    } else {
                        alert('Failed to fetch parent entities: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching parent entities:', error);
                    alert('An error occurred while fetching parent entities.');
                }
            });
        }
    });
</script>

<script>
    $(document).ready(function() {      
        const countryDropdown = $('#country_id');
        const stateDropdown = $('#state_id');
        const cityDropdown = $('#city_id');
        var urlGetStatesByIdTemplate = "{{ route('api.states', ['countryId' => '__COUNTRY_ID__']) }}";
        var urlcityByStateIdTemplate = "{{ route('api.cities', ['stateId' => '__STATE_ID__']) }}";


        countryDropdown.on('change', function() {
            const countryId = $(this).val();
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
                            data.data.forEach(state => {
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

          // Fetch cities when a state is selected
            stateDropdown.on('change', function() {
                const stateId = $(this).val();
                var cityUrl = urlcityByStateIdTemplate.replace('__STATE_ID__', stateId);
                if (stateId) {
                    $.ajax({
                        url: cityUrl,
                        type: 'GET',
                        success: function(data) {
                            // Clear and populate the city dropdown
                            cityDropdown.empty().append('<option value="">Choose City</option>');
                            if (data.success) {
                                data.data.forEach(city => {
                                    cityDropdown.append(`<option value="${city.id}">${city.name}</option>`);
                                });
                            } else {
                                alert(data.message || 'No cities available for the selected state.');
                            }
                        },
                        error: function(error) {
                            console.error('Error fetching cities:', error);
                            alert('Failed to fetch cities. Please try again.');
                        }
                    });
                } else {
                    cityDropdown.empty().append('<option value="">Choose City</option>');
                }
            });
    });
</script>
@endpush