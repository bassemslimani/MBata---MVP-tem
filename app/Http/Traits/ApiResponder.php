<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponder
{
    /**
     * Return a success JSON response.
     */
    protected function successResponse(
        string $messageKey,
        mixed $data = null,
        int $statusCode = 200,
        ?string $locale = null
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $this->getMessage($messageKey, $locale),
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Return an error JSON response.
     */
    protected function errorResponse(
        string $messageKey,
        int $statusCode = 400,
        ?string $locale = null,
        mixed $errors = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $this->getMessage($messageKey, $locale),
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a validation error JSON response compatible with Inertia.
     */
    protected function validationErrorResponse(
        array $errors,
        ?string $locale = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage('validation_failed', $locale),
            'errors' => $errors,
        ], 422);
    }

    /**
     * Return a not found JSON response.
     */
    protected function notFoundResponse(
        string $messageKey = 'not_found',
        ?string $locale = null
    ): JsonResponse {
        return $this->errorResponse($messageKey, 404, $locale);
    }

    /**
     * Return an unauthorized JSON response.
     */
    protected function unauthorizedResponse(
        string $messageKey = 'unauthorized',
        ?string $locale = null
    ): JsonResponse {
        return $this->errorResponse($messageKey, 401, $locale);
    }

    /**
     * Return a forbidden JSON response.
     */
    protected function forbiddenResponse(
        string $messageKey = 'forbidden',
        ?string $locale = null
    ): JsonResponse {
        return $this->errorResponse($messageKey, 403, $locale);
    }

    /**
     * Get localized message.
     */
    protected function getMessage(string $key, ?string $locale = null): string
    {
        if ($locale) {
            app()->setLocale($locale);
        }

        return __("api.{$key}");
    }

    /**
     * Get localized message with replacements.
     */
    protected function getMessageWithReplacements(
        string $key,
        array $replacements = [],
        ?string $locale = null
    ): string {
        if ($locale) {
            app()->setLocale($locale);
        }

        return __("api.{$key}", $replacements);
    }

    /**
     * Return a paginated JSON response.
     */
    protected function paginatedResponse(
        $paginator,
        string $messageKey = 'success',
        ?string $locale = null
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $this->getMessage($messageKey, $locale),
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }
}
