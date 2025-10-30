<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\base\Event;
use app\components\SimplePageAction;
use app\models\StoryValut;

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
        ];
    }

    /**
     * Страница просмотра списка добавленных  записей . .+ форма создания новой записи
     * @return [type] [description]
     */
    public function actionIndex()
    {
        $model = new StoryValut(['ip' => $this->request->userIp]);

        Event::on(StoryValut::class, StoryValut::OVERAGE_LIMIT_CREATEATED_EVENT, function($evnt) {
            Yii::$app->session->addFlash('danger', sprintf('Превышен лимит создания новых сообщений, Новое сообщение можно будет добавить через %d с', 180 - time() + $evnt->sender->created));
        });
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            $this->sendMail($model);
            return $this->refresh();
        }
        return $this->render('index', ['model' => $model]);
    }

    /**
     * Отправка почты со ссылками на редактирование и удаление добавленно записи
     * @param  StoryValut $post Объект записи
     * @return null
     */
    protected function sendMail($post)
    {
        $mail = Yii::$app->mailer->compose();
        $mail->setFrom(Yii::$app->params['adminEmail']);
        $mail->setTo($post->mail);
        $mail->setSubject('Управление записью');
        $mail->setHtmlBody($this->renderPartial('post-mail-links', ['links' => $post->links]));
        $mail->send();
    }

    /**
     * действие по обслуживанию записй (адаление и редактирование
     * @param  string $key Хитрый ключ в котором закодирован номер записи в базе и желаемое действие
     * @return yii\web\Response
     */
    public function actionPost($key = null)
    {
        $post = StoryValut::getPostByKey($key);

        if (!$post) {
            throw new NotFoundHttpException('ничего нету)');
        }

        $method = 'postToDo' . ucfirst($post['action']);
        return $this->$method($post['model']);
    }

    /**
     * Действие по редактированию текста записи
     * @param  app\models\StoryValut $model Модель для редактирования даных
     * @return yii\web\Response
     */
    protected function postToDoEdit($model)
    {
        $model->scenario = StoryValut::SCENARIO_ONLY_EDIT;
        $this->view->title = 'Редактирование поста';
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', 'Данные обновленвы');
            return $this->refresh();
        }
        return $this->render('post-edit-form', ['editOnly' => true, 'model' => $model]);
    }

    protected function postToDoKill($model)
    {
        if ($this->request->isPost) {
            if ($model->delete()) {
                Yii::$app->session->addFlash('info', 'Запись удалена');
                return $this->redirect(['index']);
            }
        }
        return $this->render('post-kill-form', ['model' => $model]);
    }

}
