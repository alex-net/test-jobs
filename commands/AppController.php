<?php

namespace app\commands;

use yii\console\Controller;
use yii\helpers\Console;
use app\models\User;
use Yii;

/**
 * Подготовка приложения
 */
class AppController extends Controller
{
    /**
     * создание ролей
     * @return [type] [description]
     */
    private function createRoles()
    {
        $am = Yii::$app->authManager;
        $need = false;
        $rbacDir = dirname(Yii::getAlias($am->itemFile)) ;
        if (!file_exists($rbacDir)) {
            mkdir($rbacDir);
            $need = true;
        }
        if ($need || !$am->getRole(User::ROLE_ADMIN)) {
            foreach (array_keys(User::ROLES) as $k) {
                $am->add($am->createRole($k));
            }
        }
    }

    /**
     * Инициирование RBAC
     */
    public function actionRbacInit()
    {
        $this->createRoles();

    }

    /**
     * Создание админа
     */
    public function actionCreateAdmin()
    {
        try {
            $this->createRoles();
            $admin = User::findOne(['login' => 'admin']);
            if (!$admin) {
                $admin = new User([
                    'login' => 'admin',
                    'passHash' => Yii::$app->security->generatePasswordHash('admin'),
                    'role' => User::ROLE_ADMIN,
                ]);
                $admin->save();
            }
            $am = Yii::$app->authManager;
            if (!$am->getAssignment(User::ROLE_ADMIN, $admin->id)) {
                $am->assign($am->getRole(User::ROLE_ADMIN), $admin->id);
            }

        } catch (\yii\base\Exception $e) {
            $this->stdout("Что-то пошло нетак...\n", Console::FG_RED);
        }
    }

}