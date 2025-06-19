<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;


use App\Models\Primary\Player;

use App\Models\Primary\Person;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
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

        Auth::login($user);

        // assign role user
        $role = Role::where('name', 'Guest')->first();          // 1 is id role user
        $user->syncRoles($role);


        // create player
        $person = $this->syncPerson($user);
        $player = $this->syncPlayer($user, $person->id);
        $user->player_id = $player->id;
        $user->save();

        return redirect(route('profile.edit', absolute: false));
    }



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
