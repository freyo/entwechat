<?php

namespace EntWeChat\Message;

/**
 * Class Raw.
 *
 * @property string $content
 */
class Raw extends AbstractMessage
{
    /**
     * @var string
     */
    protected $type = 'raw';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = ['content'];

    /**
     * Constructor.
     *
     * @param string $content
     */
    public function __construct($content)
    {
        parent::__construct(['content' => strval($content)]);
    }
}
