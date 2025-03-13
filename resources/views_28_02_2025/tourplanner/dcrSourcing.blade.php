{{-- resources/views/tourplanner/dcrSourcing.blade.php --}}

@extends('include.dashboardLayout')

@section('title', 'DCR Details - Sourcing')

@section('content')

<div class="pagetitle">
    <h1>DCR Details - Sourcing</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('tourplanner.dcr') }}">DCR Approvals</a></li>
        <li class="breadcrumb-item active">DCR Details - Sourcing</li>
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

            <!-- Sourcing Information Card -->
            <div class="card mb-4  mt-2">
                <div class="card-header text-black">
                    <h5 class="mb-0"><strong>Sourcing Information</strong></h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3 mt-3">
                        <!-- Sourcing City -->
                        <div class="col-md-4">
                            <strong>Sourcing City:</strong>
                            <p>{{ $dcr['extendedProps']['sourcing_city_name'] ?? 'N/A' }}</p>
                        </div>
                        <!-- Sourcing Blood Bank Name -->
                        <div class="col-md-4">
                            <strong>Sourcing Blood Bank Name:</strong>
                            <p>{{ $dcr['extendedProps']['sourcing_blood_bank_name'] ?? 'N/A' }}</p>
                        </div>
                        <!-- Contact Person Name -->
                        <div class="col-md-4">
                            <strong>Contact Person Name:</strong>
                            <p>{{ $dcr['extendedProps']['sourcing_contact_person'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <!-- Mobile No -->
                        <div class="col-md-4">
                            <strong>Mobile No:</strong>
                            <p>{{ $dcr['extendedProps']['sourcing_mobile_number'] ?? 'N/A' }}</p>
                        </div>
                        <!-- Email -->
                        <div class="col-md-4">
                            <strong>Email:</strong>
                            <p>{{ $dcr['extendedProps']['sourcing_email'] ?? 'N/A' }}</p>
                        </div>
                        <!-- Address -->
                        <div class="col-md-4">
                            <strong>Address:</strong>
                            <p>{{ $dcr['extendedProps']['sourcing_address'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <!-- FFP Procurement Company -->
                        <div class="col-md-4">
                            <strong>FFP Procurement Company:</strong>
                            <p>{{ $dcr['extendedProps']['sourcing_ffp_company'] ?? 'N/A' }}</p>
                        </div>
                        <!-- Current Plasma Price/Ltr -->
                        <div class="col-md-4">
                            <strong>Current Plasma Price/Ltr:</strong>
                            <p>{{ isset($dcr['extendedProps']['sourcing_plasma_price']) ? number_format($dcr['extendedProps']['sourcing_plasma_price'], 2) : 'N/A' }}</p>
                        </div>
                        <!-- Potential Per Month -->
                        <div class="col-md-4">
                            <strong>Potential Per Month:</strong>
                            <p>{{ $dcr['extendedProps']['sourcing_potential_per_month'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <!-- Payment Terms -->
                        <div class="col-md-4">
                            <strong>Payment Terms:</strong>
                            <p>{{ $dcr['extendedProps']['sourcing_payment_terms'] ?? 'N/A' }}</p>
                        </div>
                        <!-- Remarks -->
                        <div class="col-md-4">
                            <strong>Remarks:</strong>
                            <p>{{ $dcr['extendedProps']['sourcing_remarks'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <!-- Status -->
                        <div class="col-md-4">
                            <strong>Status:</strong>
                            <p>{{ ucfirst(str_replace('_', ' ', $dcr['extendedProps']['status'])) ?? 'N/A' }}</p>
                        </div>
                        <!-- Added By -->
                        <div class="col-md-4">
                            <strong>Added By:</strong>
                            <p>{{ $dcr['extendedProps']['created_by_name'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>


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


          <!-- Approval Buttons Form -->
          <div class="card">
            <div class="card-body text-center mt-3">
                <form method="POST" action="{{ route('tourplanner.dcr.updateStatus', ['id' => $dcr['id']]) }}" id="statusForm">
                    @csrf
                    
                    @php
                        $roleId = Auth::user()->role_id;
                    @endphp

                    @if($roleId == 2)
                        <button type="submit" name="status" value="approved" id="approveBtn" class="btn btn-success me-3">Approve</button>
                    @endif

                    @if($roleId == 6)
                        <button type="submit" name="status" value="accepted" id="acceptBtn" class="btn btn-primary me-3">Accept</button>
                    @endif

                    
                        <button type="submit" name="status" value="rejected" id="rejectBtn" class="btn btn-danger">Reject</button>
                    
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
                var title = "{{ $dcr['extendedProps']['sourcing_blood_bank_name']  ?? 'DCR Location' }}";

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
        const acceptBtn = $('#acceptBtn'); // If needed
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

        acceptBtn.on('click', function (e) { // If you have confirmation for accept
            e.preventDefault(); // Prevent the default form submission

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to accept this DCR?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#007bff', // Blue color for accept
                cancelButtonColor: '#6c757d', // Gray color for cancel
                confirmButtonText: 'Yes, accept it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Set the status value and submit the form
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'status',
                        value: 'accepted'
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