@extends('include.dashboardLayout')

@section('title', 'Reports')

@section('content')

<div class="pagetitle">
    <h1>DCR Summary</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('reports.dcr_summary') }}">Reports</a></li>
        <li class="breadcrumb-item active">View</li>
      </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-4">View DCR Details</h5>

          <!-- Filters -->
          <div class="row mb-4 align-items-end">
            <div class="col-md-4">
              <label for="collectingAgentDropdown" class="form-label">Executives</label>
              <select id="collectingAgentDropdown" class="form-select select2" name="collecting_agent_id[]" multiple>
                <option value="">Choose Executives</option>
              </select>
            </div>
            <div class="col-md-4 mt-2">
              <label for="dateRangePicker" class="form-label">Select Date Range</label>
              <input type="text" id="dateRangePicker" class="form-control" placeholder="YYYY-MM-DD - YYYY-MM-DD" />
            </div>
            <div class="col-md-2 mt-2 d-flex align-items-end">
              <button id="filterButton" class="btn btn-success w-100"><i class="bi bi-filter me-1"></i> Submit</button>
            </div>
            <div class="col-md-2 mt-2 d-flex align-items-end">
              <button id="exportButton" class="btn btn-info w-100"><i class="bi bi-download me-1"></i> Export</button>
            </div>
          </div>

          <!-- Table -->
          <div class="table-responsive">
            <table id="userWiseDCRSummaryTable" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>SI.No.</th>
                  <th>Executive</th>
                  <th>TP</th>
                  <th>Date</th>
                  <th>TP Type</th>
                  <th>Tour Plan</th>
                  <th>Qty</th>
                  <th>Remarks</th>
                  <th>TP Status</th>
                  <th>Mgr Status</th>
                  <th>CA Status</th>
                  <th>Avail Qty.</th>
                  <th>Price</th>
                  <th>Transport Info</th>
                  <th>Sourcing-1</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@push('styles')
<style>
  .table-responsive { overflow-x: auto; }
  .table td, .table th { word-wrap: break-word; white-space: normal; }
</style>
@endpush

@push('scripts')
<!-- Dependencies -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.datatables.net/buttons/2.3.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.5/js/buttons.html5.min.js"></script>

