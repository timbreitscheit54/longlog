<?php

namespace frontend\models;

use common\helpers\enum\ProjectInviteStatus;
use common\helpers\enum\UserRole;
use common\helpers\enum\UserStatus;
use common\models\ProjectInvite;
use common\models\SecureKey;
use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 *
 * @property-write string $inviteCode
 * @property-read ProjectInvite $invite
 */
class SignupForm extends Model
{
    public $email;
    public $name;
    public $password;
    public $password_confirm;
    public $captcha;
    /**
     * If new user was invited
     *
     * @var ProjectInvite
     */
    protected $projectInvite;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // required
            [['email', 'name', 'password', 'password_confirm', 'captcha'], 'required'],
            // email
            [['email'], 'email'],
            // unique
            [
                ['email'], 'unique', 'targetClass' => '\common\models\User',
                'message' => Yii::t('app/error', 'SUGNUP_EMAIL_ALREADY_USED'),
            ],
            // string
            [['name'], 'string', 'min' => 1, 'max' => 20],
            [['email'], 'string', 'min' => 5, 'max' => 100],
            [['password', 'password_confirm'], 'string', 'min' => 6, 'max' => 32],
            // Compare
            [
                ['password_confirm'], 'compare', 'compareAttribute' => 'password',
                'message' => Yii::t('app/error', 'SIGNUP_PASSWORDS_NOT_COMPARE'),
            ],
            // captcha
            [['captcha'], 'demi\recaptcha\ReCaptchaValidator', 'secretKey' => Yii::$app->params['reCAPTCHA.secretKey']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'SIGNUP_EMAIL'),
            'name' => Yii::t('app', 'SIGNUP_NAME'),
            'password' => Yii::t('app', 'SIGNUP_PASSWORD'),
            'password_confirm' => Yii::t('app', 'SIGNUP_PASSWORD_CONFIRM'),
            'captcha' => Yii::t('app', 'SIGNUP_CAPTCHA'),
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();

        $user->name = $this->name;
        $user->email = $this->email;
        $user->setPassword($this->password);

        $user->role = UserRole::getDefault();
        $user->status = UserStatus::INACTIVE;
        $user->generateAuthKey();
        $user->generateAccessToken();

        // Send user account activation email
        $activationEmailRequired = Yii::$app->params['user.sendActivationEmail'];
        if ($this->invite) {
            // For invited users auto email confirmation
            $activationEmailRequired = false;
        }

        if (!$activationEmailRequired) {
            // Auto confirmation new user
            $user->status = UserStatus::ACTIVE;
        }

        if ($user->save()) {
            // Activate invite
            if ($this->projectInvite) {
                $this->projectInvite->activate($user);
            }

            // Send confirmation email
            if ($activationEmailRequired) {
                // Send activation email
                SecureKey::create(SecureKey::TYPE_ACTIVATE, $user);
            }

            // Delete all unused invites for user email
            $unusedInvites = ProjectInvite::find()->where(['email' => $user->email, 'status' => ProjectInviteStatus::SENT])->all();
            foreach ($unusedInvites as $invite) {
                $invite->delete();
            }

            return $user;
        } else {
            $this->errors = $user->errors;
        }

        return null;
    }

    /**
     * Invite code setter.
     * Search ProjectInvite.
     *
     * @param string $code
     */
    public function setInviteCode($code)
    {
        if (!$code) {
            return;
        }

        // Search invite
        $this->projectInvite = ProjectInvite::find()->where(['code' => $code, 'status' => ProjectInviteStatus::SENT])->one();
        if ($this->projectInvite) {
            $this->email = $this->projectInvite->email;
        }
    }

    /**
     * Project invite getter
     *
     * @return ProjectInvite
     */
    public function getInvite()
    {
        return $this->projectInvite;
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        // If invite found - allow registration only for invited email
        if ($this->projectInvite) {
            $this->email = $this->projectInvite->email;
        }

        return true;
    }
}
