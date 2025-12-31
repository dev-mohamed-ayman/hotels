@extends('admin.layouts.app')

@section('title', __('activity.activity_log'))

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
                        <h5 class="card-title mb-0">{{ __('activity.activity_log') }}</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-label-primary" data-bs-toggle="modal"
                                data-bs-target="#filterModal">
                                <i class="ti tabler-filter me-1"></i> {{ __('activity.filter') }}
                            </button>
                            <a href="{{ route('activity-log.export', request()->query()) }}" class="btn btn-label-success">
                                <i class="ti tabler-download me-1"></i> {{ __('activity.export') }}
                            </a>
                            <button type="button" class="btn btn-label-danger" id="bulkDeleteBtn" style="display: none;">
                                <i class="ti tabler-trash me-1"></i> {{ __('activity.delete_selected') }}
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive text-nowrap">
                        @if ($activities->count() > 0)
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40" class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                            </div>
                                        </th>
                                        <th>{{ __('activity.user') }}</th>
                                        <th>{{ __('activity.activity') }}</th>
                                        <th>{{ __('activity.type') }}</th>
                                        <th>{{ __('activity.description') }}</th>
                                        <th>{{ __('activity.date_time') }}</th>
                                        <th class="text-center">{{ __('activity.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($activities as $activity)
                                        <tr>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input activity-checkbox" type="checkbox"
                                                        value="{{ $activity->id }}">
                                                </div>
                                            </td>
                                            <td>
                                                @if ($activity->causer)
                                                    <div class="d-flex justify-content-start align-items-center user-name">
                                                        <div class="avatar-wrapper">
                                                            <div class="avatar avatar-sm me-3">
                                                                <span
                                                                    class="avatar-initial rounded-circle bg-label-primary">
                                                                    {{ substr($activity->causer->name, 0, 1) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex flex-column">
                                                            <span
                                                                class="fw-semibold text-heading text-truncate">{{ $activity->causer->name }}</span>
                                                            <small
                                                                class="text-muted">{{ $activity->causer->email }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span
                                                        class="badge bg-label-secondary">{{ __('activity.system') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $eventColors = [
                                                        'created' => 'success',
                                                        'updated' => 'warning',
                                                        'deleted' => 'danger',
                                                        'login' => 'info',
                                                        'logout' => 'secondary',
                                                    ];
                                                    $color = $eventColors[$activity->event] ?? 'primary';
                                                @endphp
                                                <span
                                                    class="badge rounded-pill bg-label-{{ $color }} text-capitalize">{{ $activity->event }}</span>
                                            </td>
                                            <td>
                                                @if ($activity->subject_type)
                                                    <span class="badge rounded-pill bg-label-info">
                                                        {{ class_basename($activity->subject_type) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-truncate" style="max-width: 250px;"
                                                        title="{{ $activity->description }}">
                                                        {{ $activity->description }}
                                                    </span>
                                                    @if ($activity->properties && count($activity->properties) > 0)
                                                        <small class="text-muted cursor-pointer"
                                                            title="{{ __('activity.contains_additional_details') }}">
                                                            <i class="ti tabler-info-circle fs-6 align-text-bottom"></i>
                                                            {{ __('activity.has_details') }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span
                                                        class="fw-semibold">{{ $activity->created_at->format('Y-m-d') }}</span>
                                                    <small
                                                        class="text-muted">{{ $activity->created_at->format('H:i:s') }}</small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-inline-block">
                                                    <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow"
                                                        data-bs-toggle="dropdown">
                                                        <i class="ti tabler-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end m-0">
                                                        <li>
                                                            <a href="{{ route('activity-log.show', $activity) }}"
                                                                class="dropdown-item">
                                                                <i class="ti tabler-eye me-1"></i>
                                                                {{ __('activity.view_details') }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('activity-log.destroy', $activity) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger"
                                                                    onclick="return confirm('{{ __('activity.confirm_delete_record') }}')">
                                                                    <i class="ti tabler-trash me-1"></i>
                                                                    {{ __('activity.delete') }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="card-footer d-flex justify-content-end align-items-center">
                                {{ $activities->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="p-5 text-center">
                                <div class="mb-3">
                                    <span class="avatar avatar-xl rounded-circle bg-label-primary p-3">
                                        <i class="ti tabler-history fs-1"></i>
                                    </span>
                                </div>
                                <h4 class="mb-2">{{ __('activity.no_activities_found') }}</h4>
                                <p class="mb-0 text-muted">{{ __('activity.no_activities_message') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-transparent">
                    <h5 class="modal-title">{{ __('activity.filter_activity_log') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="GET" action="{{ route('activity-log.index') }}">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-12">
                                <label class="form-label">{{ __('activity.user') }}</label>
                                <select name="user_id" class="form-select">
                                    <option value="">{{ __('activity.all_users') }}</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">{{ __('activity.from_date') }}</label>
                                <input type="text" name="date_from" class="form-control flatpickr"
                                    value="{{ request('date_from') }}" placeholder="YYYY-MM-DD">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">{{ __('activity.to_date') }}</label>
                                <input type="text" name="date_to" class="form-control flatpickr"
                                    value="{{ request('date_to') }}" placeholder="YYYY-MM-DD">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('activity-log.index') }}"
                            class="btn btn-label-secondary">{{ __('activity.clear_filters') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('activity.apply_filters') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Flatpickr
            if (typeof flatpickr !== 'undefined') {
                flatpickr(".flatpickr", {
                    dateFormat: "Y-m-d", // Value sent to server
                    altInput: true, // Display readable format
                    altFormat: "Y-m-d", // Display format
                    allowInput: true,
                    static: true // Important for modals
                });
            }

            const selectAllCheckbox = document.getElementById('selectAll');
            const activityCheckboxes = document.querySelectorAll('.activity-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

            // Select all functionality
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    activityCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    toggleBulkDeleteButton();
                });
            }

            // Individual checkbox change
            activityCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checkedCount = document.querySelectorAll('.activity-checkbox:checked')
                        .length;
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = checkedCount === activityCheckboxes.length;
                        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount <
                            activityCheckboxes.length;
                    }
                    toggleBulkDeleteButton();
                });
            });

            function toggleBulkDeleteButton() {
                const checkedCount = document.querySelectorAll('.activity-checkbox:checked').length;
                if (bulkDeleteBtn) {
                    bulkDeleteBtn.style.display = checkedCount > 0 ? 'inline-block' : 'none';
                }
            }

            // Bulk delete functionality
            if (bulkDeleteBtn) {
                bulkDeleteBtn.addEventListener('click', function() {
                    const checkedIds = Array.from(document.querySelectorAll('.activity-checkbox:checked'))
                        .map(checkbox => checkbox.value);

                    if (checkedIds.length === 0) {
                        alert('{{ __('activity.select_records_to_delete') }}');
                        return;
                    }

                    const confirmMessage =
                        '{{ __('activity.confirm_delete_records', ['count' => 'COUNT_PLACEHOLDER']) }}'
                        .replace('COUNT_PLACEHOLDER', checkedIds.length);
                    if (!confirm(confirmMessage)) {
                        return;
                    }

                    fetch('{{ route('activity-log.bulk-delete') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                            body: JSON.stringify({
                                ids: checkedIds
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('{{ __('activity.delete_error') }}');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('{{ __('activity.delete_error') }}');
                        });
                });
            }
        });
    </script>
@endsection

@section('styles')
    <style>
        .avatar {
            width: 32px;
            height: 32px;
        }

        .avatar-initial {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
        }

        .activity-description {
            max-width: 300px;
            word-wrap: break-word;
        }

        .table td {
            vertical-align: middle;
        }

        .btn-group .btn {
            margin-right: 2px;
        }

        /* Ensure flatpickr is above the modal */
        .flatpickr-calendar {
            z-index: 10000 !important;
        }
    </style>
@endsection
