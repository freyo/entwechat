<?php

namespace EntWeChat\Message;

/**
 * Class Location.
 *
 * @property string $latitude
 * @property string $longitude
 * @property string $scale
 * @property string $label
 * @property string $precision
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
