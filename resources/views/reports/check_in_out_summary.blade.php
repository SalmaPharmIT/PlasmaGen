@extends('include.dashboardLayout')

@section('title', 'Check In - Check Out Summary')

@section('content')

<div class="pagetitle">
  <h1>Check In - Check Out Summary</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="{{ route('reports.check_in_out_summary') }}">Reports</a>
      </li>
      <li class="breadcrumb-item active">View</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="card">
    <div class="card-body">

      <h5 class="card-title mb-4">View Check In - Check Out</h5>

      <!-- FILTERS -->
      <div class="row mb-4 align-items-end">

        <div class="col-md-4">
          <label class="form-label">Executives</label>
          <select id="executiveDropdown" class="form-select select2" multiple></select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Date Range</label>
          <input type="text"
                 id="dateRangePicker"
                 class="form-control"
                 placeholder="YYYY-MM-DD - YYYY-MM-DD">
        </div>

        <div class="col-md-2 mt-2">
          <button id="filterButton" class="btn btn-success w-100">
            <i class="bi bi-filter me-1"></i> Submit
          </button>
        </div>

        <div class="col-md-2 mt-2">
          <button id="exportButton" class="btn btn-info w-100">
            <i class="bi bi-download me-1"></i> Export
          </button>
        </div>

      </div>

      <!-- TABLE -->
      <div class="table-responsive">
        <table id="checkInOutTable" class="table table-striped table-bordered w-100">
          <thead></thead>
          <tbody></tbody>
        </table>
      </div>

    </div>
  </div>
</section>

@endsection


@push('styles')
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
<link rel="stylesheet"
      href="https://cdn.datatables.net/buttons/2.3.5/css/buttons.dataTables.min.css"/>
<style>
  .badge {
    font-size: 12px;
  }
</style>
@endpush


@push('scripts')
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.3.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.5/js/buttons.html5.min.js"></script>

<script>
$(document).ready(function () {

    let table;

    // -----------------------------
    // DATE RANGE PICKER
    // -----------------------------
    $('#dateRangePicker').daterangepicker({
        locale: { format: 'YYYY-MM-DD' }
    });

    // -----------------------------
    // LOAD EXECUTIVES
    // -----------------------------
    function loadExecutives() {
        $.get("{{ route('tourplanner.getCollectingAgents') }}", res => {
            const dd = $('#executiveDropdown').empty();
            dd.append(new Option('Select All', 'all'));

            if (res?.data) {
                res.data.forEach(a => {
                    dd.append(
                        new Option(`${a.name} (${a.role.role_name})`, a.id)
                    );
                });
            }
        });
    }
    loadExecutives();

    $('#executiveDropdown').on('select2:select', function (e) {
        if (e.params.data.id === 'all') {
            const all = $(this)
                .find('option')
                .not('[value="all"]')
                .map((_, opt) => opt.value)
                .get();
            $(this).val(all).trigger('change');
        }
    });

    // -----------------------------
    // SUBMIT
    // -----------------------------
    $('#filterButton').click(function () {

        const dateRange = $('#dateRangePicker').val();
        const agents    = $('#executiveDropdown').val();

        if (!dateRange || !agents?.length) {
            return Swal.fire("Warning", "Please select date range and executives", "warning");
        }

        $.post(
            "{{ route('reports.getUserCheckInOutSummary') }}",
            {
                _token: '{{ csrf_token() }}',
                dateRange: dateRange,
                agent_id: agents
            }
        ).done(res => {

            if (!res.success) {
                return Swal.fire("Error", res.message, "error");
            }

            buildTable(res.data);

        }).fail(() => {
            Swal.fire("Error", "Server error", "error");
        });
    });

    // -----------------------------
    // BUILD TABLE
    // -----------------------------
    function buildTable(data) {

        if (!data || !data.length) {
            return Swal.fire("Empty", "No data found", "info");
        }

        if ($.fn.DataTable.isDataTable('#checkInOutTable')) {
            $('#checkInOutTable').DataTable().destroy();
        }

        $('#checkInOutTable thead').html(`
            <tr>
                <th>SI.No</th>
                <th>Executive</th>
                <th>Date</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Working Duration</th>
                <th>Total KM Travelled</th>
                <th>Total Pings</th>
                <th>Reporting Pings</th>
                <th>Non-Reporting Pings</th>
                <th>Status</th>
            </tr>
        `);

        const rows = data.map((r, i) => {

            const status = (r.check_in_missing || r.check_out_missing)
                ? `<span class="badge bg-danger">Missing</span>`
                : `<span class="badge bg-success">OK</span>`;

            return {
                si: i + 1,
                employee: r.employee_name,
                date: r.date,
                check_in: r.check_in_time ?? '-',
                check_out: r.check_out_time ?? '-',
                duration: r.working_duration ?? '-',
                km: r.total_km_travelled !== null ? `${parseFloat(r.total_km_travelled).toFixed(2)} km` : '-',
                pings: r.total_pings,
                reporting: r.reporting_points,
                non_reporting: r.non_reporting_points,
                status: status
            };
        });

        table = $('#checkInOutTable').DataTable({
            data: rows,
            columns: [
                { data: 'si' },
                { data: 'employee' },
                { data: 'date' },
                { data: 'check_in' },
                { data: 'check_out' },
                { data: 'duration' },
                { data: 'km' },   
                { data: 'pings' },
                { data: 'reporting' },
                { data: 'non_reporting' },
                { data: 'status' }
            ],
            paging: true,
            ordering: true,
            searching: true,
            scrollX: true,
          //  dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                title: 'Check_In_Out_Summary',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                }
            }]
        });
    }

    // -----------------------------
    // EXPORT BUTTON
    // -----------------------------
    $('#exportButton').click(() => {
        if (table) {
            table.button(0).trigger();
        }
    });

});
</script>
@endpush
