@extends('include.dashboardLayout')

@section('title', 'User Live Location')

@section('content')

<div class="pagetitle">
  <h1>User Live Location</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('reports.user_live_location') }}">User Live Location</a></li>
      <li class="breadcrumb-item active">View</li>
    </ol>
  </nav>
</div>

<section class="section">

  <!-- FILTER SECTION -->
  <div class="card mb-3">
    <div class="card-body">
      <div class="row g-3 align-items-end">

        <div class="col-md-3">
          <label class="form-label">Select Date</label>
          <input type="date" id="locationDate" class="form-control">
        </div>

        <div class="col-md-2">
          <button id="submitFilter" class="btn btn-success w-100 mt-3">
            <i class="bi bi-check-circle"></i> Submit
          </button>
        </div>

      </div>
    </div>
  </div>

  <!-- MAIN AREA -->
  <div class="row">
    
    <!-- LEFT Executive List -->
    <div class="col-md-3">
      <div class="card" style="height: 75vh; overflow-y: auto;">
        <div class="card-body">
          <h5 class="card-title">Executives</h5>
          <input type="text" id="searchExec" class="form-control mb-3" placeholder="Search Executive...">
          <ul id="executiveList" class="list-group"></ul>
        </div>
      </div>
    </div>

    <!-- RIGHT Map -->
    <div class="col-md-9">
      <div class="card">
        <div class="card-body p-0">
          <div id="liveMap" style="height: 75vh; width: 100%; position: relative;"></div>
        </div>
      </div>
    </div>

  </div>

</section>

@endsection

@push('scripts')

<!-- GOOGLE MAPS -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('auth_api.google_maps_api_key') }}&libraries=geometry"></script>

<script>
let map;
let markers = [];
let selectedEmployeeId = null;

// INIT MAP
function initMap() {
  map = new google.maps.Map(document.getElementById("liveMap"), {
    center: { lat: 20.5937, lng: 78.9629 },
    zoom: 5,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  });
}


// LOAD EXECUTIVES LIST
function loadExecutives() {
  $.get("{{ route('tourplanner.getCollectingAgents') }}", function(res) {
    if (!res.success) return Swal.fire("Error", res.message, "error");

    const list = $("#executiveList").empty();
    res.data.forEach(exec => {
      list.append(`
        <li class="list-group-item exec-item" data-id="${exec.id}">
          <strong>${exec.name}</strong><br>
          <small>${exec.role.role_name}</small>
        </li>
      `);
    });
  });
}


// EXECUTIVE CLICK
$(document).on("click", ".exec-item", function() {
  $(".exec-item").removeClass("active");
  $(this).addClass("active");

  selectedEmployeeId = $(this).data("id");
  fetchLocationData();
});


// FETCH LIVE LOCATION
function fetchLocationData() {
  let date = $("#locationDate").val();
  if (!selectedEmployeeId) return;

  $.post("{{ route('reports.getUserLiveLocation') }}", {
    _token: '{{ csrf_token() }}',
    empId: selectedEmployeeId,
    date: date
  }, function(res) {
    if (!res.success) return Swal.fire("Error", res.message, "error");
    placeMarkers(res.data);
  });
}


// ---------- NORMALIZER ----------
function toPoint(p) {
    if (typeof p.lat === "function") {
        return { lat: p.lat(), lng: p.lng() };
    }
    return { lat: Number(p.lat), lng: Number(p.lng) };
}


// ---------- BEZIER SMOOTH CURVE ----------
function createSmoothPath(coords) {
    coords = coords.map(toPoint);
    if (coords.length <= 2) return coords;

    let smooth = [];
    for (let i = 0; i < coords.length - 1; i++) {
        let p0 = coords[i];
        let p1 = coords[i + 1];

        let Q = { lat: p0.lat * 0.75 + p1.lat * 0.25, lng: p0.lng * 0.75 + p1.lng * 0.25 };
        let R = { lat: p0.lat * 0.25 + p1.lat * 0.75, lng: p0.lng * 0.25 + p1.lng * 0.75 };

        smooth.push(Q);
        smooth.push(R);
    }
    return smooth;
}


