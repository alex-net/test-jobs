<?php

namespace app\controllers;

/**
 * контроллер на CRUD для книг
 */
class BooksController extends BookAuthorControllerBase
{
    /**
     * поиск модели по ID
     * @param  int $id ID модели в базе
     * @return [type]     [description]
     */
    protected function findModel($id = null)
    {
        $model = parent::findModel($id);
        $model->authorList = array_map(fn($el) => $el->id, $model->authors);
        return $model;
    }
}