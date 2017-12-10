<?php

namespace api\responses;

use common\models\Project;
use yii\base\BaseObject;

class ProjectResponse extends BaseObject
{
    /**
     * Convert model to array
     *
     * @param \common\models\Project $model
     * @param bool $withJobs Add jobs to response
     *
     * @return array
     */
    public static function toArray(Project $model, $withJobs = false)
    {
        $response = [
            'id' => $model->id,
            'name' => $model->name,
            'token' => null,
            'ownerId' => $model->ownerId,
            'createdAt' => $model->createdAt,
            'updatedAt' => $model->updatedAt,
            'isViewable' => $model->isViewable(),
            'isManageable' => $model->isManageable(),
        ];
        if ($model->isManageable()) {
            $response['token'] = $model->token;
        }
        if ($model->currentProjectUser) {
            $response['currentProjectUser'] = [
                'userId' => $model->currentProjectUser->userId,
                'role' => $model->currentProjectUser->role,
            ];
        }

        // Add jobs to response
        if ($withJobs) {
            $response['jobs'] = [];
            foreach ($model->jobs as $job) {
                $jobItem = JobResponse::toArray($job);
                // Add job shortly stats(7 days)
                $jobItem['stats'] = JobResponse::stats($job, 7);

                $response['jobs'][] = $jobItem;
            }

        }

        return $response;
    }

    /**
     * Convert models to array
     *
     * @param Project[] $models
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
}
