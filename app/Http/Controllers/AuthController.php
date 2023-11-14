<?php

namespace App\Http\Controllers;

use AmoCRM\Exceptions\BadTypeException;
use App\Services\AuthService;
use App\Services\SimpleAuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
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
     * @throws BadTypeException
     */
    public function handle(Request $request): JsonResponse
    {
//        $validator = Validator::make($request->query(), [
//            'code' => 'required_with:from_widget|numeric',
//            'referer' => 'required_with:from_widget|numeric',
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json([
//                'status' => 'error',
//                'error_message' => 'Invalid request',
//            ], 400);
//        }

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
            'status' => 'success',
            'auth as' => $auth->auth($accountParams),
        ], 200);
    }
}

