<?php

namespace common\models;

use common\components\validators\DurationValidator;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "jobs".
 *
 * @property integer $id
 * @property integer $projectId   Project
 * @property string $key          Unique job identifier
 * @property string $title        Custom title
 * @property string $critDuration Critical job duration seconds
 * @property string $createdAt
 *
 * relations
 * @property-read Project $project
 * @property-read Operation[] $operations
 * @property-read Stat[] $stats
 *
 * getters
 * @property-read string $fTitle  HTML-encoded job name
 */
class Job extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jobs';
    }

    /**
     * @inheritdoc
     * @return \common\components\query\JobQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\components\query\JobQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['default'] = ['key', 'title', 'critDuration'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // required
            [['projectId', 'key'], 'required'],
            // integer
            [['projectId'], 'integer'],
            // duration
            [['critDuration'], DurationValidator::className(), 'min' => 0.001],
            // string max
            [['key', 'title'], 'string', 'max' => 255],
            // unique
            [
                ['projectId', 'key'], 'unique', 'targetAttribute' => ['projectId', 'key'],
                'message' => Yii::t('app/error', 'PROJECT_JOB_UNIQUE_ERROR')
            ],
            // exists
            [['projectId'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['projectId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'projectId' => Yii::t('app', 'PROJECT'),
            'key' => Yii::t('app', 'JOB_KEY'),
            'title' => Yii::t('app', 'JOB_CUSTOM_TITLE'),
            'critDuration' => Yii::t('app', 'JOB_CRITICAL_DURATION'),
            'createdAt' => Yii::t('app', 'CREATED_AT'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'projectId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperations()
    {
        return $this->hasMany(Operation::className(), ['jobId' => 'id']);
    }

    /**
     * Stats relation
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStats()
    {
        return $this->hasMany(Stat::className(), ['jobId' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => '\common\components\behaviors\TimestampBehavior',
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => false,
            ],
        ];
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

    /**
     * Return HTML-encoded title
     *
     * @return string
     */
    public function getFTitle()
    {
        return Html::encode($this->title ? $this->title : $this->key);
    }

    /**
     * Get job id by job key.
     * Create new job if doesn't exists.
     *
     * @param integer $projectId
     * @param string $key
     *
     * @return integer|null
     */
    public static function getIdByKey($projectId, $key)
    {
        $jobId = static::find()->select(['id'])->where(['projectId' => $projectId, 'key' => $key])->scalar();

        if (!$jobId) {
            $newJob = new static();
            $newJob->projectId = $projectId;
            $newJob->key = $key;
            if ($newJob->save()) {
                $jobId = $newJob->id;
            }
        }

        return $jobId;
    }
}
