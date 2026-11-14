<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class Controller extends BaseController
{
    protected const PAR_PAGE_DEFAUT = 20;

    protected const PAR_PAGE_MAX = 100;

    protected function parPage(Request $request): int
    {
        return max(1, min(self::PAR_PAGE_MAX, (int) $request->query('par_page', self::PAR_PAGE_DEFAUT)));
    }

    protected function message(string $message, int $status = 200): JsonResponse
    {
        return response()->json(['message' => $message], $status);
    }
}
