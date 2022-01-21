<?php

namespace Sky2002\ShoppingCart\Coupons;

abstract class Coupon
{
    public $id;
    public $name;

    /**
     * Coupon constructor.
     *
     * @param int $id
     * @param string $name
     */
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Apply coupon to total price.
     *
     * @param $total
     *
     * @return float Discount.
     */
    abstract public function apply($total);
}
