<?php

namespace app\controllers;

use yii\web\Controller;

/**
 * контроллер на CRUD для автоов
 */
class AuthorsController extends Controller
{
    use BookAuthorTrait;
}