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
            <div class="col-md-8">
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
              <thead></thead>
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
  // 1. Date range picker
  $('#dateRangePicker').daterangepicker({
    locale: { format:'YYYY-MM-DD' },
    autoUpdateInput: false,
    opens: 'right'
  })
  .on('apply.daterangepicker', (ev, picker) => {
    $('#dateRangePicker').val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
  })
  .on('cancel.daterangepicker', () => {
    $('#dateRangePicker').val('');
  });

  // 2. Load executives dropdown
  function loadCollectingAgents(){
    $.get("{{ route('tourplanner.getCollectingAgents') }}", res => {
      // if(res.success){
      //   const dd = $('#collectingAgentDropdown').empty().append('<option value="">Choose Executives</option>');
      //   res.data.forEach(a => dd.append(`<option value="${a.id}">${a.name} (${a.role.role_name})</option>`));
      // } else {
      //   Swal.fire('Error', res.message, 'error');
      // }
      if(!res.success) return Swal.fire('Error', res.message,'error');

      const dd = $('#collectingAgentDropdown').empty();
      // 2.1 add the "Select All" first
      dd.append(new Option('Select All','all'));
      // 2.2 then all the real agents
      res.data.forEach(a => {
        dd.append(new Option(`${a.name} (${a.role.role_name})`, a.id));
      });
      // notify select2 of the change
      dd.trigger('change');
    });
  }
  loadCollectingAgents();

  // 3. When the user picks "Select All", grab every real option and select it
  $('#collectingAgentDropdown').on('select2:select', function(e) {
    if (e.params.data.id === 'all') {
      const allIds = $(this).find('option')
        .map((_,opt) => opt.value)
        .get()
        .filter(v => v && v !== 'all');
      // overwrite the selection with all real IDs
      $(this).val(allIds).trigger('change');
    }
  });

  // 3. Shared column‐header definitions
  const staticHeaders   = ['SI.No.','Executive','TP','Date','TP Type','Tour Plan','Qty','Remarks','TP Status','Mgr Status','CA Status','Avail Qty.','Price'];
  const transportTitles = ['Vehicle','Driver','Contact1','Contact2','Email','Remarks'];
  const transportFields = ['vehicle_number','driver_name','contact_number','alternative_contact_number','email','remarks'];
  const visitTitles     = ['Blood Bank','Contact Person','Mobile No.','Email','Address','FFP Company','Plasma Price','Potential/Month','Payment Terms','Remarks','Part A Price','Part B Price','Part C Price','Include GST','GST Rate','Total Plasma Price'];
  const visitFields     = ['blood_bank_name','sourcing_contact_person','sourcing_mobile_number','sourcing_email','sourcing_address','sourcing_ffp_company','sourcing_plasma_price','sourcing_potential_per_month','sourcing_payment_terms','sourcing_remarks','sourcing_part_a_price','sourcing_part_b_price','sourcing_part_c_price','include_gst','gst_rate','sourcing_total_plasma_price'];

  // 4. On filter click: fetch & build table
  let table;
  $('#filterButton').click(() => {
    const dateRange = $('#dateRangePicker').val();
    const agents    = $('#collectingAgentDropdown').val();
    if(!dateRange || !agents?.length){
      return Swal.fire('Warning','Date range & executive required','warning');
    }

    $.post("{{ route('reports.getUserDCRSummary') }}", {
      _token: '{{ csrf_token() }}',
      dateRange,
      collectingAgent: agents
    }, ({ success, data, message }) => {
      if(!success) return Swal.fire('Error', message, 'error');

      // compute visits count
      const maxVisits = Math.max(0, ...data.map(r => (r.extendedProps.tour_plan_visits||[]).length));

      // 5. Build the on‐screen two‐row header
      const $thead = $('#userWiseDCRSummaryTable thead').empty();
      const $r1 = $('<tr>');
      staticHeaders.forEach(h => {
        $r1.append($('<th>').text(h).attr('rowspan', 2));
      });
      // Transport group
      $r1.append($('<th>')
        .text('Transport Info')
        .attr('colspan', transportTitles.length)
      );
      // Sourcing groups
      for(let i=1; i<=maxVisits; i++){
        $r1.append($('<th>')
          .text(`Sourcing-${i}`)
          .attr('colspan', visitTitles.length)
        );
      }
      $thead.append($r1);

      const $r2 = $('<tr>');
      transportTitles.forEach(t => $r2.append($('<th>').text(t)));
      for(let i=0; i<maxVisits; i++){
        visitTitles.forEach(t => $r2.append($('<th>').text(t)));
      }
      $thead.append($r2);

      // 6. Define the columns array
      const cols = [];
      // static cols
      cols.push(
        { data:null, className:'text-center', render:(_d,_t,_r,meta)=>meta.row+1 },
        { data:null, className:'text-center', render:r=>r.extendedProps.collecting_agent_name||'' },
        { data:'title', className:'text-center' },
        { data:'visit_date', className:'text-center' },
        { data:null, className:'text-center', render:r=>({1:'Collection',2:'Sourcing',3:'Both'}[r.extendedProps.tour_plan_type]||'') },
        { data:'title', className:'text-center' },
        { data:null, className:'text-center', render:r=>r.extendedProps.quantity||'' },
        { data:null, className:'text-center', render:r=>r.extendedProps.remarks||'' },
        { data:null, className:'text-center', render:r=>r.extendedProps.status||'' },
        { data:null, className:'text-center', render:r=>r.extendedProps.manager_status||'' },
        { data:null, className:'text-center', render:r=>r.extendedProps.ca_status||'' },
        { data:null, className:'text-center', render:r=>r.extendedProps.available_quantity||'' },
        { data:null, className:'text-center', render:r=>r.extendedProps.price||'' }
      );
      // transport subs
      transportFields.forEach(f => {
        cols.push({
          data: null,
          className: 'text-left',
          render: r => (r.extendedProps.transport_details||{})[f]||''
        });
      });
      // sourcing subs
      for(let i=0; i<maxVisits; i++){
        visitFields.forEach(f => {
          cols.push({
            data: null,
            className: 'text-left',
            render: r => {
              const v = (r.extendedProps.tour_plan_visits||[])[i]||{};
              let val = v[f];
              if(f==='include_gst') val=val==1?'Yes':'No';
              return val!=null?val:'';
            }
          });
        });
      }

      // 7. Init DataTable & Excel export
      if($.fn.DataTable.isDataTable('#userWiseDCRSummaryTable')) table.destroy();
      table = $('#userWiseDCRSummaryTable').DataTable({
        data, columns: cols, responsive:true, paging:true,
        buttons: [{
          extend: 'excelHtml5',
          title: 'User DCR Summary',
          exportOptions: {
            columns: ':visible',
            format: {
              header: function(_columnName, columnIdx) {
                // 1) static columns (no change)
                if (columnIdx < staticHeaders.length) {
                  return staticHeaders[columnIdx];
                }

                // 2) transport sub-cols
                const tStart = staticHeaders.length;
                const tEnd   = tStart + transportTitles.length;
                if (columnIdx >= tStart && columnIdx < tEnd) {
                  const subIdx = columnIdx - tStart;
                  // only the sub‐header + (TD)
                  return `${transportTitles[subIdx]}(TD)`;
                }

                // 3) sourcing sub-cols
                const sStart   = tEnd;
                const localIdx = columnIdx - sStart;
                const group    = Math.floor(localIdx / visitTitles.length) + 1;  // 1, 2, …
                const subIdx   = localIdx % visitTitles.length;
                // only the sub‐header + (S#)
                return `${visitTitles[subIdx]}(S${group})`;
              }
            }
          }
        }]

      });
    });
  });

  // 8. Export button
  $('#exportButton').click(() => table.button(0).trigger());
});
</script>
@endpush
