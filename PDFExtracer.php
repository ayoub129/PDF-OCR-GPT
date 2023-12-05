<?php

include 'vendor/autoload.php';

function extractTextFromPDF($filename)
{

    // Initialize and load PDF Parser library 
    $parser = new \Smalot\PdfParser\Parser();

    // Parse pdf file using Parser library 
    $pdf = $parser->parseFile($filename);

    // Extract text from PDF 
    $pages = count($pdf->getPages());

    if ($pages < 22) {
        $textContent = $pdf->getText();
    } else {
        $textContent = null;
    }


    return $textContent;
}
