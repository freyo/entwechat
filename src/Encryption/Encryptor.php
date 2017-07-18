<?php

namespace EntWeChat\Encryption;

use EntWeChat\Core\Exceptions\InvalidConfigException;
use EntWeChat\Core\Exceptions\RuntimeException;
use EntWeChat\Support\XML;
use Exception as BaseException;

/**
 * Class Encryptor.
 */
class Encryptor
{
    /**
     * ID.
     *
     * @var string
     */
    protected $id;

    /**
     * Token.
     *
     * @var string
     */
    protected $token;

    /**
     * AES key.
     *
     * @var string
     */
    protected $AESKey;

    /**
     * Block size.
     *
     * @var int
     */
    protected $blockSize;

    /**
     * Constructor.
     *
     * @param string $id
     * @param string $token
     * @param string $AESKey
     *
     * @throws RuntimeException
     */
    public function __construct($id, $token, $AESKey)
    {
        if (!extension_loaded('openssl')) {
            throw new RuntimeException("The ext 'openssl' is required.");
        }

        $this->id = $id;
        $this->token = $token;
        $this->AESKey = $AESKey;
        $this->blockSize = 32;
    }

    /**
     * Encrypt the message and return XML.
     *
     * @param string $xml
     * @param string $nonce
     * @param int    $timestamp
     *
     * @return string
     */
    public function encryptMsg($xml, $nonce = null, $timestamp = null)
    {
        $encrypt = $this->encrypt($xml, $this->id);

        !is_null($nonce) || $nonce = substr($this->id, 0, 10);
        !is_null($timestamp) || $timestamp = time();

        //生成安全签名
        $signature = $this->getSHA1($this->token, $timestamp, $nonce, $encrypt);

        $response = [
            'Encrypt'      => $encrypt,
            'MsgSignature' => $signature,
            'TimeStamp'    => $timestamp,
            'Nonce'        => $nonce,
        ];

        //生成响应xml
        return XML::build($response);
    }

    /**
     * Decrypt message.
     *
     * @param string $msgSignature
     * @param string $nonce
     * @param string $timestamp
     * @param string $postXML
     *
     * @throws EncryptionException
     *
     * @return array
     */
    public function decryptMsg($msgSignature, $nonce, $timestamp, $postXML)
    {
        try {
            $array = XML::parse($postXML);
        } catch (BaseException $e) {
            throw new EncryptionException('Invalid xml.', EncryptionException::ERROR_PARSE_XML);
        }

        $encrypted = $array['Encrypt'];

        $signature = $this->getSHA1($this->token, $timestamp, $nonce, $encrypted);

        if ($signature !== $msgSignature) {
            throw new EncryptionException('Invalid Signature.', EncryptionException::ERROR_INVALID_SIGNATURE);
        }

        return XML::parse($this->decrypt($encrypted, $this->id));
    }

    /**
     * Get SHA1.
     *
     * @throws EncryptionException
     *
     * @return string
     */
    public function getSHA1()
    {
        try {
            $array = func_get_args();
            sort($array, SORT_STRING);

            return sha1(implode($array));
        } catch (BaseException $e) {
            throw new EncryptionException($e->getMessage(), EncryptionException::ERROR_CALC_SIGNATURE);
        }
    }

    /**
     * Encode string.
     *
     * @param string $text
     *
     * @return string
     */
    public function encode($text)
    {
        $padAmount = $this->blockSize - (strlen($text) % $this->blockSize);

        $padAmount = $padAmount !== 0 ? $padAmount : $this->blockSize;

        $padChr = chr($padAmount);

        $tmp = '';

        for ($index = 0; $index < $padAmount; ++$index) {
            $tmp .= $padChr;
        }

        return $text.$tmp;
    }

    /**
     * Decode string.
     *
     * @param string $decrypted
     *
     * @return string
     */
    public function decode($decrypted)
    {
        $pad = ord(substr($decrypted, -1));

        if ($pad < 1 || $pad > $this->blockSize) {
            $pad = 0;
        }

        return substr($decrypted, 0, (strlen($decrypted) - $pad));
    }

    /**
     * Return AESKey.
     *
     * @throws InvalidConfigException
     *
     * @return string
     */
    protected function getAESKey()
    {
        if (empty($this->AESKey)) {
            throw new InvalidConfigException("Configuration mission, 'aes_key' is required.");
        }

        if (strlen($this->AESKey) !== 43) {
            throw new InvalidConfigException("The length of 'aes_key' must be 43.");
        }

        return base64_decode($this->AESKey.'=', true);
    }

    /**
     * Encrypt string.
     *
     * @param string $text
     * @param string $corpId
     *
     * @throws EncryptionException
     *
     * @return string
     */
    private function encrypt($text, $corpId)
    {
        try {
            $key = $this->getAESKey();
            $random = $this->getRandomStr();
            $text = $this->encode($random.pack('N', strlen($text)).$text.$corpId);

            $iv = substr($key, 0, 16);

            $encrypted = openssl_encrypt($text, 'aes-256-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, $iv);

            return base64_encode($encrypted);
        } catch (BaseException $e) {
            throw new EncryptionException($e->getMessage(), EncryptionException::ERROR_ENCRYPT_AES);
        }
    }

    /**
     * Decrypt message.
     *
     * @param string $encrypted
     * @param string $corpId
     *
     * @throws EncryptionException
     *
     * @return string
     */
    public function decrypt($encrypted, $corpId)
    {
        try {
            $key = $this->getAESKey();
            $ciphertext = base64_decode($encrypted, true);
            $iv = substr($key, 0, 16);

            $decrypted = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, $iv);
        } catch (BaseException $e) {
            throw new EncryptionException($e->getMessage(), EncryptionException::ERROR_DECRYPT_AES);
        }

        try {
            $result = $this->decode($decrypted);

            if (strlen($result) < 16) {
                return '';
            }

            $content = substr($result, 16, strlen($result));
            $listLen = unpack('N', substr($content, 0, 4));
            $xmlLen = $listLen[1];
            $xml = substr($content, 4, $xmlLen);
            $fromCorpId = trim(substr($content, $xmlLen + 4));
        } catch (BaseException $e) {
            throw new EncryptionException($e->getMessage(), EncryptionException::ERROR_INVALID_XML);
        }

        if ($fromCorpId !== $corpId) {
            throw new EncryptionException('Invalid corpId.', EncryptionException::ERROR_INVALID_CORPID);
        }

        return $xml;
    }

    /**
     * Generate random string.
     *
     * @return string
     */
    private function getRandomStr()
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz'), 0, 16);
    }
}
