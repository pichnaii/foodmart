<?php 
    include 'include/dbconnection.php';
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
                $stmt = $conn->prepare("INSERT INTO products (code, name, price,cost, unit_id, unit, category_id, category_name, status, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssisisis", $code, $name, $price, $cost, $unit_id, $unit, $category_id, $category_name, $status, $encryptedName);

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
                $stmt = $conn->prepare("INSERT INTO products (code, name, price, cost, unit_id, unit, category_id, category_name, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssisiss", $code, $name, $price, $cost, $unit_id, $unit, $category_id, $category_name, $status);
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

    $units = $conn->query("SELECT id, name FROM units");
    $category = $conn->query('SELECT * FROM categories');
    
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<?php include "include/header.php"?>
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
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="mb-0 fw-bold text-title">Add Products</h5>
                    </div>
                    <?php if(isset($_SESSION['message'])){?>
                        <div class="alert alert-<?=$_SESSION['message_type']?> alert-dismissible fade show" role="alert">
                            <?=$_SESSION['message']?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php } ?>
                    <form action="add_product.php" method="post" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="code" class="mb-1">Code <span class="text-danger fw-bold">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="code" name="code" placeholder="General By System or Manual...">
                                    <button type="button" class="btn btn-success" id="generateCode">
                                        <i class="fa fa-sync"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name" class="mb-1">Product Name <span class="text-danger fw-bold">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <div class="form-group">
                                    <label class="mb-1">Category</label>
                                    <select id="categorySelect" class="form-select" name="category">
                                        <?php
                                            if ($category->num_rows > 0) {
                                                while($row = $category->fetch_assoc()) {
                                                    echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                                                } 
                                            } else {
                                                echo '<option value="">No Data Display</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="mb-1">Unit</label>
                                    <select id="unitSelect" class="form-select" name="unit_id">
                                        <?php
                                            while($unit = $units->fetch_assoc()) {
                                                echo "<option value='" . $unit['id'] . "'>" . htmlspecialchars($unit['name']) . "</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <?php if($product_cost == true) { ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cost" class="mb-1">Cost</label>
                                        <input type="text" class="form-control" id="cost" name="cost">
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="price" class="mb-1">Price</label>
                                    <input type="text" class="form-control" id="price" name="price">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-select" name="status" id="edit_status">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" class="mb-1">
                                    <label for="discount">Product Image</label>
                                    <input type="file" class="form-control" name="image" accept="image/*">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" name="addproduct" value="Submit" class="btn btn-success">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php include "include/footer.php"?>
		</div>
		<?php include "include/foot.php"?>
	</div>

    <script>
        $(document).ready(function() {

            $('.delete-btn').on('click', function() {
                var id = $(this).data('id');
                $('#delete_id').val(id);
            });

            // generate code 
            let codeHistory = [];
            $('#generateCode').on('click', function() {
                // Generate new code
                let randomNum = Math.floor(Math.random() * 100000000);
                let newCode = randomNum.toString().padStart(8, '0');
                
                // Check if code already exists in history (optional)
                if (codeHistory.includes(newCode)) {
                    // If exists, generate a new one
                    generateUniqueCode();
                } else {
                    // Add to history and set value
                    codeHistory.push(newCode);
                    $('#code').val(newCode);
                    
                    // Limit history size (keep last 10)
                    if (codeHistory.length > 10) {
                        codeHistory.shift();
                    }
                }
            });
            function generateUniqueCode() {
                let randomNum = Math.floor(Math.random() * 100000000);
                let newCode = randomNum.toString().padStart(8, '0');
                
                if (codeHistory.includes(newCode)) {
                    generateUniqueCode(); // Recursive until unique
                } else {
                    codeHistory.push(newCode);
                    $('#code').val(newCode);
                }
            }   

            $('#productSearch').on('keyup', function() {
                var query = $(this).val().toLowerCase().trim();
                $('table tbody tr').each(function() {
                    var productData = $(this).text().toLowerCase();
                    if (productData.includes(query)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Initialize Select2
            $(document).ready(function () {
                $('#categorySelect').select2({
                    theme: 'bootstrap-5',           // matches Bootstrap styling
                    placeholder: '-- Choose a category --',
                    allowClear: true,               // shows an X to clear selection
                    width: '100%'                   // full width of the container
                });

                $('#unitSelect').select2({
                    theme: 'bootstrap-5',         
                    placeholder: '-- Choose a unit --',
                    allowClear: true,              
                    width: '100%'          
                });
            });
        });
    </script>
</body>
</html>