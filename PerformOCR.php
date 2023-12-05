<?php

include 'vendor/autoload.php';

// perform the OCR
function performOCR($imagePath)
{
    // Specify the path to the Tesseract executable
    $tesseractExecutable = 'C:\xampp\htdocs\pdf-gpt-master\Tesseract-OCR\tesseract.exe';

    // Output file path for the OCR result
    $outputFile = 'output';

    // Run Tesseract OCR
    $command = "{$tesseractExecutable} {$imagePath} {$outputFile}  -l osd";
    shell_exec($command);

    // Read and return the OCR result
    $ocrResult = file_get_contents($outputFile . ".txt");


    return $ocrResult;
}
