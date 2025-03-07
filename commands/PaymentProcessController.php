<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\widgets\Table;
use yii\helpers\Console;

/**
 * эмуляция процесса работы с платёжной системой
 */
class PaymentProcessController extends Controller
{
    /**
     * Действие является посредником между платёжной системой и веб-хуком сайта
     * @param  integer $n Число обрабатываемых запросов за раз
     * @return int Код ответа
     */
    public function actionIndex($n = 1)
    {
        // отсюда будем получать данные (используем адрес внешней платёжки
        $paymentExtrUrl = ENV_DATA['EXTERNAL_SYSTEM_URL'] ?? 'http://nginx/external-payment-system';
        // сюда будем слать полученные данные post запросом . (как будто отправляем запрос от платёжки .. в веб-хук
        $paymentHookUrl = ENV_DATA['PAYMENT_SYSTEM_URL'] ?? 'http://nginx/payment-data-processing';

        $curlExter = curl_init($paymentExtrUrl);
        $curlPayHook = curl_init($paymentHookUrl);
        if (!$curlExter) {
            return ExitCode::IOERR;
        }
        Console::startProgress(0, $n);
        $dataHub = [];
        curl_setopt($curlExter, CURLOPT_HEADER, false);
        curl_setopt($curlExter, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlPayHook, CURLOPT_HEADER, false);
        curl_setopt($curlPayHook, CURLOPT_RETURNTRANSFER, true);
        // забрасываем данные через post
        curl_setopt($curlPayHook, CURLOPT_POST, true);
        $i = 0;
        while ($i < $n) {
            $data = curl_exec($curlExter);
            $data = json_decode($data, 1);
            curl_setopt($curlPayHook, CURLOPT_POSTFIELDS, $data);
            $resp = curl_exec($curlPayHook);
            $data['ansver'] = curl_getinfo($curlPayHook, CURLINFO_HTTP_CODE) . ' ==> ' . $resp;
            $dataHub[] = $data;
            $i++;
            Console::updateProgress($i, $n);
        }
        curl_close($curlExter);
        curl_close($curlPayHook);
        Console::endProgress();

        echo Table::widget([
            'headers' => array_keys($dataHub[0]),
            'rows' => $dataHub,
        ]);
        return ExitCode::OK;
    }
}