<?php
    include 'include/dbconnection.php';

    // Add Unit
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addUnit']) == TRUE){
        $code = $_POST['code'];
        $name = $_POST['name'];
        $full_name = $_POST['full_name'];
        $created_date = $_POST['created_date'];

        $stmt = $conn->prepare("INSERT INTO units (code, name, full_name, created_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $code, $name, $full_name, $created_date);

        if ($stmt->execute()) {
            $_SESSION['message'] = 'Unit added Successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Unit added Unsuccessfully!';
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: unit.php');
        exit();
    }

    // Update Unit
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateUnit']) == TRUE){
        $id = $_POST['id'];
        $created_date = $_POST['created_date'];
        $code = $_POST['code'];
        $name = $_POST['name'];
        $full_name = $_POST['full_name'];

        $stmt = $conn->prepare("UPDATE units SET code = ?, name = ?, full_name = ?, created_date = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $code, $name, $full_name, $created_date, $id);

        if ($stmt->execute()) {
            $_SESSION['message'] = 'Unit updated Successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Unit updated Unsuccessfully!';
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: unit.php');
        exit();
    }

    //delete Unit 
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteUnit']) == TRUE){
        $delete_id = $_POST['delete_id'];
        $stmt = $conn->prepare("DELETE FROM units WHERE id = ?");
        $stmt->bind_param("i", $delete_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = 'Unit deleted Successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Unit deleted Unsuccessfully!';
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: unit.php');
        exit();
    }

    $units = $conn->query("SELECT * FROM units ORDER BY created_date DESC");

    $close = $conn->close();
?>