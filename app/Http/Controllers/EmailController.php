<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Inertia\Inertia;
use App\Services\MistralAIService;

class EmailController extends Controller
{


    public function show($id) {

    $email = Email::findOrFail($id);

    //dd($email->attachments);
    $attachments = json_decode($email->attachments ?? '[]', true);

    return Inertia::render('Emails/Show', [
        'email' => [
            'id' => $email->id,
            'subject' => $email->subject,
            'from' => $email->from,
            'to' => $email->to,
            'body' => $email->body,
            'ocr_text' => $email->ocr_text,
            'ai_result' => $email->ai_result, // or json_decode if stored as JSON string            
            'created_at' => $email->created_at->toDateTimeString(),
            'attachments' => collect($attachments)->map(function ($att) {
                return [
                    'filename' => $att['filename'] ?? basename($att),
                    'url' => route('attachments.download', ['filename' =>basename($att)]),
                ];
            }),
        ]
    ]);
}

public function analyzeOCROLD($emailId, MistralAIService $aiService)
{
    $email = Email::findOrFail($emailId);

    if (!$email->ocr_text) {
        return response()->json(['error' => 'No OCR text available.'], 400);
    }

    $result = $aiService->analyze($email->ocr_text);

    if ($result) {
        $email->ai_result = $result;
        $email->save();
        return response()->json(['success' => true, 'data' => $result]);
    }

    return response()->json(['error' => 'AI analysis failed.'], 500);
}
   /*  public function show(Email $email)
    {
        return Inertia::render('Show', [
            'email' => $email->load('attachments'),
        ]);
    } */
}
