<?php

namespace app\controllers;

use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use Yii;

class BookAuthorControllerBase extends Controller
{
    protected $entityClass;

    public function init()
    {
        parent::init();
        $this->entityClass = 'app\\models\\' . ucfirst(substr($this->id, 0, strlen($this->id) - 1));
    }

    /**
     * Список всех записей
     * @return [type] [description]
     */
    public function actionIndex()
    {
        $dp = new ActiveDataProvider([
            'query' => $this->entityClass::find(),
        ]);

        return $this->render('index', compact('dp'));
    }


    /**
     * поиск модели по ID
     * @param  int $id ID модели в базе
     * @return [type]     [description]
     */
    protected function findModel($id = null)
    {
        $model = $id ? $this->entityClass::findOne($id) : new $this->entityClass();
        if (!$model) {
            throw new NotFoundHttpException('Запись не найдена');
        }
        return $model;
    }

    /**
     * Редактирование записи
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionUpdate($id = null)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', $id ? 'Запись обновлена' : 'Запись добавлена');

            return $id ? $this->refresh() : $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('form', compact('model'));
    }


    /**
     * Удаление записи
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->delete()) {
            Yii::$app->session->addFlash('info', 'Запись удалена');
        }
        return $this->redirect(['index']);
    }
}