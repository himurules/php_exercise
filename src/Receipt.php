<?php
namespace TDD;
use \BadMethodCallException;

class Receipt{
    public function total(array $cart, $coupon = null){
        if(!is_null($coupon)){
            if($coupon > 100)
                throw new BadMethodCallException('Invalid coupon value!. Coupon can not be more than 100%');
        }
        $total =  array_sum($cart);
        if(!is_null($coupon))
            $total -= (($coupon/100)*$total);
        return $total;
    }

    public function calculateTax($taxPerc=null, $total = 0){
        $tax = 0;
        if(!is_null($taxPerc))
            $tax += $total * $taxPerc;

        return $tax;
    }

    public function postTaxTotal($items, $tax, $coupons){
        $subtotal = $this->total($items, $coupons);
        return $subtotal - $this->calculateTax($tax, $subtotal);
    }

    public function calculateCurrencyAmt($items){
        $total = $this->total($items);
        return round($total, 2);
    }
}