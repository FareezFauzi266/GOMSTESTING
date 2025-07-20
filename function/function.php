<?php
$sql = "SELECT ii.itemCode, ii.itemName, ii.itemQuantity, s.supplierName
        FROM inventoryItem ii
        LEFT JOIN supplier s ON ii.itemSupplierID = s.supplierID";
$result = $conn->query($sql);



// functions/functions.php

/**
 * Get all inventory items from database
 */
function getInventoryItems($conn) {
    $sql = "SELECT i.*, s.supplierName, s.supplierContactNumber, s.supplierEmail, 
                   s.supplierPICName, s.supplierPICNumber
            FROM inventoryitem i
            LEFT JOIN supplier s ON i.itemSupplierID = s.supplierID
            ORDER BY i.itemName";
    
    $result = $conn->query($sql);
    $items = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }
    
    return $items;
}

/**
 * Get all suppliers from database
 */
function getSuppliers($conn) {
    $sql = "SELECT * FROM supplier ORDER BY supplierName";
    $result = $conn->query($sql);
    $suppliers = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $suppliers[] = $row;
        }
    }
    
    return $suppliers;
}

/**
 * Get all categories from database (hardcoded based on your data)
 */

/**
 * Add new inventory item to database
 */
function addInventoryItem($conn, $data) {
    $sql = "INSERT INTO inventoryitem (itemName, itemQuantity, itemPrice, itemCategory, itemSupplierID)
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidsi", 
        $data['itemName'],
        $data['itemQuantity'],
        $data['itemPrice'],
        $data['itemCategory'],
        $data['itemSupplierID']
    );
    
    return $stmt->execute();
}

/**
 * Update existing inventory item
 */
function updateInventoryItem($conn, $itemCode, $data) {
    $sql = "UPDATE inventoryitem 
            SET itemName = ?, itemQuantity = ?, itemPrice = ?, 
                itemCategory = ?, itemSupplierID = ?
            WHERE itemCode = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidssi", 
        $data['itemName'],
        $data['itemQuantity'],
        $data['itemPrice'],
        $data['itemCategory'],
        $data['itemSupplierID'],
        $itemCode
    );
    
    return $stmt->execute();
}

/**
 * Delete inventory item
 */
function deleteInventoryItem($conn, $itemCode) {
    $sql = "DELETE FROM inventoryitem WHERE itemCode = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $itemCode);
    return $stmt->execute();
}


/**
 * Add new supplier
 */
function addSupplier($conn, $data) {
    $sql = "INSERT INTO supplier (supplierName, supplierContactNumber, supplierEmail, 
                                 supplierPICName, supplierPICNumber)
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", 
        $data['supplierName'],
        $data['supplierContactNumber'],
        $data['supplierEmail'],
        $data['supplierPICName'],
        $data['supplierPICNumber']
    );
    
    if ($stmt->execute()) {
        return $conn->insert_id;
    }
    return false;
}

/**
 * Generate next item code
 */
function generateNextItemCode($conn) {
    $sql = "SELECT itemCode FROM inventoryitem ORDER BY itemCode DESC LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['itemCode'] + 1;
    }
    
    // Default if no items exist
    return 7001;
}

/**
 * Handle AJAX requests
 */
function handleAjaxRequest($conn) {
    header('Content-Type: application/json');
    $response = ['success' => false];
    
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'add_item':
                    // Process add item
                    $data = [
                        'itemName' => $_POST['itemName'],
                        'itemQuantity' => (int)$_POST['itemQuantity'],
                        'itemPrice' => (float)$_POST['itemPrice'],
                        'itemCategory' => $_POST['itemCategory'],
                        'itemSupplierID' => (int)$_POST['itemSupplierID']
                    ];
                    
                    if (addInventoryItem($conn, $data)) {
                        $response['success'] = true;
                        $response['message'] = 'Item added successfully';
                        $response['itemCode'] = $conn->insert_id;
                    } else {
                        $response['message'] = 'Failed to add item';
                    }
                    break;
                    
                case 'update_item':
                    // Process update item
                    $itemCode = $_POST['itemCode'];
                    $data = [
                        'itemName' => $_POST['itemName'],
                        'itemQuantity' => (int)$_POST['itemQuantity'],
                        'itemPrice' => (float)$_POST['itemPrice'],
                        'itemCategory' => $_POST['itemCategory'],
                        'itemSupplierID' => (int)$_POST['itemSupplierID']
                    ];
                    
                    if (updateInventoryItem($conn, $itemCode, $data)) {
                        $response['success'] = true;
                        $response['message'] = 'Item updated successfully';
                    } else {
                        $response['message'] = 'Failed to update item';
                    }
                    break;
                    
                case 'delete_item':
                    // Process delete item
                    $itemCode = $_POST['itemCode'];
                    if (deleteInventoryItem($conn, $itemCode)) {
                        $response['success'] = true;
                        $response['message'] = 'Item deleted successfully';
                    } else {
                        $response['message'] = 'Failed to delete item';
                    }
                    break;
                    
                case 'add_supplier':
                    // Process add supplier
                    $data = [
                        'supplierName' => $_POST['supplierName'],
                        'supplierContactNumber' => $_POST['supplierContactNumber'] ?? '',
                        'supplierEmail' => $_POST['supplierEmail'] ?? '',
                        'supplierPICName' => $_POST['supplierPICName'] ?? '',
                        'supplierPICNumber' => $_POST['supplierPICNumber'] ?? ''
                    ];
                    
                    $supplier_id = addSupplier($conn, $data);
                    if ($supplier_id) {
                        $response['success'] = true;
                        $response['supplierID'] = $supplier_id;
                        $response['supplierName'] = $data['supplierName'];
                        $response['message'] = 'Supplier added successfully';
                    } else {
                        $response['message'] = 'Failed to add supplier';
                    }
                    break;
                    
                case 'get_next_code':
                    // Get next item code
                    $response['success'] = true;
                    $response['itemCode'] = generateNextItemCode($conn);
                    break;
                    
                case 'get_item_data':
                    // Get item data for editing
                    if (!isset($_POST['itemCode'])) {
                        throw new Exception('Item code not provided');
                    }
                    
                    $itemCode = $conn->real_escape_string($_POST['itemCode']);
                    $query = "SELECT ii.*, s.supplierName, s.supplierID as itemSupplierID
                             FROM inventoryItem ii
                             LEFT JOIN supplier s ON ii.itemSupplierID = s.supplierID
                             WHERE ii.itemCode = '$itemCode'";
                    $result = $conn->query($query);
                    
                    if ($result && $result->num_rows > 0) {
                        $item = $result->fetch_assoc();
                        $response = [
                            'success' => true,
                            'data' => [
                                'itemCode' => $item['itemCode'],
                                'itemName' => $item['itemName'],
                                'itemQuantity' => $item['itemQuantity'],
                                'itemPrice' => $item['itemPrice'],
                                'itemCategory' => $item['itemCategory'],
                                'itemSupplierID' => $item['itemSupplierID'],
                                'supplierName' => $item['supplierName']
                            ]
                        ];
                    } else {
                        throw new Exception('Item not found');
                    }
                    break;
            }
        }
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
    
    echo json_encode($response);
    exit;
}