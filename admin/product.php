<?php 
    include 'include/dbconnection.php';

    // Authorization: ensure current user can view products
    $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
    if (!$isAdmin && (!isset($_SESSION['product_display']) || $_SESSION['product_display'] != 1)) {
        $_SESSION['message'] = 'Access denied: you do not have permission to view products.';
        $_SESSION['message_type'] = 'danger';
        header('Location: index.php');
        exit();
    }

    // Delete Product
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        // server-side permission check for delete
        if (!$isAdmin && (!isset($_SESSION['product_delete']) || $_SESSION['product_delete'] != 1)) {
            $_SESSION['message'] = 'Access denied: you do not have permission to delete products.';
            $_SESSION['message_type'] = 'danger';
            header('Location: product.php');
            exit();
        }

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
                            products.quantity AS quantity,
                            products.status AS status,
                            categories.id AS category_id,
                            categories.name AS category_name
                        FROM products
                        LEFT JOIN categories ON products.category_id = categories.id
                        LEFT JOIN units ON products.unit_id = units.id
                        ORDER BY products.id DESC
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
                        <!-- Add Product button: render only if user can create -->
                        <?php if ($isAdmin || (isset($_SESSION['product_create']) && $_SESSION['product_create'] == 1)) { ?>
                            <a href="add_product.php" class="btn btn-primary mb-3">
                                <i class="fas fa-plus"></i> Add Product
                            </a>
                        <?php } ?>
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