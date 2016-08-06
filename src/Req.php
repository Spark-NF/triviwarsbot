<?php
namespace TriviWars;

use Longman\TelegramBot\Request;

class Req
{
    public static function send($chat_id, $text, $markup = null)
    {
        $data = [
            'chat_id'       => $chat_id,
            'parse_mode'    => 'MARKDOWN',
            'text'          => $text,
        ];
        if (!empty($markup)) {
            $data['reply_markup'] = $markup;
        }
        return Request::sendMessage($data);
    }

    public static function success($chat_id, $text, $markup = null)
    {
        return self::send($chat_id, '✅ *'.$text.'*', $markup);
    }

    public static function error($chat_id, $text, $markup = null)
    {
        return self::send($chat_id, '❌ *'.$text.'*', $markup);
    }

    public static function emptyResponse()
    {
        return Request::emptyResponse();
    }

    public static function debug($chat_id, $object)
    {
        return self::send($chat_id, var_export($object, true));
    }
}