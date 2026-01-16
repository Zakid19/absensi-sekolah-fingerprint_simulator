<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Databse di Backup ke storage/app/backups';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timestamp = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d_H-i-s');
        $fileName = "backup_{$timestamp}.sql";
        $path = storage_path("app/backups/{$fileName}");

        $db = config('database.connections.mysql');

        $user = $db['username'];
        $pass = $db['password'];
        $host = $db['host'];
        $port = $db['port'];
        $name = $db['database'];
        $dump = env('MYSQLDUMP_PATH', 'mysqldump');

        $cmd = "\"{$dump}\" -u{$user}";

        if (!empty($pass)) {
            $cmd .= " -p{$pass}";
        }

        $cmd .= " -h{$host} -P{$port} {$name} > \"{$path}\"";

        $this->info("Running: {$cmd}");

        exec($cmd, $output, $return);

        if ($return !== 0) {
            $this->error('Backup gagal');
            return Command::FAILURE;
        }

        $this->info("Backup berhasil: {$fileName}");
        return Command::SUCCESS;
    }
}
