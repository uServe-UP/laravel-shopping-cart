<?php

namespace sky2002\ShoppingCart\Tests;

use Illuminate\Support\Collection;
use sky2002\ShoppingCart\CartItem;
use sky2002\ShoppingCart\Facades\ShoppingCart;
use sky2002\ShoppingCart\ServiceProvider;

trait ShoppingCartTester
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Cart' => ShoppingCart::class,
        ];
    }

    /**
     * @param int    $id
     * @param string $name
     * @param int    $price
     * @param int    $quantity
     *
     * @return CartItem
     */
    protected function addItemToCart(
        $id = 1,
        $name = 'iPhone 7',
        $price = 100,
        $quantity = 10
    ) {
        return \Cart::add($id, $name, $price, $quantity);
    }

    /**
     * @param int $amount
     *
     * @return Collection
     */
    protected function addItemsToCart($amount = 5)
    {
        $items = new Collection();

        foreach (range(1, $amount) as $id) {
            $items->push($this->addItemToCart($id));
        }

        return $items;
    }
}
