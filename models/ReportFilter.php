<?php

namespace app\models;

use yii\base\Model;

class ReportFilter extends Model
{
    public $year;

    public function rules()
    {
        return [
            ['year', 'integer', 'min' => 1],
        ];
    }

    public function attributeLabels()
    {
        return [
            'year' => 'Год издания',
        ];
    }

    public function formName()
    {
        return '';
    }

    public function getResults()
    {
        return Author::listForReport($this->year);
    }
}