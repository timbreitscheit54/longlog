<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'О проекте';
?>
<div id="documentation">
    <h1>Сервис LongLog</h1>
    <p>
        Назначение данного сервиса:
    </p>
    <ol>
        <li>Отслеживание времени выполнения задач в любом вашем проекте.</li>
        <li>Своевременное оповещение о проблемах (задача выполнялась критически долго).</li>
        <li>Предоставление отчёта в виде наглядного графика с данными о минимальном/среднем/максимальном времени.</li>
    </ol>
    <blockquote>
        <strong>Типичная задача:</strong> В вашем проекте имеется задание, которое выполняется по крону каждые 10 минут,
        например генерация отчёта для всех пользователей.<br/>
        С увеличением количества пользователей будет увеличиваться и время выполнения задания, и вы хотели бы знать,
        когда генерация отчётов будет длиться более 10 минут.
    </blockquote>

    <h3>Установка</h3>
    <p>
        Вы можете свободно использовать данный сервис и менять его исходный код под ваши потребности.
    </p>
    <p>
        Весь исходный код web-части вы можете получить здесь:
        <a href="https://github.com/demisang/longlog" target="_blank">https://github.com/demisang/longlog</a>
    </p>
    <p>
        Сопутствующие подпроекты:
    </p>
    <ul>
        <li>
            <a href="https://github.com/demisang/longlog-php-sdk" target="_blank">PHP SDK</a>
            используется для формирования и отправки логов из вашего PHP-проекта в этот сервис.
        </li>
        <li>
            <a href="https://github.com/demisang/yii2-longlog" target="_blank">Yii2 расширение</a>
            для формирования и отправки логов из проекта на Yii2.
        </li>
        <li>
            <a href="https://github.com/demisang/longlog-android" target="_blank">Android-клиент</a>
            для телефона, отображает графики с отчётами.
        </li>
    </ul>
    <p>
        <a href="https://play.google.com/store/apps/details?id=ru.longlog" target="_blank">
            <img style="width: 200px" alt="Доступно в Google Play"
                 src="https://play.google.com/intl/en_us/badges/images/generic/ru_badge_web_generic.png"/>
        </a>
    </p>

    <p>
        Порядок установки и настройки детально расписан по ссылкам выше.
    </p>

    <p>
        <u>Если вы <strong>не</strong> используете PHP</u> в своём проекте, то вам следует вручную сформировать
        http-запрос следующего
        формата:
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
        <strong>--url http://api.&lt;your-domain.com&gt;/project/log</strong> - вы можете изменить на адрес своего
        сервера, на котором развёрнут данный сервис.<br/>
        <br/>
        <strong>projectToken</strong> - секретный ключ вашего проекта, который вы создали
        <?= Html::a('здесь', ['/project/index']) ?>.
        <br/>
        <strong>jobName</strong> - идентификатор вашей задачи в данном проекте,
        по этому значению будут группироваться логи.
        Если это первый лог с данным идентификатором - задача будет автоматически создана в проекте,
        и в дальнейшем можно будет дать человекопонятное название этой задаче в web-интерфейсе.
        <br/>
        <strong>duration</strong> - время выполнения задачи в секундах с точностью до тысячных, максимальное значение
        <code>999999.999</code>.
        <br/>
        <strong>payload</strong> <em>(не обязательно)</em> - произвольная строка длиной до 255 символов, тут можете
        передать любые данные,
        которые помогут вам в будущем определить причину, почему задача выполнялась так долго.
        Например можно передать id обрабатываемых пользователей <code>"1,4,9"</code>,
        или просто количество обрабатываемых данных <code>"users count: 381"</code>.
        <br/>
        <strong>timestamp</strong> <em>(не обязательно)</em> - время unix timestamp, когда произошло данное событие.
        По-умолчанию используется текущее время, но вы можете передать своё значение.
    </p>

    <p>
        В случае успеха вы получите ответ: <code>HTTP/1.1 201 Created</code>.<br/>
        Если произошла ошибка валидации данных, будет ответ: <code>HTTP/1.1 400 Bad Request</code>,
        а в теле ответа будет json-массив с ошибками валидации.<br/>
        Если проект с указанным токеном не найден: <code>HTTP/1.1 404 Not Found</code>.
    </p>
</div>
