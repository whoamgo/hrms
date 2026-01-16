<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class CleanDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean {--confirm : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all data from database tables (except migrations)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('confirm')) {
            if (!$this->confirm('Are you sure you want to delete ALL data from the database? This cannot be undone!')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting database cleanup...');

        try {
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // List of tables to clean (excluding migrations)
            $tables = [
                'activity_logs',
                'attendances',
                'contract_history',
                'departments',
                'designations',
                'employees',
                'leaves',
                'menu_items',
                'payment_disbursements',
                'payslips',
                'permissions',
                'role_menu_item',
                'role_permission',
                'roles',
                'service_records',
                'settings',
                'tada_claims',
                'users',
                'password_reset_tokens',
                'sessions',
            ];

            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->info("Cleaned table: {$table}");
                }
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info('Database cleanup completed successfully!');
            $this->info('You may want to run: php artisan db:seed');

            return 0;
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->error('Error cleaning database: ' . $e->getMessage());
            return 1;
        }
    }
}

