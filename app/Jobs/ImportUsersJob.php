<?php

namespace App\Jobs;

use App\Imports\UsersImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ImportUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $fileUserImport;

    /**
     * Create a new job instance.
     */
    public function __construct($fileUserImport)
    {
        $this->fileUserImport = $fileUserImport;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Excel::import(new UsersImport, $this->fileUserImport);
    }
}
