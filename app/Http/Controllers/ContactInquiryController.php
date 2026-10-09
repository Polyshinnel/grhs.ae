<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactInquiryRequest;
use App\Services\TelegramNotificationService;
use Illuminate\Http\JsonResponse;

class ContactInquiryController extends Controller
{
    public function contact(ContactInquiryRequest $request, TelegramNotificationService $telegram): JsonResponse
    {
        return $this->send($request, $telegram, 'Contact Us', [
            'Name' => $request->validated('name'),
            'Email' => $request->validated('email'),
            'Phone' => $request->validated('phone'),
            'Message' => $request->validated('message'),
        ]);
    }

    public function quick(ContactInquiryRequest $request, TelegramNotificationService $telegram): JsonResponse
    {
        return $this->send($request, $telegram, 'Quick Contact', [
            'Name' => $request->validated('name'),
            'Email' => $request->validated('email'),
            'Phone' => $request->validated('phone'),
        ]);
    }

    /** @param array<string, string|null> $fields */
    private function send(ContactInquiryRequest $request, TelegramNotificationService $telegram, string $source, array $fields): JsonResponse
    {
        if (filled($request->validated('website'))) {
            return response()->json([
                'message' => "We couldn't send your message right now. Please try again or contact us directly.",
            ], 422);
        }

        $siteUrl = rtrim((string) config('app.url'), '/');
        $pageUrl = $source === 'Contact Us' ? $siteUrl.'/contacts' : $siteUrl;

        if (! $telegram->send($source, $fields, $pageUrl)) {
            return response()->json([
                'message' => "We couldn't send your message right now. Please try again or contact us directly.",
            ], 503);
        }

        return response()->json([
            'message' => 'Thank you! Your message has been sent successfully.',
        ]);
    }
}
