<?php
require "PDFExtracter.php";
require "ChatGPT.php";
require "DataBase.php";
require "PerformOcr.php";

// Specify the folder where your PDF files are located
$folderPath = './pdfs';
$errorFolder = "./error";
$archiveFolder = "./archive";

// Get all PDF files in the specified folder
$files = glob("{$folderPath}/*.{pdf,png,jpg,jpeg}", GLOB_BRACE);

$api_key = "";

// Process each PDF file in the folder
foreach ($files as $file) {
    // Determine the file type
    $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    // Initialize variables for the results
    $textFromFile = null;
    // Check the file type and perform appropriate actions
    switch ($fileExtension) {
        case 'pdf':
            // Extract text from PDF
            $textFromPDF = extractTextFromPDF($file);
            echo $textFromPDF;
            break;
        case 'png':
        case 'jpg':
        case 'jpeg':
            // Perform OCR for image files
            $textFromFile = performOCR($file);
            echo $textFromFile;
            break;
        default:
            // Handle unsupported file types or log an error
            echo "Unsupported file type: {$fileExtension}\n";
            break;
    }

    if ($textFromFile != null) {
        $returnedText = ReqResGPT($api_key, $textFromFile, null);
    } else {
        $returnedText = ReqResGPT($api_key, null, $file);
    }


    // Extracting report number
    $reportNumber = preg_match('/Date: (.+)/', $returnedText, $matches) ? $matches[1] : null;

    // Extracting the item description
    $desc = preg_match('/Person Name: (.*)/s', $returnedText, $matches) ? $matches[1] : null;


    // Extracting analytical summary
    $analyticalSummary = preg_match('/General Summary:\n(.+)/', $returnedText, $matches) ? $matches[1] : null;


    // Name and Ratings
    $NamesAndRatingsText = preg_match('/Analysis and Values:\n(.+)/s', $returnedText, $matches) ? $matches[1] : null;

    print_r($NamesAndRatingsText);
    // Split the text into lines
    $lines = explode("\n", $NamesAndRatingsText);

    // Initialize arrays to store analytical names and ratings
    $analyticalNames = [];
    $ratings = [];

    // Iterate through each line
    foreach ($lines as $line) {

        // Use regular expression to match analytical names and ratings
        preg_match('/(.+): (.+)/', $line, $matches);

        // If a match is found, store the values in the arrays
        if (count($matches) === 3) {
            $analyticalNames[] = $matches[1];
            $ratings[] = $matches[2];
        }
    }

    echo $reportNumber;
    print_r($analyticalNames);
    print_r($ratings);
    echo $analyticalSummary;

    // Check if all necessary data is available before saving to the database
    if ($reportNumber !== null && !empty($analyticalNames) && !empty($ratings) && $analyticalSummary !== null) {
        // Save To database
        $timestamp = time();
        $newFileName = pathinfo($file, PATHINFO_FILENAME) . "_{$timestamp}." . pathinfo($file, PATHINFO_EXTENSION);

        saveToDatabase($newFileName, $desc, $reportNumber, $analyticalNames, $ratings, $analyticalSummary);

        echo "Data inserted successfully";

        // Move the processed file to the archive folder
        $archiveFilePath = "{$archiveFolder}/" . basename($newFileName);
        rename($file, $archiveFilePath);
    } else {
        $timestamp = time();
        $newFileName = pathinfo($file, PATHINFO_FILENAME) . "_{$timestamp}." . pathinfo($file, PATHINFO_EXTENSION);
        // Move the file to the error folder
        $errorFilePath = "{$errorFolder}/" . basename($newFileName);
        rename($file, $errorFilePath);
        echo "you have an error";
    }
}
