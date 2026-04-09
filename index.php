<?php

/**
 * Created by Visual Studio Code.
 * User: Harpreet
 * Date: 2020-02-28
 * Time: 20:20
 */

use App\Order\{OrderItem, Customer};

require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
require __DIR__ . '/config/db.php';

$message = '';
$pdo->exec("
    CREATE TABLE IF NOT EXISTS customer (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        address TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        notes text NOT NULL,
        type int NOT NULL,
        imei VARCHAR(255) NULL,
        device_type int NULL,
        price DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_orders_customer
            FOREIGN KEY (customer_id) REFERENCES customer(id)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    )
");


if($_REQUEST["submitBtn"] == 1) {
    $customer = new Customer();
    
    $customer->setName($_REQUEST["customerName"]);
    $customer->setMobile($_REQUEST["customerPhone"]);
    $customer->setAddress($_REQUEST["customerAddress"]);


    $orderItem = new OrderItem();
    $orderItem->orderName = $_REQUEST["itemName"];
    $orderItem->orderPrice = $_REQUEST["itemPrice"];
    $orderItem->orderNotes = $_REQUEST["itemNotes"];
    $orderItem->orderType = $_REQUEST["itemType"] == "repair" ? 1 : 2;
    if ($orderItem->orderType == 1) {
        $orderItem->imei = $_REQUEST["itemImei"];
        $orderItem->deviceType = $_REQUEST["itemDeviceType"] == "phone" ? 1 : 2;
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO customer (customer_name, phone, address)
            VALUES (:customer_name, :phone, :address)
        ");
        $stmt->execute([
            ':customer_name' => $customer->getName(),
            ':phone' => $customer->getMobile(),
            ':address' => $customer->getAddress()
        ]);

        $customerId = (int)$pdo->lastInsertId();
        $customer->setId($customerId);
        $orderItem->customerId = $customerId;

        $stmt = $pdo->prepare("
            INSERT INTO orders (customer_id, name, price, notes, type, imei, device_type)
            VALUES (:customer_id, :order_name, :order_price, :order_notes, :order_type, :order_imei, :order_dev_type)
        ");
        $stmt->execute([
            ':customer_id' => $orderItem->customerId,
            ':order_name' => $orderItem->orderName,
            ':order_price' => $orderItem->orderPrice,
            ':order_notes' => $orderItem->orderNotes,
            ':order_type' => $orderItem->orderType,
            ':order_imei' => $orderItem->imei,
            ':order_dev_type' => $orderItem->deviceType
        ]);

        $message = "Data saved successfully.";
    } catch (PDOException $e) {
        $message = "Save failed: " . $e->getMessage();
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Order Form</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background:#f5f5f5;
      padding:20px;
    }

    .form-container {
      max-width: 800px;
      margin:0 auto;
      background:#fff;
      padding:20px;
      border-radius:8px;
      box-shadow:0 2px 8px rgba(0,0,0,0.1);
    }

    h2 {
      margin-top:0;
      border-bottom:1px solid #ddd;
      padding-bottom:6px;
    }

    .field-group {
      margin-bottom:12px;
    }

    label {
      display:block;
      font-weight:bold;
      margin-bottom:4px;
    }

    input[type="text"],
    input[type="tel"],
    input[type="number"],
    textarea,
    select {
      width:100%;
      padding:8px;
      border:1px solid #ccc;
      border-radius:4px;
      box-sizing:border-box;
    }

    textarea {
      resize: vertical;
      min-height:60px;
    }

    .items-header {
      display:flex;
      justify-content:space-between;
      align-items:center;
      margin-top:16px;
      margin-bottom:8px;
    }

    .order-item {
      border:1px solid #ddd;
      padding:12px;
      border-radius:6px;
      margin-bottom:10px;
      background:#fafafa;
      position:relative;
    }

    .remove-item-btn {
      position:absolute;
      top:8px;
      right:8px;
      background:#e74c3c;
      color:#fff;
      border:none;
      padding:4px 8px;
      border-radius:4px;
      cursor:pointer;
      font-size:12px;
    }

    .add-item-btn,
    #submitBtn {
      background:#007bff;
      color:#fff;
      border:none;
      padding:8px 14px;
      border-radius:4px;
      cursor:pointer;
    }

    .add-item-btn {
      background:#28a745;
    }

    .status {
      margin-top:10px;
      font-size:14px;
    }

    .status.error {
      color:#e74c3c;
    }

    .status.success {
      color:#2ecc71;
    }

    .imei-device-group {
      display:none;
      margin-top:8px;
      padding:8px;
      border-radius:4px;
      background:#f0f8ff;
    }

    .required-star {
      color:#e74c3c;
      margin-left:2px;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif ?>
    <h2>Create Order</h2>

    <form id="orderForm" method="post">
      <!-- Customer data -->
      <h3>Customer Details</h3>

      <div class="field-group">
        <label for="customerName">Name<span class="required-star">*</span></label>
        <input type="text" id="customerName" name="customerName" required />
      </div>

      <div class="field-group">
        <label for="customerPhone">Phone<span class="required-star">*</span></label>
        <input type="tel" id="customerPhone" name="customerPhone" required />
      </div>

      <div class="field-group">
        <label for="customerAddress">Address</label>
        <textarea id="customerAddress" name="customerAddress"></textarea>
      </div>

      <!-- Order data -->
      <h3>Order Details</h3>

      <!-- <div class="field-group">
        <label for="orderNotes">Additional Notes</label>
        <textarea id="orderNotes" name="orderNotes"></textarea>
      </div>

      <div class="field-group">
        <label for="orderDescription">Description</label>
        <textarea id="orderDescription" name="orderDescription"></textarea>
      </div> -->

      <!-- Order items -->
      <!-- <div class="items-header">
        <h3>Order Items</h3>
        <button type="button" class="add-item-btn" id="addItemBtn">+ Add Item</button>
      </div> -->

      <div id="itemsContainer"></div>

      <button type="submit" id="submitBtn" name="submitBtn" value="1">Submit Order</button>
      <div id="statusMsg" class="status"></div>
    </form>
  </div>

  <script>
    const itemsContainer = document.getElementById('itemsContainer');
    const addItemBtn = document.getElementById('addItemBtn');
    const orderForm = document.getElementById('orderForm');
    const statusMsg = document.getElementById('statusMsg');

    // Add initial item
    addOrderItem();

    addItemBtn.addEventListener('click', () => {
      addOrderItem();
    });

    function addOrderItem() {
      const index = Date.now(); // simple unique ID
      const itemDiv = document.createElement('div');
      itemDiv.className = 'order-item';
      itemDiv.dataset.itemId = index;

      itemDiv.innerHTML = `
        
        <div class="field-group">
          <label>Item Name<span class="required-star">*</span></label>
          <input type="text" name="itemName" required />
        </div>

        <div class="field-group">
          <label>Price<span class="required-star">*</span></label>
          <input type="number" name="itemPrice" step="0.01" min="0" required />
        </div>

        <div class="field-group">
          <label>Notes / Description<span class="required-star">*</span></label>
          <textarea name="itemNotes"></textarea>
        </div>

        <div class="field-group">
          <label>Type<span class="required-star">*</span></label>
          <select name="itemType" class="item-type-select" required>
            <option value="">Select type</option>
            <option value="repair">Repair</option>
            <option value="sell">Sell</option>
          </select>
        </div>

        <div class="imei-device-group">
          <div class="field-group">
            <label>Device IMEI</label>
            <input type="text" name="itemImei" />
          </div>
          <div class="field-group">
            <label>Device Type</label>
            <select name="itemDeviceType">
              <option value="">Select</option>
              <option value="phone">Phone</option>
              <option value="tablet">Tablet</option>
            </select>
          </div>
        </div>
      `;

      // Attach change handler for type select to toggle IMEI/device
      const typeSelect = itemDiv.querySelector('.item-type-select');
      const imeiGroup = itemDiv.querySelector('.imei-device-group');

      typeSelect.addEventListener('change', () => {
        if (typeSelect.value === 'repair') {
          imeiGroup.style.display = 'block';
        } else {
          imeiGroup.style.display = 'none';
          // Optionally clear fields when hidden
          imeiGroup.querySelector('[name="itemImei"]').value = '';
          imeiGroup.querySelector('[name="itemDeviceType"]').value = '';
        }
      });

      itemsContainer.appendChild(itemDiv);
    }

    // Make removeItem globally accessible
    function removeItem(id) {
      const item = itemsContainer.querySelector(`.order-item[data-item-id="${id}"]`);
      if (item) {
        itemsContainer.removeChild(item);
      }
    }
    window.removeItem = removeItem;

    // orderForm.addEventListener('submit', async (e) => {
    //   e.preventDefault();
    //   statusMsg.textContent = '';
    //   statusMsg.className = 'status';

    //   // Basic validation: at least one item
    //   const itemElements = itemsContainer.querySelectorAll('.order-item');
    //   if (itemElements.length === 0) {
    //     statusMsg.textContent = 'Please add at least one order item.';
    //     statusMsg.classList.add('error');
    //     return;
    //   }

    //   // Build payload
    //   const payload = {
    //     customer: {
    //       name: document.getElementById('customerName').value.trim(),
    //       phone: document.getElementById('customerPhone').value.trim(),
    //       address: document.getElementById('customerAddress').value.trim() || null,
    //     },
    //     order: {
    //     //   additionalNotes: document.getElementById('orderNotes').value.trim() || null,
    //     //   description: document.getElementById('orderDescription').value.trim() || null,
    //         additionalNotes: null,
    //         description: null,
    //     },
    //     items: []
    //   };

    //   itemElements.forEach(item => {
    //     const name = item.querySelector('input[name="itemName"]').value.trim();
    //     const price = item.querySelector('input[name="itemPrice"]').value;
    //     const notes = item.querySelector('textarea[name="itemNotes"]').value.trim();
    //     const type = item.querySelector('select[name="itemType"]').value;
    //     const imeiGroup = item.querySelector('.imei-device-group');

    //     const imei = imeiGroup.querySelector('input[name="itemImei"]').value.trim();
    //     const deviceType = imeiGroup.querySelector('select[name="itemDeviceType"]').value;

    //     payload.items.push({
    //       name,
    //       price: price ? parseFloat(price) : null,
    //       notes: notes || null,
    //       type: type === 'repair' ? 1 : 2,
    //       imei: type === 'repair' && imei ? imei : null,
    //       device: type === 'repair' && deviceType ? deviceType : null
    //     });
    //   });

    //   try {
    //     statusMsg.textContent = 'Submitting...';
    //     // Update this to yourorderForm real API endpoint
    //     const response = await fetch('http://localhost:8000/api/orders', {
    //       method: 'POST',
    //       headers: {
    //         'Content-Type': 'application/json'
    //       },
    //       body: JSON.stringify(payload)
    //     });

    //     if (!response.ok) {
    //       throw new Error(`Request failed with status ${response.status}`);
    //     }

    //     const data = await response.json(); // optional: handle response data

    //     statusMsg.textContent = 'Order submitted successfully.';
    //     statusMsg.classList.add('success');

    //     // Optionally reset form and items
    //     orderForm.reset();
    //     itemsContainer.innerHTML = '';
    //     addOrderItem();
    //   } catch (err) {
    //     console.error(err);
    //     statusMsg.textContent = 'Failed to submit order. Please try again.';
    //     statusMsg.classList.add('error');
    //   }
    // });
  </script>
</body>
</html>