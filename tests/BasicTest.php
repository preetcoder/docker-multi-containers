<?php
/**
* Created by Visual Studio Code.
* User: Harpreet
* Date: 2020-02-28
* Time: 20:34
*/

use \CBC\Invoice;
use \CBC\Tax;

class BasicTest extends PHPUnit\Framework\TestCase {

/**
     * This test tests if price of invoice with single item can be successfully retrieved
     *
     * PRE-CONDITIONS:
     * - an item with the name 'Item 1', value '123.45' and tax type GST exists
     *
     * ACTIONS:
     * - call getTotals() to check the price 
     *
     * POST-CONDITIONS:
     * - an array of invoice subtotal, tax and total price retreived
     */
    public function testSingleItem()
    {
        $invoice = new Invoice();

        $invoice->addItem("Item 1", 123.45, Invoice::GST);

        self::assertEquals([
            "subtotal" => 123.45,
            "tax" => 12.34,
            "total" => 135.79
        ], $invoice->getTotals());
    }

    // Task 8

/**
     * This test tests if price of invoice with tax free single item can be successfully retrieved
     *
     * PRE-CONDITIONS:
     * - an item with the name 'Item 1', value '123.45' and tax type NONE exists
     *
     * ACTIONS:
     * - call getTotals() to check the price 
     *
     * POST-CONDITIONS:
     * - an array of invoice subtotal, tax and total price retreived
     */
    public function testTaxFree()
    {
        $invoice = new Invoice();

        $invoice->addItem("Item 1", 123.45, Invoice::NONE);

        self::assertEquals([
            "subtotal" => 123.45,
            "tax" => 0,
            "total" => 123.45
        ], $invoice->getTotals());
    }
/**
     * This test tests if price of invoice with multiple items can be successfully retrieved
     *
     * PRE-CONDITIONS:
     * - item 1 : value '100.00', Tax 'GST'
     * - item 2 : value '23.45', tax 'None'
     *
     * ACTIONS:
     * - call getTotals() to check the price 
     *
     * POST-CONDITIONS:
     * - an array of invoice subtotal, tax and total price retreived
     */
    public function testTwoItems()
    {
        $invoice = new Invoice();

        $invoice->addItem("Item 1", 100.00, Invoice::GST);

        // Updating this item from GST to None and then checking test @ Task 5
        $invoice->addItem("Item 2", 23.45, Invoice::NONE);

        self::assertEquals([
            "subtotal" => 123.45,
            //"tax" => 12.34,         // value changed from beginning case to new test changes
            "tax" => 10.00,
            "total" => 133.45
        ], $invoice->getTotals());
    }
}