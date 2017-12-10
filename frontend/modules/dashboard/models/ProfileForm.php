<?php

namespace frontend\modules\dashboard\models;

use common\models\SecureKey;
use common\models\User;
use yii\base\Model;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * User profile edit foem
 *
 * @property User $user Editing user model
 */
class ProfileForm extends Model
{
    /**
     * @var integer User id
     */
    public $user_id;
    public $name;
    public $email;
    public $password;
    public $password_c;
    private $_user;

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['name', 'email', 'password', 'password_c'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // required
            [['name'], 'required'],
            // string
            [['name'], 'string', 'min' => 2, 'max' => 255],
            [['password', 'password_c'], 'string', 'min' => 6, 'max' => 128],
            [
                'password_c', 'compare', 'operator' => '===', 'compareAttribute' => 'password',
                'message' => Yii::t('app/error', 'SIGNUP_PASSWORDS_NOT_COMPARE'),
            ],
            // email
            ['email', 'email'],
            // unique
            [
                'email', 'unique', 'filter' =>
                function ($query) {
                    /* @var $query Query */
                    $query->andWhere('email != :user_email', [':user_email' => $this->user->email]);
                },
                'targetClass' => '\common\models\User', 'message' => Yii::t('app/error', 'SUGNUP_EMAIL_ALREADY_USED'),
            ],
            // pattern
            ['password', 'match', 'pattern' => Yii::$app->params['user.passwordPattern']],
        ];
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge($this->user->attributeLabels(), [
            'password_c' => Yii::t('app', 'SIGNUP_PASSWORD_CONFIRM'),
        ]);
    }

    /**
     * Непосредственное обновление профиля пользователя
     *
     * @return bool
     */
    public function updateProfile()
    {
        if (!$this->validate()) {
            return false;
        }
        if (!$this->user instanceof User) {
            $this->addError('user_id', 'User not found');

            return false;
        }

        $user = $this->user;

        $user->name = $this->name;

        // Обновляем пароль
        if (!empty($this->password)) {
            // Если пароль в форме не пустой, значит он валиден.
            $user->setPassword($this->password);
            // Добавляем уведомление о смене пароля
            Yii::$app->session->setFlash('passwordChanged', Yii::t('app', 'PROFILE_FORM_PASSWORD_CHANGED'));
        }

        // Смена E-mail адреса
        if (!empty($this->email) && $this->email !== $user->email) {
            // Генерируем ссылку для подтверждения нового e-mail адреса,
            // там же происходит сохранение нового адреса в БД в users.new_email
            if (SecureKey::create(SecureKey::TYPE_CHANGE_EMAIL, $user, $this->email)) {
                // Добавляем уведомление об отправке подтверждения для смены e-mail адреса
                Yii::$app->session->setFlash('emailChangeSend',
                    Yii::t('app', 'PROFILE_FORM_EMAIL_CHANGE_REQUEST_SENT_{newEmail}', [
                        'newEmail' => '<b>"' . Html::encode($this->email) . '"</b>'
                    ])
                );
            }

        }

        return $user->save();
    }

    /**
     * Set user model to the form
     *
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;

        // Copy current user model values
        $this->user_id = $user->getId();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    /**
     * Get user model
     *
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }
}
