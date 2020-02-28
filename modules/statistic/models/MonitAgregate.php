<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\statistic\models;

use app\modules\clickhouse\models\CHBaseModel;
use Tinderbox\ClickhouseBuilder\Query\Builder;
use Tinderbox\ClickhouseBuilder\Query\From;

/**
 * Description of MonitAgregate
 *
 * @author user
 */
class MonitAgregate extends CHBaseModel
{
    public static function agregateViewsCount($dayBegin)
    {

        $appList = self::appList();
       
        $query = self::find()
                ->select([
                    'vcid',
                    'app',
                    raw('groupArray([toString(evtp), toString(ctnarch), toString(ctnonline)]) as groupData')
                ])
                ->from(function (From $from) use ($dayBegin, $appList) {
                    $from = $from->query();

                    $from
                    ->select([
                        'vcid',
                        'evtp',
                        'app',
                        raw('countIf(evtp = 0) as ctnarch'),
                        raw('countIf(evtp = 1) as ctnonline')
                    ])
                    ->from('stat')
                    ->whereIn('app', $appList)
                    ->where('day_begin', '=', $dayBegin)
                    ->where('adsst', '=', 'NULL')
                    ->where('action', '!=', 'opening-channel')
                    ->where('evtp', '!=', 666666)
                    ->groupBy(['vcid', 'evtp']);
                 })
                ->groupBy(['vcid']);

        return self::execute($query)->getQuery();
    }
}
