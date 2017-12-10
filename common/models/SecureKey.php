<?php

namespace common\models;

use common\helpers\enum\UserStatus;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\InvalidParamException;

/**
 * Secret codes model
 *
 * @property integer $id
 * @property string $userId
 * @property integer $type     Key assignment(1-activation, 2-email changing, 3-password reset)
 * @property string $code      Secret code
 * @property string $status
 * @property string $validTo   Expiration date
 * @property string $updatedAt Date of key usage
 *
 * relations
 * @property-read User $user
 *
 * getters
 * @property-read string $url
 */
class SecureKey extends ActiveRecord
{
    const STATUS_NEW = 'new';
    const STATUS_USED = 'used';
    const STATUS_FORGOTTEN = 'forgotten';
    const TYPE_ACTIVATE = 1;
    const TYPE_CHANGE_EMAIL = 2;
    const TYPE_RESET_PASSWORD = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'secure_keys';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => '\common\components\behaviors\TimestampBehavior',
                'createdAtAttribute' => null,
                'updatedAtAttribute' => 'updatedAt',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        // $scenarios['demo'] = ['userId', 'type', 'code', 'status', 'validTo', 'updatedAt'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // required
            [['userId', 'type', 'code'], 'required'],
            // integer
            [['userId', 'type'], 'integer'],
            // default
            ['status', 'default', 'value' => static::STATUS_NEW],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userId' => Yii::t('app', 'SECURE_KEY_USER_ID'),
            'type' => Yii::t('app', 'SECURE_KEY_TYPE'),
            'code' => Yii::t('app', 'SECURE_KEY_CODE'),
            'status' => Yii::t('app', 'SECURE_KEY_STATUS'),
            'validTo' => Yii::t('app', 'SECURE_KEY_VALID_TO'),
            'updatedAt' => Yii::t('app', 'SECURE_KEY_USED_DATE'),
        ];
    }

    /**
     * Relation to User model
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId'])->andOnCondition(['deletedAt' => null]);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // Set expiration time for the new key
        if ($insert && empty($this->validTo)) {
            $this->validTo = date('Y-m-d H:i:s', time() + Yii::$app->params['secureKey.expirationTime']);
        }

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
     * Проверяем, чтобы срок годности не истёк
     *
     * @return bool
     */
    public function isValidKey()
    {
        $validTo = strtotime($this->validTo);

        // Expire time should be in future
        return $validTo && $validTo >= time();
    }

    /**
     * Get formatted expiration date
     *
     * @return string
     */
    public function getFormattedExpireTime()
    {
        return Yii::$app->formatter->asDatetime(strtotime($this->validTo), 'long');
    }

    /**
     * Get random key string
     * @return string 32 characters
     * @throws \yii\base\Exception
     */
    public static function generateCode()
    {
        $maxAttempts = 100;
        $i = 0;
        $code = null;
        do {
            $code = Yii::$app->security->generateRandomString(32);
        } while ($i++ < $maxAttempts && static::find()->where(['code' => $code])->exists());

        // Check that code was generated
        if ($i >= $maxAttempts) {
            throw new Exception('Unable to generate unique secret code');
        }

        return $code;
    }

    /**
     * Generate new code by type and send it to user target email
     *
     * @param integer $type                Code type
     * @param IdentityInterface|User $user User model
     * @param string|null $newEmail        New email address for email changing
     *
     * @return bool
     */
    public static function create($type, IdentityInterface $user, $newEmail = null)
    {
        /** @var SecureKey $key */
        $key = new static();

        $key->type = $type;
        $key->userId = $user->getId();
        $key->code = static::generateCode();

        if ($key->save()) {
            // Set "forgotten" status for all previous codes for the same user and same type
            $key->updateAll(['status' => self::STATUS_FORGOTTEN], 'userId=:uid AND type=:type AND code!=:code', [
                ':uid' => $key->userId, ':type' => $key->type, ':code' => $key->code,
            ]);

            if ($type == self::TYPE_CHANGE_EMAIL) {
                // Save new email in the user model temporary field
                $user->newEmail = $newEmail;
                $user->save(false, ['newEmail']);
            }

            // Send email
            return $key->sendEmail($user, $newEmail);
        } else {
            return false;
        }

    }

    /**
     * Send sucure code to user email
     *
     * @param IdentityInterface|User $user User model
     * @param null $email                  optional: custom email
     *
     * @throws \yii\base\InvalidParamException
     * @return bool
     */
    public function sendEmail(IdentityInterface $user, $email = null)
    {
        switch ($this->type) {
            case self::TYPE_ACTIVATE:
                $view = 'userActivate-html';
                $subject = Yii::t('app', 'SECURE_KEY_SUBJECT_ACTIVATE_{sitename}', ['sitename' => Yii::$app->name]);
                break;
            case self::TYPE_CHANGE_EMAIL:
                $view = 'userChangeEmail-html';
                $subject = Yii::t('app', 'SECURE_KEY_SUBJECT_CHANGE_EMAIL_{sitename}', ['sitename' => Yii::$app->name]);
                break;
            case self::TYPE_RESET_PASSWORD:
                $view = 'userResetPassword-html';
                $subject = Yii::t('app', 'SECURE_KEY_SUBJECT_RESET_PASSWORD_{sitename}',
                    ['sitename' => Yii::$app->name]);
                break;
            default:
                throw new InvalidParamException('Unknown secure key type');
        }

        // Use custom email if isset or use user model email
        $send_email = $email ? $email : $user->email;

        return Yii::$app->mailer->compose($view, ['user' => $user, 'key' => $this])
            ->setFrom([Yii::$app->params['noreplyEmail'] => Yii::$app->name . ' robot'])
            ->setTo([$send_email => $user->name])
            ->setSubject($subject)
            ->send();
    }

    /**
     * Trying to search secure key by code
     *
     * @param integer $userId
     * @param string $code
     *
     * @return SecureKey|array|null
     */
    public static function getKey($userId, $code)
    {
        return static::find()->where(['userId' => $userId, 'code' => $code])->one();
    }

    /**
     * Get code activation absolute url by type
     *
     * @return string
     */
    public function getUrl()
    {
        $route = '';
        switch ($this->type) {
            case self::TYPE_ACTIVATE:
                $route = '/dashboard/profile/activate';
                break;
            case self::TYPE_CHANGE_EMAIL:
                $route = '/dashboard/profile/change-email';
                break;
            case self::TYPE_RESET_PASSWORD:
                $route = '/dashboard/profile/reset-password';
                break;
        }

        return Yii::$app->urlManager->createAbsoluteUrl([$route, 'id' => $this->userId, 'code' => $this->code]);
    }

    /**
     * Activate current secure key.
     * If errors: return FALSE and error descriptions in $model->errors array
     *
     * @return bool
     */
    public function activate()
    {
        $this->clearErrors();

        switch ($this->type) {
            case self::TYPE_ACTIVATE:
                // New user activation
                $user = $this->user;
                $user->status = UserStatus::ACTIVE;

                if (!$user->save(false, ['status'])) {
                    $this->addError('userId', Yii::t('app/error', 'SECURE_KEY_ERROR_USER_SAVING'));

                    return false;
                }

                break;
            case self::TYPE_CHANGE_EMAIL:
                // Confirmation email changing
                $user = $this->user;
                // If some other user already changed email and already have same email address
                if (User::find()->where(['email' => $user->newEmail])->exists()) {
                    $this->addError('userId', Yii::t('app/error', 'SECURE_KEY_EMAIL_ALREADY_USED'));

                    return false;
                }
                // Set newEmail as primary email
                $user->email = $user->newEmail;
                $user->newEmail = null;

                // Save changes
                if (!$user->save(false, ['email', 'newEmail'])) {
                    $this->addError('userId', Yii::t('app/error', 'SECURE_KEY_ERROR_USER_SAVING'));

                    return false;
                }

                break;
            case self::TYPE_RESET_PASSWORD:
                // Password already restored, no action needed

                break;
        }

        // Set status "used" for this secure key
        $this->status = self::STATUS_USED;

        // Save secure key changes
        return $this->save(false, ['status']);
    }
}
