<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use App\Models\Email;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Spatie\PdfToImage\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Jobs\AnalyzeOcrWithMistral;



class ReadInbox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:read-inbox';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ensure Ghostscript (gs) is available when this command runs
        ////////putenv('PATH=' . getenv('PATH') . ':/opt/homebrew/bin');

        $client = Client::account('default');
        //dd("test");
        $client->connect();

        $folder = $client->getFolder('INBOX');
        $messages = $folder->messages()->unseen()->get();

        foreach ($messages as $message) {
            $subject = $message->getSubject();
            $from = $message->getFrom()[0]->mail;
            $body = $message->getTextBody();

            $attachmentsList = [];
            $ocrText = '';

            $attachments = $message->getAttachments();

            foreach ($attachments as $attachment) {
                $fileName = uniqid() . '_' . $attachment->getName();
                $filePath = storage_path('app/public/email_attachments/' . $fileName);

                file_put_contents($filePath, $attachment->getContent());
                $attachmentsList[] = $fileName;

                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                try {
                    $escapedPath = escapeshellarg($filePath);
                    $pythonScript = base_path('/ocr.py');
                    $escapedScript = escapeshellarg($pythonScript);

                    $command = "/root/ocr-env/bin/python3 $escapedScript $escapedPath";

                    // Debug output
                    logger()->info("Running OCR command: $command");

                    $output = shell_exec($command . " 2>&1");  // Capture errors too
                    logger()->info("OCR output: " . $output);

                    if ($output === null || trim($output) === '') {
                        $ocrText .= "\n[Error: OCR script failed to execute or returned no output]";
                    } else {
                        $result = json_decode($output, true);
                        if (isset($result['error'])) {
                            $ocrText .= "\n[OCR Error: " . $result['error'] . "]";
                        } else {
                            $ocrText .= "\n--- OCR Result: " . basename($filePath) . " ---\n" . $result['text'] . "\n";
                        }
                    }
                } catch (\Exception $e) {
                    $ocrText .= "\n[Error processing " . basename($filePath) . "]: " . $e->getMessage();
                }

                /*  try {
                    if( $extension == "pdf"){
                        $pdf = new Pdf($filePath);
                        $pageCount = $pdf->getNumberOfPages();
            
    
                        for ($page = 1; $page <= $pageCount; $page++) {
                            $tempImage = storage_path("app/public/email_attachments/tmp/page-{$page}.jpg");
                            $pdf->setPage($page)->saveImage($tempImage);
                            $ocrText .= "--- Page {$page} ---\n";
                            $ocrText .= (new TesseractOCR($tempImage))->lang('eng')->run();
                           // unlink($tempImage);
                        }
                    }
                    else {
                        $text = (new TesseractOCR($filePath))->lang('eng')->run();
                        $ocrText .= "\n--- From: " . basename($filePath) . " ---\n" . $text . "\n";
                    }
                    
                } catch (\Exception $e) {
                    $ocrText .= "\n[Error processing " . basename($filePath) ."\n\n ". $e."]";
                } */
            }

            // 👉 Save email and attachments in database
            $messageId = $message->getMessageId();

            if (!Email::where('message_id', $messageId)->exists()) {
                $email = Email::create([
                    'message_id' => $messageId,
                    'subject' => $subject,
                    'from' => $from,
                    'body' => $body,
                    'attachments' => json_encode($attachmentsList),
                    'ocr_text' => $ocrText
                ]);

                logger()->info("Email saved: {$subject}");

                $message->setFlag('Seen');
                AnalyzeOcrWithMistral::dispatch($email);
            } else {
                logger()->info("Duplicate email skipped: {$subject}");
            }

            /* $email = Email::create([
                'subject' => $subject,
                'from' => $from,
                'body' => $body,
                'attachments' => json_encode($attachmentsList),
                'ocr_text' => $ocrText
            ]);

            logger()->info("Email saved: {$subject}");

            $message->setFlag('Seen');
            AnalyzeOcrWithMistral::dispatch($email); */
        }

        return 0;
    }
}
