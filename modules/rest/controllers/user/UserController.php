<?php
namespace app\modules\rest\controllers\user;

use Yii;
use app\modules\rest\components\BaseController;

class UserController extends BaseController
{
    public function actionPermissions()
    {
        if (Yii::$app->request->getIsOptions()) {
            return true;
        }

        return $this->asJson([
            'permissions' => [
                $this->getUser()->username
            ]
        ]);
    }
}