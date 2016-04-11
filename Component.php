<?php
/**
 * Item class file.
 * @copyright (c) 2016, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace bariew\yii2Pusher;

/**
 * Description.
 *
 * Usage:
 *
 *
 * @author Pavel Bariev <bariew@yandex.ru>
 *
 */
class Component extends \yii\base\Component
{
    /** @var string pusher.com credentials */
    public $app_id, $key, $secret;

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var string
     */
    public $defaultEventName = 'notification';

    /** @var \Pusher */
    private $_pusher;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        foreach (['app_id', 'key', 'secret'] as $attribute) {
            if (!$this->$attribute) {
                throw new \Exception("Pusher $attribute is required");
            }
        }

        $this->_pusher = new \Pusher($this->key, $this->secret, $this->app_id, array_intersect_key(
            $this->options,
            ['scheme' => 'http', 'port' => 80, 'timeout' => 30, 'debug' => false] // these will be by default
        ));
    }

    /**
     * Trigger an event by providing event name and payload.
     * Optionally provide a socket ID to exclude a client (most likely the sender).
     *
     * @param array $channels An array of channel names to publish the event on.
     * @param string $event
     * @param mixed $data Event data
     * @param string $socket_id [optional]
     * @param bool $debug [optional]
     * @return bool|string
     */
    public function push($channels, $event, $data, $socket_id = null, $debug = false, $already_encoded = false )
    {
        return $this->_pusher->trigger($channels, $event, $data, $socket_id, $debug, $already_encoded);
    }

    /**
     * Simple push for a specific user
     * @param $data
     * @param null $user_id
     * @param null $event
     * @return bool|string
     */
    public function userPush($data, $user_id = null, $event = null)
    {
        $event = $event ? : $this->defaultEventName;
        return $this->push($this->getUserChannelName($user_id), $event, $data);
    }

    /**
     *	Fetch channel information for a specific channel.
     *
     * @param string $channel The name of the channel
     * @param array $params Additional parameters for the query e.g. $params = array( 'info' => 'connection_count' )
     *	@return object
     */
    public function get_channel_info($channel, $params = array() )
    {
        return $this->_pusher->get_channel_info($channel, $params);
    }

    /**
     * Fetch a list containing all channels
     *
     * @param array $params Additional parameters for the query e.g. $params = array( 'info' => 'connection_count' )
     *
     * @return array
     */
    public function get_channels($params = array())
    {
        return $this->_pusher->get_channels($params);
    }

    /**
     * User channel based on his ID
     * @param bool $ids
     * @return string
     */
    public function getUserChannelName($ids = false)
    {
        $ids = $ids ? : \Yii::$app->user->id;
        $result = [];
        foreach ((array) $ids as $id) {
            $result[] = 'user_' . sha1($this->secret . $id);
        }

        return is_array($ids) ? $result : end($result);
    }

    /**
     * @inheritdoc
     */
//    public function __call($method, $params)
//    {
//        //Override the normal Yii functionality checking the Keen SDK for a matching method
//        if (method_exists($this, $method)) {
//            return parent::__call($method, $params);
//        } else if (method_exists($this->_pusher, $method)) {
//            return call_user_func_array(array($this->_pusher, $method), $params);
//        }
//
//        throw new Exception("$method does not exist");
//    }
}