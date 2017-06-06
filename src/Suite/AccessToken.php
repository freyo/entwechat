<?php

namespace EntWeChat\Suite;

use EntWeChat\Core\AccessToken as CoreAccessToken;
use EntWeChat\Core\Exceptions\HttpException;

class AccessToken extends CoreAccessToken
{
    /**
     * API.
     */
    const API_TOKEN_GET = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_suite_token';
    /**
     * Ticket.
     *
     * @var \EntWeChat\Suite\Ticket
     */
    protected $ticket;
    /**
     * {@inheritdoc}.
     */
    protected $queryName = 'suite_access_token';

    /**
     * {@inheritdoc}.
     */
    protected $tokenJsonKey = 'suite_access_token';

    /**
     * {@inheritdoc}.
     */
    protected $prefix = 'EntWeChat.suite.suite_access_token.';

    /**
     * Set VerifyTicket.
     *
     * @param \EntWeChat\Suite\Ticket $ticket
     *
     * @return $this
     */
    public function setTicket(Ticket $ticket)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * {@inheritdoc}.
     */
    public function getTokenFromServer()
    {
        $data = [
            'suite_id'     => $this->id,
            'suite_secret' => $this->secret,
            'suite_ticket' => $this->ticket->getTicket(),
        ];

        $http = $this->getHttp();

        $token = $http->parseJSON($http->json(self::API_TOKEN_GET, $data));

        if (empty($token[$this->tokenJsonKey])) {
            throw new HttpException('Request SuiteAccessToken fail. response: '.json_encode($token, JSON_UNESCAPED_UNICODE));
        }

        return $token;
    }
}
