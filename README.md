# laravel-dingtalk
钉钉自定义机器人

#### 安装
>composer require yywxf/laravel-dingtalk

* Laravel < 5.5(>5.5的跳过此步骤)
add providers in config/app.php
> // providers  
Yywxf\Dingtalk\DingtalkServiceProvider::class,    
// aliases  
'Ding' => Yywxf\Dingtalk\Facades\Ding::class,

* 发布配置文件
>php artisan vendor:publish --provider=Yywxf\Dingtalk\DingtalkServiceProvider  
覆盖配置文件 --force

#### 文档
>参考[钉钉文档](https://open-doc.dingtalk.com/microapp/serverapi3/iydd5h)
```php
use Ding;

Ding::text('xxx');
```
or
```php
\Ding::text('xxx');
```

text类型
```php
Ding::text($text);
Ding::at(['155****'])->text($text);
Ding::atAll()->text($text);
```

markdown类型
```php
Ding::md($title,$text);
Ding::at(['155****'])->md($title,$text);
Ding::atAll()->md($title,$text);
```
link类型
```php
Ding::link($title, $text, $messageUrl, $picUrl='');
```

整体跳转ActionCard类型
```php
Ding::single($title, $url)->card($title, $text, $btnOrientation = '0', $hideAvatar = '0');
```
独立跳转ActionCard类型
```php
Ding::addButton($title1, $url1)->addButton($title2, $url2)->card($title, $text, $btnOrientation = '0', $hideAvatar = '0');
$btns= [[
            "title"=> "内容不错", 
            "actionURL"=> "https://www.dingtalk.com/"
        ], 
        [
            "title"=> "不感兴趣", 
            "actionURL"=> "https://www.dingtalk.com/"
        ]];
Ding::addButtons($btns)->addButton($title2, $url2)->card($title, $text, $btnOrientation = '0', $hideAvatar = '0');
```

FeedCard类型
```php
Ding::addLink($title1, $msgURL1, $picURL1 = '')->addLink($title2, $msgURL2, $picURL2 = '')->card($title, $text, $btnOrientation = '0', $hideAvatar = '0');
$links= [
             [
                 "title": "时代的火车向前开", 
                 "messageURL": "https://www.dingtalk.com/s?__biz=MzA4NjMwMTA2Ng==&mid=2650316842&idx=1&sn=60da3ea2b29f1dcc43a7c8e4a7c97a16&scene=2&srcid=09189AnRJEdIiWVaKltFzNTw&from=timeline&isappinstalled=0&key=&ascene=2&uin=&devicetype=android-23&version=26031933&nettype=WIFI", 
                 "picURL": "https://www.dingtalk.com/"
             ],
             [
                 "title": "时代的火车向前开2", 
                 "messageURL": "https://www.dingtalk.com/s?__biz=MzA4NjMwMTA2Ng==&mid=2650316842&idx=1&sn=60da3ea2b29f1dcc43a7c8e4a7c97a16&scene=2&srcid=09189AnRJEdIiWVaKltFzNTw&from=timeline&isappinstalled=0&key=&ascene=2&uin=&devicetype=android-23&version=26031933&nettype=WIFI", 
                 "picURL": "https://www.dingtalk.com/"
             ]
         ];
Ding::addLinks($links)->addLink($title1, $msgURL1, $picURL1 = '')->card($title, $text, $btnOrientation = '0', $hideAvatar = '0');
```

```php
class Ding {
        
    /**
     * 指定robot
     *
     * @param string $robot
     * @return $this 
     * @static 
     */ 
    public static function robot($robot = 'default')
    {
        return \Yywxf\Dingtalk\Dingtalk::robot($robot);
    }
    
    /**
     * 指定消息@某人
     * 在 text() 或 md() 前调用
     *
     * @param array $atMobiles
     * @return $this 
     * @static 
     */ 
    public static function at($atMobiles = array())
    {
        return \Yywxf\Dingtalk\Dingtalk::at($atMobiles);
    }
    
    /**
     * 指定text消息@所有人
     * 在 text() 或 md() 前调用
     *
     * @param bool $isAtAll
     * @return $this 
     * @static 
     */ 
    public static function atAll()
    {
        return \Yywxf\Dingtalk\Dingtalk::atAll();
    }
    
    /**
     * 发送text类型消息
     *
     * @param string $content
     * @param array $params
     * @static 
     */ 
    public static function text($content, $params = array())
    {
        return \Yywxf\Dingtalk\Dingtalk::text($content, $params);
    }
    
    /**
     * 发送link类型消息
     *
     * @param string $title
     * @param string $text 消息内容。如果太长只会部分展示
     * @param string $messageUrl 点击消息跳转的URL
     * @param string $picUrl 图片URL
     * @static 
     */ 
    public static function link($title, $text, $messageUrl, $picUrl = '')
    {
        return \Yywxf\Dingtalk\Dingtalk::link($title, $text, $messageUrl, $picUrl);
    }
    
    /**
     * 发送markdown类型
     *
     * @param string $title
     * @param string $text markdown格式的消息
     * @static 
     */ 
    public static function md($title, $text)
    {
        return \Yywxf\Dingtalk\Dingtalk::md($title, $text);
    }
    
    /**
     * 整体跳转，card()前调用，不与addButton()，addButtons()同时使用，single()优先
     *
     * @param $title
     * @param $url
     * @return $this 
     * @static 
     */ 
    public static function single($title, $url)
    {
        return \Yywxf\Dingtalk\Dingtalk::single($title, $url);
    }
    
    /**
     * 独立跳转，card()前调用，不与single() 同时使用，single()优先
     *
     * @param $title
     * @param $url
     * @return $this 
     * @static 
     */ 
    public static function addButton($title, $url)
    {
        return \Yywxf\Dingtalk\Dingtalk::addButton($title, $url);
    }
    
    /**
     * 独立跳转，card()前调用，不与single() 同时使用，single()优先
     *
     * @param $title
     * @param $url
     * @return $this 
     * @static 
     */ 
    public static function addButtons($btns)
    {
        return \Yywxf\Dingtalk\Dingtalk::addButtons($btns);
    }
    
    /**
     * 发送card类型消息
     *
     * @param string $title 首屏会话透出的展示内容
     * @param string $text markdown格式的消息
     * @param string $btnOrientation 0-按钮竖直排列，1-按钮横向排列
     * @param string $hideAvatar 0-正常发消息者头像,1-隐藏发消息者头像
     * @static 
     */ 
    public static function card($title, $text, $btnOrientation = '0', $hideAvatar = '0')
    {
        return \Yywxf\Dingtalk\Dingtalk::card($title, $text, $btnOrientation, $hideAvatar);
    }
    
    /**
     * feed() 前调用
     *
     * @param $title
     * @param $msgURL
     * @param $picURL
     * @return $this 
     * @static 
     */ 
    public static function addLink($title, $msgURL, $picURL = '')
    {
        return \Yywxf\Dingtalk\Dingtalk::addLink($title, $msgURL, $picURL);
    }
    
    /**
     * feed() 前调用
     *
     * @param $title
     * @param $msgURL
     * @param $picURL
     * @return $this 
     * @static 
     */ 
    public static function addlinks($links)
    {
        return \Yywxf\Dingtalk\Dingtalk::addlinks($links);
    }
    
    /**
     * 发送FeedCard类型消息
     *
     * @static 
     */ 
    public static function feed()
    {
        return \Yywxf\Dingtalk\Dingtalk::feed();
    }
    
    /**
     * 发送消息
     *
     * @param $data
     * @static 
     */ 
    public static function send($data)
    {
        return \Yywxf\Dingtalk\Dingtalk::send($data);
    }
    
    /**
     * curl 请求
     *
     * @param $remote_server
     * @param $post_string
     * @return bool|string 
     * @static 
     */ 
    public static function request_by_curl($remote_server, $post_string)
    {
        return \Yywxf\Dingtalk\Dingtalk::request_by_curl($remote_server, $post_string);
    }
     
}
```