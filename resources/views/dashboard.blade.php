<!-- resources/views/dashboard.blade.php -->

@extends('include.dashboardLayout')

@section('title', 'Dashboard')

@section('content')
<div class="pagetitle">
  <h1>Dashboard</h1>
  <nav class="d-flex justify-content-between align-items-center">
    <!-- Breadcrumb on the left -->
    <ol class="breadcrumb m-0">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
    
    <!-- Filter dropdown on the right -->
    <div class="filter d-flex align-items-center">
      <!-- Visible label showing the selected filter -->
      <span id="selectedFilter" class="me-2 fw-bold">This Month</span>
      <!-- Dropdown toggle icon -->
      <a class="icon"  data-bs-toggle="dropdown">
        <i class="bi bi-caret-down-fill"></i>
      </a>
      <!-- Dropdown menu -->
      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        <li class="dropdown-header text-start">
          <h6>Filter</h6>
        </li>
        <!-- Marking "This Month" as active by default -->
        <li>
          <a class="dropdown-item active"  onclick="updateFilter('This Month')">This Month</a>
        </li>
        <li>
          <a class="dropdown-item" onclick="updateFilter('Last 3 Months')">Last 3 Months</a>
        </li>
        <li>
          <a class="dropdown-item"  onclick="updateFilter('Last 6 Months')">Last 6 Months</a>
        </li>
        <li>
          <a class="dropdown-item"  onclick="updateFilter('Last 12 Months')">Last 12 Months</a>
        </li>
        <li>
          <a class="dropdown-item"  onclick="updateFilter('All')">All</a>
        </li>
      </ul>
    </div>
  </nav>
</div>
<!-- End Page Title -->

  <section class="section dashboard">
    <div class="row">

      <!-- Left side columns -->
      <div class="col-lg-12">
        <div class="row">

          <!-- Sales Card -->
          <div class="col-xxl-3 col-md-6">
            <a href="{{ route('bloodbank.index') }}" style="text-decoration: none; color: inherit;">
              <div class="card info-card sales-card">
              
                <div class="card-body">
                  <h5 class="card-title">Blood Banks</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-droplet-half"></i>
                    </div>
                    <div class="ps-3">
                      <h6>0</h6>
                  
                    </div>
                  </div>
                </div>

              </div>
            </a>
          </div><!-- End Sales Card -->

          <!-- Revenue Card -->
          <div class="col-xxl-3 col-md-6">
            <a href="{{ route('warehouse.index') }}" style="text-decoration: none; color: inherit;">
              <div class="card info-card revenue-card">

                <div class="card-body">
                  <h5 class="card-title">Warehouses</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-building"></i>
                    </div>
                    <div class="ps-3">
                      <h6>0</h6>
                      {{-- <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span> --}}

                    </div>
                  </div>
                </div>

              </div>
            </a>
          </div><!-- End Revenue Card -->

           <!-- Total Collected Plasma Qty Card -->
           <div class="col-xxl-3 col-xl-12">
            <div class="card info-card collected-plasma-card" style="cursor: pointer;">

              <div class="card-body">
                <h5 class="card-title">Collected Plasma </h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-droplet-fill"></i>
                  </div>
                  <div class="ps-3">
                    <h6>0</h6>
                  
                  </div>
                </div>

              </div>
            </div>
          </div>
          <!-- End Total Collected Plasma Qty Card -->

          <!-- Customers Card -->
          <div class="col-xxl-3 col-xl-12">
            <a href="{{ route('users.index') }}" style="text-decoration: none; color: inherit;">
              <div class="card info-card customers-card">

                <div class="card-body">
                  <h5 class="card-title">Employees </h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <div class="ps-3">
                      <h6>0</h6>
                    
                    </div>
                  </div>

                </div>
              </div>
            </a>

          </div>
          <!-- End Customers Card -->

           <!-- Total Planned Plasma Qty Card -->
           {{-- <div class="col-xxl-3 col-xl-12">

            <div class="card info-card planned-plasma-card">

              <div class="card-body">
                <h5 class="card-title">Planned Plasma </h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-droplet"></i>
                  </div>
                  <div class="ps-3">
                    <h6>0</h6>
                  
                  </div>
                </div>

              </div>
            </div>

          </div> --}}
          <!-- End Total Planned Plasma Qty Card -->

         

        </div>
      </div><!-- End Left side columns -->
    </div>

    <!-- Second Row: Blood Banks Map Section (initially hidden) -->
    <div class="row" id="bloodBankMapsSection" style="display: none;">
      <!-- Map Container (will be 100% width initially) -->
      <div class="col-lg-12 map-container">
        <div class="card info-card top-performance-bloodbanks-card">
          <div class="card-body">
            <h5 class="card-title">Blood Banks Map</h5>
            <div class="activity" id="bloodBankMap">
              <div id="collectionMap" style="width: 100%; height: 500px;"></div>
            </div>
          </div>
        </div>
      </div>
      <!-- Details Container (hidden initially) -->
      <div class="col-lg-4 details-container" style="display: none;">
        <div class="card info-card top-performance-bloodbanks-card">
          <div class="card-body">
            {{-- Blood Bank details will be populated here after marker click --}}
            <div class="activity" id="bloodBankDetails"></div>
          </div>
        </div>
      </div>
    </div>

     <!-- Third Row -->
     <div class="row">
     
        <div class="col-lg-8">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Reports</h5>

              <!-- Line Chart -->
              <div id="reportsChart"></div>

           

            </div>

          </div>
        </div>

        <!-- Right side columns -->
        <div class="col-lg-4">
          <!-- Top Performing Blood Banks Card -->
          <div class="card info-card top-performance-bloodbanks-card">
            <div class="card-body">
              <h5 class="card-title">Top Performing Blood Banks</h5>
              <div class="activity" id="topPerformanceBloodbanks">
                Loading...
              </div>
            </div>
          </div>

          <!-- Top Performing Areas Card -->
          <div class="card info-card top-performance-areas-card">
            <div class="card-body">
              <h5 class="card-title">Top Performing Areas</h5>
              <div class="activity" id="topPerformanceAreas">
                Loading...
              </div>
            </div>
          </div>

        </div><!-- End Right side columns -->
    <div>
  </section>


