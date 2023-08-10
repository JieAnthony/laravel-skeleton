<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Foundation\Console\DownCommand as LaravelDownCommand;
use Illuminate\Foundation\Events\MaintenanceModeEnabled;

class DownCommand extends LaravelDownCommand
{
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            if ($this->laravel->maintenanceMode()->active()) {
                $this->components->info('Application is already down.');

                return 0;
            }

            $this->laravel->maintenanceMode()->activate($this->getDownFilePayload());

            file_put_contents(
                storage_path('framework/maintenance.php'),
                file_get_contents(base_path('/stubs/maintenance-mode.stub'))
            );

            $this->laravel->get('events')->dispatch(new MaintenanceModeEnabled());

            $this->components->info('Application is now in maintenance mode.');
        } catch (Exception $e) {
            $this->components->error(sprintf(
                'Failed to enter maintenance mode: %s.',
                $e->getMessage(),
            ));

            return 1;
        }

        return 0;
    }

    protected function getDownFilePayload()
    {
        return [
            'except' => $this->excludedPaths(),
            'redirect' => $this->redirectPath(),
            'retry' => $this->getRetryTime(),
            'refresh' => $this->option('refresh'),
            'secret' => $this->option('secret'),
            'status' => (int) $this->option('status', 503),
            'template' => json_encode(['code' => 503, 'message' => '服务暂时不可用', 'data' => null], JSON_UNESCAPED_UNICODE),
        ];
    }
}
