<?php

namespace EntWeChat\Card;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use EntWeChat\Core\AbstractAPI;
use EntWeChat\Support\Arr;
use Psr\Http\Message\ResponseInterface;

class Card extends AbstractAPI
{
    /**
     * Cache.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Ticket cache key.
     *
     * @var string
     */
    protected $ticketCacheKey;

    /**
     * Ticket cache prefix.
     *
     * @var string
     */
    protected $ticketCachePrefix = 'entwechat.card_api_ticket.';

    const API_CREATE_CARD = 'https://qyapi.weixin.qq.com/cgi-bin/card/create';
    const API_CREATE_QRCODE = 'https://qyapi.weixin.qq.com/cgi-bin/card/qrcode/create';
    const API_SHOW_QRCODE = 'https://mp.weixin.qq.com/cgi-bin/showqrcode';
    const API_GET_CARD_TICKET = 'https://qyapi.weixin.qq.com/cgi-bin/ticket/get';
    const API_GET_HTML = 'https://qyapi.weixin.qq.com/cgi-bin/card/mpnews/gethtml';
    const API_GET_CODE = 'https://qyapi.weixin.qq.com/cgi-bin/card/code/get';
    const API_CONSUME_CARD = 'https://qyapi.weixin.qq.com/cgi-bin/card/code/consume';
    const API_GET_CARD = 'https://qyapi.weixin.qq.com/cgi-bin/card/get';
    const API_LIST_CARD = 'https://qyapi.weixin.qq.com/cgi-bin/card/batchget';
    const API_MODIFY_STOCK = 'https://qyapi.weixin.qq.com/cgi-bin/card/modifystock';
    const API_DELETE_CARD = 'https://qyapi.weixin.qq.com/cgi-bin/card/delete';

    // 卡券类型
    const TYPE_GENERAL_COUPON = 'GENERAL_COUPON';   // 通用券
    const TYPE_GROUPON = 'GROUPON';          // 团购券
    const TYPE_DISCOUNT = 'DISCOUNT';         // 折扣券
    const TYPE_GIFT = 'GIFT';             // 礼品券
    const TYPE_CASH = 'CASH';             // 代金券

    // 卡券状态
    const CARD_STATUS_NOT_VERIFY = 'CARD_STATUS_NOT_VERIFY';    // 待审核
    const CARD_STATUS_VERIFY_FAIL = 'CARD_STATUS_VERIFY_FAIL';   // 审核失败
    const CARD_STATUS_VERIFY_OK = 'CARD_STATUS_VERIFY_OK';     // 通过审核
    const CARD_STATUS_USER_DELETE = 'CARD_STATUS_USER_DELETE';   // 卡券被商户删除
    const CARD_STATUS_USER_DISPATCH = 'CARD_STATUS_USER_DISPATCH'; // 在公众平台投放过的卡券

    /**
     * 创建卡券.
     *
     * @param string $cardType
     * @param array  $baseInfo
     * @param array  $especial
     * @param array  $advancedInfo
     *
     * @return \EntWeChat\Support\Collection
     */
    public function create($cardType = 'member_card', array $baseInfo = [], array $especial = [], array $advancedInfo = [])
    {
        $params = [
            'card' => [
                'card_type'           => strtoupper($cardType),
                strtolower($cardType) => array_merge(['base_info' => $baseInfo], $especial, ['advanced_info' => $advancedInfo]),
            ],
        ];

        return $this->parseJSON('json', [self::API_CREATE_CARD, $params]);
    }

    /**
     * 创建二维码.
     *
     * @param array $cards
     *
     * @return \EntWeChat\Support\Collection
     */
    public function QRCode(array $cards = [])
    {
        return $this->parseJSON('json', [self::API_CREATE_QRCODE, $cards]);
    }

