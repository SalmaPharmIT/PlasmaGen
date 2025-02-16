@extends('include.dashboardLayout')

@section('title', 'Blood Bank Registration')

@section('content')

<div class="pagetitle">
    <h1>Blood Bank Registration</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('bloodbank.index') }}">Blood Banks</a></li>
        <li class="breadcrumb-item active">Regitration</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Add New Blood Bank</h5>


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
            <form class="row g-3" action="{{ route('bloodbank.register.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf


                <!-- Optional: Hidden Field for DCR ID -->
                @if($dcrDetails)
                    <input type="hidden" name="dcr_id" value="{{ $dcrDetails['id'] }}">
                @endif

                <!-- Name -->
                <div class="col-md-6">
                    <label for="name" class="form-label">Blood Bank Name <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $dcrDetails['blood_bank_name'] ?? '') }}" required>
                </div>

            
                <!-- Entity Licence Number -->
                <div class="col-md-6">
                    <label for="entity_license_number" class="form-label">License Number <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="entity_license_number" name="entity_license_number" value="{{ old('entity_license_number') }}" required>
                </div>

                <!-- License Validity -->
                <div class="col-md-6">
                    <label for="license_validity" class="form-label">License Validity</label>
                    <input type="date" class="form-control" id="license_validity" name="license_validity" value="{{ old('license_validity') }}">
                </div>

                 <!-- Entity Customer Care No -->
                 <div class="col-md-6">
                    <label for="entity_contact_person" class="form-label">Contact Person Name <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="entity_contact_person" name="entity_contact_person" value="{{ old('entity_contact_person', $dcrDetails['sourcing_contact_person'] ?? '') }}" required>
                </div>

                  <!-- Email -->
                  <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $dcrDetails['sourcing_email'] ?? '') }}">
                </div>

                <!-- Mobile No -->
                <div class="col-md-6">
                    <label for="mobile_no" class="form-label">Mobile No <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="mobile_no" name="mobile_no" value="{{ old('mobile_no', $dcrDetails['sourcing_mobile_number'] ?? '') }}" required>
                </div>

                 <!-- GSTIN -->
                <div class="col-md-6">
                    <label for="gstin" class="form-label">GSTIN <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="gstin" name="gstin" value="{{ old('gstin') }}" required>
                </div>

                <!-- PAN ID -->
                <div class="col-md-6">
                    <label for="pan_id" class="form-label">PAN ID <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="pan_id" name="pan_id" value="{{ old('pan_id') }}" required>
                </div>

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

                <!-- Address -->
                <div class="col-md-6">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="2">{{ old('address', $dcrDetails['sourcing_address'] ?? '') }}</textarea>
                </div>

                 <!-- Pincode -->
                 <div class="col-md-6">
                    <label for="pincode" class="form-label">Pincode <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="pincode" name="pincode" value="{{ old('pincode') }}" required>
                </div>

                 <!-- FFP Pocurement Company -->
                 <div class="col-md-6">
                    <label for="FFP_procurement_company" class="form-label">Past/Current FFP Procurement Company <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="FFP_procurement_company" name="FFP_procurement_company" value="{{ old('FFP_procurement_company', $dcrDetails['sourcing_ffp_company'] ?? '') }}" required>
                </div>

                 <!--Final Accepted Offer -->
                 <div class="col-md-6">
                    <label for="final_accepted_offer" class="form-label">Final Accepted Offer <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="final_accepted_offer" name="final_accepted_offer" value="{{ old('final_accepted_offer', $dcrDetails['sourcing_plasma_price'] ?? '') }}" required>
                </div>

                 <!--Payment Terms -->
                 <div class="col-md-6">
                    <label for="payment_terms" class="form-label">Payment Terms <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="payment_terms" name="payment_terms" value="{{ old('payment_terms', $dcrDetails['sourcing_payment_terms'] ?? '') }}" required>
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
                    <label for="entity_customer_care_no" class="form-label">Customer Care No <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="entity_customer_care_no" name="entity_customer_care_no" value="{{ old('entity_customer_care_no') }}" required>
                </div>

                <!-- Billing Address -->
                <div class="col-md-6">
                    <label for="billing_address" class="form-label">Billing Address</label>
                    <textarea class="form-control" id="billing_address" name="billing_address" rows="2">{{ old('billing_address') }}</textarea>
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
                {{-- <div class="col-md-6">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" required>
                </div> --}}

                <!-- Password -->
                {{-- <div class="col-md-6">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div> --}}

                <!-- Confirm Password -->
                {{-- <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div> --}}

                  <!-- Attach Documents -->
                  <div class="col-md-6">
                    <label for="documents" class="form-label">Attach Documents</label>
                    <input type="file" class="form-control" id="documents" name="documents[]" multiple>
                    <small class="form-text text-muted">You can upload multiple files (DOC, PDF, Images, etc.).</small>
                </div>

                 <!-- Preview Section for Documents -->
                 <div class="col-md-12">
                    <div id="document-preview" class="d-flex flex-wrap">
                        <!-- Previews will be appended here -->
                    </div>
                </div>


                <!-- Created By (Hidden or Pre-filled) -->
                <input type="hidden" name="created_by" value="{{ Auth::user()->id ?? '' }}">

                <!-- Modified By (Hidden or Pre-filled) -->
                <input type="hidden" name="modified_by" value="{{ Auth::user()->id ?? '' }}">

                 <!-- Modified By (Hidden or Pre-filled) -->
                <input type="hidden" name="entity_type_id" value="2">     <!-- entity_type_id - 2 for Blood banks -->

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
            </form><!-- End Entity Registration Form -->

        </div>
    </div>
    
  </section>