@endsection

@push('styles')
<style>
  #bloodBankDetails {
    height: 570px;
    overflow-y: auto;
    scrollbar-width: thin; /* Firefox */
  }
  
  /* WebKit browsers */
  #bloodBankDetails::-webkit-scrollbar {
    width: 8px; /* Set desired width */
  }
  
  #bloodBankDetails::-webkit-scrollbar-track {
    background: #f1f1f1;
  }
  
  #bloodBankDetails::-webkit-scrollbar-thumb {
    background: #888;
  }
  
  #bloodBankDetails::-webkit-scrollbar-thumb:hover {
    background: #555;
  }
</style>
@endpush

@push('scripts')

<!-- Global function definitions: these are available to the Maps API callback -->
<script>
  // Define the collection map initialization function
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
        title: title,
        icon: {
          url: "{{ asset('assets/img/location.png') }}",
          scaledSize: new google.maps.Size(50, 50) // Adjust the width and height as needed
        }
    });
    
    var infoWindow = new google.maps.InfoWindow({
        content: `<strong>${title}</strong>`
    });
    
    marker.addListener('click', function() {
        infoWindow.open(map, marker);
    });
    
    infoWindow.open(map, marker);
  }
  
  // Expose initMap globally so that the Maps API callback finds it
  window.initMap = function() {
    initCollectionMap();
  };
  
  // Expose updateFilter globally if needed in inline HTML onclick attributes
  window.updateFilter = function(value) {
    console.log('updateFilter', value);
    document.getElementById('selectedFilter').innerText = value;
    document.querySelectorAll('.dropdown-item').forEach(function(item) {
      item.classList.remove('active');
      if (item.innerText.trim() === value) {
        item.classList.add('active');
      }
    });

    // Re-call all dashboard loader functions with the new filter value.
    window.loadDashboardData(value);
    window.loadDashboardGraphData(value);
    window.loadDashboardBloodBankMap(value);
  };
