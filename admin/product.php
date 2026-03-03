<?php 
    include 'include/dbconnection.php';

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
                            purchase_items.quantity AS quantity,
                            products.status AS status,
                            categories.id AS category_id,
                            categories.name AS category_name
                        FROM products
                        LEFT JOIN categories ON products.category_id = categories.id
                        LEFT JOIN units ON products.unit_id = units.id
                        LEFT JOIN purchase_items ON products.id = purchase_items.product_id
                    ";
    $result = $conn->query($product_query);
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
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-0">
                        <!-- <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#add">
                            <i class="fas fa-plus"></i> Add Product
                        </button> -->
                        <a href="add_product.php" class="btn btn-primary mb-3">
                            <i class="fas fa-plus"></i> Add Product
                        </a>
                        <h5 class="mb-0 fw-bold text-title">Products List</h5>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div></div>
                        <div class="input-group w-25">
                            <input type="text" id="productSearch" class="form-control w-50" placeholder="Search by product name, code...">
                        </div>
                    </div>
                    <?php if(isset($_SESSION['message'])){?>
                        <div class="alert alert-<?=$_SESSION['message_type']?> alert-dismissible fade show" role="alert">
                            <?=$_SESSION['message']?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php } ?>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="bg-secondary text-light text-center">
                                    <th>No</th>
                                    <?php if($product_image == true) { ?>
                                        <th width="5%">Image</th>
                                    <?php } ?>
                                    <th>Code</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Unit</th>
                                    <?php if($product_cost == true) { ?>
                                        <th>Cost</th>
                                    <?php } ?>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-title">
                                <?php
                                    if ($result->num_rows > 0) {
                                        $no = 1;
                                        while($row = $result->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no ?></td>
                                    <?php if($product_image == true) { ?>
                                        <td class="text-center"><?= !empty($row['product_image']) ? '<img src="images/uploads/'. $row['product_image'] .'" style="width:3rem;height:3rem;">' : '<img src="images/uploads/no-image.png" style="width:3rem;height:auto;">';?></td>
                                    <?php } ?>
                                    <td class="text-center"><?= $row['product_code'] ?></td>
                                    <td><?= $row['product_name'] ?></td>
                                    <td class="text-left"><?= $row['category_name'] ?></td>
                                    <td class="text-center"><?= $row['unit_name'] ?></td>
                                    <?php if($product_cost == true) { ?>
                                        <td class="text-center"><?= !empty($row['product_cost']) ? '<span class="badge-red">$ ' . $row['product_cost'] . '</span>' : '' ?></td>
                                    <?php } ?>
                                    <td class="text-center"><span class="badge-green">$ <?= $row['product_price'] ?></span></td>
                                    <td class="text-center"><?= $row['quantity'] ?><span class="ps-1 text-primary fs-6"><?= $row['unit_name'] ?></span></td>
                                    <td class="text-center"><span class="badge-<?= $row['status'] == "1" ? "purple" : "red" ?>"><?= $row['status'] == 1 ? "Active"  : "Inactive" ?></span></td>
                                    <td class="text-center">
                                        <!-- for modal edit -->
                                        <a href="#" class="edit-btn d-none"
                                            data-id="<?= $row['product_id'] ?>" 
                                            data-code="<?= $row['product_code'] ?>" 
                                            data-name="<?= $row['product_name'] ?>" 
                                            data-unit="<?= $row['unit_name'] ?>" 
                                            data-price="<?= $row['product_price'] ?>" 
                                            data-cost="<?= $row['product_cost'] ?>" 
                                            data-category="<?= $row['category_id'] ?>"
                                            data-status="<?= $row['status'] ?>"
                                            data-image="<?= htmlspecialchars($row['product_image']) ?>"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#EditProduct">
                                            <i class="bi bi-pencil-square fs-4 cursor-pointer"></i>
                                        </a>
                                        <a href="edit_product.php?id=<?= $row['product_id'] ?>" class="edit-btn">
                                            <i class="bi bi-pencil-square fs-4 cursor-pointer"></i>
                                        </a>
                                        <a class="delete-btn" data-id="<?= $row['product_id'] ?>" data-bs-toggle="modal" data-bs-target="#delete">
                                            <i class="bi bi-trash text-danger cursor-pointer fs-4"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                                    $no++;
                                    } 
                                } else { ?>
                                    <tr><td colspan='8' class='text-center'>No products found.</td></tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php include "include/footer.php"?>
		</div>
		<?php include "include/foot.php"?>
	</div>

    <!-- Add Products -->
    <div class="modal fade" id="add" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add Products</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="product.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php 
                                        $newcode = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);

                                        // Generate a new random code when requested via AJAX
                                        // if (isset($_GET['generate_code'])) {
                                        //     $newcode = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
                                        //     echo $newcode;
                                        //     exit; // Stop further execution after returning the code
                                        // }
                                    ?>
                                </div>
                                <label for="code">Code</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="code" name="code" placeholder="General By System..." readonly>
                                    <button type="button" class="btn btn-primary" id="generateCode">
                                        <i class="fa fa-sync"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Product Name</label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select id="categorySelect" class="form-select" name="category">
                                        <option value="all">All Categories</option>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="unit">Unit</label>
                                    <input type="text" class="form-control" id="unit" name="unit">
                                </div>
                            </div>
                            <?php if($product_cost == true) { ?>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cost">cost</label>
                                        <input type="text" class="form-control" id="cost" name="cost">
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price">Price</label>
                                    <input type="text" class="form-control" id="price" name="price">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="discount">Product Image</label>
                                    <input type="file" class="form-control" name="image" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <input type="submit" name="addproduct" value="Submit" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product -->
    <div class="modal fade" id="EditProduct" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="product.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="update_id" id="update_id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_code">Code</label>
                                    <input type="text" class="form-control" id="edit_code" name="code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_name">Product Name</label>
                                    <input type="text" class="form-control" id="edit_name" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select class="form-select" name="category" id="edit_category">
                                        <?php
                                            $editCategory = new mysqli($servername, $username, $password, $dbname);
                                            $cat_result = $editCategory->query('SELECT * FROM categories');
                                            if ($cat_result->num_rows > 0) {
                                                while($cat = $cat_result->fetch_assoc()) {
                                                    echo "<option value='" . htmlspecialchars($cat['id']) . "'>" . htmlspecialchars($cat['name']) . "</option>";
                                                }
                                            } else {
                                                echo '<option value="">No Data Display</option>';
                                            }
                                            $editCategory->close();
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_unit">Unit</label>
                                    <input type="text" class="form-control" id="edit_unit" name="unit">
                                </div>
                            </div>
                            <?php if($product_cost == true) { ?>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_cost">Cost</label>
                                        <input type="text" class="form-control" id="edit_cost" name="cost" required>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_price">Price</label>
                                    <input type="text" class="form-control" id="edit_price" name="price" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-select" name="status" id="edit_status">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_image">Product Image</label>
                                    <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                                    <div class="mb-2">
                                        <img class="mt-2" id="edit_image_preview" src="https://i.pinimg.com/1200x/5b/f7/22/5bf722d58d3497843454d4f31b5ec224.jpg" alt="Product Preview" style="width:27rem;height:auto;object-fit:cover;border-radius:4px;border:2px solid #ddd;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="updateProduct" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this record?
                </div>
                <div class="modal-footer">
                    <form action="product.php" method="post">
                        <input type="hidden" name="delete_id" id="delete_id">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.edit-btn').on('click', function() {
                var id = $(this).data('id');
                var code = $(this).data('code');
                var name = $(this).data('name');
                var unit = $(this).data('unit');
                var price = $(this).data('price');
                var cost = $(this).data('cost');
                var category = $(this).data('category');
                var status = $(this).data('status');
                var image = $(this).data('image');

                $('#update_id').val(id);
                $('#edit_code').val(code);
                $('#edit_name').val(name);
                $('#edit_unit').val(unit);
                $('#edit_price').val(price);
                $('#edit_cost').val(cost);
                $('#edit_category').val(category);
                $('#edit_status').val(status);
                var previewSrc = image ? 'images/uploads/' + image : 'images/uploads/no-image.png';
                $('#edit_image_preview').attr('src', previewSrc);
            });

            $('#edit_image').on('change', function(e) {
                var file = this.files && this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(evt) {
                        $('#edit_image_preview').attr('src', evt.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });

            $('.delete-btn').on('click', function() {
                var id = $(this).data('id');
                $('#delete_id').val(id);
            });

            $('#generateCode').on('click', function() {
                let newCode = '<?= $newcode; ?>';
                $('#code').val(newCode);
            });

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
            // $(document).ready(function () {
            //     $('#categorySelect').select2({
            //         theme: 'bootstrap-5',           // matches Bootstrap styling
            //         placeholder: '-- Choose a category --',
            //         allowClear: true,               // shows an X to clear selection
            //         width: '100%'                   // full width of the container
            //     });
            // });
        });
    </script>
</body>
</html>