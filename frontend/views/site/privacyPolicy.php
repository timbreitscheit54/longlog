<?php
/* @var $this \yii\web\View */

use yii\helpers\Html;

$this->title = 'Privacy Policy';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="privacy">
    <h1>Privacy Policy</h1>

    <h3>General information</h3>
    <ol>
        <li>
            The term "Service" hereinafter implies web-site <?= Yii::$app->request->hostInfo ?> and all its subdomains.
        </li>
        <li>
            The term "Components" hereinafter includes related materials, such as:<br/>
            Mobile app,<br/>
            Tools for integration.
        </li>
        <li>
            This Service and all its Components are distributed free of charge under the
            <a href="https://www.gnu.org/licenses/gpl-3.0.html">GNU GPLv3</a> license.
        </li>
    </ol>

    <h3>Terms of Use</h3>
    <ol>
        <li>
            This service and all its components are distributed according to the "As is" principle and the developer
            does not bear any responsibility related to the operation of this service, its components
            or other technologies used in the project.
        </li>
        <li>The developer can not guarantee the safety of information and uninterrupted access to it.</li>
        <li>You use this service and its components at your own risk.</li>
    </ol>

    <h3>Collected data and permissions</h3>
    <ol>
        <li>Email.</li>
        <li>A mobile application requires an Internet access authorization.</li>
    </ol>

    <h3>Objectives for collecting and processing user data</h3>
    <ol>
        <li>
            Your email address is used only for registration / authorization in the service,
            as well as for sending system notifications about the operation of the service
            (for example, "Detected exceeding the time duration").
        </li>
        <li>
            Mobile app requires internet access to send authorization requests and receive data from the service.
        </li>
        <li>
            Your personal data will never be transferred to a third party, but the developer
            is not responsible for unauthorized access to them.
        </li>
    </ol>

    <h3>Measures to protect collected data</h3>
    <ol>
        <li>
            Your email address will never be published on the site in public access.<br/>
            To view your email-address allow access only authorized administrators and
            authorized projects participants in which you are a member.
        </li>
        <li>Your account password stored in an encrypted form without possibility for reverse decryption.</li>
    </ol>

    <h3>Contacts</h3>

    Email: <?= \demi\safeText\Widget::widget([
        'url' => 'mailto:ivan@orlov.io',
        'text' => 'ivan@orlov.io',
    ]) ?>
</div>
