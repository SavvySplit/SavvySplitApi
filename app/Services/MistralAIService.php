<?php

    
namespace App\Services;

use Illuminate\Support\Facades\Http;

class MistralAIService
{
    protected string $baseUrl = 'http://srv797850.hstgr.cloud:11434'; // Update if needed
    protected string $model = 'mistral'; // or 'mistral:7b', depending on your config


    public function analyze(string $ocrText): ?string
{
    try {
        $response = Http::withOptions(['timeout' => 300])->post($this->baseUrl . '/api/generate', [
            'model' => $this->model,
            'prompt' => "You are a helpful code assistant. Generate a valid JSON object with no comments and do not claculate the line items, based on the following OCR text and include document type/classification,
             take into account letter S with an amount is a dollar sign:\n\n" . $ocrText,
            'stream' => false,
        ]);

        if ($response->successful()) {
            logger()->info('Mistral AI request successful', ['response' => $response->body()]);
            return $response->json('response'); 
           //return $response; // Adjust if the API structure differs

            //return $response->body(); // Adjust based on your actual response format
        }

        logger()->warning('Mistral AI request failed', [
            'status' => $response->status(),
            'response' => $response->body(),
        ]);
        return null;

    } catch (\Throwable $e) {
        logger()->error('Mistral AI request exception', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return null;
    }
}


   /*  public function analyze(string $ocrText): ?string
    {
        $response = Http::withOptions(['timeout' => 180])->post($this->baseUrl . '/api/generate', [
            'model' => $this->model,
            'prompt' => "with quick reponse, Analyze the following OCR text and extract relevant structured data:\n\n" . $ocrText,
            //'prompt' => "responde only with extracted structured data, add the category then categorize if it is an invoice, PO or quote from the provided OCR text: \n\n". $ocrText,
            //'promt' => "You are a helpful code assistant. Your task is to generate a valid JSON object based on the provided OCR-like text: \n\n" .$ocrText,
            'stream' => false,
        ]);

        if ($response->successful()) {
           // return $response->json('response'); // Adjust if the API structure differs
           logger()->error('Mistral AI request sucess', ['response' => $response->body()]);
            return $response; // Adjust if the API structure differs

        }

        logger()->error('Mistral AI request failed', ['response' => $response->body()]);
        return null;
    } */
}
