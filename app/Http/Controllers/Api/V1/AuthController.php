<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\UpdatePasswordRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Connexion : échange email + mot de passe contre un jeton Bearer.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()->where('email', $request->string('email'))->first();

        if ($user === null || ! Hash::check($request->string('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Identifiants incorrects.'],
            ]);
        }

        if (! $user->estActif()) {
            return response()->json([
                'message' => 'Votre compte est désactivé. Contactez votre administrateur.',
                'code' => 'compte_inactif',
            ], 403);
        }

        $token = $user->createToken($request->string('device_name'), [$user->role() ?? '*']);

        return response()->json([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ]);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /** Déconnexion de l'appareil courant. */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->message('Déconnexion réussie.');
    }

    /** Déconnexion de tous les appareils. */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return $this->message('Tous les appareils ont été déconnectés.');
    }

    /** Changement de mot de passe : les autres appareils sont déconnectés. */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->password = Hash::make($request->string('password'));
        $user->save();

        $user->tokens()->whereKeyNot($user->currentAccessToken()->getKey())->delete();

        return $this->message('Mot de passe modifié avec succès.');
    }
}
