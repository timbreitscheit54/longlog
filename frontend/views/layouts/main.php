<?php

/* @var $this \yii\web\View */

/* @var $content string */

use common\widgets\LanguageDropdown;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
$baseUrl = Yii::$app->request->baseUrl;
$faviconsDir = $baseUrl . '/images/favicon';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
	<meta name="msapplication-square70x70logo" content="<?= $faviconsDir ?>/windows-tile-70x70.png">
	<meta name="msapplication-square150x150logo" content="<?= $faviconsDir ?>/windows-tile-150x150.png">
	<meta name="msapplication-square310x310logo" content="<?= $faviconsDir ?>/windows-tile-310x310.png">
	<meta name="msapplication-TileImage" content="<?= $faviconsDir ?>/windows-tile-144x144.png">
	<meta name="msapplication-TileColor" content="#89194D">
	<link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?= $faviconsDir ?>/apple-touch-icon-152x152-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="120x120" href="<?= $faviconsDir ?>/apple-touch-icon-120x120-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="76x76" href="<?= $faviconsDir ?>/apple-touch-icon-76x76-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="60x60" href="<?= $faviconsDir ?>/apple-touch-icon-60x60-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?= $faviconsDir ?>/apple-touch-icon-144x144-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?= $faviconsDir ?>/apple-touch-icon-114x114-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?= $faviconsDir ?>/apple-touch-icon-72x72-precomposed.png">
	<link rel="apple-touch-icon" sizes="57x57" href="<?= $faviconsDir ?>/apple-touch-icon.png">
	<link rel="icon" sizes="228x228" href="<?= $faviconsDir ?>/coast-icon-228x228.png">
	<link rel="shortcut icon" href="<?= $faviconsDir ?>/favicon.ico">
	<link rel="icon" type="image/png" sizes="64x64" href="<?= $faviconsDir ?>/favicon.png">
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => Yii::t('app', 'MENU_PROJECTS'), 'url' => Yii::$app->homeUrl],
        ['label' => Yii::t('app', 'MENU_ABOUT'), 'url' => ['/site/about']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => Yii::t('app', 'MENU_SIGNUP'), 'url' => ['/site/signup']];
        $menuItems[] = ['label' => Yii::t('app', 'MENU_LOGIN'), 'url' => ['/site/login']];
    } else {
        $menuItems[] = ['label' => Yii::t('app', 'MENU_DASHBOARD'), 'url' => ['/dashboard/default/index']];
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                Yii::t('app', 'MENU_LOGOUT_{name}', ['name' => Yii::$app->user->identity->fName]),
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>

        <div class="pull-right">
            <div class="dropdown dropup">
                <a href="#" data-toggle="dropdown" class="dropdown-toggle"><?= LanguageDropdown::label(Yii::$app->language) ?> <b class="caret"></b></a>
                <?= LanguageDropdown::widget(['options' => ['class' => ['widget' => 'dropdown-menu dropdown-menu-right']]]) ?>
            </div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
