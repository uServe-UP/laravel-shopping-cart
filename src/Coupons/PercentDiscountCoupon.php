<?php

namespace Sky2002\ShoppingCart\Coupons;

class PercentDiscountCoupon extends Coupon
{
    /**
     * @var float
     */
    private $percent;
    /**
     * @var array
     */
    private $range;

    /**
     * PercentCoupon constructor.
     *
     * @param int $id
     * @param string $name
     * @param float  $discount
     * @param array $range
     */
    public function __construct($id, $name, $discount, $range)
    {
        parent::__construct($id, $name);

        $this->percent = $discount;
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
        return $total * $this->percent;
    }
}
