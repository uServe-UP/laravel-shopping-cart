<?php

namespace Sky2002\ShoppingCart\Repositories;

use Illuminate\Support\Facades\Redis;
use stdClass;

class ShoppingCartRedisRepository implements ShoppingCartRepositoryInterface
{
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
        if (!Redis::exists($key)) {
            $newKey = $this->getKey($newName, $instanceName);
            Redis::rename($key, $newKey);
        }
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
        return sprintf('%s.%s', $id, $instanceName);
    }
}
