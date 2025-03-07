<?php

namespace app\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use Yii;
use yii\web\NotFoundHttpException;
use app\models\LoginForm;
use app\models\User;
use app\models\Account;


class UsersController extends Controller
{
    /**
     * Счёт для просмотра в view-account
     * @var Account
     */
    private $userAccount ;


    public function behaviors()
    {
        return [
            [
                'class' => AccessControl::class,
                'only' => ['view-account'],
                'rules' => [[
                    'allow' => true, 'roles' => ['@'], 'matchCallback' => [$this, 'testAccessToCashStack'],
                ]],
            ],
            [
                'class' => AccessControl::class,
                'except' => ['view-account'],
                'rules' => [
                    ['allow' => true, 'roles' => [User::ADMIN_ROLE]],
                    ['allow' => true, 'roles' => ['?'], 'actions' => ['login']],
                    ['allow' => true, 'roles' => ['@'], 'actions' => ['logout', 'profile']],
                    // ['allow' => true, 'roles'=> ['@', User::ADMIN_ROLE], 'actions' => ['view-account'], 'matchCallback' => function($rule, $action) {
                    //     Yii::info($action->controller->actionParams, 'act');
                    //     return false;
                    // }],


                ],
            ]
        ];
    }

    /**
     * разрешаем просматривать только свои счета
     * @return bool       true - открываем доступ на просмотр
     */
    public function testAccessToCashStack()
    {
        $this->userAccount = Account::findOne($this->request->get('aid'));
        if (!$this->userAccount) {
            return false;
        }
        return Yii::$app->user->id == $this->userAccount->uid;
    }

    private function findModel($id)
    {
        $model = User::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Пользователь не найден");
        }
        return $model;
    }

    /**
     * Список пользователей
     * @return [type] [description]
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'dp' => new ActiveDataProvider([
                'query' => User::find()->with('accounts'),
            ]),
        ]);
    }

    /**
     * Редактирование/добавление пользователя
     * @param  int $id Номер пользователя
     * @return [type]     [description]
     */
    public function actionUpdate($id = null)
    {
        $model = $id ? $this->findModel($id) : new User();
        $model->password = null;
        if ($this->request->isPost && $model->load($this->request->post()) &&  $model->save()) {
            Yii::$app->session->addFlash('success', 'Данные сохранены');
            return $id ? $this->refresh() : $this->redirect(['', 'id' => $model->id]);
        }
        return $this->render('edit', compact('model'));
    }

    /**
     * просмотр карточки пользователя ...
     * @param  int $id Номер пользователя в базе
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', compact('model'));
    }

    /**
     * Страница профиля пользователя ...
     */
    public function actionProfile()
    {
        return $this->actionView(Yii::$app->user->id);
    }

    /**
     *  Удаление пользователя.
     * @param  int $id Номер пользователя в базе
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->delete()) {
            Yii::$app->session->addFlash('info', sprintf('Пользователь "%s" удалён', $model->fullName));
            return $this->redirect(['index']);
        }
    }

    /**
     * просмотр списка пополнений для счёта пользователя
     * может просматривать только владелец счёта
     * @param int $aid Номер счёта в базе
     */
    public function actionViewAccount($aid)
    {
        return $this->render('view-account', [
            'acc' => $this->userAccount,
            'operations' => new ActiveDataProvider([
                'query' => $this->userAccount->getOperations(),
            ]),
        ]);
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

}