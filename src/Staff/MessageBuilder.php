<?php

namespace EntWeChat\Staff;

use EntWeChat\Core\Exceptions\InvalidArgumentException;
use EntWeChat\Core\Exceptions\RuntimeException;
use EntWeChat\Message\AbstractMessage;
use EntWeChat\Message\Raw as RawMessage;
use EntWeChat\Message\Text;

/**
 * Class MessageBuilder.
 */
class MessageBuilder
{
    /**
     * Message to send.
     *
     * @var \EntWeChat\Message\AbstractMessage;
     */
    protected $message;

    /**
     * Message receiver.
     *
     * @var array
     */
    protected $to;

    /**
     * Message sender.
     *
     * @var array
     */
    protected $by;

    /**
     * Staff instance.
     *
     * @var \EntWeChat\Staff\Staff
     */
    protected $staff;

    /**
     * User types.
     *
     * @var array
     */
    private $userTypes = [
        Staff::USER_TYPE_STAFF,
        Staff::USER_TYPE_USERID,
        Staff::USER_TYPE_OPENID,
    ];

    /**
     * MessageBuilder constructor.
     *
     * @param \EntWeChat\Staff\Staff $staff
     */
    public function __construct(Staff $staff)
    {
        $this->staff = $staff;
    }

    /**
     * Set message to send.
     *
     * @param string|AbstractMessage $message
     *
     * @return MessageBuilder
     *
     * @throws InvalidArgumentException
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
     * Set staff account to send message.
     *
     * @param string $type
     * @param string $id
     *
     * @return MessageBuilder
     */
    public function by($type, $id)
    {
        $this->account = [
            'type' => $type,
            'id' => $id,
        ];;

        return $this;
    }

    /**
     * Set target user open id.
     *
     * @param string $type
     * @param string $id
     *
     * @return MessageBuilder
     */
    public function to($type, $id)
    {
        $this->to = [
            'type' => $type,
            'id' => $id,
        ];

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
            $message = [
                'sender' => $this->by,
                'receiver' => $this->to,
            ];

            $message = array_merge($message, $content);
        }

        return $this->staff->send($message);
    }

    /**
     * Return property.
     *
     * @param $property
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
