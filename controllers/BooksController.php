<?php

namespace app\controllers;

use yii\web\Controller;

/**
 * контроллер на CRUD для книг
 */
class BooksController extends Controller
{
    use BookAuthorTrait;
}