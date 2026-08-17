<?php 
    require_once 'include/dbconnection.php';
    header('Content-Type: application/json; charset=utf-8');
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_id'])) {
        $purchase_id = (int)$_POST['purchase_id'];
        // fetch purchase details
        $stmt = $conn->prepare("SELECT 
                                    p.id,
                                    p.create_date, 
                                    p.reference,
                                    p.company_id,
                                    c.local_name AS company_name_kh,
                                    c.name AS company_name_en,
                                    c.local_address AS address_kh,
                                    c.vat AS vat,
                                    p.warehouse, 
                                    s.name AS supplier_name,
                                    s.phone AS tel,
                                    p.rate,
                                    p.shipping,
                                    p.discount,
                                    p.grand_total, 
                                    p.paid, 
                                    (p.grand_total - IFNULL(p.paid, 0)) AS balance,
                                    p.payment_status,
                                    p.payment_method,
                                    p.payment_note,
                                    p.status,
                                    p.note
                                    FROM purchases p
                                    LEFT JOIN company c ON c.id = p.company_id
                                    LEFT JOIN supplier s ON s.id = p.supplier_id
                                    WHERE p.id = ?
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