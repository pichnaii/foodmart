<?php 
    session_start();
    $servername = "localhost";
    $username = "root";
    $password = "";     
    $dbname = "foodmart";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Load permission settings for logged-in user into session
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
        // Try to resolve role id from user_roles table using role name
        $roleId = 0;
        $stmtRole = $conn->prepare("SELECT id FROM user_roles WHERE name = ? LIMIT 1");
        if ($stmtRole) {
            $stmtRole->bind_param("s", $_SESSION['user_role']);
            $stmtRole->execute();
            $stmtRole->bind_result($roleId);
            $stmtRole->fetch();
            $stmtRole->close();
        }

        if ($roleId > 0) {
            $stmtPerm = $conn->prepare("SELECT 
                                            slideshow, 
                                            product_display, 
                                            product_create, 
                                            product_read, 
                                            product_update, 
                                            product_delete,
                                            report_display
                                        FROM user_permission WHERE userole_id = ? LIMIT 1
                                    ");
            if ($stmtPerm) {
                $stmtPerm->bind_param("i", $roleId);
                $stmtPerm->execute();
                $res = $stmtPerm->get_result();
                $perm = $res ? $res->fetch_assoc() : null;
                $stmtPerm->close();

                $_SESSION['product_display'] = isset($perm['product_display']) ? (int)$perm['product_display'] : 0;
                $_SESSION['product_create']  = isset($perm['product_create']) ? (int)$perm['product_create'] : 0;
                $_SESSION['product_read']    = isset($perm['product_read']) ? (int)$perm['product_read'] : 0;
                $_SESSION['product_update']  = isset($perm['product_update']) ? (int)$perm['product_update'] : 0;
                $_SESSION['product_delete']  = isset($perm['product_delete']) ? (int)$perm['product_delete'] : 0;
                $_SESSION['slideshow']       = isset($perm['slideshow']) ? (int)$perm['slideshow'] : 0;
                $_SESSION['report_display'] = isset($perm['report_display']) ? (int)$perm['report_display'] : 0;
            } else {
                // defaults
                $_SESSION['product_display'] = 0;
                $_SESSION['product_create'] = 0;
                $_SESSION['product_read'] = 0;
                $_SESSION['product_update'] = 0;
                $_SESSION['product_delete'] = 0;

                $_SESSION['slideshow'] = 0;
                $_SESSION['report_display'] = 0;
            }
        } else {
            // no role mapping found -> default deny
            $_SESSION['product_display'] = 0;
            $_SESSION['product_create'] = 0;
            $_SESSION['product_read'] = 0;
            $_SESSION['product_update'] = 0;
            $_SESSION['product_delete'] = 0;

            $_SESSION['slideshow'] = 0;
            $_SESSION['report_display'] = 0;
        }
    }


    function generateReference($conn) {
        $year = date('Y');
        $prefix = "PU/FBS/{$year}/";

        // Get the latest reference for the current year
        $sql = "SELECT reference 
                FROM purchases 
                WHERE reference LIKE ? 
                ORDER BY id DESC 
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $like = $prefix . '%';
        $stmt->bind_param('s', $like);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Extract the last 6 digits
            $lastNumber = (int) substr($row['reference'], -6);
            $nextNumber = $lastNumber + 1;
        } else {
            // First record of the year
            $nextNumber = 1;
        }

        $stmt->close();

        // Format as 000001, 000002, ...
        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
?>