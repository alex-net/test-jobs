<?php

namespace app\components;

use yii\base\Action;
use app\models\LoginAndRegistrationForm;
use Yii;

class LoginAndRegistrationAction extends Action
{
    public function run()
    {

        if (!Yii::$app->user->isGuest) {
            return $this->controller->goHome();
        }

        $model = new LoginAndRegistrationForm(['scenario' => $this->id]);
        if ($forUser = Yii::$app->request->get('for')) {
            $model->username = $forUser;
        }

        if ($model->todo(Yii::$app->request->post())) {
            if ($model->scenario == $model::SCENARIO_REGISTRATION) {
                return $this->controller->redirect(['login', 'for' => $model->username]);
            }
            return $this->controller->goBack();
        }

        $model->password = '';
        return $this->controller->render('login-register', [
            'model' => $model,
        ]);
        return '@' . $this->id;
    }
}