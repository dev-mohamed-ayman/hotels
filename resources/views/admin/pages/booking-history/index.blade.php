@extends('admin.layouts.app')

@section('title', __('Booking History'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="ti tabler-calendar-history me-2"></i>
                        {{ __('Booking History') }}
                    </h5>
                    @can('delete bookings')
                        <button type="button" class="btn btn-danger btn-sm" onclick="bulkDelete()">
                            <i class="ti tabler-trash me-1"></i>{{ __('Delete Selected') }}
                        </button>
                    @endcan
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Filters --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            <form action="{{ route('booking-history.index') }}" method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Booking') }}</label>
                                    <select name="booking_id" class="form-select">
                                        <option value="">{{ __('All Bookings') }}</option>
                                        @foreach($bookings as $booking)
                                            <option value="{{ $booking->id }}" {{ request('booking_id') == $booking->id ? 'selected' : '' }}>
                                                {{ $booking->code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('User') }}</label>
                                    <select name="user_id" class="form-select">
                                        <option value="">{{ __('All Users') }}</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('Event') }}</label>
                                    <select name="event" class="form-select">
                                        <option value="">{{ __('All Events') }}</option>
                                        @foreach($events as $event)
                                            <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>
                                                {{ ucfirst($event) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('Date From') }}</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('Date To') }}</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti tabler-search me-2"></i>{{ __('Search') }}
                                    </button>
                                    <a href="{{ route('booking-history.index') }}" class="btn btn-secondary">
                                        <i class="ti tabler-refresh me-2"></i>{{ __('Reset') }}
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if($activities->isEmpty())
                        <div class="text-center py-5">
                            <i class="ti tabler-history" style="font-size: 3rem; color: #cbd5e0;"></i>
                            <p class="mt-3 text-muted">{{ __('No booking history records found') }}</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" class="form-check-input" id="selectAll" onchange="toggleAll(this)">
                                        </th>
                                        <th>{{ __('Date & Time') }}</th>
                                        <th>{{ __('Booking Code') }}</th>
                                        <th>{{ __('User') }}</th>
                                        <th>{{ __('Action') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activities as $activity)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input activity-checkbox" value="{{ $activity->id }}">
                                            </td>
                                            <td class="text-nowrap">
                                                {{ $activity->created_at->format('Y-m-d H:i:s') }}
                                            </td>
                                            <td>
                                                @if($activity->subject)
                                                    <a href="{{ route('bookings.show', $activity->subject_id) }}" class="text-primary fw-bold">
                                                        {{ $activity->subject->code }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($activity->causer)
                                                    <strong>{{ $activity->causer->name }}</strong>
                                                @else
                                                    <span class="text-muted">{{ __('System') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $activity->event == 'created' ? 'success' : ($activity->event == 'deleted' ? 'danger' : 'primary') }}">
                                                    {{ ucfirst($activity->event) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $activity->description }}
                                            </td>
                                            <td class="text-nowrap">
                                                <a href="{{ route('booking-history.show', $activity->subject_id) }}" class="btn btn-sm btn-primary">
                                                    <i class="ti tabler-eye me-1"></i>{{ __('View') }}
                                                </a>
                                                @can('delete bookings')
                                                    <form action="{{ route('booking-history.destroy', $activity->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure you want to delete this history record?') }}')">
                                                            <i class="ti tabler-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{ $activities->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleAll(checkbox) {
            const checkboxes = document.querySelectorAll('.activity-checkbox');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
        }

        function bulkDelete() {
            const checkboxes = document.querySelectorAll('.activity-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('{{ __('Please select at least one record to delete') }}');
                return;
            }

            if (!confirm('{{ __('Are you sure you want to delete the selected records?') }}')) {
                return;
            }

            const ids = Array.from(checkboxes).map(cb => cb.value);

            fetch('{{ route('booking-history.bulk-delete') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || '{{ __('An error occurred') }}');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __('An error occurred') }}');
            });
        }
    </script>
    @endpush
@endsection
