<?php

namespace Skywalker\Laraguard\View\Components;

use Illuminate\View\Component;
use Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable;

class QrCode extends Component
{
    /**
     * The authenticatable user.
     *
     * @var \Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable
     */
    public $user;

    /**
     * The size of the QR code.
     *
     * @var int
     */
    public $size;

    /**
     * Create a new component instance.
     *
     * @param  \Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable  $user
     * @param  int  $size
     * @return void
     */
    public function __construct(TwoFactorAuthenticatable $user, int $size = 200)
    {
        $this->user = $user;
        $this->size = $size;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $qrCodeSvg = $this->user->twoFactorAuth->toQr();

        return view('laraguard::components.qrcode', [
            'qrCodeSvg' => $qrCodeSvg,
        ]);
    }
}
