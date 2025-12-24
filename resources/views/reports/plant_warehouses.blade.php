@extends('include.dashboardLayout')

@section('title', 'Plant Warehouses')

@section('content')

<div class="pagetitle">
    <h1>Plant Warehouses</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Warehouses</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">

                    <h5 class="card-title">Transferred Plant Warehouses</h5>

                    <div class="table-responsive">
                        <table id="plantWarehouseTable"
                               class="table table-striped table-bordered w-100">
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

                </div>
            </div>

        </div>
    </div>
</section>

<!-- VIEW TRANSFER DETAILS MODAL -->
<div class="modal fade" id="viewInfoModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Transfer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Collection Warehouse</th>
                            <th class="text-center">Boxes</th>
                            <th class="text-center">Units</th>
                            <th class="text-center">Litres</th>
                            <th class="text-center">Transfer Date</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="transferTableBody"></tbody>
                </table>
            </div>

        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    table td, table th {
        vertical-align: middle;
        white-space: normal;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function () {

    const table = $('#plantWarehouseTable').DataTable({
        processing: true,
        responsive: true,
        serverSide: false,
        data: [],
        columns: [

            {
                data: null,
                className: "text-center",
                render: (d, t, r, m) => m.row + 1
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

            /* TOTAL BOXES */
            {
                data: null,
                className: 'text-center',
                render: d => d.transfers
                    ? d.transfers.reduce((s, t) => s + Number(t.num_boxes || 0), 0)
                    : 0
            },

            /* TOTAL UNITS */
            {
                data: null,
                className: 'text-center',
                render: d => d.transfers
                    ? d.transfers.reduce((s, t) => s + Number(t.num_units || 0), 0)
                    : 0
            },

            /* TOTAL LITRES */
            {
                data: null,
                className: 'text-center',
                render: d => d.transfers
                    ? d.transfers.reduce((s, t) => s + Number(t.num_litres || 0), 0)
                    : 0
            },

            /* ACTION */
            {
                data: null,
                className: "text-center",
                orderable: false,
                render: d => `
                    <button class="btn btn-sm btn-primary view-info-btn"
                            data-row='${JSON.stringify(d)}'>
                        View
                    </button>
                `
            }
        ],
        language: {
            emptyTable: "No plant warehouse transfers found"
        }
    });

    fetchPlantWarehouses();

    function fetchPlantWarehouses() {
        $.ajax({
            url: "{{ route('reports.getTransferedPlantWarehouses') }}",
            type: "GET",
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    table.clear().rows.add(res.data).draw();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Unable to fetch plant warehouses.', 'error');
            }
        });
    }

    /* VIEW DETAILS */
    $(document).on('click', '.view-info-btn', function () {

        const row = $(this).data('row');
        const tbody = $('#transferTableBody');
        tbody.empty();

        if (!row.transfers || row.transfers.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="5" class="text-center">No transfer records found</td>
                </tr>
            `);
        } else {
            row.transfers.forEach(t => {
                tbody.append(`
                    <tr>
                        <td>${t.collection_warehouse_name || '-'}</td>
                        <td class="text-center">${t.num_boxes}</td>
                        <td class="text-center">${t.num_units}</td>
                        <td class="text-center">${t.num_litres}</td>
                        <td class="text-center">${formatDate(t.transfer_date)}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-danger delete-transfer-btn"
                                    data-id="${t.transaction_id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        }

        $('#viewInfoModal').modal('show');
    });

    function formatDate(dateTime) {
        if (!dateTime) return '-';
        const d = new Date(dateTime.replace(' ', 'T'));
        return String(d.getDate()).padStart(2,'0') + '-' +
               String(d.getMonth()+1).padStart(2,'0') + '-' +
               d.getFullYear();
    }


    $(document).on('click', '.delete-transfer-btn', function () {
            const transactionId = $(this).data('id');
            console.log('transactionId: ', transactionId);

            Swal.fire({
                title: 'Delete Transfer?',
                text: 'Are you sure want to delete? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {

                if (!result.isConfirmed) return;

                $.ajax({
                    url: "{{ route('reports.deletePlantWarehouseTransaction') }}",
                    type: "POST",
                    data: {
                        transaction_id: transactionId
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    success: function (res) {
                        if (res.success) {
                            Swal.fire('Deleted', res.message, 'success');
                            $('#viewInfoModal').modal('hide');
                            fetchPlantWarehouses(); // 🔥 refresh main table
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Unable to delete transfer.', 'error');
                    }
                });

            });
    });

});
</script>
@endpush
