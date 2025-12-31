@php
    $recentActivities = \Spatie\Activitylog\Models\Activity::with(['causer'])
        ->latest()
        ->take(5)
        ->get();
@endphp

@if($recentActivities->count() > 0)
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('activity.recent_activities') }}</h5>
        @can('view activity log')
            <a href="{{ route('activity-log.index') }}" class="btn btn-sm btn-outline-primary">
                {{ __('activity.view_all') }}
            </a>
        @endcan
    </div>
    <div class="card-body">
        <div class="timeline">
            @foreach($recentActivities as $activity)
                <div class="timeline-item">
                    <div class="timeline-marker">
                        @php
                            $eventColors = [
                                'created' => 'success',
                                'updated' => 'warning',
                                'deleted' => 'danger',
                                'login' => 'info',
                                'logout' => 'secondary'
                            ];
                            $color = $eventColors[$activity->event] ?? 'primary';
                        @endphp
                        <span class="timeline-marker-icon bg-{{ $color }}">
                            @switch($activity->event)
                                @case('created')
                                    <i class="ti ti-plus"></i>
                                    @break
                                @case('updated')
                                    <i class="ti ti-edit"></i>
                                    @break
                                @case('deleted')
                                    <i class="ti ti-trash"></i>
                                    @break
                                @case('login')
                                    <i class="ti ti-login"></i>
                                    @break
                                @case('logout')
                                    <i class="ti ti-logout"></i>
                                    @break
                                @default
                                    <i class="ti ti-info-circle"></i>
                            @endswitch
                        </span>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <h6 class="timeline-title">{{ $activity->description }}</h6>
                            <small class="text-muted">
                                {{ $activity->created_at->diffForHumans() }}
                            </small>
                        </div>
                        @if($activity->causer)
                            <div class="timeline-body">
                                <small class="text-muted">
                                    {{ __('activity.caused_by') }}: {{ $activity->causer->name }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 0;
}

.timeline-item {
    position: relative;
    padding-left: 40px;
    margin-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 30px;
    bottom: -20px;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
}

.timeline-marker-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    color: white;
    font-size: 12px;
}

.timeline-content {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 12px;
    border-left: 3px solid #dee2e6;
}

.timeline-header {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    margin-bottom: 5px;
}

.timeline-title {
    font-size: 14px;
    margin: 0;
    flex: 1;
    margin-right: 10px;
}

.timeline-body {
    font-size: 12px;
}
</style>
@endpush