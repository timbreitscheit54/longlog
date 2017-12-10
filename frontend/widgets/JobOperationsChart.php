<?php

namespace frontend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * JobOperations Chart widget
 */
class JobOperationsChart extends \dosamigos\chartjs\ChartJs
{
    /**
     * @var \common\models\Job
     */
    public $jobModel;
    public $type = 'line';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Replace default config
        $defaultClientOptions = [
            'responsive' => true,
            'tooltips' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'hover' => [
                'mode' => 'nearest',
                'intersect' => true,
                'animationDuration' => 0,
            ],
            'scales' => [
                'xAxes' => [
                    [
                        'display' => true,
                        'scaleLabel' => [
                            'display' => true,
                            'labelString' => Yii::t('app', 'CHART_DATE_LABEL'),
                        ],
                    ]
                ],
                'yAxes' => [
                    [
                        'display' => true,
                        'scaleLabel' => [
                            'display' => true,
                            'labelString' => Yii::t('app', 'CHART_MINUTES_LABEL'),
                        ],
                    ]
                ],
            ],
            'animation' => [
                'duration' => 0,
            ],
            'operationIds' => [],
            'responsiveAnimationDuration' => 0,
        ];

        // Handle chart point click
        $statViewUrl = Url::to(['/job/stat', 'id' => $this->jobModel->id, 'date' => '__DATE__']);
        $defaultClientOptions['onClick'] = new JsExpression(<<<JS
function (event, context) {
    if (typeof context[0] !== "undefined") {
        var index = context[0]._index;
        var date = context[0]._chart.options.dates[index];
        if (date) {
            window.location = "$statViewUrl".replace("__DATE__", date);
        }
    }
}
JS
        );

        $this->clientOptions = ArrayHelper::merge($defaultClientOptions, $this->clientOptions);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        /** @var \common\models\Stat[] $items */
        $items = $this->jobModel->getStats()->with(['minOperation', 'maxOperation'])
            ->orderBy(['date' => SORT_DESC])->limit(30)->all();
        $items = array_reverse($items);
        $labels = [];
        $averageDataSet = [];
        $minDataSet = [];
        $maxDataSet = [];
        foreach ($items as $stat) {
            // Label - createdAt
            $labels[] = $stat->date;
            // Minutes
            $this->clientOptions['dates'][] = $stat->date;
            $averageDataSet[] = round($stat->avgDuration / 60, 2);
            $minDataSet[] = $stat->minOperation ? round($stat->minOperation->duration / 60, 2) : 0;
            $maxDataSet[] = $stat->maxOperation ? round($stat->maxOperation->duration / 60, 2) : 0;
        }

        $this->data = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => Yii::t('app', 'CHART_MIN_DURATION_LABEL'),
                    'backgroundColor' => "rgb(54, 241, 57, 0.2)",
                    'borderColor' => "rgb(54, 241, 57)",
                    'pointBackgroundColor' => "rgb(54, 241, 57)",
                    'pointBorderColor' => "#fff",
                    'pointHoverBackgroundColor' => "rgb(54, 241, 57)",
                    'pointHoverBorderColor' => "rgba(54, 162, 235, 1)",
                    'pointHoverRadius' => 10,
                    'pointRadius' => 5,
                    'pointHitRadius' => 15,
                    'data' => $minDataSet,
                ],
                [
                    'label' => Yii::t('app', 'CHART_AVERAGE_DURATION_LABEL'),
                    'backgroundColor' => "rgb(237, 241, 54, 0.6)",
                    'borderColor' => "rgb(237, 241, 54)",
                    'pointBackgroundColor' => "rgb(237, 241, 54)",
                    'pointBorderColor' => "#fff",
                    'pointHoverBackgroundColor' => "rgb(237, 241, 54)",
                    'pointHoverBorderColor' => "rgba(54, 162, 235, 1)",
                    'pointHoverRadius' => 10,
                    'pointRadius' => 5,
                    'pointHitRadius' => 15,
                    'data' => $averageDataSet,
                ],
                [
                    'label' => Yii::t('app', 'CHART_MAX_DURATION_LABEL'),
                    'backgroundColor' => "rgb(241, 57, 54, 0.2)",
                    'borderColor' => "rgb(241, 57, 54)",
                    'pointBackgroundColor' => "rgb(241, 57, 54)",
                    'pointBorderColor' => "#fff",
                    'pointHoverBackgroundColor' => "rgb(241, 57, 54)",
                    'pointHoverBorderColor' => "rgba(54, 162, 235, 1)",
                    'pointHoverRadius' => 10,
                    'pointRadius' => 5,
                    'pointHitRadius' => 15,
                    'data' => $maxDataSet,
                ],
            ],
        ];

        return parent::run();
    }
}
