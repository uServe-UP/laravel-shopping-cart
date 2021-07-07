<?php

namespace sky2002\ShoppingCart\Tests;

use sky2002\ShoppingCart\CartItem;
use Orchestra\Testbench\TestCase;

class CartItemTest extends TestCase
{
    public function testTotal()
    {
        $cartItem = new CartItem(1, 'iPhone', 100, 10);

        $this->assertEquals($cartItem->getTotal(), 1000);
    }
}
