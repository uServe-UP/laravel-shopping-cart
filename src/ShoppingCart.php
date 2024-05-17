<?php

namespace Sky2002\ShoppingCart;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Sky2002\ShoppingCart\Coupons\Coupon;
use Sky2002\ShoppingCart\Repositories\ShoppingCartRepositoryInterface;

class ShoppingCart
{
    /**
     * Default instance name.
     */
    const DEFAULT_INSTANCE_NAME = 'default';

    /**
     * Current instance name.
     *
     * User can several instances of the cart. For example, regular shopping
     * cart, wishlist, etc.
     *
     * @var string
     */
    private $instanceName;

    /**
     * Repository for cart store.
     *
     * @var ShoppingCartRepositoryInterface
     */
    private $repo;

    /**
     * Shopping cart content.
     *
     * @var Collection
     */
    private $content;

    /**
     * Coupons.
     *
     * @var Collection
     */
    private $coupons;

    /**
     * Store Infomations.
     *
     * @var Collection
     */
    private $storeInfo;

    /**
     * Delivery Infomations.
     *
     * @var Collection
     */
    private $deliveryInfo;

    /**
     * params Infomations.
     *
     * @var Collection
     */
    private $params;

    /**
     * Tips Infomations.
     *
     * @var float
     */
    private $tips;
    /**
     * Fees Infomations.
     *
     * @var Collection
     */
    private $feesAmountList;

    /**
     * ShoppingCart constructor.
     *
     * @param ShoppingCartRepositoryInterface $repo
     */
    public function __construct(ShoppingCartRepositoryInterface $repo)
    {
        $this->repo = $repo;
        $this->instance(self::DEFAULT_INSTANCE_NAME);
        $this->content = new Collection();
        $this->coupons = new Collection();
        $this->storeInfo = new Collection();
        $this->deliveryInfo = new Collection();
        $this->params = new Collection();
        $this->feesAmountList = new Collection();
        $this->tips = 0;
    }

    /**
     * Add an item to the shopping cart.
     *
     * If an item is already in the shopping cart then we simply update its
     * quantity.
     *
     * @param string|int $id
     * @param string $name
     * @param int|float $price
     * @param int|float $tax
     * @param int|float $total
     * @param int $quantity
     * @param array $options
     *
     * @return CartItem
     */
    public function add($id, $name, $price, $quantity, $tax, $total, $options = [])
    {
        $cartItem = new CartItem($id, $name, $price, $quantity, $tax, $total, $options);
        $uniqueId = $cartItem->getUniqueId();

        if ($this->content->has($uniqueId)) {
            $cartItem->quantity += $this->content->get($uniqueId)->quantity;
            $cartItem->total += $this->content->get($uniqueId)->total;
            $cartItem->tax += $this->content->get($uniqueId)->tax;
        }

        $this->content->put($uniqueId, $cartItem);

        return $cartItem;
    }

    /**
     * Remove the item with the specified unique id from shopping cart.
     *
     * @param string|int $uniqueId
     *
     * @return bool
     */
    public function remove($uniqueId)
    {
        if ($cartItem = $this->get($uniqueId)) {
            $this->content->pull($cartItem->getUniqueId());

            return true;
        }

        return false;
    }

    /**
     * Check if an item with specified unique id is in shopping cart.
     *
     * @param string|int $uniqueId
     *
     * @return bool
     */
    public function has($uniqueId)
    {
        return $this->content->has($uniqueId);
    }

    /**
     * Get the item with the specified unique id from shopping cart.
     *
     * @param string|int $uniqueId
     *
     * @return CartItem|null
     */
    public function get($uniqueId)
    {
        return $this->content->get($uniqueId);
    }

    /**
     * Get shopping cart content.
     *
     * @return Collection
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * Get shopping cart content.
     *
     * @return Collection
     */
    public function storeInfo()
    {
        return $this->storeInfo;
    }

    /**
     * Get the store infomations.
     * @param $storeInfo
     */
    public function setStoreInfo($storeInfo)
    {
        $this->storeInfo = $storeInfo;
    }

    /**
     * Get delivery content.
     *
     * @return Collection
     */
    public function getDeliveryInfo()
    {
        return $this->deliveryInfo;
    }

