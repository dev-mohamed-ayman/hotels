@extends('admin.layouts.app')

@section('title', __('Customers'))

@section('content')
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Customers List') }}</h5>
            <a href="{{ route('customers.create') }}" class="btn btn-primary">
                <i class="ti tabler-plus me-2"></i>{{ __('Add Customer') }}
            </a>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table border-top table-bordered table-hover table-sm text-center">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Phone 1') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Priority') }}</th>
                        <th>{{ __('Interested Hotels') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->phone_1 }}</td>
                            <td>{{ $customer->email ?? '-' }}</td>
                            <td>
                                @if ($customer->type == 'individual')
                                    <span class="badge bg-label-primary">{{ __('Individual') }}</span>
                                @else
                                    <span class="badge bg-label-info">{{ __('Corporate') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($customer->status == 'potential')
                                    <span class="badge bg-label-warning">{{ __('Potential') }}</span>
                                @elseif ($customer->status == 'active')
                                    <span class="badge bg-label-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-label-danger">{{ __('Cancelled') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($customer->priority == 'low')
                                    <span class="badge bg-label-secondary">{{ __('Low') }}</span>
                                @elseif ($customer->priority == 'medium')
                                    <span class="badge bg-label-info">{{ __('Medium') }}</span>
                                @elseif ($customer->priority == 'high')
                                    <span class="badge bg-label-warning">{{ __('High') }}</span>
                                @else
                                    <span class="badge bg-label-danger">{{ __('Urgent') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($customer->hotels->count() > 0)
                                    <span class="badge bg-label-success">{{ $customer->hotels->count() }}</span>
                                @else
                                    <span class="badge bg-label-secondary">0</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('customers.edit', $customer->id) }}"
                                    class="btn btn-sm btn-icon btn-success text-white" data-bs-toggle="tooltip"
                                    title="{{ __('Edit') }}">
                                    <i class="ti tabler-edit ti-sm"></i>
                                </a>
                                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST"
                                    class="d-inline-block" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-danger text-white"
                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                        <i class="ti tabler-trash ti-sm"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <p class="mb-0">{{ __('No customers found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($customers->hasPages())
            <div class="card-footer">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
@endsection
