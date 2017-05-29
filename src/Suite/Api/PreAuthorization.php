<?php

namespace EntWeChat\Suite\Api;

use EntWeChat\Core\Exceptions\InvalidArgumentException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PreAuthorization extends AbstractSuite
{
    /**
     * Get pre auth code url.
     */
    const GET_PRE_AUTH_CODE = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_pre_auth_code';

    /**
     * Pre auth link.
     */
    const PRE_AUTH_LINK = 'https://qy.weixin.qq.com/cgi-bin/loginpage?suite_id=%s$&pre_auth_code=%s$&redirect_uri=%s';

    /**
     * Redirect to WeChat PreAuthorization page.
     *
     * @param string $url
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect($url)
    {
        return new RedirectResponse(
            sprintf(self::PRE_AUTH_LINK, $this->getSuiteId(), $this->getCode(), urlencode($url))
        );
    }

    /**
     * Get pre auth code.
     *
     * @throws \EntWeChat\Core\Exceptions\InvalidArgumentException
     *
     * @return string
     */
    public function getCode()
    {
        $data = [
            'suite_id' => $this->getSuiteId(),
        ];

        $result = $this->parseJSON('json', [self::GET_PRE_AUTH_CODE, $data]);

        if (empty($result['pre_auth_code'])) {
            throw new InvalidArgumentException('Invalid response.');
        }

        return $result['pre_auth_code'];
    }
}
