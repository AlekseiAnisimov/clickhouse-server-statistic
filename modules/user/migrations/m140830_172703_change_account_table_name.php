<?php

/*
 * 
 *
 *
 *
 *
 *
 */

use yii\db\Migration;

/**
 * >
 */
class m140830_172703_change_account_table_name extends Migration
{
    public function up()
    {
        $this->renameTable('{{%account}}', '{{%social_account}}');
    }

    public function down()
    {
        $this->renameTable('{{%social_account}}', '{{%account}}');
    }
}
