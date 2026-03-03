<?php
    require_once 'include/dbconnection.php';

    // Add Product
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addproduct']) == TRUE){
        // $code = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        $code = $_POST['code'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $cost = $_POST['cost'];
        $status = $_POST['status'];

        // category id and name
        $category_id = (int)$_POST['category'];
        $stmtCat = $conn->prepare("SELECT name FROM categories WHERE id = ?");
        $stmtCat->bind_param("i", $category_id);
        $stmtCat->execute();
        $stmtCat->bind_result($category_name);
        $stmtCat->fetch();
        $stmtCat->close();

        // unit id and name
        $unit_id = (int)$_POST['unit_id'];
        $stmtUnit = $conn->prepare("SELECT name FROM units WHERE id = ?");
        $stmtUnit->bind_param("i", $unit_id);
        $stmtUnit->execute();
        $stmtUnit->bind_result($unit);
        $stmtUnit->fetch();
        $stmtUnit->close();

        // Check if the code already exists
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM products WHERE code = ?");
        $stmtCheck->bind_param("s", $code);
        $stmtCheck->execute();
        $stmtCheck->bind_result($count);
        $stmtCheck->fetch();
        $stmtCheck->close();

        if ($count > 0) {
            $_SESSION['message'] = 'Product code already exists!';
            $_SESSION['message_type'] = 'danger';
            header('Location: product.php');
            exit();
        }

        if (isset($_FILES['image'])) {
            $image = $_FILES['image'];
            $encryptedName = md5(time() . $image['name']) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
            $targetDir = "images/uploads/";
            $targetFilePath = $targetDir . $encryptedName;

            if (move_uploaded_file($image['tmp_name'], $targetFilePath)) {
                $stmt = $conn->prepare("INSERT INTO products (
                                            code, 
                                            name, 
                                            price, 
                                            cost, 
                                            unit_id, 
                                            unit, 
                                            category_id, 
                                            category_name, 
                                            status, 
                                            image_path
                                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                                    ");
                $stmt->bind_param("ssssisisis", 
                                    $code, 
                                    $name, 
                                    $price, 
                                    $cost, 
                                    $unit_id, 
                                    $unit, 
                                    $category_id, 
                                    $category_name, 
                                    $status, 
                                    $encryptedName
                                );

                if ($stmt->execute()) {
                    $_SESSION['message'] = 'Product added successfully!';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = "Error123: " . $stmt->error;
                    $_SESSION['message_type'] = 'danger';
                }
                $stmt->close();
                header('Location: product.php');
                exit();

            } else {
                $stmt = $conn->prepare("INSERT INTO products (
                                            code, 
                                            name, 
                                            price, 
                                            cost, 
                                            unit_id, 
                                            unit, 
                                            category_id, 
                                            category_name, 
                                            status
                                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                                    ");
                $stmt->bind_param("ssssisiss", 
                                    $code, 
                                    $name, 
                                    $price, 
                                    $cost, 
                                    $unit_id, 
                                    $unit, 
                                    $category_id, 
                                    $category_name, 
                                    $status
                                );
            }
        }

        if ($stmt->execute()) {
            $_SESSION['message'] = 'Product Added successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: product.php');
        exit();
    }

    // Update Product
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateProduct']) == TRUE) {
        $update_id = $_POST['update_id'];
        $code = $_POST['code'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $cost = $_POST['cost'];
        $status = $_POST['status'];

        // category id and name
        $category_id = (int)($_POST['category'] ?? 0);
        $stmtCat = $conn->prepare("SELECT name FROM categories WHERE id = ?");
        $stmtCat->bind_param("i", $category_id);
        $stmtCat->execute();
        $stmtCat->bind_result($category_name);
        $stmtCat->fetch();
        $stmtCat->close();

        // unit id and name
        $unit_id = (int)$_POST['unit_id'];
        $stmtUnit = $conn->prepare("SELECT name FROM units WHERE id = ?");
        $stmtUnit->bind_param("i", $unit_id);
        $stmtUnit->execute();
        $stmtUnit->bind_result($unit);
        $stmtUnit->fetch();
        $stmtUnit->close();

        $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
        $stmt->bind_param("i", $update_id);
        $stmt->execute();
        $stmt->bind_result($oldImagePath);
        $stmt->fetch();
        $stmt->close();
    
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image = $_FILES['image'];
            $encryptedName = md5(time() . $image['name']) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
            $targetDir = "images/uploads/";
            $targetFilePath = $targetDir . $encryptedName;

            if (move_uploaded_file($image['tmp_name'], $targetFilePath)) {
                if (!empty($oldImagePath)) {
                    $oldFilePath = $targetDir . $oldImagePath;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                $stmt = $conn->prepare("UPDATE products SET 
                                            code = ?, 
                                            name = ?, 
                                            price = ?, 
                                            cost = ?, 
                                            unit_id = ?, 
                                            unit = ?, 
                                            category_id = ?, 
                                            category_name = ?, 
                                            status = ?, 
                                            image_path = ? 
                                        WHERE id = ?
                                    ");
                $stmt->bind_param("ssssisisisi", 
                                    $code, 
                                    $name, 
                                    $price, 
                                    $cost, 
                                    $unit_id, 
                                    $unit, 
                                    $category_id, 
                                    $category_name, 
                                    $status, 
                                    $encryptedName, 
                                    $update_id
                                );
            } else {
                $_SESSION['message'] = 'Error uploading image!';
                $_SESSION['message_type'] = 'danger';
                header('Location: product.php');
                exit();
            }
        } else {
            $stmt = $conn->prepare("UPDATE products SET 
                                        code = ?, 
                                        name = ?, 
                                        price = ?, 
                                        cost = ?, 
                                        unit_id = ?, 
                                        unit = ?, 
                                        category_id = ?, 
                                        category_name = ?, 
                                        status = ? 
                                    WHERE id = ?
                                ");
            $stmt->bind_param("ssssisisii",
                                $code, 
                                $name, 
                                $price, 
                                $cost, 
                                $unit_id, 
                                $unit, 
                                $category_id, 
                                $category_name, 
                                $status, 
                                $update_id
                            );
        }
    
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Product updated successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: product.php');
        exit();
    }
    
    // Delete Product
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        $delete_id = $_POST['delete_id'];
        $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->bind_result($imagePath);
        $stmt->fetch();
        $stmt->close();
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
    
        if ($stmt->execute()) {
            if (!empty($imagePath)) {
                $filePath = "images/uploads/" . $imagePath;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $_SESSION['message'] = 'Product deleted successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to delete Product!';
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: product.php');
        exit();
    }

    // for display product
    $product_query = " SELECT products.id AS product_id, 
                        products.image_path AS product_image,
                        products.code AS product_code, 
                        products.name AS product_name,
                        units.id AS unit_id,
                        units.name AS unit_name,
                        products.price AS product_price,
                        products.cost AS product_cost,
                        products.quantity AS qty,
                        products.status AS status,
                        categories.id AS category_id,
                        categories.name AS category_name
                        FROM products
                        LEFT JOIN categories ON products.category_id = categories.id
                        LEFT JOIN units ON products.unit_id = units.id
                    ";
    $result = $conn->query($product_query);

    // for edit product
    $id = (int) $_GET['id'];
    $edit_sql = "SELECT 
                    products.id AS product_id,
                    products.image_path AS product_image,
                    products.code AS product_code,
                    products.name AS product_name,
                    units.id AS unit_id,
                    units.name AS unit_name,
                    products.price AS product_price,
                    products.cost AS product_cost,
                    products.quantity AS quantity,
                    products.status AS status,
                    categories.id AS category_id,
                    categories.name AS category_name
                FROM products
                LEFT JOIN categories ON products.category_id = categories.id
                LEFT JOIN units ON products.unit_id = units.id
                WHERE products.id = $id
            ";
    $edit_product = $conn->query($edit_sql)->fetch_assoc();
    
    $units = $conn->query("SELECT id, name FROM units");
    $category = $conn->query('SELECT * FROM categories');
    
    $conn->close();
?>