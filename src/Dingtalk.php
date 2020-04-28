<?php

namespace Yywxf\Dingtalk;

use Illuminate\Support\Facades\Log;

class Dingtalk
{
    private $apiUrl;
    private $webhook;
    private $timeout;
    private $at;
    private $single;    //actioncard
    private $btns;  //actioncard
    private $links; //feedcard

    public function __construct()
    {
        $this->apiUrl = config('dingtalk.url');
        $this->timeout = config('dingtalk.timeout');
        $this->robot();
        $this->at();
    }

    /**
     * 指定robot
     *
     * @param string $robot
     * @return $this
     */
    public function robot(string $robot = 'default')
    {
        $this->webhook = $this->apiUrl . config('dingtalk.robot.' . $robot);
        return $this;
    }

    /**
     * 指定消息@某人
     * 在 text() 或 md() 前调用
     *
     * @param array $atMobiles
     * @return $this
     */
    public function at(array $atMobiles = [])
    {
        $this->at = [
            'atMobiles' => $atMobiles,
            'isAtAll'   => false,
        ];
        return $this;
    }

    /**
     * 指定text消息@所有人
     * 在 text() 或 md() 前调用
     *
     * @param bool $isAtAll
     * @return $this
     */
    public function atAll()
    {
        $this->at = [
            'atMobiles' => [],
            'isAtAll'   => true,
        ];
        return $this;
    }

    /**
     * 发送text类型消息
     *
     * @param string $content
     * @param array  $params
     */
    public function text(string $content, array $params = [])
    {
        $content .= empty($params) ? '' : ' ' . json_encode($params, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $data = [
            'msgtype' => 'text',
            'text'    => ['content' => $content],
            'at'      => $this->at,
        ];
        $this->at();//初始化
        return $this->send($data);
    }

    /**
     * 发送link类型消息
     *
     * @param string $title
     * @param string $text       消息内容。如果太长只会部分展示
     * @param string $messageUrl 点击消息跳转的URL
     * @param string $picUrl     图片URL
     */
    public function link($title, $text, $messageUrl, $picUrl = '')
    {
        $data = [
            'msgtype' => 'link',
            'link'    => [
                'title'      => $title,
                'text'       => $text,
                'messageUrl' => $messageUrl,
                'picUrl'     => $picUrl,
            ],
        ];
        $this->send($data);
    }

    /**
     * 发送markdown类型
     *
     * @param string $title
     * @param string $text markdown格式的消息
     */
    public function md($title, $text)
    {
        $data = [
            'msgtype'  => 'markdown',
            'markdown' => [
                'title' => $title,
                'text'  => $text,
            ],
            'at'       => $this->at,
        ];
        $this->at();//初始化
        return $this->send($data);
    }

    /**
     * 整体跳转，card()前调用，不与addButton()，addButtons()同时使用，single()优先
     *
     * @param $title
     * @param $url
     * @return $this
     */
    public function single($title, $url)
    {
        $this->single = [
            'title' => $title,
            'url'   => $url,
        ];
        unset($this->btns);
        return $this;
    }

    /**
     * 独立跳转，card()前调用，不与single() 同时使用，single()优先
     *
     * @param $title
     * @param $url
     * @return $this
     */
    public function addButton($title, $url)
    {
        $this->btns[] = [
            'title'     => $title,
            'actionURL' => $url,
        ];
        unset($this->single);
        return $this;
    }

    /**
     * 独立跳转，card()前调用，不与single() 同时使用，single()优先
     *
     * @param $title
     * @param $url
     * @return $this
     */
    public function addButtons(array $btns)
    {
        $this->btns[] = $btns;
        unset($this->single);
        return $this;
    }

    /**
     * 发送card类型消息
     *
     * @param string $title          首屏会话透出的展示内容
     * @param string $text           markdown格式的消息
     * @param string $btnOrientation 0-按钮竖直排列，1-按钮横向排列
     * @param string $hideAvatar     0-正常发消息者头像,1-隐藏发消息者头像
     */
    public function card($title, $text, $btnOrientation = '0', $hideAvatar = '0')
    {
        $data = [
            'msgtype'    => 'actionCard',
            'actionCard' => [
                'title'          => $title,
                'text'           => $text,
                'hideAvatar'     => $hideAvatar,
                'btnOrientation' => $btnOrientation,
            ],
            'at'         => $this->at,
        ];
        if (!empty($this->single)) {
            $data['actionCard']['singleTitle'] = $this->single['title'];
            $data['actionCard']['singleURL'] = $this->single['url'];
            unset($this->single);
        } else if (!empty($this->btns)) {
            $data['actionCard']['btns'] = $this->btns;
            unset($this->btns);
        }
        return $this->send($data);
    }

    /**
     * feed() 前调用
     *
     * @param $title
     * @param $msgURL
     * @param $picURL
     * @return $this
     */
    public function addLink($title, $msgURL, $picURL = '')
    {
        $this->links[] = [
            'title'      => $title,
            'messageURL' => $msgURL,
            'picURL'     => $picURL,
        ];
        return $this;
    }

    /**
     * feed() 前调用
     *
     * @param $title
     * @param $msgURL
     * @param $picURL
     * @return $this
     */
    public function addlinks(array $links)
    {
        $this->links = $links;
        return $this;
    }

    /**
     * 发送FeedCard类型消息
     */
    public function feed()
    {
        $data = [
            'msgtype'  => 'feedCard',
            'feedCard' => [
                'links' => $this->links,
            ],
        ];
        unset($this->links);
        return $this->send($data);
    }

    /**
     * 发送消息
     *
     * @param $data
     */
    public function send($data)
    {
        $data_string = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = $this->request_by_curl($this->webhook, $data_string);
        $res = json_decode($result);
        $this->robot();//初始化
        try {
            if ($res->errcode !== 0) {
                Log::error('[Ding]' . $result);
                return ['code' => $res->errcode, 'msg' => $res->errmsg ?? ''];
            } else {
                return ['code' => $res->errcode, 'msg' => $res->errmsg];
            }
        } catch (\Exception $e) {
            Log::error('[Ding] error' . $result . ' ' . $e->getMessage());
            return ['code' => $res->errcode, 'msg' => $e->getMessage()];
        }
    }

    /**
     * curl 请求
     *
     * @param $remote_server
     * @param $post_string
     * @return bool|string
     */
    public function request_by_curl($remote_server, $post_string)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_server);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json;charset=utf-8']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

}
