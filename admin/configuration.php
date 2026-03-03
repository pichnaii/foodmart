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

    $config_setting = $conn->query('SELECT * FROM configurations')->fetch_assoc();

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
                <div class="bg-light text-center rounded p-4">
                    <?php if(isset($_SESSION['message'])){?>
                        <div class="alert alert-<?=$_SESSION['message_type']?> alert-dismissible fade show" role="alert">
                            <?=$_SESSION['message']?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php } ?>
                    <form action="configuration.php" method="post" enctype="multipart/form-data">
                        <h5 class="mb-3 fw-bold text-title">Pages Configuration</h5>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="hidden" name="config_id" id="config_id" value="<?php echo $config_setting['id']; ?>">
                                <div class="form-group">
                                    <label for="slideshow" class="float-start fw-bold">Slideshow</label>
                                    <select class="form-select" id="slideshow" name="slideshow" aria-label="Select User Role">
                                        <option value="1" <?php echo $config_setting['slideshow'] == 1 ? 'selected' : ''; ?>>បើក</option>
                                        <option value="0" <?php echo $config_setting['slideshow'] == 0 ? 'selected' : ''; ?>>បិទ</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <input type="hidden" name="config_id" id="config_id" value="<?php echo $config_setting['id']; ?>">
                                <div class="form-group">
                                    <label for="product" class="float-start fw-bold">Product</label>
                                    <select class="form-select" id="product" name="product" value="<?php echo $config_setting['product'] ?>" aria-label="Select User Role">
                                        <option value="1" <?php echo $config_setting['product'] == 1 ? 'selected' : ''; ?>>Enable</option>
                                        <option value="0" <?php echo $config_setting['product'] == 0 ? 'selected' : ''; ?>>Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="category" class="float-start fw-bold">Category</label>
                                    <select class="form-select" id="category" name="category" aria-label="Select User Role">
                                        <option value="1" <?php echo $config_setting['category'] == 1 ? 'selected' : ''; ?>>Enable</option>
                                        <option value="0" <?php echo $config_setting['category'] == 0 ? 'selected' : ''; ?>>Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="page" class="float-start fw-bold">Page</label>
                                    <select class="form-select" id="page" name="page" aria-label="Select User Role">
                                        <option value="1" <?php echo $config_setting['page'] == 1 ? 'selected' : ''; ?>>Enable</option>
                                        <option value="0" <?php echo $config_setting['page'] == 0 ? 'selected' : ''; ?>>Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="user" class="float-start fw-bold">User</label>
                                    <select class="form-select" id="user" name="user" aria-label="Select User Role">
                                        <option value="1" <?php echo $config_setting['user'] == 1 ? 'selected' : ''; ?>>Enable</option>
                                        <option value="0" <?php echo $config_setting['user'] == 0 ? 'selected' : ''; ?>>Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="setting" class="float-start fw-bold">Setting</label>
                                    <select class="form-select" id="setting" name="setting" aria-label="Select User Role">
                                        <option value="1" <?php echo $config_setting['setting'] == 1 ? 'selected' : ''; ?>>Enable</option>
                                        <option value="0" <?php echo $config_setting['setting'] == 0 ? 'selected' : ''; ?>>Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="report" class="float-start fw-bold">Report</label>
                                    <select class="form-select" id="report" name="report" aria-label="Select User Role">
                                        <option value="1" <?php echo $config_setting['report'] == 1 ? 'selected' : ''; ?>>Enable</option>
                                        <option value="0" <?php echo $config_setting['report'] == 0 ? 'selected' : ''; ?>>Disable</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-2">
                                <div class="form-group float-start">
                                    <input type="checkbox" id="user_edit" name="user_edit" value="1" <?php echo $config_setting['user_edit'] == 1 ? 'checked' : ''; ?>>
                                    <label for="user_edit">Edit User</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group float-start">
                                    <input type="checkbox" id="user_delete" name="user_delete" value="1" <?php echo $config_setting['user_delete'] == 1 ? 'checked' : ''; ?>>
                                    <label for="user_delete">Edit Delete</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="productprefix" class="float-start fw-bold">Product Prefix</label>
                                    <input type="text" class="form-control" id="productprefix" name="productprefix" value="<?php echo $config_setting['productprefix'] ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sidebar_color" class="float-start fw-bold">Sidebar Color</label>
                                    <input type="color" class="form-control form-control-lg" id="sidebar_color" name="sidebar_color" value="<?php echo $config_setting['sidebar_color'] ?>">
                                </div>
                            </div>
                            <?php if(1 > 2 == FALSE) { ?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="bg_color" class="float-start fw-bold">Background Color</label>
                                        <input type="color" class="form-control form-control-lg" id="bg_color" name="bg_color" value="<?php echo $config_setting['bg_color'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="body_color" class="float-start fw-bold">Body Color</label>
                                        <input type="color" class="form-control form-control-lg" id="body_color" name="body_color" value="<?php echo $config_setting['body_color'] ?>">
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="theme_color" class="float-start fw-bold">Themes Color</label>
                                    <select class="form-select" id="theme_color" name="theme_color" aria-label="Select User Role">
                                        <option value="1" <?php echo $config_setting['theme_color'] == 1 ? 'selected' : ''; ?>>Light</option>
                                        <option value="0" <?php echo $config_setting['theme_color'] == 0 ? 'selected' : ''; ?>>Dark</option>
                                    </select>
                                </div>
                            </div>
                        <div>
                        <div class="modal-footer mt-3">
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