    /**
     * ticket 换取二维码图片.
     *
     * @param string $ticket
     *
     * @return array
     */
    public function showQRCode($ticket = null)
    {
        $params = [
            'ticket' => $ticket,
        ];

        $http = $this->getHttp();

        /** @var ResponseInterface $response */
        $response = $http->get(self::API_SHOW_QRCODE, $params);

        return [
            'status'  => $response->getStatusCode(),
            'reason'  => $response->getReasonPhrase(),
            'headers' => $response->getHeaders(),
            'body'    => strval($response->getBody()),
            'url'     => self::API_SHOW_QRCODE.'?'.http_build_query($params),
        ];
    }

    /**
     * 通过ticket换取二维码 链接.
     *
     * @param string $ticket
     *
     * @return string
     */
    public function getQRCodeUrl($ticket)
    {
        return self::API_SHOW_QRCODE.'?ticket='.$ticket;
    }

    /**
     * 获取 卡券 Api_ticket.
     *
     * @param bool $refresh 是否强制刷新
     *
     * @return string $apiTicket
     */
    public function getAPITicket($refresh = false)
    {
        $key = $this->getTicketCacheKey();

        $ticket = $this->getCache()->fetch($key);

        if (!$ticket || $refresh) {
            $result = $this->parseJSON('get', [self::API_GET_CARD_TICKET, ['type' => 'wx_card']]);

            $this->getCache()->save($key, $result['ticket'], $result['expires_in'] - 500);

            return $result['ticket'];
        }

        return $ticket;
    }

    /**
     * 微信卡券：JSAPI 卡券发放.
     *
     * @param array $cards
     *
     * @return string
     */
    public function jsConfigForAssign(array $cards)
    {
        return json_encode(array_map(function ($card) {
            return $this->attachExtension($card['card_id'], $card);
        }, $cards));
    }

    /**
     * 生成 js添加到卡包 需要的 card_list 项.
     *
     * @param string $cardId
     * @param array  $extension
     *
     * @return string
     */
    public function attachExtension($cardId, array $extension = [])
    {
        $timestamp = time();
        $ext = [
            'code'                 => Arr::get($extension, 'code'),
            'openid'               => Arr::get($extension, 'openid', Arr::get($extension, 'open_id')),
            'timestamp'            => $timestamp,
            'outer_id'             => Arr::get($extension, 'outer_id'),
            'balance'              => Arr::get($extension, 'balance'),
            'fixed_begintimestamp' => Arr::get($extension, 'fixed_begintimestamp'),
            'outer_str'            => Arr::get($extension, 'outer_str'),
        ];
        $ext['signature'] = $this->getSignature(
            $this->getAPITicket(),
            $timestamp,
            $cardId,
            $ext['code'],
            $ext['openid'],
            $ext['balance']
        );

        return [
            'cardId'  => $cardId,
            'cardExt' => json_encode($ext),
        ];
    }

    /**
     * 生成签名.
     *
     * @return string
     */
    public function getSignature()
    {
        $params = func_get_args();
        sort($params, SORT_STRING);

        return sha1(implode($params));
    }

    /**
     * 核查code接口.
     *
     * @param string $cardId
     * @param array  $code
     *
     * @return \EntWeChat\Support\Collection
     */
    public function checkCode($cardId, $code)
    {
        $params = [
            'card_id' => $cardId,
            'code'    => $code,
        ];

        return $this->parseJSON('json', [self::API_CHECK_CODE, $params]);
    }

    /**
     * 查询Code接口.
     *
     * @param string $code
     * @param bool   $checkConsume
     * @param string $cardId
     *
     * @return \EntWeChat\Support\Collection
     */
    public function getCode($code, $checkConsume, $cardId)
    {
        $params = [
            'code'          => $code,
            'check_consume' => $checkConsume,
            'card_id'       => $cardId,
        ];

        return $this->parseJSON('json', [self::API_GET_CODE, $params]);
    }

