<?php
    include 'include/dbconnection.php';

    $requested_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $userole_id = 0;
    $permission_id = 0;
    $permission = null;

    // Permission Operations
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        $permission_id = isset($_POST['permission_id']) ? (int) $_POST['permission_id'] : 0;
        $userole_id = isset($_POST['userole_id']) ? (int) $_POST['userole_id'] : 0;

        if ($permission_id <= 0 && $userole_id <= 0) {
            $_SESSION['message'] = 'Permission ID or User Role ID missing...!';
            $_SESSION['message_type'] = 'danger';
            header('Location: permission.php');
            exit();
        }

        $slideshow = isset($_POST['slideshow']) ? 1 : 0;
        $product_display = isset($_POST['product_display']) ? 1 : 0;
        $product_create = isset($_POST['product_create']) ? 1 : 0;
        $product_read = isset($_POST['product_read']) ? 1 : 0;
        $product_update = isset($_POST['product_update']) ? 1 : 0;
        $product_delete = isset($_POST['product_delete']) ? 1 : 0;
        $report_display = isset($_POST['report_display']) ? 1 : 0;

        if ($permission_id > 0) {
            $stmtPermission = $conn->prepare("UPDATE user_permission SET 
                                                    slideshow = ?, 
                                                    product_display = ?, 
                                                    product_create = ?, 
                                                    product_read = ?, 
                                                    product_update = ?, 
                                                    product_delete = ?,
                                                    report_display = ?
                                                WHERE id = ?
                                            ");
            $stmtPermission->bind_param("iiiiiiii", 
                                            $slideshow, 
                                            $product_display, 
                                            $product_create, 
                                            $product_read, 
                                            $product_update, 
                                            $product_delete, 
                                            $report_display,
                                            $permission_id
                                        );
        } else {
            $stmt = $conn->prepare("SELECT id FROM user_permission WHERE userole_id = ?");
            $stmt->bind_param("i", $userole_id);
            $stmt->execute();
            $stmt->store_result();
            $hasPermission = $stmt->num_rows > 0;
            $stmt->close();

            if ($hasPermission) {
                $stmtPermission = $conn->prepare("UPDATE user_permission SET 
                                                        slideshow = ?, 
                                                        product_display = ?, 
                                                        product_create = ?, 
                                                        product_read = ?, 
                                                        product_update = ?, 
                                                        product_delete = ?,
                                                        report_display = ?
                                                    WHERE userole_id = ?
                                                ");
                $stmtPermission->bind_param("iiiiiiii", 
                                                $slideshow, 
                                                $product_display, 
                                                $product_create, 
                                                $product_read, 
                                                $product_update, 
                                                $product_delete,
                                                $report_display, 
                                                $userole_id
                                            );
            } else {
                $stmtPermission = $conn->prepare("INSERT INTO user_permission 
                                                    (
                                                        userole_id, 
                                                        slideshow, 
                                                        product_display, 
                                                        product_create, 
                                                        product_read, 
                                                        product_update, 
                                                        product_delete,
                                                        report_display
                                                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                                                ");
                $stmtPermission->bind_param("iiiiiiii", 
                                                $userole_id, 
                                                $slideshow, 
                                                $product_display, 
                                                $product_create, 
                                                $product_read, 
                                                $product_update, 
                                                $product_delete,
                                                $report_display
                                            );
            }
        }

        if ($stmtPermission->execute()) {
            $_SESSION['message'] = 'Permission saved successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to save permission!';
            $_SESSION['message_type'] = 'danger';
        }
        $stmtPermission->close();

        header('Location: user_permission.php');
        exit();
    }

    $config_setting = $conn->query('SELECT sidebar_color FROM configurations')->fetch_assoc();

    if ($requested_id > 0) {
        $stmt = $conn->prepare("SELECT * FROM user_permission WHERE id = ?");
        $stmt->bind_param("i", $requested_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $permission = $result->fetch_assoc();
        $stmt->close();

        if ($permission) {
            $permission_id = (int) $permission['id'];
            $userole_id = (int) $permission['userole_id'];
        } else {
            $stmt = $conn->prepare("SELECT * FROM user_permission WHERE userole_id = ?");
            $stmt->bind_param("i", $requested_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $permission = $result->fetch_assoc();
            $stmt->close();

            if ($permission) {
                $permission_id = (int) $permission['id'];
                $userole_id = (int) $permission['userole_id'];
            } else {
                $userole_id = $requested_id;
            }
        }
    }

    if (!$permission) {
        $permission = [
            'slideshow' => 0, 
            'product_display' => 0
        ];
    }

    $conn->close();
?>
<style>
    table.table tbody tr:hover td {
        background-color: #DDDDDD !important;
    }
    table.table thead tr th {
        color: <?= $config_setting['sidebar_color'] ?> !important;
    }
</style>
<!DOCTYPE html>
<html lang="en">
<?php include "include/header.php"?>
<style>
    input[type="checkbox"] {
        width: 20px;
        height: 20px;
    } 
    label {
        vertical-align: top;
    }

    /* Toggle Switch Styles */
    .switch {
        position: relative;
        display: inline-block;
        width: 45px;
        height: 25px;
    }
    
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.4s;
        border-radius: 34px;
    }
    
    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
    }
    
    input:checked + .slider {
        background-color: <?= $config_setting['sidebar_color'] ?> !important;
    }
    
    input:checked + .slider:before {
        transform: translateX(19px);
    }

    @media (max-width: 991.98px) {
        .content {
            width: auto;
            margin-left: 0;
        }
    }
</style>
<body>
    <div class="position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->
		<?php include "include/sidebar.php"?>
        <div class="content">
            <?php include "include/navbar.php"?>
            <div class="container-fluid pt-3 px-3">
                <div class="bg-light rounded p-4">
                    <?php if(isset($_SESSION['message'])){?>
                        <div class="alert alert-<?=$_SESSION['message_type']?> alert-dismissible fade show" role="alert">
                            <?=$_SESSION['message']?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php } ?>
                    <form action="permission.php?id=<?php echo isset($_GET['id']) ? (int) $_GET['id'] : 0; ?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="userole_id" value="<?= $userole_id ?>">
                        <input type="hidden" name="permission_id" value="<?= $permission_id ?>">
                        <h5 class="mb-3 fw-bold text-title text-right">Permission Settings</h5>
                        <div class="row mt-2">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Menu Name</th>
                                            <th class="text-center">Display</th>
                                            <th class="text-center">Create</th>
                                            <th class="text-center">Read</th>
                                            <th class="text-center">Update</th>
                                            <th class="text-center">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Slideshow</td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="slideshow" value="1" <?php echo ($permission['slideshow'] == 1) ? 'checked' : ''; ?>>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                        </tr>
                                        <tr>
                                            <td>Dashboard</td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Products</td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="product_display" <?php echo ($permission['product_display'] == 1) ? 'checked' : ''; ?>>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="product_create" <?php echo ($permission['product_create'] == 1) ? 'checked' : ''; ?>>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="product_read" <?php echo ($permission['product_read'] == 1) ? 'checked' : ''; ?>>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="product_update" <?php echo ($permission['product_update'] == 1) ? 'checked' : ''; ?>>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="product_delete" <?php echo ($permission['product_delete'] == 1) ? 'checked' : ''; ?>>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Sales</td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Purchase</td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Expenses</td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Report</td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="report_display" <?php echo ($permission['report_display'] == 1) ? 'checked' : ''; ?>>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <input type="submit" name="submit" value="Update" class="btn btn-success" style="background-color: <?= $config_setting['sidebar_color'] ?> !important; border-color: <?= $config_setting['sidebar_color'] ?> !important;">
                        </div>
                    </form>
                </div>
            </div>
		</div>
		<?php include "include/foot.php"?>
	</div>
</body>
</html>