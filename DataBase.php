<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = 'pfdextracteddata';

// Function to save data to MySQL database
function saveToDatabase($pdfFile, $desc, $reportNumber, $analysisNames, $analysisRating, $analyticalSummary)
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
        die('Connection failed: ' . mysqli_connect_error());
    }

    // Escape values to prevent SQL injection
    $reportNumber = $conn->real_escape_string($reportNumber);
    $desc = $conn->real_escape_string($desc);
    $analyticalSummary = $conn->real_escape_string($analyticalSummary);
    $pdfFile = $conn->real_escape_string($pdfFile);

    // Insert into the database
    $sql = "INSERT INTO `report` (`report_number`, `description`, `filename`, `summary`)
            VALUES ('$reportNumber', '$desc', '$pdfFile', '$analyticalSummary')";


    if (mysqli_query($conn, $sql)) {
        // Retrieve the last inserted id from the report table
        $lastInsertId = mysqli_insert_id($conn);

        // Loop through each element in analysisNames and analysisRatings arrays
        foreach ($analysisNames as $index => $analysisName) {
            $rating = isset($analysisRating[$index]) ? $analysisRating[$index] : null;

            // Escape values to prevent SQL injection
            $analysisName = $conn->real_escape_string($analysisName);
            $rating = $conn->real_escape_string($rating);

            $sql2 = "INSERT INTO `result_project` (`report_id`, `analysis_name`, `rating`)
            VALUES ('$lastInsertId', '$analysisName', '$rating')";

            if (mysqli_query($conn, $sql2)) {
            } else {
                echo 'Error: ' . $sql2 . '<br>' . mysqli_error($conn);
            }
        }
    } else {
        echo 'Error: ' . $sql . '<br>' . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}
