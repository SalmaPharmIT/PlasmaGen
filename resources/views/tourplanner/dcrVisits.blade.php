@extends('include.dashboardLayout')

@section('title', 'DCR Visit Details')

@section('content')

@section('content')
<div class="pagetitle d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <div>
            <h1>DCR Visit Lists</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('tourplanner.finalDCR') }}">Final DCR</a></li>
                    <li class="breadcrumb-item active">DCR Visit Details</li>
                </ol>
            </nav>
        </div>
        <!-- Move status display here -->
        <div id="statusDisplay" class="ms-4">
            <span id="mgrStatusDisplay" class="bg-warning p-2 rounded text-white fw-bold me-2"></span>
            <span id="caStatusDisplay" class="bg-info p-2 rounded text-white fw-bold"></span>
            <p class="mt-2" id="remarksDisplay">Remarks</p>
        </div>
    </div>
    <!-- Status Display next to the action buttons -->
    <div class="d-flex align-items-center">
        {{-- <div id="statusDisplay" class="me-3">
            <span id="mgrStatusDisplay" class="bg-warning p-2 rounded text-white fw-bold me-2"></span>
            <span id="caStatusDisplay" class="bg-info p-2 rounded text-white fw-bold"></span>
        </div> --}}
        <div>
            <form method="POST" action="{{ route('tourplanner.dcr.updateStatus',['id' => 'PLACEHOLDER']) }}" id="statusForm">
                @csrf
                @php $roleId = Auth::user()->role_id; @endphp
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
</div><!-- End Page Title -->

<section class="section">
    <div class="row">
        <!-- Left Column: Cards Listing -->
        <div class="col-md-4">
            <div id="cardsContainer" class="bg-white p-3">
                <!-- Cards will be appended here by JavaScript -->
            </div>
        </div>
        <!-- Right Column: Details (initially empty) -->
        <div class="col-md-8">
            <div id="dcrDetailsContainer">
                <div class="card">
                    <div class="card-body text-center mt-4">
                        <h5>Select an item from the list to view details.</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
    <style>
        /* Ensure the container background is white */
        #cardsContainer {
            background-color: #fff;
        }
        /* Updated card styles for a listing-like look */
        .dcr-card {
            border: 1px solid #012970; /* Blue border */
            border-radius: 4px;
            margin-bottom: 10px;
            padding: 10px;
            cursor: pointer;
            transition: box-shadow 0.2s, background-color 0.2s;
        }
        .dcr-card:hover {
            box-shadow: 0 0 8px rgba(0,0,0,0.2);
            background-color: #f8f9fa;
        }
        .dcr-card h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: bold;
        }
        .dcr-card p {
            font-size: 0.9rem;
            margin: 2px 0;
        }
        /* Text alignment utility */
        .text-right {
            text-align: right;
        }

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

