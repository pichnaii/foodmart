<?php include 'action/UnitAction.php'; ?>
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
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUnit">
                            <i class="fas fa-plus"></i> Add Unit
                        </button>
                        <h5 class="mb-0 fw-bold text-title">Units List</h5>
                    </div>
                    <?php if(isset($_SESSION['message'])){?>
                        <div class="alert alert-<?=$_SESSION['message_type']?> alert-dismissible fade show turn-off" role="alert">
                            <?=$_SESSION['message']?>
                            <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php } ?>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="bg-secondary text-light text-center">
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Code</th>
                                    <th>Unit Name</th>
                                    <th>Unit Full Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-title">
                                <?php 
                                    if($units->num_rows > 0) {
                                        $i = 1; 
                                        while($row = $units->fetch_assoc()) { 
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $i++ ?></td>
                                            <td class="text-center"><?= date('d/m/Y', strtotime($row['created_date'])) ?></td>
                                            <td class="text-center"><?= $row['code'] ?></td>
                                            <td class="text-center"><?= $row['name'] ?></td>
                                            <td class="text-center"><?= $row['full_name'] ?></td>
                                            <td class="text-center">
                                                <a class="edit-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editUnit" 
                                                    data-id="<?= $row['id'] ?>"
                                                    data-created_date="<?= $row['created_date'] ?>" 
                                                    data-code="<?= $row['code'] ?>" 
                                                    data-name="<?= $row['name'] ?>" 
                                                    data-fullname="<?= $row['full_name'] ?>"
                                                >
                                                    <i class="bi bi-pencil-square cursor-pointer fs-4 text-title"></i>
                                                </a>
                                                <a class="delete-btn" data-bs-toggle="modal" data-bs-target="#deleteUnit" data-id="<?= $row['id'] ?>">
                                                    <i class="bi bi-trash text-danger cursor-pointer fs-4"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } } else { ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No Units Found</td>
                                        </tr>
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

    <!-- Add Unit -->
    <div class="modal fade" id="addUnit" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="unit.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" class="form-control" id="date" name="created_date" value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="code">Code</label>
                                    <input type="text" class="form-control gen-code" id="code" name="code">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">Unit Name</label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fullname">Unit Full Name</label>
                                    <input type="text" class="form-control" id="fullname" name="full_name">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <input type="submit" name="addUnit" value="Submit" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Unit -->
    <div class="modal fade" id="editUnit" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="edit_created_date">Date</label>
                                    <input type="date" class="form-control" id="edit_created_date" name="created_date">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="edit_code">Code</label>
                                    <input type="text" class="form-control" id="edit_code" name="code">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="edit_name">Unit Name</label>
                                    <input type="text" class="form-control" id="edit_name" name="name">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="edit_fullname">Unit Full Name</label>
                                    <input type="text" class="form-control" id="edit_fullname" name="full_name">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="updateUnit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Unit Confirmation -->
    <div class="modal fade" id="deleteUnit" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this record?
                    <small class="d-none mt-2 text-danger">*Your action cannot be reversed.</small>
                    <small class="d-block mt-2 text-danger">*សកម្មភាពរបស់អ្នកមិនអាចត្រឡប់ក្រោយបានទេ។</small>
                </div>
                <div class="modal-footer">
                    <form action="" method="post">
                        <input type="hidden" name="delete_id" id="delete_id">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="deleteUnit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.edit-btn').on('click', function() {
                var id = $(this).data('id');
                var date = $(this).data('created_date');
                var code = $(this).data('code');
                var name = $(this).data('name');
                var fullname = $(this).data('fullname');

                $('#edit_id').val(id);
                $('#edit_created_date').val(date);
                $('#edit_code').val(code);
                $('#edit_name').val(name);
                $('#edit_fullname').val(fullname);
            });

            $('.delete-btn').on('click', function() {
                var id = $(this).data('id');
                $('#delete_id').val(id);
            });

            // Auto hide alert after 3 seconds
            setTimeout(function() {
                $('.turn-off').alert('close');
            }, 1500);
        });
    </script>
</body>
</html>