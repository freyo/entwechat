<?php

namespace EntWeChat\Chat;

use EntWeChat\Core\AbstractAPI;

/**
 * Class Chat.
 */
class Chat extends AbstractAPI
{
    const API_GET          = 'https://qyapi.weixin.qq.com/cgi-bin/chat/get';
    const API_CREATE       = 'https://qyapi.weixin.qq.com/cgi-bin/chat/create';
    const API_UPDATE       = 'https://qyapi.weixin.qq.com/cgi-bin/chat/update';
    const API_QUIT         = 'https://qyapi.weixin.qq.com/cgi-bin/chat/quit';
    const API_CLEAR_NOTIFY = 'https://qyapi.weixin.qq.com/cgi-bin/chat/clearnotify';
    const API_SEND         = 'https://qyapi.weixin.qq.com/cgi-bin/chat/send';
    const API_SET_MUTE     = 'https://qyapi.weixin.qq.com/cgi-bin/chat/setmute';

    const CHAT_TYPE_SINGLE = 'single';  // 单聊
    const CHAT_TYPE_GROUP  = 'group';   // 群聊

    const MSG_TYPE_TEXT  = 'text';   // 文本
    const MSG_TYPE_VOICE = 'voice';  // 语音
    const MSG_TYPE_IMAGE = 'image';  // 图片
    const MSG_TYPE_FILE  = 'file';   // 文件
    const MSG_TYPE_LINK  = 'link';   // 文件

    /**
     * Fetch a chat by chat id.
     *
     * @param string $chatId
     *
     * @return \EntWeChat\Support\Collection
     */
    public function get($chatId)
    {
        $params = [
            'chatid' => $chatId,
        ];

        return $this->parseJSON('get', [self::API_GET, $params]);
    }

    /**
     * Create chat.
     *
     * @param string $chatId
     * @param string $name
     * @param string $owner
     * @param array  $userList
     *
     * @return \EntWeChat\Support\Collection
     */
    public function create($chatId, $name, $owner, array $userList)
    {
        $params = [
            'chatid' => $chatId,
            'name' => $name,
            'owner' => $owner,
            'userlist' => $userList,
        ];

        return $this->parseJSON('json', [self::API_CREATE, $params]);
    }

    /**
     * Update chat.
     *
     * @param string $chatId
     * @param string $opUser
     * @param array  $chatInfo
     *
     * @return \EntWeChat\Support\Collection
     */
    public function update($chatId, $opUser, array $chatInfo = [])
    {
        $params = array_merge($chatInfo, [
            'chatid' => $chatId,
            'op_user' => $opUser,
        ]);

        return $this->parseJSON('json', [self::API_UPDATE, $params]);
    }

    /**
     * Quit chat.
     *
     * @param string $chatId
     * @param string $opUser
     *
     * @return \EntWeChat\Support\Collection
     */
    public function quit($chatId, $opUser)
    {
        $params = [
            'chatid' => $chatId,
            'op_user' => $opUser,
        ];

        return $this->parseJSON('json', [self::API_QUIT, $params]);
    }

    /**
     * Clear chat.
     *
     * @param string $chatId
     * @param array  $chat
     *
     * @return \EntWeChat\Support\Collection
     */
    public function clear($chatId, array $chat)
    {
        $params = [
            'chatid' => $chatId,
            'chat' => $chat,
        ];

        return $this->parseJSON('json', [self::API_CLEAR_NOTIFY, $params]);
    }

    /**
     * Send chat.
     *
     * @param string $type
     * @param string $id
     * @param string $sender
     * @param string $msgType
     * @param mixed  $message
     *
     * @return \EntWeChat\Support\Collection
     */
    public function send($type, $id, $sender, $msgType, $message)
    {
        $content = (new Transformer($msgType, $message))->transform();

        $params = array_merge([
            'receiver' => array(
                'type' => $type,
                'id' => $id
            ),
            'sender' => $sender,
        ], $content);

        return $this->parseJSON('json', [self::API_SEND, $params]);
    }

    /**
     * Set chat.
     *
     * @return \EntWeChat\Support\Collection
     */
    public function mute(array $userMuteList)
    {
        $params = [
            'user_mute_list' => $userMuteList,
        ];

        return $this->parseJSON('json', [self::API_SET_MUTE, $params]);
    }
}
