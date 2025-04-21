@extends('include.dashboardLayout')

@section('title', 'Upload Invalid Results')

@section('content')
<div class="pagetitle">
    <h1>Upload Invalid Results</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Upload Invalid Results</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Upload Invalid Results File</h5>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('invalid-results.process') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="invalidFile" class="form-label">Invalid Results File</label>
                        <input type="file" class="form-control" id="invalidFile" name="invalidFile" accept=".xlsx,.xls,.csv" required>
                        <div class="invalid-feedback">Please select an invalid results file.</div>
                        <div class="form-text">Supported formats: Excel (.xlsx, .xls) or CSV files</div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i> Process Invalid Results
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection 