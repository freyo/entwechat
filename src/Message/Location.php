<?php

namespace EntWeChat\Message;

/**
 * Class Location.
 *
 * @property string $title
 * @property string $description
 * @property string $url
 */
class Location extends AbstractMessage
{
    /**
     * Message type.
     *
     * @var string
     */
    protected $type = 'location';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
        'latitude',
        'longitude',
        'scale',
        'label',
        'precision',
    ];
}
