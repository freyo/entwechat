<?php

namespace EntWeChat\Suite\EventHandlers;

abstract class EventHandler
{
    /**
     * Handle an incoming event message from WeChat server-side.
     *
     * @param \EntWeChat\Support\Collection $message
     */
    abstract public function handle($message);
}
