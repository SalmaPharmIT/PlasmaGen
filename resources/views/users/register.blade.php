@extends('include.dashboardLayout')

@section('title', 'User Registration')

@section('content')

<div class="pagetitle">
    <h1>User Registration</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
        <li class="breadcrumb-item active">Registration</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Add New User</h5>


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

            <!-- User Registration Form -->
            <form class="row g-3" action="{{ route('user.register.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Name -->
                <div class="col-md-6">
                    <label for="name" class="form-label">User Name <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                </div>

                {{-- <!-- Entity -->
                <div class="col-md-6">
                    <label for="entity_id" class="form-label">Entity</label>
                    <select id="entity_id" name="entity_id" class="form-select select2" required>
                        <option value="">Choose Entity</option>
                        @foreach($entity as $type)
                            <option value="{{ $type->id }}" {{ old('entity_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div> --}}

                  <!-- Entity (Conditional) -->
                  @if (Auth::user()->role_id == 1)
                  <div class="col-md-6">
                    <label for="entity_id" class="form-label">Entity <span style="color:red">*</span></label>
                    <select id="entity_id" name="entity_id" class="form-select select2" required>
                        <option value="">Choose Entity</option>
                        @foreach($parentEntityTypes as $parentEntity)
                            <option value="{{ $parentEntity['id'] }}" {{ old('entity_id') == $parentEntity['id'] ? 'selected' : '' }}>
                                {{ $parentEntity['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
              @else
                  <input type="hidden" name="entity_id" value="{{ Auth::user()->entity_id ?? '' }}">
              @endif

                <!-- Role -->
                <div class="col-md-6">
                    <label for="role_id" class="form-label">Role <span style="color:red">*</span></label>
                    <select id="role_id" name="role_id" class="form-select select2" required>
                        <option value="">Choose Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Test Type (shown only for role ID 16) -->
                <div class="col-md-6" id="testTypeDiv" style="display: none;">
                    <label for="test_type" class="form-label">Test Type <span style="color:red">*</span></label>
                    <select id="test_type" name="test_type" class="form-select select2">
                        <option value="">Choose Test Type</option>
                        <option value="1" {{ old('test_type') == '1' ? 'selected' : '' }}>NAT</option>
                        <option value="2" {{ old('test_type') == '2' ? 'selected' : '' }}>ELISA</option>
                        <option value="3" {{ old('test_type') == '3' ? 'selected' : '' }}>BOTH</option>
                    </select>
                </div>

                 <!-- Gender -->
                 <div class="col-md-6">
                    <label for="gender" class="form-label">Gender <span style="color:red">*</span></label>
                    <select id="gender" name="gender" class="form-select select2" required>
                        <option value="">Choose Gender</option>
                        @foreach($userGender as $value => $label)
                            <option value="{{ $value }}" {{ old('gender') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Mobile No -->
                <div class="col-md-6">
                    <label for="mobile_no" class="form-label">Mobile No <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="mobile_no" name="mobile_no" value="{{ old('mobile_no') }}" required>
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label for="email" class="form-label">Email <span style="color:red">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                </div>

                 <!-- Date Of Birth -->
                 <div class="col-md-6">
                    <label for="dob" class="form-label">Date Of Birth</label>
                    <input type="date" class="form-control" id="dob" name="dob" value="{{ old('dob') }}">
                </div>

                <!-- PAN ID -->
                <div class="col-md-6">
                    <label for="pan_id" class="form-label">PAN ID <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="pan_id" name="pan_id" value="{{ old('pan_id') }}" required>
                </div>

                <!-- Aadhar ID -->
                <div class="col-md-6">
                    <label for="aadhar_id" class="form-label">Aadhar ID <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="aadhar_id" name="aadhar_id" value="{{ old('aadhar_id') }}" required>
                </div>

                 <!-- Account Status -->
                 {{-- <div class="col-md-6">
                    <label for="account_status" class="form-label">Account Status</label>
                    <select id="account_status" name="account_status" class="form-select select2" required>
                        <option value="">Choose Status</option>
                        @foreach($userStatuses as $value => $label)
                            <option value="{{ $value }}" {{ old('account_status') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div> --}}

                <!-- Country -->
                <div class="col-md-6">
                    <label for="country_id" class="form-label">Country <span style="color:red">*</span></label>
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
                    <label for="state_id" class="form-label">State <span style="color:red">*</span></label>
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
                    <label for="city_id" class="form-label">City <span style="color:red">*</span></label>
                    <select id="city_id" name="city_id" class="form-select select2" required>
                        <option value="">Choose City</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pincode -->
                <div class="col-md-6">
                    <label for="pincode" class="form-label">Pincode <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="pincode" name="pincode" value="{{ old('pincode') }}" required>
                </div>

                <!-- Address -->
                <div class="col-md-6">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="2">{{ old('address') }}</textarea>
                </div>

                <!-- Latitude -->
                <div class="col-md-3">
                    <label for="latitude" class="form-label">Latitude</label>
                    <input type="number" step="0.000001" class="form-control" id="latitude" name="latitude" value="{{ old('latitude') }}">
                </div>

                <!-- Longitude -->
                <div class="col-md-3">
                    <label for="longitude" class="form-label">Longitude</label>
                    <input type="number" step="0.000001" class="form-control" id="longitude" name="longitude" value="{{ old('longitude') }}">
                </div>

                <!-- Profile Pic -->
                <div class="col-md-6">
                    <label for="profile_pic" class="form-label">Profile Picture</label>
                    <input type="file" class="form-control" id="profile_pic" name="profile_pic">
                </div>

                <!-- Username -->
                <div class="col-md-6">
                    <label for="username" class="form-label">Username <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" required>
                </div>

                <!-- Password -->
                <div class="col-md-6">
                    <label for="password" class="form-label">Password <span style="color:red">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirm Password <span style="color:red">*</span></label>
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
            </form><!-- End User Registration Form -->

        </div>
    </div>
    
  </section>
@endsection


@push('scripts')
<script>
        $(document).ready(function() {      
            const countryDropdown = $('#country_id');
            const stateDropdown = $('#state_id');
            const cityDropdown = $('#city_id');
            const roleDropdown = $('#role_id');
            const testTypeDiv = $('#testTypeDiv');
            const testTypeSelect = $('#test_type');
            var urlGetStatesByIdTemplate = "{{ route('api.states', ['countryId' => '__COUNTRY_ID__']) }}";
            var urlcityByStateIdTemplate = "{{ route('api.cities', ['stateId' => '__STATE_ID__']) }}";

            // Handle role change to show/hide test type dropdown
            roleDropdown.on('change', function() {
                const roleId = $(this).val();
                if (roleId == 16) {
                    testTypeDiv.show();
                    testTypeSelect.prop('required', true);
                } else {
                    testTypeDiv.hide();
                    testTypeSelect.prop('required', false);
                    testTypeSelect.val('');
                }
            });

            // Initialize test type visibility based on current role selection
            if (roleDropdown.val() == 16) {
                testTypeDiv.show();
                testTypeSelect.prop('required', true);
            }

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