</script>

    <script>

        $(document).ready(function() {

           let reportsChartInstance = null;

            // Initially hide the Blood Banks Map section (second row)
            $("#bloodBankMapsSection").hide();

            // When the Collected Plasma card is clicked...
            $(".collected-plasma-card").on('click', function() {
              // Show the entire second row
              $("#bloodBankMapsSection").show();
              // Make the map container full width (col-lg-12) and hide details column
              $("#bloodBankMapsSection .map-container")
                .removeClass("col-lg-8")
                .addClass("col-lg-12");
              $("#bloodBankMapsSection .details-container").hide();
            });

               // Define the dashboard loader functions with an optional filter parameter.
              function loadDashboardData(filter = 'This Month') {
                $.ajax({
                    url: "{{ route('dashboard.getDashboardData') }}",
                    type: 'GET',
                    // Pass the filter as a GET parameter.
                    data: { filter: filter },
                    success: function(response) {
                        if(response.success) {
                          const data = response.data;

                          // Update Blood Bank Count
                          $(".sales-card .card-body h6").text(data.blood_bank_count ?? 0);

                          // Update Warehouse Count
                          $(".revenue-card .card-body h6").text(data.warehouse_count  ?? 0);

                           // Update Users Count
                           $(".customers-card .card-body h6").text(data.user_count  ?? 0);

                          // Update Planned Plasma Count
                          $(".planned-plasma-card .card-body h6").text(data.tour_plan_total_quantity  ?? 0);

                          // Update Collected Plasma Count
                          $(".collected-plasma-card .card-body h6").text(data.tour_plan_total_available_quantity  ?? 0);

                         // Update Top Performing Blood Banks
                        if(data.top_performance_bloodbanks && data.top_performance_bloodbanks.length > 0) {
                          let bloodbanksHtml = "";
                          // Define an array of color classes for blood banks
                          const bloodColors = ["text-success", "text-info", "text-warning", "text-primary", "text-danger"];
                          data.top_performance_bloodbanks.forEach((name, index) => {
                            // Cycle through colors based on index
                            let colorClass = bloodColors[index % bloodColors.length];
                            bloodbanksHtml += `<div class="activity-item d-flex">
                              <div class="activite-label">Top ${index + 1}</div>
                              <i class="bi bi-circle-fill activity-badge ${colorClass} align-self-start"></i>
                              <div class="activity-content">${name}</div>
                            </div>`;
                          });
                          $("#topPerformanceBloodbanks").html(bloodbanksHtml);
                        } else {
                          $("#topPerformanceBloodbanks").html("No data available.");
                        }

                        // Update Top Performing Areas
                        if(data.top_performance_areas && data.top_performance_areas.length > 0) {
                          let areasHtml = "";
                          // Define an array of color classes for areas (or reuse the same)
                          const areaColors = ["text-primary", "text-secondary", "text-success", "text-warning", "text-danger"];
                          data.top_performance_areas.forEach((area, index) => {
                            let colorClass = areaColors[index % areaColors.length];
                            areasHtml += `<div class="activity-item d-flex">
                              <div class="activite-label">Top ${index + 1}</div>
                              <i class="bi bi-circle-fill activity-badge ${colorClass} align-self-start"></i>
                              <div class="activity-content">${area}</div>
                            </div>`;
                          });
                          $("#topPerformanceAreas").html(areasHtml);
                        } else {
                          $("#topPerformanceAreas").html("No data available.");
                        }



                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching dashboard data:", error);
                        Swal.fire('Error', 'An error occurred while fetching dashboard data.', 'error');
                    }
                });
            }


             // Function to load dashboard graph data and render the Reports chart
            //  function loadDashboardGraphData(filter = 'This Month') {
            //     $.ajax({
            //         url: "{{ route('dashboard.getDashboardGraphData') }}",
            //         type: 'GET',
            //         data: { filter: filter },
            //         success: function(response) {
            //             if(response.success) {
            //                 const data = response.data;
            //                 // Reverse the array if the API returns months in descending order so that the chart displays in chronological order
            //                 data.reverse();
                            
            //                 // Prepare arrays for the chart series and x-axis labels
            //                 const months = data.map(item => item.month);
            //                 const bloodBanks = data.map(item => item.blood_bank_count);
            //                 const warehouses = data.map(item => item.warehouse_count);
            //                 const customers = data.map(item => item.customer_count);

            //                 // Render the ApexCharts Reports Chart with dynamic data
            //                 new ApexCharts(document.querySelector("#reportsChart"), {
            //                     series: [{
            //                         name: 'Blood Banks',
            //                         data: bloodBanks,
            //                     }, {
            //                         name: 'Warehouses',
            //                         data: warehouses,
            //                     }, {
            //                         name: 'Customers',
            //                         data: customers,
            //                     }],
            //                     chart: {
            //                         height: 350,
            //                         type: 'area',
            //                         toolbar: {
            //                             show: false
            //                         },
            //                     },
            //                     markers: {
            //                         size: 4
            //                     },
            //                     colors: ['#4154f1', '#2eca6a', '#ff771d'],
            //                     fill: {
            //                         type: "gradient",
            //                         gradient: {
            //                             shadeIntensity: 1,
            //                             opacityFrom: 0.3,
            //                             opacityTo: 0.4,
            //                             stops: [0, 90, 100]
            //                         }
            //                     },
            //                     dataLabels: {
            //                         enabled: false
            //                     },
            //                     stroke: {
            //                         curve: 'smooth',
            //                         width: 2
            //                     },
            //                     xaxis: {
            //                         type: 'category',
            //                         categories: months,
            //                     },
            //                     tooltip: {
            //                         x: {
            //                             format: 'yyyy-MM'
            //                         },
            //                     }
            //                 }).render();
            //             } else {
            //                 Swal.fire('Error', response.message, 'error');
            //             }
            //         },
            //         error: function(xhr, status, error) {
            //             console.error("Error fetching dashboard graph data:", error);
            //             Swal.fire('Error', 'An error occurred while fetching dashboard graph data.', 'error');
            //         }
            //     });
            // }

            function loadDashboardGraphData(filter = 'This Month') {

              // HARD RESET (only if you plan to recreate the chart below)
              if (reportsChartInstance) {
                reportsChartInstance.destroy();
                reportsChartInstance = null;
                $('#reportsChart').empty();
              }
              
              $.ajax({
                url: "{{ route('dashboard.getDashboardGraphData') }}",
                type: 'GET',
                data: { filter: filter },
                success: function(response) {
                  if (!response.success) {
                    Swal.fire('Error', response.message, 'error');
                    return;
                  }

                  const data = Array.isArray(response.data) ? [...response.data] : [];
                  if (data.length === 0) {
                    // If no data, either clear or show an empty chart
                    if (reportsChartInstance) {
                      reportsChartInstance.updateSeries([{name:'Blood Banks', data:[]},{name:'Warehouses', data:[]},{name:'Customers', data:[]}], true);
                      reportsChartInstance.updateOptions({ xaxis: { categories: [] } }, false, true);
                    } else {
                      $('#reportsChart').empty().append('<div class="text-muted small">No data available for the selected range.</div>');
                    }
                    return;
                  }

                  // If your API returns newest-first, reverse to chronological
                  // (If API is already chronological, remove the next line)
                  data.reverse();

                  const months     = data.map(item => String(item.month ?? ''));
                  const bloodBanks = data.map(item => Number(item.blood_bank_count ?? 0));
                  const warehouses = data.map(item => Number(item.warehouse_count ?? 0));
                  const customers  = data.map(item => Number(item.customer_count ?? 0));

                  // First time: create chart
                  if (!reportsChartInstance) {
                    const options = {
                      series: [
                        { name: 'Blood Banks', data: bloodBanks },
                        { name: 'Warehouses', data: warehouses },
                        { name: 'Customers',  data: customers  }
                      ],
                      chart: {
                        height: 350,
                        type: 'area',
                        toolbar: { show: false }
                      },
                      markers: { size: 4 },
                      colors: ['#4154f1', '#2eca6a', '#ff771d'],
                      fill: {
                        type: "gradient",
                        gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.4, stops: [0, 90, 100] }
                      },
                      dataLabels: { enabled: false },
                      stroke: { curve: 'smooth', width: 2 },
                      xaxis: {
                        type: 'category',
                        categories: months
                      },
                      tooltip: {
                        // With category x-axis, don't force a datetime format
                        x: { formatter: (val) => val }
                      }
                    };

                    // Ensure container is clean before first render
                    $('#reportsChart').empty();
                    reportsChartInstance = new ApexCharts(document.querySelector("#reportsChart"), options);
                    reportsChartInstance.render();
                    return;
                  }

                  // Subsequent times: update existing chart (no re-render)
                  reportsChartInstance.updateSeries([
                    { name: 'Blood Banks', data: bloodBanks },
                    { name: 'Warehouses', data: warehouses },
                    { name: 'Customers',  data: customers  }
                  ], true);

                  reportsChartInstance.updateOptions({
                    xaxis: { type: 'category', categories: months }
                  }, false, true);
                },
                error: function(xhr, status, error) {
                  console.error("Error fetching dashboard graph data:", error);
                  Swal.fire('Error', 'An error occurred while fetching dashboard graph data.', 'error');
                }
              });
            }


             // Function to load dashboard blood bank mapview
             function loadDashboardBloodBankMap(filter = 'This Month') {
                $.ajax({
                    url: "{{ route('dashboard.getDashboardBloodBanksMapData') }}",
                    type: 'GET',
                    data: { filter: filter },
                    success: function(response) {
                        if(response.success) {
                            const data = response.data;
                            // response.data is expected to be an array of blood bank objects
                            const bloodBanksData = response.data;
                            initBloodBankMap(bloodBanksData);
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching dashboard graph data:", error);
                        Swal.fire('Error', 'An error occurred while fetching dashboard graph data.', 'error');
                    }
                });
            }


          // Attach loader functions to window object so that updateFilter can call them
          window.loadDashboardData = loadDashboardData;
          window.loadDashboardGraphData = loadDashboardGraphData;
          window.loadDashboardBloodBankMap = loadDashboardBloodBankMap;

          // Initially call the loader functions with default filter "This Month"
           loadDashboardData();
           loadDashboardGraphData();
           loadDashboardBloodBankMap();
           
         

          // Function to initialize the blood bank map using the data from the API
          function initBloodBankMap(bloodBanks) {
              if (!bloodBanks || bloodBanks.length === 0) {
                  console.warn('No blood bank data available.');
                  return;
              }

              // Center map on the first blood bank's location (or compute an average if you prefer)
              var firstBank = bloodBanks[0];
              var centerLat = parseFloat(firstBank.latitude) || 0;
              var centerLng = parseFloat(firstBank.longitude) || 0;

              // Create the map in the container (div id="collectionMap")
              var map = new google.maps.Map(document.getElementById('collectionMap'), {
                //  center: {lat: centerLat, lng: centerLng},
                  center: { lat: 0, lng: 0 },
                  zoom: 8 // This zoom value will be overridden by fitBounds.
              });

              // Create a LatLngBounds object to adjust the map view automatically.
              var bounds = new google.maps.LatLngBounds();


              // Create a single infoWindow to be re-used
              var infoWindow = new google.maps.InfoWindow();

              // Loop through blood banks and create markers
              bloodBanks.forEach(function(bank) {
                  var bankLat = parseFloat(bank.latitude);
                  var bankLng = parseFloat(bank.longitude);

                  // Create marker for current blood bank
                  var marker = new google.maps.Marker({
                      position: {lat: bankLat, lng: bankLng},
                      map: map,
                      title: bank.blood_bank_name,
                      icon: {
                        url: "{{ asset('assets/img/location.png') }}",
                        scaledSize: new google.maps.Size(50, 50) // Adjust the width and height as needed
                      }
                  });

                  // Extend the bounds to include this marker's location
                  bounds.extend(marker.getPosition());

                  // Setup click listener for marker
                  marker.addListener('click', function() {

                    // On marker click, adjust layout:
                    // Change map column to 8-wide and show the details column
                    $("#bloodBankMapsSection .map-container")
                      .removeClass("col-lg-12")
                      .addClass("col-lg-8");
                    $("#bloodBankMapsSection .details-container").show();
                    
                      // Build content for the info window (a quick summary)
                      var infoContent = `
                          <div>
                              <strong>${bank.blood_bank_name}</strong><br>
                              ${bank.address ? bank.address : ''}<br>
                              ${bank.contact_person ? 'Contact: ' + bank.contact_person : ''}
                          </div>
                      `;
                      infoWindow.setContent(infoContent);
                      infoWindow.open(map, marker);

                      const fullAddress = [bank.address, bank.pincode, bank.city_name, bank.state_name, bank.country_name]
                                          .filter(Boolean)
                                          .join(', ');

                      // Build detailed content for the #bloodBankDetails div
                      var detailsContent = `
                          <h5 class="card-title">${bank.blood_bank_name}</h5>
                          <p><strong>Total Collections:</strong> ${bank.total_number_of_collections || 0}</p>
                          <p><strong>Total Collected Qty:</strong> ${bank.sum_of_available_quantity || 0}</p>
                           <p><strong>Contact Person:</strong> ${bank.contact_person ? bank.contact_person : '-'}</p>
                          <p><strong>Email:</strong> ${bank.email ? bank.email : '-'}</p>
                          <p><strong>Mobile:</strong> ${bank.mobile_no ? bank.mobile_no : '-'}</p>
                          <p><strong>Address:</strong> ${fullAddress}</p>
                          <h5 class="card-title">Latest Collection Details</h5>
                          `;
                   

                      // Define an array of color classes to style each collection entry
                      const collectionColors = ["text-primary", "text-secondary",  "text-warning", "text-success", "text-danger"];

                      // Check if there are collection details to display
                      if (bank.collection_details && bank.collection_details.length > 0) {
                          bank.collection_details.forEach((collection, index) => {
                              let colorClass = collectionColors[index % collectionColors.length];

                              detailsContent += `
                                  <div class="activity-item d-flex mb-2">
                                      <div class="activite-label me-2">Collection ${index + 1}</div>
                                      <i class="bi bi-droplet-fill activity-badge ${colorClass} align-self-start me-2"></i>
                                      <div class="activity-content">
                                          <strong>Date:</strong> ${collection.start}<br>
                                          <strong>Executive:</strong> ${collection.extendedProps.collecting_agent_name || '-'}<br>
                                          <strong>Planned:</strong> ${collection.extendedProps.quantity || 0}, 
                                          <strong>Collected:</strong> ${collection.extendedProps.available_quantity || 0}<br>
                                           <strong>Warehouse:</strong> ${collection.extendedProps.transport_details.warehouse_name || '-'}<br>
                                          <strong>Plasma Price:</strong> ${collection.extendedProps.price || 0}<br>
                                          <strong>Part-A:</strong> ${collection.extendedProps.part_a_invoice_price || 0},  
                                          <strong>Part-B:</strong> ${collection.extendedProps.part_b_invoice_price || 0},  
                                          <strong>Part-C:</strong> ${collection.extendedProps.part_c_invoice_price || 0}<br>
                                          <strong>Total Invoice Price:</strong> ${collection.extendedProps.collection_total_plasma_price || 0}<br>
                                          <strong>Boxes/Units/Litres:</strong> ${collection.extendedProps.num_boxes || 0} / 
                                          ${collection.extendedProps.num_units || 0} / ${collection.extendedProps.num_litres || 0}
                                      </div>
                                  </div>
                              `;
                          });
                      } else {
                          detailsContent += `<div class="activity-item">No collection details available.</div>`;
                      }


                      // Update the details section
                      document.getElementById('bloodBankDetails').innerHTML = detailsContent;
                  });
              });

              // Adjust the map's viewport to cover all markers
              map.fitBounds(bounds);

              // Optional: Set a maximum zoom level after fitting bounds (e.g., zoom level 15)
              var listener = google.maps.event.addListener(map, 'bounds_changed', function() {
                  if (map.getZoom() > 15) {
                      map.setZoom(15);
                  }
                  google.maps.event.removeListener(listener);
              });
          }
          
        });
    </script>


<!-- Load Google Maps JavaScript API using async & defer -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFwtHIaHQ1J8PKur9RmQy4Z5WsM6kVVPE&callback=initMap" async defer></script>

    
@endpush

