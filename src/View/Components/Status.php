<?php

namespace Skywalker\Laraguard\View\Components;

use Illuminate\View\Component;
use Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable;

class Status extends Component
{
    /**
     * The authenticatable user.
     *
     * @var \Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable
     */
    public $user;

    /**
     * Create a new component instance.
     *
     * @param  \Skywalker\Laraguard\Contracts\TwoFactorAuthenticatable  $user
     * @return void
     */
    public function __construct(TwoFactorAuthenticatable $user)
    {
        $this->user = $user;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $enabled = $this->user->hasTwoFactorEnabled();

        return view('laraguard::components.status', [
            'enabled' => $enabled,
            'color'   => $enabled ? '#10b981' : '#f59e0b',
            'label'   => $enabled ? 'Secured' : 'Setup Required',
        ]);
    }
}
