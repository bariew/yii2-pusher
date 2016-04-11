<?php
/**
 * Item class file.
 * @copyright (c) 2016, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace bariew\yii2Pusher;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use Yii;

/**
 * Description.
 *
 * Usage:
 * @author Pavel Bariev <bariew@yandex.ru>
 *
 */
class Widget extends \yii\base\Widget
{
    /**
     * JS events array with pusher event names as keys
     * @example ['notification' => new \yii\web\JsExpression("function(data){console.log(data);}")]
     * @var array
     */
    public $events = [];

    /**
     * @inheritdoc
     */
    public function run()
    {
        /** @var Component $pusher */
        if (!$pusher = $this->getComponentByClass(Component::className())){
            return;
        }
        Yii::$app->view->registerJsFile('http://js.pusher.com/2.2/pusher.min.js');
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
        return Html::tag('div', $channel, ['id' => 'pusher-channel', 'class' => 'hide']);
    }

    /**
     * Gets application component
     * @param $class
     * @return \yii\base\Component|null
     */
    private function getComponentByClass($class)
    {
        foreach (\Yii::$app->getComponents() as $name => $config) {
            $componentClass = is_array($config) ? @$config['class'] : $config;
            if ($componentClass == $class) {
                return Yii::$app->$name;
            }
        }
        return null;
    }
}