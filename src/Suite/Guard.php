<?php

namespace EntWeChat\Suite;

use EntWeChat\Server\Guard as ServerGuard;
use EntWeChat\Support\Collection;
use EntWeChat\Support\Log;
use Symfony\Component\HttpFoundation\Response;

class Guard extends ServerGuard
{
    const EVENT_CREATE_AUTH  = 'create_auth';
    const EVENT_CANCEL_AUTH  = 'cancel_auth';
    const EVENT_CHANGE_AUTH  = 'change_auth';
    const EVENT_SUITE_TICKET = 'suite_ticket';

    /**
     * Event handlers.
     *
     * @var \EntWeChat\Support\Collection
     */
    protected $handlers;

    /**
     * Get handlers.
     *
     * @return \EntWeChat\Support\Collection
     */
    public function getHandlers()
    {
        return $this->handlers;
    }

    /**
     * Set handlers.
     *
     * @param array $handlers
     */
    public function setHandlers(array $handlers)
    {
        $this->handlers = new Collection($handlers);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function serve()
    {
        $message = $this->getMessage();

        // Handle Messages.
        if (isset($message['MsgType'])) {
            return parent::serve();
        }

        Log::debug('Suite Request received:', [
            'Method'   => $this->request->getMethod(),
            'URI'      => $this->request->getRequestUri(),
            'Query'    => $this->request->getQueryString(),
            'Protocal' => $this->request->server->get('SERVER_PROTOCOL'),
            'Content'  => $this->request->getContent(),
        ]);

        // If sees the `auth_code` query parameter in the url, that is,
        // authorization is successful and it calls back, meanwhile, an
        // `authorized` event, which also includes the auth code, is sent
        // from WeChat, and that event will be handled.
        if ($this->request->get('auth_code')) {
            return new Response(self::SUCCESS_EMPTY_RESPONSE);
        }

        $this->handleEventMessage($message);

        return new Response(self::SUCCESS_EMPTY_RESPONSE);
    }

    /**
     * Handle event message.
     *
     * @param array $message
     */
    protected function handleEventMessage(array $message)
    {
        Log::debug('Suite Event Message detail:', $message);

        $message = new Collection($message);

        $infoType = $message->get('InfoType');

        if ($handler = $this->getHandler($infoType)) {
            $handler->handle($message);
        } else {
            Log::notice("No existing handler for '{$infoType}'.");
        }

        if ($customHandler = $this->getMessageHandler()) {
            $customHandler($message);
        }
    }

    /**
     * Get handler.
     *
     * @param string $type
     *
     * @return \EntWeChat\Suite\EventHandlers\EventHandler|null
     */
    public function getHandler($type)
    {
        return $this->handlers->get($type);
    }
}
