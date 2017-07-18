<?php

namespace EntWeChat\Server;

use EntWeChat\Core\Exceptions\FaultException;
use EntWeChat\Core\Exceptions\InvalidArgumentException;
use EntWeChat\Core\Exceptions\RuntimeException;
use EntWeChat\Encryption\Encryptor;
use EntWeChat\Message\AbstractMessage;
use EntWeChat\Message\Raw as RawMessage;
use EntWeChat\Message\Text;
use EntWeChat\Support\Collection;
use EntWeChat\Support\Log;
use EntWeChat\Support\XML;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Guard.
 */
class Guard
{
    /**
     * Empty string.
     */
    const SUCCESS_EMPTY_RESPONSE = 'success';

    const TEXT_MSG = 2;
    const IMAGE_MSG = 4;
    const VOICE_MSG = 8;
    const VIDEO_MSG = 16;
    const SHORT_VIDEO_MSG = 32;
    const LOCATION_MSG = 64;
    const LINK_MSG = 128;
    const DEVICE_EVENT_MSG = 256;
    const DEVICE_TEXT_MSG = 512;
    const EVENT_MSG = 1048576;
    const ALL_MSG = 1049598;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $corpId;

    /**
     * @var Encryptor
     */
    protected $encryptor;

    /**
     * @var string|callable
     */
    protected $messageHandler;

    /**
     * @var int
     */
    protected $messageFilter;

    /**
     * @var array
     */
    protected $messageTypeMapping = [
        'text'         => 2,
        'image'        => 4,
        'voice'        => 8,
        'video'        => 16,
        'shortvideo'   => 32,
        'location'     => 64,
        'link'         => 128,
        'device_event' => 256,
        'device_text'  => 512,
        'event'        => 1048576,
    ];

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * Constructor.
     *
     * @param string  $token
     * @param string  $corpId
     * @param Request $request
     */
    public function __construct($token, $corpId, Request $request = null)
    {
        $this->token = $token;
        $this->corpId = $corpId;
        $this->request = $request ?: Request::createFromGlobals();
    }

    /**
     * Enable/Disable debug mode.
     *
     * @param bool $debug
     *
     * @return $this
     */
    public function debug($debug = true)
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * Handle and return response.
     *
     * @throws BadRequestException
     *
     * @return Response
     */
    public function serve()
    {
        Log::debug('Request received:', [
            'Method'   => $this->request->getMethod(),
            'URI'      => $this->request->getRequestUri(),
            'Query'    => $this->request->getQueryString(),
            'Protocol' => $this->request->server->get('SERVER_PROTOCOL'),
            'Content'  => $this->request->getContent(),
        ]);

        $this->validate($this->token);

        if ($str = $this->request->get('echostr')) {
            $str = $this->encryptor->decrypt($str, $this->corpId);
            Log::debug("Output 'echostr' is '$str'.");

            return new Response($str);
        }

        $result = $this->handleRequest();

        $response = $this->buildResponse($result['to'], $result['from'], $result['response']);

        Log::debug('Server response created:', compact('response'));

        return new Response($response);
    }

    /**
     * Validation request params.
     *
     * @param string $token
     *
     * @throws FaultException
     */
    public function validate($token)
    {
        $params = [
            $this->request->get('echostr'),
            $token,
            $this->request->get('timestamp'),
            $this->request->get('nonce'),
        ];

        if (!$this->debug && $this->request->get('msg_signature') !== $this->signature($params)) {
            throw new FaultException('Invalid request signature.', 400);
        }
    }

