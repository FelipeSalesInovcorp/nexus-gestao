<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            // IMPORTANTE: guardar hash
            'password' => Hash::make($input['password']),
        ]);

        //  Role default automático
        // (Se o role não existir, não quebra o registo)
        try {
            if ($user->roles()->count() === 0) {
                $user->assignRole('Financeiro');
            }
        } catch (\Throwable $e) {
            // noop
        }

        return $user;
    }
}
