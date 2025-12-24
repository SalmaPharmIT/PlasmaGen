@extends('include.dashboardLayout')

@section('title', 'Collection Warehouses')

@section('content')

<div class="pagetitle">
    <h1>Collection Warehouses</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Warehouses</li>
        </ol>
    </nav>
</div>
<!-- End Page Title -->

<section class="section">
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">

                    <h5 class="card-title">Warehouse List</h5>

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-1"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Error Messages -->
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-octagon me-1"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Table -->
                    <div class="table-responsive">
                        <table id="collectionWarehouseTable"
                               class="table table-striped table-bordered col-lg-12">
                            <thead>
                            <tr>
                                <th class="text-center">SI. No.</th>
                                <th>Warehouse Name</th>
                                <th class="text-center">City</th>
                                <th class="text-center">State</th>
                                <th class="text-center">Pincode</th>
                                <th class="text-center">Mobile</th>
                                <th>Email</th>
                                <th class="text-center">Total Boxes</th>
                                <th class="text-center">Total Units</th>
                                <th class="text-center">Total Litres</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <!-- End Table -->

                </div>
            </div>

        </div>
    </div>
</section>

<!-- View Info Model -->
<div class="modal fade" id="viewInfoModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tour Plan Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Visit Date</th>
                            <th>Employee Name</th>
                            <th>Blood Bank</th>
                            <th>No. of Boxes</th>
                            <th>No. of Units</th>
                            <th>No. of Litres</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="tourPlanTableBody"></tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Add Transfer Details Model -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add Transfer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="transferForm">

                    <input type="hidden" id="transferWarehouseId">
                    <input type="hidden" id="transferTourPlanIds" name="transferTourPlanIds">

                    <div class="row">
                         <div class="col-md-12 mb-3">
                            <strong>Warehouse:</strong> <span id="twName"></span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12 d-flex justify-content-between gap-2">

                            <button type="button"
                                    class="btn btn-primary btn-sm flex-fill text-center py-2">
                                <div class="small text-white-50"><strong>Total Boxes</strong></div>
                                <div class="fw-bold fs-5" id="twBoxes">0</div>
                            </button>

                            <button type="button"
                                    class="btn btn-success btn-sm flex-fill text-center py-2">
                                <div class="small text-white-50"><strong>Total Units</strong></div>
                                <div class="fw-bold fs-5" id="twUnits">0</div>
                            </button>

                            <button type="button"
                                    class="btn btn-warning btn-sm flex-fill text-center py-2">
                                <div class="small"><strong>Total Litres</strong></div>
                                <div class="fw-bold fs-5" id="twLitres">0</div>
                            </button>

                        </div>
                    </div>


                     <div class="row">
                         <div class="col-md-12 mb-3">
                             <label class="form-label"><strong>Select Plant Warehouse</strong></label>
                        <select id="plantWarehouseSelect" class="form-select select2"  required></select>
                        </div>
                    </div>
                

                    <button type="submit" class="btn btn-success">Submit Transfer</button>

                </form>
            </div>

        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .table-responsive {
        overflow-x: auto;
    }
    table td, table th {
        vertical-align: middle;
        word-wrap: break-word;
        white-space: normal;
    }
</style>
@endpush

@push('scripts')

