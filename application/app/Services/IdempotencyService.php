<?php

namespace App\Services;

use App\Models\IdempotencyKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\IdempotencyKeyMismatchException;

 class IdempotencyService
{

    /**
     * How long an idempotency record should be retained.
     */
    private int $expirationHours = 24;

    /**
     * Get the idempotency key from the request.
     */
    public function getKey(Request $request): ?string
    {
        return $request->header('Idempotency-Key');
    }

    /**
     * Generate a hash representing the request.
     */
    public function generateRequestHash(Request $request): string
    {
        $payload = [
            'method' => $request->method(),
            'path' => $request->path(),
            'body' => session('credentials'),
        ];

        return hash(
            'sha256',
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Find an existing idempotency record.
     */
    public function findExisting(
        Request $request,
        string $key
    ): ?IdempotencyKey {
        return IdempotencyKey::query()
            ->where('user_email', $request->user()?->email)
            ->where('key', $key)
            ->first();
    }

    /**
     * Validate an existing idempotency request.
     */
    public function validateExisting(
        IdempotencyKey $record,
        string $requestHash
    ): void {
         if ($record->request_hash !== $requestHash) {
        throw new IdempotencyKeyMismatchException(
            'This request is already '.$record->status
        );
        }
    }
    /**
     * Return the previously stored response.
     */
    public function replayResponse(): void  {
         throw new IdempotencyKeyMismatchException(
            "This request has already been processed successfully. Please do not resubmit the same request."
        );

    }

    /**
     * Create a new idempotency record.
     */
    public function createRecord(
        Request $request,
        string $key,
        string $requestHash
    ): IdempotencyKey {
        return IdempotencyKey::create([
            'user_email' => $request->user()?->email,
            'key' => $key,
            'endpoint' => $request->method() . ' ' . $request->path(),
            'request_hash' => $requestHash,
            'status' => 'processing',
            'expires_at' => now()->addHours($this->expirationHours),
        ]);
    }

    /**
     * Store the final response.
     */
    public function complete(
        IdempotencyKey $record,
        Response $response
    ): void {
        $body = json_decode(
            $response->getContent(),
            true
        );

        $record->update([
            'status' => 'completed',
            'response_code' => $response->getStatusCode(),
            'response_body' => $body,
        ]);

        
    }


}