    /**
     * Set the store infomations.
     * @param $deliveryInfo
     */
    public function setDeliveryInfo($deliveryInfo)
    {
        $this->deliveryInfo = $deliveryInfo;
    }

    /**
     * Get delivery content.
     *
     * @return Collection
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set the store infomations.
     * @param $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     *
     * Set the tips infomations.
     * @param $tips
     */
    public function setTips($tips)
    {
        $this->tips = $tips;
    }

    /**
     *
     * Set the fees infomations.
     * @param array $feesList
     */
    public function setFeesAmountList($feesList)
    {
        $this->feesAmountList = $feesList;
    }

    /**
     * Get the quantity of the cart item with specified unique id.
     *
     * @param $uniqueId
     * @param $quantity
     *
     * @return bool
     */
    public function setQuantity($uniqueId, $quantity)
    {
        if ($cartItem = $this->get($uniqueId)) {
            $cartItem->quantity = $quantity;

            $this->content->put($cartItem->getUniqueId(), $cartItem);

            return true;
        }

        return false;
    }

    /**
     * Clear shopping cart.
     */
    public function clear()
    {
        $this->content = new Collection();
        $this->coupons = new Collection();
        $this->storeInfo = new Collection();
        $this->deliveryInfo = new Collection();
        $this->params = new Collection();
        $this->feesAmountList = new Collection();
        $this->tips = 0;
    }

    /**
     * Clear shopping cart.
     */
    public function clearCoupons()
    {
        $this->coupons = new Collection();
    }

    /**
     * Get the number of item in the shopping cart.
     *
     * @return int
     */
    public function count()
    {
        return $this->content->count();
    }

    /**
     * Get total price without coupons.
     *
     * @return float
     */
    public function getSubtotal()
    {
        return $this->content->sum(function (CartItem $cartItem) {
            return $cartItem->getItemWithOptionTotal();
        });
    }

    /**
     * Get total tax.
     *
     * @return float
     */
    public function getTotalTax()
    {
        return $this->content->sum(function (CartItem $cartItem) {
            return $cartItem->getTax();
        });
    }

    /**
     * Get total coupons amount.
     *
     * @return float
     */
    public function getCouponsAmount()
    {
        $totalCoupons = 0;

        $this->coupons->each(function (Coupon $coupon) use (&$totalCoupons) {
            $subtotal = $this->getSubtotal();
            $tax = $this->getTotalTax();
            $feesList = $this->getFeesAmountList();
            $range = $coupon->getRange();
            $total = $subtotal + $tax;

            foreach ($feesList as $key => $value) {
                if ((is_bool($range) && $range == true) || (is_array($range) && Arr::exists($range, $key)))
                    $total += $value;
            }

            /**
             * @var Coupon $coupon
             */
            $totalCoupons += $coupon->apply($total);
        });

        return $totalCoupons;
    }

    /**
     * Get total price with coupons and tax.
     *
     * @return float
     */
    public function getSubtotalWithTax()
    {
        $subtotal = $this->getSubtotal();
        $tax = $this->getTotalTax();

        $total = $subtotal + $tax;

        return $total;
    }

    /**
     * Get tips.
     *
     * @return float
     */
    public function getTips()
    {
        return $this->tips;
    }

    /**
     * Get fees.
     *
     * @return float
     */
    public function getFeesAmountList()
    {
        return $this->feesAmountList;
    }

    /**
     * Get fees.
     *
     * @return float
     */
    public function getFeesAmount()
    {
        $amount = 0;
        foreach ($this->feesAmountList as $value) {
            if (is_numeric($value))
                $amount += $value;
        }

        return $amount;
    }

    /**
     * Get total price with coupons and tax and ohter fees.
     *
     * @return float
     */
    public function getAmount()
    {
        $subtotal = $this->getSubtotalWithTax();
        $feesAmount = $this->getFeesAmount();
        $couponTotal = $this->getCouponsAmount();
        $tips = $this->getTips();

        $totalWithCoupons = $subtotal + $feesAmount + $tips - $couponTotal;

        $total = $totalWithCoupons >= 0 ? $totalWithCoupons : 0;

        return $total;
    }

