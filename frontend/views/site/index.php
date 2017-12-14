<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'About project';
?>
<div id="documentation">
    <h1>LongLog service</h1>
    <p>
        Purpose of this service:
    </p>
    <ol>
        <li>Tracking execution time of tasks in any of your projects.</li>
        <li>Timely notification of problems (task has been critical duration).</li>
        <li>Visual chart with information about min/avg/max durations by days.</li>
    </ol>
    <blockquote>
        <strong>A typical task:</strong> Your project has a task that is performed by cron-job of every 10 minutes,
        for example report generation for all users.<br/>
        With increasing number of users will increase and task duration time and you would like to know when
        reports generation will take more than 10 minutes.
    </blockquote>

    <h3>Installation</h3>
    <p>
        You can freely use this service and change its source code to your needs.
    </p>
    <p>
        All the source code of the web-part you can get here:
        <a href="https://github.com/demisang/longlog" target="_blank">https://github.com/demisang/longlog</a>
    </p>
    <p>
        Related subprojects:
    </p>
    <ul>
        <li>
            <a href="https://github.com/demisang/longlog-php-sdk" target="_blank">PHP SDK</a>
            Used to create and send logs from your PHP project to this service.
        </li>
        <li>
            <a href="https://github.com/demisang/yii2-longlog" target="_blank">Yii2 extension</a>
            to compose and send logs from any Yii2 project.
        </li>
        <li>
            <a href="https://github.com/demisang/longlog-android" target="_blank">Android-client</a>
            for the phone, displays charts with reports.
        </li>
    </ul>
    <p>
        <a href="https://play.google.com/store/apps/details?id=ru.longlog" target="_blank">
            <img style="width: 200px" alt="Get it on Google Play"
                 src="https://play.google.com/intl/en_us/badges/images/generic/en_badge_web_generic.png"/>
        </a>
    </p>

    <p>
        The order of installation and configuration is detailed described by the links above.
    </p>

    <p>
        <u>If you <strong>not</strong> using PHP</u> in your project, you should write log submitting via http-request
        as it:
    </p>

    <pre>curl --request POST \
  --url http://api.longlog.ru/project/log \
  --header 'content-type: application/json' \
  --data '{
      "projectToken": "ZRsOMDtgSIecLDryc3jBsvFsdGUXAyR2",
      "jobName"     : "CRON_SEND_EMAIL",
      "duration"    : 122.734,
      "payload"     : "{1, 2, 3, 4, 5}"
  }'</pre>

    <p>
        <strong>--url http://api.&lt;your-domain.com&gt;/project/log</strong> - you can change the address
        of your server on which this service is deployed.<br/>
        <br/>
        <strong>projectToken</strong> - the secret key of your project that you created
        <?= Html::a('here', ['/project/index']) ?>.
        <br/>
        <strong>jobName</strong> - identifier of your task in this project, by this value logs will be grouped.
        If this is the first log with this jobName, the new task will be automatically created in the project,
        and in the future it will be possible to give a human-understandable name for this task in the web-interface.
        <br/>
        <strong>duration</strong> - time of task execution in seconds accurate to thousandth, maximum value is
        <code>999999.999</code>.
        <br/>
        <strong>payload</strong> <em>(optional)</em> - an arbitrary string of up to 255 characters long, you can
        transfer any data that will help you in the future to determine the reason why the task was performed
        for so long. For example, you can pass the id of the users being processed <code>"1,4,9"</code>,
        or simply amount of data processed <code>"users count: 381"</code>.
        <br/>
        <strong>timestamp</strong> <em>(optional)</em> - unix timestamp, when this event occurred. By default
        current time is used, but you can pass your custom value.
    </p>

    <p>
        If successful, you will get response: <code>HTTP/1.1 201 Created</code>.<br/>
        If an error occurs by data validation, response is: <code>HTTP/1.1 400 Bad Request</code>,
        and in the response body will be json-array validation errors.<br/>
        If project with the specified token is not found: <code>HTTP/1.1 404 Not Found</code>.
    </p>
</div>
