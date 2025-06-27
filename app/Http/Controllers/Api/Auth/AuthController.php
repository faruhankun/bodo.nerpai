<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;

use App\Services\Primary\Auth\AuthService;


class AuthController extends Controller
{
    protected $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }

    public function login(Request $request){ return $this->auth->login($request); }
    public function logout(Request $request){ return $this->auth->logout($request); }
    public function register(Request $request){ return $this->auth->register($request); }

    public function sendVerificationEmail(Request $request){ return $this->auth->sendVerificationEmail($request); }
    public function verifyEmail(EmailVerificationRequest $request){ return $this->auth->verifyEmail($request); }

    public function forgotPassword(Request $request){ return $this->auth->forgotPassword($request); }
    public function resetPassword(Request $request){ return $this->auth->resetPassword($request); }
}
