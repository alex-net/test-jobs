<?php

namespace app\components;

use app\models\User;
use app\models\Operation;
use app\models\Account;
use app\components\SignaturaDeectBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use Yii;
use yii\base\Component;

class ExternalPaymentSystem extends Component
{
    const MAX_ACCOUNTS = 10;

    public function behaviors()
    {
        return [
            SignaturaDeectBehavior::class,
        ];
    }


    public function generate()
    {
        $user = User::find()->where(['active' => 1])->with('accounts')->orderBy(new Expression('random()'))->one();
        $accid = null;
        // счета пользователя ...
        $accIds = ArrayHelper::getColumn($user->accounts, 'id');

        $maxAccounts = ENV_DATA['MAX_ACCOUNTS'] ?? 0;
        // остались не занятые счета
        if ($maxAccounts && Account::find()->count() < $maxAccounts || !$maxAccounts) {
            do {
                // расширяем счета полдзователя новым счётом ...
                $accIdsExt = array_merge([$maxAccounts ? rand(1, $maxAccounts) : rand()], $accIds);
                // выбираем случайный счёт из списка
                $accid = $accIdsExt[array_rand($accIdsExt)];
                // проверяем есть ли счёт в базе ...
                $acc = Account::findOne(['id' => $accid]);
                // повторяем до тех пор, пока натнёмся на счёт выбранного пользователя либо на несуществующий счёт
            } while ($acc && $acc->uid != $user->id);
        } else {
            if ($accIds) {
                $accid = $accIds[array_rand($accIds)];
            } else {
                Yii::error('Свободные счета закончились', 'no free account');
                return [];
            }
        }

        $data = [
            'account_id' => $accid,
            'amount' => rand(100, 100000) / 100,
            'transaction_id' => $this->transactionId,
            'user_id' => $user->id,
        ];
        $data['signature'] = $this->detectSignatura($data);

        return $data;
    }
}