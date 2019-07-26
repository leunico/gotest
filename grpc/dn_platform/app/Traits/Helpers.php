<?php

declare(strict_types=1);

namespace App\Traits;

use App\Factories\Response\ResponseFactory;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Http\JsonResponse;

/**
 * @method \App\Modules\Factories\ResponseFactory response()
 */
trait Helpers
{
    /**
     * @return \App\Factories\Response\ResponseFactory
     */
    public function response(): ResponseFactory
    {
        return app(ResponseFactory::class);
    }

    /**
     * @return \App\User|null
     */
    public function user(): ?User
    {
        return Auth::user();
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function respondWithToken($token): JsonResponse
    {
        return $this->response()->success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
