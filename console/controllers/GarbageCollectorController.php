<?php

namespace console\controllers;

use common\models\RateLimiter;
use common\models\Operation;
use common\models\Project;
use common\models\Stat;
use console\components\ConsoleOutput;
use yii\console\Controller;

/**
 * Console Garbage Collector
 */
class GarbageCollectorController extends Controller
{
    use ConsoleOutput;

    /**
     * Run all collectors with default max age value
     */
    public function actionIndex()
    {
        $this->actionProjects();
        $this->actionOperations();
        $this->actionOparationsPlayload();
        $this->actionStats();
        $this->actionRateLimiter();
    }

    /**
     * Erase old operations
     *
     * @param int $maxMonths Remove data older this value
     */
    public function actionOperations($maxMonths = 6)
    {
        $maxAge = date('Y-m-d H:i:s', strtotime("-$maxMonths months"));

        $this->info('Erase operations older: ' . $maxAge);
        $count = Operation::deleteAll(['<=', 'createdAt', $maxAge]);
        $this->success("$count operations clear!");
    }

    /**
     * Erase old deleted projects
     *
     * @param int $maxMonths Remove data older this value
     */
    public function actionProjects($maxMonths = 3)
    {
        $maxAge = date('Y-m-d H:i:s', strtotime("-$maxMonths months"));

        $this->info('Erase deleted projects older: ' . $maxAge);
        $query = Project::find()->where('deletedAt IS NOT NULL AND deletedAt <= :maxAge', [':maxAge' => $maxAge]);

        $count = 0;
        foreach ($query->each() as $project) {
            /** @var Project $project */
            if ($project->delete()) {
                $count++;
            }
        }

        $this->success("$count projects clear!");
    }

    /**
     * Set to NULL old operations payload
     *
     * @param int $maxMonths Remove data older this value
     */
    public function actionOparationsPlayload($maxMonths = 6)
    {
        $maxAge = date('Y-m-d H:i:s', strtotime("-$maxMonths months"));

        $this->info('Set to NULL payload for operations older: ' . $maxAge);
        $count = Operation::updateAll(
            ['payload' => null],
            'payload IS NOT NULL AND createdAt <= :maxAge',
            [':maxAge' => $maxAge]
        );
        $this->success("$count operations payload setted to NULL!");
    }

    /**
     * Erase old stats
     *
     * @param int $maxMonths Remove data older this value
     */
    public function actionStats($maxMonths = 3)
    {
        $maxAge = date('Y-m-d', strtotime("-$maxMonths months"));

        $this->info('Erase stats data older: ' . $maxAge);
        $count = Stat::deleteAll(['<=', 'date', $maxAge]);
        $this->success("$count stat items clear!");
    }

    /**
     * Erase rate limiter data
     */
    public function actionRateLimiter()
    {
        // Delete old login requests for auth rate limiter
        $this->info('Erase old rate limiter data');
        $count = RateLimiter::deleteAll(['<=', 'time', strtotime('-1 minute')]);
        $this->success("$count items deleted");
    }
}
