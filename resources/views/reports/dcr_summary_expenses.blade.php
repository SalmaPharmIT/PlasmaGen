@extends('include.dashboardLayout')

@section('title', 'DCR + Expenses Summary')

@section('content')

<div class="pagetitle">
  <h1>DCR + Expenses Summary</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="{{ route('reports.dcr_summary_expenses') }}">Reports</a>
      </li>
      <li class="breadcrumb-item active">View</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="card">
    <div class="card-body">

      <h5 class="card-title mb-4">View DCR + Expenses</h5>

      <!-- Filters -->
      <div class="row mb-4 align-items-end">

        <div class="col-md-4">
          <label class="form-label">Executives</label>
          <select id="executiveDropdown" class="form-select select2" multiple></select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Date Range</label>
          <input type="text" id="dateRangePicker" class="form-control"
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

      <!-- Combined Table -->
      <div class="table-responsive">
        <table id="dcrExpensesTable" class="table table-striped table-bordered w-100">
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
<link rel="stylesheet"
      href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css"/>

<style>
  .table td,
  .table th {
    white-space: normal !important;
  }

  /* make fixed columns look ok with horizontal scroll */
  div.dataTables_wrapper div.dataTables_scrollHead table,
  div.dataTables_wrapper div.dataTables_scrollBody table {
    margin: 0 !important;
  }
</style>
@endpush


@push('scripts')
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.3.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.5/js/buttons.html5.min.js"></script>

<script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>

