<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Email;

class DashboardController extends Controller
{
    public function index()
    {
        // Example: count emails per day
        return"test";
        $stats = Email::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

            // Recent 5 emails
        $recentEmails = Email::orderBy('created_at', 'desc')
        // ->take(5)
        ->get(['id', 'subject', 'from', 'created_at', 'attachments']);

        // Summary metrics (mock for now — replace with real model queries)
        $summary = [
            'documents' => 12, // example: Document::count()
            //'unread'    => Email::where('is_read', false)->count(),
            'unread'    => 100,
            'items'     => 34, // example: Item::count()
        ];



        return Inertia::render('dashboard', [
            'emailStats' => [
                'labels' => $stats->pluck('date')->map(fn($d) => date('M d', strtotime($d))),
                'data' => $stats->pluck('count'),
            ],
            'recentEmails' => $recentEmails,
            'summary'      => $summary,

        ]);
    }
}
