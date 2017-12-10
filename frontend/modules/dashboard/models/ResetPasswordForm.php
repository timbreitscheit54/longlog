<?php
namespace frontend\modules\dashboard\models;

use common\models\User;
use yii\base\Model;
use Yii;

/**
 * Форма восстановления пароля
 *
 * @property User $user моделька редактируемого пользователя
 */
class ResetPasswordForm extends Model
{
    public $password;
    public $password_c;
    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'password_c'], 'required'],
            [['password', 'password_c'], 'string', 'min' => 6, 'max' => 128],
            [
                'password_c', 'compare', 'operator' => '===', 'compareAttribute' => 'password',
                'message' => 'Confirm password does not match the password'
            ],
            // pattern
            ['password', 'match', 'pattern' => Yii::$app->params['user.passwordPattern']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => Yii::t('app', 'RESET_PASSWORD_NEW_PASSWORD'),
            'password_c' => Yii::t('app', 'RESET_PASSWORD_NEW_PASSWORD_CONFIRMATION'),
        ];
    }

    /**
     *
     *
     * @return bool
     */
    public function updatePassword()
    {
        if (!$this->validate()) {
            return false;
        }
        if (!$this->user instanceof User) {
            $this->addError('user_id', 'User not found');

            return false;
        }

        $user = $this->user;

        // Устанавливаем пароль
        $user->setPassword($this->password);

        return $user->save(false, ['password']);
    }

    public function setUser(User $user)
    {
        $this->_user = $user;
    }

    public function getUser()
    {
        return $this->_user;
    }
}
