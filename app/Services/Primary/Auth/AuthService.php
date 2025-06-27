<?php

namespace App\Services\Primary\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Auth\Events\Verified;

use Illuminate\Validation\Rules;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;

use Spatie\Permission\Models\Role;

use App\Models\User;
use App\Models\Primary\Player;
use App\Models\Primary\Person;

class AuthService
{
    // Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                        ? response()->json(['success' => true, 'message' => 'Password berhasil direset'], 200)
                        : response()->json(['success' => false, 'message' => __($status)], 400);
    }



    // Forgot Password
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status == Password::RESET_LINK_SENT
                    ? response()->json(['success' => true, 'message' => 'Reset password link berhasil dikirim'], 200)
                    : response()->json(['success' => false, 'message' => __($status)], 400);
    }



    // Verify Email
    public function verifyEmail(EmailVerificationRequest $request)
    {
        if($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email sudah terverifikasi'], 200);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));

            // update guest to user
            if($request->user()->hasRole('Guest')){
                $request->user()->syncRoles('User');

                $user = $request->user();
                $user->role_id = 2;
                $user->save();
            }
        }

        return response()->json(['message' => 'Email berhasil diverifikasi'], 200);
    }



    // Send Email Verification
    public function sendVerificationEmail(Request $request)
    {
        if($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'success' => true,
                'message' => 'Email sudah terverifikasi'
            ], 200);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Email verifikasi berhasil dikirim'
        ], 200);
    }



    // Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Login gagal'], 401);
        }
        
        $user = Auth::user();
        $token = $user->createToken('react-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'success' => true,
        ]);
    }


    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ], 200);
    }



    // Sign up
    public function register(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));


            // assign role user
            $role = Role::where('name', 'Guest')->first();          // 1 is id role user
            $user->syncRoles($role);


            // create player
            $person = $this->syncPerson($user);
            $player = $this->syncPlayer($user, $person->id);
            $user->player_id = $player->id;
            $user->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'data' => [],
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'data' => $user,
            'success' => true,
            'message' => 'User created successfully',
        ], 201);
    }



    // Utility

    public function syncPerson($user)
    {
        // update if email already exist, or maybe phone number too
        $person = Person::updateOrCreate(
            [
                'email' => $user->email,
            ], [
                'name' => $user->name,
                'email' => $user->email,
            ]
        );

        return $person;
    }



    public function syncPlayer($user, $person_id)
    {
        $player = Player::updateOrCreate(
            [
                'size_type' => 'PERS',
                'size_id' => $person_id,
            ],
            [
                'name' => $user->name,
                'size_type' => 'PERS',
                'size_id' => $person_id,
            ]
        );

        $user->player_id = $player->id;
        $user->save();

        return $player;
    }
}
