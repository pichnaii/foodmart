<?php include "action/SlideshowControll.php" ?>
<style>
    table > thead > tr > th:nth-child(7),
    table > tbody > tr > td:nth-child(7) {
        display: none;
    }

    table > thead > tr > th:nth-child(8),
    table > tbody > tr > td:nth-child(8) {
        display: none;
    }
</style>
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
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addSlideshow">
                            <i class="fas fa-plus"></i> Add Slideshow
                        </button>
                        <h5 class="mb-0 fw-bold text-title">Products List</h5>
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
                                    <th class="py-3">No</th>
                                    <th class="py-3">Image</th>
                                    <th class="py-3">Code</th>
                                    <th class="py-3">Product Name</th>
                                    <th class="py-3">Flavor</th>
                                    <th class="py-3">Description</th>
                                    <th class="py-3">Link Page</th>
                                    <th class="py-3">Link Social Media</th>
                                    <th class="py-3">Slide Type</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Action</th>
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
                                    <td class="text-center"><?= !empty($row['image_path']) ? '<img src="images/uploads/slideshows/'. $row['image_path'] .'" style="width:3rem;height:auto;">' : '<img src="images/uploads/no-image.png" style="width:3rem;height:auto;">';?></td>
                                    <td class="text-center"><?= $row['code'] ?></td>
                                    <td class="text-left"><?= $row['name'] ?></td>
                                    <td class="text-left"><?= $row['flavor'] ?></td>
                                    <td class="text-left">
                                        <?= strlen($row['description']) > 50 ? substr($row['description'], 0, 40) . '.......' : $row['description'] ?>
                                    </td>
                                    <td class="text-center"><?= $row['link_page'] ?></td>
                                    <td class="text-center"><?= $row['link_media'] ?></td>
                                    <td class="text-center">
                                        <span class="text-<?= ($row['imagetype'] == 1) ? 'dark' : 'light'?> btn-sm btn-<?= ($row['imagetype'] == 1) ? 'warning' : 'primary'?>">
                                            <?php 
                                                if($row['imagetype'] == 1){
                                                    echo 'Slideshow';
                                                } else if($row['imagetype'] == 2){
                                                    echo 'Banner Top';
                                                } else {
                                                    echo 'Banner Down';
                                                }
                                            ?>
                                        </span>
                                    </td>
                                    <td class="text-center"><span class="text-light btn-sm btn-<?= ($row['status'] == 'Active') ? 'success' : 'danger'?>"><?= $row['status'] ?></span></td>
                                    <td class="text-center">
                                        <a class="edit-btn cursor-pointer" 
                                            data-id="<?= $row['id'] ?>" 
                                            data-code="<?= $row['code'] ?>" 
                                            data-name="<?= $row['name'] ?>" 
                                            data-flavor="<?= $row['flavor'] ?>" 
                                            data-description="<?= $row['description'] ?>" 
                                            data-link_page="<?= $row['link_page'] ?>" 
                                            data-link_media="<?= $row['link_media'] ?>" 
                                            data-status="<?= $row['status'] ?>" 
                                            data-imagetype="<?= htmlspecialchars($row['imagetype']) ?>" 
                                            data-image="<?= htmlspecialchars($row['image_path']) ?>"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#EditSlide"
                                        >
                                            <i class="bi bi-pencil-square fs-4 text-title"></i>
                                        </a>
                                        <a class="delete-btn cursor-pointer"
                                            data-id="<?= $row['id'] ?>" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#delete"
                                        >
                                            <i class="bi bi-trash text-danger fs-4"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                                    $no++;
                                    } 
                                } else { ?>
                                    <tr><td colspan='9' class='text-center'>No products found.</td></tr>
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
    <div class="modal fade" id="addSlideshow" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add Slideshow</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="slideshow.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Code</label>
                                    <input type="text" class="form-control" id="code" name="code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Product Name</label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="flavor">Flavor</label>
                                    <input type="text" class="form-control" id="flavor" name="flavor">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="discount">Slideshow Image</label>
                                    <input type="file" class="form-control" name="image" accept="image/*">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="link_page">Link Page</label>
                                    <input type="text" class="form-control" id="link_page" name="link_page">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="link_media">Link Social Media</label>
                                    <input type="text" class="form-control" id="link_media" name="link_media">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="imagetype">Slide Type</label>
                                    <select class="form-select" id="imagetype" name="imagetype" aria-label="Select Gender">
                                        <option value="1">Slide Show</option>
                                        <option value="2">Banner Top</option>
                                        <option value="3">Banner Down</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-select" id="status" name="status" aria-label="Select Gender">
                                        <option selected>Select Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" name="description" placeholder="Write Description Here..." id="description" style="height: 100px"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                        <input type="submit" name="addSlide" value="Submit" class="btn btn-outline-success">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="EditSlide" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Slideshow</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="slideshow.php" method="post" enctype="multipart/form-data">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_flavor">Flavor</label>
                                    <input type="text" class="form-control" id="edit_flavor" name="flavor">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_link_page">Link Page</label>
                                    <input type="text" class="form-control" id="edit_link_page" name="link_page">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_link_media">Link Social Media</label>
                                    <input type="text" class="form-control" id="edit_link_media" name="link_media">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_imagetype">Slide Type</label>
                                    <select class="form-select" id="edit_imagetype" name="imagetype" aria-label="Select Gender">
                                        <option value="1">Slide Show</option>
                                        <option value="2">Banner Top</option>
                                        <option value="3">Banner Down</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_status">Status</label>
                                    <select class="form-select" id="edit_status" name="status" aria-label="Select Status">
                                        <option selected>Select Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_image">Slide Image</label>
                                    <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                                    <div class="mb-2">
                                        <img class="mt-2" id="edit_image_preview" src="https://i.pinimg.com/1200x/5b/f7/22/5bf722d58d3497843454d4f31b5ec224.jpg" alt="Product Preview" style="width:27rem;height:auto;object-fit:cover;border-radius:4px;border:2px solid #ddd;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="edit_description">Description</label>
                                    <textarea class="form-control" name="description" placeholder="Write Description Here..." id="edit_description" style="height: 100px"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="updateSlide" class="btn btn-outline-success">Update</button>
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
                    <form action="" method="post">
                        <input type="hidden" name="delete_id" id="delete_id">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete" class="btn btn-outline-danger">Delete</button>
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
                var flavor = $(this).data('flavor');
                var description = $(this).data('description');
                var link_page = $(this).data('link_page');
                var link_media = $(this).data('link_media');
                var status = $(this).data('status');
                var imagetype = $(this).data('imagetype');
                var image = $(this).data('image');

                $('#update_id').val(id);
                $('#edit_code').val(code);
                $('#edit_name').val(name);
                $('#edit_flavor').val(flavor);
                $('#edit_description').val(description);
                $('#edit_link_page').val(link_page);
                $('#edit_link_media').val(link_media);
                $('#edit_status').val(status);
                $('#edit_imagetype').val(imagetype);
                var previewSrc = image ? 'images/uploads/slideshows/' + image : 'images/uploads/no-image.png';
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
        });
    </script>
</body>
</html>