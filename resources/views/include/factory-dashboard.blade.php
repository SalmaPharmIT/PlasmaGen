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
            <!-- Desktop Toggle Button -->
            {{-- <div class="d-none d-md-block">
                <button class="btn btn-outline-primary toggle-stats collapsed" data-bs-toggle="collapse" data-bs-target="#secondaryStats" aria-expanded="false">
                    <i class="bi bi-chevron-down"></i> View Additional Statistics
                </button>
            </div> --}}
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
        <!-- Primary Stats Section - Always Visible -->
        <div class="row g-3 mb-4">
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
                                <p class="stat-label mb-2">Total Collections</p>
                                <div class="stat-trend positive">
                                    <i class="bi bi-arrow-up"></i>
                                    <span>12.5% increase</span>
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
                                <p class="stat-label mb-2">Total Tail Cuttings</p>
                                <div class="stat-trend positive">
                                    <i class="bi bi-arrow-up"></i>
                                    <span>8.2% increase</span>
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
                                <p class="stat-label mb-2">Plasma Dispensed</p>
                                <div class="stat-trend negative">
                                    <i class="bi bi-arrow-down"></i>
                                    <span>3.2% decrease</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Additional Statistics Cards - Collapsible -->
            <div class="collapse w-100" id="secondaryStats">
                <div class="row g-3">
                    <!-- Stock Received Card -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card" id="stock-received">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="stat-icon-box bg-info-gradient">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                    <div class="stat-badge">
                                        <span class="badge bg-soft-info text-info">Active</span>
                                    </div>
                                </div>
                                <div class="stat-content mt-4">
                                    <h3 class="stat-value mb-2">0</h3>
                                    <p class="stat-label mb-2">Stock Received</p>
                                    <div class="progress stat-progress">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 65%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Cutting Card -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card" id="pending-cutting">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="stat-icon-box bg-purple-gradient">
                                        <i class="bi bi-hourglass-split"></i>
                                    </div>
                                    <div class="stat-badge">
                                        <span class="badge bg-soft-purple text-purple">Pending</span>
                                    </div>
                                </div>
                                <div class="stat-content mt-4">
                                    <h3 class="stat-value mb-2">0</h3>
                                    <p class="stat-label mb-2">Pending Cutting</p>
                                    <div class="progress stat-progress">
                                        <div class="progress-bar bg-purple" role="progressbar" style="width: 45%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Under Test Stock Card -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card" id="under-test">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="stat-icon-box bg-orange-gradient">
                                        <i class="bi bi-clipboard-data"></i>
                                    </div>
                                    <div class="stat-badge">
                                        <span class="badge bg-soft-orange text-orange">Testing</span>
                                    </div>
                                </div>
                                <div class="stat-content mt-4">
                                    <h3 class="stat-value mb-2">0</h3>
                                    <p class="stat-label mb-2">Under Test Stock</p>
                                    <div class="progress stat-progress">
                                        <div class="progress-bar bg-orange" role="progressbar" style="width: 75%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resolution Test Card -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card" id="under-resolution">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="stat-icon-box bg-teal-gradient">
                                        <i class="bi bi-file-earmark-medical"></i>
                                    </div>
                                    <div class="stat-badge">
                                        <span class="badge bg-soft-teal text-teal">In Progress</span>
                                    </div>
                                </div>
                                <div class="stat-content mt-4">
                                    <h3 class="stat-value mb-2">0</h3>
                                    <p class="stat-label mb-2">Under Resolution Test</p>
                                    <div class="progress stat-progress">
                                        <div class="progress-bar bg-teal" role="progressbar" style="width: 55%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Release Qty Card -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card" id="release-qty">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="stat-icon-box bg-indigo-gradient">
                                        <i class="bi bi-box-arrow-up"></i>
                                    </div>
                                    <div class="stat-badge">
                                        <span class="badge bg-soft-indigo text-indigo">Released</span>
                                    </div>
                                </div>
                                <div class="stat-content mt-4">
                                    <h3 class="stat-value mb-2">0</h3>
                                    <p class="stat-label mb-2">Release Qty</p>
                                    <div class="progress stat-progress">
                                        <div class="progress-bar bg-indigo" role="progressbar" style="width: 85%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Stock Card -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card" id="total-stock">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="stat-icon-box bg-cyan-gradient">
                                        <i class="bi bi-boxes"></i>
                                    </div>
                                    <div class="stat-badge">
                                        <span class="badge bg-soft-cyan text-cyan">Available</span>
                                    </div>
                                </div>
                                <div class="stat-content mt-4">
                                    <h3 class="stat-value mb-2">0</h3>
                                    <p class="stat-label mb-2">Total Stock</p>
                                    <div class="progress stat-progress">
                                        <div class="progress-bar bg-cyan" role="progressbar" style="width: 70%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Analytics -->
        <div class="row mt-4">
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
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Base Styles */
.dashboard-container {
    padding: 1.5rem;
    background-color: #f8f9fa;
    background-image: 
        radial-gradient(at 90% 10%, rgb(65, 84, 241, 0.1) 0px, transparent 50%),
        radial-gradient(at 10% 90%, rgb(46, 202, 106, 0.1) 0px, transparent 50%);
    min-height: 100vh;
}

/* Header Styles */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 1rem;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
}

.header-wrapper h1 {
    font-size: 1.75rem;
    color: #2c3345;
    margin-bottom: 0.5rem;
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
    border-radius: 1rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
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
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.dashboard-card:hover::before {
    transform: translateX(100%);
}

.card-link {
    text-decoration: none;
    color: inherit;
}

.card-body {
    padding: 1.5rem;
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
    border-radius: 1rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
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

.text-indigo {
    color: #6610f2;
}

.text-cyan {
    color: #0dcaf0;
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
    margin-top: 1.5rem;
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
                    
                    // Update Secondary Stats
                    updateSecondaryStats(data);

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

    // Function to update primary statistics
    function updatePrimaryStats(data) {
        console.log('Updating Primary Stats with data:', data); // Debug log

        // Total Collections
        const totalCollections = data.total_collections || 0;
        const collectionsCard = document.querySelector('#total-collections .stat-value');
        if (collectionsCard) {
            collectionsCard.textContent = totalCollections;
            console.log('Updated Total Collections to:', totalCollections); // Debug log
        }

        // Tail Cutting
        const tailCutting = data.tail_cutting_count || 0;
        const tailCuttingCard = document.querySelector('#tail-cutting .stat-value');
        if (tailCuttingCard) {
            tailCuttingCard.textContent = tailCutting;
            console.log('Updated Tail Cutting to:', tailCutting); // Debug log
        }

        // Plasma Approved
        const plasmaApproved = data.approved_count || 0;
        const approvedCard = document.querySelector('#plasma-approved .stat-value');
        if (approvedCard) {
            approvedCard.textContent = plasmaApproved;
        }

        // Plasma Dispensed
        const plasmaDispensed = data.dispensed_count || 0;
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

    // Function to update secondary statistics
    function updateSecondaryStats(data) {
        console.log('Updating Secondary Stats with data:', data); // Debug log
        
        // Update each secondary stat card
        updateStatCard('stock-received', data.stock_received || 0, data.stock_received_progress);
        updateStatCard('pending-cutting', data.pending_cutting || 0, data.pending_cutting_progress);
        updateStatCard('under-test', data.under_test || 0, data.under_test_progress);
        updateStatCard('under-resolution', data.under_resolution || 0, data.under_resolution_progress);
        updateStatCard('release-qty', data.release_qty || 0, data.release_qty_progress);
        updateStatCard('total-stock', data.total_stock || 0, data.total_stock_progress);
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

