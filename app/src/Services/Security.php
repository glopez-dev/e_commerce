<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Security
{
    public static function isAuthed(Request $request)
    {
        if (null === $request->headers->get('authorization')) {
            return new JsonResponse([
                'message' => 'Token invalid',
                'code' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }
    }
}