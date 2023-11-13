<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * Handles the /auth HTTP request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        $validator = Validator::make($request->query(), [
            'id' => 'nullable|numeric',
            'code' => 'required_with:from_widget|numeric',
            'referer' => 'required_with:from_widget|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'error_message' => 'Invalid request',
            ], 400);
        }

        $auth = new AuthService();
        $simpleAuth = new SimpleAuthService();
        $accountParams = $request->query();

        if ($request->filled('from_widget') && $request->filled('code')) {
            return response()->json([
                'status' => 'success',
                'auth as' => $simpleAuth->auth($accountParams),
            ], 200);
        }

        return response()->json([
            $auth->auth($accountParams),
        ]);
    }
}