<script>
$(function() {
  // Date range picker
  $('#dateRangePicker').daterangepicker({
    locale: { format: 'YYYY-MM-DD' },
    autoUpdateInput: false,
    opens: 'right'
  })
  .on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
  })
  .on('cancel.daterangepicker', function() {
    $(this).val('');
  });

  // Load executives
  function loadCollectingAgents() {
    $.get("{{ route('tourplanner.getCollectingAgents') }}", function(res) {
      if (res.success) {
        const dd = $('#collectingAgentDropdown').empty().append('<option value="">Choose Executives</option>');
        res.data.forEach(a => dd.append(`<option value="${a.id}">${a.name} (${a.role.role_name})</option>`));
      } else {
        Swal.fire('Error', res.message, 'error');
      }
    });
  }
  loadCollectingAgents();

  // Clear any existing table when executives change
  $('#collectingAgentDropdown').on('change', function() {
    if ($.fn.DataTable.isDataTable('#userWiseDCRSummaryTable')) {
      $('#userWiseDCRSummaryTable').DataTable().destroy();
      $('#userWiseDCRSummaryTable tbody').empty();
      $('#userWiseDCRSummaryTable thead tr').html(
        '<th>SI.No.</th>' +
        '<th>Executive</th>' +
        '<th>TP</th>' +
        '<th>Date</th>' +
        '<th>TP Type</th>' +
        '<th>Tour Plan</th>' +
        '<th>Qty</th>' +
        '<th>Remarks</th>' +
        '<th>TP Status</th>' +
        '<th>Mgr Status</th>' +
        '<th>CA Status</th>' +
        '<th>Avail Qty.</th>' +
        '<th>Price</th>' +
        '<th>Transport Info</th>' +
        '<th>Sourcing-1</th>'
      );
    }
  });

  let table;

  // Handle filter submit
  $('#filterButton').click(function() {
    const dateRange = $('#dateRangePicker').val();
    const agents = $('#collectingAgentDropdown').val();
    if (!dateRange || !agents || !agents.length) {
      return Swal.fire('Warning', 'Date range & executive required', 'warning');
    }

    $.post("{{ route('reports.getUserDCRSummary') }}", {
      _token: '{{ csrf_token() }}',
      dateRange: dateRange,
      collectingAgent: agents
    }, function(json) {
      if (!json.success) {
        return Swal.fire('Error', json.message, 'error');
      }

      const data = json.data;
      const maxVisits = data.reduce((m, r) => Math.max(m, (r.extendedProps.tour_plan_visits || []).length), 0);

      // Build columns
      const cols = [
        { title: 'SI.No.', data: null, className: 'text-center', render: (_d, _t, _r, m) => m.row + 1 },
        { title: 'Executive', data: null, className: 'text-center', render: r => r.extendedProps.collecting_agent_name || '' },
        { title: 'TP', data: 'title', className: 'text-center' },
        { title: 'Date', data: 'visit_date', className: 'text-center' },
        { title: 'TP Type', data: null, className: 'text-center', render: r => ({1: 'Collection', 2: 'Sourcing', 3: 'Both'}[r.extendedProps.tour_plan_type] || '') },
        { title: 'Tour Plan', data: 'title', className: 'text-center' },
        { title: 'Qty', data: null, className: 'text-center', render: r => r.extendedProps.quantity || '' },
        { title: 'Remarks', data: null, className: 'text-center', render: r => r.extendedProps.remarks || '' },
        { title: 'TP Status', data: null, className: 'text-center', render: r => r.extendedProps.status || '' },
        { title: 'Mgr Status', data: null, className: 'text-center', render: r => r.extendedProps.manager_status || '' },
        { title: 'CA Status', data: null, className: 'text-center', render: r => r.extendedProps.ca_status || '' },
        { title: 'Avail Qty.', data: null, className: 'text-center', render: r => r.extendedProps.available_quantity || '' },
        { title: 'Price', data: null, className: 'text-center', render: r => r.extendedProps.price || '' },
        { title: 'Transport Info', data: null, className: 'text-left', render: r => {
          const t = r.extendedProps.transport_details; if (!t) return 'N/A';
          const parts = [];
          t.vehicle_number && parts.push('Vehicle: ' + t.vehicle_number);
          t.driver_name && parts.push('Driver: ' + t.driver_name);
          t.contact_number && parts.push('Contact1: ' + t.contact_number);
          t.alternative_contact_number && parts.push('Contact2: ' + t.alternative_contact_number);
          t.email && parts.push('Email: ' + t.email);
          t.remarks && parts.push('Remarks: ' + t.remarks);
          return parts.join('<br>');
        }}
      ];

      // Dynamic Sourcing-n columns
      for (let i = 0; i < maxVisits; i++) {
        cols.push({
          title: `Sourcing-${i+1}`,
          data: null,
          className: 'text-left',
          render: function(r) {
            const v = (r.extendedProps.tour_plan_visits || [])[i];
            if (!v) return 'N/A';
            const skip = ['id','created_by','modified_by','created_at','updated_at'];
            const labelMap = {
              tour_plan_type: 'Type', blood_bank_name: 'Blood Bank', sourcing_contact_person: 'Contact Person',
              sourcing_mobile_number: 'Mobile No.', sourcing_email: 'Email', sourcing_address: 'Address',
              sourcing_ffp_company: 'FFP Company', sourcing_plasma_price: 'Plasma Price',
              sourcing_potential_per_month: 'Potential/Month', sourcing_payment_terms: 'Payment Terms',
              sourcing_remarks: 'Remarks', sourcing_part_a_price: 'Part A Price', sourcing_part_b_price: 'Part B Price',
              sourcing_part_c_price: 'Part C Price', include_gst: 'Include GST', gst_rate: 'GST Rate',
              sourcing_total_plasma_price: 'Total Plasma Price'
            };
            return Object.entries(v)
              .filter(([k]) => !skip.includes(k))
              .map(([k,val]) => {
                if (k === 'include_gst') val = val == 1 ? 'Yes' : 'No';
                return `${labelMap[k] || k}: ${val == null ? '' : val}`;
              })
              .join('<br>');
          }
        });
      }

      // Rebuild header
      const $tr = $('#userWiseDCRSummaryTable thead tr').empty();
      cols.forEach(c => $tr.append(`<th>${c.title}</th>`));

      // Destroy previous and init new DataTable
      if ($.fn.DataTable.isDataTable('#userWiseDCRSummaryTable')) {
        $('#userWiseDCRSummaryTable').DataTable().destroy();
      }
      table = $('#userWiseDCRSummaryTable').DataTable({
        data: data,
        columns: cols,
        responsive: true,
        paging: true,
        buttons: [{ extend: 'excelHtml5', title: 'User DCR Summary', exportOptions: { columns: ':visible' } }]
      });
    });
  });

  // Export trigger
  $('#exportButton').click(() => table.button(0).trigger());
});
</script>
@endpush