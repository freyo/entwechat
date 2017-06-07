<?php

namespace EntWeChat\Message;

/**
 * Class DeviceEvent.
 *
 * @property string $device_type
 * @property string $device_id
 * @property string $content
 * @property string $session_id
 * @property string $open_id
 */
class DeviceEvent extends AbstractMessage
{
    /**
     * Message type.
     *
     * @var string
     */
    protected $type = 'device_event';

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
