<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function store(StoreContactRequest $request): JsonResponse
    {
        // Honeypot: bots fill the hidden checkbox. Pretend success, store nothing.
        if ($request->filled('botcheck')) {
            return response()->json(['message' => 'Received.'], 201);
        }

        Contact::create([
            ...$request->safe()->except('botcheck'),
            'status' => Contact::STATUS_NEW,
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 500),
        ]);

        return response()->json(['message' => 'Received.'], 201);
    }
}