// ---------- ANIMATED POLYLINE ----------
function drawAnimatedPolyline(path) {
    path = path.map(toPoint);

    let poly = new google.maps.Polyline({
        path: [],
        strokeColor: "#007bff",
        strokeOpacity: 1.0,
        strokeWeight: 5,
        map: map
    });

    let i = 0;
    let interval = setInterval(() => {
        if (i < path.length) {
            poly.getPath().push(new google.maps.LatLng(path[i].lat, path[i].lng));
            i++;
        } else {
            clearInterval(interval);
        }
    }, 40);
}



// ---------- HAVERSINE DISTANCE ----------
function haversineDistance(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;

    const a =
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1 * Math.PI/180) *
        Math.cos(lat2 * Math.PI/180) *
        Math.sin(dLon/2) * Math.sin(dLon/2);

    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
}



// ---------- DRAW EVERYTHING ----------
function placeMarkers(locations) {

  markers.forEach(m => m.setMap(null));
  markers = [];
  $("#distanceBox").remove();

  if (!locations.length) {
    Swal.fire("No data", "No location data found", "info");
    return;
  }

  locations.sort((a,b) => new Date(a.time) - new Date(b.time));

  // Build path
  let fullPath = locations.map(l => ({
    lat: parseFloat(l.latitude),
    lng: parseFloat(l.longitude)
  }));


  // ---------- TOTAL KM ----------
  let totalKm = 0;
  for (let i = 0; i < locations.length - 1; i++) {
      totalKm += haversineDistance(
          parseFloat(locations[i].latitude),
          parseFloat(locations[i].longitude),
          parseFloat(locations[i+1].latitude),
          parseFloat(locations[i+1].longitude)
      );
  }


  // Floating box
  const distanceDiv = document.createElement("div");
  distanceDiv.id = "distanceBox";
  distanceDiv.style.position = "absolute";
  distanceDiv.style.bottom = "20px";
  distanceDiv.style.left = "20px";
  distanceDiv.style.background = "white";
  distanceDiv.style.padding = "10px 15px";
  distanceDiv.style.borderRadius = "8px";
  distanceDiv.style.boxShadow = "0 2px 6px rgba(0,0,0,0.3)";
  distanceDiv.style.fontWeight = "bold";
  distanceDiv.style.zIndex = "9999";
  distanceDiv.innerHTML = `Total Distance: ${totalKm.toFixed(2)} km`;
  document.getElementById("liveMap").appendChild(distanceDiv);


  // ---------- SMOOTH CURVE ----------
  let smooth1 = createSmoothPath(fullPath);
  let smooth2 = createSmoothPath(smooth1);

  drawAnimatedPolyline(smooth2);


  // ---------- SPECIAL MARKERS ----------
  locations.forEach(loc => {

    let icon;
    let size = new google.maps.Size(28, 28);

    if (loc.check_in == 1) {
        icon = "https://maps.google.com/mapfiles/ms/icons/green-dot.png";
        size = new google.maps.Size(50, 50);
    }
    else if (loc.check_out == 1) {
        icon = "https://maps.google.com/mapfiles/ms/icons/red-dot.png";
        size = new google.maps.Size(50, 50);
    }
    else if (loc.is_reporting == 1) {
        icon = "https://maps.google.com/mapfiles/ms/icons/yellow-dot.png";
        size = new google.maps.Size(50, 50);
    }
    else {
        icon = "https://maps.google.com/mapfiles/ms/icons/blue-dot.png";
        size = new google.maps.Size(28, 28);
    }

    markers.push(new google.maps.Marker({
      position: { lat: parseFloat(loc.latitude), lng: parseFloat(loc.longitude) },
      map: map,
      icon: { url: icon, scaledSize: size },
      title: loc.time
    }));
  });


  // ---------- SMART ZOOM ----------
  const bounds = new google.maps.LatLngBounds();
  smooth2.forEach(p => bounds.extend(new google.maps.LatLng(p.lat, p.lng)));
  map.fitBounds(bounds);
}



// SEARCH FILTER
$("#searchExec").on("keyup", function() {
  let val = $(this).val().toLowerCase();
  $(".exec-item").filter(function() {
    $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
  });
});


// INIT
$(document).ready(function() {
  initMap();
  loadExecutives();
  $("#locationDate").val(new Date().toISOString().split('T')[0]);
  $("#submitFilter").click(() => fetchLocationData());
});
</script>

@endpush
