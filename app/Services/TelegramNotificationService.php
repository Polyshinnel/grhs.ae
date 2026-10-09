<?php

namespace App\Services;

use App\Models\TelegramSettings;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramNotificationService
{
    /** @param array<string, string|null> $fields */
    public function send(string $source, array $fields, string $pageUrl): bool
    {
        $token = config('services.telegram.bot_token');
        $settings = TelegramSettings::query()->find(1);

        if (! is_string($token) || $token === '') {
            $this->logFailure('missing_token', $source);

            return false;
        }

        if (! is_string($settings?->chat_id) || ! preg_match('/^-?[1-9][0-9]{0,19}$/', $settings->chat_id)) {
            $this->logFailure('missing_or_invalid_chat_id', $source);

            return false;
        }

        $text = $this->formatMessage($source, $fields, $pageUrl);
        $request = Http::asJson()->connectTimeout(2)->timeout(5);

        if ($settings->use_proxy) {
            if (! $this->isValidProxy($settings->http_proxy)) {
                $this->logFailure('invalid_proxy_url', $source);

                return false;
            }

            $request = $request->withOptions(['proxy' => $settings->http_proxy]);
        }

        try {
            $response = $request->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $settings->chat_id,
                'text' => $text,
            ]);
        } catch (ConnectionException) {
            $this->logFailure('connection_error', $source);

            return false;
        } catch (\Throwable) {
            $this->logFailure('request_error', $source);

            return false;
        }

        $data = $response->json();

        if (! $response->successful() || ! is_array($data) || ($data['ok'] ?? null) !== true) {
            $this->logFailure(
                ! $response->successful() ? 'http_error' : (! is_array($data) || ! array_key_exists('ok', $data) ? 'unexpected_response' : 'telegram_rejected'),
                $source,
                $response->status(),
                isset($data['error_code']) && is_int($data['error_code']) ? $data['error_code'] : null,
            );

            return false;
        }

        return true;
    }

    /** @param array<string, string|null> $fields */
    private function formatMessage(string $source, array $fields, string $pageUrl): string
    {
        $lines = ['📩 New enquiry — GRHS', '', "Form: {$source}"];

        foreach ($fields as $label => $value) {
            if (! filled($value)) {
                continue;
            }

            $value = $label === 'Message'
                ? trim((string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value))
                : trim((string) preg_replace('/[\r\n]+/u', ' ', $value));

            if ($value !== '') {
                $lines[] = "{$label}: {$value}";
            }
        }

        $lines[] = '';
        $lines[] = "Page: {$pageUrl}";

        return Str::limit(implode("\n", $lines), 4096, '');
    }

    private function isValidProxy(?string $proxy): bool
    {
        if (! is_string($proxy) || filter_var($proxy, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $scheme = parse_url($proxy, PHP_URL_SCHEME);
        $host = parse_url($proxy, PHP_URL_HOST);

        return in_array($scheme, ['http', 'https'], true) && is_string($host) && $host !== '';
    }

    private function logFailure(string $errorType, string $source, ?int $httpStatus = null, ?int $errorCode = null): void
    {
        Log::warning('Telegram enquiry delivery failed.', array_filter([
            'error_type' => $errorType,
            'http_status' => $httpStatus,
            'error_code' => $errorCode,
            'source' => $source,
        ], static fn (mixed $value): bool => $value !== null));
    }
}
