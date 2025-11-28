@extends('admin.layouts.app')

@section('title', __('Hotels'))

@section('content')
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Hotels List') }}</h5>
            <a href="{{ route('hotels.create') }}" class="btn btn-primary">
                <i class="ti tabler-plus me-2"></i>{{ __('Add Hotel') }}
            </a>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table border-top table-bordered table-hover table-sm text-center">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Address') }}</th>
                        <th>{{ __('Bank Accounts') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hotels as $hotel)
                        <tr>
                            <td>{{ $hotel->name }}</td>
                            <td>{{ $hotel->address }}</td>
                            <td>
                                @if ($hotel->bankAccounts->count() > 0)
                                    <span class="badge bg-label-success">{{ $hotel->bankAccounts->count() }}</span>
                                @else
                                    <span class="badge bg-label-secondary">0</span>
                                @endif
                            </td>
                            <td>
                                @if ($hotel->is_active)
                                    <span class="badge bg-label-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-label-danger">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('hotels.edit', $hotel->id) }}"
                                    class="btn btn-sm btn-icon btn-success text-white" data-bs-toggle="tooltip"
                                    title="{{ __('Edit') }}">
                                    <i class="ti tabler-edit ti-sm"></i>
                                </a>
                                <form action="{{ route('hotels.destroy', $hotel->id) }}" method="POST"
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
                            <td colspan="5" class="text-center py-5">
                                <p class="mb-0">{{ __('No hotels found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($hotels->hasPages())
            <div class="card-footer">
                {{ $hotels->links() }}
            </div>
        @endif
    </div>
@endsection
