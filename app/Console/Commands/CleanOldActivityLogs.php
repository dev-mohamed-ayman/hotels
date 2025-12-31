<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;

class CleanOldActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity-log:clean {--days=30 : Number of days to keep}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old activity logs older than specified days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Cleaning activity logs older than {$days} days (before {$cutoffDate->format('Y-m-d H:i:s')})...");

        $count = Activity::where('created_at', '<', $cutoffDate)->count();

        if ($count === 0) {
            $this->info('No old activity logs found to clean.');
            return;
        }

        if ($this->confirm("This will delete {$count} activity log records. Do you want to continue?")) {
            $deleted = Activity::where('created_at', '<', $cutoffDate)->delete();
            $this->info("Successfully deleted {$deleted} old activity log records.");
        } else {
            $this->info('Operation cancelled.');
        }
    }
}