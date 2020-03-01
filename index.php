<?php

/**
 * Created by Visual Studio Code.
 * User: Harpreet
 * Date: 2020-02-28
 * Time: 20:20
 */

use CBC\Invoice as Invoice;
require __DIR__ . '/vendor/autoload.php';


 $invoice = new Invoice();

    $invoice->addItem("Pantry Shelves", 1004.56, Invoice::GST);

    $invoice->addItem("Hinges", 24.56, Invoice::NONE);

    $invoice->addItem("Item 1", 100.00, Invoice::GST);

    $invoice->addItem("Item 2", 23.45, Invoice::GST);

    $invoice->addItem("Door handles", 12.45, Invoice::GST);

// HTML table for accessing invoice items
// Task 10
echo "<table border='1'>";
echo "<tr>
<th>Name</th>
<th>Item Price</th>
<th>Tax</th>

<th>Total Price</th>
</tr>";

// Task 9
foreach($invoice->getitem() as $singleInvoice){
    echo "<tr>";
    echo "<td>".$singleInvoice['description']."</td>";
    echo "<td>".$singleInvoice['value']."</td>";
    $tax = 0;
    if($singleInvoice["tax"]){
        $taxpercent = 10;
        $tax = bcdiv($singleInvoice['value'], $taxpercent, 2);
        echo "<td>".$tax."</td>";
    }
    else{
        echo "<td>".$tax."</td>";
    }
    //echo "<td>".$singleInvoice['tax']."%</td>";
   
    echo "<td>".($singleInvoice['value'] + $tax)."</td>";
    echo "</tr>";

    //echo gettype($singleInvoice);
    
}
echo "</table>";
