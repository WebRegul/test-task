<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class Helper
{
    /**
     * @param string $value
     * @return string
     */
    public static function clearString(string $value): string
    {
        $value = preg_replace('/[\x00-\x1F\x7f-\xFF]/', '', trim($value));
        $value = self::removeEmoji($value);
        $value = preg_replace('/[[:^print:]]/', '', $value);
        return $value;
    }

    protected static function removeEmoji($string)
    {
        return preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $string);
    }

    /**
     * @param string $token
     * @return JsonResponse
     */
    public static function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL()
        ]);
    }
}
