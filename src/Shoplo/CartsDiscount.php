<?php

namespace Shoplo;

class CartsDiscount extends Resource
{
    public function modify($id, $fields)
    {
        $fields = array('cart_discount' => $fields);

        return $this->send("carts_discount/".$id, 'PUT', $fields);
    }

    public function remove($id)
    {
        return $this->send("carts_discount/".$id, 'DELETE');
    }
}