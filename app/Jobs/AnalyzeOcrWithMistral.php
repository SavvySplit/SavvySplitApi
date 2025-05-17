<?php

namespace App\Jobs;

use App\Models\Email;
use App\Services\MistralAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Foundation\Bus\Dispatchable;

class AnalyzeOcrWithMistral implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Email $email;
    public $timeout = 300; // 5 minutes
    //public $tries = 1;
    //public $maxExceptions = 1;


    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function handle(MistralAIService $aiService): void
    {
        if (empty($this->email->ocr_text)) {
            logger()->warning('No OCR text for Email ID: ' . $this->email->id);
            return;
        }

        $result = $aiService->analyze($this->email->ocr_text);

        logger()->error("Ai Service Result: {$result}");


        if ($result) {
            $this->email->update([
                'ai_result' => $result,
                //'ai_result' => json_encode(['content' => 'result']),

            ]);
            logger()->info("AI analysis complete for Email ID: {$this->email->id}");
        } else {
            logger()->error("AI analysis failed for Email ID: {$this->email->id}");
        }
    }
}
