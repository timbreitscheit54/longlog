<?php

namespace console\controllers;

use common\components\stat\StatHandler;
use common\models\Stat;
use common\models\Project;
use console\components\ConsoleOutput;
use yii\console\Controller;

class StatController extends Controller
{
    use ConsoleOutput;

    /**
     * Generate today stats
     */
    public function actionToday()
    {
        $this->actionPeriod(0);
    }

    /**
     * Generate daily stats
     */
    public function actionDaily()
    {
        $this->actionPeriod(1);
    }

    /**
     * Generate monthly stats
     */
    public function actionMonth()
    {
        $this->actionPeriod(30);
    }

    /**
     * Generate stats for period
     *
     * @param integer $days (0 - means today only, 1 - today and yesterday, 2 - today, yesterday and day before yesterday...)
     */
    public function actionPeriod($days)
    {
        $query = Project::find()->active()->with(['jobs']);

        foreach ($query->each(20) as $project) {
            // Lock stats table write - prevent duplication entry errors
            Stat::lockTable();

            $statHandler = new StatHandler();
            foreach ($project->jobs as $job) {
                for ($i = $days; $i >= 0; $i--) {
                    $date = date('Y-m-d', strtotime("-$i days"));
                    // $this->info("Process job#{$job->id}: $date");

                    if (!$statHandler->processJob($job, $date)) {
                        foreach ($statHandler->getErrors() as $error) {
                            $this->error($error);
                        }
                    }
                }
            }
            // Manualy insert last part of stats data from buffer
            $statHandler->finish();

            // Unlock stats table
            Stat::unlockTable();
        }

    }
}
