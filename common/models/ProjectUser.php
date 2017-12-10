<?php

namespace common\models;

use common\helpers\enum\ProjectUserRole;
use Yii;

/**
 * This is the model class for table "projectUsers".
 *
 * @property integer $projectId Project
 * @property integer $userId    User
 * @property string $role       User project role
 * @property string $createdAt
 * @property string $updatedAt
 *
 * relations
 * @property-read User $user
 * @property-read Project $project
 */
class ProjectUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'projectUsers';
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['projectId', 'userId'];
    }

    /**
     * @inheritdoc
     * @return \common\components\query\ProjectUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\components\query\ProjectUserQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['default'] = ['projectId', 'userId', 'role'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // required
            [['projectId', 'userId', 'role'], 'required'],
            // integer
            [['projectId', 'userId'], 'integer'],
            // string
            [['role'], 'string'],
            // range
            [['role'], 'in', 'range' => ProjectUserRole::getKeys()],
            // exists
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userId' => 'id']],
            [['projectId'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['projectId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'projectId' => Yii::t('app', 'PROJECT'),
            'userId' => Yii::t('app', 'USER'),
            'role' => Yii::t('app', 'PROJECT_USER_ROLE'),
            'createdAt' => Yii::t('app', 'CREATED_AT'),
            'updatedAt' => Yii::t('app', 'UPDATED_AT'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'projectId']);
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
                'updatedAtAttribute' => 'updatedAt',
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
     * Check that user has admin access to project OR role = $role
     *
     * @param integer $projectId
     * @param integer $userId
     * @param string $role
     *
     * @return boolean
     */
    public static function hasAccess($projectId, $userId, $role)
    {
        $userRole = static::find()->select(['role'])->where(['projectId' => $projectId, 'userId' => $userId])->scalar();
        // Access not found
        if ($userRole === false) {
            return false;
        }

        // Has access if role admin or role = $role
        return $userRole == ProjectUserRole::ADMIN || $userRole == $role;
    }

    /**
     * Create/Update user permission for project
     *
     * @param integer $projectId
     * @param integer $userId
     * @param string $role
     *
     * @return bool
     */
    public static function assign($projectId, $userId, $role)
    {
        $model = static::find()->where(['projectId' => $projectId, 'userId' => $userId])->one();
        if (!$model) {
            $model = new static;
            $model->projectId = $projectId;
            $model->userId = $userId;
        }

        $model->role = $role;

        return $model->save();
    }

    /**
     * Revoke user permission for project
     *
     * @param integer $projectId
     * @param integer $userId
     *
     * @return bool
     */
    public static function revoke($projectId, $userId)
    {
        $model = static::find()->where(['projectId' => $projectId, 'userId' => $userId])->one();

        if ($model && !$model->delete()) {
            return false;
        }

        return true;
    }
}
