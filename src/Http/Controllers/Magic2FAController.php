<?php

namespace Skywalker\Laraguard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\URL;
use Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable;

class Magic2FAController extends Controller
{
    /**
     * Generate a magic 2FA link for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(Request $request)
    {
        $user = $request->user();

        if (!$user instanceof TwoFactorAuthenticatable) {
            return response()->json(['error' => 'User not supported'], 400);
        }

        $url = URL::temporarySignedRoute(
            '2fa.magic.login',
            now()->addMinutes(15),
            ['id' => $user->getAuthIdentifier()]
        );

        return response()->json(['magic_link' => $url]);
    }

    /**
     * Authenticate the user via the magic link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Invalid or expired magic link.');
        }

        $request->session()->put('2fa.totp_confirmed_at', now()->timestamp);

        return redirect()->intended('/home');
    }
}
