<?php

namespace app\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\models\User;

class UsersController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => [User::ROLE_ADMIN]],
                ],
            ],
        ];
    }

    /**
     * Список пользлвателей ..
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'dp' => new ActiveDataProvider([
                'query' => User::find(),
            ]),
        ]);
    }
}