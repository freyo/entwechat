<?php

namespace EntWeChat\Suite\Api;

use EntWeChat\Core\AbstractAPI;
use EntWeChat\Suite\AccessToken;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractSuite extends AbstractAPI
{
    /**
     * Request.
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * AbstractSuite constructor.
     *
     * @param \EntWeChat\Suite\AccessToken              $accessToken
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(AccessToken $accessToken, Request $request)
    {
        parent::__construct($accessToken);

        $this->request = $request;
    }

    /**
     * Get Suite Id.
     *
     * @return string
     */
    public function getSuiteId()
    {
        return $this->getAccessToken()->getId();
    }
}
