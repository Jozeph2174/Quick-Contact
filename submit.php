<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $website = htmlspecialchars($_POST['website']);
    $message = htmlspecialchars($_POST['message']);

    // Check if the same message has already been submitted by the user
    $checkQuery = "SELECT * FROM submissions WHERE email = ? AND message = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $email, $message);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Allow new submission with the same email, phone, website but different message
        $insertQuery = "INSERT INTO submissions (name, email, phone, website, message) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sssss", $name, $email, $phone, $website, $message);

        // Execute and check for errors
        if ($stmt->execute()) {
            echo  "<div style='background-color: #4CAF50; color: white; padding: 15px; border-radius: 5px; text-align: center; font-size: 1.2em; margin-top: 20px; font-weight: bold;'>Thank you, $name! Your message has been sent.</div>";
        } else {
            echo "<div style='background-color: #f44336; color: white; padding: 15px; border-radius: 5px; text-align: center; font-size: 1.2em; margin-top: 20px; font-weight: bold;'>Error: " . $stmt->error . "</div>";
        }
    } else {
        echo "<div style='background-color: #f44336; color: white; padding: 15px; border-radius: 5px; text-align: center; font-size: 1.2em; margin-top: 20px; font-weight: bold;'>You have already submitted this exact message. Please submit a new one.</div>";
    }

    $stmt->close();
    $conn->close();
}
?>
