<x-app-layout>
    <div class="pagetitle">
        <h1>BarTender Integration Test</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">BarTender Test</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Test BarTender Label Printing</h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ar_number" class="form-label">AR Number</label>
                                    <input type="text" class="form-control" id="ar_number" value="AR12345">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="ref_number" class="form-label">Reference Number</label>
                                    <input type="text" class="form-control" id="ref_number" value="REF67890">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="mega_pool" class="form-label">Mega Pool</label>
                                    <input type="text" class="form-control" id="mega_pool" value="MP001">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Mini Pools</label>
                                    <div id="mini_pools_container">
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control mini-pool-input" value="MP001-1">
                                            <button class="btn btn-outline-secondary remove-mini-pool" type="button"><i class="bi bi-trash"></i></button>
                                        </div>
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control mini-pool-input" value="MP001-2">
                                            <button class="btn btn-outline-secondary remove-mini-pool" type="button"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add_mini_pool">
                                        <i class="bi bi-plus-circle"></i> Add Mini Pool
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-primary" id="printLabelsBtn">
                                    <i class="bi bi-printer"></i> Print Labels
                                </button>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5>Response:</h5>
                                    <pre id="response" class="mt-2">No response yet</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add Mini Pool
            document.getElementById('add_mini_pool').addEventListener('click', function() {
                const container = document.getElementById('mini_pools_container');
                const newInput = document.createElement('div');
                newInput.className = 'input-group mb-2';
                newInput.innerHTML = `
                    <input type="text" class="form-control mini-pool-input" value="">
                    <button class="btn btn-outline-secondary remove-mini-pool" type="button"><i class="bi bi-trash"></i></button>
                `;
                container.appendChild(newInput);

                // Add event listener to the new remove button
                newInput.querySelector('.remove-mini-pool').addEventListener('click', function() {
                    container.removeChild(newInput);
                });
            });

            // Remove Mini Pool
            document.querySelectorAll('.remove-mini-pool').forEach(button => {
                button.addEventListener('click', function() {
                    const inputGroup = this.parentElement;
                    if (document.querySelectorAll('.mini-pool-input').length > 1) {
                        inputGroup.parentElement.removeChild(inputGroup);
                    } else {
                        alert('You need at least one mini pool');
                    }
                });
            });

            // Print Labels
            document.getElementById('printLabelsBtn').addEventListener('click', function() {
                const button = this;
                const originalText = button.innerHTML;

                // Get values
                const arNumber = document.getElementById('ar_number').value.trim();
                const refNumber = document.getElementById('ref_number').value.trim();
                const megaPool = document.getElementById('mega_pool').value.trim();

                // Get mini pools
                const miniPools = [];
                document.querySelectorAll('.mini-pool-input').forEach(input => {
                    const value = input.value.trim();
                    if (value) {
                        miniPools.push(value);
                    }
                });

                // Validate
                if (!arNumber || !refNumber || !megaPool || miniPools.length === 0) {
                    alert('Please fill in all fields');
                    return;
                }

                // Prepare data
                const data = {
                    ar_number: arNumber,
                    ref_number: refNumber,
                    mega_pool: megaPool,
                    mini_pools: miniPools
                };

                // Show loading
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Printing...';

                // Send request
                fetch('{{ route("print.bartender") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    // Display response
                    document.getElementById('response').textContent = JSON.stringify(data, null, 2);

                    // Show alert
                    if (data.success) {
                        alert('Print job sent successfully!');
                    } else {
                        alert('Failed to send print job: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('response').textContent = 'Error: ' + error.message;
                    alert('Error sending print job');
                })
                .finally(() => {
                    // Reset button
                    button.disabled = false;
                    button.innerHTML = originalText;
                });
            });
        });
    </script>
</x-app-layout>
