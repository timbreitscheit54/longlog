<?php

namespace console\controllers;

use common\helpers\enum\UserStatus;
use common\models\Job;
use common\models\Operation;
use common\models\Project;
use common\models\User;
use console\components\ConsoleOutput;
use Yii;
use common\helpers\enum\UserRole;
use yii\console\Controller;

class DemoController extends Controller
{
    use ConsoleOutput;

    protected $demoUser;
    protected $operationsBuffer = [];

    /**
     * Creates demo user and fill demo projects data
     */
    public function actionFill()
    {
        // Create/Update demo user
        $demoUser = User::find()->where([
            'email' => 'demo@longlog.ru',
        ])->one();

        if (!$demoUser) {
            $demoUser = new User();
            $demoUser->email = 'demo@longlog.ru';
            $demoUser->generateAuthKey();
            $demoUser->generateAccessToken();
        }

        $demoUser->name = 'Demo';
        $demoUser->setPassword('demo');
        $demoUser->role = UserRole::MANAGER;
        $demoUser->status = UserStatus::ACTIVE;
        $demoUser->deletedAt = null;

        $demoUser->save(false);
        $this->demoUser = $demoUser;


        // Create/Update demo projects
        $projectNames = [
            // project name => [job1, job2, job3, ...]
            'Demo' => [
                'JOB1_SEND_EMAIL',
                'JOB2_UPDATE_USER',
                'JOB3_GENERATE_SITEMAP',
                'JOB4_CLEAR_CACHE',
            ],
            'Example' => [
                'CRON_GENERATE_RSS',
                'CRON_GARBAGE_COLLECTOR',
                'CRON_SYNC_MEMBERS',
            ],
        ];
        foreach ($projectNames as $projectName => $jobNames) {
            $this->info("Fill project '$projectName'...");
            $project = $this->project($projectName, $jobNames);
        }
    }

    /**
     * Create/Update project and fill project jobs
     *
     * @param string $name    Project name
     * @param array $jobNames Job names
     *
     * @return array|\common\models\Project|null
     */
    protected function project($name, $jobNames = [])
    {
        $project = Project::find()->where([
            'ownerId' => $this->demoUser->id,
            'name' => $name,
        ])->one();

        if (!$project) {
            $project = new Project();
            $project->ownerId = $this->demoUser->id;
            $project->name = $name;
        }
        $project->deletedAt = null;
        $project->save();

        // Generate jobs
        $this->jobs($project, $jobNames);

        // Insert last operations buffer values
        $this->insertOperation(null, null, null, 0);

        return $project;
    }

    /**
     * Create/Update project job and fill job operations
     *
     * @param Project $project Project model
     * @param array $jobNames  Job names
     */
    protected function jobs(Project $project, $jobNames)
    {
        foreach ($jobNames as $jobName) {
            $this->info("Fill project job '$jobName'...");
            $job = Job::find()->where([
                'projectId' => $project->id,
                'key' => $jobName,
            ])->one();

            if (!$job) {
                $job = new Job();
                $job->projectId = $project->id;
                $job->key = $jobName;
            } else {
                // Truncate job old operations
                Operation::deleteAll(['jobId' => $job->id]);
            }

            // Critical duration (5-10 minutes)
            $job->critDuration = mt_rand(5, 10) * 60;

            // Fill operations
            if ($job->save(false)) {
                $this->operations($job);
            }
        }
    }

    /**
     * Generate operations for job for the last month
     *
     * @param Job $job Job model
     */
    protected function operations(Job $job)
    {
        // Fill operations for the 31 days
        for ($i = 31; $i >= 0; $i--) {
            // Number of operations per day
            $operationsPerDay = mt_rand(20, 50);
            $date = strtotime("-$i days");
            // Operations evenly for the day
            $operationsPeriod = floor(86399 / $operationsPerDay);
            for ($j = 0; $j < $operationsPerDay; $j++) {
                // Calculate operation duration seconds
                $duration = $this->getSmartDuration($job->critDuration);
                // Operation event time
                $time = $date + ($operationsPeriod * $j);
                $this->insertOperation($job->id, $duration, date('Y-m-d H:i:s', $time));
            }
        }
    }

    /**
     * Get random duration time
     *
     * @param float $critDuration Job critical duration
     *
     * @return float Random duration value
     */
    protected function getSmartDuration($critDuration)
    {
        $maxTimeHits = 2; // 2% that duration has max time
        $minTimeHits = 15; // 13% that duration has min time
        // 85% that duration has average time

        $hit = mt_rand(1, 100);
        if ($hit <= $maxTimeHits) {
            // maximal hits: crit + (-crit/2 .. crit/4)
            $duration = $critDuration + mt_rand(-$critDuration / 2, $critDuration / 4);
        } elseif ($hit <= $minTimeHits) {
            // minimal hits: crit + (-crit .. crit/1.5)
            $duration = $critDuration + mt_rand(-$critDuration, $critDuration / 1.5);
        } else {
            // average hits: crit/2 - (-crit/3 .. crit/3)
            $duration = $critDuration / 2 - mt_rand(-$critDuration / 3, $critDuration / 3);
        }

        return round($duration, 3);
    }

    /**
     * Operations insert buffer
     *
     * @param int|null $jobId     Job id
     * @param float $duration     Operation duration
     * @param string $createdAt   Operation event time
     * @param integer $bufferSize Insert batch size, 0 - means insert every item
     */
    protected function insertOperation($jobId, $duration, $createdAt, $bufferSize = 1000)
    {
        if ($jobId !== null) {
            $this->operationsBuffer[] = [
                (int)$jobId, (float)$duration, $createdAt,
            ];
        }

        // Buffer is full - insert to db
        if (count($this->operationsBuffer) > $bufferSize) {
            $count = Operation::getDb()->createCommand()->batchInsert(Operation::tableName(),
                ['jobId', 'duration', 'createdAt'],
                $this->operationsBuffer
            )->execute();
            // Clear buffer
            $this->operationsBuffer = [];

            $this->success("Inserted $count operations");
        }
    }
}
