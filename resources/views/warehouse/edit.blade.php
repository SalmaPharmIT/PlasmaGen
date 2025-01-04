@extends('include.dashboardLayout')

@section('title', 'Edit Warehouse')

@section('content')

<div class="pagetitle">
    <h1>Edit Warehouse</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('warehouse.index') }}">Warehouses</a></li>
        <li class="breadcrumb-item active">Edit</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Edit Warehouse</h5>


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

            <!-- Entity Edit Form -->
            <form class="row g-3" action="{{ route('warehouse.update', $entity['id']) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div class="col-md-6">
                    <label for="name" class="form-label">Warehouse Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $entity['name']) }}" required>
                </div>

                   <!-- Entity License Number -->
                <div class="col-md-6">
                    <label for="entity_license_number" class="form-label">Entity License Number</label>
                    <input type="text" class="form-control" id="entity_license_number" name="entity_license_number" value="{{ old('entity_license_number', $entity['entity_license_number']) }}">
                </div>

                <!-- License Validity -->
                <div class="col-md-6">
                    <label for="license_validity" class="form-label">License Validity</label>
                    <input type="date" class="form-control" id="license_validity" name="license_validity" value="{{ old('license_validity', isset($entity['license_validity']) ? \Carbon\Carbon::parse($entity['license_validity'])->format('Y-m-d') : '') }}">
                </div>

                <!-- Entity Customer Care No -->
                <div class="col-md-6">
                    <label for="entity_contact_person" class="form-label">Contact Person Name</label>
                    <input type="text" class="form-control" id="entity_contact_person" name="entity_contact_person" value="{{ old('entity_contact_person', $entity['entity_contact_person']) }}" required>
                </div>

                {{-- <!-- Entity Type -->
                <div class="col-md-6">
                    <label for="entity_type_id" class="form-label">Entity Type</label>
                    <select id="entity_type_id" name="entity_type_id" class="form-select select2" required>
                        <option value="">Choose Entity Type</option>
                        @foreach($entityTypes as $type)
                            <option value="{{ $type->id }}" {{ old('entity_type_id', $entity['entity_type_id']) == $type->id ? 'selected' : '' }}>
                                {{ $type->entity_name }}
                            </option>
                        @endforeach
                    </select>
                </div> --}}

                <!-- Parent Entity (Initially Hidden) -->
                {{-- <div class="col-md-6" id="parent_entity_div">
                    <label for="parent_entity_id" class="form-label">Parent Entity</label>
                    <select id="parent_entity_id" name="parent_entity_id" class="form-select select2" required>
                        <option value="">Choose Parent Entity</option>
                        @foreach($entities as $parentEntity)
                            <option value="{{ $parentEntity->id }}" {{ (old('parent_entity_id', $entity['parent_entity_id']) == $parentEntity->id) ? 'selected' : '' }}>
                                {{ $parentEntity->name }}
                            </option>
                        @endforeach
                    </select>
                </div>  --}}

                 <!-- Email -->
                 <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $entity['email']) }}">
                </div>

                <!-- Mobile No -->
                <div class="col-md-6">
                    <label for="mobile_no" class="form-label">Mobile No</label>
                    <input type="text" class="form-control" id="mobile_no" name="mobile_no" value="{{ old('mobile_no', $entity['mobile_no']) }}" required>
                </div>

                <!-- GSTIN -->
                <div class="col-md-6">
                    <label for="gstin" class="form-label">GSTIN</label>
                    <input type="text" class="form-control" id="gstin" name="gstin" value="{{ old('gstin', $entity['gstin']) }}">
                </div>

                <!-- PAN ID -->
                <div class="col-md-6">
                    <label for="pan_id" class="form-label">PAN ID</label>
                    <input type="text" class="form-control" id="pan_id" name="pan_id" value="{{ old('pan_id', $entity['pan_id']) }}">
                </div>

                <!-- Country -->
                <div class="col-md-6">
                    <label for="country_id" class="form-label">Country</label>
                    <select id="country_id" name="country_id" class="form-select select2" required>
                        <option value="">Choose Country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id', $entity['country_id']) == $country->id ? 'selected' : '' }}>
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
                            <option value="{{ $state->id }}" {{ old('state_id', $entity['state_id']) == $state->id ? 'selected' : '' }}>
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
                            <option value="{{ $city->id }}" {{ old('city_id', $entity['city_id']) == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Address -->
                <div class="col-md-6">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="2">{{ old('address', $entity['address']) }}</textarea>
                </div>

                 <!-- Pincode -->
                 <div class="col-md-6">
                    <label for="pincode" class="form-label">Pincode</label>
                    <input type="text" class="form-control" id="pincode" name="pincode" value="{{ old('pincode', $entity['pincode']) }}">
                </div>

                <!-- FFP Pocurement Company -->
                <div class="col-md-6">
                    <label for="FFP_procurement_company" class="form-label">Past/Current FFP Pocurement Company</label>
                    <input type="text" class="form-control" id="FFP_procurement_company" name="FFP_procurement_company" value="{{ old('FFP_procurement_company', $entity['FFP_procurement_company']) }}">
                </div>

                <!--Final Accepted Offer -->
                <div class="col-md-6">
                    <label for="final_accepted_offer" class="form-label">Final Accepted Offer</label>
                    <input type="text" class="form-control" id="final_accepted_offer" name="final_accepted_offer" value="{{ old('final_accepted_offer', $entity['final_accepted_offer']) }}">
                </div>

                 <!--Payment Terms -->
                 <div class="col-md-6">
                    <label for="payment_terms" class="form-label">Payment Terms</label>
                    <input type="text" class="form-control" id="payment_terms" name="payment_terms" value="{{ old('payment_terms', $entity['payment_terms']) }}">
                </div>


                <!-- Fax Number -->
                <div class="col-md-6">
                    <label for="fax_number" class="form-label">Fax Number</label>
                    <input type="text" class="form-control" id="fax_number" name="fax_number" value="{{ old('fax_number', $entity['fax_number']) }}">
                </div>

               
                <!-- Bank Account Number -->
                <div class="col-md-6">
                    <label for="bank_account_number" class="form-label">Bank Account Number</label>
                    <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $entity['bank_account_number']) }}">
                </div>

                <!-- IFSC Code -->
                <div class="col-md-6">
                    <label for="ifsc_code" class="form-label">IFSC Code</label>
                    <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="{{ old('ifsc_code', $entity['ifsc_code']) }}">
                </div>

                <!-- Logo -->
                <div class="col-md-6">
                    <label for="logo" class="form-label">Logo</label>
                    <input type="file" class="form-control" id="logo" name="logo">
                    @if(isset($entity['logo']) && $entity['logo'])
                        <img src="{{  config('auth_api.base_image_url') . $entity['logo'] }}" alt="Logo" width="50" height="50" class="mt-2">
                    @endif
                </div>

                <!-- Entity Customer Care No -->
                <div class="col-md-6">
                    <label for="entity_customer_care_no" class="form-label">Entity Customer Care No</label>
                    <input type="text" class="form-control" id="entity_customer_care_no" name="entity_customer_care_no" value="{{ old('entity_customer_care_no', $entity['entity_customer_care_no']) }}">
                </div>

              

                <!-- Billing Address -->
                <div class="col-md-6">
                    <label for="billing_address" class="form-label">Billing Address</label>
                    <textarea class="form-control" id="billing_address" name="billing_address" rows="2">{{ old('billing_address', $entity['billing_address']) }}</textarea>
                </div>


                <!-- Latitude -->
                <div class="col-md-6">
                    <label for="latitude" class="form-label">Latitude</label>
                    <input type="number" step="0.000001" class="form-control" id="latitude" name="latitude" value="{{ old('latitude', $entity['latitude']) }}">
                </div>

                <!-- Longitude -->
                <div class="col-md-6">
                    <label for="longitude" class="form-label">Longitude</label>
                    <input type="number" step="0.000001" class="form-control" id="longitude" name="longitude" value="{{ old('longitude', $entity['longitude']) }}">
                </div>

                <!-- Username -->
                {{-- <div class="col-md-6">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $entity['username']) }}">
                </div> --}}

                <!-- Password -->
                {{-- <div class="col-md-6">
                    <label for="password" class="form-label">Password <small>(Leave blank to keep current password)</small></label>
                    <input type="password" class="form-control" id="password" name="password">
                </div> --}}

                <!-- Confirm Password -->
                {{-- <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div> --}}

                  <!-- Account Status -->
                  <div class="col-md-6">
                    <label for="account_status" class="form-label">Account Status</label>
                    <select id="account_status" name="account_status" class="form-select" required>
                        <option value="">Choose Status</option>
                        @foreach(\App\Models\Entity::$statuses as $value => $label)
                            <option value="{{ $value }}" {{ old('account_status', $entity['account_status']) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>


                  <!-- Attach Documents -->
                  <div class="col-md-12">
                    <label for="documents" class="form-label">Attach Documents</label>
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

                <!-- Hidden Inputs Container for Documents to Delete -->
                <div id="documents_to_delete_container"></div>

                <!-- Preview Section for New Documents -->
                <div class="col-md-12">
                    <h6>New Documents Preview</h6>
                    <div id="new-document-preview" class="d-flex flex-wrap">
                        <!-- Previews will be appended here -->
                    </div>
                </div>


                <!-- Created By (Hidden or Pre-filled) -->
                <input type="hidden" name="created_by" value="{{ $entity['created_by'] ?? '' }}">

                <!-- Modified By (Hidden or Pre-filled) -->
                <input type="hidden" name="modified_by" value="{{ Auth::id() }}">

                <!-- Modified By (Hidden or Pre-filled) -->
                <input type="hidden" name="entity_type_id" value="3">     <!-- entity_type_id - 3 for Warehouses -->

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('warehouse.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form><!-- End Entity Edit Form -->

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
</style>
@endpush

@push('scripts')
<script>
   document.addEventListener('DOMContentLoaded', function() {
    const documentsInput = document.getElementById('documents');
    const newDocumentPreview = document.getElementById('new-document-preview');
    const documentsToDeleteContainer = document.getElementById('documents_to_delete_container');
    const existingDocumentPreview = document.getElementById('existing-document-preview');

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
                    // Add multiple classes using spread operator
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

