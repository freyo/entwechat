<?php

namespace EntWeChat\Broadcast;

use EntWeChat\Core\Exceptions\RuntimeException;
use EntWeChat\Message\Raw as RawMessage;
use EntWeChat\Message\Text;
use EntWeChat\Support\Arr;

/**
 * Class MessageBuilder.
 */
class MessageBuilder
{
    /**
     * Message target user or group.
     *
     * @var array
     */
    protected $to = [];

    /**
     * Message type.
     *
     * @var string
     */
    protected $msgType;

    /**
     * Agent id.
     *
     * @var mixed
     */
    protected $agentId;

    /**
     * Message.
     *
     * @var mixed
     */
    protected $message;

    /**
     * Safe message.
     *
     * @var mixed
     */
    protected $safe = 0;

    /**
     * Broadcast instance.
     *
     * @var \EntWeChat\Broadcast\Broadcast
     */
    protected $broadcast;

    /**
     * Message types.
     *
     * @var array
     */
    private $msgTypes = [
        Broadcast::MSG_TYPE_TEXT,
        Broadcast::MSG_TYPE_NEWS,
        Broadcast::MSG_TYPE_IMAGE,
        Broadcast::MSG_TYPE_VIDEO,
        Broadcast::MSG_TYPE_VOICE,
        Broadcast::MSG_TYPE_CARD,
        Broadcast::MSG_TYPE_FILE,
        Broadcast::MSG_TYPE_MPNEWS,
    ];

    /**
     * MessageBuilder constructor.
     *
     * @param \EntWeChat\Broadcast\Broadcast $broadcast
     */
    public function __construct(Broadcast $broadcast)
    {
        $this->broadcast = $broadcast;
    }

    /**
     * Set message.
     *
     * @param string|array $message
     *
     * @return $this
     */
    public function message($message)
    {
        if (is_string($message)) {
            $message = new Text(['content' => $message]);
        }

        $this->message = $message;

        return $this;
    }

    /**
     * Set agent to send message.
     *
     * @param $agentId
     *
     * @return $this
     */
    public function by($agentId)
    {
        $this->agentId = $agentId;

        return $this;
    }

    /**
     * Set target user or group.
     *
     * @param array $to
     *
     * @return $this
     */
    public function to(array $to)
    {
        $this->to = Arr::only($to, ['touser', 'toparty', 'totag']);

        return $this;
    }

    /**
     * Message target all.
     *
     * @return $this
     */
    public function toAll()
    {
        $this->to['touser'] = '@all';

        return $this;
    }

    /**
     * Message target user.
     *
     * @return $this
     */
    public function toUser()
    {
        if (func_num_args() > 0) {
            $userIds = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();

            $this->to['touser'] = implode('|', $userIds);
        }

        return $this;
    }

    /**
     * Message target party.
     *
     * @return $this
     */
    public function toParty()
    {
        if (func_num_args() > 0) {
            $partyIds = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();

            $this->to['toparty'] = implode('|', $partyIds);
        }

        return $this;
    }

    /**
     * Message target tag.
     *
     * @return $this
     */
    public function toTag()
    {
        if (func_num_args() > 0) {
            $tagIds = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();

            $this->to['totag'] = implode('|', $tagIds);
        }

        return $this;
    }

    /**
     * Use safe message.
     *
     * @return $this
     */
    public function safe()
    {
        $this->safe = 1;

        return $this;
    }

    /**
     * Send the message.
     *
     * @throws RuntimeException
     *
     * @return bool
     */
    public function send()
    {
        if (empty($this->message)) {
            throw new RuntimeException('No message to send.');
        }

        $transformer = new Transformer();

        if ($this->message instanceof RawMessage) {
            $message = $this->message->get('content');
        } else {
            $content = $transformer->transform($this->message);

            $message = array_merge($this->to, ['agentid' => $this->agentId], ['safe' => $this->safe], $content);
        }

        return $this->broadcast->send($message);
    }

    /**
     * Return property.
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
}
