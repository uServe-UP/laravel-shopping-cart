<?php

namespace Sky2002\ShoppingCart\Coupons;

class FixedDiscountCoupon extends Coupon
{
    /**
     * @var float
     */
    private $discount;
    /**
     * @var array
     */
    private $range;

    /**
     * PercentCoupon constructor.
     *
     * @param string $name
     * @param float $discount
     * @param array $range
     */
    public function __construct($name, $discount, $range)
    {
        parent::__construct($name);

        $this->discount = $discount;
        $this->range = $range;
    }

    /**
     * @return array
     */
    public function getRange(){
        return $this->range;
    }

    /**
     * Apply coupon to total price.
     *
     * @param $total
     *
     * @return float Discount.
     */
    public function apply($total)
    {
        return $this->discount;
    }
}
