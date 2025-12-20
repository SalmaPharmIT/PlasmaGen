@extends('include.dashboardLayout')

@section('title', 'View Entities')

@section('content')

<div class="pagetitle">
    <h1>Entity Feature Settings</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('entities.index') }}">Entity</a></li>
        <li class="breadcrumb-item active">Settings</li>
      </ol>
    </nav>

  </div><!-- End Page Title -->

  <section class="section">

    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
           
           <!-- Updated Header with Button -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title">Entity Features & Km Limit </h5>
            </div>


                <!-- Display Success Message -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
    
                <!-- Display Error Messages -->
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-octagon me-1"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
    

              <!-- Entity Feature Settings Form -->
              <form class="row g-3" action="{{ route('entities.features.update') }}" method="POST">
                @csrf
                @method('PUT') <!-- Use PUT method for updating -->

                <!-- Kms Bound Limit -->
                <div class="col-md-6">
                    <label for="km_bound" class="form-label">Kms Bound Limit (Collection)</label>
                    <input type="number" class="form-control @error('km_bound') is-invalid @enderror" id="km_bound" name="km_bound"   step="any" value="{{ old('km_bound', $entity->km_bound ?? '') }}" required>
                    @error('km_bound')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Kms Bound Limit for Sourcing -->
                <div class="col-md-6">
                    <label for="km_bound_sourcing" class="form-label">Kms Bound Limit (Sourcing)</label>
                    <input type="number"
                          class="form-control @error('km_bound_sourcing') is-invalid @enderror"
                          id="km_bound_sourcing"
                          name="km_bound_sourcing"
                          step="any"
                          value="{{ old('km_bound_sourcing', $entity->km_bound_sourcing ?? '') }}">
                    @error('km_bound_sourcing')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Location Enabled -->
                <div class="col-md-6">
                    <label for="location_enabled" class="form-label">Location Enabled?</label>
                    <select class="form-select @error('location_enabled') is-invalid @enderror" id="location_enabled" name="location_enabled" required>
                        <option value="" disabled {{ is_null(old('location_enabled', $entity->location_enabled)) ? 'selected' : '' }}>Select an option</option>
                        <option value="yes" {{ old('location_enabled', $entity->location_enabled) === 'yes' ? 'selected' : '' }}>Yes</option>
                        <option value="no" {{ old('location_enabled', $entity->location_enabled) === 'no' ? 'selected' : '' }}>No</option>
                    </select>
                    @error('location_enabled')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Modified By (Hidden or Pre-filled) -->
                <input type="hidden" name="modified_by" value="{{ Auth::user()->id ?? '' }}">

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
            </form><!-- End Entity Feature Settings Form -->

          

          </div>
        </div>

      </div>
    </div>
    
  </section>

@endsection
