Long Log
===============================

LongLog web-application.

Installation
---
Based on [Yii2 advanced template](https://github.com/yiisoft/yii2-app-advanced/blob/master/docs/guide/README.md)
<br>
```bash
composer create-project --prefer-dist longlog/web longlog
```

### Vagrant installation
```bash
cp longlog/vagrant/config/vagrant-local.example.yml longlog/vagrant/config/vagrant-local.yml
```
Generate github token here [https://github.com/settings/tokens/new](https://github.com/settings/tokens/new?scopes=repo&description=LongLog%20composer%20token)
<br>
Paste token to file: `longlog/vagrant/config/vagrant-local.yml`
```bash
vagrant up
```
If you need [Grunt](https://gruntjs.com) support:
```bash
vagrant ssh
cd /app
./install-grunt.sh
source ~/.profile

# Run all grunt tasks (sass and favicons)
grunt
# Process scss to css
grunt sass
# Generate favicons
grunt favicons

# Watching scss file changes and run "sass" task.
# Try to edit files in /frontend/resourses/scss/*.scss and 
# you changes immediately applied to /frontend/web/css/style.css
grunt watch
```

Complete!<br>
Frontend URL: http://longlog.dev<br>
Backend URL: http://admin.longlog.dev<br>
Api URL: http://api.longlog.dev<br>

# Configurations
---
1. [Register](https://www.google.com/recaptcha/admin) new reCAPTCHA API keys and replace it in `/common/config/params.php`
2. Change other params in file `/common/config/params.php`, for example set: `'user.sendActivationEmail' => true`
3. PhpStorm: Mark the file `/vendor/yiisoft/yii2/Yii.php` as plain text (right-click "Mark as Plain Text")
<br>

Helpful commands:
```bash
# init RBAC roles
php yii rbac/init

# Extract message translations
php yii message @console/config/translation.php
```

crontab:
```bash
# @midnight: Run Garbage Collector
0 0 * * * /usr/bin/php -q /app/yii garbage-collector

# @midnight: yesterday full stats
0 0 * * * /usr/bin/php -q /app/yii stat/daily
# every hour in 30 minutes: totay stats
30 * * * * /usr/bin/php -q /app/yii stat/today
```
