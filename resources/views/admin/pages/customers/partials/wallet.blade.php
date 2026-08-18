<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('Wallet') }}</h5>
        <div class="d-flex gap-2">
            <a href="{{ route('customers.wallet.export-pdf', ['customer' => $customer->id] + request()->query()) }}"
                class="btn btn-success" target="_blank">
                <i class="ti tabler-file-download me-2"></i>{{ __('Export PDF') }}
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#addWalletTransactionModal">
                <i class="ti tabler-plus me-2"></i>{{ __('Add Transaction') }}
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form action="{{ route('customers.show', $customer->id) }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">{{ __('Date From') }}</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('Date To') }}</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('Currency') }}</label>
                <select name="currency_id" class="form-select">
                    <option value="">{{ __('All Currencies') }}</option>
                    @foreach ($currencies as $currency)
                        <option value="{{ $currency->id }}"
                            {{ request('currency_id') == $currency->id ? 'selected' : '' }}>
                            {{ $currency->code }} - {{ $currency->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">{{ __('Filter') }}</button>
                <a href="{{ route('customers.show', $customer->id) }}"
                    class="btn btn-label-secondary">{{ __('Reset') }}</a>
            </div>
        </form>

        <!-- Wallet Statistics Cards -->
        <div class="row mb-4">
            @php
                $balances = $customer
                    ->walletTransactions()
                    ->select(
                        'currency_id',
                        \Illuminate\Support\Facades\DB::raw(
                            'SUM(CASE WHEN type = "debit" THEN amount ELSE -amount END) as balance',
                        ),
                    )
                    ->with('currency')
                    ->groupBy('currency_id')
                    ->reorder()
                    ->get();
            @endphp

            @forelse($balances as $balance)
                <div class="col-md-3 mb-3">
                    <div class="card bg-label-secondary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 text-muted">{{ $balance->currency->code ?? '???' }}</h6>
                                    <h4 class="mb-0 {{ $balance->balance < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ formatNumber($balance->balance) }}
                                        {{ $balance->currency->symbol ?? '' }}
                                    </h4>
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-white text-primary">
                                        <i class="ti tabler-wallet"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        {{ __('No wallet transactions yet.') }}
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Transactions Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Reference') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($walletTransactions as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                            <td>{{ $transaction->reference }}</td>
                            <td>{{ $transaction->description }}</td>
                            <td>
                                {{ formatNumber($transaction->amount) }}
                                {{ $transaction->currency->code ?? '' }}
                            </td>
                            <td>
                                @if ($transaction->type == 'debit')
                                    <span class="badge bg-label-success">{{ __('Debit') }}</span>
                                @else
                                    <span class="badge bg-label-danger">{{ __('Credit') }}</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-icon btn-label-info btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editWalletTransactionModal" data-id="{{ $transaction->id }}"
                                    data-created-at="{{ $transaction->created_at->format('Y-m-d') }}"
                                    data-type="{{ $transaction->type }}"
                                    data-currency="{{ $transaction->currency_id }}"
                                    data-amount="{{ $transaction->amount }}"
                                    data-reference="{{ $transaction->reference }}"
                                    data-description="{{ $transaction->description }}"
                                    onclick="editWalletTransaction(this)">
                                    <i class="ti tabler-edit"></i>
                                </button>
                                <form action="{{ route('wallet.transactions.destroy', $transaction->id) }}"
                                    method="POST" class="d-inline-block"
                                    onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-label-danger btn-sm">
                                        <i class="ti tabler-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Transaction Modal -->
<div class="modal fade" id="editWalletTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Wallet Transaction') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editWalletTransactionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Date') }}</label>
                        <input type="text" name="created_at" id="edit_created_at" class="form-control"
                            placeholder="YYYY-MM-DD" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Type') }}</label>
                        <select name="type" id="edit_type" class="form-select" required>
                            <option value="debit">{{ __('Debit (Add)') }}</option>
                            <option value="credit">{{ __('Credit (Deduct)') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Currency') }}</label>
                        <select name="currency_id" id="edit_currency_id" class="form-select" required>
                            @foreach ($currencies as $currency)
                                <option value="{{ $currency->id }}">{{ $currency->code }} - {{ $currency->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Amount') }}</label>
                        <input type="number" step="any" name="amount" id="edit_amount" class="form-control"
                            required min="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Reference') }}</label>
                        <input type="text" name="reference" id="edit_reference" class="form-control"
                            placeholder="{{ __('Optional') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Description') }}</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editDateInput = document.querySelector('#edit_created_at');
        if (editDateInput && typeof flatpickr !== 'undefined') {
            flatpickr(editDateInput, {
                dateFormat: 'Y-m-d',
                static: true, // Important for modals
                allowInput: true
            });
        }
    });

    function editWalletTransaction(button) {
        var id = button.getAttribute('data-id');
        var createdAt = button.getAttribute('data-created-at');
        var type = button.getAttribute('data-type');
        var currency = button.getAttribute('data-currency');
        var amount = button.getAttribute('data-amount');
        var reference = button.getAttribute('data-reference');
        var description = button.getAttribute('data-description');

        var form = document.getElementById('editWalletTransactionForm');
        // Construct the URL dynamically based on the current domain/path
        // Assuming the route is /wallet-transactions/{id}
        // Use a base URL if necessary, but relative path often works if at root
        // Better to use a data attribute for the base route if possible, or just hardcode if known
        // Since we are in admin panel, it might be /admin/wallet-transactions/... or just /wallet-transactions/...
        // Let's check the route definition. It's just 'wallet-transactions/{transaction}'.
        // However, if there is a route prefix, it matters.
        // routes/web.php has:
        // prefix => LaravelLocalization::setLocale()
        // inside that: dashboard prefix? No.
        // It is inside `Route::group(['prefix' => LaravelLocalization::setLocale() ...`
        // So the URL should be /{locale}/wallet-transactions/{id}
        // I should use a JS variable or helper to get the correct URL.
        // A simple way is to use a placeholder route and replace it.

        var actionUrl = "{{ route('wallet.transactions.update', ':id') }}";
        actionUrl = actionUrl.replace(':id', id);
        form.action = actionUrl;

        var dateInput = document.getElementById('edit_created_at');
        if (dateInput._flatpickr) {
            dateInput._flatpickr.setDate(createdAt);
        } else {
            dateInput.value = createdAt;
        }

        document.getElementById('edit_type').value = type;
        document.getElementById('edit_currency_id').value = currency;
        document.getElementById('edit_amount').value = amount;
        document.getElementById('edit_reference').value = reference;
        document.getElementById('edit_description').value = description;
    }
</script>

<!-- Add Transaction Modal -->
<div class="modal fade" id="addWalletTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add Wallet Transaction') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('customers.wallet.transaction', $customer->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Type') }}</label>
                        <select name="type" class="form-select" required>
                            <option value="debit">{{ __('Debit (Add)') }}</option>
                            <option value="credit">{{ __('Credit (Deduct)') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Currency') }}</label>
                        <select name="currency_id" class="form-select" required>
                            @foreach ($currencies as $currency)
                                <option value="{{ $currency->id }}">{{ $currency->code }} - {{ $currency->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Amount') }}</label>
                        <input type="number" step="any" name="amount" class="form-control" required
                            min="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Reference') }}</label>
                        <input type="text" name="reference" class="form-control"
                            placeholder="{{ __('Optional') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Description') }}</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
