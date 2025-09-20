<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\ViewAction;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Yii;
use app\models\Book;
use app\models\Author;
use app\models\User;
use app\models\AuthotBookBinder;

class ContentRenderController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@'], 'actions' => ['book', 'author']],
                    ['allow' => true, 'roles' => [User::ROLE_ADMIN], 'actions' => ['new', 'update', 'delete', 'binder']],
                ],
            ],
        ];
    }


    public function actions()
    {
        return [
            'book' => [ // список книг
                'class' => ViewAction::class,
                'viewPrefix' => '',
                'defaultView' => 'obj-list',
            ],
            'author' => [ // список авторов
                'class' => ViewAction::class,
                'viewPrefix' => '',
                'defaultView' => 'obj-list',
            ],
        ];
    }

    /**
     * поиск объекта модели в базе ..
     * @param  string $class Класс модели в текстовом представлении
     * @param  int $id    Id объекта
     * @return Book|Author      Найденный объект модели
     * @throws NotFoundHttpException Если ничего не нашли
     */
    protected function findModel($class, $id)
    {
        $model = $class::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Объект не найден');
        }
        return $model;
    }

    /**
     * Создание объекта
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public function actionNew($type)
    {
        return $this->actionUpdate($type);
    }

    /**
     * Обновление объекта
     * @param  string $type Тип объекта
     * @param  int $id   Номер редактируемого объекта
     * @return yii\web\Response
     */
    public function actionUpdate($type, $id = null)
    {
        $mClass = 'app\\models\\'  . ucfirst($type);
        $model = $id ? $this->findModel($mClass, $id) : Yii::createObject($mClass);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            if ($id) {
                Yii::$app->session->addFlash('success', 'Данные обновлены');
                return $this->refresh();
            } else {
                return $this->redirect(['update', 'type' => $type, 'id' => $model->id]);
            }
        }

        return $this->render('edit-' . $type, ['model' => $model]);
    }

    /**
     * Удаление сущности
     * @param  string $type Тип удаляемой сущности
     * @param  int $id   Номер удаляемой сущности
     * @return yii\web\Response
     */
    public function actionDelete($type, $id)
    {
        $mClass = 'app\\models\\'  . ucfirst($type);
        $model = $this->findModel($mClass, $id);
        if ($model->delete()) {
            Yii::$app->session->addFlash('info', 'Запись удалена');
        }
        return $this->redirect([$type]);
    }

    /**
     * Обновление связей
     * @param  string $type Тип сущностей из списка которых нужно выбрать связанные объекты
     * @param  int $id   Номер сущности, которой нужно обновить связи
     * @return yii\web\Response
     */
    public function actionBinder($type, $id)
    {
        $bindType = substr($type, 0, strlen($type)-1);
        $lClass = 'app\\models\\' . ucfirst($bindType);
        $backUrl = substr($lClass::COUNT_PARAMS[0], 0, strlen($lClass::COUNT_PARAMS[0])-1);
        $mClass = 'app\\models\\' . ucfirst($backUrl);
        $model = $this->findModel($mClass, $id);

        if ($this->request->isPost && $binders = $this->request->post('binders')) {
            // то что было выбрано в исходной модели
            $existBinderIds = ArrayHelper::getColumn($model->binder, $bindType.'_id');
            // выбранные записи в форме
            $binders = array_filter($binders);

            // нужно удалить
            $toKill = array_diff($existBinderIds, $binders);
            if ($toKill) {
                AuthotBookBinder::deleteAll([
                    $bindType.'_id' => $toKill,
                    $backUrl.'_id' => $id,
                ]);
            }
            // нужно добавить
            $toAdd = array_diff($binders, $existBinderIds);
            foreach ($toAdd as $bid) {
                $ob = new AuthotBookBinder([
                    $bindType.'_id' => $bid,
                    $backUrl.'_id' => $id,
                ]);
                $ob->save();
            }

            Yii::$app->session->addFlash('info', 'Данные связей обновлены');
            return $this->refresh();
        }

        $ddList = ArrayHelper::map($lClass::find()->where(['uid' => $model->uid])->all(), 'id', 'name');
        $ddSelected = ArrayHelper::getColumn($model->$type, 'id');
        return $this->render('binder', compact('model', 'backUrl', 'ddList', 'ddSelected', 'lClass'));
    }
}