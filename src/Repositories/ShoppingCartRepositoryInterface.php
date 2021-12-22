<?php

namespace Sky2002\ShoppingCart\Repositories;

use stdClass;

interface ShoppingCartRepositoryInterface
{
    /**
     * Save shopping cart.
     *
     * @param $id
     * @param $instanceName
     * @param $content
     */
    public function createOrUpdate($id, $instanceName, $content);

    /**
     * Find shopping cart by its identifier and instance name.
     *
     * @param string $id
     * @param string $instanceName
     *
     * @return stdClass|null
     */
    public function findByIdAndInstanceName($id, $instanceName);

    /**
     * Remove shopping cart by its identifier and instance name.
     *
     * @param string $id
     * @param string $instanceName
     */
    public function remove($id, $instanceName);

    /**
     * Set Expired time By seconds
     *
     * @param int $expireTime
     * @return mixed
     */
    public function setExpireTime($id, $instanceName, $expireTime);

    /**
     * Rename Cart
     *
     *
     * @param $name
     * @param $newName
     * @return mixed
     */
    public function renameCart($name,$newName);
}
