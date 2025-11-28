<?php

namespace App\Jobs;

use App\Models\SearchLog;
use App\Models\SearchTerm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class LogSearchHistory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected array $logData
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Ghi log chi tiết
        SearchLog::create($this->logData);

        // 2. Cập nhật bảng tổng hợp (Upsert)
        SearchTerm::updateOrInsert(
            ['term' => $this->logData['keyword']],
            [
                'hits' => DB::raw('hits + 1'),
                'last_searched_at' => now()
            ]
        );
    }
}
