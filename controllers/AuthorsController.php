<?php

namespace app\controllers;

use app\models\Author;
/**
 * контроллер на CRUD для автоов
 */
class AuthorsController extends BookAuthorControllerBase
{
    public function actionAjaxList($q = null)
    {
        $list = Author::find()->select(['id', 'text' => 'fio'])->filterWhere(['like', 'fio', $q])->asArray()->limit(10)->all();
        return $this->asJson(['results' => $list]);
    }
}