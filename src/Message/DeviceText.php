<?php

namespace EntWeChat\Message;

/**
 * Class DeviceText.
 *
 * @property string $device_type
 * @property string $device_id
 * @property string $content
 * @property string $session_id
 * @property string $open_id
 */
class DeviceText extends AbstractMessage
{
    /**
     * Message type.
     *
     * @var string
     */
    protected $type = 'device_text';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
        'device_type',
        'device_id',
        'content',
        'session_id',
        'open_id',
    ];
}
