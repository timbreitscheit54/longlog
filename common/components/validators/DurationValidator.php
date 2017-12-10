<?php

namespace common\components\validators;

use Yii;
use yii\validators\NumberValidator;

class DurationValidator extends NumberValidator
{
    public $min = 0;
    public $max = 999999.999;
    public $numberPattern = '/^[0-9]{1,5}\.?[0-9]{0,3}$/';

    public function init()
    {
        if ($this->message === null) {
            $this->message = Yii::t('app/error', 'DURATION_NUMBER_VALIDATION_FAIL_{attribute}{min}{max}', [
                'attribute' => '{attribute}',
                'min' => $this->min,
                'max' => $this->max,
            ]);
        }

        parent::init();
    }
}
