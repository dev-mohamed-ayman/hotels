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
                                                            <label class="form-label">{{ __('Remaining Amount') }}</label>
                                                            <input type="text" class="form-control"
                                                                value="{{ number_format($booking->total_amount - $booking->paid_amount, 2) }} {{ $booking->currency->code }}"
                                                                readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label"
                                                                for="paid_amount{{ $booking->id }}">{{ __('New Paid Amount') }}</label>
                                                            <input type="number" step="0.01" class="form-control"
                                                                id="paid_amount{{ $booking->id }}" name="paid_amount"
                                                                value="{{ $booking->paid_amount }}" required>
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
@endsection
