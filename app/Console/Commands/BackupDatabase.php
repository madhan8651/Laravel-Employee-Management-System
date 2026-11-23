<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Throwable;

class BackupDatabase extends Command
{
    protected $signature = 'app:backup-database';

    protected $description = 'Create a timestamped database backup';

    public function handle(): int
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");

        if (! $database) {
            $this->error('Database name is not configured.');

            return self::FAILURE;
        }

        $backupDirectory = base_path('backups');

        File::ensureDirectoryExists($backupDirectory);

        $filename = $database.'_'.now()->format('Ymd_His').'.sql';
        $backupPath = $backupDirectory.DIRECTORY_SEPARATOR.$filename;

        $mysqlDump = 'D:\\xampp\\mysql\\bin\\mysqldump.exe';

        if (! File::exists($mysqlDump)) {
            $this->error('mysqldump.exe was not found: '.$mysqlDump);

            return self::FAILURE;
        }

        $config = config("database.connections.{$connection}");

        $host = $config['host'] ?? '127.0.0.1';
        $port = (string) ($config['port'] ?? 3306);
        $username = $config['username'] ?? '';
        $password = $config['password'] ?? '';

        $process = new Process([
            $mysqlDump,
            '--host='.$host,
            '--port='.$port,
            '--user='.$username,
            '--single-transaction',
            '--routines',
            '--triggers',
            '--events',
            '--result-file='.$backupPath,
            $database,
        ]);

        if ($password !== '') {
            $process->setEnv([
                'MYSQL_PWD' => $password,
            ]);
        }

        try {
            $process->run();

            if (! $process->isSuccessful()) {
                File::delete($backupPath);

                $this->error('Database backup failed.');
                $this->line($process->getErrorOutput());

                return self::FAILURE;
            }

            $this->info('Database backup created successfully.');
            $this->line('File: '.$backupPath);
            $this->line('Size: '.number_format(File::size($backupPath)).' bytes');

            $this->cleanupOldBackups($backupDirectory, $database);

            return self::SUCCESS;
        } catch (Throwable $exception) {
            File::delete($backupPath);

            $this->error('Backup error: '.$exception->getMessage());

            return self::FAILURE;
        }
    }

    private function cleanupOldBackups(string $directory, string $database): void
    {
        $cutoff = now()->subDays(7)->timestamp;

        $pattern = $database.'_????????_??????.sql';

        foreach (File::glob($directory.DIRECTORY_SEPARATOR.$pattern) as $file) {
            if (File::lastModified($file) < $cutoff) {
                File::delete($file);

                $this->warn('Deleted expired backup: '.basename($file));
            }
        }

        $this->info('Backup retention: keeping the latest 7 days.');
    }
}
