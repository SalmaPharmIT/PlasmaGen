@extends('include.dashboardLayout')

@section('title', 'Non-Field Work')

@section('content')

<div class="pagetitle">
    <h1>Non-Field Work</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('tourplanner.nonFieldWork') }}">Non-Field Work</a></li>
        <li class="breadcrumb-item active">Non-Field Work</li>
      </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">

            <!-- Filters Row -->
            <div class="row mb-4 mt-2 align-items-end">

                <!-- Collecting Agent Dropdown -->
                <div class="col-md-4">
                    <label class="form-label">Collecting / Sourcing Executives</label>
                    <select id="collectingAgentDropdown" class="form-select select2">
                        <option value="">Choose Collecting Executives</option>
                    </select>
                </div>

                <!-- Month Picker -->
                <div class="col-md-4">
                    <label class="form-label">Select Month</label>
                    <input type="month" id="monthPicker" class="form-control" value="{{ date('Y-m') }}">
                </div>

                <!-- Filter Button -->
                <div class="col-md-2">
                    <button id="filterButton" class="btn btn-success w-100">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </div>

                <!-- Reset Button -->
                <div class="col-md-2">
                    <button id="resetButton" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table id="nonFieldWorkTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>SI No.</th>
                            <th>Employee Name</th>
                            <th>Visit Date</th>
                            <th>Actions</th>
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
#nonFieldWorkTable th, #nonFieldWorkTable td {
    text-align: center;
    vertical-align: middle;
}
</style>
@endpush


@push('scripts')

<script>
$(document).ready(function() {

    // Your PHP endpoint URLs
    var fetchNFUrl   = "{{ route('tourplanner.getNonFieldWork') }}";
    var deleteNFUrl  = "{{ route('tourplanner.deleteNonFieldWork') }}";
    var agentListUrl = "{{ route('tourplanner.getDCRApprovalsCollectingAgents') }}";

    // Initialize DataTable
    var table = $('#nonFieldWorkTable').DataTable({
        columns: [
            { data: null },
            { data: "employee_name" },
            { data: "visit_date" },
            { data: "actions" }
        ],
        order: [[2, "asc"]],
        drawCallback: function(settings) {
            var api = this.api();
            api.column(0).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }
    });

    // Load Collecting Agents
    function loadAgents() {
        $.ajax({
            url: agentListUrl,
            type: "GET",
            success: function(res) {
                if (res.success) {
                    var dropdown = $("#collectingAgentDropdown");
                    dropdown.empty().append('<option value="">Choose Collecting Executives</option>');
                    res.data.forEach(a => {
                        dropdown.append(`<option value="${a.id}">${a.name} (${a.role.role_name})</option>`);
                    });
                }
            }
        });
    }
    loadAgents();

    // Load Non Field Work Records
    function loadRecords() {

        let agentId = $("#collectingAgentDropdown").val();
        let month   = $("#monthPicker").val();

        Swal.fire({ title: "Loading...", allowOutsideClick: false, didOpen:()=>Swal.showLoading() });

        $.ajax({
            url: fetchNFUrl,
            type: "GET",
            data: { agent_id: agentId, month: month },
            success: function(res) {
                Swal.close();
                if (res.success) {
                    table.clear();

                    res.events.forEach(row => {
                        let deleteBtn = `
                            <button class="btn btn-danger btn-sm deleteNF" data-id="${row.id}">
                                Delete
                            </button>
                        `;

                        table.row.add({
                            employee_name: row.employee_name,
                            visit_date: row.visit_date,
                            actions: deleteBtn
                        });
                    });

                    table.draw();
                } else {
                    Swal.fire("Error", res.message, "error");
                }
            },
            error: function() {
                Swal.fire("Error", "Unable to load records", "error");
            }
        });
    }

    // Filter button
    $("#filterButton").click(loadRecords);

    // Reset button
    $("#resetButton").click(function() {
        $("#collectingAgentDropdown").val("");
        $("#monthPicker").val("{{ date('Y-m') }}");
        table.clear().draw();
    });

    // Delete record
    $(document).on("click", ".deleteNF", function() {
        let id = $(this).data("id");

        Swal.fire({
            title: "Are you sure?",
            text: "This will delete the record.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!"
        }).then(result => {
            if (result.isConfirmed) {
                console.log('deleteNFUrl: '+JSON.stringify({ id: id }));

                $.ajax({
                    url: deleteNFUrl,
                    type: "POST",
                    dataType: "json",   // ✅ IMPORTANT: forces jQuery to parse JSON
                    contentType: "application/json",
                    data: JSON.stringify({ id: id }),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                   
                    success: function(res) {
                        console.log("Delete Response:", res);
                        if (res.success === true) {      // ✅ strict check
                            Swal.fire("Deleted!", res.message, "success").then(() => {
                                loadRecords();
                            });
                        } else {
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function() {
                        Swal.fire("Error", "Unable to delete record", "error");
                    }
                });

            }
        });

    });

    // Initial load
    loadRecords();

});
</script>

@endpush
