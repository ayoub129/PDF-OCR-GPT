<?php

include 'vendor/autoload.php';

// perform the OCR
function performOCR($imagePath)
{

    // Specify the path to the Tesseract executable
    $tesseractExecutable = __DIR__ . 'Tesseract-OCR\tesseract.exe';

    // Output file path for the OCR result with timestamp
    $outputFile = 'output';

    // Run Tesseract OCR
    $command = "{$tesseractExecutable} {$imagePath} {$outputFile}  -l ita";
    shell_exec($command);


    // Read and return the OCR result
    $ocrResult = file_get_contents($outputFile . ".txt");
    echo file_get_contents($outputFile . ".txt");

    return $ocrResult;
}
