<?php

namespace EntWeChat\Message;

/**
 * Class Link.
 */
class Link extends AbstractMessage
{
    /**
     * Message type.
     *
     * @var string
     */
    protected $type = 'link';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
        'title',
        'description',
        'url',
    ];
}
