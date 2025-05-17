<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Webklex\IMAP\Facades\Client;
use App\Models\Email;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Spatie\PdfToImage\Pdf;
use Illuminate\Support\Facades\Http;





Route::get('/ocrtest', function () {

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

            $extension = strtolower(pathinfo($filePath , PATHINFO_EXTENSION));

            
            try {
                if( $extension == "pdf"){
                    $pdf = new Pdf($filePath);
                    $pageCount = $pdf->getNumberOfPages();
        

                    for ($page = 1; $page <= $pageCount; $page++) {
                        $tempImage = storage_path("app/public/tmp/page-{$page}.jpg");
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
                $ocrText .= "\n[Error processing " . basename($filePath) . "]";
            }

        }

        // 👉 Save email and attachments in database
        Email::create([
            'subject' => $subject,
            'from' => $from,
            'body' => $body,
            'attachments' => json_encode($attachmentsList),
            'ocr_text' => $ocrText
        ]);


        $message->setFlag('Seen');
    }

    return 0;
    
})->name('test');

Route::get('/d', function () {

    return Inertia::render('welcome');
})->name('home');


Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
//Route::get('/emails/{email}', [EmailController::class, 'show'])->name('emails.show');


// Show individual email
Route::get('/emails/{id}', [EmailController::class, 'show'])->name('emails.show');

// Download attachment from email
Route::get('/attachments/download', function (\Illuminate\Http\Request $request) {
    $disk = Storage::disk('public');
    $filename = $request->query('filename');

    if (!$filename || !$disk->exists("email_attachments/". $filename)) {
        abort(404, 'File not found.');
    }

    //return Storage::disk('public')->download($filename);
    return Storage::disk('public')->download("email_attachments/". $filename);


})->name('attachments.download');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
