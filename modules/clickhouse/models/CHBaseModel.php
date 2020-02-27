<?php
namespace app\modules\clickhouse\models;

use Tinderbox\Clickhouse\Client;
use Tinderbox\Clickhouse\Query\Result;
use Tinderbox\ClickhouseBuilder\Query\BaseBuilder;
use Tinderbox\ClickhouseBuilder\Query\Builder;
use Yii;

class CHBaseModel
{
    public final static function getBuilder() : Builder
    {
        return Yii::$app->clickhouse->getBuilder();
    }

    public final static function getClient() : Client
    {
        return Yii::$app->clickhouse->getClient();
    }

    public static function find() : Builder
    {
        return static::getBuilder();
    }

    public static function execute(BaseBuilder $query) : Result
    {
        return static::getClient()->readOne($query->toSql());
    }

    public static function toArray($query)
    {
        if ($query instanceof Result) {
            return $query->getRows();
        }

        return self::execute($query)->getRows();
    }

    public static function appList() : array
    {
        return [
            'mejortvios',
            'tv.mejor.mejortv',
            'tv.limehd.mejor'
        ];
    }

    public final static function all() {}
}