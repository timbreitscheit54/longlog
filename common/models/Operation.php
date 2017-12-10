<?php

namespace common\models;

use common\components\traits\LockableTable;
use Yii;

/**
 * This is the model class for table "operations".
 *
 * @property integer $id
 * @property integer $jobId    Job
 * @property string $duration  Duration seconds
 * @property string $payload   Some working data
 * @property string $createdAt Record created time
 *
 * relations
 * @property-read Job $job
 */
class Operation extends \yii\db\ActiveRecord
{
    use LockableTable;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'operations';
    }

    /**
     * @inheritdoc
     * @return \common\components\query\OperationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\components\query\OperationQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['default'] = ['jobId', 'duration', 'payload'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // required
            [['jobId', 'duration'], 'required'],
            // integer
            [['jobId'], 'integer'],
            // float
            [['duration'], 'number'],
            // string max
            [['payload'], 'string', 'max' => 255],
            // exists
            [['jobId'], 'exist', 'skipOnError' => true, 'targetClass' => Job::className(), 'targetAttribute' => ['jobId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'jobId' => 'Job',
            'duration' => Yii::t('app', 'JOB_DURATION'),
            'payload' => Yii::t('app', 'JOB_PAYLOAD'),
            'createdAt' => Yii::t('app', 'JOB_CREATED_AT'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJob()
    {
        return $this->hasOne(Job::className(), ['id' => 'jobId']);
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
