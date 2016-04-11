Yii2 pusher
===================

INSTALL
-------
php composer.phar require bariew/yii2-pusher

USAGE
_____

1. Add a new component into yor config file:
```
'components' => [
    'webpusher' => [
        'class' => 'bariew\yii2Pusher\Component',
        'app_id' => '***', // get your credentials on pusher.com
        'key' => '***',
        'secret' => '***',
    ]
]
```

2. Include a widget into your view file e.g. "main.php":
```
    <?= \bariew\yii2Pusher\Widget::widget(['events' => [
        'notification' => new \yii\web\JsExpression("function(data){console.log(data);}")
    ]]) ?>
```

3. Now you can send a notification to any user or an array of users:
```
Yii::$app->webpusher->userPush("hello user", [Yii::$app->user->id]);
```
.