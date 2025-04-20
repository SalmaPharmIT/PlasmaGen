@extends('include.dashboardLayout')

@section('title', 'Bag Entry Details')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Bag Entry Details</h5>
                <a href="{{ route('bag-entries.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Basic Information -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th style="width: 40%">Blood Centre</th>
                            <td>{{ $bagEntry->blood_centre }}</td>
                        </tr>
                        <tr>
                            <th>Work Station</th>
                            <td>{{ $bagEntry->work_station }}</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <td>{{ $bagEntry->date->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>Pickup Date</th>
                            <td>{{ $bagEntry->pickup_date->format('d-m-Y') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th style="width: 40%">A.R. No.</th>
                            <td>{{ $bagEntry->ar_no }}</td>
                        </tr>
                        <tr>
                            <th>GRN No.</th>
                            <td>{{ $bagEntry->grn_no }}</td>
                        </tr>
                        <tr>
                            <th>Mega Pool No.</th>
                            <td>{{ $bagEntry->mega_pool_no }}</td>
                        </tr>
                        <tr>
                            <th>Total Volume</th>
                            <td>{{ number_format($bagEntry->total_volume, 2) }} L</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Bag Details Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Donor ID</th>
                            <th>Donation Date</th>
                            <th>Blood Group</th>
                            <th>Bag Volume (ML)</th>
                            <th>Mini Pool Volume (L)</th>
                            <th>Segment No.</th>
                            <th>Tail Cutting</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bagEntry->bag_details as $index => $bag)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $bag['donor_id'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($bag['donation_date'])->format('d-m-Y') }}</td>
                                <td>{{ $bag['blood_group'] }}</td>
                                <td class="text-end">{{ $bag['bag_volume'] }}</td>
                                <td class="text-end">{{ $bag['mini_pool_bag_volume'] }}</td>
                                <td>{{ $bag['segment_number'] }}</td>
                                <td>{{ $bag['tail_cutting'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Audit Information -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th style="width: 40%">Created By</th>
                            <td>{{ $bagEntry->creator->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $bagEntry->created_at->format('d-m-Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th style="width: 40%">Last Updated By</th>
                            <td>{{ $bagEntry->updater->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated At</th>
                            <td>{{ $bagEntry->updated_at->format('d-m-Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table-sm th, 
    .table-sm td {
        padding: 0.5rem;
        font-size: 0.875rem;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>
@endpush 