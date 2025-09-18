<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login (LoginRequest $request) {

    }

    public function register (RegisterRequest $request) {
        try {
            $validated = $request->safe()->all();
        }catch(Exception $e) {

        }

    }

    public function logout () {

    }
}
