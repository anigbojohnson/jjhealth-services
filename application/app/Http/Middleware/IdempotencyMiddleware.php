<?php

namespace App\Http\Middleware;

use App\Services\IdempotencyService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class IdempotencyMiddleware
{
    public function __construct(
        private IdempotencyService $idempotencyService
    ) {
    }

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        /*
         * Only apply idempotency to methods
         * that can create or modify resources.
         */
        if (!in_array($request->method(), [
            'POST',
            'PUT',
            'PATCH',
            'DELETE',
        ])) {
            return $next($request);
        }
        $key = $this->idempotencyService->getKey($request);

        /*
         * If the client didn't provide an
         * Idempotency-Key, continue normally.
         */
        if (!$key) {
            return $next($request);
        }

        $requestHash =
            $this->idempotencyService->generateRequestHash($request);

        try {
            /*
             * Check PostgreSQL for an existing key.
             */
            $existing =
                $this->idempotencyService->findExisting(
                    $request,
                    $key
                );

            if ($existing) {

                /*
                 * Make sure the same key wasn't
                 * reused with different data.
                 */
                $this->idempotencyService->validateExisting(
                    $existing,
                    $requestHash
                );

                /*
                 * If the original request completed,
                 * return the original response.
                 */
                 if ($existing->status === 'completed') {
                    return $this->idempotencyService
                        ->replayResponse();
                }
            }

            /*
             * Create the idempotency record.
             */
            if (!$existing) {
                $existing =
                    $this->idempotencyService->createRecord(
                        $request,
                        $key,
                        $requestHash
                    );
            }

            /*
             * Execute the actual controller.
             */
            $response = $next($request);

            /*
             * Store the controller response.
             */
            $this->idempotencyService->complete(
                $existing,
                $response
            );

            return $response;

        } catch (Throwable $exception) {

            /*
             * Mark failed requests so they aren't
             * treated as successfully completed.
             */
            if (isset($existing)) {
                $existing->update([
                    'status' => 'failed',
                ]);
            }
            throw $exception;
        } finally {

       
        }
    }
}