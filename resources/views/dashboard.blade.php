<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <h1 class="text-3xl font-bold mb-6">📬 Email Dashboard</h1>

    <div class="space-y-4">
        @foreach ($emails as $email)
            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-xl font-semibold">{{ $email->subject ?? '(No Subject)' }}</h2>
                <p class="text-gray-600">From: {{ $email->from }}</p>
                <p class="mt-2">{{ Str::limit($email->body, 150) }}</p>

                @if($email->attachments)
                    <div class="mt-4">
                        <h4 class="font-bold">Attachments:</h4>
                        <ul class="list-disc ml-6">
                            @foreach (json_decode($email->attachments, true) as $file)
                                <li>
                                    <a href="{{ asset('storage/' . $file) }}" class="text-blue-500 underline" target="_blank">{{ basename($file) }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $emails->links() }}
    </div>
</body>
</html>
