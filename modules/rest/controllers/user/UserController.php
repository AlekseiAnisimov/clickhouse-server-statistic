<?php
namespace app\modules\rest\controllers\user;

use Yii;
use app\modules\rest\components\BaseController;
use app\modules\user\models\UserChannels;
use app\modules\user\models\User;
use app\modules\user\models\UserPermissions;

class UserController extends BaseController
{
    public function beforeAction($action): bool
    {
        if (Yii::$app->request->getIsOptions()) {
            return true;
        }

        return parent::beforeAction($action);
    }

    public function actionAccess()
    {
        if (Yii::$app->request->getIsOptions()) {
            return true;
        }

        return $this->asJson([
            'access' => [
                'permissions' => $this->getUser()->getPermissionList(),
                'groups' => $this->getUser()->getGroupList()
            ]
        ]);
    }
    
    public function actionLinkChannels()
    {
        if (Yii::$app->request->getIsOptions()) {
            return true;
        }

        $data = Yii::$app->request->post();

        if (empty($data['channels'])) {
            return $this->asJson([
                'message'   => 'Channels list is empty',
                'result'    => false,
            ]);
        }
        
        $rows = [];
        foreach ($data['channels'] as $channelId) {
            $rows[] = [
                'user_id'       => $this->user->id,
                'channel_id'    => $channelId,
            ];
        }
        
        try {
            Yii::$app->db->createCommand()->batchInsert(UserChannels::tableName(), ['user_id', 'channel_id'], $rows)->execute();
        } catch (\Exception $ex) {
            return $this->asJson([
                'message'   => 'Record existence',
                'return'    => false,
            ]);
        }
        
        return $this->asJson([
            'message'   => 'Success',
            'result'    => true,
        ]);
    }
    
    public function actionUnlinkChannels()
    {
        if (Yii::$app->request->getIsOptions()) {
            return true;
        }
        
        $channelId = Yii::$app->request->post('channel_id');
        
        if (is_null($channelId)) {
            return $this->asJson([
                'message'   => 'Channel id is null',
                'result'    => false,
            ]);
        }
        
        $userChannel = UserChannels::find()->where(['user_id' => $this->user->id, 'channel_id' => $channelId])->one();
        
        if (is_null($userChannel)) {
            return $this->asJson([
                'message'   => 'Not found',
                'result'    => false,
            ]);
        }
        
        $userChannel->delete();
        
        return $this->asJson([
            'message'   => 'Succesfull',
            'return'    => true,
        ]);
    }
    
    public function actionGetUsers()
    {
        if (Yii::$app->request->getIsOptions()) {
            return true;
        }
        
        $accounts = User::find()->select([
            'id',
            'username',
            'email',
            'confirmed_at',
            'blocked_at',
            'registration_ip',
            'created_at',
            'updated_at'
            ])->asArray()->all();
        
        return $this->asJson($accounts);
    }
    
    public function actionConfirm($id) {
        //$user = $this->finder->findUserById($id);
        $user = User::findOne($id);

        if ($user === null /*|| $userModule->enableConfirmation == false*/) {
            throw new NotFoundHttpException();
        }

        $user->confirmed_at = time();
        $user->save();
               
        $permissionId = 5;
        
        $permissionLink = Yii::createObject([
            'class' => UserPermissions::class,
            'user_id' => $id,
            'permission_id' => $permissionId
        ]);

        if (!$permissionLink->create()) {
            throw new BadRequestHttpException();
        }

        return $this->asJson([
            'id' => $id,
            'confirmed_at' => $user->confirmed_at,
            'message' => "User activated succesfull",
        ]);
    }

    public function actionUnconfirm($id) {
        //$user = $this->finder->findUserById($id);
        $user = User::findOne($id);
        
        if ($user === null /*|| $this->module->enableConfirmation == false*/) {
            throw new NotFoundHttpException();
        }

        $user->confirmed_at = null;
        $user->save();
        
        $userPermissions = new UserPermissions();
        
        if ($userPermissions->terminate($id) == false) {
            throw new NotFoundHttpException();
        }
        
        return $this->asJson([
                    'id' => $id,
                    'confirmed_at' => $user->confirmed_at,
                    'message' => "User disconnect succesfull",
        ]);
    }

}