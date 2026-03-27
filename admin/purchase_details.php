<?php 
    require_once 'include/dbconnection.php';
    header('Content-Type: application/json; charset=utf-8');
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_id'])) {

        $purchase_id = (int)$_POST['purchase_id'];
        // fetch purchase details
        $stmt = $conn->prepare("SELECT id,
                                    create_date, 
                                    reference, 
                                    company, 
                                    warehouse, 
                                    supplier_name,
                                    rate, 
                                    grand_total, 
                                    paid, 
                                    (grand_total - IFNULL(paid,0)) AS balance,
                                    payment_status 
                                    FROM purchases 
                                    WHERE id = ?
                                ");
        if (!$stmt) {
            echo json_encode(['success' => false, 'error' => $conn->error]);
            exit;
        }
        $stmt->bind_param('i', $purchase_id);
        $stmt->execute();
        $purchase = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$purchase) {
            echo json_encode(['success' => false, 'error' => 'Purchase not found']);
            exit;
        }

        // fetch items
        $purchaseItems = $conn->prepare("SELECT 
                                    product_id, 
                                    product_code, 
                                    product_name, 
                                    unit, 
                                    cost, 
                                    quantity 
                                    FROM purchase_items 
                                    WHERE purchase_id = ?
                                ");
        if (!$purchaseItems) {
            echo json_encode(['success' => false, 'error' => $conn->error]);
            exit;
        }
        $purchaseItems->bind_param('i', $purchase_id);
        $purchaseItems->execute();
        $res = $purchaseItems->get_result();
        $items = [];
        while ($row = $res->fetch_assoc()) {
            $items[] = $row;
        }
        $purchaseItems->close();
        $conn->close();

        echo json_encode(['success' => true, 'purchase' => $purchase, 'items' => $items]);
        exit();
    }
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit();
?>