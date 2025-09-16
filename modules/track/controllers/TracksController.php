<?php

namespace app\modules\track\controllers;

use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;
use app\modules\track\models\Track;

/**
 * контроллер управления треками из web интерфейса
 */
class TracksController extends Controller
{
    use TrackControllerTrait;

    public function behaviors()
    {
        return [
            'verb' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['get'],
                    'add' => ['get', 'post'],
                    'update' => ['get', 'post'],
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
        ];
    }


    /**
     * Действие - просмотр списка треков
     * @return yii\web\Response
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'dp' => new ActiveDataProvider(['query' => Track::find()->alias('t')->joinWith('log l')]),
        ]);
    }

    /**
     * добавление нового трека
     * @return yii\web\Response
     */
    public function actionAdd()
    {
        return $this->actionUpdate();
    }

    /**
     * Редактирование существующего трека
     * @param  int $id Номер трека для редактирования. Если пусто - создаём новый трек
     * @return yii\web\Response
     */
    public function actionUpdate($id = null)
    {
        $resp = $this->updateAction($id);
        if ($resp === true) {
            Yii::$app->session->addFlash('success', sprintf('%s трек', $id ? 'Обновлён' : 'Добавлен новый'));
            return $this->redirect(['index']);
        }

        return $this->render('edit', ['model' => $resp]);
    }

    /**
     * удаление выбранного трека
     * @param  int $id Номер трека для редактирования. Если пусто - создаём новый трек
     * @return yii\web\Response
     */
    public function actionDelete($id)
    {
        if ($this->deleteAction($id)) {
            Yii::$app->session->addFlash('info', sprintf('Трек "%s" удалён', $model->track_number));
        }
        return $this->redirect(['index']);
    }

    /**
     * Просмотр детальной информации по выбранному треку
     * @param int $id Ключик просматриваемого трека
     * @return yii\web\Response
     */
    public function actionView($id)
    {
        $model = $this->finModel($id);
        return $this->render('view', ['model' => $model]);
    }


    public function actionChanges($id)
    {
        $model = $this->finModel($id);
        $dp = new ActiveDataProvider(['query' => $model->getLog()]);
        return $this->render('changes', [
            'dp' => $dp,
            'model' => $model,
        ]);
    }
}