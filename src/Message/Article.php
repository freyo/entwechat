<?php

namespace EntWeChat\Message;

/**
 * Class Article.
 *
 * @property string $title
 * @property string $media_id
 * @property string $content
 * @property string $author
 * @property string $digest
 * @property string $source_url
 * @property string $thumb_media_id
 * @property string $show_cover
 */
class Article extends AbstractMessage
{
    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
        'thumb_media_id',
        'author',
        'title',
        'content',
        'digest',
        'source_url',
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
     * @param $mediaId
     *
     * @return $this
     */
    public function thumb($mediaId)
    {
        $this->setAttribute('thumb_media_id', $mediaId);

        return $this;
    }
}
