<?php

namespace common\components\behaviors;

use yii\db\Expression;

class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Override the default value time() to `NOW()`
        if (empty($this->value)) {
            $this->value = new Expression('NOW()');
        }
    }
}
