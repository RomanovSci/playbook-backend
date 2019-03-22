<?php

namespace App\Services\SmsDeliveryService;

use App\Objects\Service\ExecResult;

/**
 * Class SmsDeliveryServiceSmsRu
 * @package App\Services\SmsDeliveryService
 */
class SmsDeliveryServiceSmsRu implements SmsDeliveryServiceInterface
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $protocol = 'https';

    /**
     * @var string
     */
    private $domain = 'sms.ru';

    /**
     * @var int
     */
    private $attempts = 5;    //количество попыток достучаться до сервера если он не доступен

    /**
     * SmsDeliveryServiceSmsRu constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->apiKey = env('SMS_DELIVERY_SMS_RU_API_KEY');
    }

    /**
     * @inheritdoc
     * @param string $phone
     * @param string $text
     * @return ExecResult
     */
    public function send(string $phone, string $text): ExecResult
    {
        /**
         * $data->to = string - Номер телефона получателя (либо несколько номеров, через запятую — до 100 штук за один запрос). Если вы указываете несколько номеров и один из них указан неверно, то на остальные номера сообщения также не отправляются, и возвращается код ошибки.
         * $data->msg = string - Текст сообщения в кодировке UTF-8
         * $data->multi = array('номер получателя' => 'текст сообщения') - Если вы хотите в одном запросе отправить разные сообщения на несколько номеров, то воспользуйтесь этим параметром (до 100 сообщений за 1 запрос). В этом случае, параметры to и text использовать не нужно
         * $data->from = string - Имя отправителя (должно быть согласовано с администрацией). Если не заполнено, в качестве отправителя будет указан ваш номер.
         * $data->time = Если вам нужна отложенная отправка, то укажите время отправки. Указывается в формате UNIX TIME (пример: 1280307978). Должно быть не больше 7 дней с момента подачи запроса. Если время меньше текущего времени, сообщение отправляется моментально.
         * $data->translit = 1 - Переводит все русские символы в латинские. (по умолчанию 0)
         * $data->test = 1 - Имитирует отправку сообщения для тестирования ваших программ на правильность обработки ответов сервера. При этом само сообщение не отправляется и баланс не расходуется. (по умолчанию 0)
         * $data->partner_id = int - Если вы участвуете в партнерской программе, укажите этот параметр в запросе и получайте проценты от стоимости отправленных сообщений.
         */
        $data = new \stdClass();
        $data->to = $phone;
        $data->text = $text;

        $url = $this->protocol . '://' . $this->domain . '/sms/send';
        $request = $this->sendRequest($url, $data);
        $response = $this->checkReplyError($request, 'send');

        if ($response->status == 'OK') {
            ExecResult::instance()->setSuccess();
        }

        return ExecResult::instance()->setData((array) $response);
    }

    /**
     * Get status
     *
     * @param $id
     * @return mixed|\stdClass
     */
    public function getStatus($id)
    {
        $url = $this->protocol . '://' . $this->domain . '/sms/status';

        $post = new \stdClass();
        $post->sms_id = $id;

        $request = $this->sendRequest($url, $post);
        return $this->checkReplyError($request, 'getStatus');
    }

    /**
     * Возвращает стоимость сообщения на указанный номер и количество сообщений, необходимых для его отправки.
     * @param $data
     *   $post->to = string - Номер телефона получателя (либо несколько номеров, через запятую — до 100 штук за один запрос) Если вы указываете несколько номеров и один из них указан неверно, то возвращается код ошибки.
     *   $post->text = string - Текст сообщения в кодировке UTF-8. Если текст не введен, то возвращается стоимость 1 сообщения. Если текст введен, то возвращается стоимость, рассчитанная по длине сообщения.
     *   $post->translit = int - Переводит все русские символы в латинские
     * @return mixed|\stdClass
     */
    public function getCost($data)
    {
        $url = $this->protocol . '://' . $this->domain . '/sms/cost';
        $request = $this->sendRequest($url, $data);
        return $this->checkReplyError($request, 'getCost');
    }

    /**
     * Получение состояния баланса
     */
    public function getBalance()
    {
        $url = $this->protocol . '://' . $this->domain . '/my/balance';
        $request = $this->sendRequest($url);
        return $this->checkReplyError($request, 'getBalance');
    }

    /**
     * Получение текущего состояния вашего дневного лимита.
     */
    public function getLimit()
    {
        $url = $this->protocol . '://' . $this->domain . '/my/limit';
        $request = $this->sendRequest($url);
        return $this->checkReplyError($request, 'getLimit');
    }

    /**
     * Получение списка отправителей
     */
    public function getSenders()
    {
        $url = $this->protocol . '://' . $this->domain . '/my/senders';
        $request = $this->sendRequest($url);
        return $this->checkReplyError($request, 'getSenders');
    }

    /**
     * На номера, добавленные в стоплист, не доставляются сообщения (и за них не списываются деньги)
     * @param string $phone Номер телефона.
     * @param string $text Примечание (доступно только вам).
     * @return mixed|\stdClass
     */
    public function addStopList($phone, $text = "")
    {
        $url = $this->protocol . '://' . $this->domain . '/stoplist/add';

        $post = new \stdClass();
        $post->stoplist_phone = $phone;
        $post->stoplist_text = $text;

        $request = $this->sendRequest($url, $post);
        return $this->checkReplyError($request, 'addStopList');
    }

    /**
     * Удаляет один номер из стоплиста
     * @param string $phone Номер телефона.
     * @return mixed|\stdClass
     */
    public function delStopList($phone)
    {
        $url = $this->protocol . '://' . $this->domain . '/stoplist/del';
        
        $post = new \stdClass();
        $post->stoplist_phone = $phone;

        $request = $this->sendRequest($url, $post);
        return $this->checkReplyError($request, 'delStopList');
    }

    /**
     * Получить номера занесённые в стоплист
     */
    public function getStopList()
    {
        $url = $this->protocol . '://' . $this->domain . '/stoplist/get';
        $request = $this->sendRequest($url);
        return $this->checkReplyError($request, 'getStopList');
    }

    /**
     * Добавить URL Callback системы на вашей стороне, на которую будут возвращаться статусы отправленных вами сообщений
     * @param $post
     *    $post->url = string - Адрес обработчика (должен начинаться на http://)
     * @return mixed|\stdClass
     */
    public function addCallback($post)
    {
        $url = $this->protocol . '://' . $this->domain . '/callback/add';
        $request = $this->sendRequest($url, $post);
        return $this->checkReplyError($request, 'addCallback');
    }

    /**
     * Удалить обработчик, внесенный вами ранее
     * @param $post
     *   $post->url = string - Адрес обработчика (должен начинаться на http://)
     * @return mixed|\stdClass
     */
    public function delCallback($post)
    {
        $url = $this->protocol . '://' . $this->domain . '/callback/del';
        $request = $this->sendRequest($url, $post);
        return $this->checkReplyError($request, 'delCallback');
    }

    /**
     * Get all callbacks
     * @return mixed|\stdClass
     */
    public function getCallback()
    {
        $url = $this->protocol . '://' . $this->domain . '/callback/get';
        $request = $this->sendRequest($url);
        return $this->checkReplyError($request, 'getCallback');
    }

    /**
     * Send request
     *
     * @param $url
     * @param bool $post
     * @return bool|string
     */
    private function sendRequest($url, $post = false)
    {
        if ($post) {
            $r_post = $post;
        }

        $ch = curl_init($url . "?json=1");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        if (!$post) {
            $post = new \stdClass();
        }

        if (!empty($post->api_id) && $post->api_id == 'none') {
        } else {
            $post->api_id = $this->apiKey;
        }

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query((array)$post));

        $body = curl_exec($ch);
        $error = $body === false ? curl_error($ch) : false;

        curl_close($ch);

        if ($error && $this->attempts > 0) {
            $this->attempts--;
            return $this->sendRequest($url, $r_post);
        }

        return $body;
    }

    /**
     * @param $res
     * @param string $action
     * @return mixed|\stdClass
     */
    private function checkReplyError($res, $action)
    {
        if (!$res) {
            $temp = new \stdClass();
            $temp->status = "ERROR";
            $temp->status_code = "000";
            $temp->status_text = "Невозможно установить связь с сервером.";
            $temp->action = $action;

            return $temp;
        }

        $result = json_decode($res);

        if (!$result || !$result->status) {
            $temp = new \stdClass();
            $temp->status = "ERROR";
            $temp->status_code = "000";
            $temp->status_text = "Невозможно установить связь с сервером.";
            $temp->action = $action;

            return $temp;
        }

        return $result;
    }
}