<script>
        // Pass the PHP data (from the controller) to a JavaScript variable
        var dcrData = @json($dcrData);

        $(document).ready(function() {

            // Define route URL for fetching DCR details (this route returns the HTML for details)
            var dcrDetailsRoute = "{{ route('tourplanner.dcr-details', ['id' => ':id']) }}";

            // Reference to the container for the cards (left column)
            var $cardsContainer = $('#cardsContainer');

            // If dcrData is available, populate the cards:
            if (dcrData && dcrData.success) {
                var events = dcrData.data;
                $.each(events, function(index, event) {
                    var tourPlanType = "-";
                    if (event.extendedProps.tour_plan_type === 1) {
                        tourPlanType = 'Collections';
                    } else if (event.extendedProps.tour_plan_type === 2) {
                        tourPlanType = 'Sourcing';
                    } else if (event.extendedProps.tour_plan_type === 3) {
                        tourPlanType = 'Both';
                    }

                    var visitDate = event.visit_date ? event.visit_date : '-';
                    var time = event.time ? formatTime(event.time) : '-';
                    var tp_status = event.extendedProps.status ? capitalizeFirstLetter(event.extendedProps.status.replace('_', ' ')) : '-';
                    
                    // Optionally, if you have an "end_time" field, set it:
                    var endTime = event.end_time ? formatTime(event.end_time) : '-';

                    // Build the card HTML with a two/three row layout:
                    var cardHtml = `
                        <div class="dcr-card" data-id="${event.id}">
                            <div class="row">
                                <div class="col-12">
                                    <h5>${event.title}</h5>
                                </div>
                            </div>

                             <div class="row mt-1">
                                <div class="col-8">
                                    <p>Type: ${tourPlanType}</p>
                                </div>
                                 <div class="col-4 text-right">
                                    <p>${visitDate}</p>
                                </div>
                            </div>

                            <div class="row mt-1">
                               <div class="col-8">
                                    <p>${tp_status}</p>
                                </div>
                               <div class="col-4 text-right">
                                    <p>${time}</p>
                                </div>
                            </div>
                            
                        </div>
                    `;
                    $cardsContainer.append(cardHtml);
                });
            }

            // Utility function: Format time (e.g., "14:30:00" to "14:30")
            function formatTime(timeStr) {
                if (!timeStr) return '-';
                var parts = timeStr.split(':');
                return `${parts[0]}:${parts[1]}`;
            }

            // Utility function: Capitalize first letter
            function capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            // Attach click event handler to the cards
            $cardsContainer.on('click', '.dcr-card', function() {
                var id = $(this).data('id');
                // Build the details URL using the placeholder replacement
                var url = dcrDetailsRoute.replace(':id', id);
                // Show a loading indicator in the right column
                $('#dcrDetailsContainer').html('<div class="card"><div class="card-body text-center mt-4"><h5>Loading details...</h5></div></div>');

                // Load the details via AJAX into the right-side container
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(html) {
                        $('#dcrDetailsContainer').html(html);
                        // Reinitialize the map for the newly loaded content
                        if (typeof initMap === 'function') {
                            initMap();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching DCR Details:", error);
                        $('#dcrDetailsContainer').html('<div class="card"><div class="card-body text-center text-danger"><p>Error loading details.</p></div></div>');
                    }
                });
            });


            // DCR Approve, Reject Status Fetch 
            // Parse query parameters from the URL
            var urlParams = new URLSearchParams(window.location.search);
            var id = urlParams.get('id');
            var empId = urlParams.get('emp_id');
            var visitDate = urlParams.get('visit_date');

            if (id) {
                // Get the update URL with the placeholder
                var updateUrl = "{{ route('tourplanner.dcr.updateStatus', ['id' => 'PLACEHOLDER']) }}";
                // Replace the placeholder with the actual id
                updateUrl = updateUrl.replace('PLACEHOLDER', id);
                // Set the form's action attribute to the updated URL
                $('#statusForm').attr('action', updateUrl);
            }

            // Only proceed if all required query parameters exist
            if (id && empId && visitDate) {

                $.ajax({
                    url: "{{ route('tourplanner.dcrStatus') }}",
                    type: 'GET',
                    dataType: 'json', // ensures the response is parsed as JSON
                    data: {
                        id: id,
                        emp_id: empId,
                        visit_date: visitDate
                    },
                    success: function(response) {
                        console.log("dcr status result", response);
                        if (response.success) {
                            if (response.data.length > 0) {
                                var dcr = response.data[0];
                                $('#mgrStatusDisplay').text("Manager Status: " + capitalizeFirstLetter(dcr.manager_status));
                                $('#caStatusDisplay').text("CA Status: " + capitalizeFirstLetter(dcr.ca_status));
                                $('#remarksDisplay').text(function() {
                                    // Initialize the display text
                                    var remarksText = "";

                                    // Check if manager status remarks are available
                                    if (dcr.manager_status_remarks && dcr.manager_status_remarks.trim() !== "") {
                                        remarksText += "Mgr Remarks: " + capitalizeFirstLetter(dcr.manager_status_remarks);
                                    }

                                    // Check if CA status remarks are available
                                    if (dcr.ca_status_remarks && dcr.ca_status_remarks.trim() !== "") {
                                        // If there's already text, add a separator
                                        if (remarksText) {
                                            remarksText += " | ";
                                        }
                                        remarksText += "CA Remarks: " + capitalizeFirstLetter(dcr.ca_status_remarks);
                                    }

                                    // Display the final remarks text (if any)
                                    return remarksText || ""; // Default message if no remarks
                                });
                            } else {
                                $('#mgrStatusDisplay').text("Manager Status: N/A");
                                $('#caStatusDisplay').text("CA Status: N/A");
                                $('#remarksDisplay').text("");
                            }
                        } else {
                            $('#mgrStatusDisplay').text("Manager Status: N/A");
                            $('#caStatusDisplay').text("CA Status: N/A");
                            $('#remarksDisplay').text("");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching DCR status:", error);
                    }
                });
            
            }



            // Update Approve, Reject Status
            const approveBtn = $('#approveBtn');
            const rejectBtn = $('#rejectBtn');
            const acceptBtn = $('#acceptBtn');
            const statusForm = $('#statusForm');

            approveBtn.on('click', function (e) {
                e.preventDefault(); // Prevent the default form submission

                Swal.fire({
                    title: 'Are you sure?',
                    //text: "Do you really want to approve this DCR?",
                    html: `<p>Do you really want to approve this DCR?</p><textarea class="form-control" id="remarks" class="swal2-input" placeholder="Enter your remarks here..." rows="3"></textarea>`,     
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745', // Green color for approve
                    cancelButtonColor: '#6c757d', // Gray color for cancel
                    confirmButtonText: 'Yes, approve it!',
                    preConfirm: () => {
                        const remarks = document.getElementById('remarks').value;
                        // if (!remarks) {
                        //     Swal.showValidationMessage('Remarks cannot be empty');
                        //     return false;
                        // }
                        return remarks; // Return remarks to be included in the form submission
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                         // Add remarks to the form and submit
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'remarks',
                            value: result.value
                        }).appendTo(statusForm);

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
                   // text: "Do you really want to reject this DCR?",
                    html: `<p>Do you really want to reject this DCR?</p><textarea class="form-control"  id="remarks" class="swal2-input" placeholder="Enter your remarks here..." rows="3"></textarea>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545', // Red color for reject
                    cancelButtonColor: '#6c757d', // Gray color for cancel
                    confirmButtonText: 'Yes, reject it!',
                    preConfirm: () => {
                        const remarks = document.getElementById('remarks').value;
                        if (!remarks) {
                            Swal.showValidationMessage('Remarks cannot be empty');
                            return false;
                        }
                        return remarks; // Return remarks to be included in the form submission
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Add remarks to the form and submit
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'remarks',
                            value: result.value
                        }).appendTo(statusForm);

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

            acceptBtn.on('click', function (e) {
                e.preventDefault(); // Prevent the default form submission

                Swal.fire({
                    title: 'Are you sure?',
                    // text: "Do you really want to accept this DCR?",
                    html: `<p>Do you really want to approve this DCR?</p><textarea class="form-control" id="remarks" class="swal2-input" placeholder="Enter your remarks here..." rows="3"></textarea>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745', // Green color for approve
                    cancelButtonColor: '#6c757d', // Gray color for cancel
                    confirmButtonText: 'Yes, accept it!',
                    preConfirm: () => {
                        const remarks = document.getElementById('remarks').value;
                        // if (!remarks) {
                        //     Swal.showValidationMessage('Remarks cannot be empty');
                        //     return false;
                        // }
                        return remarks; // Return remarks to be included in the form submission
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Add remarks to the form and submit
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'remarks',
                            value: result.value
                        }).appendTo(statusForm);

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

    <!-- Load Google Maps JavaScript API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFwtHIaHQ1J8PKur9RmQy4Z5WsM6kVVPE&callback=initMap" async defer></script>


<script>
    // Define initMap globally

    // Initialize Collection Map
    function initCollectionMap() {
        var collectionMapContainer = document.getElementById('collectionMap');
        if (!collectionMapContainer) {
            console.warn('Collection map container not found.');
            return;
        }
        
        var latInput = document.getElementById('collectionLatitude');
        var lngInput = document.getElementById('collectionLongitude');
        if (!latInput || !lngInput || !latInput.value || !lngInput.value) {
            console.log("No collection latitude/longitude data available.");
            return;
        }
        
        var latitude = parseFloat(latInput.value);
        var longitude = parseFloat(lngInput.value);
        var title = document.getElementById('collectionMapTitle') ? document.getElementById('collectionMapTitle').value : 'DCR Location';
        
        var map = new google.maps.Map(collectionMapContainer, {
            center: { lat: latitude, lng: longitude },
            zoom: 15
        });
        
        var marker = new google.maps.Marker({
            position: { lat: latitude, lng: longitude },
            map: map,
            title: title
        });
        
        var infoWindow = new google.maps.InfoWindow({
            content: `<strong>${title}</strong>`
        });
        
        marker.addListener('click', function() {
            infoWindow.open(map, marker);
        });
        
        infoWindow.open(map, marker);
    }

    // Initialize Sourcing Map
    function initSourcingMap() {
        var sourcingMapContainer = document.getElementById('sourcingMap');
        if (!sourcingMapContainer) {
            console.warn('Sourcing map container not found.');
            return;
        }
        
        var latInput = document.getElementById('sourcingLatitude');
        var lngInput = document.getElementById('sourcingLongitude');
        if (!latInput || !lngInput || !latInput.value || !lngInput.value) {
            console.log("No sourcing latitude/longitude data available.");
            return;
        }
        
        var latitude = parseFloat(latInput.value);
        var longitude = parseFloat(lngInput.value);
        var title = document.getElementById('sourcingMapTitle') ? document.getElementById('sourcingMapTitle').value : 'DCR Location';
        
        var map = new google.maps.Map(sourcingMapContainer, {
            center: { lat: latitude, lng: longitude },
            zoom: 15
        });
        
        var marker = new google.maps.Marker({
            position: { lat: latitude, lng: longitude },
            map: map,
            title: title
        });
        
        var infoWindow = new google.maps.InfoWindow({
            content: `<strong>${title}</strong>`
        });
        
        marker.addListener('click', function() {
            infoWindow.open(map, marker);
        });
        
        infoWindow.open(map, marker);
    }

    // Global initMap function (called by the Google Maps API callback)
    function initMap() {
        // Try to initialize both maps. Only the one(s) present in the DOM will be initialized.
        initCollectionMap();
        initSourcingMap();
    }
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
                 icon: 'warning',
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
                 icon: 'warning',
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
