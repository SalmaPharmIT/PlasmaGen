@extends('include.dashboardLayout')

@section('title', 'User Report Mapping')

@section('content')
<div class="pagetitle">
    <h1>User Report Mapping</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('users.reportMapping') }}">User Report Mapping</a></li>
        <li class="breadcrumb-item active">View</li>
      </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <!-- Header with Button -->
          <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="card-title">User Report Mapping</h5>
              <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addManagerModal">
                  <i class="bi bi-plus me-1"></i> Add Manager
              </a>
          </div>

          <!-- Success Message -->
          @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="bi bi-check-circle me-1"></i>
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          @if($errors->any())
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="bi bi-exclamation-octagon me-1"></i>
              {{ implode(' ', $errors->all()) }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          <!-- Mapping Data Table -->
          <table id="mappingTable" class="table table-striped table-bordered col-lg-12">
            <thead>
              <tr>
                <th>SI. No</th>
                <th>Entity</th>
                <th>Manager</th>
                <th>Manager Role</th>
                <th>Employee</th>
                <th>Employee Role</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {{-- Data populated via AJAX --}}
            </tbody>
          </table>
          <!-- End Mapping Data Table -->
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Add Manager Modal -->
<div class="modal fade" id="addManagerModal" tabindex="-1" aria-labelledby="addManagerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form id="addManagerForm" action="{{ route('users.reportMapping.submit') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addManagerModalLabel">Add Manager</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Manager Role Dropdown -->
            <div class="col-md-6 mb-3">
              <label for="role" class="form-label">Manager Role <span style="color:red">*</span></label>
              <select name="role" id="role" class="form-select select2">
                <option value="">Select Role</option>
                @foreach($roles as $role)
                  <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
              </select>
            </div>
            <!-- Manager Dropdown -->
            <div class="col-md-6 mb-3">
              <label for="manager" class="form-label">Manager <span style="color:red">*</span></label>
              <select name="manager" id="manager" class="form-select select2">
                <option value="">Select Manager</option>
                {{-- Options loaded dynamically --}}
              </select>
            </div>
            <!-- Employee Role Dropdown -->
            <div class="col-md-6 mb-3">
              <label for="employee_role" class="form-label">Employee Role <span style="color:red">*</span></label>
              <select name="employee_role" id="employee_role" class="form-select select2">
                <option value="">Select Employee Role</option>
                {{-- Options loaded dynamically via getRoleByDownwardHierarchy --}}
              </select>
            </div>
            <!-- Employee Dropdown -->
            <div class="col-md-6 mb-3">
              <label for="employee" class="form-label">Employee <span style="color:red">*</span></label>
              <select name="employee" id="employee" class="form-select select2">
                <option value="">Select Employee</option>
                {{-- Options loaded dynamically --}}
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Mapping Modal -->
<div class="modal fade" id="editMappingModal" tabindex="-1" aria-labelledby="editMappingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form id="editMappingForm" action="#" method="POST">
        @csrf
        <!-- You may include a hidden input for the mapping ID -->
        <input type="hidden" name="mapping_id" id="mapping_id">
        <div class="modal-header">
          <h5 class="modal-title" id="editMappingModalLabel">Edit Manager</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Manager Role Dropdown (read-only or selectable as needed) -->
            <div class="col-md-6 mb-3">
              <label for="edit_role" class="form-label">Manager Role <span style="color:red">*</span></label>
              <select name="role" id="edit_role" class="form-select select2">
                <option value="">Select Role</option>
                @foreach($roles as $role)
                  <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
              </select>
            </div>
            <!-- Manager Dropdown -->
            <div class="col-md-6 mb-3">
              <label for="edit_manager" class="form-label">Manager <span style="color:red">*</span></label>
              <select name="manager" id="edit_manager" class="form-select select2">
                <option value="">Select Manager</option>
              </select>
            </div>
            <!-- Employee Role Dropdown -->
            <div class="col-md-6 mb-3">
              <label for="edit_employee_role" class="form-label">Employee Role <span style="color:red">*</span></label>
              <select name="employee_role" id="edit_employee_role" class="form-select select2">
                <option value="">Select Employee Role</option>
              </select>
            </div>
            <!-- Employee Dropdown -->
            <div class="col-md-6 mb-3">
              <label for="edit_employee" class="form-label">Employee <span style="color:red">*</span></label>
              <select name="employee" id="edit_employee" class="form-select select2">
                <option value="">Select Employee</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable for mapping data
        var mappingTable = $('#mappingTable').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('getUserReportMapping') }}",
                type: 'GET',
                dataSrc: function(json) {
                    if(json.success) {
                        return json.data;
                    } else {
                        Swal.fire('Error', json.message, 'error');
                        return [];
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching mapping data:", error);
                    Swal.fire('Error', 'An error occurred while fetching the mapping data.', 'error');
                }
            },
            columns: [
                // { data: 'id' },
                { 
                    data: null, 
                    title: 'SI. No',
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'entity_name', defaultContent: 'N/A' },
                { data: 'manager_name', defaultContent: 'N/A' },
                { data: 'manager_role_name', defaultContent: 'N/A' },
                { data: 'employee_name', defaultContent: 'N/A' },
                { data: 'employee_role_name', defaultContent: 'N/A' },
                { 
                  data: 'created_at', 
                  render: function(data) {
                      // Show only the date part (assuming format "YYYY-MM-DD HH:MM:SS")
                      return data ? data.split(' ')[0] : 'N/A';
                  },
                  defaultContent: 'N/A'
                },
                { 
                    data: null,
                    render: function(data, type, row) {
                        return `
                           <div class="dropdown">
                               <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                   <i class="bi bi-gear"></i>
                               </button>
                               <ul class="dropdown-menu">
                                   <li><a href="#" class="dropdown-item editMapping" data-mapping='${JSON.stringify(row)}'>Edit</a></li>
                                   <li><a href="#" class="dropdown-item deleteMapping" data-mapping-id="${row.id}">Delete</a></li>
                               </ul>
                           </div>
                       `;
                    },
                    orderable: false,
                    searchable: false
                }
            ],
            order: [[0, 'desc']], // Sort by the first column (ID) in descending order
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100]
        });

        // Initialize Select2 for modal elements when modals are shown
        $('#addManagerModal, #editMappingModal').on('shown.bs.modal', function() {
            $(this).find('.select2').select2({
                theme: 'bootstrap-5',
                dropdownParent: $(this),
                width: '100%',
                placeholder: 'Select an option',
                allowClear: true
            });
        });

        // Destroy Select2 on modal hide (optional)
        $('#addManagerModal, #editMappingModal').on('hidden.bs.modal', function() {
            $(this).find('.select2').each(function() {
                $(this).select2('destroy');
            });
        });

        // When Manager Role changes (in Add Modal):
        $('#role').on('change', function(){
            var roleId = $(this).val();
            if(roleId) {
                // Update Manager dropdown
                $.ajax({
                    url: "{{ route('getEmployeeByRoleId', ['roleId' => ':roleId']) }}".replace(':roleId', roleId),
                    type: 'GET',
                    success: function(response){
                        if(response.success){
                            var managerSelect = $('#manager');
                            managerSelect.empty().append('<option value="">Select Manager</option>');
                            $.each(response.data, function(index, employee){
                                managerSelect.append('<option value="'+employee.id+'">'+employee.name+'</option>');
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error){
                        console.error("Error fetching employees for manager role:", error);
                    }
                });
                // Update Employee Role dropdown
                $.ajax({
                    url: "{{ route('getRoleByDownwardHierarchy', ['roleId' => ':roleId']) }}".replace(':roleId', roleId),
                    type: 'GET',
                    success: function(response){
                        if(response.success){
                            var employeeRoleSelect = $('#employee_role');
                            employeeRoleSelect.empty().append('<option value="">Select Employee Role</option>');
                            $.each(response.data, function(index, role){
                                employeeRoleSelect.append('<option value="'+role.id+'">'+role.name+'</option>');
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error){
                        console.error("Error fetching roles by downward hierarchy:", error);
                    }
                });
            } else {
                $('#manager').empty().append('<option value="">Select Manager</option>');
                $('#employee_role').empty().append('<option value="">Select Employee Role</option>');
            }
        });

        // When Employee Role changes (in Add Modal), update Employee dropdown
        $('#employee_role').on('change', function(){
            var roleId = $(this).val();
            if(roleId) {
                $.ajax({
                    url: "{{ route('getEmployeeByRoleId', ['roleId' => ':roleId']) }}".replace(':roleId', roleId),
                    type: 'GET',
                    success: function(response){
                        if(response.success){
                            var employeeSelect = $('#employee');
                            employeeSelect.empty().append('<option value="">Select Employee</option>');
                            $.each(response.data, function(index, employee){
                                employeeSelect.append('<option value="'+employee.id+'">'+employee.name+'</option>');
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error){
                        console.error("Error fetching employees for employee role:", error);
                    }
                });
            } else {
                $('#employee').empty().append('<option value="">Select Employee</option>');
            }
        });

        $('#mappingTable').on('click', '.editMapping', function(e) {
            e.preventDefault();
            var mappingData = $(this).data('mapping');
            // Fill the hidden mapping id and the Manager Role field
            $('#mapping_id').val(mappingData.id);
            $('#edit_role').val(mappingData.manager_role_id);

            // First, update the Manager dropdown based on the Manager Role
            $.ajax({
                url: "{{ route('getEmployeeByRoleId', ['roleId' => ':roleId']) }}".replace(':roleId', mappingData.manager_role_id),
                type: 'GET',
                success: function(response) {
                    if(response.success){
                      var managerSelect = $('#edit_manager');
                      managerSelect.empty().append('<option value="">Select Manager</option>');
                      $.each(response.data, function(index, employee){
                          managerSelect.append('<option value="'+employee.id+'">'+employee.name+'</option>');
                      });
                      // Set the selected Manager
                      $('#edit_manager').val(mappingData.manager_id);
                    } else {
                      Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr, status, error){
                    console.error("Error fetching employees for edit manager role:", error);
                }
            });
            
            // Next, update the Employee Role dropdown based on the Manager Role (via downward hierarchy)
            $.ajax({
                url: "{{ route('getRoleByDownwardHierarchy', ['roleId' => ':roleId']) }}".replace(':roleId', mappingData.manager_role_id),
                type: 'GET',
                success: function(response) {
                    if(response.success){
                      var employeeRoleSelect = $('#edit_employee_role');
                      employeeRoleSelect.empty().append('<option value="">Select Employee Role</option>');
                      $.each(response.data, function(index, role){
                          employeeRoleSelect.append('<option value="'+role.id+'">'+role.name+'</option>');
                      });
                      // Set the selected Employee Role
                      $('#edit_employee_role').val(mappingData.employee_role_id);
                      
                      // Once Employee Role is set, update the Employee dropdown
                      $.ajax({
                            url: "{{ route('getEmployeeByRoleId', ['roleId' => ':roleId']) }}".replace(':roleId', mappingData.employee_role_id),
                            type: 'GET',
                            success: function(resp){
                                if(resp.success){
                                    var employeeSelect = $('#edit_employee');
                                    employeeSelect.empty().append('<option value="">Select Employee</option>');
                                    $.each(resp.data, function(index, employee){
                                        employeeSelect.append('<option value="'+employee.id+'">'+employee.name+'</option>');
                                    });
                                    // Set the selected Employee
                                    $('#edit_employee').val(mappingData.employee_id);
                                } else {
                                    Swal.fire('Error', resp.message, 'error');
                                }
                            },
                            error: function(xhr, status, error){
                                console.error("Error fetching employees for edit employee role:", error);
                            }
                      });
                    } else {
                      Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr, status, error){
                    console.error("Error fetching roles by downward hierarchy for edit:", error);
                }
            });
            
            // Finally, open the edit modal
            $('#editMappingModal').modal('show');
        });


        // Handle Edit Modal form submission via AJAX
        $('#editMappingForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: "{{ route('users.reportMapping.edit') }}", // Define an edit route in web.php and controller
                type: 'POST',
                data: formData,
                success: function(response) {
                    if(response.success) {
                        $('#editMappingModal').modal('hide');
                        Swal.fire('Success', response.message, 'success');
                        mappingTable.ajax.reload();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error updating mapping:", error);
                    Swal.fire('Error', 'An error occurred while updating mapping.', 'error');
                }
            });
        });


        // Similar change events for edit modal dropdowns
        $('#edit_role').on('change', function(){
            var roleId = $(this).val();
            if(roleId) {
                $.ajax({
                    url: "{{ route('getEmployeeByRoleId', ['roleId' => ':roleId']) }}".replace(':roleId', roleId),
                    type: 'GET',
                    success: function(response){
                        if(response.success){
                            var managerSelect = $('#edit_manager');
                            managerSelect.empty().append('<option value="">Select Manager</option>');
                            $.each(response.data, function(index, employee){
                                managerSelect.append('<option value="'+employee.id+'">'+employee.name+'</option>');
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error){
                        console.error("Error fetching employees for edit manager role:", error);
                    }
                });
                $.ajax({
                    url: "{{ route('getRoleByDownwardHierarchy', ['roleId' => ':roleId']) }}".replace(':roleId', roleId),
                    type: 'GET',
                    success: function(response){
                        if(response.success){
                            var employeeRoleSelect = $('#edit_employee_role');
                            employeeRoleSelect.empty().append('<option value="">Select Employee Role</option>');
                            $.each(response.data, function(index, role){
                                employeeRoleSelect.append('<option value="'+role.id+'">'+role.name+'</option>');
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error){
                        console.error("Error fetching roles by downward hierarchy for edit:", error);
                    }
                });
            } else {
                $('#edit_manager').empty().append('<option value="">Select Manager</option>');
                $('#edit_employee_role').empty().append('<option value="">Select Employee Role</option>');
            }
        });

        $('#edit_employee_role').on('change', function(){
            var roleId = $(this).val();
            if(roleId) {
                $.ajax({
                    url: "{{ route('getEmployeeByRoleId', ['roleId' => ':roleId']) }}".replace(':roleId', roleId),
                    type: 'GET',
                    success: function(response){
                        if(response.success){
                            var employeeSelect = $('#edit_employee');
                            employeeSelect.empty().append('<option value="">Select Employee</option>');
                            $.each(response.data, function(index, employee){
                                employeeSelect.append('<option value="'+employee.id+'">'+employee.name+'</option>');
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error){
                        console.error("Error fetching employees for edit employee role:", error);
                    }
                });
            } else {
                $('#edit_employee').empty().append('<option value="">Select Employee</option>');
            }
        });


        // (Optional) Handle Delete action...
        $('#mappingTable').on('click', '.deleteMapping', function(e) {
            e.preventDefault();
            var mappingId = $(this).data('mapping-id');
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to delete this user report mapping?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if(result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('users.reportMapping.delete') }}",
                        type: 'POST',
                        data: { mapping_id: mappingId, _token: "{{ csrf_token() }}" },
                        success: function(response) {
                            if(response.success) {
                                Swal.fire('Deleted', response.message, 'success');
                                mappingTable.ajax.reload();
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error deleting mapping:", error);
                            Swal.fire('Error', 'An error occurred while deleting mapping.', 'error');
                        }
                    });
                }
            });
        });


    });
</script>
@endpush
