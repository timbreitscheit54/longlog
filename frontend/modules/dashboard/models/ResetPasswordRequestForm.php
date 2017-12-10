<?php
namespace frontend\modules\dashboard\models;

use common\models\SecureKey;
use common\models\User;
use yii\base\Model;
use Yii;

/**
 * Форма запроса на восстановления пароля
 *
 * @property User $user моделька редактируемого пользователя
 */
class ResetPasswordRequestForm extends Model
{
    public $email;
    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['email'], 'email'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'RESET_PASSWORD_REQUEST_EMAIL'),
        ];
    }

    /**
     *
     *
     * @return bool
     */
    public function sendRequest()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = User::find()->where(['email' => $this->email])->one();
        /* @var $user User */

        if (!$user) {
            $this->addError('email', Yii::t('app/error', 'RESET_PASSWORD_USER_NOT_FOUND'));

            return false;
        }

        // Если будет успешно создан новый ключ для восстановления пароля, то будет возвращено TRUE
        return SecureKey::create(SecureKey::TYPE_RESET_PASSWORD, $user);
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