<script>
$(document).ready(function () {
    let table;
    const baseImageUrl = "{{ config('auth_api.base_image_url') }}";

    // -----------------------------
    // 1) DATE RANGE PICKER
    // -----------------------------
    $('#dateRangePicker').daterangepicker({
        locale: { format: 'YYYY-MM-DD' }
    });

    // -----------------------------
    // 2) EXECUTIVES LOADING
    // -----------------------------
    function loadExecutives() {
        $.get("{{ route('tourplanner.getCollectingAgents') }}", res => {
            const dd = $('#executiveDropdown').empty();
            dd.append(new Option('Select All', 'all'));

            if (res && res.data) {
                res.data.forEach(a => {
                    dd.append(new Option(`${a.name} (${a.role.role_name})`, a.id));
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
    // 3) SUBMIT → CALL API
    // -----------------------------
    $('#filterButton').click(function () {
        const dateRange = $('#dateRangePicker').val();
        const agents    = $('#executiveDropdown').val();

        if (!dateRange || !agents?.length) {
            return Swal.fire("Warning", "Please select date range and executives", "warning");
        }

        $.post(
            "{{ route('reports.getUserDCRWithExpensesSummary') }}",
            {
                _token: '{{ csrf_token() }}',
                dateRange: dateRange,
                agent_id: agents
            }
        ).done(res => {
            if (!res.success) {
                return Swal.fire("Error", res.message, "error");
            }
            buildCombinedTable(res.data);
        }).fail(() => {
            Swal.fire("Error", "Server error", "error");
        });
    });

    // -------------------------------------------
    // 4) BUILD DYNAMIC TABLE
    // -------------------------------------------
    function buildCombinedTable(combinedData) {

        if (!combinedData || !combinedData.length) {
            return Swal.fire("Empty", "No data found", "info");
        }

        let maxVisits   = 0;
        let maxExpenses = 0;

        combinedData.forEach(row => {
            maxVisits   = Math.max(maxVisits,   row.visits.length);
            maxExpenses = Math.max(maxExpenses, row.expenses.length);
        });

        // ---------------------------------
        // 4.1 Build table header
        // ---------------------------------
        const thead   = $('#dcrExpensesTable thead').empty();
        const header1 = $('<tr>');
        const header2 = $('<tr>');

        // STATIC COLUMNS:
        //  1) SI.No
        //  2) Executive
        //  3) Date
        //  4) TP Status
        //  5) MGR Status
        //  6) CA Status
        //  7) Travel Mode
        //  8) KM Travelled
        //  9) Travel Remarks
        // 10) App. KM Travel
        // 11) App. Travel Price
        // 12) App. Travel Remarks
        [
          "SI.No",
          "Executive",
          "Date",
          "TP Status",
          "MGR Status",
          "CA Status",
          "Travel Mode",
          "KM Travelled",
          "Travel Remarks",
          "App. KM Travel",
          "App. Travel Price",
          "App. Travel Remarks"
        ].forEach(h => header1.append(`<th rowspan="2">${h}</th>`));

        // VISIT SUB-HEADERS (same for each Visit-N group)
        const visitSubHeaders = [
            // Basic
            "Type",
            "Title",
            "Qty",
            "Status",
            "Remarks",

            // Collection side
            "Qty. Collected",
            "Qty. Remaining",
            "Price",
            "Part-A Price",
            "Part-B Price",
            "Part-C Price",
            "GST?",
            "GST Rate",
            "Total Collection Price",
            "No. Boxes",
            "No. Units",
            "No. Litres",

            // Sourcing side (from tour_plan_visits)
            "S-Contact Person",
            "S-Mobile",
            "S-Email",
            "S-Address",
            "S-FFP Company",
            "S-Plasma Price",
            "S-Potential/Month",
            "S-Payment Terms",
            "S-Remarks",
            "S-Part-A Price",
            "S-Part-B Price",
            "S-Part-C Price",
            "S-GST?",
            "S-GST Rate",
            "S-Total Plasma Price"
        ];

        const visitColSpan = visitSubHeaders.length; // 32

        // Visit column groups
        for (let i = 1; i <= maxVisits; i++) {
            header1.append(`<th colspan="${visitColSpan}">Visit-${i}</th>`);
            visitSubHeaders.forEach(h => header2.append(`<th>${h}</th>`));
        }

        // Expense column groups (still 5 per group)
        for (let i = 1; i <= maxExpenses; i++) {
            header1.append(`<th colspan="5">Expense-${i}</th>`);
            header2.append(
                `<th>Description</th><th>Food</th><th>Conv</th><th>Total</th><th>Attachments</th>`
            );
        }

        // Action column
        header1.append(`<th rowspan="2">Action</th>`);

        thead.append(header1).append(header2);

        // ---------------------------------
        // 4.2 Build row objects
        // ---------------------------------
        const rows = combinedData.map((r, index) => {
            const firstVisit = r.visits && r.visits.length ? r.visits[0] : null;
            const ext        = firstVisit && firstVisit.extendedProps ? firstVisit.extendedProps : {};

            return {
                si: index + 1,
                exe: r.employee_name,
                date: r.date,

                // DCR-level (from first visit's extendedProps)
                tp_status:            ext.tp_status            || '',
                mgr_status:           ext.manager_status       || '',
                ca_status:            ext.ca_status            || '',
                travel_mode:          ext.travel_mode          || '',
                km_travelled:         ext.km_travelled         || '',
                travel_remarks:       ext.travel_remarks       || '',
                approved_km_travel:   ext.approved_km_travel   || '',
                approved_travel_cost: ext.approved_travel_cost || '',
                approved_travel_remarks: ext.approved_travel_remarks || '',

                visits:   r.visits,
                expenses: r.expenses
            };
        });

        // ---------------------------------
        // 4.3 Build DataTables columns
        // ---------------------------------
        const columns = [
            { data: "si" },
            { data: "exe" },
            { data: "date" },
            { data: "tp_status" },
            { data: "mgr_status" },
            { data: "ca_status" },
            { data: "travel_mode" },
            { data: "km_travelled" },
            { data: "travel_remarks" },
            { data: "approved_km_travel" },
            { data: "approved_travel_cost" },
            { data: "approved_travel_remarks" }
        ];

        // Helper: type map
        const tpTypeMap = {
            1: 'Collection',
            2: 'Sourcing',
            3: 'Both'
        };

        // VISIT COLUMNS
        for (let i = 0; i < maxVisits; i++) {

            // Basic 5
            columns.push({
                data: null,
                render: r => {
                    const v = r.visits[i];
                    if (!v || !v.extendedProps) return "";
                    const t = v.extendedProps.tour_plan_type;
                    return tpTypeMap[t] || "";
                }
            });
            columns.push({
                data: null,
                render: r => (r.visits[i] ? r.visits[i].title : "")
            });
            columns.push({
                data: null,
                render: r => (r.visits[i]?.extendedProps?.quantity ?? "")
            });
            columns.push({
                data: null,
                render: r => (r.visits[i]?.extendedProps?.status ?? "")
            });
            columns.push({
                data: null,
                render: r => (r.visits[i]?.extendedProps?.remarks ?? "")
            });

            // Collection side (extendedProps of that visit)
            columns.push({
                data: null,
                render: r => (r.visits[i]?.extendedProps?.available_quantity ?? "")
            });
            columns.push({
                data: null,
                render: r => (r.visits[i]?.extendedProps?.remaining_quantity ?? "")
            });
            columns.push({
                data: null,
                render: r => (r.visits[i]?.extendedProps?.price ?? "")
            });
            columns.push({
                data: null,
                render: r => (r.visits[i]?.extendedProps?.part_a_invoice_price ?? "")
            });
            columns.push({
                data: null,
                render: r => (r.visits[i]?.extendedProps?.part_b_invoice_price ?? "")
            });
            columns.push({
                data: null,
                render: r => (r.visits[i]?.extendedProps?.part_c_invoice_price ?? "")
            });
            columns.push({
                data: null,
                render: r => {
                    const val = r.visits[i]?.extendedProps?.include_gst;
                    if (val === null || val === undefined || val === "") return "";
                    return (parseInt(val) === 1) ? "Yes" : "No";
                }
            });
            columns.push({
                data: null,
                render: r => (r.visits[i]?.extendedProps?.gst_rate ?? "")
            });
            columns.push({
                data: null,
                render: r => (r.visits[i]?.extendedProps?.collection_total_plasma_price ?? "")
            });
            columns.push({
                data: null,
                render: r => (r.visits[i]?.extendedProps?.num_boxes ?? "")
            });
            columns.push({
                data: null,
                render: r => (r.visits[i]?.extendedProps?.num_units ?? "")
            });
            columns.push({
                data: null,
                render: r => (r.visits[i]?.extendedProps?.num_litres ?? "")
            });

            // Sourcing side: pull from first tour_plan_visit of that TP
            columns.push({
                data: null,
                render: r => {
                    const v   = r.visits[i];
                    if (!v || !v.extendedProps) return "";
                    const src = (v.extendedProps.tour_plan_visits || [])[0] || {};
                    return src.sourcing_contact_person || "";
                }
            });
            columns.push({
                data: null,
                render: r => {
                    const v   = r.visits[i];
                    if (!v || !v.extendedProps) return "";
                    const src = (v.extendedProps.tour_plan_visits || [])[0] || {};
                    return src.sourcing_mobile_number || "";
                }
            });
            columns.push({
                data: null,
                render: r => {
                    const v   = r.visits[i];
                    if (!v || !v.extendedProps) return "";
                    const src = (v.extendedProps.tour_plan_visits || [])[0] || {};
                    return src.sourcing_email || "";
                }
            });
            columns.push({
                data: null,
                render: r => {
                    const v   = r.visits[i];
                    if (!v || !v.extendedProps) return "";
                    const src = (v.extendedProps.tour_plan_visits || [])[0] || {};
                    return src.sourcing_address || "";
                }
            });
            columns.push({
                data: null,
                render: r => {
                    const v   = r.visits[i];
                    if (!v || !v.extendedProps) return "";
                    const src = (v.extendedProps.tour_plan_visits || [])[0] || {};
                    return src.sourcing_ffp_company || "";
                }
            });
            columns.push({
                data: null,
                render: r => {
                    const v   = r.visits[i];
                    if (!v || !v.extendedProps) return "";
                    const src = (v.extendedProps.tour_plan_visits || [])[0] || {};
                    return src.sourcing_plasma_price || "";
                }
            });
            columns.push({
                data: null,
                render: r => {
                    const v   = r.visits[i];
                    if (!v || !v.extendedProps) return "";
                    const src = (v.extendedProps.tour_plan_visits || [])[0] || {};
                    return src.sourcing_potential_per_month || "";
                }
            });
            columns.push({
                data: null,
                render: r => {
                    const v   = r.visits[i];
                    if (!v || !v.extendedProps) return "";
                    const src = (v.extendedProps.tour_plan_visits || [])[0] || {};
                    return src.sourcing_payment_terms || "";
                }
            });
            columns.push({
                data: null,
                render: r => {
                    const v   = r.visits[i];
                    if (!v || !v.extendedProps) return "";
                    const src = (v.extendedProps.tour_plan_visits || [])[0] || {};
                    return src.sourcing_remarks || "";
                }
            });
            columns.push({
                data: null,
                render: r => {
                    const v   = r.visits[i];
                    if (!v || !v.extendedProps) return "";
                    const src = (v.extendedProps.tour_plan_visits || [])[0] || {};
                    return src.sourcing_part_a_price || "";
                }
            });
            columns.push({
                data: null,
                render: r => {
                    const v   = r.visits[i];
                    if (!v || !v.extendedProps) return "";
                    const src = (v.extendedProps.tour_plan_visits || [])[0] || {};
                    return src.sourcing_part_b_price || "";
                }
            });
            columns.push({
                data: null,
                render: r => {
                    const v   = r.visits[i];
                    if (!v || !v.extendedProps) return "";
                    const src = (v.extendedProps.tour_plan_visits || [])[0] || {};
                    return src.sourcing_part_c_price || "";
                }
            });
            columns.push({
                data: null,
                render: r => {
                    const v   = r.visits[i];
                    if (!v || !v.extendedProps) return "";
                    const src = (v.extendedProps.tour_plan_visits || [])[0] || {};
                    const val = src.include_gst;
                    if (val === null || val === undefined || val === "") return "";
                    return (parseInt(val) === 1) ? "Yes" : "No";
                }
            });
            columns.push({
                data: null,
                render: r => {
                    const v   = r.visits[i];
                    if (!v || !v.extendedProps) return "";
                    const src = (v.extendedProps.tour_plan_visits || [])[0] || {};
                    return src.gst_rate || "";
                }
            });
            columns.push({
                data: null,
                render: r => {
                    const v   = r.visits[i];
                    if (!v || !v.extendedProps) return "";
                    const src = (v.extendedProps.tour_plan_visits || [])[0] || {};
                    return src.sourcing_total_plasma_price || "";
                }
            });
        }

        // EXPENSE COLUMNS
        for (let i = 0; i < maxExpenses; i++) {
            columns.push({
                data: null,
                render: r => (r.expenses[i]?.description || "")
            });
            columns.push({
                data: null,
                render: r => (r.expenses[i]?.food || "")
            });
            columns.push({
                data: null,
                render: r => (r.expenses[i]?.convention || "")
            });
            columns.push({
                data: null,
                render: r => (r.expenses[i]?.total_price || "")
            });
            columns.push({
                data: null,
                render: r => {
                    const docs = r.expenses[i]?.documents || [];
                    if (!docs.length) return "";
                    return docs.map(d => {
                        const url = baseImageUrl + d.attachments;
                        return `
                            <a href="${url}" target="_blank">
                              <img src="${url}"
                                   style="width:30px;height:30px;border-radius:4px;">
                            </a>
                        `;
                    }).join(" ");
                }
            });
        }

        // ACTION
        columns.push({
            data: null,
            orderable: false,
            render: () =>
                `<button class="btn btn-primary btn-sm printRow">
                   <i class="bi bi-printer"></i>
                 </button>`
        });

        // ---------------------------------
        // 4.4 INIT DATATABLE
        // ---------------------------------
        if ($.fn.DataTable.isDataTable('#dcrExpensesTable')) {
            table.destroy();
        }

        table = $('#dcrExpensesTable').DataTable({
            data: rows,
            columns: columns,
            scrollX: true,
            scrollCollapse: true,
            paging: true,
        //    dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                title: 'DCR_Expenses_Combined',
                exportOptions: {
                    columns: ':not(:last-child)' // exclude Action
                }
            }],
            fixedColumns: {
                left: 3   // SI.No, Executive, Date remain fixed
            }
        });
    }

    // -----------------------------
    // EXPORT
    // -----------------------------
    $('#exportButton').click(() => {
        if (table) {
            table.button(0).trigger();
        }
    });

    // -----------------------------
    // PRINT ROW (Full Visit + Expense Details)
    // -----------------------------
    $('#dcrExpensesTable').on('click', '.printRow', function () {
        if (!table) return;
        const row = table.row($(this).closest('tr')).data();
        if (!row) return;

        const win = window.open("", "_blank");
        win.document.write(`<html><body><h2>DCR + Expense Summary</h2>`);

        // General DCR Info
        win.document.write(`<h3>Executive: ${row.exe}</h3>`);
        win.document.write(`<h3>Date: ${row.date}</h3>`);

        win.document.write(`<p><b>TP Status:</b> ${row.tp_status}</p>`);
        win.document.write(`<p><b>Manager Status:</b> ${row.mgr_status}</p>`);
        win.document.write(`<p><b>CA Status:</b> ${row.ca_status}</p>`);
        win.document.write(`<p><b>Travel Mode:</b> ${row.travel_mode}</p>`);
        win.document.write(`<p><b>KM Travelled:</b> ${row.km_travelled}</p>`);
        win.document.write(`<p><b>Travel Remarks:</b> ${row.travel_remarks}</p>`);
        win.document.write(`<p><b>Approved KM Travel:</b> ${row.approved_km_travel}</p>`);
        win.document.write(`<p><b>Approved Travel Price:</b> ${row.approved_travel_cost}</p>`);
        win.document.write(`<p><b>Approved Travel Remarks:</b> ${row.approved_travel_remarks}</p>`);

        win.document.write(`<hr><h2>VISITS</h2>`);

        (row.visits || []).forEach((v, i) => {
            const ext = v.extendedProps || {};
            const typeText = ({1:'Collection',2:'Sourcing',3:'Both'}[ext.tour_plan_type] || '');

            // Sourcing data (first row)
            const src = (ext.tour_plan_visits || [])[0] || {};

            win.document.write(`<h3>Visit-${i + 1}</h3>`);
            win.document.write(`<p><b>Type:</b> ${typeText}</p>`);
            win.document.write(`<p><b>Title:</b> ${v.title || ''}</p>`);
            win.document.write(`<p><b>Qty:</b> ${ext.quantity || ''}</p>`);
            win.document.write(`<p><b>Status:</b> ${ext.status || ''}</p>`);
            win.document.write(`<p><b>Remarks:</b> ${ext.remarks || ''}</p>`);

            // Collection Block
            win.document.write(`<h4>Collection Details</h4>`);
            win.document.write(`<p><b>Qty Collected:</b> ${ext.available_quantity || ''}</p>`);
            win.document.write(`<p><b>Qty Remaining:</b> ${ext.remaining_quantity || ''}</p>`);
            win.document.write(`<p><b>Price:</b> ${ext.price || ''}</p>`);
            win.document.write(`<p><b>Part-A Price:</b> ${ext.part_a_invoice_price || ''}</p>`);
            win.document.write(`<p><b>Part-B Price:</b> ${ext.part_b_invoice_price || ''}</p>`);
            win.document.write(`<p><b>Part-C Price:</b> ${ext.part_c_invoice_price || ''}</p>`);
            win.document.write(`<p><b>GST Applicable:</b> ${ext.include_gst == 1 ? "Yes" : "No"}</p>`);
            win.document.write(`<p><b>GST Rate:</b> ${ext.gst_rate || ''}</p>`);
            win.document.write(`<p><b>Total Collection Price:</b> ${ext.collection_total_plasma_price || ''}</p>`);
            win.document.write(`<p><b>No. Boxes:</b> ${ext.num_boxes || ''}</p>`);
            win.document.write(`<p><b>No. Units:</b> ${ext.num_units || ''}</p>`);
            win.document.write(`<p><b>No. Litres:</b> ${ext.num_litres || ''}</p>`);

            // Sourcing Block
            win.document.write(`<h4>Sourcing Details</h4>`);
            win.document.write(`<p><b>Contact Person:</b> ${src.sourcing_contact_person || ''}</p>`);
            win.document.write(`<p><b>Mobile:</b> ${src.sourcing_mobile_number || ''}</p>`);
            win.document.write(`<p><b>Email:</b> ${src.sourcing_email || ''}</p>`);
            win.document.write(`<p><b>Address:</b> ${src.sourcing_address || ''}</p>`);
            win.document.write(`<p><b>FFP Company:</b> ${src.sourcing_ffp_company || ''}</p>`);
            win.document.write(`<p><b>Plasma Price:</b> ${src.sourcing_plasma_price || ''}</p>`);
            win.document.write(`<p><b>Potential / Month:</b> ${src.sourcing_potential_per_month || ''}</p>`);
            win.document.write(`<p><b>Payment Terms:</b> ${src.sourcing_payment_terms || ''}</p>`);
            win.document.write(`<p><b>Remarks:</b> ${src.sourcing_remarks || ''}</p>`);

            win.document.write(`<p><b>Part-A Price:</b> ${src.sourcing_part_a_price || ''}</p>`);
            win.document.write(`<p><b>Part-B Price:</b> ${src.sourcing_part_b_price || ''}</p>`);
            win.document.write(`<p><b>Part-C Price:</b> ${src.sourcing_part_c_price || ''}</p>`);
            win.document.write(`<p><b>GST Applicable:</b> ${src.include_gst == 1 ? "Yes" : "No"}</p>`);
            win.document.write(`<p><b>GST Rate:</b> ${src.gst_rate || ''}</p>`);
            win.document.write(`<p><b>Total Plasma Price:</b> ${src.sourcing_total_plasma_price || ''}</p>`);

            win.document.write(`<hr>`);
        });

        // ------------------------------------------
        // EXPENSE DETAILS
        // ------------------------------------------
        win.document.write(`<h2>EXPENSES</h2>`);

        (row.expenses || []).forEach((e, i) => {
            win.document.write(`<h3>Expense-${i + 1}</h3>`);
            win.document.write(`<p><b>Description:</b> ${e.description || ''}</p>`);
            win.document.write(`<p><b>Food:</b> ${e.food || ''}</p>`);
            win.document.write(`<p><b>Conveyance:</b> ${e.convention || ''}</p>`);
            win.document.write(`<p><b>Total:</b> ${e.total_price || ''}</p>`);

            if (e.documents && e.documents.length) {
                win.document.write(`<h4>Attachments:</h4>`);
                e.documents.forEach(d => {
                    const url = baseImageUrl + d.attachments;
                    win.document.write(`<img src="${url}" style="width:250px;margin:10px 0;"><br>`);
                });
            }

            win.document.write(`<hr>`);
        });

        win.document.write("</body></html>");
        win.document.close();
        win.print();
    });


});
</script>
@endpush