    /**
     * 核销Code接口.
     *
     * @param string $code
     * @param string $cardId
     *
     * @return \EntWeChat\Support\Collection
     */
    public function consume($code, $cardId = null)
    {
        if (strlen($code) === 28 && $cardId && strlen($cardId) !== 28) {
            list($code, $cardId) = [$cardId, $code];
        }

        $params = [
            'code' => $code,
        ];

        if ($cardId) {
            $params['card_id'] = $cardId;
        }

        return $this->parseJSON('json', [self::API_CONSUME_CARD, $params]);
    }

    /**
     * 图文消息群发卡券.
     *
     * @param string $cardId
     *
     * @return \EntWeChat\Support\Collection
     */
    public function getHtml($cardId)
    {
        $params = [
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [self::API_GET_HTML, $params]);
    }

    /**
     * 查看卡券详情.
     *
     * @param string $cardId
     *
     * @return \EntWeChat\Support\Collection
     */
    public function getCard($cardId)
    {
        $params = [
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [self::API_GET_CARD, $params]);
    }

    /**
     * 批量查询卡列表.
     *
     * @param int    $offset
     * @param int    $count
     * @param string $statusList
     *
     * @return \EntWeChat\Support\Collection
     */
    public function lists($offset = 0, $count = 10, $statusList = 'CARD_STATUS_VERIFY_OK')
    {
        $params = [
            'offset'      => $offset,
            'count'       => $count,
            'status_list' => $statusList,
        ];

        return $this->parseJSON('json', [self::API_LIST_CARD, $params]);
    }

    /**
     * 增加库存.
     *
     * @param string $cardId
     * @param int    $amount
     *
     * @return \EntWeChat\Support\Collection
     */
    public function increaseStock($cardId, $amount)
    {
        return $this->updateStock($cardId, $amount, 'increase');
    }

    /**
     * 减少库存.
     *
     * @param string $cardId
     * @param int    $amount
     *
     * @return \EntWeChat\Support\Collection
     */
    public function reduceStock($cardId, $amount)
    {
        return $this->updateStock($cardId, $amount, 'reduce');
    }

    /**
     * 修改库存接口.
     *
     * @param string $cardId
     * @param int    $amount
     * @param string $action
     *
     * @return \EntWeChat\Support\Collection
     */
    protected function updateStock($cardId, $amount, $action = 'increase')
    {
        $key = $action === 'increase' ? 'increase_stock_value' : 'reduce_stock_value';
        $params = [
            'card_id' => $cardId,
            $key      => abs($amount),
        ];

        return $this->parseJSON('json', [self::API_MODIFY_STOCK, $params]);
    }

    /**
     * 删除卡券接口.
     *
     * @param string $cardId
     *
     * @return \EntWeChat\Support\Collection
     */
    public function delete($cardId)
    {
        $params = [
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [self::API_DELETE_CARD, $params]);
    }

    /**
     * Set cache manager.
     *
     * @param \Doctrine\Common\Cache\Cache $cache
     *
     * @return $this
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Return cache manager.
     *
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache ?: $this->cache = new FilesystemCache(sys_get_temp_dir());
    }

    /**
     * Set Api_ticket cache prifix.
     *
     * @param string $prefix
     *
     * @return $this
     */
    public function setTicketCachePrefix($prefix)
    {
        $this->ticketCachePrefix = $prefix;

        return $this;
    }

    /**
     * Set Api_ticket cache key.
     *
     * @param string $cacheKey
     *
     * @return $this
     */
    public function setTicketCacheKey($cacheKey)
    {
        $this->ticketCacheKey = $cacheKey;

        return $this;
    }

    /**
     * Get ApiTicket token cache key.
     *
     * @return string
     */
    public function getTicketCacheKey()
    {
        if (is_null($this->ticketCacheKey)) {
            return $this->ticketCachePrefix.$this->getAccessToken()->getFingerprint();
        }

        return $this->ticketCacheKey;
    }

    /**
     * Set current url.
     *
     * @param string $url
     *
     * @return Card
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}
