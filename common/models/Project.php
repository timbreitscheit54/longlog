<?php

namespace common\models;

use common\helpers\enum\ProjectUserRole;
use common\helpers\UserHelper;
use Yii;
use yii\base\Exception;
use yii\helpers\Html;

/**
 * This is the model class for table "projects".
 *
 * @property integer $id
 * @property string $name       Project name
 * @property string $token      Secret token
 * @property integer $ownerId   Project owner user id
 * @property string $deletedAt  Deleted date
 * @property string $createdAt
 * @property string $updatedAt
 *
 * relations
 * @property-read Job[] $jobs
 * @property-read ProjectUser[] $projectUsers
 * @property-read ProjectUser $currentProjectUser
 * @property-read User[] $users
 * @property-read User $ownerUser
 * @property-read ProjectInvite[] $invites
 *
 * getters
 * @property-read string $fName
 */
class Project extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'projects';
    }

    /**
     * @inheritdoc
     * @return \common\components\query\ProjectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\components\query\ProjectQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['default'] = ['name'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // required
            [['name'], 'required'],
            // string max
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'PROJECT_NAME'),
            'token' => Yii::t('app', 'PROJECT_TOKEN'),
            'ownerId' => Yii::t('app', 'PROJECT_OWNER'),
            'deletedAt' => Yii::t('app', 'DELETED_AT'),
            'createdAt' => Yii::t('app', 'CREATED_AT'),
            'updatedAt' => Yii::t('app', 'UPDATED_AT'),
            'myRole' => Yii::t('app', 'PROJECT_SEARCH_MY_ROLE'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobs()
    {
        return $this->hasMany(Job::className(), ['projectId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectUsers()
    {
        return $this->hasMany(ProjectUser::className(), ['projectId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvites()
    {
        return $this->hasMany(ProjectInvite::className(), ['projectId' => 'id']);
    }

    /**
     * Current project user relation (by logged in user id)
     *
     * @param int|null $userId
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCurrentProjectUser($userId = null)
    {
        if (!$userId) {
            $userId = UserHelper::getCurrentId();
        }

        return $this->hasOne(ProjectUser::className(), ['projectId' => 'id'])
            ->andOnCondition(['userId' => (int)$userId])
            ->alias('currentProjectUser');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'userId'])->viaTable('projectUsers', ['projectId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwnerUser()
    {
        return $this->hasOne(User::className(), ['id' => 'ownerId']);
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

        if ($insert) {
            // Save created by user id
            $this->ownerId = UserHelper::getCurrentId();

            // Generate random unique project token
            $this->generateToken();
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            // Add project users admin record
            ProjectUser::assign($this->id, $this->ownerId, ProjectUserRole::ADMIN);
        }

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
     * Generate random unique project token
     */
    public function generateToken()
    {
        $maxAttempts = 100;
        $i = 0;
        do {
            $this->token = Yii::$app->security->generateRandomString(32);
        } while ($i++ < $maxAttempts && static::find()->where(['token' => $this->token])->exists());

        // Check that token was generated
        if ($i >= $maxAttempts) {
            throw new Exception('Unable to generate unique project token');
        }
    }

    /**
     * Check that current user has view access to current project
     *
     * @return bool
     */
    public function isViewable()
    {
        return $this->currentProjectUser &&
            in_array($this->currentProjectUser->role, [ProjectUserRole::VIEWER, ProjectUserRole::ADMIN]);
    }

    /**
     * Check that current user has admin access to current project
     *
     * @return bool
     */
    public function isManageable()
    {
        return $this->currentProjectUser && $this->currentProjectUser->role == ProjectUserRole::ADMIN;
    }

    /**
     * Change project owner
     *
     * @param integer $newOwnerId
     *
     * @return bool
     */
    public function changeOwner($newOwnerId)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $oldOwner = $this->ownerId;
            $this->ownerId = $newOwnerId;
            if ($this->save(false, ['ownerId'])) {
                // Ensure that new project owner is project admin too
                ProjectUser::assign($this->id, $newOwnerId, ProjectUserRole::ADMIN);
            } else {
                throw new Exception('Error while saving new project owner');
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();

            return false;
        }

        // @todo Send notification email for old/new project owners?

        return true;
    }

    /**
     * Mark project as deleted
     *
     * @return bool
     */
    public function safeDelete()
    {
        $this->deletedAt = date('Y-m-d H:i:s');

        return $this->save(false, ['deletedAt']);
    }

    /**
     * Return HTML-encoded name
     *
     * @return string
     */
    public function getFName()
    {
        return Html::encode($this->name);
    }

    /**
     * Get project id by token
     *
     * @param string $token
     *
     * @return false|null|string
     */
    public static function getIdByToken($token)
    {
        return static::find()->select(['id'])->where(['token' => $token])->scalar();
    }
}
