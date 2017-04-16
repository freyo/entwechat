<?php

namespace EntWeChat\Message;

/**
 * Class MpNews.
 */
class MpNews extends AbstractMessage
{
    /**
     * Message type.
     *
     * @var string
     */
    protected $type = 'mpnews';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
        'title',
        'thumb_media_id',
        'author',
        'content_source_url',
        'content',
        'digest',
        'show_cover_pic',
    ];

    /**
     * Aliases of attribute.
     *
     * @var array
     */
    protected $aliases = [
        'source_url' => 'content_source_url',
        'show_cover' => 'show_cover_pic',
    ];
}
