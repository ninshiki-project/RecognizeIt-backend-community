<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class UserRedeem
{
    use Dispatchable;

    /**
     * The Shop Instance.
     *
     * @var mixed
     */
    public mixed $shop;

    /**
     * The User Instance.
     *
     * @var mixed
     */
    public mixed $user;

    /**
     * The Redeem Instance.
     *
     * @var mixed
     */
    public mixed $redeem;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $redeem
     * @param  mixed  $user
     * @param  mixed  $shop
     */
    public function __construct(mixed $redeem, mixed $user, mixed $shop)
    {
        $this->shop = $shop;
        $this->user = $user;
        $this->redeem = $redeem;
    }
}
