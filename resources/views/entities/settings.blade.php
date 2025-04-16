@extends('include.dashboardLayout')

@section('title', 'Entity Settings')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header py-2">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="{{ asset('assets/img/pgblogo.png') }}" alt="" style="max-height: 40px;">
                </div>
                <div class="col-md-6 text-end">
                    <h5 class="mb-0">Entity Settings</h5>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Setting</th>
                                    <th>Value</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Entity Name</td>
                                    <td>{{ Auth::user()->entity->name ?? 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editEntityNameModal">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Entity Code</td>
                                    <td>{{ Auth::user()->entity->code ?? 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editEntityCodeModal">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Contact Person</td>
                                    <td>{{ Auth::user()->entity->contact_person ?? 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editContactPersonModal">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Contact Number</td>
                                    <td>{{ Auth::user()->entity->contact_number ?? 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editContactNumberModal">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>{{ Auth::user()->entity->email ?? 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editEmailModal">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Address</td>
                                    <td>{{ Auth::user()->entity->address ?? 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editAddressModal">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Entity Name Modal -->
<div class="modal fade" id="editEntityNameModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Entity Name</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editEntityNameForm">
                    @csrf
                    <div class="mb-3">
                        <label for="entityName" class="form-label">Entity Name</label>
                        <input type="text" class="form-control" id="entityName" name="name" value="{{ Auth::user()->entity->name ?? '' }}">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateEntityName()">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Entity Code Modal -->
<div class="modal fade" id="editEntityCodeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Entity Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editEntityCodeForm">
                    @csrf
                    <div class="mb-3">
                        <label for="entityCode" class="form-label">Entity Code</label>
                        <input type="text" class="form-control" id="entityCode" name="code" value="{{ Auth::user()->entity->code ?? '' }}">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateEntityCode()">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Contact Person Modal -->
<div class="modal fade" id="editContactPersonModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Contact Person</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editContactPersonForm">
                    @csrf
                    <div class="mb-3">
                        <label for="contactPerson" class="form-label">Contact Person</label>
                        <input type="text" class="form-control" id="contactPerson" name="contact_person" value="{{ Auth::user()->entity->contact_person ?? '' }}">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateContactPerson()">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Contact Number Modal -->
<div class="modal fade" id="editContactNumberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Contact Number</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editContactNumberForm">
                    @csrf
                    <div class="mb-3">
                        <label for="contactNumber" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contactNumber" name="contact_number" value="{{ Auth::user()->entity->contact_number ?? '' }}">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateContactNumber()">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Email Modal -->
<div class="modal fade" id="editEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editEmailForm">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->entity->email ?? '' }}">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateEmail()">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Address Modal -->
<div class="modal fade" id="editAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editAddressForm">
                    @csrf
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3">{{ Auth::user()->entity->address ?? '' }}</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateAddress()">Save changes</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateEntityName() {
        const form = document.getElementById('editEntityNameForm');
        const formData = new FormData(form);
        
        fetch('/entities/update-name', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating entity name');
            }
        });
    }

    function updateEntityCode() {
        const form = document.getElementById('editEntityCodeForm');
        const formData = new FormData(form);
        
        fetch('/entities/update-code', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating entity code');
            }
        });
    }

    function updateContactPerson() {
        const form = document.getElementById('editContactPersonForm');
        const formData = new FormData(form);
        
        fetch('/entities/update-contact-person', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating contact person');
            }
        });
    }

    function updateContactNumber() {
        const form = document.getElementById('editContactNumberForm');
        const formData = new FormData(form);
        
        fetch('/entities/update-contact-number', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating contact number');
            }
        });
    }

    function updateEmail() {
        const form = document.getElementById('editEmailForm');
        const formData = new FormData(form);
        
        fetch('/entities/update-email', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating email');
            }
        });
    }

    function updateAddress() {
        const form = document.getElementById('editAddressForm');
        const formData = new FormData(form);
        
        fetch('/entities/update-address', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating address');
            }
        });
    }
</script>
@endpush
@endsection 