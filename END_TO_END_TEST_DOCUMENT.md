# Plasma Management System - End-to-End Test Document

## Document Information
- **Application Name:** Plasma Management System
- **Version:** 1.0
- **Created Date:** October 29, 2025
- **Test Environment:** Development/Staging
- **Framework:** Laravel 10+

---

## Table of Contents
1. [Application Overview](#application-overview)
2. [User Roles & Permissions](#user-roles--permissions)
3. [Complete Workflow Testing](#complete-workflow-testing)
4. [Module-Wise Test Cases](#module-wise-test-cases)
5. [Dashboard Testing](#dashboard-testing)
6. [Integration Testing](#integration-testing)
7. [Data Validation & Security](#data-validation--security)
8. [Performance Testing](#performance-testing)
9. [Test Data Requirements](#test-data-requirements)

---

## Application Overview

### Purpose
The Plasma Management System manages the complete lifecycle of plasma collection, processing, testing (ELISA & NAT), quality control, and dispensing to blood centers.

### Key Modules
1. **Blood Bank Management** - Blood center registration and management
2. **Tour Planning** - Collection scheduling and DCR (Daily Collection Reports)
3. **Plasma Inward** - Recording received plasma from blood banks
4. **Warehouse/Tail Cutting** - Bag processing and tail cutting
5. **Bag Entry** - Creating mega pools and mini pools
6. **ELISA Testing** - Upload and manage ELISA test results
7. **NAT Testing** - Upload and manage NAT test results
8. **Sub Mini-Pool Testing** - Reactive sample resolution testing
9. **Plasma Release/Rejection** - Quality control decisions
10. **Plasma Dispensing** - Distribution to blood centers
11. **Barcode Generation** - Label printing for bags
12. **Reporting** - Various reports and analytics
13. **Audit Trail** - Activity logging and tracking
14. **Dashboard** - Real-time monitoring and KPIs

---

## User Roles & Permissions

| Role ID | Role Name | Access Level | Key Responsibilities |
|---------|-----------|--------------|---------------------|
| 1 | Super Admin | Full System Access | System configuration, all modules |
| 2 | Company Admin | Company-wide Access | Manage company operations |
| 6 | RBE | Regional Access | Regional management |
| 7 | Logistics Admin | Logistics Operations | Transport and logistics |
| 8 | Sourcing Agent | Field Operations | Blood bank sourcing |
| 9 | Collecting Agent | Collection | Plasma collection from blood banks |
| 12 | Factory Admin | Factory Operations | Warehouse, testing, release, dispensing |
| 15 | Lab Technician | Laboratory | ELISA/NAT test upload and entry |
| 16 | Quality Control | QC Operations | Test result verification |
| 17 | Production Technician | Production | Bag entry, pooling |
| 18 | Manager | Management | Oversight and approvals |
| 19 | Supervisor | Supervision | Team supervision |

---

## Complete Workflow Testing

### END-TO-END SCENARIO: Plasma Journey from Collection to Dispensing

**Test ID:** E2E-001  
**Test Name:** Complete Plasma Processing Workflow  
**Duration:** ~2-3 hours  
**Prerequisites:** Active blood banks, users with appropriate roles, barcode printer configured

---

### Phase 1: Blood Bank Registration & Tour Planning

#### Test Case 1.1: Register Blood Bank
**Role:** Company Admin / Super Admin

| Step | Action | Expected Result | Status |
|------|--------|-----------------|--------|
| 1 | Navigate to Blood Banks → Register | Blood bank registration form loads | ⬜ |
| 2 | Fill in blood bank details:<br>- Name: "City Blood Bank"<br>- Type: Blood Bank<br>- Contact Person<br>- Address, City, State<br>- Mobile, Email<br>- Latitude/Longitude | Form accepts all inputs | ⬜ |
| 3 | Click Submit | Success message displayed<br>Blood bank created with ID | ⬜ |
| 4 | Verify in Blood Banks list | New blood bank appears in list | ⬜ |

**Expected Data:**
```
Blood Bank ID: (Auto-generated)
Status: Active
Location: Displayed on map
```

---

#### Test Case 1.2: Create Tour Plan
**Role:** Sourcing Agent / Manager

| Step | Action | Expected Result | Status |
|------|--------|-----------------|--------|
| 1 | Navigate to Tour Planner → Create Tour Plan | Tour plan form loads | ⬜ |
| 2 | Select:<br>- Blood Bank: "City Blood Bank"<br>- Collection Date: [Future Date]<br>- Planned Quantity: 150 Liters<br>- Collecting Agent | Form accepts inputs | ⬜ |
| 3 | Click Submit | Tour plan created successfully | ⬜ |
| 4 | Verify in Tour Plan list | Tour plan appears with "Pending" status | ⬜ |

---

### Phase 2: Plasma Collection & Inward

#### Test Case 2.1: Execute Collection (DCR)
**Role:** Collecting Agent

| Step | Action | Expected Result | Status |
|------|--------|-----------------|--------|
| 1 | Navigate to Tour Planner → DCR | Tour plans for today listed | ⬜ |
| 2 | Select assigned tour plan | Collection form opens | ⬜ |
| 3 | Enter collection details:<br>- Actual Collected: 145 Liters<br>- Start Time<br>- End Time<br>- Remarks | Data saved | ⬜ |
| 4 | Click Submit | DCR marked as completed | ⬜ |

---

#### Test Case 2.2: Plasma Inward Entry
**Role:** Factory Admin

| Step | Action | Expected Result | Status |
|------|--------|-----------------|--------|
| 1 | Navigate to Blood Banks → Index | Blood bank list displayed | ⬜ |
| 2 | Click on blood bank | Plasma entry form opens | ⬜ |
| 3 | Enter plasma inward details:<br>- Pickup Date<br>- Receipt Date<br>- GRN No: "GRN/2025/001"<br>- Blood Bank: "City Blood Bank"<br>- Plasma Qty: 145.00 Liters | Form accepts all values | ⬜ |
| 4 | Click Submit | Success message<br>Plasma entry created | ⬜ |
| 5 | Verify Dashboard | "Total Plasma Inwards" shows 145.00 | ⬜ |

**Expected Dashboard Data:**
```
Process Flow:
- Plasma Received: 145.00L ✅
- Tail Cut & Pooled: 0L
- ELISA Tested: 0
- NAT Tested: 0
- Released: 0L
- Dispensed: 0L
```

---

### Phase 3: Bag Entry & Pooling

#### Test Case 3.1: Create Bag Entry (Tail Cutting)
**Role:** Factory Admin / Production Technician

| Step | Action | Expected Result | Status |
|------|--------|-----------------|--------|
| 1 | Navigate to New Bag Entry | Bag entry form loads | ⬜ |
| 2 | Select:<br>- Blood Bank: "City Blood Bank"<br>- Work Station: "Station A"<br>- Date<br>- Pickup Date<br>- GRN No: "GRN/2025/001" | Form populated | ⬜ |
| 3 | Generate Mega Pool Number | Mega Pool No auto-generated (e.g., "MP/2025/001") | ⬜ |
| 4 | Enter 48 bag details (4 mini pools × 12 bags):<br>- Donor ID<br>- Donation Date<br>- Blood Group<br>- Bag Volume (ml): 250ml each<br>- Tail Cutting: "Yes" for all | Grid populated with 48 rows | ⬜ |
| 5 | System auto-calculates:<br>- Mini Pool Volumes (12 bags × 250ml = 3000ml = 3.00L each)<br>- Total Volume: 12.00L | Calculations correct | ⬜ |
| 6 | Mini Pool Numbers auto-generated:<br>- MP/2025/001-001<br>- MP/2025/001-002<br>- MP/2025/001-003<br>- MP/2025/001-004 | Mini pool numbers assigned | ⬜ |
| 7 | Click Submit | Bag entry saved successfully | ⬜ |
| 8 | Verify Dashboard | "Total Plasma Cuttings" shows 12.00 | ⬜ |

**Expected Data:**
- 4 Mini Pools created, each 3.00L
- 48 Bag Entry Details
- 1 Bag Entry record

---

### Phase 4: ELISA Testing

#### Test Case 4.1: Upload ELISA Test Results
**Role:** Lab Technician / Factory Admin

| Step | Action | Expected Result | Status |
|------|--------|-----------------|--------|
| 1 | Navigate to Report Upload (ELISA) | ELISA upload page loads | ⬜ |
| 2 | Upload CSV/Excel file with:<br>- Sequence ID (Mini Pool Numbers)<br>- Well Numbers<br>- OD Values<br>- HBV, HCV, HIV results | File uploaded successfully | ⬜ |
| 3 | System processes file and shows preview:<br>- 4 mini pools<br>- Results: 3 Nonreactive, 1 Reactive | Preview table displays correctly | ⬜ |
| 4 | Review results | Data accurate | ⬜ |
| 5 | Click Save | Results saved to database | ⬜ |
| 6 | Verify Dashboard | "Quality Testing Overview":<br>- ELISA: 3/4 (75% Pass Rate)<br>- ELISA Reactive: 1 | ⬜ |

**Expected Results:**
```
Mini Pool 001: Nonreactive (HBV: nonreactive, HCV: nonreactive, HIV: nonreactive)
Mini Pool 002: Nonreactive
Mini Pool 003: Nonreactive
Mini Pool 004: Reactive (HBV: reactive)  ← Needs sub-pool testing
```

---

#### Test Case 4.2: Sub Mini-Pool Entry for Reactive Samples
**Role:** Lab Technician / Factory Admin

| Step | Action | Expected Result | Status |
|------|--------|-----------------|--------|
| 1 | Navigate to Factory Report → Sub Mini-Pool Entry | Form loads with reactive mini pools | ⬜ |
| 2 | Select Reactive Mini Pool: "MP/2025/001-004" | Mini pool details load | ⬜ |
| 3 | System shows 12 bags in the reactive mini pool | 12 bags listed | ⬜ |
| 4 | Upload sub-pool ELISA results (individual bag testing) | Results uploaded for 12 bags | ⬜ |
| 5 | Review: 11 bags Nonreactive, 1 bag Reactive (Donor ID: D12345) | Correct identification | ⬜ |
| 6 | Click Save | Sub-pool results saved | ⬜ |
| 7 | Verify Dashboard | ELISA Reactive count updated | ⬜ |

---

### Phase 5: NAT Testing

#### Test Case 5.1: Upload NAT Test Results
**Role:** Lab Technician

| Step | Action | Expected Result | Status |
|------|--------|-----------------|--------|
| 1 | Navigate to NAT Report | NAT upload page loads | ⬜ |
| 2 | Upload CSV file with:<br>- Mini Pool Numbers<br>- HIV, HBV, HCV results<br>- Analyzer info | File uploaded | ⬜ |
| 3 | System processes:<br>- 3 mini pools (skipping the reactive one)<br>- All 3 showing Nonreactive | Preview shows 3 pools | ⬜ |
| 4 | Click Save | NAT results saved | ⬜ |
| 5 | Verify Dashboard | "Quality Testing Overview":<br>- NAT: 3/3 (100% Pass Rate)<br>- NAT Test Liters: 9.00L | ⬜ |

**Expected Dashboard:**
```
ELISA Test: 3/4 (75% Pass Rate)
NAT Test: 3/3 (100% Pass Rate)
ELISA Reactive: 1
NAT Reactive: 0
```

---

### Phase 6: Barcode Generation

#### Test Case 6.1: Generate AR Number & Barcodes
**Role:** Factory Admin

| Step | Action | Expected Result | Status |
|------|--------|-----------------|--------|
| 1 | Navigate to Barcode → Generate | Barcode generation page loads | ⬜ |
| 2 | System generates AR Number: "AR/2025/001" | AR number displayed | ⬜ |
| 3 | Select Mini Pools (3 non-reactive ones):<br>- MP/2025/001-001 (3.00L)<br>- MP/2025/001-002 (3.00L)<br>- MP/2025/001-003 (3.00L) | 3 mini pools selected | ⬜ |
| 4 | Enter batch details:<br>- Batch No: "BATCH/2025/001"<br>- Manufacturing Date<br>- Expiry Date | Details entered | ⬜ |
| 5 | Click Generate Barcodes | Barcodes generated for 3 mini pools | ⬜ |
| 6 | Preview barcodes | QR codes/Barcodes display correctly | ⬜ |
| 7 | Click Print | Barcodes sent to printer | ⬜ |
| 8 | Save | AR number assigned to plasma entry | ⬜ |

---

### Phase 7: Plasma Release

#### Test Case 7.1: Release Plasma
**Role:** Quality Control / Factory Admin

| Step | Action | Expected Result | Status |
|------|--------|-----------------|--------|
| 1 | Navigate to Factory Report → Plasma Release | Release form loads | ⬜ |
| 2 | Enter AR Number: "AR/2025/001" | System fetches mini pool details | ⬜ |
| 3 | Review test results:<br>- 3 mini pools<br>- All tests passed<br>- Total Volume: 9.00L | Details displayed correctly | ⬜ |
| 4 | Verify quality metrics | All parameters within acceptable range | ⬜ |
| 5 | Click "Release" | Plasma released successfully | ⬜ |
| 6 | Verify Dashboard | "Plasma Approved" shows 9.00L | ⬜ |

**Expected Status:**
```
bag_status_details:
- AR No: AR/2025/001
- Status: release
- Status Type: final
- 3 mini pools marked as released
```

---

### Phase 8: Plasma Dispensing

#### Test Case 8.1: Dispense Plasma to Blood Center
**Role:** Factory Admin / Warehouse Team

| Step | Action | Expected Result | Status |
|------|--------|-----------------|--------|
| 1 | Navigate to Factory Report → Plasma Despense | Despense form loads | ⬜ |
| 2 | Enter:<br>- AR Number: "AR/2025/001"<br>- Batch Number: "BATCH/2025/001"<br>- Date | Mini pools loaded | ⬜ |
| 3 | System displays:<br>- 3 mini pools<br>- Total Available: 9.00L | Correct volumes shown | ⬜ |
| 4 | Enter issued volumes:<br>- MP-001: 3.00L<br>- MP-002: 3.00L<br>- MP-003: 2.50L<br>- Total Issued: 8.50L | Calculations auto-update | ⬜ |
| 5 | Save as Draft | Draft saved successfully | ⬜ |
| 6 | Review and click Final Submit | Final submission successful | ⬜ |
| 7 | Verify Dashboard | "Plasma Dispensed" shows 8.50L | ⬜ |

**Expected Data:**
```
bag_status_details:
- Status: despense
- Status Type: final
- Issued Volume: 8.50L
- Remaining: 0.50L
```

---

### Phase 9: Plasma Rejection

#### Test Case 9.1: Reject Failed Plasma
**Role:** Quality Control / Factory Admin

| Step | Action | Expected Result | Status |
|------|--------|-----------------|--------|
| 1 | Navigate to Factory Report → Plasma Rejection | Rejection form loads | ⬜ |
| 2 | Enter AR Number of failed plasma | System retrieves details | ⬜ |
| 3 | Select rejection reason:<br>- Quality Rejected<br>- Reactive Test Result | Reason selected | ⬜ |
| 4 | System generates Destruction No: "DES/2025/001" | Destruction number assigned | ⬜ |
| 5 | Click Submit | Rejection processed | ⬜ |
| 6 | Verify destruction record created | Record in plasma_entries_destruction | ⬜ |

---

## Module-Wise Test Cases

### Module 1: Dashboard

#### Test Case: Dashboard Data Accuracy
**Role:** Factory Admin

| Test ID | Test Description | Steps | Expected Result | Status |
|---------|------------------|-------|-----------------|--------|
| DASH-001 | Factory dashboard loads | Login as Factory Admin → Navigate to Dashboard | Factory-specific dashboard displays | ⬜ |
| DASH-002 | Process flow accuracy | Check all 6 pipeline steps | Accurate counts at each stage | ⬜ |
| DASH-003 | Quality metrics calculation | Verify ELISA & NAT pass rates | Correct percentage calculations | ⬜ |
| DASH-004 | Filter by This Month | Click filter → Select "This Month" | Data updates for current month only | ⬜ |
| DASH-005 | Filter by Last 3 Months | Click filter → Select "Last 3 Months" | Data shows last 3 months (including current) | ⬜ |
| DASH-006 | Filter by Last 6 Months | Click filter → Select "Last 6 Months" | Data shows last 6 months | ⬜ |
| DASH-007 | Filter by Last 12 Months | Click filter → Select "Last 12 Months" | Data shows last 12 months | ⬜ |
| DASH-008 | Filter by All | Click filter → Select "All" | All historical data displayed | ⬜ |
| DASH-009 | Action Required alerts | Check pending action cards | Correct counts for pending items | ⬜ |
| DASH-010 | Quick Actions links | Click each action button | Correct pages load | ⬜ |

**Dashboard Metrics to Verify:**

| Metric | Calculation | Expected Value Example |
|--------|-------------|------------------------|
| Total Plasma Inwards | Sum of `plasma_entries.plasma_qty` | 145.00 Liters |
| Total Plasma Cuttings | Sum of `bag_volume_ml / 1000` where `tail_cutting = 'Yes'` | 12.00 Liters |
| Plasma Approved | Sum of `plasma_qty` where `alloted_ar_no IS NOT NULL` | 9.00 Liters |
| Plasma Dispensed | Sum of `issued_volume` in `bag_status_details` | 8.50 Liters |
| ELISA Test Results | Non-Reactive / Total Tests | 3 / 4 (75%) |
| NAT Test Results | Non-Reactive / Total Tests | 3 / 3 (100%) |
| ELISA Reactive | Count of reactive ELISA tests | 1 |
| NAT Reactive | Count of reactive NAT tests | 0 |

---

### Module 2: Blood Bank Management

| Test ID | Test Description | Input | Expected Output | Status |
|---------|------------------|-------|-----------------|--------|
| BB-001 | Register blood bank | Valid data | Blood bank created | ⬜ |
| BB-002 | Duplicate blood bank | Same name | Error: Already exists | ⬜ |
| BB-003 | Edit blood bank | Update contact info | Changes saved | ⬜ |
| BB-004 | Search blood bank | Search by name/city | Filtered results | ⬜ |
| BB-005 | Map location display | Blood bank with coordinates | Pin shown on map | ⬜ |
| BB-006 | Required field validation | Submit without required fields | Validation errors shown | ⬜ |

---

### Module 3: Warehouse / Tail Cutting

| Test ID | Test Description | Input | Expected Output | Status |
|---------|------------------|-------|-----------------|--------|
| WH-001 | View warehouse list | Navigate to warehouse | All warehouses listed | ⬜ |
| WH-002 | Register warehouse | Valid warehouse data | Warehouse created | ⬜ |
| WH-003 | Tail cutting process | Mark bags for tail cutting | Bags flagged correctly | ⬜ |
| WH-004 | Volume calculation | 12 bags × 250ml | Auto-calculates to 3.00L | ⬜ |

---

### Module 4: Bag Entry & Mini Pool Creation

| Test ID | Test Description | Input | Expected Output | Status |
|---------|------------------|-------|-----------------|--------|
| BE-001 | Create mega pool | Select GRN, enter bags | Mega pool number generated | ⬜ |
| BE-002 | Auto mini pool creation | 48 bags entered | 4 mini pools created (12 bags each) | ⬜ |
| BE-003 | Volume validation | Enter invalid volume (negative) | Error message | ⬜ |
| BE-004 | Duplicate mega pool | Use existing mega pool number | Error: Duplicate | ⬜ |
| BE-005 | Check mega pool | Search by mega pool number | Details retrieved | ⬜ |
| BE-006 | Sub mini-pool creation | Reactive mini pool | Sub mini-pools created | ⬜ |

---

### Module 5: ELISA Test Report

| Test ID | Test Description | Input | Expected Output | Status |
|---------|------------------|-------|-----------------|--------|
| EL-001 | Upload ELISA file | Valid CSV with 4 mini pools | 4 records created | ⬜ |
| EL-002 | Invalid file format | Wrong CSV structure | Error: Invalid format | ⬜ |
| EL-003 | Duplicate upload | Upload same data twice | Update existing records | ⬜ |
| EL-004 | Reactive detection | Upload with reactive sample | Final result = "Reactive" | ⬜ |
| EL-005 | Borderline detection | Upload with borderline OD value | Final result = "Borderline" | ⬜ |
| EL-006 | Nonreactive result | All markers nonreactive | Final result = "Nonreactive" | ⬜ |
| EL-007 | Missing mini pool | Upload for non-existent pool | Error: Pool not found | ⬜ |

**ELISA Final Result Logic:**
```
IF (HBV = reactive OR HCV = reactive OR HIV = reactive) THEN "Reactive"
ELSE IF (HBV = borderline OR HCV = borderline OR HIV = borderline) THEN "Borderline"
ELSE "Nonreactive"
```

---

### Module 6: NAT Test Report

| Test ID | Test Description | Input | Expected Output | Status |
|---------|------------------|-------|-----------------|--------|
| NAT-001 | Upload NAT file | Valid CSV with mini pools | Records created | ⬜ |
| NAT-002 | All nonreactive | All markers nonreactive | Tests passed | ⬜ |
| NAT-003 | Any reactive marker | HIV reactive | Test failed, needs action | ⬜ |
| NAT-004 | NAT retest entry | Upload retest results | Retest records created | ⬜ |
| NAT-005 | Mega pool retest | Reactive mega pool | Mega pool retest workflow | ⬜ |

---

### Module 7: Barcode & Label Generation

| Test ID | Test Description | Input | Expected Output | Status |
|---------|------------------|-------|-----------------|--------|
| BC-001 | Generate AR number | Click generate | AR/YYYY/### format | ⬜ |
| BC-002 | Select mini pools | Choose passed mini pools | Mini pools linked to AR | ⬜ |
| BC-003 | Generate barcodes | Click generate | QR codes created | ⬜ |
| BC-004 | Print labels | Click print | Labels sent to printer | ⬜ |
| BC-005 | Reprint barcodes | Select existing AR | Reprinting successful | ⬜ |
| BC-006 | Download CSV | Click download | CSV file downloaded | ⬜ |

---

### Module 8: Reports

| Test ID | Report Name | Input | Expected Output | Status |
|---------|-------------|-------|-----------------|--------|
| REP-001 | Mega Pool/Mini Pool Report | Enter mega pool number | PDF with all details | ⬜ |
| REP-002 | Tail Cutting Report | Select date range | List of tail cut bags | ⬜ |
| REP-003 | Blood Bank Summary | Select blood bank | Collection summary report | ⬜ |
| REP-004 | DCR Summary | Select date range | Daily collection reports | ⬜ |
| REP-005 | User-wise Collection | Select user, date range | User performance report | ⬜ |
| REP-006 | Plasma Dispensing Report | Select date range | Dispensing details | ⬜ |

---

### Module 9: Audit Trail

| Test ID | Test Description | Action | Expected Result | Status |
|---------|------------------|--------|-----------------|--------|
| AUD-001 | Create operation logged | Create any record | Audit entry with "create" action | ⬜ |
| AUD-002 | Update operation logged | Edit any record | Old & new values logged | ⬜ |
| AUD-003 | Delete operation logged | Delete record | Delete action logged | ⬜ |
| AUD-004 | View audit trail | Navigate to Audit | All activities listed | ⬜ |
| AUD-005 | Filter audit by module | Filter by "Plasma Management" | Only relevant entries shown | ⬜ |
| AUD-006 | Filter audit by user | Select specific user | User's activities shown | ⬜ |
| AUD-007 | View audit details | Click on audit entry | Old/new values shown | ⬜ |

---

## Dashboard Testing

### Dashboard Sections Verification

#### Section 1: Process Flow Visualization

| Element | Data Source | Unit | Expected Example | Status |
|---------|-------------|------|------------------|--------|
| Plasma Received | `plasma_entries.plasma_qty` | Liters | 145.00L | ⬜ |
| Tail Cut & Pooled | `bag_volume_ml / 1000` | Liters | 12.00L | ⬜ |
| ELISA Tested | Count of ELISA tests | Count | 4 | ⬜ |
| NAT Tested | Count of NAT tests | Count | 3 | ⬜ |
| Released | Sum `plasma_qty` where AR assigned | Liters | 9.00L | ⬜ |
| Dispensed | Sum `issued_volume` | Liters | 8.50L | ⬜ |

#### Section 2: Quick Actions

| Button | Target Page | Expected Behavior | Status |
|--------|-------------|-------------------|--------|
| New Bag Entry | `/newBagEntry` | Bag entry form loads | ⬜ |
| Upload ELISA | `/report/upload` | ELISA upload page loads | ⬜ |
| Upload NAT | `/nat-report` | NAT upload page loads | ⬜ |
| Plasma Release | `/factory/report/plasma-release` | Release form loads | ⬜ |
| Plasma Rejection | `/factory/report/plasma-rejection` | Rejection form loads | ⬜ |
| Generate Reports | `/factory/generate-report/mega-pool-mini-pool` | Report generation page loads | ⬜ |

#### Section 3: Action Required Alerts

| Alert Card | Calculation | When to Show | Status |
|------------|-------------|--------------|--------|
| Pending Tail Cutting | Plasma entries without bag entries (>24hrs) | Count > 0 | ⬜ |
| Reactive Mini Pools | ELISA reactive count | Count > 0 | ⬜ |
| Pending Release | Plasma without AR number | Count > 0 | ⬜ |
| Awaiting Results | Mini pools without test results | Count > 0 | ⬜ |

#### Section 4: Quality Testing Overview

| Card | Calculation | Format | Status |
|------|-------------|--------|--------|
| ELISA Test Results | `Non-Reactive / Total` | "450 / 500" + "90% Pass Rate" | ⬜ |
| NAT Test Results | `Non-Reactive / Total` | "475 / 500" + "95% Pass Rate" | ⬜ |
| ELISA Reactive | Count where `final_result = 'Reactive'` | Large number with alert styling | ⬜ |
| NAT Reactive | Count where any marker reactive | Large number with alert styling | ⬜ |

#### Section 5: Plasma Collection & Processing

| Card | Data Source | Unit | Status |
|------|-------------|------|--------|
| Total Plasma Inwards | `plasma_entries.plasma_qty` | Liters | ⬜ |
| Total Plasma Cuttings | `bag_volume_ml / 1000` | Liters | ⬜ |
| Plasma Approved | `plasma_qty` where AR assigned | Liters | ⬜ |
| Plasma Dispensed | `issued_volume` sum | Liters | ⬜ |

---

## Integration Testing

### Test Case: Complete Data Flow Integration

**Test ID:** INT-001  
**Test Name:** End-to-End Data Integrity

| Stage | Action | Data Verification | Status |
|-------|--------|-------------------|--------|
| 1 | Create plasma entry: 145L | `plasma_entries` table has record | ⬜ |
| 2 | Create bag entry: 48 bags @ 250ml each | `bag_entries_details`: 48 records<br>`bag_entries_mini_pools`: 4 records<br>Total: 12.00L | ⬜ |
| 3 | Upload ELISA for 4 mini pools | `sub_mini_pool_elisa_test_report`: 4 records | ⬜ |
| 4 | Upload NAT for 3 pools | `nat_test_report`: 3 records | ⬜ |
| 5 | Generate AR number | `plasma_entries.alloted_ar_no` updated | ⬜ |
| 6 | Release plasma (9.00L) | `bag_status_details` release records | ⬜ |
| 7 | Dispense 8.50L | `bag_status_details` despense records<br>`issued_volume` = 8.50 | ⬜ |
| 8 | Check dashboard totals | All metrics match:<br>- Received: 145.00L<br>- Cut: 12.00L<br>- Released: 9.00L<br>- Dispensed: 8.50L | ⬜ |

---

### Test Case: Filter Integration

**Test ID:** INT-002  
**Test Name:** Date Filter Accuracy Across All Metrics

**Setup:**
- Create plasma entries across 6 months
- Process them through all stages
- Test each filter

| Filter | Test Action | Expected Behavior | Status |
|--------|-------------|-------------------|--------|
| This Month | Create 100L this month, 200L last month | Shows only 100L | ⬜ |
| Last 3 Months | Data from Sept, Oct, Nov | Shows total from 3 months | ⬜ |
| Last 6 Months | Data from May to Oct | Shows 6 months total | ⬜ |
| Last 12 Months | Data from last year | Shows 12 months total | ⬜ |
| All | Data from 2+ years | Shows all data | ⬜ |

---

## Data Validation & Security

### Input Validation Tests

| Test ID | Field | Invalid Input | Expected Behavior | Status |
|---------|-------|---------------|-------------------|--------|
| VAL-001 | Plasma Qty | -50 | Error: Must be positive | ⬜ |
| VAL-002 | Plasma Qty | "ABC" | Error: Must be numeric | ⬜ |
| VAL-003 | Date | Future date (where not allowed) | Error: Invalid date | ⬜ |
| VAL-004 | GRN No | Empty (if required) | Error: Required field | ⬜ |
| VAL-005 | Email | Invalid format | Error: Invalid email | ⬜ |
| VAL-006 | Mobile | Less than 10 digits | Error: Invalid mobile | ⬜ |
| VAL-007 | AR Number | Non-existent | Error: Not found | ⬜ |
| VAL-008 | Bag Volume | 0 or negative | Error: Invalid volume | ⬜ |

### Security Tests

| Test ID | Test Description | Steps | Expected Result | Status |
|---------|------------------|-------|-----------------|--------|
| SEC-001 | Unauthorized access | Non-factory user tries to access factory dashboard | Redirect or error | ⬜ |
| SEC-002 | SQL injection | Enter SQL in search field | Input sanitized | ⬜ |
| SEC-003 | XSS attack | Enter `<script>` in text field | Script escaped | ⬜ |
| SEC-004 | CSRF protection | Submit form without token | Error: Invalid token | ⬜ |
| SEC-005 | Session timeout | Wait for session expiry | Redirect to login | ⬜ |
| SEC-006 | Role-based access | Lab tech tries to access admin function | Access denied | ⬜ |

---

## Performance Testing

### Load Testing

| Test ID | Scenario | Load | Expected Response Time | Status |
|---------|----------|------|----------------------|--------|
| PERF-001 | Dashboard load | Single user | < 2 seconds | ⬜ |
| PERF-002 | Dashboard with 10,000 records | Single user | < 3 seconds | ⬜ |
| PERF-003 | Concurrent users | 10 simultaneous users | No errors, < 5 sec | ⬜ |
| PERF-004 | ELISA file upload | 1000 records CSV | < 10 seconds | ⬜ |
| PERF-005 | Report generation | 6-month mega pool report | < 15 seconds | ⬜ |

---

## Test Data Requirements

### Minimum Test Data Set

```
Blood Banks: 5
Users:
  - 1 Super Admin
  - 1 Factory Admin (role_id: 12)
  - 1 Lab Technician (role_id: 15)
  - 1 Collecting Agent (role_id: 9)
  
Plasma Entries: 10 (across 3 months)
  - Total Volume: 500 Liters
  
Bag Entries: 3
  - 144 total bags (3 mega pools × 48 bags)
  - 12 mini pools
  
ELISA Tests: 12 mini pools
  - 10 Nonreactive
  - 2 Reactive
  
NAT Tests: 10 mini pools (non-reactive ones)
  - 10 Nonreactive
  - 0 Reactive
  
Released Plasma: 2 AR numbers
  - AR/2025/001: 9.00L
  - AR/2025/002: 6.00L
  
Dispensed: 2 batches
  - Batch 1: 8.50L
  - Batch 2: 5.75L
```

---

## Test Execution Checklist

### Pre-Test Setup
- [ ] Database backed up
- [ ] Test environment configured
- [ ] Test users created with all roles
- [ ] Sample blood banks registered
- [ ] Barcode printer configured (or mock)
- [ ] Test data prepared

### Module Testing Order
1. [ ] User Management & Authentication
2. [ ] Blood Bank Management
3. [ ] Tour Planning & DCR
4. [ ] Plasma Inward Entry
5. [ ] Warehouse & Tail Cutting
6. [ ] Bag Entry & Pooling
7. [ ] ELISA Testing
8. [ ] NAT Testing
9. [ ] Sub Mini-Pool Testing
10. [ ] Barcode Generation
11. [ ] Plasma Release
12. [ ] Plasma Dispensing
13. [ ] Plasma Rejection
14. [ ] Reports Generation
15. [ ] Dashboard Verification
16. [ ] Audit Trail
17. [ ] Integration Testing
18. [ ] Performance Testing

### Post-Test Activities
- [ ] Document all bugs found
- [ ] Verify all data in database
- [ ] Check audit logs
- [ ] Review dashboard accuracy
- [ ] Export test results
- [ ] Cleanup test data (if needed)

---

## Bug Tracking Template

### Bug Report Format

```
Bug ID: BUG-###
Module: [Module Name]
Severity: [Critical / High / Medium / Low]
Reported By: [Tester Name]
Date: [Date]

Description:
[Clear description of the issue]

Steps to Reproduce:
1. [Step 1]
2. [Step 2]
3. [Step 3]

Expected Result:
[What should happen]

Actual Result:
[What actually happened]

Screenshots:
[Attach screenshots]

Status: [Open / In Progress / Resolved / Closed]
```

---

## Test Sign-Off

### Test Completion Criteria

- [ ] All critical test cases passed (100%)
- [ ] All high-priority test cases passed (≥95%)
- [ ] No critical or high severity bugs open
- [ ] Dashboard displays accurate data
- [ ] All filters working correctly
- [ ] All reports generating successfully
- [ ] Performance benchmarks met
- [ ] Security vulnerabilities addressed

### Sign-Off

| Role | Name | Signature | Date |
|------|------|-----------|------|
| QA Lead | | | |
| Development Lead | | | |
| Factory Admin (User) | | | |
| Project Manager | | | |

---

## Appendix

### A. Test Data Examples

#### Sample Plasma Entry CSV
```csv
Pickup Date,Receipt Date,GRN No,Blood Bank ID,Plasma Qty (Liters),Remarks
2025-10-15,2025-10-16,GRN/2025/001,1,145.50,Regular collection
2025-10-16,2025-10-17,GRN/2025/002,2,200.00,Large batch
```

#### Sample ELISA Result CSV
```csv
Sequence ID,Well Label,OD Value,Ratio,HBV,HCV,HIV
MP/2025/001-001,A1,0.125,0.85,nonreactive,nonreactive,nonreactive
MP/2025/001-002,A2,0.140,0.95,nonreactive,nonreactive,nonreactive
MP/2025/001-003,A3,0.135,0.90,nonreactive,nonreactive,nonreactive
MP/2025/001-004,A4,2.450,2.10,reactive,nonreactive,nonreactive
```

#### Sample NAT Result CSV
```csv
Mini Pool ID,HIV,HBV,HCV,Analyzer,Operator
MP/2025/001-001,nonreactive,nonreactive,nonreactive,Roche 6800,John Doe
MP/2025/001-002,nonreactive,nonreactive,nonreactive,Roche 6800,John Doe
MP/2025/001-003,nonreactive,nonreactive,nonreactive,Roche 6800,John Doe
```

### B. Database Table Relationships

```
plasma_entries (Plasma Qty in Liters)
    └─→ bag_entries (via GRN No)
        └─→ bag_entries_details (Bag Volume in ML)
            └─→ bag_entries_mini_pools (Mini Pool Volume in Liters)
                ├─→ sub_mini_pool_entries
                │   └─→ sub_mini_pool_elisa_test_report
                ├─→ elisa_test_report (deprecated)
                └─→ nat_test_report

plasma_entries (with AR No) ──→ bag_status_details
    ├─→ Status: release
    └─→ Status: despense (Issued Volume in Liters)
```

### C. Unit Conversion Reference

| Field | Table | Stored Unit | Display Unit | Conversion |
|-------|-------|-------------|--------------|------------|
| plasma_qty | plasma_entries | Liters | Liters | No conversion |
| bag_volume_ml | bag_entries_details | Milliliters | Liters | ÷ 1000 |
| mini_pool_bag_volume | bag_entries_mini_pools | Liters | Liters | No conversion |
| issued_volume | bag_status_details | Liters | Liters | No conversion |
| total_volume | bag_status_details | Liters | Liters | No conversion |

### D. Common Test Scenarios

#### Scenario 1: Reactive Sample Resolution
1. ELISA test shows reactive mini pool
2. Sub mini-pool testing identifies specific reactive bag
3. That bag is rejected
4. Remaining 11 bags proceed to NAT testing
5. If NAT passes, remaining plasma released

#### Scenario 2: Complete Rejection
1. Mini pool fails both ELISA and NAT
2. Quality control rejects entire mini pool
3. Destruction number assigned
4. Record moved to plasma_entries_destruction

#### Scenario 3: Partial Dispensing
1. AR number has 9.00L total
2. Blood center requests 5.00L
3. Dispense 5.00L
4. Remaining 4.00L stays in stock
5. Later, dispense remaining 4.00L to another center

---

## Notes for Testers

### Important Testing Points

1. **Unit Consistency**: Always verify liters vs milliliters
2. **Date Filters**: Test all 5 filter options (This Month, 3/6/12 months, All)
3. **Calculations**: Manually verify all calculated fields
4. **Status Transitions**: Verify status changes (Pending → Processed → Released → Dispensed)
5. **Audit Trail**: Check every CRUD operation is logged
6. **Responsive Design**: Test on desktop, tablet, and mobile
7. **Browser Compatibility**: Test on Chrome, Firefox, Edge
8. **Data Integrity**: Ensure no orphaned records
9. **Concurrent Operations**: Test multiple users working simultaneously
10. **Error Handling**: Verify user-friendly error messages

### Common Issues to Watch For

- ⚠️ Date range filters not working (FIXED in latest update)
- ⚠️ Unit conversion errors (ml vs liters)
- ⚠️ Pass rate calculations incorrect
- ⚠️ Reactive sample detection logic
- ⚠️ Duplicate mini pool numbers
- ⚠️ Missing audit trail entries
- ⚠️ Dashboard not updating after filter change
- ⚠️ Barcode printer connectivity
- ⚠️ CSV upload format validation

---

## Test Result Summary Template

### Test Execution Summary

**Test Cycle:** [Test Cycle Number]  
**Start Date:** [Date]  
**End Date:** [Date]  
**Tested By:** [Tester Names]  
**Environment:** [Development/Staging/Production]

### Results

| Module | Total Cases | Passed | Failed | Blocked | Pass Rate |
|--------|-------------|--------|--------|---------|-----------|
| Dashboard | 10 | | | | |
| Blood Bank | 6 | | | | |
| Warehouse | 4 | | | | |
| Bag Entry | 6 | | | | |
| ELISA Testing | 7 | | | | |
| NAT Testing | 5 | | | | |
| Barcode | 6 | | | | |
| Reports | 6 | | | | |
| Audit Trail | 7 | | | | |
| **TOTAL** | **57** | | | | |

### Defects Summary

| Severity | Open | In Progress | Resolved | Closed |
|----------|------|-------------|----------|---------|
| Critical | | | | |
| High | | | | |
| Medium | | | | |
| Low | | | | |

### Test Conclusion

```
Overall Status: [PASS / FAIL / CONDITIONAL PASS]

Key Findings:
1. [Finding 1]
2. [Finding 2]
3. [Finding 3]

Recommendations:
1. [Recommendation 1]
2. [Recommendation 2]

Sign-off Status: [APPROVED / CONDITIONAL / REJECTED]
```

---

## Revision History

| Version | Date | Modified By | Changes |
|---------|------|-------------|---------|
| 1.0 | 2025-10-29 | System | Initial document creation |

---

**END OF DOCUMENT**

