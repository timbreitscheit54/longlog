<?php

namespace common\models;

use common\helpers\enum\ProjectInviteStatus;
use common\helpers\enum\ProjectUserRole;
use common\helpers\ProjectHelper;
use common\helpers\UserHelper;
use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\Url;

/**
 * This is the model class for table "projectInvites".
 *
 * @property integer $id
 * @property integer $projectId Project
 * @property string $email      Invited user email
 * @property string $code       Invitation secret code
 * @property string $role       Invited user project role
 * @property string $status     Invitation status
 * @property integer $createdBy Who invited
 * @property string $sentAt     Last email sent date
 * @property string $acceptedAt Accepted date
 *
 * relations
 * @property-read Project $project
 * @property-read User $inviter
 *
 * getters
 * @property-read string $url
 */
class ProjectInvite extends \yii\db\ActiveRecord
{
    const FLASH_SUCCESS_KEY = 'invite-success';
    const FLASH_ERROR_KEY = 'invite-error';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'projectInvites';
    }

    /**
     * @inheritdoc
     * @return \common\components\query\ProjectInviteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\components\query\ProjectInviteQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_DEFAULT] = ['email', 'role'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // required
            [['projectId', 'email', 'role'], 'required'],
            // integer
            [['projectId', 'createdBy'], 'integer'],
            // string max
            [['email'], 'string', 'max' => 255],
            // string length
            [['code'], 'string', 'length' => 32],
            // email
            [['email'], 'email'],
            // default
            [['status'], 'default', 'value' => ProjectInviteStatus::getDefault()],
            // range
            [['status'], 'in', 'range' => ProjectInviteStatus::getKeys()],
            [['role'], 'in', 'range' => ProjectUserRole::getKeys()],
            // exists
            [['projectId'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['projectId' => 'id']],
            [['createdBy'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['createdBy' => 'id']],
            // unique
            [
                ['email'], 'unique', 'skipOnError' => true, 'filter' =>
                function (Query $query) {
                    // User email should be unique within project and status = SENT
                    $query->andWhere(['projectId' => $this->projectId, 'status' => ProjectInviteStatus::SENT]);
                },
                'message' => Yii::t('app/error', 'PROJECT_INVITE_EMAIL_ALREADY_TAKEN'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'projectId' => Yii::t('app', 'INVITE_PROJECT'),
            'email' => Yii::t('app', 'INVITE_EMAIL'),
            'code' => Yii::t('app', 'INVITE_CODE'),
            'role' => Yii::t('app', 'INVITE_ROLE'),
            'status' => Yii::t('app', 'INVITE_STATUS'),
            'createdBy' => Yii::t('app', 'INVITE_CREATED_BY'),
            'sentAt' => Yii::t('app', 'INVITE_SENT_AT'),
            'acceptedAt' => Yii::t('app', 'INVITE_ACCEPTED_AT'),
        ];
    }

    /**
     * Project relation
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'projectId']);
    }

    /**
     * Inviter relation
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInviter()
    {
        return $this->hasOne(User::className(), ['id' => 'createdBy']);
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
            $this->generateCode();
            $this->createdBy = UserHelper::getCurrentId();
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
     * @inheritdoc
     */
    public function afterDelete()
    {
        // Do not forget remove related data!

        parent::afterDelete();
    }

    /**
     * Generate random unique invitation code
     */
    public function generateCode()
    {
        $maxAttempts = 100;
        $i = 0;
        do {
            $this->code = Yii::$app->security->generateRandomString(32);
        } while ($i++ < $maxAttempts && static::find()->where(['code' => $this->code])->exists());

        // Check that code was generated
        if ($i >= $maxAttempts) {
            throw new Exception('Unable to generate unique secret code');
        }
    }

    /**
     * Send invitation to email
     *
     * @throws \yii\base\InvalidParamException
     * @return bool
     */
    public function sendEmail()
    {
        $view = 'projectInvite-html';
        $subject = Yii::t('app', 'PROJECT_INVITE_EMAIL_SUBJECT_{sitename}{inviterName}{projectName}',
            [
                'sitename' => Yii::$app->name,
                'inviterName' => Yii::$app->user->identity->name,
                'projectName' => $this->project->name,
            ]);

        $result = Yii::$app->mailer->compose($view, ['project' => $this->project, 'invite' => $this])
            ->setFrom([Yii::$app->params['noreplyEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject($subject)
            ->send();

        if ($result) {
            $this->sentAt = date('Y-m-d H:i:s');
            $this->save(false, ['sentAt']);
        }

        return $result;
    }

    /**
     * Get invite activation absolute url
     *
     * @return string
     */
    public function getUrl()
    {
        return Url::to(['/site/signup', 'inviteCode' => $this->code], true);
    }

    /**
     * Mark this invite as used
     *
     * @param User $user Registered user
     *
     * @return bool
     */
    public function activate(User $user)
    {
        // Create new project user
        ProjectUser::assign($this->projectId, $user->id, $this->role);

        $this->status = ProjectInviteStatus::ACCEPTED;
        $this->acceptedAt = date('Y-m-d H:i:s');

        return $this->save(false, ['status', 'acceptedAt']);
    }

    /**
     * Check that invite can be resent
     *
     * @return integer 0 - can be resent, otherwise seconds count before resend will posible
     */
    public function canResendAfter()
    {
        $minResendInterval = Yii::$app->params['inviteMinResendInterval'];
        if (!$minResendInterval) {
            $minResendInterval = 5 * 60; // default 5 minutes
        }


        if (!$this->sentAt) {
            return 0;
        }

        $diff = time() - strtotime($this->sentAt);
        // If the last time an email was sent later than the interval
        if ($diff >= $minResendInterval) {
            return 0;
        }

        return $minResendInterval - $diff;
    }

    /**
     * Save new invite and send email, or just assign user to project
     *
     * @return bool
     */
    public function processNewInvite()
    {
        if (!$this->validate()) {
            return false;
        }

        $session = Yii::$app->session;
        // Trying to search registered user
        $user = User::findByUsername($this->email);
        if ($user) {
            $hasAccess = ProjectUser::hasAccess($this->projectId, $user->id, $this->role);
            if ($hasAccess) {
                // User already have access
                $session->setFlash(static::FLASH_ERROR_KEY, Yii::t('app/error',
                    'PROJECT_INVITE_ERROR_USER_ALREADY_INVITED_{email}', ['email' => $user->email]));

                return false;
            }

            // Assign exists user to project
            $result = ProjectUser::assign($this->projectId, $user->id, $this->role);
            if ($result) {
                $session->setFlash(static::FLASH_SUCCESS_KEY, Yii::t('app',
                    'PROJECT_INVITE_USER_ASSIGNED_{email}', ['email' => $user->email]));
            } else {
                $session->setFlash(static::FLASH_ERROR_KEY, Yii::t('app/error',
                    'PROJECT_INVITE_SERVER_ERROR_{email}', ['email' => $user->email]));
            }

            return $result;
        }

        // Send invitation email
        if ($this->save(false)) {
            $result = $this->sendEmail();
            if ($result) {
                $session->setFlash(static::FLASH_SUCCESS_KEY,
                    Yii::t('app', 'PROJECT_INVITE_EMAIL_SENT_{email}{role}',
                        ['email' => $this->email, 'role' => ProjectUserRole::getLabel($this->role)]));
            }

            return $result;
        } else {
            $session->setFlash(static::FLASH_ERROR_KEY, Yii::t('app/error',
                'PROJECT_INVITE_SAVE_ERROR_{email}', ['email' => $this->email]));
        }

        return false;
    }
}