    /**
     * Add coupon.
     *
     * @param Coupon $coupon
     */
    public function addCoupon(Coupon $coupon)
    {
        $this->coupons->push($coupon);
    }

    /**
     * Add coupon.
     *
     * @param Coupon $coupon
     */
    public function removeCoupon(Coupon $coupon)
    {
        $key = $this->coupons->search(function ($couponList) use ($coupon) {
            return $couponList->name == $couponList->name;
        });
        return $this->coupons->pull($key);
    }

    /**
     * Get coupons.
     *
     * @return Collection
     */
    public function coupons()
    {
        return $this->coupons;
    }

    /**
     * Check is Coupon exist.
     *
     * @param Coupon $coupon
     *
     * @return bool
     */
    public function hasCoupon(Coupon $coupon)
    {
        return $this->coupons->contains($coupon);
    }

    /**
     * Set shopping cart instance name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function instance($name)
    {
        $name = $name ?: self::DEFAULT_INSTANCE_NAME;
        $name = str_replace('shopping-cart.', '', $name);

        $this->instanceName = sprintf('%s.%s', 'shopping-cart', $name);

        return $this;
    }

    /**
     * Get current shopping cart instance name.
     *
     * @return string
     */
    public function currentInstance()
    {
        return $this->instanceName;
    }

    /**
     * @param $id
     * @param int $expireTime
     * @return $this
     */
    public function setCartExpireTime($id, $expireTime = 604800)
    {
        $this->repo->setExpireTime(
            $id,
            $this->instanceName,
            $expireTime
        );

        return $this;
    }

    /**
     * @param $name
     * @param $newName
     * @return $this
     */
    public function renameCart($name, $newName)
    {
        $this->repo->renameCart(
            $name,
            $newName,
            $this->instanceName
        );

        return $this;
    }

    /**
     * Store the current instance of the cart.
     *
     * @param $id
     *
     * @return $this
     */
    public function store($id)
    {
        $this->repo->createOrUpdate(
            $id,
            $this->instanceName,
            json_encode(serialize([
                'content' => $this->content,
                'coupons' => $this->coupons,
                'store-info' => $this->storeInfo,
                'delivery-info' => $this->deliveryInfo,
                'fees-amount-list' => $this->feesAmountList,
                'params' => $this->params,
                'tips' => $this->tips,

            ]))
        );

        return $this;
    }

    /**
     * Store the specified instance of the cart.
     *
     * @param $id
     *
     * @return $this
     */
    public function restore($id)
    {
        $cart = $this->repo->findByIdAndInstanceName($id, $this->instanceName);

        if ($cart === null) {
            return;
        }

        $unserialized = unserialize(json_decode($cart->content));

        $this->content = $unserialized['content'];
        $this->coupons = $unserialized['coupons'];
        $this->storeInfo = $unserialized['store-info'];
        $this->deliveryInfo = $unserialized['delivery-info'];
        $this->feesAmountList = $unserialized['fees-amount-list'];
        $this->params = $unserialized['params'];
        $this->tips = $unserialized['tips'];

        $this->instance($cart->instance);

        return $this;
    }

    /**
     * Delete current shopping cart instance from storage.
     *
     * @param $id
     */
    public function destroy($id)
    {
        $this->repo->remove($id, $this->instanceName);
    }

    /**
     * Get orders from redis
     *
     * @param $key
     * @param int $expire
     */
    public function getOrders($key, $expire = 2678400)
    {
        return $this->repo->getOrders($key, $expire);
    }

    /**
     * Get orders from redis
     *
     * @param $key
     * @param int $expire
     */
    public function getAndKeepOrders($key, $expire = 2678400)
    {
        return $this->repo->getAndKeepOrders($key, $expire);
    }

    /**
     * Delete a specified number of orders from Redis while keeping the rest
     *
     * @param string $key
     * @param int $deleteLength Number of orders to delete
     * @param int $expire Expiry time for the key in seconds
     * @return void
     */
    public function deleteOrders($key, $deleteLength = 1, $expire = 2678400)
    {
        return $this->repo->deleteOrders($key, $deleteLength, $expire);
    }
}
