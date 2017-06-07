<?php

namespace EntWeChat\Message;

/**
 * Class TextCard.
 *
 * @property string $title
 * @property string $description
 * @property string $url
 * @property string $btntxt
 */
class TextCard extends AbstractMessage
{
    /**
     * Message type.
     *
     * @var string
     */
    protected $type = 'textcard';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
        'title',
        'description',
        'url',
        'btntxt',
    ];
}
