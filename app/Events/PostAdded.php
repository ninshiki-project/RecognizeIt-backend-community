<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class PostAdded
{
    use Dispatchable;

    /**
     * The Post Instance.
     *
     * @var mixed
     */
    public mixed $post;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $post
     */
    public function __construct(mixed $post)
    {
        $this->post = $post;
    }
}
