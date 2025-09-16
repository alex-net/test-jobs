<?php

namespace app\modules\track\controllers;

use yii\web\NotFoundHttpException;
use Yii;
use app\modules\track\models\Track;

trait TrackControllerTrait
{
    /**
     * получение объекта трека
     * @param  int $id Ключик траке
     * @return Track     Объект трека
     */
    protected function finModel($id = null)
    {
        $model = $id ? Track::findOne(['id' => $id]) : new Track();
        if (!$model) {
            throw new NotFoundHttpException("Трек не найден");
        }
        return $model;
    }

    /**
     * Обновление/создание Трекера
     * @param  int $id Номер трекера для обновления
     * @return true|Tracker
     */
    protected function updateAction($id = null)
    {
        $model = $this->finModel($id);

        if ((Yii::$app->request->isPost || Yii::$app->request->isPut) && $model->load($this->request->post()) && $model->save()) {
            return true;
        }
        return $model;
    }

    /**
     * Удаление объекта Tracker
     * @param  int $id Номер удаляемого объекта Tracker
     * @return bool     Итоги удаления. True - всё удалилось
     */
    protected function deleteAction($id)
    {
        $model = $this->finModel($id);
        return $model->delete();
    }
}