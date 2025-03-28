<?php

namespace app\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use app\models\UrlLink;
use app\models\UrlRedirect;
use Yii;

class UrlApplyerController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'actions' => ['redirect']],
                    ['allow' => true, 'roles' => ['@']],
                    // ['allow'].
                ],
            ],
        ];
    }

    /**
     * страница перенаправления с короткой ссылки на оригинальную
     * @param  string $hash Хеш оригинальной ссылки
     * @return [type]       [description]
     */
    public function actionRedirect($hash)
    {
        $url = UrlLink::findByHash($hash);
        if (!$url) {
            Yii::$app->session->addFlash('error', 'Ссылка не найдена');
            return $this->redirect(['site/front']);
        }

        $history = new UrlRedirect([
            'uid' => $url->id,
            'ip' => ip2long($this->request->remoteIP),
        ]);
        $history->save();

        return $this->redirect($url->url);
        // http://localhost:1945/go/242e933a6049b03488adbe18d937a44f2211dd37
    }

    /**
     * Список сохранённых ссылок
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'dp' => new ActiveDataProvider([
                'query' => UrlLink::find()->with('redirects'),
            ]),
        ]);
    }

    /**
     * просмотр посещений ссылки
     * @param  int $id Номер сохранённой ссылки
     */
    public function actionDetail($id)
    {
        $model = UrlLink::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Ссылка на нейдена');
        }
        return $this->render('detail', [
            'model' => $model,
            'dp' => new ActiveDataProvider([
                'query' => $model->getRedirects(),
                'sort' => [
                    'defaultOrder' => [
                        'time' => SORT_DESC,
                    ],
                ],
            ]),
        ]);
    }
}