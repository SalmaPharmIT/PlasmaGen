<!-- resources/views/dashboard.blade.php -->

@extends('include.dashboardLayout')

@section('title', 'Dashboard')

@section('content')
  <div class="pagetitle">
    <h1>Dashboard</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section dashboard">
    <div class="row">

      <!-- Left side columns -->
      <div class="col-lg-8">
        <div class="row">

          <!-- Sales Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card sales-card">

              <div class="filter">
                {{-- <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a> --}}
                {{-- <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start">
                    <h6>Filter</h6>
                  </li>

                  <li><a class="dropdown-item" href="#">Today</a></li>
                  <li><a class="dropdown-item" href="#">This Month</a></li>
                  <li><a class="dropdown-item" href="#">This Year</a></li>
                </ul> --}}
              </div>

              <div class="card-body">
                <h5 class="card-title">Blood Banks</h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-droplet-half"></i>
                  </div>
                  <div class="ps-3">
                    <h6>0</h6>
                    {{-- <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span> --}}

                  </div>
                </div>
              </div>

            </div>
          </div><!-- End Sales Card -->

          <!-- Revenue Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card revenue-card">

              <div class="filter">
                {{-- <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a> --}}
                {{-- <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start">
                    <h6>Filter</h6>
                  </li>

                  <li><a class="dropdown-item" href="#">Today</a></li>
                  <li><a class="dropdown-item" href="#">This Month</a></li>
                  <li><a class="dropdown-item" href="#">This Year</a></li>
                </ul> --}}
              </div>

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
          </div><!-- End Revenue Card -->

          <!-- Customers Card -->
          <div class="col-xxl-4 col-xl-12">

            <div class="card info-card customers-card">

              <div class="filter">
                {{-- <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a> --}}
                {{-- <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start">
                    <h6>Filter</h6>
                  </li>

                  <li><a class="dropdown-item" href="#">Today</a></li>
                  <li><a class="dropdown-item" href="#">This Month</a></li>
                  <li><a class="dropdown-item" href="#">This Year</a></li>
                </ul> --}}
              </div>

              <div class="card-body">
                <h5 class="card-title">Customers </h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people"></i>
                  </div>
                  <div class="ps-3">
                    <h6>0</h6>
                    {{-- <span class="text-danger small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">decrease</span> --}}

                  </div>
                </div>

              </div>
            </div>

          </div>
          <!-- End Customers Card -->

           <!-- Total Planned Plasma Qty Card -->
           <div class="col-xxl-4 col-xl-12">

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

          </div>
          <!-- End Total Planned Plasma Qty Card -->

          <!-- Total Collected Plasma Qty Card -->
          <div class="col-xxl-4 col-xl-12">

          <div class="card info-card collected-plasma-card">

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

 
          <div class="col-12">
            <div class="card">

              <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start">
                    <h6>Filter</h6>
                  </li>

                  <li><a class="dropdown-item" href="#">Today</a></li>
                  <li><a class="dropdown-item" href="#">This Month</a></li>
                  <li><a class="dropdown-item" href="#">This Year</a></li>
                </ul>
              </div>

              <div class="card-body">
                <h5 class="card-title">Reports</h5>

                <!-- Line Chart -->
                <div id="reportsChart"></div>

                {{-- <script>
                  document.addEventListener("DOMContentLoaded", () => {
                    new ApexCharts(document.querySelector("#reportsChart"), {
                      series: [{
                        name: 'Blood Banks',
                        data: [31, 40, 28, 51, 42, 82, 56],
                      }, {
                        name: 'Wherehouses',
                        data: [11, 32, 45, 32, 34, 52, 41]
                      }, {
                        name: 'Customers',
                        data: [15, 11, 32, 18, 9, 24, 11]
                      }],
                      chart: {
                        height: 350,
                        type: 'area',
                        toolbar: {
                          show: false
                        },
                      },
                      markers: {
                        size: 4
                      },
                      colors: ['#4154f1', '#2eca6a', '#ff771d'],
                      fill: {
                        type: "gradient",
                        gradient: {
                          shadeIntensity: 1,
                          opacityFrom: 0.3,
                          opacityTo: 0.4,
                          stops: [0, 90, 100]
                        }
                      },
                      dataLabels: {
                        enabled: false
                      },
                      stroke: {
                        curve: 'smooth',
                        width: 2
                      },
                      xaxis: {
                        type: 'datetime',
                        categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
                      },
                      tooltip: {
                        x: {
                          format: 'dd/MM/yy HH:mm'
                        },
                      }
                    }).render();
                  });
                </script> --}}
                <!-- End Line Chart -->

              </div>

            </div>
          </div>
          <!-- End Reports -->

          <!-- Recent Sales -->
          {{-- <div class="col-12">
            <div class="card recent-sales overflow-auto">

              <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start">
                    <h6>Filter</h6>
                  </li>

                  <li><a class="dropdown-item" href="#">Today</a></li>
                  <li><a class="dropdown-item" href="#">This Month</a></li>
                  <li><a class="dropdown-item" href="#">This Year</a></li>
                </ul>
              </div>

              <div class="card-body">
                <h5 class="card-title">Recent Sales <span>| Today</span></h5>

                <table class="table table-borderless datatable">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Customer</th>
                      <th scope="col">Product</th>
                      <th scope="col">Price</th>
                      <th scope="col">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th scope="row"><a href="#">#2457</a></th>
                      <td>Brandon Jacob</td>
                      <td><a href="#" class="text-primary">At praesentium minu</a></td>
                      <td>$64</td>
                      <td><span class="badge bg-success">Approved</span></td>
                    </tr>
                    <tr>
                      <th scope="row"><a href="#">#2147</a></th>
                      <td>Bridie Kessler</td>
                      <td><a href="#" class="text-primary">Blanditiis dolor omnis similique</a></td>
                      <td>$47</td>
                      <td><span class="badge bg-warning">Pending</span></td>
                    </tr>
                    <tr>
                      <th scope="row"><a href="#">#2049</a></th>
                      <td>Ashleigh Langosh</td>
                      <td><a href="#" class="text-primary">At recusandae consectetur</a></td>
                      <td>$147</td>
                      <td><span class="badge bg-success">Approved</span></td>
                    </tr>
                    <tr>
                      <th scope="row"><a href="#">#2644</a></th>
                      <td>Angus Grady</td>
                      <td><a href="#" class="text-primar">Ut voluptatem id earum et</a></td>
                      <td>$67</td>
                      <td><span class="badge bg-danger">Rejected</span></td>
                    </tr>
                    <tr>
                      <th scope="row"><a href="#">#2644</a></th>
                      <td>Raheem Lehner</td>
                      <td><a href="#" class="text-primary">Sunt similique distinctio</a></td>
                      <td>$165</td>
                      <td><span class="badge bg-success">Approved</span></td>
                    </tr>
                  </tbody>
                </table>

              </div>

            </div>
          </div> --}}
          <!-- End Recent Sales -->

          <!-- Top Selling -->
          {{-- <div class="col-12">
            <div class="card top-selling overflow-auto">

              <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start">
                    <h6>Filter</h6>
                  </li>

                  <li><a class="dropdown-item" href="#">Today</a></li>
                  <li><a class="dropdown-item" href="#">This Month</a></li>
                  <li><a class="dropdown-item" href="#">This Year</a></li>
                </ul>
              </div>

              <div class="card-body pb-0">
                <h5 class="card-title">Top Selling <span>| Today</span></h5>

                <table class="table table-borderless">
                  <thead>
                    <tr>
                      <th scope="col">Preview</th>
                      <th scope="col">Product</th>
                      <th scope="col">Price</th>
                      <th scope="col">Sold</th>
                      <th scope="col">Revenue</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th scope="row"><a href="#"><img src="assets/img/product-1.jpg" alt=""></a></th>
                      <td><a href="#" class="text-primary fw-bold">Ut inventore ipsa voluptas nulla</a></td>
                      <td>$64</td>
                      <td class="fw-bold">124</td>
                      <td>$5,828</td>
                    </tr>
                    <tr>
                      <th scope="row"><a href="#"><img src="assets/img/product-2.jpg" alt=""></a></th>
                      <td><a href="#" class="text-primary fw-bold">Exercitationem similique doloremque</a></td>
                      <td>$46</td>
                      <td class="fw-bold">98</td>
                      <td>$4,508</td>
                    </tr>
                    <tr>
                      <th scope="row"><a href="#"><img src="assets/img/product-3.jpg" alt=""></a></th>
                      <td><a href="#" class="text-primary fw-bold">Doloribus nisi exercitationem</a></td>
                      <td>$59</td>
                      <td class="fw-bold">74</td>
                      <td>$4,366</td>
                    </tr>
                    <tr>
                      <th scope="row"><a href="#"><img src="assets/img/product-4.jpg" alt=""></a></th>
                      <td><a href="#" class="text-primary fw-bold">Officiis quaerat sint rerum error</a></td>
                      <td>$32</td>
                      <td class="fw-bold">63</td>
                      <td>$2,016</td>
                    </tr>
                    <tr>
                      <th scope="row"><a href="#"><img src="assets/img/product-5.jpg" alt=""></a></th>
                      <td><a href="#" class="text-primary fw-bold">Sit unde debitis delectus repellendus</a></td>
                      <td>$79</td>
                      <td class="fw-bold">41</td>
                      <td>$3,239</td>
                    </tr>
                  </tbody>
                </table>

              </div>

            </div>
          </div> --}}
          <!-- End Top Selling -->

        </div>
      </div><!-- End Left side columns -->

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



        <!-- Recent Activity -->
        {{-- <div class="card">
          <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li class="dropdown-header text-start">
                <h6>Filter</h6>
              </li>

              <li><a class="dropdown-item" href="#">Today</a></li>
              <li><a class="dropdown-item" href="#">This Month</a></li>
              <li><a class="dropdown-item" href="#">This Year</a></li>
            </ul>
          </div>

          <div class="card-body">
            <h5 class="card-title">Recent Activity <span>| Today</span></h5>

            <div class="activity">

              <div class="activity-item d-flex">
                <div class="activite-label">32 min</div>
                <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                <div class="activity-content">
                  <a href="#" class="fw-bold text-dark">DCR</a> submitted
                </div>
              </div>

              <div class="activity-item d-flex">
                <div class="activite-label">56 min</div>
                <i class='bi bi-circle-fill activity-badge text-danger align-self-start'></i>
                <div class="activity-content">
                   Visit updated
                </div>
              </div>

              <div class="activity-item d-flex">
                <div class="activite-label">2 hrs</div>
                <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                <div class="activity-content">
                  <a href="#" class="fw-bold text-dark">Tour Plan</a> created.
                </div>
              </div>

             
              </div>

            </div>

          </div>
        </div> --}}
        <!-- End Recent Activity -->

        <!-- Budget Report -->
        {{-- <div class="card">
          <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li class="dropdown-header text-start">
                <h6>Filter</h6>
              </li>

              <li><a class="dropdown-item" href="#">Today</a></li>
              <li><a class="dropdown-item" href="#">This Month</a></li>
              <li><a class="dropdown-item" href="#">This Year</a></li>
            </ul>
          </div>

          <div class="card-body pb-0">
            <h5 class="card-title">Budget Report <span>| This Month</span></h5>

            <div id="budgetChart" style="min-height: 400px;" class="echart"></div>

            <script>
              document.addEventListener("DOMContentLoaded", () => {
                var budgetChart = echarts.init(document.querySelector("#budgetChart")).setOption({
                  legend: {
                    data: ['Allocated Budget', 'Actual Spending']
                  },
                  radar: {
                    // shape: 'circle',
                    indicator: [{
                        name: 'Sales',
                        max: 6500
                      },
                      {
                        name: 'Administration',
                        max: 16000
                      },
                      {
                        name: 'Information Technology',
                        max: 30000
                      },
                      {
                        name: 'Customer Support',
                        max: 38000
                      },
                      {
                        name: 'Development',
                        max: 52000
                      },
                      {
                        name: 'Marketing',
                        max: 25000
                      }
                    ]
                  },
                  series: [{
                    name: 'Budget vs spending',
                    type: 'radar',
                    data: [{
                        value: [4200, 3000, 20000, 35000, 50000, 18000],
                        name: 'Allocated Budget'
                      },
                      {
                        value: [5000, 14000, 28000, 26000, 42000, 21000],
                        name: 'Actual Spending'
                      }
                    ]
                  }]
                });
              });
            </script>

          </div>
        </div> --}}
        <!-- End Budget Report -->

        <!-- Website Traffic -->
        {{-- <div class="card">
          <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li class="dropdown-header text-start">
                <h6>Filter</h6>
              </li>

              <li><a class="dropdown-item" href="#">Today</a></li>
              <li><a class="dropdown-item" href="#">This Month</a></li>
              <li><a class="dropdown-item" href="#">This Year</a></li>
            </ul>
          </div>

          <div class="card-body pb-0">
            <h5 class="card-title">Website Traffic <span>| Today</span></h5>

            <div id="trafficChart" style="min-height: 400px;" class="echart"></div>

            <script>
              document.addEventListener("DOMContentLoaded", () => {
                echarts.init(document.querySelector("#trafficChart")).setOption({
                  tooltip: {
                    trigger: 'item'
                  },
                  legend: {
                    top: '5%',
                    left: 'center'
                  },
                  series: [{
                    name: 'Access From',
                    type: 'pie',
                    radius: ['40%', '70%'],
                    avoidLabelOverlap: false,
                    label: {
                      show: false,
                      position: 'center'
                    },
                    emphasis: {
                      label: {
                        show: true,
                        fontSize: '18',
                        fontWeight: 'bold'
                      }
                    },
                    labelLine: {
                      show: false
                    },
                    data: [{
                        value: 1048,
                        name: 'Search Engine'
                      },
                      {
                        value: 735,
                        name: 'Direct'
                      },
                      {
                        value: 580,
                        name: 'Email'
                      },
                      {
                        value: 484,
                        name: 'Union Ads'
                      },
                      {
                        value: 300,
                        name: 'Video Ads'
                      }
                    ]
                  }]
                });
              });
            </script>

          </div>
        </div> --}}
        <!-- End Website Traffic -->

        <!-- News & Updates Traffic -->
        {{-- <div class="card">
          <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li class="dropdown-header text-start">
                <h6>Filter</h6>
              </li>

              <li><a class="dropdown-item" href="#">Today</a></li>
              <li><a class="dropdown-item" href="#">This Month</a></li>
              <li><a class="dropdown-item" href="#">This Year</a></li>
            </ul>
          </div>

          <div class="card-body pb-0">
            <h5 class="card-title">News &amp; Updates <span>| Today</span></h5>

            <div class="news">
              <div class="post-item clearfix">
                <img src="assets/img/news-1.jpg" alt="">
                <h4><a href="#">Nihil blanditiis at in nihil autem</a></h4>
                <p>Sit recusandae non aspernatur laboriosam. Quia enim eligendi sed ut harum...</p>
              </div>

              <div class="post-item clearfix">
                <img src="assets/img/news-2.jpg" alt="">
                <h4><a href="#">Quidem autem et impedit</a></h4>
                <p>Illo nemo neque maiores vitae officiis cum eum turos elan dries werona nande...</p>
              </div>

              <div class="post-item clearfix">
                <img src="assets/img/news-3.jpg" alt="">
                <h4><a href="#">Id quia et et ut maxime similique occaecati ut</a></h4>
                <p>Fugiat voluptas vero eaque accusantium eos. Consequuntur sed ipsam et totam...</p>
              </div>

              <div class="post-item clearfix">
                <img src="assets/img/news-4.jpg" alt="">
                <h4><a href="#">Laborum corporis quo dara net para</a></h4>
                <p>Qui enim quia optio. Eligendi aut asperiores enim repellendusvel rerum cuder...</p>
              </div>

              <div class="post-item clearfix">
                <img src="assets/img/news-5.jpg" alt="">
                <h4><a href="#">Et dolores corrupti quae illo quod dolor</a></h4>
                <p>Odit ut eveniet modi reiciendis. Atque cupiditate libero beatae dignissimos eius...</p>
              </div>

            </div><!-- End sidebar recent posts-->

          </div>
        </div> --}}
        <!-- End News & Updates -->

      </div><!-- End Right side columns -->

    </div>


    <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-8">
          <div class="card info-card top-performance-bloodbanks-card">
            <div class="card-body">
              <h5 class="card-title">Blood Banks Map</h5>
              <div class="activity" id="bloodBankMap">
                <div id="collectionMap" style="width: 100%; height: 500px;"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card info-card top-performance-bloodbanks-card">
            <div class="card-body">
              {{-- <h5 class="card-title">Blood Banks Details</h5> --}}
              <div class="activity" id="bloodBankDetails">
              
              </div>
            </div>
          </div>
        </div>
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


