<?php

namespace EntWeChat\Material;

use EntWeChat\Core\AbstractAPI;
use EntWeChat\Core\Exceptions\InvalidArgumentException;
use EntWeChat\Support\File;

/**
 * Class Temporary.
 */
class Temporary extends AbstractAPI
{
    /**
     * Allow media type.
     *
     * @var array
     */
    protected $allowTypes = ['image', 'voice', 'video', 'file'];

    const API_GET = 'https://qyapi.weixin.qq.com/cgi-bin/media/get';
    const API_UPLOAD = 'https://qyapi.weixin.qq.com/cgi-bin/media/upload';

    /**
     * Download temporary material.
     *
     * @param string $mediaId
     * @param string $directory
     * @param string $filename
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    public function download($mediaId, $directory, $filename = '')
    {
        if (!is_dir($directory) || !is_writable($directory)) {
            throw new InvalidArgumentException("Directory does not exist or is not writable: '$directory'.");
        }

        $filename = $filename ?: $mediaId;

        $stream = $this->getStream($mediaId);

        $filename .= File::getStreamExt($stream);

        file_put_contents($directory.'/'.$filename, $stream);

        return $filename;
    }

    /**
     * Fetch item from WeChat server.
     *
     * @param string $mediaId
     *
     * @throws \EntWeChat\Core\Exceptions\RuntimeException
     *
     * @return mixed
     */
    public function getStream($mediaId)
    {
        $response = $this->getHttp()->get(self::API_GET, ['media_id' => $mediaId]);

        return $response->getBody();
    }

    /**
     * Upload temporary material.
     *
     * @param string $type
     * @param string $path
     *
     * @throws \EntWeChat\Core\Exceptions\InvalidArgumentException
     *
     * @return string
     */
    public function upload($type, $path)
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new InvalidArgumentException("File does not exist, or the file is unreadable: '$path'");
        }

        if (!in_array($type, $this->allowTypes, true)) {
            throw new InvalidArgumentException("Unsupported media type: '{$type}'");
        }

        return $this->parseJSON('upload', [self::API_UPLOAD, ['media' => $path], [], ['type' => $type]]);
    }

    /**
     * Upload image.
     *
     * @param $path
     *
     * @throws \EntWeChat\Core\Exceptions\InvalidArgumentException
     *
     * @return string
     */
    public function uploadImage($path)
    {
        return $this->upload('image', $path);
    }

    /**
     * Upload video.
     *
     * @param $path
     *
     * @throws \EntWeChat\Core\Exceptions\InvalidArgumentException
     *
     * @return string
     */
    public function uploadVideo($path)
    {
        return $this->upload('video', $path);
    }

    /**
     * Upload voice.
     *
     * @param $path
     *
     * @throws \EntWeChat\Core\Exceptions\InvalidArgumentException
     *
     * @return string
     */
    public function uploadVoice($path)
    {
        return $this->upload('voice', $path);
    }

    /**
     * Upload file.
     *
     * @param $path
     *
     * @throws \EntWeChat\Core\Exceptions\InvalidArgumentException
     *
     * @return string
     */
    public function uploadFile($path)
    {
        return $this->upload('file', $path);
    }
}
