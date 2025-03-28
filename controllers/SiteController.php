<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use app\models\LoginForm;
use app\components\SimplePageAction;
use app\models\UrlLink;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'tz' => SimplePageAction::class,
            'index' => SimplePageAction::class,
            'about' => SimplePageAction::class,
        ];
    }


    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * действие отображающее форму добавления ссылки на главной странице
     * @return [type] [description]
     */
    public function actionFront()
    {
        $model = new UrlLink();
        // пришел запрос по post
        if ($this->request->isAjax && $model->load($this->request->post())) {
            $model->save();
            if ($model->isNewRecord) {
                return $this->renderPartial('url-form',  compact('model'));
            } else {
                return $this->renderPartial('url-result',  compact('model'));
            }
        }

        return $this->render('url-form',  compact('model'));
    }
}
