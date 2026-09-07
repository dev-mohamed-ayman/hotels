@php
    $idPrefix = $idPrefix ?? '';
@endphp
<!-- Hotel Payment Update Modal -->
<div class="modal fade" id="hotelPaymentModal{{ $idPrefix }}{{ $booking->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Update Hotel Payment') }} - {{ $booking->code }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('bookings.update-hotel-payment', $booking) }}" method="POST"
                onsubmit="return confirmHotelOverpayment(this)">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Total Net Amount') }}</label>
                        <input type="text" class="form-control"
                            value="{{ $booking->net_amount == 0 ? '' : \App\Helpers\NumberHelper::format($booking->net_amount) }} {{ $booking->currency->symbol }}"
                            readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Current Paid to Hotel') }}</label>
                        <input type="text" class="form-control"
                            value="{{ $booking->hotel_paid_amount == 0
                                ? ''
                                : \App\Helpers\NumberHelper::format($booking->hotel_paid_amount) . ' ' . $booking->currency->symbol }}"
                            readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"
                            for="hotel_paid_amount{{ $idPrefix }}{{ $booking->id }}">{{ __('Set New Paid to Hotel Amount') }}
                            *</label>
                        <div class="input-group">
                            <input type="number" step="any" class="form-control"
                                id="hotel_paid_amount{{ $idPrefix }}{{ $booking->id }}"
                                name="hotel_paid_amount" data-net-amount="{{ $booking->net_amount }}"
                                data-currency="{{ $booking->currency->symbol }}"
                                data-booking-id="{{ $idPrefix }}{{ $booking->id }}"
                                value="{{ $booking->hotel_paid_amount }}"
                                placeholder="{{ __('Enter total paid amount') }}" required>
                            <span class="input-group-text">{{ $booking->currency->symbol }}</span>
                        </div>
                        <small
                            class="text-muted">{{ __('Enter the total amount paid to the hotel. This will replace the current paid amount.') }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('New Remaining Amount') }}</label>
                        <input type="text" class="form-control"
                            id="new_hotel_remaining{{ $idPrefix }}{{ $booking->id }}"
                            value="{{ \App\Helpers\NumberHelper::format($booking->net_amount - $booking->hotel_paid_amount) . ' ' . $booking->currency->symbol }}"
                            readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
