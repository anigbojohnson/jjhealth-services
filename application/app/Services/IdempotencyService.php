<?php

namespace App\Services;

use App\Models\IdempotencyKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyService
{
    /**
     * How long the Redis lock should live.
     */
    private int $lockSeconds = 30;

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
            'body' => $request->all(),
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
            ->where('user_id', $request->user()?->id)
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
            abort(
                Response::HTTP_CONFLICT,
                'The idempotency key was already used with a different request.'
            );
        }
    }

    /**
     * Return the previously stored response.
     */
    public function replayResponse(
        IdempotencyKey $record
    ): JsonResponse {
        return response()->json(
            $record->response_body,
            $record->response_code
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
            'user_id' => $request->user()?->id,
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

    /**
     * Acquire a Redis lock.
     */
    public function acquireLock(
        Request $request,
        string $key
    ) {
        $userId = $request->user()?->id ?? 'guest';

        $lockKey = "idempotency:{$userId}:{$key}";

        return Cache::lock(
            $lockKey,
            $this->lockSeconds
        );
    }
}