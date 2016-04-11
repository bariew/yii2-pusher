<?php
/**
 * Created by PhpStorm.
 * User: pt
 * Date: 11.04.16
 * Time: 12:21
 */

namespace bariew\yii2Pusher;


use yii\helpers\Json;
use yii\web\View;
use Yii;
class Widget extends \yii\base\Widget
{
    public $events = [];

    public function run()
    {
        if (!$name = $this->getComponentNameByClass(Component::className())){
            return;
        }
        Yii::$app->view->registerJsFile('http://js.pusher.com/2.2/pusher.min.js');
        /** @var Component $pusher */
        $pusher = \Yii::$app->$name;
        $channel = $pusher->getUserChannelName();
        $events = Json::encode($this->events);
        \Yii::$app->view->registerJs(
<<<JS
    var pusher = new Pusher("{$pusher->key}");
    var channel = pusher.subscribe("{$channel}");
    var events = {$events};
    for (var name in events) {
        channel.bind(name, events[name]);
    }
JS
    , View::POS_READY);
    }

    private function getComponentNameByClass($class)
    {

        foreach (\Yii::$app->getComponents() as $name => $config) {
            $componentClass = is_array($config) ? @$config['class'] : $config;
            if ($componentClass == $class) {
                return $name;
            }
        }
        return null;
    }
}