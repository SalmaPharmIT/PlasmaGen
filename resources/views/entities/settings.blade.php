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
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">
                    {{ session('warning') }}
                </div>
            @endif

            <form action="{{ route('entity.settings.save') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    {{-- <div class="col-md-6">
                        <div class="mb-3">
                            <label for="entity_name">Entity Name</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->entity->name ?? 'N/A' }}" name="entity_name" id="entity_name">
                        </div>
                    </div> --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="entity_ref_doc">Reference Document No</label>
                            <input type="text" class="form-control @error('entity_ref_doc') is-invalid @enderror" 
                                value="{{ $settings->ref_no ?? old('entity_ref_doc') }}" 
                                name="entity_ref_doc" 
                                id="entity_ref_doc">
                            @error('entity_ref_doc')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
   
</script>
@endpush
@endsection 