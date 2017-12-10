<?php

namespace common\models;

use common\components\traits\LockableTable;
use Yii;
use common\components\validators\DurationValidator;

/**
 * This is the model class for table "stats".
 *
 * @property integer $jobId          Job
 * @property string $date            Statistical day
 * @property string $avgDuration     Average duration
 * @property string $minOperationId  Fastest operation id
 * @property string $maxOperationId  Slowest operation id
 * @property integer $operationsCount
 *
 * relations
 * @property-read Operation $maxOperation
 * @property-read Operation $minOperation
 */
class Stat extends \yii\db\ActiveRecord
{
    use LockableTable;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stats';
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['jobId', 'date'];
    }

    /**
     * @inheritdoc
     * @return \common\components\query\StatQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\components\query\StatQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['default'] = ['date', 'avgDuration', 'minOperationId', 'maxOperationId', 'operationsCount'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // date
            [['date', 'avgDuration'], 'required'],
            // number
            [['avgDuration'], DurationValidator::className()],
            // integer
            [['jobId', 'minOperationId', 'maxOperationId', 'operationsCount'], 'integer'],
            // exists
            [['maxOperationId'], 'exist', 'skipOnError' => true, 'targetClass' => Operation::className(), 'targetAttribute' => ['maxOperationId' => 'id']],
            [['minOperationId'], 'exist', 'skipOnError' => true, 'targetClass' => Operation::className(), 'targetAttribute' => ['minOperationId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jobId' => Yii::t('app', 'JOB'),
            'date' => Yii::t('app', 'DATE'),
            'avgDuration' => Yii::t('app', 'STAT_AVERAGE_DURATION'),
            'minOperationId' => Yii::t('app', 'STAT_MIN_OPERATION'),
            'maxOperationId' => Yii::t('app', 'STAT_MAX_OPERATION'),
            'operationsCount' => Yii::t('app', 'STAT_OPERATIONS_COUNT'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaxOperation()
    {
        return $this->hasOne(Operation::className(), ['id' => 'maxOperationId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMinOperation()
    {
        return $this->hasOne(Operation::className(), ['id' => 'minOperationId']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // something...

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        // something...

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        // Do not forget delete the related data!

        parent::afterDelete();
    }
}
