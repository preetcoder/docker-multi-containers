<?php

/**
 * Created by Visual Studio Code.
 * User: Harpreet
 * Date: 2020-02-28
 * Time: 20:19
 */

namespace CBC;

/**
 * Class Invoice
 * This class controls all the invoice operations from adding new items to getting total of invoice.
 */

class Invoice {
    const GST = 10;
    const NONE = 0;

    private $items = [];

    // public function __construct(){
    //     die("hehhh");
    // }
 /**
     * @param $description - the description of the invoice
     * @param $value - price value of the item
     * @param $tax - tax applied on invoice for item
     *
     * This method add new item to items property of this class
     */
    public function addItem($description, $value, $tax)
    {
        $this->items[] = [
            "description" => $description,
            "value" => $value,
            //"tax" => self::NONE,
            "tax" => $tax,
        ];
    }

    /**
     *
     * @return - array of item subtotal, tax and total price
     * 
     * This method return the price array of all the items present in the instance.
     */
    public function getTotals()
    {
        // @ Task 6
        $subtotal = 0;
        $tax = 0;

        foreach($this->items as $item){
           
            // get data from array
            $itemPrice = $item["value"];
            $subtotal += $itemPrice;
            $taxpercent = 0;
            // either 0 or 10
            if($item["tax"]){
                $taxpercent = 10;
                $tax += bcdiv($itemPrice, $taxpercent, 2);
            }

        }
        // $subtotal = $this->items[0]["value"];
        // $taxpercent = 0;
        // $tax = 0;
        // // either 0 or 10%
        // if($this->items[0]["tax"]){
        //     $taxpercent = 10;
        //     $tax = bcdiv($subtotal, $taxpercent, 2);
        // }


        // return price array
        return [
            "subtotal" => $subtotal,
            "tax" => $tax,
            "total" => $subtotal + $tax,
        ];
    }
    /**
     *
     * @return - array of items
     * 
     * This method return the array of all the items present in the instance like a getter.
     */
    public function getitem()
    {
        return $this->items;
    }

}