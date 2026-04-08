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

    // Add Configuration
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']) == TRUE){
    
        $config_id = $_POST['config_id'];
        $slideshow = $_POST['slideshow'];
        $product = $_POST['product'];
        $category = $_POST['category'];
        $page = $_POST['page'];
        $user = $_POST['user'];
        $setting = $_POST['setting'];
        $report = $_POST['report'];
        $productprefix = $_POST['productprefix'];
        $user_edit = isset($_POST['user_edit']) ? 1 : 0;
        $user_delete = isset($_POST['user_delete']) ? 1 : 0;

        $theme_color = $_POST['theme_color'];
        $bg_color = filter_input(INPUT_POST, 'bg_color', FILTER_SANITIZE_STRING);
        $sidebar_color = filter_input(INPUT_POST, 'sidebar_color', FILTER_SANITIZE_STRING);
        $body_color = filter_input(INPUT_POST, 'body_color', FILTER_SANITIZE_STRING);

        if (preg_match('/^#[a-fA-F0-9]{6}$/', $bg_color) && preg_match('/^#[a-fA-F0-9]{6}$/', $sidebar_color) && preg_match('/^#[a-fA-F0-9]{6}$/', $body_color) ) {
            $stmt = $conn->prepare("UPDATE configurations SET theme_color = ?, body_color = ?, bg_color = ?, sidebar_color = ?, slideshow = ?, product = ?, category = ?, page = ?, user = ?, setting = ?, report = ?, user_edit = ?, user_delete = ?, productprefix = ? WHERE id = ?");
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($conn->error));
            }
            $stmt->bind_param("isssiiiiiiiiisi", $theme_color, $body_color, $bg_color, $sidebar_color, $slideshow, $product, $category, $page, $user, $setting, $report, $user_edit, $user_delete, $productprefix, $config_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = 'Update Successfully!';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Update Unsuccessfully!';
                $_SESSION['message_type'] = 'danger';
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = 'Invalid color format.';
            $_SESSION['message_type'] = 'danger';
        }
        header('Location: configuration.php');
        exit();
    }

    $permission = $conn->query('SELECT * FROM configurations')->fetch_assoc();

    $sql = "SELECT * FROM configurations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $config_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $configurate = $result->fetch_assoc();
    $stmt->close();

    $conn->close();
?>
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
        background-color: <?= $permission['sidebar_color'] ?> !important;
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
                    <form action="configuration.php" method="post" enctype="multipart/form-data">
                        <h5 class="mb-3 fw-bold text-title text-right">Permission Settings</h5>
                        <div class="row mt-2">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Menu Name</th>
                                            <th class="text-center">Create</th>
                                            <th class="text-center">Read</th>
                                            <th class="text-center">Update</th>
                                            <th class="text-center">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Dashboard</td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1" checked>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1" checked>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1" checked>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1" checked>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Sales</td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1" checked>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1" checked>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1" checked>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1" checked>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Purchase</td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1" checked>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1" checked>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1" checked>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1" checked>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Expenses</td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1" checked>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1" checked>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1" checked>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input type="checkbox" name="user_edit" value="1" checked>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <input type="submit" name="submit" value="Update" class="btn btn-success">
                        </div>
                    </form>
                </div>
            </div>
		</div>
		<?php include "include/foot.php"?>
	</div>
</body>
</html>