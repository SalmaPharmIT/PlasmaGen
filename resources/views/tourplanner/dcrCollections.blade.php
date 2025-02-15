{{-- resources/views/tourplanner/dcrCollections.blade.php --}}

@extends('include.dashboardLayout')

@section('title', 'DCR Details - Collections')

@section('content')

<div class="pagetitle">
    <h1>DCR Details - Collections</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('tourplanner.dcrVisits') }}">DCR Approvals</a></li>
        <li class="breadcrumb-item active">DCR Details - Collections</li>
      </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">

            <!-- DCR Status Card -->
            <div class="card">
                <div class="card-header">
                    <div class="row justify-content-end">
                        <!-- Manager Status -->
                        <div class="col-md-3">
                            <div class="p-2 bg-success text-white rounded">
                                <strong>Manager Status: </strong>{{ ucfirst($dcr['extendedProps']['manager_status'] ?? '-') }}
                              </div>
                        </div>
                        <!-- CA Status -->
                        <div class="col-md-3">
                            <div class="p-2 bg-info text-white rounded">
                                <strong>CA Status: </strong>{{ ucfirst($dcr['extendedProps']['ca_status'] ?? '-') }}
                            </div>
                        </div>
                    </div>
                </div>
            <div>

            <!-- Visit Information Card -->
            <div class="card mb-4 mt-4">
                <div class="card-header text-black">
                    <h5 class="mb-0"><strong>Visit Information</strong></h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3 mt-3">
                        <!-- Blood Bank -->
                        <div class="col-md-4">
                            <strong>Blood Bank:</strong>
                            <p>{{ $dcr['extendedProps']['blood_bank_name'] ?? 'N/A' }}</p>
                        </div>
                        <!-- Planned Quantity -->
                        <div class="col-md-4">
                            <strong>Planned Quantity:</strong>
                            <p>{{ $dcr['extendedProps']['quantity'] ?? '0' }}</p>
                        </div>
                        <!-- Time -->
                        <div class="col-md-4">
                            <strong>Time:</strong>
                            <p>{{ isset($dcr['time']) ? date('h:i A', strtotime($dcr['time'])) : 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <!-- Available Quantity -->
                        <div class="col-md-4">
                            <strong>Available Quantity:</strong>
                            <p>{{ $dcr['extendedProps']['available_quantity'] ?? '0' }}</p>
                        </div>
                        <!-- Remaining Quantity -->
                        <div class="col-md-4">
                            <strong>Remaining Quantity:</strong>
                            <p>{{ $dcr['extendedProps']['remaining_quantity'] ?? '0' }}</p>
                        </div>
                        <!-- Price -->
                        <div class="col-md-4">
                            <strong>Price:</strong>
                            <p>{{ isset($dcr['extendedProps']['price']) ? number_format($dcr['extendedProps']['price'], 2) : 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <!-- Remarks -->
                        <div class="col-md-4">
                            <strong>Remarks:</strong>
                            <p>{{ $dcr['extendedProps']['remarks'] ?? 'N/A' }}</p>
                        </div>
                        <!-- Pending Documents -->
                        <div class="col-md-4">
                            <strong>Pending Documents:</strong>
                            <p>
                                @if(isset($dcr['pending_document_names']) && count($dcr['pending_document_names']) > 0)
                                    {{ implode(', ', $dcr['pending_document_names']) }}
                                @else
                                    None
                                @endif
                            </p>
                        </div>
                        <!-- Added By -->
                        <div class="col-md-4">
                            <strong>Added By:</strong>
                            <p>{{ $dcr['extendedProps']['created_by_name'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transport Information Card -->
            @if(isset($dcr['extendedProps']['transport_details']) && !empty($dcr['extendedProps']['transport_details']))
                <div class="card mb-4">
                    <div class="card-header text-black">
                        <h5 class="mb-0"><strong>Transport Information</strong></h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3 mt-3">
                            <!-- Driver Name -->
                            <div class="col-md-4">
                                <strong>Driver Name:</strong>
                                <p>{{ $dcr['extendedProps']['transport_details']['driver_name'] ?? 'N/A' }}</p>
                            </div>
                            <!-- Driver Contact -->
                            <div class="col-md-4">
                                <strong>Driver Contact:</strong>
                                <p>{{ $dcr['extendedProps']['transport_details']['contact_number'] ?? 'N/A' }}</p>
                            </div>
                            <!-- Vehicle Number -->
                            <div class="col-md-4">
                                <strong>Vehicle Number:</strong>
                                <p>{{ $dcr['extendedProps']['transport_details']['vehicle_number'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Transport Remarks -->
                            <div class="col-md-12">
                                <strong>Remarks:</strong>
                                <p>{{ $dcr['extendedProps']['transport_details']['remarks'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

             <!-- View Map Card -->
             @if(isset($dcr['extendedProps']['latitude']) && isset($dcr['extendedProps']['longitude']))
                <div class="card mb-4">
                    <div class="card-header text-black">
                        <h5 class="mb-0"><strong>View Map</strong></h5>
                    </div>
                    <div class="card-body">
                        <div id="map" style="width: 100%; height: 400px;"></div>
                    </div>
                </div>
            @endif

            <!-- Attachments Card -->
            <div class="card mb-4 mt-4">
                <div class="card-header text-black">
                    <h5 class="mb-0"><strong>DCR Attachments</strong></h5>
                </div>
                <div class="card-body">
                    <!-- Certificate of Quality -->
                    <div class="mb-4 mt-4">
                        <h6><strong>1. Certificate of Quality</strong></h6>
                        <div class="d-flex flex-wrap">
                            @if(isset($dcr['extendedProps']['dcr_attachments']) && count($dcr['extendedProps']['dcr_attachments']) > 0)
                                @foreach($dcr['extendedProps']['dcr_attachments'] as $attachment)
                                    @if($attachment['attachment_type'] == 1)
                                        <a href="{{ config('auth_api.base_image_url') . $attachment['attachment'] }}" target="_blank">
                                            <img src="{{ config('auth_api.base_image_url') . $attachment['attachment'] }}" alt="Certificate of Quality" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; margin-right: 10px; margin-bottom: 10px;">
                                        </a>
                                    @endif
                                @endforeach
                            @else
                                <p>No Certificates of Quality available.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Donor Report -->
                    <div class="mb-4">
                        <h6><strong>2. Donor Report</strong></h6>
                        <div class="d-flex flex-wrap">
                            @if(isset($dcr['extendedProps']['dcr_attachments']) && count($dcr['extendedProps']['dcr_attachments']) > 0)
                                @foreach($dcr['extendedProps']['dcr_attachments'] as $attachment)
                                    @if($attachment['attachment_type'] == 2)
                                        <a href="{{ config('auth_api.base_image_url') . $attachment['attachment'] }}" target="_blank">
                                            <img src="{{ config('auth_api.base_image_url') . $attachment['attachment'] }}" alt="Donor Report" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; margin-right: 10px; margin-bottom: 10px;">
                                        </a>
                                    @endif
                                @endforeach
                            @else
                                <p>No Donor Reports available.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Invoice Copy -->
                    <div class="mb-4">
                        <h6><strong>3. Invoice Copy</strong></h6>
                        <div class="d-flex flex-wrap">
                            @if(isset($dcr['extendedProps']['dcr_attachments']) && count($dcr['extendedProps']['dcr_attachments']) > 0)
                                @foreach($dcr['extendedProps']['dcr_attachments'] as $attachment)
                                    @if($attachment['attachment_type'] == 3)
                                        <a href="{{ config('auth_api.base_image_url') . $attachment['attachment'] }}" target="_blank">
                                            <img src="{{ config('auth_api.base_image_url') . $attachment['attachment'] }}" alt="Invoice Copy" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; margin-right: 10px; margin-bottom: 10px;">
                                        </a>
                                    @endif
                                @endforeach
                            @else
                                <p>No Invoice Copies available.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Pending Documents -->
                    <div class="mb-4">
                        <h6><strong>4. Pending Documents</strong></h6>
                        <div class="d-flex flex-wrap">
                            @if(isset($dcr['extendedProps']['dcr_attachments']) && count($dcr['extendedProps']['dcr_attachments']) > 0)
                                @foreach($dcr['extendedProps']['dcr_attachments'] as $attachment)
                                    @if($attachment['attachment_type'] == 4)
                                        <a href="{{ config('auth_api.base_image_url') . $attachment['attachment'] }}" target="_blank">
                                            <img src="{{ config('auth_api.base_image_url') . $attachment['attachment'] }}" alt="Pending Document" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; margin-right: 10px; margin-bottom: 10px;">
                                        </a>
                                    @endif
                                @endforeach
                            @else
                                <p>No Pending Documents available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


             <!-- Approval Buttons Form -->
             <div class="card">
                <div class="card-body text-center mt-3">
                    <form method="POST" action="{{ route('tourplanner.dcr.updateStatus', ['id' => $dcr['id']]) }}" id="statusForm">
                        @csrf
                        <button type="button" name="status" value="approved"  id="approveBtn" class="btn btn-success me-3">Approve</button>
                        <button type="button" name="status" value="rejected" id="rejectBtn" class="btn btn-danger">Reject</button>
                    </form>
                </div>
            </div>


          </div>
        </div>
    </section>

</div>

@endsection

@push('styles')
    <style>
        /* Existing styles... */

        /* Optional: Style for the table */
        #dcrApprovalsTable th, #dcrApprovalsTable td {
            vertical-align: middle;
            text-align: center;
        }

        /* Additional styles for better presentation */
        .img-thumbnail {
            transition: transform 0.2s;
        }

        .img-thumbnail:hover {
            transform: scale(1.05);
        }
        
        /* Style for the map */
        #map {
            width: 100%;
            height: 400px;
        }
    </style>
@endpush

@push('scripts')

    <!-- Load Google Maps JavaScript API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('auth_api.google_maps_api_key') }}&callback=initMap" async defer></script>

    <!-- Your Custom Scripts -->
    <script>
        // Initialize the Google Map
        function initMap() {
            @if(isset($dcr['extendedProps']['latitude']) && isset($dcr['extendedProps']['longitude']))
                var latitude = parseFloat("{{ $dcr['extendedProps']['latitude'] }}");
                var longitude = parseFloat("{{ $dcr['extendedProps']['longitude'] }}");
                var title = "{{ $dcr['title'] ?? 'DCR Location' }}";

                // Create a map object centered at the given coordinates
                var map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: latitude, lng: longitude },
                    zoom: 15
                });

                // Create a marker at the given coordinates
                var marker = new google.maps.Marker({
                    position: { lat: latitude, lng: longitude },
                    map: map,
                    title: title
                });

                // Create an info window with the title
                var infoWindow = new google.maps.InfoWindow({
                    content: `<strong>${title}</strong>`
                });

                // Add a click listener to open the info window when the marker is clicked
                marker.addListener('click', function() {
                    infoWindow.open(map, marker);
                });

                // Optionally, open the info window by default
                infoWindow.open(map, marker);
            @endif
        }


        $(document).ready(function () {
        const approveBtn = $('#approveBtn');
        const rejectBtn = $('#rejectBtn');
        const statusForm = $('#statusForm');

        approveBtn.on('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to approve this DCR?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745', // Green color for approve
                cancelButtonColor: '#6c757d', // Gray color for cancel
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Set the status value and submit the form
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'status',
                        value: 'approved'
                    }).appendTo(statusForm);
                    statusForm.submit();
                }
            });
        });

        rejectBtn.on('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to reject this DCR?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545', // Red color for reject
                cancelButtonColor: '#6c757d', // Gray color for cancel
                confirmButtonText: 'Yes, reject it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Set the status value and submit the form
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'status',
                        value: 'rejected'
                    }).appendTo(statusForm);
                    statusForm.submit();
                }
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
                    icon: 'error',
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
                    icon: 'error',
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