<!-- Load Google Maps JavaScript API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFwtHIaHQ1J8PKur9RmQy4Z5WsM6kVVPE&callback=initMap" async defer></script>


    <script>
        $(document).ready(function() {

              // Function to populate loadDashboardData
              function loadDashboardData() {
                $.ajax({
                    url: "{{ route('dashboard.getDashboardData') }}",
                    type: 'GET',
                    success: function(response) {
                        if(response.success) {
                          const data = response.data;

                          // Update Blood Bank Count
                          $(".sales-card .card-body h6").text(data.blood_bank_count);

                          // Update Warehouse Count
                          $(".revenue-card .card-body h6").text(data.warehouse_count);

                           // Update Users Count
                           $(".customers-card .card-body h6").text(data.user_count);

                          // Update Planned Plasma Count
                          $(".planned-plasma-card .card-body h6").text(data.tour_plan_total_quantity);

                          // Update Collected Plasma Count
                          $(".collected-plasma-card .card-body h6").text(data.tour_plan_total_available_quantity);

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
             function loadDashboardGraphData() {
                $.ajax({
                    url: "{{ route('dashboard.getDashboardGraphData') }}",
                    type: 'GET',
                    success: function(response) {
                        if(response.success) {
                            const data = response.data;
                            // Reverse the array if the API returns months in descending order so that the chart displays in chronological order
                            data.reverse();
                            
                            // Prepare arrays for the chart series and x-axis labels
                            const months = data.map(item => item.month);
                            const bloodBanks = data.map(item => item.blood_bank_count);
                            const warehouses = data.map(item => item.warehouse_count);
                            const customers = data.map(item => item.customer_count);

                            // Render the ApexCharts Reports Chart with dynamic data
                            new ApexCharts(document.querySelector("#reportsChart"), {
                                series: [{
                                    name: 'Blood Banks',
                                    data: bloodBanks,
                                }, {
                                    name: 'Warehouses',
                                    data: warehouses,
                                }, {
                                    name: 'Customers',
                                    data: customers,
                                }],
                                chart: {
                                    height: 350,
                                    type: 'area',
                                    toolbar: {
                                        show: false
                                    },
                                },
                                markers: {
                                    size: 4
                                },
                                colors: ['#4154f1', '#2eca6a', '#ff771d'],
                                fill: {
                                    type: "gradient",
                                    gradient: {
                                        shadeIntensity: 1,
                                        opacityFrom: 0.3,
                                        opacityTo: 0.4,
                                        stops: [0, 90, 100]
                                    }
                                },
                                dataLabels: {
                                    enabled: false
                                },
                                stroke: {
                                    curve: 'smooth',
                                    width: 2
                                },
                                xaxis: {
                                    type: 'category',
                                    categories: months,
                                },
                                tooltip: {
                                    x: {
                                        format: 'yyyy-MM'
                                    },
                                }
                            }).render();
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


             // Function to load dashboard blood bank mapview
             function loadDashboardBloodBankMap() {
                $.ajax({
                    url: "{{ route('dashboard.getDashboardBloodBanksMapData') }}",
                    type: 'GET',
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

           // Load DashboardData on page load
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
                      title: bank.blood_bank_name
                  });

                  // Extend the bounds to include this marker's location
                  bounds.extend(marker.getPosition());

                  // Setup click listener for marker
                  marker.addListener('click', function() {
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
                          <h5 class="card-title">Collection Details</h5>
                          `;
                      // // Loop through collection_details to list each collection
                      // if (bank.collection_details && bank.collection_details.length > 0) {
                      //     bank.collection_details.forEach(function(collection) {
                      //         detailsContent += `
                      //             <li>
                      //                 <strong>Date: ${collection.start}</strong><br>
                      //                 Executive: ${collection.extendedProps.collecting_agent_name || ''}<br>
                      //                 Planned: ${collection.extendedProps.quantity || 0}, Collected: ${collection.extendedProps.available_quantity || 0}<br>
                      //                 Plama Price: ${collection.extendedProps.price || 0}<br>
                      //                 Part-A: ${collection.extendedProps.part_a_invoice_price || 0},  Part-B: ${collection.extendedProps.part_b_invoice_price || 0},  Part-C: ${collection.extendedProps.part_c_invoice_price || 0}<br>
                      //                 Total Invoice Price: ${collection.extendedProps.collection_total_plasma_price || 0}<br>
                      //                 No. of Boxes: ${collection.extendedProps.num_boxes || 0}, Units: ${collection.extendedProps.num_units || 0}, Litres: ${collection.extendedProps.num_litres || 0}<br>
                      //             </li>`;
                      //     });
                      // } else {
                      //     detailsContent += `<li>No collection details available.</li>`;
                      // }
                      // detailsContent += `</ul>`;

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

          // Global initMap function (called by the Google Maps API callback)
          function initMap() {
              // Try to initialize both maps. Only the one(s) present in the DOM will be initialized.
              initCollectionMap();
          }
        });
    </script>
    
@endpush

