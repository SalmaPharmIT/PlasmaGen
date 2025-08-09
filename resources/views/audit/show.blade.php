@extends('include.dashboardLayout')

@section('content')
<div class="pagetitle">
  <h1>Audit Trail Details</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('audit.index') }}">Audit Trail</a></li>
      <li class="breadcrumb-item active">Details #{{ $auditTrail->id }}</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body pt-3">
          <!-- Back button -->
          <div class="mb-4">
            <a href="{{ route('audit.index') }}" class="btn btn-secondary">
              <i class="bi bi-arrow-left"></i> Back to Audit Trail
            </a>
          </div>
          
          <h5 class="card-title">Audit Event Details</h5>
          
          <!-- General Information -->
          <div class="row mb-4">
            <div class="col-md-6">
              <table class="table table-bordered">
                <tr>
                  <th style="width: 30%">ID</th>
                  <td>{{ $auditTrail->id }}</td>
                </tr>
                <tr>
                  <th>User</th>
                  <td>{{ $auditTrail->user_name ?? 'System' }}</td>
                </tr>
                <tr>
                  <th>Role</th>
                  <td>{{ $auditTrail->user_role ?? '-' }}</td>
                </tr>
                <tr>
                  <th>Action</th>
                  <td>
                    <span class="badge bg-{{ getActionBadgeColor($auditTrail->action) }}">
                      {{ ucfirst($auditTrail->action) }}
                    </span>
                  </td>
                </tr>
              </table>
            </div>
            <div class="col-md-6">
              <table class="table table-bordered">
                <tr>
                  <th style="width: 30%">Module</th>
                  <td>{{ $auditTrail->module ?? '-' }}</td>
                </tr>
                <tr>
                  <th>Section</th>
                  <td>{{ $auditTrail->section ?? '-' }}</td>
                </tr>
                <tr>
                  <th>Record ID</th>
                  <td>{{ $auditTrail->record_id ?? '-' }}</td>
                </tr>
                <tr>
                  <th>Date/Time</th>
                  <td>{{ $auditTrail->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
              </table>
            </div>
          </div>
          
          <!-- Description -->
          <div class="row mb-4">
            <div class="col-md-12">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Description</h5>
                  <p>{{ $auditTrail->description ?? 'No description available' }}</p>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Technical Details -->
          <div class="row mb-4">
            <div class="col-md-12">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Technical Details</h5>
                  <table class="table table-bordered">
                    <tr>
                      <th style="width: 20%">IP Address</th>
                      <td>{{ $auditTrail->ip_address ?? '-' }}</td>
                    </tr>
                    <tr>
                      <th>User Agent</th>
                      <td>{{ $auditTrail->user_agent ?? '-' }}</td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Changed Values -->
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Changed Values</h5>
                  
                  @if(!empty($oldValues) || !empty($newValues))
                    <div class="table-responsive">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th>Field</th>
                            <th>Old Value</th>
                            <th>New Value</th>
                          </tr>
                        </thead>
                        <tbody>
                          @if($auditTrail->action == 'update')
                            @foreach($newValues as $field => $value)
                              <tr>
                                <td><strong>{{ $field }}</strong></td>
                                <td>{{ $oldValues[$field] ?? '-' }}</td>
                                <td>{{ $value ?? '-' }}</td>
                              </tr>
                            @endforeach
                          @elseif($auditTrail->action == 'create')
                            @foreach($newValues as $field => $value)
                              <tr>
                                <td><strong>{{ $field }}</strong></td>
                                <td>-</td>
                                <td>{{ $value ?? '-' }}</td>
                              </tr>
                            @endforeach
                          @elseif($auditTrail->action == 'delete')
                            @foreach($oldValues as $field => $value)
                              <tr>
                                <td><strong>{{ $field }}</strong></td>
                                <td>{{ $value ?? '-' }}</td>
                                <td>-</td>
                              </tr>
                            @endforeach
                          @endif
                        </tbody>
                      </table>
                    </div>
                  @else
                    <p>No value changes recorded for this action.</p>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@php
function getActionBadgeColor($action) {
  switch ($action) {
    case 'create': return 'success';
    case 'update': return 'warning';
    case 'delete': return 'danger';
    case 'login': return 'info';
    case 'logout': return 'secondary';
    case 'test': return 'primary';
    case 'view': return 'info';
    default: return 'primary';
  }
}
@endphp 