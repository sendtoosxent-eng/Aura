<?php

namespace App\Services;

use AfricasTalking\SDK\AfricasTalking;
use App\Exceptions\SmsDeliveryException;
use Illuminate\Support\Facades\Log;

class AfricasTalkingSmsService
{
    public function send(string $phone, string $message): void
    {
        $phone = $this->normalizePhone($phone);

        if (config('africastalking.driver') === 'log') {
            Log::info('AfyaLink SMS', ['phone' => $phone, 'message' => $message]);

            return;
        }

        $username = trim((string) config('africastalking.username'));
        $apiKey = trim((string) config('africastalking.api_key'));
        if ($username === '' || $apiKey === '') {
            throw new SmsDeliveryException(
                'SMS delivery is not configured. Add the Africa’s Talking username and API key on the server.'
            );
        }

        $payload = [
            'to' => [$phone],
            'message' => $message,
            'enqueue' => true,
        ];
        $sender = trim((string) config('africastalking.sender_id'));
        if ($sender !== '') {
            $payload['from'] = $sender;
        }

        try {
            $response = (new AfricasTalking($username, $apiKey))->sms()->send($payload);
        } catch (\Throwable $exception) {
            report($exception);
            throw new SmsDeliveryException('We could not send the verification code. Please try again.');
        }

        if (($response['status'] ?? null) !== 'success') {
            Log::warning('Africa’s Talking rejected an SMS request.', ['response' => $response]);
            throw new SmsDeliveryException('The SMS provider rejected the verification message. Please try again.');
        }

        $recipient = $response['data']->SMSMessageData->Recipients[0] ?? null;
        $statusCode = (int) ($recipient->statusCode ?? 0);
        if (! in_array($statusCode, [100, 101, 102], true)) {
            Log::warning('Africa’s Talking could not deliver an SMS.', [
                'phone' => $phone,
                'status' => $recipient->status ?? null,
                'status_code' => $statusCode,
            ]);
            throw new SmsDeliveryException('The verification SMS could not be delivered to that number.');
        }
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if (str_starts_with($digits, '0')) {
            $digits = '256'.substr($digits, 1);
        }
        if (strlen($digits) === 9) {
            $digits = '256'.$digits;
        }

        if (! preg_match('/^256\d{9}$/', $digits)) {
            throw new SmsDeliveryException('Enter a valid Ugandan phone number before requesting an OTP.');
        }

        return '+'.$digits;
    }
}