    /**
     * Add a event listener.
     *
     * @param callable $callback
     * @param int      $option
     *
     * @throws InvalidArgumentException
     *
     * @return Guard
     */
    public function setMessageHandler($callback = null, $option = self::ALL_MSG)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Argument #2 is not callable.');
        }

        $this->messageHandler = $callback;
        $this->messageFilter = $option;

        return $this;
    }

    /**
     * Return the message listener.
     *
     * @return string
     */
    public function getMessageHandler()
    {
        return $this->messageHandler;
    }

    /**
     * Request getter.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Request setter.
     *
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Set Encryptor.
     *
     * @param Encryptor $encryptor
     *
     * @return Guard
     */
    public function setEncryptor(Encryptor $encryptor)
    {
        $this->encryptor = $encryptor;

        return $this;
    }

    /**
     * Return the encryptor instance.
     *
     * @return Encryptor
     */
    public function getEncryptor()
    {
        return $this->encryptor;
    }

    /**
     * Build response.
     *
     * @param       $to
     * @param       $from
     * @param mixed $message
     *
     * @throws \EntWeChat\Core\Exceptions\InvalidArgumentException
     *
     * @return string
     */
    protected function buildResponse($to, $from, $message)
    {
        if (empty($message) || $message === self::SUCCESS_EMPTY_RESPONSE) {
            return self::SUCCESS_EMPTY_RESPONSE;
        }

        if ($message instanceof RawMessage) {
            return $message->get('content', self::SUCCESS_EMPTY_RESPONSE);
        }

        if (is_string($message) || is_numeric($message)) {
            $message = new Text(['content' => $message]);
        }

        if (!$this->isMessage($message)) {
            $messageType = gettype($message);
            throw new InvalidArgumentException("Invalid Message type .'{$messageType}'");
        }

        $response = $this->buildReply($to, $from, $message);

        Log::debug('Message safe mode is enable.');
        $response = $this->encryptor->encryptMsg(
            $response,
            $this->request->get('nonce'),
            $this->request->get('timestamp')
        );

        return $response;
    }

    /**
     * Whether response is message.
     *
     * @param mixed $message
     *
     * @return bool
     */
    protected function isMessage($message)
    {
        if (is_array($message)) {
            foreach ($message as $element) {
                if (!is_subclass_of($element, AbstractMessage::class)) {
                    return false;
                }
            }

            return true;
        }

        return is_subclass_of($message, AbstractMessage::class);
    }

    /**
     * Get request message.
     *
     * @throws BadRequestException
     *
     * @return array
     */
    public function getMessage()
    {
        $message = $this->parseMessageFromRequest($this->request->getContent(false));

        if (!is_array($message) || empty($message)) {
            throw new BadRequestException('Invalid request.');
        }

        return $message;
    }

    /**
     * Handle request.
     *
     * @throws \EntWeChat\Core\Exceptions\RuntimeException
     * @throws \EntWeChat\Server\BadRequestException
     *
     * @return array
     */
    protected function handleRequest()
    {
        $message = $this->getMessage();
        $response = $this->handleMessage($message);

        return [
            'to'       => $message['FromUserName'],
            'from'     => $message['ToUserName'],
            'response' => $response,
        ];
    }

    /**
     * Handle message.
     *
     * @param array $message
     *
     * @return mixed
     */
    protected function handleMessage(array $message)
    {
        $handler = $this->messageHandler;

        if (!is_callable($handler)) {
            Log::debug('No handler enabled.');

            return;
        }

        Log::debug('Message detail:', $message);

        $message = new Collection($message);

        $type = $this->messageTypeMapping[$message->get('MsgType')];

        $response = null;

        if ($this->messageFilter & $type) {
            $response = call_user_func_array($handler, [$message]);
        }

        return $response;
    }

    /**
     * Build reply XML.
     *
     * @param string          $to
     * @param string          $from
     * @param AbstractMessage $message
     *
     * @return string
     */
    protected function buildReply($to, $from, $message)
    {
        $base = [
            'ToUserName'   => $to,
            'FromUserName' => $from,
            'CreateTime'   => time(),
            'MsgType'      => is_array($message) ? current($message)->getType() : $message->getType(),
        ];

        $transformer = new Transformer();

        return XML::build(array_merge($base, $transformer->transform($message)));
    }

    /**
     * Get signature.
     *
     * @param array $request
     *
     * @return string
     */
    protected function signature($request)
    {
        sort($request, SORT_STRING);

        return sha1(implode($request));
    }

    /**
     * Parse message array from raw php input.
     *
     * @param string|resource $content
     *
     * @throws \EntWeChat\Core\Exceptions\RuntimeException
     * @throws \EntWeChat\Encryption\EncryptionException
     *
     * @return array
     */
    protected function parseMessageFromRequest($content)
    {
        $content = strval($content);

        $arrayable = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $arrayable;
        }

        if (!$this->encryptor) {
            throw new RuntimeException('Safe mode Encryptor is necessary, please use Guard::setEncryptor(Encryptor $encryptor) set the encryptor instance.');
        }

        $message = $this->encryptor->decryptMsg(
            $this->request->get('msg_signature'),
            $this->request->get('nonce'),
            $this->request->get('timestamp'),
            $content
        );

        return $message;
    }
}
