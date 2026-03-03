<?php
    // Start the session (if needed)
    session_start();

    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "foodmart";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the theme value from the POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['theme'])) {
        $theme = intval($_POST['theme']); // Convert to integer for safety

        // Validate the theme value (must be 0 or 1)
        if ($theme === 0 || $theme === 1) {
            // Assume you have a configurations table with a `theme` column
            // Insert or update the theme value
            $stmt = $conn->prepare("UPDATE configurations SET theme = ? WHERE id = ?");
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($conn->error));
            }

            $stmt->bind_param("i", $theme); // Bind the theme value twice (for INSERT and UPDATE)
            if ($stmt->execute()) {
                echo "Theme updated successfully.";
            } else {
                echo "Error updating theme.";
            }

            $stmt->close();
        } else {
            echo "Invalid theme value.";
        }
    } else {
        echo "Invalid request.";
    }

// Close the connection
$conn->close();
?>