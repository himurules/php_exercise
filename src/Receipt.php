<?php
namespace TDD;
class Receipt{
    public function total(array $cart){
        $total =  array_sum($cart);
        return $total;
    }

    public function calculateTax($taxPerc=null, $total = 0){
        $tax = 0;
        if(!is_null($taxPerc))
            $tax += $total * $taxPerc;

        return $tax;
    }
}