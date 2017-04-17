<?php

namespace EntWeChat\Chat;

/**
 * Class Transformer.
 */
class Transformer
{
    /**
     * Message type.
     *
     * @var string
     */
    protected $msgType;

    /**
     * message.
     *
     * @var mixed
     */
    protected $message;

    /**
     * Transformer constructor.
     *
     * @param $msgType
     * @param $message
     */
    public function __construct($msgType, $message)
    {
        $this->msgType = $msgType;

        $this->message = $message;
    }

    /**
     * Transform message.
     *
     * @return array
     */
    public function transform()
    {
        $handle = sprintf('transform%s', ucfirst($this->msgType));

        return method_exists($this, $handle) ? $this->$handle($this->message) : [];
    }

    /**
     * Transform text message.
     *
     * @param string $message
     *
     * @return array
     */
    public function transformText($message)
    {
        return [
            'text' => [
                'content' => $message,
            ],
            'msgtype' => 'text',
        ];
    }

    /**
     * Transform image message.
     *
     * @param string $message
     *
     * @return array
     */
    public function transformImage($message)
    {
        return [
            'image' => [
                'media_id' => $message,
            ],
            'msgtype' => 'image',
        ];
    }

    /**
     * Transform voice message.
     *
     * @param string $message
     *
     * @return array
     */
    public function transformVoice($message)
    {
        return [
            'voice' => [
                'media_id' => $message,
            ],
            'msgtype' => 'voice',
        ];
    }

    /**
     * Transform file message.
     *
     * @param $message
     *
     * @return array
     */
    public function transformFile($message)
    {
        return [
            'file' => [
                'media_id' => $message,
            ],
            'msgtype' => 'file',
        ];
    }

    /**
     * Transform link message.
     *
     * @param $message
     *
     * @return array
     */
    public function transformLink($message)
    {
        return [
            'link' => [
                'title'       => $message[0],
                'description' => $message[1],
                'url'         => $message[2],
            ],
            'msgtype' => 'link',
        ];
    }
}
