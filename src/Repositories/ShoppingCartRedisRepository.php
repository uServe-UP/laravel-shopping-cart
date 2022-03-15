<?php

namespace Sky2002\ShoppingCart\Repositories;

use Illuminate\Support\Facades\Redis;
use stdClass;

class ShoppingCartRedisRepository implements ShoppingCartRepositoryInterface
{
    public function __construct()
    {
        Redis::select($this->getDatabase());
    }

    /**
     * Save shopping cart.
     *
     * @param $id
     * @param $instanceName
     * @param $content
     */
    public function createOrUpdate($id, $instanceName, $content)
    {
        Redis::set($this->getKey($id, $instanceName), $content);
    }

    /**
     * Find shopping cart by its identifier and instance name.
     *
     * @param string $id
     * @param string $instanceName
     *
     * @return stdClass|null
     */
    public function findByIdAndInstanceName($id, $instanceName)
    {
        $content = Redis::get($this->getKey($id, $instanceName));

        if ($content === null) {
            return;
        }

        return (object)[
            'id' => $id,
            'instance' => $instanceName,
            'content' => $content,
        ];
    }

    /**
     * Remove shopping cart by its identifier and instance name.
     *
     * @param string $id
     * @param string $instanceName
     */
    public function remove($id, $instanceName)
    {
        Redis::del($this->getKey($id, $instanceName));
    }

    /**
     * Remove shopping cart by its identifier and instance name.
     *
     * @param $id
     * @param $instanceName
     * @param int $expireTime
     * @return mixed|void
     */
    public function setExpireTime($id, $instanceName, $expireTime)
    {
        Redis::expire($this->getKey($id, $instanceName), $expireTime);
    }

    /**
     * Rename Shopping cart
     *
     * @param $name
     * @param $newName
     * @return mixed|void
     */
    public function renameCart($name, $newName, $instanceName)
    {
        $key = $this->getKey($name, $instanceName);
        if (Redis::exists($key)) {
            $newKey = $this->getKey($newName, $instanceName);
            Redis::rename($key, $newKey);
        }
    }

    /**
     * Get orders from redis
     *
     * @param $key
     * @param int $expire
     */
    public function getOrders($key, $expire = 2678400)
    {
        $result = [];
        $orderLength = Redis::llen($key);
        if ($orderLength > 0) {
            $len = $orderLength - 1;
            $response = Redis::lrange($key, 0, $len);
            foreach ($response as $item) {
                $result[] = json_decode($item, true);
            }
            Redis::ltrim($key, $orderLength, -1);
            Redis::expire($key, $expire);
        }

        return $result;
    }

    /**
     * Get the key to store shopping cart.
     *
     * @param $id
     * @param $instanceName
     *
     * @return string
     */
    protected function getKey($id, $instanceName)
    {
        return sprintf('%s:%s.%s', $this->getTableName(), $id, $instanceName);
    }

    /**
     * Get the database table name.
     *
     * @return string
     */
    private function getTableName()
    {
        return config('shopping-cart.redis.table', 'shopping_cart');
    }

    /**
     * Get the database.
     *
     * @return string
     */
    private function getDatabase()
    {
        return config('shopping-cart.redis.database', 0);
    }
}
