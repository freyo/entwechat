<?php

namespace EntWeChat\Message;

/**
 * Class File.
 *
 * @property string $media_id
 */
class File extends AbstractMessage
{
    /**
     * Message type.
     *
     * @var string
     */
    protected $type = 'file';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = ['media_id'];

    /**
     * Set media id.
     *
     * @param string $mediaId
     *
     * @return $this
     */
    public function media($mediaId)
    {
        $this->setAttribute('media_id', $mediaId);

        return $this;
    }
}
