<?php

namespace EntWeChat\Message;

/**
 * Class MpNews.
 *
 * @property string $title
 * @property string $thumb_media_id
 * @property string $author
 * @property string $source_url
 * @property string $content
 * @property string $digest
 * @property string $show_cover
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
        'source_url',
        'content',
        'digest',
        'show_cover',
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

    /**
     * 设置音乐封面.
     *
     * @param string $mediaId
     *
     * @return $this
     */
    public function thumb($mediaId)
    {
        $this->setAttribute('thumb_media_id', $mediaId);

        return $this;
    }
}
