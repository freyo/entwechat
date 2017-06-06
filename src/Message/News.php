<?php

namespace EntWeChat\Message;

/**
 * Class News.
 *
 * @property string $title
 * @property string $description
 * @property string $url
 * @property string $image
 * @property string $btntxt
 */
class News extends AbstractMessage
{
    /**
     * Message type.
     *
     * @var string
     */
    protected $type = 'news';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
        'title',
        'description',
        'url',
        'image',
        'btntxt',
    ];

    /**
     * Aliases of attribute.
     *
     * @var array
     */
    protected $aliases = [
        'image' => 'picurl',
    ];
}