@endsection

<!-- Add styles section -->
@push('styles')
<style>
    /* Style for the preview container */
    #document-preview {
        display: flex;
        flex-wrap: wrap;
    }

    /* Style for each preview item */
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

    /* Style for images in preview */
    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Style for the delete button */
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

    /* Hover effect for delete button */
    .preview-item .delete-btn:hover {
        background-color: rgba(255, 0, 0, 0.8);
    }
</style>
@endpush

<!-- Add scripts section -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const documentsInput = document.getElementById('documents');
        const documentPreview = document.getElementById('document-preview');

        // Initialize a DataTransfer object to manage files
        const dt = new DataTransfer();

        documentsInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);

            // Add new files to the DataTransfer object
            files.forEach(file => {
                dt.items.add(file);
            });

            // Update the input's files to the DataTransfer's files
            documentsInput.files = dt.files;

            // Clear existing previews
            documentPreview.innerHTML = '';

            // Display previews for each file
            Array.from(dt.files).forEach((file, index) => {
                const fileReader = new FileReader();

                fileReader.onload = function(e) {
                    const fileURL = e.target.result;
                    const fileType = file.type;

                    const previewItem = document.createElement('div');
                    previewItem.classList.add('preview-item');

                    // Create delete button
                    const deleteBtn = document.createElement('button');
                    deleteBtn.classList.add('delete-btn');
                    deleteBtn.innerHTML = '&times;';
                    deleteBtn.title = 'Remove this file';

                    // Event listener for delete button
                    deleteBtn.addEventListener('click', function() {
                        // Remove the file from DataTransfer
                        dt.items.remove(index);
                        // Update the input's files
                        documentsInput.files = dt.files;
                        // Remove the preview
                        documentPreview.removeChild(previewItem);
                        // Re-render the remaining previews to update indices
                        renderPreviews();
                    });

                    // Append delete button
                    previewItem.appendChild(deleteBtn);

                    if (fileType.startsWith('image/')) {
                        const img = document.createElement('img');
                        img.src = fileURL;
                        img.alt = file.name;
                        previewItem.appendChild(img);
                    } else {
                        // For non-image files, display an icon or file name
                        const icon = document.createElement('i');
                        icon.classList.add('bi', getIconClass(fileType));
                        icon.style.fontSize = '2rem';
                        previewItem.appendChild(icon);
                    }

                    documentPreview.appendChild(previewItem);
                };

                fileReader.readAsDataURL(file);
            });
        });

        // Function to get Bootstrap Icon class based on file type
        function getIconClass(fileType) {
            if (fileType === 'application/pdf') {
                return 'bi-file-earmark-pdf-fill text-danger';
            } else if (fileType === 'application/msword' || fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                return 'bi-file-earmark-word-fill text-primary';
            } else if (fileType === 'application/vnd.ms-excel' || fileType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                return 'bi-file-earmark-excel-fill text-success';
            } else if (fileType === 'text/plain') {
                return 'bi-file-earmark-text-fill text-secondary';
            } else {
                return 'bi-file-earmark-fill text-warning';
            }
        }

        // Function to re-render previews (useful after deletion to update indices)
        function renderPreviews() {
            // Clear existing previews
            documentPreview.innerHTML = '';

            // Display previews for each file
            Array.from(dt.files).forEach((file, index) => {
                const fileReader = new FileReader();

                fileReader.onload = function(e) {
                    const fileURL = e.target.result;
                    const fileType = file.type;

                    const previewItem = document.createElement('div');
                    previewItem.classList.add('preview-item');

                    // Create delete button
                    const deleteBtn = document.createElement('button');
                    deleteBtn.classList.add('delete-btn');
                    deleteBtn.innerHTML = '&times;';
                    deleteBtn.title = 'Remove this file';

                    // Event listener for delete button
                    deleteBtn.addEventListener('click', function() {
                        // Remove the file from DataTransfer
                        dt.items.remove(index);
                        // Update the input's files
                        documentsInput.files = dt.files;
                        // Remove the preview
                        documentPreview.removeChild(previewItem);
                        // Re-render the remaining previews
                        renderPreviews();
                    });

                    // Append delete button
                    previewItem.appendChild(deleteBtn);

                    if (fileType.startsWith('image/')) {
                        const img = document.createElement('img');
                        img.src = fileURL;
                        img.alt = file.name;
                        previewItem.appendChild(img);
                    } else {
                        // For non-image files, display an icon or file name
                        const icon = document.createElement('i');
                        icon.classList.add('bi', getIconClass(fileType));
                        icon.style.fontSize = '2rem';
                        previewItem.appendChild(icon);
                    }

                    documentPreview.appendChild(previewItem);
                };

                fileReader.readAsDataURL(file);
            });
        }
    });
</script>

<script>
    $(document).ready(function() {      
        const countryDropdown = $('#country_id');
        const stateDropdown = $('#state_id');
        const cityDropdown = $('#city_id');
        

        countryDropdown.on('change', function() {
            const countryId = $(this).val();
            console.log("Fetching states for countryId:", countryId);

            if (countryId) {
                $.ajax({
                    url: `/api/states/${countryId}`,
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

                if (stateId) {
                    $.ajax({
                        url: `/api/cities/${stateId}`,
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