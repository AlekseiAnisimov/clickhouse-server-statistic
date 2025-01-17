<?php

use yii\db\Query;
use yii\db\Schema;
use yii\db\Migration;

class m150614_103145_update_social_account_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%social_account}}', 'code', Schema::TYPE_STRING . '(32)');
        $this->addColumn('{{%social_account}}', 'created_at', Schema::TYPE_INTEGER);
        $this->addColumn('{{%social_account}}', 'email', Schema::TYPE_STRING);
        $this->addColumn('{{%social_account}}', 'username', Schema::TYPE_STRING);
        $this->createIndex('account_unique_code', '{{%social_account}}', 'code', true);
    }

    public function down()
    {
        $this->dropIndex('account_unique_code', '{{%social_account}}');
        $this->dropColumn('{{%social_account}}', 'email');
        $this->dropColumn('{{%social_account}}', 'username');
        $this->dropColumn('{{%social_account}}', 'code');
        $this->dropColumn('{{%social_account}}', 'created_at');
    }
}