<script>
    $(document).ready(function () {
        let plantWarehouses = [];

        // Initialize DataTable
        var table = $('#collectionWarehouseTable').DataTable({
            processing: true,
            responsive: true,
            serverSide: false,
            data: [],
            columns: [
                {
                    data: null,
                    className: "text-center",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'name' },
                {
                    data: 'city.name',
                    className: 'text-center',
                    defaultContent: '-'
                },
                {
                    data: 'state.name',
                    className: 'text-center',
                    defaultContent: '-'
                },
                {
                    data: 'pincode',
                    className: 'text-center',
                    defaultContent: '-'
                },
                {
                    data: 'mobile_no',
                    className: 'text-center',
                    defaultContent: '-'
                },
                {
                    data: 'email',
                    defaultContent: '-'
                },
                {
                    data: 'total_num_boxes',
                    className: 'text-center',
                    defaultContent: '0'
                },
                {
                    data: 'total_num_units',
                    className: 'text-center',
                    defaultContent: '0'
                },
                {
                    data: 'total_num_litres',
                    className: 'text-center',
                    defaultContent: '0'
                },
                {
                    data: null,
                    className: "text-center",
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-sm btn-primary view-info-btn mb-1"
                                    data-row='${JSON.stringify(row)}'>
                                View
                            </button>
                            <button class="btn btn-sm btn-success add-transfer-btn ms-1"
                                    data-row='${JSON.stringify(row)}'>
                                Transfer
                            </button>
                        `;
                    }
                }
            ],
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            language: {
                emptyTable: "No warehouses found"
            }
        });

        $('#plantWarehouseSelect').select2({
            placeholder: 'Select Plant Warehouse',
            theme: 'bootstrap-5',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#transferModal') // 🔥 VERY IMPORTANT
        });

        // Fetch data on page load
        fetchCollectionWarehouses();
        fetchPlantWarehouses();

        function fetchCollectionWarehouses() {
            $.ajax({
                url: "{{ route('reports.geCollectionWarehousesList') }}",
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        table.clear().rows.add(response.data).draw();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                        table.clear().draw();
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Unable to fetch collection warehouses.', 'error');
                }
            });
        }

         function fetchPlantWarehouses() {
            $.ajax({
                url: "{{ route('reports.getPlantWarehouses') }}",
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    plantWarehouses = response.data;
                },
                error: function () {
                    Swal.fire('Error', 'Unable to fetch plant warehouses.', 'error');
                }
            });
        }


        $(document).on('click', '.view-info-btn', function () {
            const row = $(this).data('row');
            const tbody = $('#tourPlanTableBody');
            tbody.empty();

            if (!row.tour_plan_data || row.tour_plan_data.length === 0) {
                tbody.append(`<tr><td colspan="7" class="text-center">No records found</td></tr>`);
            } else {
                row.tour_plan_data.forEach(tp => {
                    tbody.append(`
                        <tr>
                            <td>${tp.visit_date}</td>
                            <td>${tp.employee_name}</td>
                            <td>${tp.blood_bank_name}</td>
                            <td class="text-center">${tp.num_boxes}</td>
                            <td class="text-center">${tp.num_units}</td>
                            <td class="text-center">${tp.num_litres}</td>
                            <td>${formatDate(tp.created_at)}</td>
                        </tr>
                    `);
                });
            }

            $('#viewInfoModal').modal('show');
        });

        function formatDate(dateTime) {
            if (!dateTime) return '-';

            const date = new Date(dateTime.replace(' ', 'T')); // Safari-safe
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();

            return `${day}-${month}-${year}`;
        }


        $(document).on('click', '.add-transfer-btn', function () {
            const row = $(this).data('row');

            $('#transferWarehouseId').val(row.id);

            // ✅ Extract tour plan IDs
            const tourPlanIds = row.tour_plan_data.map(tp => tp.id);
            $('#transferTourPlanIds').val(JSON.stringify(tourPlanIds));

            $('#twName').text(row.name);
            $('#twBoxes').text(row.total_num_boxes);
            $('#twUnits').text(row.total_num_units);
            $('#twLitres').text(row.total_num_litres);

            const select = $('#plantWarehouseSelect');
            select.empty().append('<option value="">Select</option>');

            plantWarehouses.forEach(pw => {
                select.append(`<option value="${pw.id}">${pw.name}</option>`);
            });

            select.trigger('change'); // refresh select2

            // store tour plan ids
            $('#transferForm').data('tourPlans', row.tour_plan_data);

            $('#transferModal').modal('show');
        });


        $('#transferForm').on('submit', function (e) {
            e.preventDefault();

            const payload = {
                warehouse_id: $('#transferWarehouseId').val(),
                plant_warehouse_id: $('#plantWarehouseSelect').val(),
                // tour_plan_ids: $(this).data('tourPlans').map(tp => tp.id),
                tour_plan_ids: JSON.parse($('#transferTourPlanIds').val()),
                // ✅ ADD THESE
                num_boxes: parseInt($('#twBoxes').text()) || 0,
                num_units: parseInt($('#twUnits').text()) || 0,
                num_litres: parseInt($('#twLitres').text()) || 0,
            };

            $.ajax({
                url: "{{ route('reports.transferToPlantWarehouseSubmit') }}",
                type: "POST",
                data: payload,
                dataType: "json",   // ✅ IMPORTANT: forces jQuery to parse JSON
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                success: function (res) {
                    if (res.success) {
                        Swal.fire('Success', res.message, 'success');
                        $('#transferModal').modal('hide');
                        fetchCollectionWarehouses();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }
            });
        });

    });
</script>
@endpush
