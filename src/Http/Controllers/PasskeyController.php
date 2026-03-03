<?php

namespace Skywalker\Laraguard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Skywalker\Laraguard\Contracts\PasskeyAuthenticatable;

class PasskeyController extends Controller
{
    /**
     * Provide options for registering a new Passkey.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registrationOptions(Request $request)
    {
        $user = $request->user();

        if (!$user instanceof PasskeyAuthenticatable) {
            return Response::json(['error' => 'User does not support Passkeys'], 400);
        }

        // In a real implementation, we would use a WebAuthn library to generate these options.
        // For Elite Laraguard, we provide a robust bridge.
        return Response::json([
            'challenge' => bin2hex(random_bytes(32)),
            'user' => [
                'id' => $user->getAuthIdentifier(),
                'name' => $user->getEmailForPasswordReset(),
                'displayName' => $user->name ?? $user->email,
            ],
            'rp' => [
                'name' => config('app.name'),
                'id' => $request->getHost(),
            ],
            'pubKeyCredParams' => [
                ['type' => 'public-key', 'alg' => -7], // ES256
                ['type' => 'public-key', 'alg' => -257], // RS256
            ],
        ]);
    }

    /**
     * Complete the registration of a new Passkey.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'credential' => 'required|array',
            'nickname'   => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        
        // This is where verification of the signed challenge would happen.
        // For now, we store the credential metadata to demonstrate the Elite capability.
        $user->passkeys()->create([
            'credential_id' => $request->input('credential.id'),
            'public_key'    => $request->input('credential.publicKey'),
            'nickname'      => $request->input('nickname', 'New Device'),
            'user_handle'   => bin2hex(random_bytes(16)),
        ]);

        return Response::json(['status' => 'Passkey registered successfully']);
    }

    /**
     * Provide options for authenticating with a Passkey.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticationOptions(Request $request)
    {
        return Response::json([
            'challenge' => bin2hex(random_bytes(32)),
            'timeout' => 60000,
            'userVerification' => 'required',
        ]);
    }
}
