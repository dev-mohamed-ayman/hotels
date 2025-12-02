@extends('admin.layouts.app')

@section('title', __('Bookings'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Bookings List') }}</h5>
                    <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                        <i class="ti tabler-plus me-2"></i>{{ __('Add Booking') }}
                    </a>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-hover text-center">
                            <thead>
                                <tr>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Hotel') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Check In') }}</th>
                                    <th>{{ __('Check Out') }}</th>
                                    <th>{{ __('Nights') }}</th>
                                    <th>{{ __('Total') }}</th>
                                    <th>{{ __('Paid') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bookings as $booking)
                                    <tr>
                                        <td><strong>{{ $booking->code }}</strong></td>
                                        <td>{{ $booking->hotel->name }}</td>
                                        <td>{{ $booking->customer->name }}</td>
                                        <td>{{ $booking->check_in->format('Y-m-d') }}</td>
                                        <td>{{ $booking->check_out->format('Y-m-d') }}</td>
                                        <td>{{ $booking->nights }}</td>
                                        <td>{{ number_format($booking->total_amount, 2) }} {{ $booking->currency->code }}
                                        </td>
                                        <td>{{ number_format($booking->paid_amount, 2) }} {{ $booking->currency->code }}
                                        </td>
                                        <td>
                                            @if ($booking->paid_amount == 0)
                                                <span class="badge bg-danger" title="{{ __('Unpaid') }}">
                                                    <i class="ti tabler-x"></i>
                                                </span>
                                            @elseif ($booking->paid_amount >= $booking->total_amount)
                                                <span class="badge bg-success" title="{{ __('Paid') }}">
                                                    <i class="ti tabler-check"></i>
                                                </span>
                                            @else
                                                <span class="badge bg-warning" title="{{ __('Partial Payment') }}">
                                                    <i class="ti tabler-question-mark"></i>
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                @if ($booking->check_in >= now())
                                                    <a href="{{ route('bookings.edit', $booking) }}"
                                                        class="btn btn-sm btn-success" title="{{ __('Edit') }}">
                                                        <i class="ti tabler-edit"></i>
                                                    </a>
                                                @endif

                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                    data-bs-target="#paymentModal{{ $booking->id }}">
                                                    <i class="ti tabler-currency-dollar"></i>
                                                </button>

                                                @if ($booking->check_in >= now())
                                                    <form action="{{ route('bookings.destroy', $booking) }}" method="POST"
                                                        class="d-inline-block"
                                                        onsubmit="return confirm('{{ __('Are you sure you want to delete this booking?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            title="{{ __('Delete') }}">
                                                            <i class="ti tabler-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Payment Update Modal -->
                                    <div class="modal fade" id="paymentModal{{ $booking->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('Update Payment') }} -
                                                        {{ $booking->code }}</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('bookings.update-payment', $booking) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('Total Amount') }}</label>
                                                            <input type="text" class="form-control"
                                                                value="{{ number_format($booking->total_amount, 2) }} {{ $booking->currency->code }}"
                                                                readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label">{{ __('Current Paid Amount') }}</label>
                                                            <input type="text" class="form-control"
                                                                value="{{ number_format($booking->paid_amount, 2) }} {{ $booking->currency->code }}"
                                                                readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            @php
                                                                $remaining =
                                                                    $booking->total_amount - $booking->paid_amount;
                                                            @endphp
                                                            <label class="form-label">{{ __('Remaining Amount') }}</label>
                                                            <input type="text" class="form-control"
                                                                id="remaining{{ $booking->id }}"
                                                                value="{{ number_format($remaining, 2) }} {{ $booking->currency->code }}"
                                                                readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label"
                                                                for="payment_amount{{ $booking->id }}">{{ __('Payment Amount') }}
                                                                *</label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="payment_amount{{ $booking->id }}"
                                                                    name="payment_amount" min="0.01"
                                                                    max="{{ $remaining }}"
                                                                    data-remaining="{{ $remaining }}"
                                                                    data-currency="{{ $booking->currency->code }}"
                                                                    data-booking-id="{{ $booking->id }}"
                                                                    placeholder="{{ __('Enter amount to pay') }}" required>
                                                                <span
                                                                    class="input-group-text">{{ $booking->currency->code }}</span>
                                                            </div>
                                                            <small class="text-muted">{{ __('Maximum') }}:
                                                                {{ number_format($remaining, 2) }}
                                                                {{ $booking->currency->code }}</small>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label">{{ __('New Remaining Amount') }}</label>
                                                            <input type="text" class="form-control"
                                                                id="new_remaining{{ $booking->id }}"
                                                                value="{{ number_format($remaining, 2) }} {{ $booking->currency->code }}"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                        <button type="submit"
                                                            class="btn btn-primary">{{ __('Update') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">{{ __('No bookings found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Get all payment amount inputs
                const paymentInputs = document.querySelectorAll('[id^="payment_amount"]');

                paymentInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        const bookingId = this.getAttribute('data-booking-id');
                        const remaining = parseFloat(this.getAttribute('data-remaining'));
                        const currency = this.getAttribute('data-currency');
                        const paymentAmount = parseFloat(this.value) || 0;

                        // Calculate new remaining
                        const newRemaining = remaining - paymentAmount;

                        // Update the new remaining field
                        const newRemainingField = document.getElementById('new_remaining' + bookingId);
                        if (newRemainingField) {
                            newRemainingField.value = newRemaining.toFixed(2) + ' ' + currency;

                            // Add visual feedback
                            if (paymentAmount > remaining) {
                                this.classList.add('is-invalid');
                                newRemainingField.classList.add('text-danger');
                            } else {
                                this.classList.remove('is-invalid');
                                newRemainingField.classList.remove('text-danger');
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
