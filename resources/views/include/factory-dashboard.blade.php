<!-- resources/views/dashboard.blade.php -->

@extends('include.dashboardLayout')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Page Title -->
    <div class="page-header">
        <div class="header-wrapper">
            <h1>Dashboard</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="filter-dropdown">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <span id="selectedFilter">All</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" onclick="updateFilter('This Month')">This Month</a></li>
                        <li><a class="dropdown-item" onclick="updateFilter('Last 3 Months')">Last 3 Months</a></li>
                        <li><a class="dropdown-item" onclick="updateFilter('Last 6 Months')">Last 6 Months</a></li>
                        <li><a class="dropdown-item" onclick="updateFilter('Last 12 Months')">Last 12 Months</a></li>
                        <li><a class="dropdown-item active" onclick="updateFilter('All')">All</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="dashboard-content">

        <!-- Process Flow Visualization -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card process-flow-card">
                        <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="section-title mb-0"><i class="bi bi-diagram-3"></i> Plasma Processing Pipeline</h5>
                            <span class="badge bg-soft-primary text-primary px-2 py-1" style="font-size: 0.688rem;">Live Status</span>
                        </div>
                        <div class="process-flow-container">
                            <!-- Step 1: Collection -->
                            <div class="process-step" id="step-collection">
                                <div class="step-icon bg-primary-gradient">
                                    <i class="bi bi-droplet-fill"></i>
                                </div>
                                <div class="step-content">
                                    <h6 class="step-title">Plasma Received</h6>
                                    <div class="step-count" id="flow-collection-count">0L</div>
                                    <p class="step-label">Liters</p>
                                </div>
                                <div class="step-arrow">
                                    <i class="bi bi-arrow-right"></i>
                            </div>
                                </div>

                            <!-- Step 2: Tail Cutting -->
                            <div class="process-step" id="step-cutting">
                                <div class="step-icon bg-success-gradient">
                                    <i class="bi bi-scissors"></i>
                            </div>
                                <div class="step-content">
                                    <h6 class="step-title">Tail Cut & Pooled</h6>
                                    <div class="step-count" id="flow-cutting-count">0L</div>
                                    <p class="step-label">Liters</p>
                                </div>
                                <div class="step-arrow">
                                    <i class="bi bi-arrow-right"></i>
                    </div>
                            </div>

                            <!-- Step 3: ELISA Testing -->
                            <div class="process-step" id="step-elisa">
                                <div class="step-icon bg-info-gradient">
                                    <i class="bi bi-clipboard2-pulse"></i>
                                </div>
                                <div class="step-content">
                                    <h6 class="step-title">ELISA Tested</h6>
                                    <div class="step-count" id="flow-elisa-count">0</div>
                                    <p class="step-label">Tests</p>
                                </div>
                                <div class="step-arrow">
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                            </div>

                            <!-- Step 4: NAT Testing -->
                            <div class="process-step" id="step-nat">
                                <div class="step-icon bg-cyan-gradient">
                                    <i class="bi bi-activity"></i>
                                </div>
                                <div class="step-content">
                                    <h6 class="step-title">NAT Tested</h6>
                                    <div class="step-count" id="flow-nat-count">0</div>
                                    <p class="step-label">Tests</p>
                                </div>
                                <div class="step-arrow">
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                            </div>

                            <!-- Step 5: Released -->
                            <div class="process-step" id="step-released">
                                <div class="step-icon bg-warning-gradient">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div class="step-content">
                                    <h6 class="step-title">Released</h6>
                                    <div class="step-count" id="flow-released-count">0L</div>
                                    <p class="step-label">Liters</p>
                                </div>
                                <div class="step-arrow">
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                            </div>

                            <!-- Step 6: Dispensed -->
                            <div class="process-step" id="step-dispensed">
                                <div class="step-icon bg-teal-gradient">
                                    <i class="bi bi-box-arrow-right"></i>
                                </div>
                                <div class="step-content">
                                    <h6 class="step-title">Dispensed</h6>
                                    <div class="step-count" id="flow-dispensed-count">0L</div>
                                    <p class="step-label">Liters</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card quick-actions-card">
                    <div class="card-body">
                        <h5 class="section-title mb-2"><i class="bi bi-lightning-charge"></i> Quick Actions</h5>
                        <div class="quick-actions-grid">
                            <a href="{{ route('newBag.index') }}" class="action-btn action-btn-primary">
                                <i class="bi bi-plus-circle"></i>
                                <span>New Bag Entry</span>
                            </a>
                            <a href="{{ route('report.upload') }}" class="action-btn action-btn-info">
                                <i class="bi bi-upload"></i>
                                <span>Upload ELISA</span>
                            </a>
                            <a href="{{ route('nat-report.index') }}" class="action-btn action-btn-cyan">
                                <i class="bi bi-upload"></i>
                                <span>Upload NAT</span>
                            </a>
                            <a href="{{ route('factory.report.plasma_release') }}" class="action-btn action-btn-success">
                                <i class="bi bi-check-circle"></i>
                                <span>Plasma Release</span>
                            </a>
                            <a href="{{ route('factory.report.plasma_rejection') }}" class="action-btn action-btn-danger">
                                <i class="bi bi-x-circle"></i>
                                <span>Plasma Rejection</span>
                            </a>
                            <a href="{{ route('factory.generate_report.mega_pool_mini_pool') }}" class="action-btn action-btn-warning">
                                <i class="bi bi-file-earmark-text"></i>
                                <span>Generate Reports</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            </div>

        <!-- Pending Actions Alert Section -->
        <div class="row g-3 action-required-section" id="pending-actions-section">
            <div class="col-12">
                <div class="action-required-header">
                    <h5 class="section-title-alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Action Required
                        <span class="pulse-dot"></span>
                    </h5>
                </div>
            </div>

            <!-- Pending Tail Cutting -->
            <div class="col-md-6 col-xl-3">
                <div class="card alert-action-card alert-card-warning">
                        <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="alert-icon bg-warning-gradient">
                                    <i class="bi bi-scissors"></i>
                                </div>
                            <span class="alert-badge badge-warning">Pending</span>
                                </div>
                        <h6 class="alert-title">Pending Tail Cutting</h6>
                        <div class="alert-count" id="pending-cutting-count">0</div>
                        <p class="alert-description">Bags awaiting tail cutting</p>
                        {{-- <a href="{{ route('warehouse.index') }}" class="alert-action-link">
                            View Details <i class="bi bi-arrow-right"></i>
                        </a> --}}
                            </div>
                                </div>
                            </div>

            <!-- Reactive Mini Pools -->
            <div class="col-md-6 col-xl-3">
                <div class="card alert-action-card alert-card-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="alert-icon bg-danger-gradient">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                            <span class="alert-badge badge-danger">Urgent</span>
                    </div>
                        <h6 class="alert-title">Reactive Mini Pools</h6>
                        <div class="alert-count" id="reactive-pools-count">0</div>
                        <p class="alert-description">Need sub-pool testing</p>
                        {{-- <a href="{{ route('factory.report.sub_minipool_entry') }}" class="alert-action-link">
                            Take Action <i class="bi bi-arrow-right"></i>
                        </a> --}}
                    </div>
                </div>
            </div>

            <!-- Pending AR Numbers -->
            <div class="col-md-6 col-xl-3">
                <div class="card alert-action-card alert-card-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="alert-icon bg-info-gradient">
                                <i class="bi bi-file-earmark-check"></i>
                            </div>
                            <span class="alert-badge badge-info">Review</span>
                        </div>
                        <h6 class="alert-title">Pending Release</h6>
                        <div class="alert-count" id="pending-release-count">0</div>
                        <p class="alert-description">AR numbers for approval</p>
                        {{-- <a href="{{ route('factory.report.plasma_release') }}" class="alert-action-link">
                            Review Now <i class="bi bi-arrow-right"></i>
                        </a> --}}
                    </div>
                </div>
            </div>

            <!-- Test Results Entry -->
            <div class="col-md-6 col-xl-3">
                <div class="card alert-action-card alert-card-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="alert-icon bg-primary-gradient">
                                <i class="bi bi-clipboard-data"></i>
                            </div>
                            <span class="alert-badge badge-primary">Data Entry</span>
                        </div>
                        <h6 class="alert-title">Awaiting Results</h6>
                        <div class="alert-count" id="pending-results-count">0</div>
                        <p class="alert-description">Test results to enter</p>
                        {{-- <a href="{{ route('report.upload') }}" class="alert-action-link">
                            Enter Results <i class="bi bi-arrow-right"></i>
                        </a> --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- Quality Metrics Section -->
        <div class="row g-3 ">
            <div class="col-12">
                <div class="quality-metrics-header">
                    <h5 class="section-title"><i class="bi bi-shield-check"></i> Quality Testing Overview</h5>
                </div>
            </div>

            <!-- ELISA Quality Card -->
            <div class="col-md-6 col-xl-3">
                <div class="card stat-card" id="elisa-quality">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="stat-icon-box bg-primary-gradient">
                                <i class="bi bi-clipboard2-pulse"></i>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">View Details</a></li>
                                    <li><a class="dropdown-item" href="#">Export Data</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="stat-content mt-4">
                            <div class="count-display mb-2">
                                <span class="count-value" id="elisa-non-reactive-display">0</span>
                                <span class="count-separator">/</span>
                                <span class="count-total" id="elisa-total-display">0</span>
                            </div>
                            <p class="stat-label mb-2">ELISA Test (Non-Reactive/Total)</p>
                            <div class="stat-trend positive">
                                <i class="bi bi-check-circle"></i>
                                <span id="elisa-pass-rate">0%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NAT Quality Card -->
            <div class="col-md-6 col-xl-3">
                <div class="card stat-card" id="nat-quality">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                            <div class="stat-icon-box bg-info-gradient">
                                <i class="bi bi-activity"></i>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View Details</a></li>
                                        <li><a class="dropdown-item" href="#">Export Data</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="stat-content mt-4">
                            <div class="count-display mb-2">
                                <span class="count-value" id="nat-non-reactive-display">0</span>
                                <span class="count-separator">/</span>
                                <span class="count-total" id="nat-total-display">0</span>
                                </div>
                            <p class="stat-label mb-2">NAT Test (Non-Reactive/Total)</p>
                            <div class="stat-trend positive">
                                <i class="bi bi-check-circle"></i>
                                <span id="nat-pass-rate">0%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ELISA Reactive Count -->
                    <div class="col-md-6 col-xl-3">
                <div class="card stat-card" id="elisa-reactive-alert">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                            <div class="stat-icon-box bg-danger-gradient">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                    </div>
                            <div class="dropdown">
                                <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">View Details</a></li>
                                    <li><a class="dropdown-item" href="#">Export Data</a></li>
                                </ul>
                                    </div>
                                </div>
                                <div class="stat-content mt-4">
                            <h3 class="stat-value mb-2" id="elisa-reactive-alert-count">0</h3>
                            <p class="stat-label mb-2">ELISA Reactive</p>
                            <div class="stat-trend negative">
                                <i class="bi bi-arrow-right-circle"></i>
                                <span>Sub-Pool Testing</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

            <!-- NAT Reactive Count -->
                    <div class="col-md-6 col-xl-3">
                <div class="card stat-card" id="nat-reactive-alert">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                            <div class="stat-icon-box bg-warning-gradient">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                    </div>
                            <div class="dropdown">
                                <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">View Details</a></li>
                                    <li><a class="dropdown-item" href="#">Export Data</a></li>
                                </ul>
                                    </div>
                                </div>
                                <div class="stat-content mt-4">
                            <h3 class="stat-value mb-2" id="nat-reactive-alert-count">0</h3>
                            <p class="stat-label mb-2">NAT Reactive</p>
                            <div class="stat-trend negative">
                                <i class="bi bi-arrow-right-circle"></i>
                                <span>Requires Review</span>
                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

        <!-- Primary Stats Section - Always Visible -->
        <div class="row g-3">
            <div class="col-12">
                <div class="quality-metrics-header">
                    <h5 class="section-title"><i class="bi bi-graph-up-arrow"></i> Plasma Collection & Processing</h5>
                </div>
            </div>
            <!-- Plasma Collection Card -->
                    <div class="col-md-6 col-xl-3">
                <a href="{{ route('bloodbank.index') }}" class="card-link">
                    <div class="card stat-card" id="total-collections">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                <div class="stat-icon-box bg-primary-gradient">
                                    <i class="bi bi-droplet-fill"></i>
                                    </div>
                                <div class="dropdown">
                                    <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View Details</a></li>
                                        <li><a class="dropdown-item" href="#">Export Data</a></li>
                                    </ul>
                                    </div>
                                </div>
                            <div class="stat-content mt-4">
                                <h3 class="stat-value mb-2">0</h3>
                                <p class="stat-label mb-2">Total Plasma Inwards (Liters)</p>
                                <div class="stat-trend positive">
                                    <i class="bi bi-arrow-up"></i>
                                    {{-- <span>12.5% increase</span> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                </a>
                    </div>

            <!-- Tail Cutting Card -->
                    <div class="col-md-6 col-xl-3">
                <a href="{{ route('warehouse.index') }}" class="card-link">
                    <div class="card stat-card" id="tail-cutting">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                <div class="stat-icon-box bg-success-gradient">
                                    <i class="bi bi-scissors"></i>
                                    </div>
                                <div class="dropdown">
                                    <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View Details</a></li>
                                        <li><a class="dropdown-item" href="#">Export Data</a></li>
                                    </ul>
                                    </div>
                                </div>
                            <div class="stat-content mt-4">
                                <h3 class="stat-value mb-2">0</h3>
                                <p class="stat-label mb-2">Total Plasma Cuttings (Liters)</p>
                                <div class="stat-trend positive">
                                    <i class="bi bi-arrow-up"></i>
                                    {{-- <span>8.2% increase</span> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                </a>
                    </div>

            <!-- Plasma Approved Card -->
                    <div class="col-md-6 col-xl-3">
                <div class="card stat-card" id="plasma-approved">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                            <div class="stat-icon-box bg-warning-gradient">
                                <i class="bi bi-check-circle-fill"></i>
                                    </div>
                            <div class="dropdown">
                                <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">View Details</a></li>
                                    <li><a class="dropdown-item" href="#">Export Data</a></li>
                                </ul>
                                    </div>
                                </div>
                                <div class="stat-content mt-4">
                                    <h3 class="stat-value mb-2">0</h3>
                            <p class="stat-label mb-2">Plasma Approved</p>
                            <div class="stat-trend positive">
                                <i class="bi bi-arrow-up"></i>
                                <span>15.3% increase</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

            <!-- Plasma Dispensed Card -->
                    <div class="col-md-6 col-xl-3">
                <a href="{{ route('factory.generate_report.plasma_dispensing') }}" class="card-link">
                    <div class="card stat-card" id="plasma-dispensed">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                <div class="stat-icon-box bg-danger-gradient">
                                    <i class="bi bi-box-arrow-right"></i>
                                    </div>
                                <div class="dropdown">
                                    <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View Details</a></li>
                                        <li><a class="dropdown-item" href="#">Export Data</a></li>
                                    </ul>
                                    </div>
                                </div>
                            <div class="stat-content mt-4">
                                <h3 class="stat-value mb-2">0</h3>
                                <p class="stat-label mb-2">Plasma Dispensed (Liters)</p>
                                <div class="stat-trend negative">
                                    <i class="bi bi-arrow-down"></i>
                                    <span>3.2% decrease</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                </a>
            </div>
        </div>

        <!-- Charts and Analytics -->
        {{-- <div class="row mt-4">
            <!-- Main Analytics -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-white border-bottom-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Collection Analytics</h5>
                            <select class="form-select form-select-sm w-auto" onchange="updateAnalytics(this.value)">
                                <option value="7">Last 7 Days</option>
                                <option value="30" selected>Last 30 Days</option>
                                <option value="90">Last 90 Days</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Quick Stats -->
                        <div class="row mb-4">
                            <div class="col-sm-4">
                                <div class="quick-stat">
                                    <div class="stat-label">Total Collections</div>
                                    <div class="stat-value">2,458</div>
                                    <div class="stat-change up">+12.5% from last period</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="quick-stat">
                                    <div class="stat-label">Average Daily</div>
                                    <div class="stat-value">82</div>
                                    <div class="stat-change up">+5.2% from last period</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="quick-stat">
                                    <div class="stat-label">Success Rate</div>
                                    <div class="stat-value">94.8%</div>
                                    <div class="stat-change down">-1.3% from last period</div>
                                </div>
                            </div>
                        </div>

                        <!-- Main Chart -->
                        <div id="reportsChart"></div>
                    </div>
                </div>
            </div>

            <!-- Performance Rankings -->

            </div>
        </div> --}}
    </div>
</div>
@endsection

@push('styles')
<style>
/* Base Styles */
.dashboard-container {
    padding: 1rem;
    background-color: #f8f9fa;
    background-image:
        radial-gradient(at 90% 10%, rgb(65, 84, 241, 0.1) 0px, transparent 50%),
        radial-gradient(at 10% 90%, rgb(46, 202, 106, 0.1) 0px, transparent 50%);
    min-height: 100vh;
}

/* Section Headers */
.quality-metrics-header {
    margin-bottom: 0.5rem;
}

.section-title {
    font-size: 0.938rem;
    font-weight: 600;
    color: #2c3345;
    margin: 0;
    padding: 0.25rem 0;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.section-title i {
    font-size: 1rem;
    color: #4154f1;
}

/* Quality Metric Cards */
.quality-metric-card {
    background: white;
    border: none;
    border-radius: 1rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    overflow: hidden;
    height: 100%;
}

.quality-metric-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.quality-metric-card .metric-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.quality-metric-card .metric-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.quality-metric-card .metric-counts {
    margin: 1.5rem 0;
}

.quality-metric-card .count-display {
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.quality-metric-card .count-value {
    font-size: 2.25rem;
    font-weight: 700;
    color: #2eca6a;
    line-height: 1;
}

.quality-metric-card .count-separator {
    font-size: 1.75rem;
    font-weight: 400;
    color: #6c757d;
}

.quality-metric-card .count-total {
    font-size: 1.75rem;
    font-weight: 600;
    color: #2c3345;
}

.quality-metric-card .count-display-large {
    text-align: center;
    margin-bottom: 0.5rem;
}

.quality-metric-card .count-value-large {
    font-size: 3rem;
    font-weight: 700;
    color: #dc3545;
    line-height: 1;
}

.quality-metric-card.alert-card .count-value-large {
    color: #dc3545;
}

.quality-metric-card .count-label {
    font-size: 0.813rem;
    color: #6c757d;
    text-align: center;
    margin: 0;
}

.quality-metric-card .metric-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 1rem;
    border-top: 1px solid #f0f0f0;
}

.quality-metric-card .pass-rate {
    font-size: 1.25rem;
    font-weight: 700;
    color: #2eca6a;
}

.quality-metric-card .pass-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.quality-metric-card .action-label {
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Alert Card Specific Styles */
.quality-metric-card.alert-card {
    border-left: 4px solid #dc3545;
}

.quality-metric-card.alert-card:hover {
    border-left-color: #bb2d3b;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .quality-metric-card .count-value {
        font-size: 1.75rem;
    }

    .quality-metric-card .count-total {
        font-size: 1.5rem;
    }

    .quality-metric-card .count-value-large {
        font-size: 2.5rem;
    }
}

/* =================================================================
   PROCESS FLOW VISUALIZATION
   ================================================================= */
.process-flow-card {
    background: white;
    border: none;
    border-radius: 0.875rem;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.process-flow-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    padding: 0.75rem 0;
    overflow-x: auto;
}

.process-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.375rem;
    position: relative;
    min-width: 90px;
    flex: 1;
}

.process-step .step-icon {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.process-step:hover .step-icon {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

.process-step .step-content {
    text-align: center;
}

.process-step .step-title {
    font-size: 0.688rem;
    font-weight: 600;
    color: #6c757d;
    margin: 0 0 0.125rem 0;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.process-step .step-count {
    font-size: 1.375rem;
    font-weight: 700;
    color: #2c3345;
    line-height: 1;
    margin-bottom: 0.1rem;
}

.process-step .step-label {
    font-size: 0.563rem;
    color: #6c757d;
    margin: 0;
}

.process-step .step-arrow {
    position: absolute;
    right: -22px;
    top: 16px;
    font-size: 1.125rem;
    color: #dee2e6;
    transition: color 0.3s ease;
}

.process-step:hover .step-arrow {
    color: #4154f1;
}

.process-step:last-child .step-arrow {
    display: none;
}

/* =================================================================
   QUICK ACTIONS SECTION
   ================================================================= */
.quick-actions-card {
    background: white;
    border: none;
    border-radius: 0.875rem;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 0.75rem;
}

.action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
    padding: 1rem 0.75rem;
    border-radius: 0.625rem;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0.1;
    transition: opacity 0.3s ease;
}

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.action-btn i {
    font-size: 1.5rem;
    transition: transform 0.3s ease;
}

.action-btn:hover i {
    transform: scale(1.1);
}

.action-btn span {
    font-size: 0.813rem;
    font-weight: 600;
    text-align: center;
    line-height: 1.2;
}

/* Action Button Variants */
.action-btn-primary {
    background: rgba(65, 84, 241, 0.1);
    color: #4154f1;
    border-color: rgba(65, 84, 241, 0.2);
}

.action-btn-primary:hover {
    background: rgba(65, 84, 241, 0.15);
    border-color: #4154f1;
    color: #4154f1;
}

.action-btn-info {
    background: rgba(13, 202, 240, 0.1);
    color: #0dcaf0;
    border-color: rgba(13, 202, 240, 0.2);
}

.action-btn-info:hover {
    background: rgba(13, 202, 240, 0.15);
    border-color: #0dcaf0;
    color: #0dcaf0;
}

.action-btn-cyan {
    background: rgba(13, 202, 240, 0.1);
    color: #0aa2c0;
    border-color: rgba(13, 202, 240, 0.2);
}

.action-btn-cyan:hover {
    background: rgba(13, 202, 240, 0.15);
    border-color: #0aa2c0;
    color: #0aa2c0;
}

.action-btn-success {
    background: rgba(46, 202, 106, 0.1);
    color: #2eca6a;
    border-color: rgba(46, 202, 106, 0.2);
}

.action-btn-success:hover {
    background: rgba(46, 202, 106, 0.15);
    border-color: #2eca6a;
    color: #2eca6a;
}

.action-btn-danger {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    border-color: rgba(220, 53, 69, 0.2);
}

.action-btn-danger:hover {
    background: rgba(220, 53, 69, 0.15);
    border-color: #dc3545;
    color: #dc3545;
}

.action-btn-warning {
    background: rgba(255, 119, 29, 0.1);
    color: #ff771d;
    border-color: rgba(255, 119, 29, 0.2);
}

.action-btn-warning:hover {
    background: rgba(255, 119, 29, 0.15);
    border-color: #ff771d;
    color: #ff771d;
}

/* =================================================================
   ALERT ACTION CARDS
   ================================================================= */
.alert-action-card {
    background: white;
    border: none;
    border-radius: 0.875rem;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    overflow: hidden;
    border-left: 3px solid transparent;
}

.alert-action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
}

.alert-action-card .alert-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.alert-action-card .alert-badge {
    font-size: 0.625rem;
    font-weight: 600;
    padding: 0.3rem 0.625rem;
    border-radius: 0.375rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.alert-action-card .alert-title {
    font-size: 0.813rem;
    font-weight: 600;
    color: #6c757d;
    margin: 0.75rem 0 0.375rem 0;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.alert-action-card .alert-count {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    margin: 0.5rem 0;
}

.alert-action-card .alert-description {
    font-size: 0.75rem;
    color: #6c757d;
    margin: 0 0 0.75rem 0;
}

.alert-action-card .alert-action-link {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.813rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.alert-action-card .alert-action-link:hover {
    gap: 0.625rem;
}

.alert-action-card .alert-action-link i {
    transition: transform 0.3s ease;
}

.alert-action-card .alert-action-link:hover i {
    transform: translateX(3px);
}

/* Alert Card Variants */
.alert-card-warning {
    border-left-color: #ff771d;
}

.alert-card-warning .alert-count {
    color: #ff771d;
}

.alert-card-warning .alert-action-link {
    color: #ff771d;
}

.alert-card-warning .badge-warning {
    background: rgba(255, 119, 29, 0.1);
    color: #ff771d;
}

.alert-card-danger {
    border-left-color: #dc3545;
}

.alert-card-danger .alert-count {
    color: #dc3545;
}

.alert-card-danger .alert-action-link {
    color: #dc3545;
}

.alert-card-danger .badge-danger {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.alert-card-info {
    border-left-color: #0dcaf0;
}

.alert-card-info .alert-count {
    color: #0dcaf0;
}

.alert-card-info .alert-action-link {
    color: #0dcaf0;
}

.alert-card-info .badge-info {
    background: rgba(13, 202, 240, 0.1);
    color: #0dcaf0;
}

.alert-card-primary {
    border-left-color: #4154f1;
}

.alert-card-primary .alert-count {
    color: #4154f1;
}

.alert-card-primary .alert-action-link {
    color: #4154f1;
}

.alert-card-primary .badge-primary {
    background: rgba(65, 84, 241, 0.1);
    color: #4154f1;
}

/* =================================================================
   RESPONSIVE DESIGN
   ================================================================= */
@media (max-width: 992px) {
    .process-flow-container {
        justify-content: flex-start;
        padding-bottom: 1rem;
    }

    .process-step {
        min-width: 100px;
    }

    .process-step .step-arrow {
        right: -25px;
    }
}

@media (max-width: 768px) {
    .process-flow-container {
        gap: 0.5rem;
    }

    .process-step {
        min-width: 80px;
    }

    .process-step .step-icon {
        width: 48px;
        height: 48px;
        font-size: 1.25rem;
    }

    .process-step .step-count {
        font-size: 1.5rem;
    }

    .process-step .step-arrow {
        right: -20px;
        font-size: 1.25rem;
    }

    .quick-actions-grid {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    }

    .action-btn {
        padding: 1rem 0.75rem;
    }

    .action-btn i {
        font-size: 1.5rem;
    }

    .action-btn span {
        font-size: 0.75rem;
    }

    .alert-action-card .alert-count {
        font-size: 2rem;
    }
}

/* Header Styles */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding: 0.875rem 1rem;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 0.875rem;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
}

.header-wrapper h1 {
    font-size: 1.5rem;
    color: #2c3345;
    margin-bottom: 0.375rem;
    position: relative;
}

.header-wrapper h1::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -8px;
    height: 3px;
    width: 40px;
    background: linear-gradient(to right, #4154f1, #2eca6a);
    border-radius: 2px;
}

.breadcrumb {
    margin-bottom: 0;
}

.filter-dropdown .btn {
    border: 1px solid rgba(65, 84, 241, 0.2);
    background: white;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.filter-dropdown .btn:hover {
    background: rgba(65, 84, 241, 0.05);
    border-color: rgba(65, 84, 241, 0.3);
}

/* Toggle Button Styles */
.toggle-stats {
    padding: 0.5rem 1rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.toggle-stats i {
    transition: transform 0.3s ease;
}

.toggle-stats[aria-expanded="true"] i {
    transform: rotate(180deg);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .d-flex.align-items-center.gap-3 {
        width: 100%;
        margin-top: 1rem;
    }

    .filter-dropdown {
        width: 100%;
    }

    .filter-dropdown .btn {
        width: 100%;
        text-align: left;
    }
}

/* Card Styles */
.dashboard-card {
    background: white;
    border: none;
    border-radius: 0.875rem;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.dashboard-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transform: translateX(-100%);
    transition: transform 0.6s ease;
}

.dashboard-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
}

.dashboard-card:hover::before {
    transform: translateX(100%);
}

.card-link {
    text-decoration: none;
    color: inherit;
}

.card-body {
    padding: 1.125rem;
    position: relative;
    z-index: 1;
}

.card-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.card-icon {
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 24px;
    color: #fff;
    position: relative;
    overflow: hidden;
}

.card-icon::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transform: translateX(-100%);
}

.dashboard-card:hover .card-icon::after {
    transform: translateX(100%);
    transition: transform 0.6s ease;
}

.card-icon.blue {
    background: linear-gradient(135deg, #4154f1, #2039c7);
}
.card-icon.green {
    background: linear-gradient(135deg, #2eca6a, #1b994f);
}
.card-icon.orange {
    background: linear-gradient(135deg, #ff771d, #e65c00);
}
.card-icon.purple {
    background: linear-gradient(135deg, #7928ca, #5b1e9a);
}

.card-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3345;
    line-height: 1.2;
    margin-bottom: 0.25rem;
}

.card-label {
    font-size: 0.813rem;
    color: #6c757d;
    font-weight: 500;
}

/* Chart Card Styles */
.card {
    border: none;
    border-radius: 1rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    background: white;
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

/* Performance List Styles */
.performance-list {
    margin-top: 1rem;
}

.performance-item {
    padding: 1rem;
    border-radius: 8px;
    background: rgba(65, 84, 241, 0.03);
    margin-bottom: 0.75rem;
    transition: all 0.3s ease;
}

.performance-item:hover {
    background: rgba(65, 84, 241, 0.06);
    transform: translateX(5px);
}

/* Loading Placeholder Styles */
.placeholder-content {
    padding: 0.75rem;
}

.placeholder-item {
    height: 1rem;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    border-radius: 6px;
    margin-bottom: 0.75rem;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Map Styles */
#collectionMap {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: inset 0 0 15px rgba(0, 0, 0, 0.1);
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Analytics Card Styles */
.analytics-card {
    background: white;
    border: none;
    border-radius: 1rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

.analytics-card .card-title {
    color: #2c3345;
    font-size: 1.1rem;
    font-weight: 600;
}

/* Chart Controls */
.chart-actions .btn-group {
    background: #f8f9fa;
    padding: 2px;
    border-radius: 8px;
}

.chart-actions .btn {
    font-size: 0.813rem;
    padding: 0.25rem 0.75rem;
    border: none;
}

.chart-actions .btn.active {
    background: #4154f1;
    color: white;
}

/* Chart Legend */
.chart-legend {
    padding: 0.5rem 0;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.legend-label {
    font-size: 0.813rem;
    color: #6c757d;
}

/* Chart Container */
.chart-container {
    position: relative;
    min-height: 300px;
}

.chart-loader {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 255, 255, 0.8);
    padding: 1rem;
    border-radius: 8px;
}

/* Metrics Styles */
.metric-item {
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.metric-label {
    font-size: 0.813rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.metric-value {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.metric-trend {
    font-size: 0.813rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
}

.metric-trend.positive {
    color: #2eca6a;
}

.metric-trend.negative {
    color: #dc3545;
}

/* Performance Card Styles */
.performance-card {
    background: white;
    border: none;
    border-radius: 1rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

.performance-item {
    padding: 1rem;
    border-radius: 8px;
    background: #f8f9fa;
    margin-bottom: 0.75rem;
    transition: all 0.3s ease;
}

.performance-item:hover {
    background: #f0f0f0;
    transform: translateX(5px);
}

.performance-rank {
    width: 28px;
    height: 28px;
    background: #4154f1;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
    margin-right: 1rem;
}

.performance-info {
    flex: 1;
}

.performance-info h6 {
    font-size: 0.875rem;
    color: #2c3345;
    margin: 0;
}

.performance-stats {
    display: flex;
    gap: 1rem;
    margin-top: 0.25rem;
}

.stat-item {
    font-size: 0.75rem;
    color: #6c757d;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.performance-trend .badge {
    font-weight: 500;
    padding: 0.25rem 0.5rem;
}

.bg-success-light {
    background: rgba(46, 202, 106, 0.1);
    color: #2eca6a;
}

.performance-progress {
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    margin-top: 0.5rem;
}

.performance-progress .progress-bar {
    background: #4154f1;
    border-radius: 3px;
}

.performance-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: #2c3345;
}

/* Table Styles */
.table {
    margin-bottom: 0;
}

.table > :not(caption) > * > * {
    padding: 0.75rem 0;
}

.table > thead {
    font-size: 0.875rem;
}

.bank-name {
    font-weight: 500;
    color: #2c3345;
}

.bank-rate {
    font-size: 0.813rem;
    color: #6c757d;
}

.collection-count {
    font-weight: 500;
    color: #2c3345;
}

.collection-trend {
    font-size: 0.813rem;
}

.collection-trend.up {
    color: #2eca6a;
}

.collection-trend.down {
    color: #dc3545;
}

.achieved-value {
    font-weight: 500;
    color: #2c3345;
}

.achieved-percent {
    font-size: 0.813rem;
    color: #6c757d;
}

/* Form Controls */
.form-select {
    border-color: #e5e9f2;
    font-size: 0.875rem;
}

.form-select:focus {
    border-color: #4154f1;
    box-shadow: none;
}

/* Chart Container */
#reportsChart {
    min-height: 300px;
}

/* Stat Card Styles */
.stat-card {
    background: white;
    border: none;
    border-radius: 0.875rem;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
}

.card-link {
    text-decoration: none;
    color: inherit;
}

/* Stat Icon Box */
.stat-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

/* Gradient Backgrounds */
.bg-primary-gradient {
    background: linear-gradient(135deg, #4154f1, #2039c7);
}

.bg-success-gradient {
    background: linear-gradient(135deg, #2eca6a, #1b994f);
}

.bg-warning-gradient {
    background: linear-gradient(135deg, #ff771d, #e65c00);
}

.bg-danger-gradient {
    background: linear-gradient(135deg, #dc3545, #b02a37);
}

.bg-info-gradient {
    background: linear-gradient(135deg, #0dcaf0, #0aa2c0);
}

.bg-purple-gradient {
    background: linear-gradient(135deg, #6f42c1, #533180);
}

.bg-orange-gradient {
    background: linear-gradient(135deg, #fd7e14, #ca6510);
}

.bg-teal-gradient {
    background: linear-gradient(135deg, #20c997, #198754);
}

.bg-indigo-gradient {
    background: linear-gradient(135deg, #6610f2, #520dc2);
}

.bg-cyan-gradient {
    background: linear-gradient(135deg, #0dcaf0, #0097b2);
}

/* Soft Background Colors */
.bg-soft-info {
    background-color: rgba(13, 202, 240, 0.1);
}

.bg-soft-purple {
    background-color: rgba(111, 66, 193, 0.1);
}

.bg-soft-orange {
    background-color: rgba(253, 126, 20, 0.1);
}

.bg-soft-teal {
    background-color: rgba(32, 201, 151, 0.1);
}

.bg-soft-indigo {
    background-color: rgba(102, 16, 242, 0.1);
}

.bg-soft-cyan {
    background-color: rgba(13, 202, 240, 0.1);
}

.bg-soft-primary {
    background-color: rgba(65, 84, 241, 0.1);
}

.bg-soft-success {
    background-color: rgba(46, 202, 106, 0.1);
}

.bg-soft-danger {
    background-color: rgba(220, 53, 69, 0.1);
}

.bg-soft-warning {
    background-color: rgba(255, 119, 29, 0.1);
}

.text-indigo {
    color: #6610f2;
}

.text-cyan {
    color: #0dcaf0;
}

.text-primary {
    color: #4154f1;
}

.text-success {
    color: #2eca6a;
}

.text-danger {
    color: #dc3545;
}

.text-warning {
    color: #ff771d;
}

/* Progress Bar Colors */
.progress-bar.bg-indigo {
    background-color: #6610f2;
}

.progress-bar.bg-cyan {
    background-color: #0dcaf0;
}

/* Stat Content */
.stat-content {
    margin-top: 1.125rem;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3345;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

/* Count Display for Quality Cards */
.stat-card .count-display {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.stat-card .count-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2eca6a;
    line-height: 1;
}

.stat-card .count-separator {
    font-size: 1.5rem;
    font-weight: 400;
    color: #6c757d;
}

.stat-card .count-total {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3345;
}

/* Trend Indicators */
.stat-trend {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.813rem;
    font-weight: 500;
}

.stat-trend.positive {
    color: #2eca6a;
}

.stat-trend.negative {
    color: #dc3545;
}

/* Progress Bars */
.stat-progress {
    height: 6px;
    background-color: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
    margin-top: 1rem;
}

.stat-progress .progress-bar {
    border-radius: 3px;
    transition: width 0.6s ease;
}

/* Badges */
.stat-badge .badge {
    padding: 0.5rem 0.75rem;
    font-weight: 500;
    border-radius: 6px;
}

.bg-soft-info {
    background-color: rgba(13, 202, 240, 0.1);
}

.bg-soft-purple {
    background-color: rgba(111, 66, 193, 0.1);
}

.bg-soft-orange {
    background-color: rgba(253, 126, 20, 0.1);
}

.bg-soft-teal {
    background-color: rgba(32, 201, 151, 0.1);
}

/* Dropdown Menu */
.btn-link {
    color: #6c757d;
    padding: 0;
    font-size: 1.25rem;
}

.btn-link:hover {
    color: #2c3345;
}

.dropdown-menu {
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    border-radius: 0.5rem;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

/* Responsive Adjustments */
@media (max-width: 1200px) {
    .stat-value {
        font-size: 1.5rem;
    }
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }

    .stat-card {
        margin-bottom: 1rem;
    }

    .stat-icon-box {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
    }
}

/* Secondary Stats Section Styles */
.secondary-stats {
    background: rgba(255, 255, 255, 0.5);
    border-radius: 1rem;
    padding: 1rem;
}

.section-header {
    padding: 0.5rem;
}

.section-header h5 {
    color: #2c3345;
    font-weight: 600;
}

/* Collapsible Section Styles */
.toggle-stats {
    color: #4154f1;
    text-decoration: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.toggle-stats i {
    transition: transform 0.3s ease;
}

.toggle-stats[aria-expanded="true"] i {
    transform: rotate(180deg);
}

/* Carousel Styles */
#statsCarousel {
    border-radius: 1rem;
    overflow: hidden;
}

.carousel-item {
    padding: 0.5rem;
}

.carousel-controls .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: white;
    border: 1px solid #e5e9f2;
    color: #2c3345;
    transition: all 0.3s ease;
}

.carousel-controls .btn:hover {
    background: #4154f1;
    border-color: #4154f1;
    color: white;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .secondary-stats {
        padding: 0.5rem;
    }

    .carousel-item {
        padding: 0.25rem;
    }
}

/* Collapse Animation */
.collapse:not(.show) {
    display: none !important;
}

#secondaryStats {
    width: 100%;
}

#secondaryStats .row {
    margin-top: 1.5rem;
}

#secondaryStats.collapsing {
    height: 0;
    overflow: hidden;
    display: none;
}

#secondaryStats.show {
    display: block;
}

#secondaryStats .col-md-6 {
    animation: fadeInUp 0.3s ease-out forwards;
    opacity: 0;
}

/* Ensure proper card spacing */
.row.g-3 {
    --bs-gutter-x: 1.5rem;
    --bs-gutter-y: 1.5rem;
    margin-right: calc(var(--bs-gutter-x) * -.5);
    margin-left: calc(var(--bs-gutter-x) * -.5);
}

.row.g-3 > * {
    padding-right: calc(var(--bs-gutter-x) * .5);
    padding-left: calc(var(--bs-gutter-x) * .5);
    margin-top: var(--bs-gutter-y);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Add animation delays for each card */
#secondaryStats .col-md-6:nth-child(1) { animation-delay: 0.1s; }
#secondaryStats .col-md-6:nth-child(2) { animation-delay: 0.2s; }
#secondaryStats .col-md-6:nth-child(3) { animation-delay: 0.3s; }
#secondaryStats .col-md-6:nth-child(4) { animation-delay: 0.4s; }
#secondaryStats .col-md-6:nth-child(5) { animation-delay: 0.5s; }
#secondaryStats .col-md-6:nth-child(6) { animation-delay: 0.6s; }

/* Section Container Styles */
.section-container {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.section-container .page-header {
    padding: 1rem 1.5rem;
    background: white;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    margin-bottom: 0;
}

.section-container .header-wrapper h2 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: #2c3345;
    position: relative;
}

.section-container .header-wrapper h2::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -8px;
    height: 3px;
    width: 40px;
    background: linear-gradient(to right, #4154f1, #2eca6a);
    border-radius: 2px;
}

/* Toggle Button Styles */
.toggle-stats {
    padding: 0.5rem 1rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.toggle-stats i {
    transition: transform 0.3s ease;
}

.toggle-stats[aria-expanded="true"] i {
    transform: rotate(180deg);
}

/* Carousel Controls */
.carousel-controls {
    display: flex;
    gap: 0.5rem;
}

.carousel-controls .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

/* Content Area */
.section-container .collapse {
    padding: 1.5rem;
    background: #f8f9fa;
}

#statsCarousel {
    padding: 1.5rem;
    background: #f8f9fa;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .section-container .page-header {
        padding: 1rem;
    }

    .section-container .collapse,
    #statsCarousel {
        padding: 1rem;
    }

    .section-container .header-wrapper h2 {
        font-size: 1.25rem;
    }
}
</style>
@endpush

@push('scripts')

<!-- Global function definitions: these are available to the Maps API callback -->
<script>
  // Define the collection map initialization function
  function initCollectionMap() {
    var collectionMapContainer = document.getElementById('collectionMap');
    if (!collectionMapContainer) {
      console.warn('Collection map container not found.');
      return;
    }

    var latInput = document.getElementById('collectionLatitude');
    var lngInput = document.getElementById('collectionLongitude');
    if (!latInput || !lngInput || !latInput.value || !lngInput.value) {
      console.log("No collection latitude/longitude data available.");
      return;
    }

    var latitude = parseFloat(latInput.value);
    var longitude = parseFloat(lngInput.value);
    var title = document.getElementById('collectionMapTitle') ? document.getElementById('collectionMapTitle').value : 'DCR Location';

    var map = new google.maps.Map(collectionMapContainer, {
        center: { lat: latitude, lng: longitude },
        zoom: 15
    });

    var marker = new google.maps.Marker({
        position: { lat: latitude, lng: longitude },
        map: map,
        title: title,
        icon: {
          url: "{{ asset('assets/img/location.png') }}",
          scaledSize: new google.maps.Size(50, 50) // Adjust the width and height as needed
        }
    });

    var infoWindow = new google.maps.InfoWindow({
        content: `<strong>${title}</strong>`
    });

    marker.addListener('click', function() {
        infoWindow.open(map, marker);
    });

    infoWindow.open(map, marker);
  }

  // Expose initMap globally so that the Maps API callback finds it
  window.initMap = function() {
    initCollectionMap();
  };

  // Expose updateFilter globally if needed in inline HTML onclick attributes
  window.updateFilter = function(value) {
    console.log('updateFilter', value);
    document.getElementById('selectedFilter').innerText = value;
    document.querySelectorAll('.dropdown-item').forEach(function(item) {
      item.classList.remove('active');
      if (item.innerText.trim() === value) {
        item.classList.add('active');
      }
    });

    // Re-call all dashboard loader functions with the new filter value.
    window.loadDashboardData(value);
    window.loadDashboardGraphData(value);
    window.loadDashboardBloodBankMap(value);
  };
</script>

    <script>

        $(document).ready(function() {

            // Initially hide the Blood Banks Map section (second row)
            $("#bloodBankMapsSection").hide();

            // When the Collected Plasma card is clicked...
            $(".collected-plasma-card").on('click', function() {
              // Show the entire second row
              $("#bloodBankMapsSection").show();
              // Make the map container full width (col-lg-12) and hide details column
              $("#bloodBankMapsSection .map-container")
                .removeClass("col-lg-8")
                .addClass("col-lg-12");
              $("#bloodBankMapsSection .details-container").hide();
            });

               // Define the dashboard loader functions with an optional filter parameter.
              function loadDashboardData(filter = 'This Month') {
                $.ajax({
                    url: "{{ route('dashboard.getFactoryDashboardData') }}",
                    type: 'GET',
                    data: { filter: filter },
                    success: function(response) {
                        if(response.success) {
                            const data = response.data;

                            // Update Total Collections count
                            $(".card-value").first().text(data.total_collections ?? 0);

                            // Update Tail Cutting count
                            $(".card-value").eq(1).text(data.tail_cutting_count ?? 0);

                            // Update Plasma Approved count
                            $(".card-value").eq(2).text(data.approved_count ?? 0);

                            // Update Plasma Rejected count
                            $(".card-value").last().text(data.rejected_count ?? 0);
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching dashboard data:", error);
                        Swal.fire('Error', 'An error occurred while fetching dashboard data.', 'error');
                    }
                });
            }


             // Function to load dashboard graph data and render the Reports chart
             function loadDashboardGraphData(filter = 'This Month') {
                $.ajax({
                    url: "{{ route('dashboard.getDashboardGraphData') }}",
                    type: 'GET',
                    data: { filter: filter },
                    success: function(response) {
                        if(response.success) {
                            const data = response.data;
                            // Reverse the array if the API returns months in descending order so that the chart displays in chronological order
                            data.reverse();

                            // Prepare arrays for the chart series and x-axis labels
                            const months = data.map(item => item.month);
                            const bloodBanks = data.map(item => item.blood_bank_count);
                            const warehouses = data.map(item => item.warehouse_count);
                            const customers = data.map(item => item.customer_count);

                            // Render the ApexCharts Reports Chart with dynamic data
                            new ApexCharts(document.querySelector("#reportsChart"), {
                                series: [{
                                    name: 'Blood Banks',
                                    data: bloodBanks,
                                }, {
                                    name: 'Warehouses',
                                    data: warehouses,
                                }, {
                                    name: 'Customers',
                                    data: customers,
                                }],
                                chart: {
                                    height: 350,
                                    type: 'area',
                                    toolbar: {
                                        show: false
                                    },
                                },
                                markers: {
                                    size: 4
                                },
                                colors: ['#4154f1', '#2eca6a', '#ff771d'],
                                fill: {
                                    type: "gradient",
                                    gradient: {
                                        shadeIntensity: 1,
                                        opacityFrom: 0.3,
                                        opacityTo: 0.4,
                                        stops: [0, 90, 100]
                                    }
                                },
                                dataLabels: {
                                    enabled: false
                                },
                                stroke: {
                                    curve: 'smooth',
                                    width: 2
                                },
                                xaxis: {
                                    type: 'category',
                                    categories: months,
                                },
                                tooltip: {
                                    x: {
                                        format: 'yyyy-MM'
                                    },
                                }
                            }).render();
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching dashboard graph data:", error);
                        Swal.fire('Error', 'An error occurred while fetching dashboard graph data.', 'error');
                    }
                });
            }


             // Function to load dashboard blood bank mapview
             function loadDashboardBloodBankMap(filter = 'This Month') {
                $.ajax({
                    url: "{{ route('dashboard.getDashboardBloodBanksMapData') }}",
                    type: 'GET',
                    data: { filter: filter },
                    success: function(response) {
                        if(response.success) {
                            const data = response.data;
                            // response.data is expected to be an array of blood bank objects
                            const bloodBanksData = response.data;
                            initBloodBankMap(bloodBanksData);
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching dashboard graph data:", error);
                        Swal.fire('Error', 'An error occurred while fetching dashboard graph data.', 'error');
                    }
                });
            }


          // Attach loader functions to window object so that updateFilter can call them
          window.loadDashboardData = loadDashboardData;
          window.loadDashboardGraphData = loadDashboardGraphData;
          window.loadDashboardBloodBankMap = loadDashboardBloodBankMap;

          // Initially call the loader functions with default filter "This Month"
           loadDashboardData();
           loadDashboardGraphData();
           loadDashboardBloodBankMap();



          // Function to initialize the blood bank map using the data from the API
          function initBloodBankMap(bloodBanks) {
              if (!bloodBanks || bloodBanks.length === 0) {
                  console.warn('No blood bank data available.');
                  return;
              }

              // Center map on the first blood bank's location (or compute an average if you prefer)
              var firstBank = bloodBanks[0];
              var centerLat = parseFloat(firstBank.latitude) || 0;
              var centerLng = parseFloat(firstBank.longitude) || 0;

              // Create the map in the container (div id="collectionMap")
              var map = new google.maps.Map(document.getElementById('collectionMap'), {
                //  center: {lat: centerLat, lng: centerLng},
                  center: { lat: 0, lng: 0 },
                  zoom: 8 // This zoom value will be overridden by fitBounds.
              });

              // Create a LatLngBounds object to adjust the map view automatically.
              var bounds = new google.maps.LatLngBounds();


              // Create a single infoWindow to be re-used
              var infoWindow = new google.maps.InfoWindow();

              // Loop through blood banks and create markers
              bloodBanks.forEach(function(bank) {
                  var bankLat = parseFloat(bank.latitude);
                  var bankLng = parseFloat(bank.longitude);

                  // Create marker for current blood bank
                  var marker = new google.maps.Marker({
                      position: {lat: bankLat, lng: bankLng},
                      map: map,
                      title: bank.blood_bank_name,
                      icon: {
                        url: "{{ asset('assets/img/location.png') }}",
                        scaledSize: new google.maps.Size(50, 50) // Adjust the width and height as needed
                      }
                  });

                  // Extend the bounds to include this marker's location
                  bounds.extend(marker.getPosition());

                  // Setup click listener for marker
                  marker.addListener('click', function() {

                    // On marker click, adjust layout:
                    // Change map column to 8-wide and show the details column
                    $("#bloodBankMapsSection .map-container")
                      .removeClass("col-lg-12")
                      .addClass("col-lg-8");
                    $("#bloodBankMapsSection .details-container").show();

                      // Build content for the info window (a quick summary)
                      var infoContent = `
                          <div>
                              <strong>${bank.blood_bank_name}</strong><br>
                              ${bank.address ? bank.address : ''}<br>
                              ${bank.contact_person ? 'Contact: ' + bank.contact_person : ''}
                          </div>
                      `;
                      infoWindow.setContent(infoContent);
                      infoWindow.open(map, marker);

                      const fullAddress = [bank.address, bank.pincode, bank.city_name, bank.state_name, bank.country_name]
                                          .filter(Boolean)
                                          .join(', ');

                      // Build detailed content for the #bloodBankDetails div
                      var detailsContent = `
                          <h5 class="card-title">${bank.blood_bank_name}</h5>
                          <p><strong>Total Collections:</strong> ${bank.total_number_of_collections || 0}</p>
                          <p><strong>Total Collected Qty:</strong> ${bank.sum_of_available_quantity || 0}</p>
                           <p><strong>Contact Person:</strong> ${bank.contact_person ? bank.contact_person : '-'}</p>
                          <p><strong>Email:</strong> ${bank.email ? bank.email : '-'}</p>
                          <p><strong>Mobile:</strong> ${bank.mobile_no ? bank.mobile_no : '-'}</p>
                          <p><strong>Address:</strong> ${fullAddress}</p>
                          <h5 class="card-title">Latest Collection Details</h5>
                          `;


                      // Define an array of color classes to style each collection entry
                      const collectionColors = ["text-primary", "text-secondary",  "text-warning", "text-success", "text-danger"];

                      // Check if there are collection details to display
                      if (bank.collection_details && bank.collection_details.length > 0) {
                          bank.collection_details.forEach((collection, index) => {
                              let colorClass = collectionColors[index % collectionColors.length];

                              detailsContent += `
                                  <div class="activity-item d-flex mb-2">
                                      <div class="activite-label me-2">Collection ${index + 1}</div>
                                      <i class="bi bi-droplet-fill activity-badge ${colorClass} align-self-start me-2"></i>
                                      <div class="activity-content">
                                          <strong>Date:</strong> ${collection.start}<br>
                                          <strong>Executive:</strong> ${collection.extendedProps.collecting_agent_name || '-'}<br>
                                          <strong>Planned:</strong> ${collection.extendedProps.quantity || 0},
                                          <strong>Collected:</strong> ${collection.extendedProps.available_quantity || 0}<br>
                                           <strong>Warehouse:</strong> ${collection.extendedProps.transport_details.warehouse_name || '-'}<br>
                                          <strong>Plasma Price:</strong> ${collection.extendedProps.price || 0}<br>
                                          <strong>Part-A:</strong> ${collection.extendedProps.part_a_invoice_price || 0},
                                          <strong>Part-B:</strong> ${collection.extendedProps.part_b_invoice_price || 0},
                                          <strong>Part-C:</strong> ${collection.extendedProps.part_c_invoice_price || 0}<br>
                                          <strong>Total Invoice Price:</strong> ${collection.extendedProps.collection_total_plasma_price || 0}<br>
                                          <strong>Boxes/Units/Litres:</strong> ${collection.extendedProps.num_boxes || 0} /
                                          ${collection.extendedProps.num_units || 0} / ${collection.extendedProps.num_litres || 0}
                                      </div>
                                  </div>
                              `;
                          });
                      } else {
                          detailsContent += `<div class="activity-item">No collection details available.</div>`;
                      }


                      // Update the details section
                      document.getElementById('bloodBankDetails').innerHTML = detailsContent;
                  });
              });

              // Adjust the map's viewport to cover all markers
              map.fitBounds(bounds);

              // Optional: Set a maximum zoom level after fitting bounds (e.g., zoom level 15)
              var listener = google.maps.event.addListener(map, 'bounds_changed', function() {
                  if (map.getZoom() > 15) {
                      map.setZoom(15);
                  }
                  google.maps.event.removeListener(listener);
              });
          }

        });
    </script>


<!-- Load Google Maps JavaScript API using async & defer -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFwtHIaHQ1J8PKur9RmQy4Z5WsM6kVVPE&callback=initMap" async defer></script>


@endpush

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to load dashboard data and update all statistics
    function loadDashboardData(filter = 'All') {
        $.ajax({
            url: "{{ route('dashboard.getFactoryDashboardData') }}",
            type: 'GET',
            data: { filter: filter },
            success: function(response) {
                console.log('Dashboard Data Response:', response); // Debug log
                if(response.success) {
                    const data = response.data;

                    // Update Primary Stats
                    updatePrimaryStats(data);

                    // Load graph data
                    loadDashboardGraphData(filter);

                    // Load map data if container exists
                    if (document.getElementById('collectionMap')) {
                        loadDashboardBloodBankMap(filter);
                    }
                } else {
                    console.error("Error in response:", response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching dashboard data:", error);
                console.log("XHR Status:", status);
                console.log("XHR Response:", xhr.responseText);
            }
        });
    }

    // Function to update process flow
    function updateProcessFlow(data) {
        console.log('Updating Process Flow with data:', data);

        // Update process flow counts - showing liters with L suffix
        const formatLiters = (value) => {
            const num = parseFloat(value) || 0;
            return num.toFixed(2) + 'L';
        };

        document.getElementById('flow-collection-count').textContent = formatLiters(data.total_collections);
        document.getElementById('flow-cutting-count').textContent = formatLiters(data.tail_cutting_count);

        // Calculate test counts
        const elisaTotal = (data.elisa_non_reactive_count || 0) + (data.elisa_reactive_count || 0);
        const natTotal = (data.nat_non_reactive_count || 0) + (data.nat_reactive_count || 0);

        document.getElementById('flow-elisa-count').textContent = elisaTotal;
        document.getElementById('flow-nat-count').textContent = natTotal;
        document.getElementById('flow-released-count').textContent = formatLiters(data.approved_count);
        document.getElementById('flow-dispensed-count').textContent = formatLiters(data.dispensed_count);
    }

    // Function to update pending actions
    function updatePendingActions(data) {
        console.log('Updating Pending Actions with data:', data);

        // Update pending action counts
        document.getElementById('pending-cutting-count').textContent = data.pending_tail_cutting || 0;
        document.getElementById('reactive-pools-count').textContent = data.elisa_reactive_count || 0;
        document.getElementById('pending-release-count').textContent = data.pending_release || 0;
        document.getElementById('pending-results-count').textContent = data.pending_test_entry || 0;
    }

    // Function to update quality metrics
    function updateQualityMetrics(data) {
        console.log('Updating Quality Metrics with data:', data);

        // ELISA Quality Metrics
        const elisaNonReactive = data.elisa_non_reactive_count || 0;
        const elisaReactive = data.elisa_reactive_count || 0;
        const elisaTotal = elisaNonReactive + elisaReactive;
        const elisaPassRate = elisaTotal > 0 ? ((elisaNonReactive / elisaTotal) * 100).toFixed(1) : 0;

        document.getElementById('elisa-non-reactive-display').textContent = elisaNonReactive;
        document.getElementById('elisa-total-display').textContent = elisaTotal;
        document.getElementById('elisa-pass-rate').textContent = elisaPassRate + '%';
        document.getElementById('elisa-reactive-alert-count').textContent = elisaReactive;

        // NAT Quality Metrics
        const natNonReactive = data.nat_non_reactive_count || 0;
        const natReactive = data.nat_reactive_count || 0;
        const natTotal = natNonReactive + natReactive;
        const natPassRate = natTotal > 0 ? ((natNonReactive / natTotal) * 100).toFixed(1) : 0;

        document.getElementById('nat-non-reactive-display').textContent = natNonReactive;
        document.getElementById('nat-total-display').textContent = natTotal;
        document.getElementById('nat-pass-rate').textContent = natPassRate + '%';
        document.getElementById('nat-reactive-alert-count').textContent = natReactive;

        // Update pass rate colors and trend icons based on performance
        const elisaPassRateElement = document.getElementById('elisa-pass-rate');
        const elisaTrendDiv = document.querySelector('#elisa-quality .stat-trend');
        if (elisaPassRate >= 90) {
            elisaPassRateElement.style.color = '#2eca6a'; // Green
            if (elisaTrendDiv) {
                elisaTrendDiv.className = 'stat-trend positive';
                elisaTrendDiv.querySelector('i').className = 'bi bi-check-circle';
            }
        } else if (elisaPassRate >= 75) {
            elisaPassRateElement.style.color = '#ff771d'; // Orange
            if (elisaTrendDiv) {
                elisaTrendDiv.className = 'stat-trend negative';
                elisaTrendDiv.querySelector('i').className = 'bi bi-exclamation-triangle';
            }
        } else {
            elisaPassRateElement.style.color = '#dc3545'; // Red
            if (elisaTrendDiv) {
                elisaTrendDiv.className = 'stat-trend negative';
                elisaTrendDiv.querySelector('i').className = 'bi bi-x-circle';
            }
        }

        const natPassRateElement = document.getElementById('nat-pass-rate');
        const natTrendDiv = document.querySelector('#nat-quality .stat-trend');
        if (natPassRate >= 90) {
            natPassRateElement.style.color = '#2eca6a';
            if (natTrendDiv) {
                natTrendDiv.className = 'stat-trend positive';
                natTrendDiv.querySelector('i').className = 'bi bi-check-circle';
            }
        } else if (natPassRate >= 75) {
            natPassRateElement.style.color = '#ff771d';
            if (natTrendDiv) {
                natTrendDiv.className = 'stat-trend negative';
                natTrendDiv.querySelector('i').className = 'bi bi-exclamation-triangle';
            }
        } else {
            natPassRateElement.style.color = '#dc3545';
            if (natTrendDiv) {
                natTrendDiv.className = 'stat-trend negative';
                natTrendDiv.querySelector('i').className = 'bi bi-x-circle';
            }
        }
    }

    // Function to update primary statistics
    function updatePrimaryStats(data) {
        console.log('Updating Primary Stats with data:', data); // Debug log

        // Update all dashboard sections
        updateProcessFlow(data);
        updatePendingActions(data);
        updateQualityMetrics(data);

        // Format liters helper function
        const formatLiters = (value) => {
            const num = parseFloat(value) || 0;
            return num.toFixed(2);
        };

        // Total Collections
        const totalCollections = formatLiters(data.total_collections);
        const collectionsCard = document.querySelector('#total-collections .stat-value');
        if (collectionsCard) {
            collectionsCard.textContent = totalCollections;
            console.log('Updated Total Collections to:', totalCollections); // Debug log
        }

        // Tail Cutting
        const tailCutting = formatLiters(data.tail_cutting_count);
        const tailCuttingCard = document.querySelector('#tail-cutting .stat-value');
        if (tailCuttingCard) {
            tailCuttingCard.textContent = tailCutting;
            console.log('Updated Tail Cutting to:', tailCutting); // Debug log
        }

        // Plasma Approved
        const plasmaApproved = formatLiters(data.approved_count);
        const approvedCard = document.querySelector('#plasma-approved .stat-value');
        if (approvedCard) {
            approvedCard.textContent = plasmaApproved;
        }

        // Plasma Dispensed
        const plasmaDispensed = formatLiters(data.dispensed_count);
        const dispensedCard = document.querySelector('#plasma-dispensed .stat-value');
        if (dispensedCard) {
            dispensedCard.textContent = plasmaDispensed;
        }

        // Update trends if available
        updateTrend('total-collections', data.collections_trend);
        updateTrend('tail-cutting', data.tail_cutting_trend);
        updateTrend('plasma-approved', data.approved_trend);
        updateTrend('plasma-dispensed', data.dispensed_trend);
    }

    // Function to update trend indicators
    function updateTrend(cardId, trendValue) {
        const trendElement = document.querySelector(`#${cardId} .stat-trend`);
        if (trendElement && typeof trendValue !== 'undefined') {
            const isPositive = trendValue >= 0;
            const trendIcon = isPositive ? 'bi-arrow-up' : 'bi-arrow-down';
            const trendClass = isPositive ? 'positive' : 'negative';

            trendElement.className = `stat-trend ${trendClass}`;
            trendElement.innerHTML = `
                <i class="bi ${trendIcon}"></i>
                <span>${Math.abs(trendValue)}% ${isPositive ? 'increase' : 'decrease'}</span>
            `;
        }
    }


    // Helper function to update individual stat cards
    function updateStatCard(cardId, value, progress) {
        const card = document.getElementById(cardId);
        if (!card) return;

        const valueElement = card.querySelector('.stat-value');
        if (valueElement) {
            valueElement.textContent = value;
        }

        const progressBar = card.querySelector('.progress-bar');
        if (progressBar && typeof progress !== 'undefined') {
            progressBar.style.width = `${progress}%`;
        }
    }

    // Update filter function
    window.updateFilter = function(value) {
        console.log('Filter changed to:', value); // Debug log
        document.getElementById('selectedFilter').innerText = value;
        document.querySelectorAll('.dropdown-item').forEach(function(item) {
            item.classList.remove('active');
            if (item.innerText.trim() === value) {
                item.classList.add('active');
            }
        });

        // Load dashboard data with new filter
        loadDashboardData(value);
    };

    // Initialize dashboard with 'All' filter
    loadDashboardData('All');
});
</script>

