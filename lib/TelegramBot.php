<?php


namespace Medialine\Deploy;

use Bitrix\Main\Config\Option;

class TelegramBot
{
    protected $module_id = 'medialine.deploy';
    protected $token;
    protected $chatId;

    public function __construct()
    {
        $arDefaultOptions = Option::getDefaults($this->module_id);
        //TODO Exception if option empty
        $this->token = $arDefaultOptions['TG_TOKEN'];
        $this->chatId = $arDefaultOptions['TG_CHAT_ID'];
    }

    public function sendMessage($message)
    {
        return $this->getResponse($message);
    }


    protected function getResponse($message, $method = 'sendMessage')
    {
        if(is_array($message)){
            $message = print_r($message, true);
        }
        $arPostFields = [
            'text' => $message,
            'chat_id' => $this->chatId
            ];

        $ch = curl_init();
        curl_setopt_array(
            $ch,
            [
                CURLOPT_URL=> 'https://api.telegram.org/bot' . $this->token . '/' . $method,
                CURLOPT_POST=> true,
                CURLOPT_RETURNTRANSFER=> true,
                CURLOPT_TIMEOUT=> 10,
                CURLOPT_POSTFIELDS=> $arPostFields,
            ]
        );
        return json_decode(curl_exec($ch), true);
    }

    /**
     * @param string $chatId
     */
    public function setChatId($chatId)
    {
        $this->chatId = $chatId;
    }

}