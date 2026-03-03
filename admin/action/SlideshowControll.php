<?php
    include 'include/dbconnection.php';

    // Add Slideshow
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addSlide']) == TRUE){
        $code = $_POST['code'];
        $name = $_POST['name'];
        $flavor = $_POST['flavor'];
        $description = $_POST['description'];
        $link_page = $_POST['link_page'];
        $link_media = $_POST['link_media'];
        $status = $_POST['status'];
        $imagetype = $_POST['imagetype'];

        if (isset($_FILES['image'])) {
            $image = $_FILES['image'];
            $encryptedName = md5(time() . $image['name']) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
            $targetDir = "images/uploads/slideshows/";
            $targetFilePath = $targetDir . $encryptedName;
    
            if (move_uploaded_file($image['tmp_name'], $targetFilePath)) {
                $stmt = $conn->prepare("INSERT INTO slideshows (code, name, flavor, description, link_page, link_media, status, imagetype, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssss", $code, $name, $flavor, $description, $link_page, $link_media, $status, $imagetype, $encryptedName);
    
                if ($stmt->execute()) {
                    $_SESSION['message'] = 'Slideshow added successfully!';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = "Error: " . $stmt->error;
                    $_SESSION['message_type'] = 'danger';
                }
                $stmt->close();
                header('Location: slideshow.php');
                exit();
            } else {
                $_SESSION['message'] = 'Error uploading image!';
                $_SESSION['message_type'] = 'danger';
                header('Location: slideshow.php');
                exit();
            }
        } else {
            $_SESSION['message'] = 'No image uploaded!';
            $_SESSION['message_type'] = 'danger';
            header('Location: slideshow.php');
            exit();
        }
    }

    // Update Product
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateSlide']) == TRUE) {
        $update_id = $_POST['update_id'];
        $code = $_POST['code'];
        $name = $_POST['name'];
        $flavor = $_POST['flavor'];
        $description = $_POST['description'];
        $link_page = $_POST['link_page'];
        $link_media = $_POST['link_media'];
        $status = $_POST['status'];
        $imagetype = $_POST['imagetype'];

        $stmt = $conn->prepare("SELECT image_path FROM slideshows WHERE id = ?");
        $stmt->bind_param("i", $update_id);
        $stmt->execute();
        $stmt->bind_result($oldImagePath);
        $stmt->fetch();
        $stmt->close();
    
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image = $_FILES['image'];
            $encryptedName = md5(time() . $image['name']) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
            $targetDir = "images/uploads/slideshows/";
            $targetFilePath = $targetDir . $encryptedName;

            if (move_uploaded_file($image['tmp_name'], $targetFilePath)) {
                if (!empty($oldImagePath)) {
                    $oldFilePath = $targetDir . $oldImagePath;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                $stmt = $conn->prepare("UPDATE slideshows SET code = ?, name = ?, flavor = ?, description = ?, link_page = ?, link_media = ?, status = ?, imagetype = ?, image_path = ? WHERE id = ?");
                $stmt->bind_param("sssssssisi", $code, $name, $flavor, $description, $link_page, $link_media, $status, $imagetype, $encryptedName, $update_id);
            } else {
                $_SESSION['message'] = 'Error uploading image!';
                $_SESSION['message_type'] = 'danger';
                header('Location: slideshow.php');
                exit();
            }
        } else {
            $stmt = $conn->prepare("UPDATE slideshows SET code = ?, name = ?, flavor = ?, description = ?, link_page = ?, link_media = ?, status = ?, imagetype = ? WHERE id = ?");
            $stmt->bind_param("sssssssii", $code, $name, $flavor, $description, $link_page, $link_media, $status, $imagetype, $update_id);
        }
    
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Slideshows updated successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: slideshow.php');
        exit();
    }
    
    // Delete Product
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        $delete_id = $_POST['delete_id'];
        $stmt = $conn->prepare("SELECT image_path FROM slideshows WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->bind_result($imagePath);
        $stmt->fetch();
        $stmt->close();
        $stmt = $conn->prepare("DELETE FROM slideshows WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
    
        if ($stmt->execute()) {
            if (!empty($imagePath)) {
                $filePath = $imagePath;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $_SESSION['message'] = 'Slideshow deleted successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to delete Slideshow!';
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: slideshow.php');
        exit();
    }

    $sql = "SELECT * FROM slideshows ORDER BY code DESC";
    $result = $conn->query($sql);

    $conn->close();
?>