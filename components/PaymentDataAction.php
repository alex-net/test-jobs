<?php

namespace app\components;

use yii\base\Action;
use Yii;
use app\models\Account;
use app\models\Operation;
use app\models\User;
use app\components\SignaturaDeectBehavior;

/**
 * оюработка входящих транзакций от платёжки
 */
class PaymentDataAction extends Action
{
    public function behaviors() {
        return [
            SignaturaDeectBehavior::class,
        ];
    }

    public function init()
    {
        parent::init();
        // Нужно выключить Csrf валидацию т.к. запрос является внешним
        $this->controller->enableCsrfValidation = false;
    }

    public function run()
    {
        if (!Yii::$app->request->isPost || !$this->checkData(Yii::$app->request->post())) {
            Yii::error(Yii::$app->request->post(), 'no valid post');
            return 'noOk';
        }

        $post = Yii::$app->request->post();
        // поиск юзера ..
        $user = User::findOne($post['user_id']);
        if (!$user) {
            Yii::error('no user ', 'user');
            return 'noOk';
        }

        $trans = Yii::$app->db->beginTransaction();
        try {
            // поиск счёта ...
            $account = Account::findOne([
                'id' => $post['account_id'] ?: null,
                'uid' => $user->id,
            ]);
            // не нашли счёт ... надо создать
            if (!$account) {
                $account = new Account([
                    'id' => $post['account_id'],
                    'uid' => $user->id
                ]);
                if (!$account->save()) {
                    Yii::error($account->errors, 'account errors');
                    throw new \Exception("No Account save");
                }
            }
            // новая  операция по счёту ...
            $op = new Operation([
                'aid' => $account->id,
                'amount' => $post['amount'] ?? null,
                'transId' => $post['transaction_id'] ?? null,
            ]);
            if (!$op->save()) {
                Yii::error($op->errors, 'op errors');
                throw new \Exception("No Operation save");
            }

        } catch (\Exception $e) {
            $trans->rollBack();
            Yii::error($e, 'expr');
            return 'noOk';
        }
        $trans->commit();

        return 'Ok';
    }

    /**
     * проверка входных ланных с использованием секретной сигнатуры
     * @param  array $post Входные данные от платёжной системы
     * @return bool      результаты валидации данных по сигнатуре
     */
    protected function checkData($post)
    {
        return !empty($post['signature']) &&  $this->detectSignatura($post) == $post['signature'];
    }

}