<?php

namespace api\components;

use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

/**
 * Base Api Controller
 */
class ApiController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(), [
            'bearerAuth' => [
                'class' => HttpBearerAuth::className(),
            ],
        ]);
    }
}
