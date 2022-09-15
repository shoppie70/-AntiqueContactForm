<?php

class CreateMailBody
{
    public static function getBody( array $request, $date, $uri, $agent )
    {
        return "[ お問い合わせ内容 ]
        
    ■お名前
    　{$request['name']}

    ■TEL
    　{$request['tel']}

    ■Email
    　{$request['email']}

    ■お問い合わせ内容
    　{$request['detail']}

    送信日時：{$date}
    情報：{$uri}, {$agent}";
    }
}