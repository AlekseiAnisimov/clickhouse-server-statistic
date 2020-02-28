<?php

namespace app\commands;

use app\modules\statistic\models\MonitLoadChannels;
use app\modules\statistic\models\MonitAgregate;
use yii\console\Controller;

class CronController extends Controller
{
    public function actionChannelLoads()
    {
        MonitLoadChannels::saveLoadChannels();
    }
    
    public function actionAgregate($dayBegin)
    {
        $result = MonitAgregate::agregateViewsCount($dayBegin);
        
        print $result;
    }
}