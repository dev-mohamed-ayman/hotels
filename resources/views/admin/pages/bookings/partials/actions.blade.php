@php
    $idPrefix = $idPrefix ?? '';
@endphp
<td class="text-nowrap">
    <div class="dropdown">
        <button class="btn btn-sm btn-icon btn-secondary" type="button"
            id="actionsDropdown{{ $idPrefix }}{{ $booking->id }}" data-bs-toggle="dropdown"
            aria-expanded="false" style="padding: 0.25rem 0.5rem;">
            <i class="ti tabler-dots-vertical" style="font-size: 0.9rem;"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end"
            aria-labelledby="actionsDropdown{{ $idPrefix }}{{ $booking->id }}">
            <li>
                <a class="dropdown-item" href="{{ route('bookings.show', $booking) }}">
                    <i class="ti tabler-eye me-2"></i>{{ __('View Details') }}
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('activity-log.booking-history', $booking->id) }}">
                    <i class="ti tabler-history me-2"></i>{{ __('History') }}
                </a>
            </li>
            @can('edit bookings')
                <li>
                    <a class="dropdown-item" href="{{ route('bookings.edit', $booking->id) }}">
                        <i class="ti tabler-edit me-2"></i>{{ __('Edit') }}
                    </a>
                </li>
                <li>
                    <form action="{{ route('bookings.duplicate', $booking->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="ti tabler-copy me-2"></i>{{ __('Duplicate') }}
                        </button>
                    </form>
                </li>
                <li>
                    <form action="{{ route('bookings.toggle-status', $booking->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            @if ($booking->payment_status === 'revised')
                                <i class="ti tabler-calculator me-2"></i>{{ __('Set as Auto') }}
                            @else
                                <i class="ti tabler-refresh me-2"></i>{{ __('Set as Revised') }}
                            @endif
                        </button>
                    </form>
                </li>
                <li>
                    <form action="{{ route('bookings.toggle-payment-list', $booking->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="dropdown-item {{ $booking->in_payment_list ? 'text-warning' : '' }}">
                            @if ($booking->in_payment_list)
                                <i class="ti tabler-playlist-x me-2"></i>{{ __('Remove from Payment List') }}
                            @else
                                <i class="ti tabler-playlist-add me-2"></i>{{ __('Add to Payment List') }}
                            @endif
                        </button>
                    </form>
                </li>
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                        data-bs-target="#hotelPaymentModal{{ $idPrefix }}{{ $booking->id }}">
                        <i class="ti tabler-building me-2"></i>{{ __('Update Hotel Payment') }}
                    </a>
                </li>
            @endcan
            @can('delete bookings')
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form action="{{ route('bookings.destroy', $booking) }}" method="POST"
                        onsubmit="return confirm('{{ __('Are you sure you want to delete this booking?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger w-100 text-start">
                            <i class="ti tabler-trash me-2"></i>{{ __('Delete') }}
                        </button>
                    </form>
                </li>
            @endcan
        </ul>
    </div>
</td>
