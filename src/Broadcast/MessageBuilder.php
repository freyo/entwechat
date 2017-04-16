<?php

namespace EntWeChat\Broadcast;

use EntWeChat\Core\Exceptions\RuntimeException;
use EntWeChat\Message\Text;
use EntWeChat\Support\Arr;
use EntWeChat\Message\Raw as RawMessage;

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
        Broadcast::MSG_TYPE_MPNEWS
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
     * @return MessageBuilder
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
     * @return MessageBuilder
     *
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
     * @return MessageBuilder
     */
    public function to(array $to)
    {
        $this->to = Arr::only($to, ['touser', 'toparty', 'totag']);

        return $this;
    }

    /**
     * Message target all.
     *
     * @return mixed
     */
    public function toAll()
    {
        $this->to['touser'] = '@all';

        return $this;
    }

    /**
     * Message target user.
     *
     * @param int|array $userIds
     *
     * @return mixed
     */
    public function toUser($userIds)
    {
        if (1 < func_num_args()) {
            $userIds = Arr::where(func_get_args(), function ($key, $value) {
                return is_string($value);
            });
        }

        $this->to['touser'] = implode('|', (array)$userIds);

        return $this;
    }

    /**
     * Message target party.
     *
     * @param int|array $partyIds
     *
     * @return mixed
     */
    public function toParty($partyIds)
    {
        if (1 < func_num_args()) {
            $partyIds = Arr::where(func_get_args(), function ($key, $value) {
                return is_numeric($value);
            });
        }

        $this->to['toparty'] = implode('|', (array)$partyIds);

        return $this;
    }

    /**
     * Message target tag.
     *
     * @param int|array $tagIds
     *
     * @return mixed
     */
    public function toTag($tagIds)
    {
        if (1 < func_num_args()) {
            $tagIds = Arr::where(func_get_args(), function ($key, $value) {
                return is_numeric($value);
            });
        }

        $this->to['totag'] = implode('|', (array)$tagIds);

        return $this;
    }

    /**
     * Send the message.
     *
     * @return bool
     *
     * @throws RuntimeException
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

            $message = array_merge($this->to, ['agentid' => $this->agentId], $content);
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
