<?php
namespace TriviWars\DB;

use Longman\TelegramBot\Telegram;

class MyTelegram extends Telegram
{
    public function __construct($api_key, $bot_name)
    {
        parent::__construct($api_key, $bot_name);
    }

    public function getPDO()
    {
        return $this->pdo;
    }
}