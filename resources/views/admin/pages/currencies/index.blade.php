@extends('admin.layouts.app')

@section('title', __('Currencies'))

@section('content')
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Currencies List') }}</h5>
            <a href="{{ route('currencies.create') }}" class="btn btn-primary">
                <i class="ti tabler-plus me-2"></i>{{ __('Add Currency') }}
            </a>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table border-top table-bordered table-hover table-sm text-center">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Symbol') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($currencies as $currency)
                        <tr>
                            <td>{{ $currency->name }}</td>
                            <td><span class="badge bg-label-primary">{{ $currency->code }}</span></td>
                            <td>{{ $currency->symbol }}</td>
                            <td>
                                @if ($currency->is_active)
                                    <span class="badge bg-label-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-label-danger">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                    <a href="{{ route('currencies.edit', $currency->id) }}" class="btn btn-sm btn-icon btn-success text-white"
                                        data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                        <i class="ti tabler-edit ti-sm"></i>
                                    </a>
                                    <form action="{{ route('currencies.destroy', $currency->id) }}" method="POST"
                                        class="d-inline-block" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-sm btn-icon btn-danger text-white"
                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                            <i class="ti tabler-trash ti-sm"></i>
                                        </button>
                                    </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <p class="mb-0">{{ __('No currencies found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
