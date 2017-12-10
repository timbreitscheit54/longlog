<?php

namespace common\models;

use common\helpers\enum\Language;
use common\helpers\enum\UserRole;
use common\helpers\enum\UserStatus;
use Yii;
use yii\base\NotSupportedException;
use yii\helpers\Html;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $newEmail
 * @property string $role
 * @property string $authKey
 * @property string $accessToken
 * @property integer $status
 * @property string $language
 * @property string $deletedAt
 * @property string $createdAt
 * @property string $updatedAt
 *
 * relations
 * @property-read Project[] $projects
 *
 * getters
 * @property-read string $fName
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     * @return \common\components\query\UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\components\query\UserQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_DEFAULT] = ['name', 'email'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // required
            [['name', 'email', 'password'], 'required'],
            // integer
            [['status'], 'integer'],
            // string max
            [['name'], 'string', 'max' => 255],
            [['email', 'newEmail'], 'string', 'max' => 100],
            [['authKey', 'accessToken'], 'string', 'max' => 32],
            // email
            [['email', 'newEmail'], 'string', 'max' => 255],
            // range
            [['role'], 'in', 'range' => UserRole::getKeys()],
            [['status'], 'in', 'range' => UserStatus::getKeys()],
            [['language'], 'in', 'range' => Language::getKeys()],
            // unique
            [['email'], 'unique'],
            [['authKey'], 'unique'],
            [['accessToken'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'USER_NAME'),
            'email' => Yii::t('app', 'USER_EMAIL'),
            'password' => Yii::t('app', 'USER_PASSWORD'),
            'newEmail' => Yii::t('app', 'USER_NEW_EMAIL'),
            'role' => Yii::t('app', 'USER_ROLE'),
            'authKey' => Yii::t('app', 'USER_AUTH_KEY'),
            'accessToken' => Yii::t('app', 'USER_ACCESS_TOKEN'),
            'status' => Yii::t('app', 'USER_STATUS'),
            'deletedAt' => Yii::t('app', 'USER_DELETED_AT'),
            'createdAt' => Yii::t('app', 'USER_CREATED_AT'),
            'updatedAt' => Yii::t('app', 'UPDATED_AT'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Project::className(), ['id' => 'projectId'])
            ->viaTable('projectUsers', ['userId' => 'id']);
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
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => UserStatus::ACTIVE, 'deletedAt' => null]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['accessToken' => $token, 'status' => UserStatus::ACTIVE, 'deletedAt' => null]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     *
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['email' => $username, 'status' => UserStatus::ACTIVE, 'deletedAt' => null]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     *
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generate "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->authKey = Yii::$app->security->generateRandomString(32);
    }

    /**
     * Generate "accessToken" key
     */
    public function generateAccessToken()
    {
        $this->accessToken = Yii::$app->security->generateRandomString(32);
    }

    /**
     * @return string HTML-encoded user name
     */
    public function getFName()
    {
        return Html::encode($this->name);
    }

    /**
     * User changed language
     *
     * @param \codemix\localeurls\LanguageChangedEvent $event
     */
    public static function onLanguageChanged($event)
    {
        // $event->language: new language
        // $event->oldLanguage: old language

        // Save the current language to user record
        if (!Yii::$app->user->isGuest) {
            static::updateAll(['language' => $event->language], ['id' => Yii::$app->user->id]);
        }
    }
}
