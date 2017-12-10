<?php

namespace api\responses;

use common\models\Job;
use yii\base\BaseObject;

class JobResponse extends BaseObject
{
    /**
     * Convert model to array
     *
     * @param Job $model
     *
     * @return array
     */
    public static function toArray(Job $model)
    {
        $response = [
            'id' => $model->id,
            'projectId' => $model->projectId,
            'key' => $model->key,
            'title' => $model->title,
            'critDuration' => $model->critDuration,
            'createdAt' => $model->createdAt,
        ];

        return $response;
    }

    /**
     * Convert models to array
     *
     * @param Job[] $models
     *
     * @return array
     */
    public static function toArrayModels($models = [])
    {
        $response = [];
        foreach ($models as $model) {
            $response[] = static::toArray($model);
        }

        return $response;
    }

    /**
     * Convert job stat models to array
     *
     * @param Job $job
     * @param int $days Return stats for latest $days
     *
     * @return array
     */
    public static function stats(Job $job, $days = 30)
    {
        /** @var \common\models\Stat[] $items */
        $items = $job->getStats()->with(['minOperation', 'maxOperation'])
            ->orderBy(['date' => SORT_DESC])->limit($days)->all();
        $items = array_reverse($items);

        $statItems = [];

        foreach ($items as $stat) {
            // Label - createdAt
            $statItem = [
                'label' => $stat->date,
                'minOperationId' => $stat->minOperationId,
                'maxOperationId' => $stat->maxOperationId,
                'minValue' => $stat->minOperation ? round($stat->minOperation->duration / 60, 2) : 0,
                'maxValue' => $stat->maxOperation ? round($stat->maxOperation->duration / 60, 2) : 0,
                'avgValue' => round($stat->avgDuration / 60, 2),
            ];

            $statItems[] = $statItem;
        }

        return $statItems;
    }
}
