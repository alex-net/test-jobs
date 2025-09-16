<?php

namespace app\modules\track\controllers;

use yii\rest\Controller;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use Yii;
use app\modules\track\models\Track;

class ApiController extends Controller
{
    use TrackControllerTrait;

    public function init()
    {
        Yii::$app->user->enableSession = false;
        parent::init();
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => HttpBearerAuth::class,
                'only' => ['update','add', 'delete', 'update-statuses'],
            ],
        ]);
    }

    protected function verbs()
    {
        return [
            'index' => ['get'],
            'view' => ['get'],
            'add' => ['post'],
            'update' => ['put'],
            'delete' => ['delete'],
            'update-statuses' => ['put'],
        ];
    }

    /**
     * Получить список моделей Track с применением фильтра
     * @param  string $status Набор фильтруемых стутусов через запятую.
     * @return array          Результат выборки
     */
    public function actionIndex($status = null)
    {
        $query = Track::search(['status' => $status]);
        $dp = new ActiveDataProvider(['query' => $query]);
        return $dp->models;
    }

    /**
     * просмотр данных объекта Track
     * @param  int $id Номер просматриваемого объекта
     * @return array     Данные объекта Track с номером $id
     */
    public function actionView($id)
    {
        $model = $this->finModel($id);
        return $model;
    }

    /**
     * Обновление/создание записи с типом Track
     * @param int $id Номер записи для её обновления
     * @return array  результаты операции
     */
    protected function addOrUpdate($id = null)
    {
        $resp = $this->updateAction($id);
        return $resp === true ? ['ok' => true] : ['errors' => $resp->errors];
    }

    /**
     * Создание новой записи Track
     * @return array Результаты операции
     */
    public function actionAdd()
    {
        return $this->addOrUpdate();
    }

    /**
     * Обновление записи с типом Track
     * @param int $id Номер записи для её обновления
     * @return array  результаты операции
     */
    public function actionUpdate($id)
    {
        return $this->addOrUpdate($id);
    }


    /**
     * Удаление записи Track по номеру
     * @param  int $id Номер удаляемой записи
     * @return bool     Результат операции удаления
     */
    public function actionDelete($id)
    {
        return ['ok' => $this->deleteAction($id)];
    }

    /**
     * Массовое присвоение статусов
     * @return array Результат выполнения операции
     */
    public function actionUpdateStatuses()
    {
        $md = $this->request->post('Tracks', []);
        $res = [];
        if ($md) {
            $models = Track::findAll(['id' => array_keys($md)]);
            for ($i = 0; $i < count($models); $i++) {
                $models[$i]->status = $md[$models[$i]->id];
                $res[$models[$i]->id] = $models[$i]->validate('status') && $models[$i]->save() ? 'ok' : $models[$i]->getFirstError('status');
            }
        }
        return $res;
    }
}