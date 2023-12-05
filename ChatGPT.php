<?php
function ReqResGPT($api_key, $input_text, $pdfFilePath)
{
    // Set the OpenAI endpoint for chat completions
    $endpoint = 'https://api.openai.com/v1/chat/completions';

    // Set the headers for the API request
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
    ];

    if ($pdfFilePath != null) {
        // Initialize and load PDF Parser library
        $parser = new \Smalot\PdfParser\Parser();

        // Specify the number of pages to process in each chunk
        $pagesPerChunk = 22;

        // Parse pdf file using Parser library
        $pdf = $parser->parseFile($pdfFilePath);

        // Get the total number of pages in the PDF
        $totalPages = count($pdf->getPages());

        // Calculate the number of chunks
        $numChunks = ceil($totalPages / $pagesPerChunk);

        // Initialize an array to store the response from each chunk
        $responses = [];

        // Iterate through each chunk
        for ($chunkNumber = 0; $chunkNumber < $numChunks; $chunkNumber++) {
            // Calculate the starting page for the current chunk
            $startPage = $chunkNumber * $pagesPerChunk + 1;

            // Calculate the ending page for the current chunk
            $endPage = min(($chunkNumber + 1) * $pagesPerChunk, $totalPages);

            // Extract text from the specified pages
            $chunkText = extractTextFromPDFPages($pdf, $startPage, $endPage);

            // Set the data for the API request
            $data = [
                'model' => 'gpt-3.5-turbo-1106',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    ['role' => 'user', 'content' => "Analyze the following text and provide a date of performed blood analysis and the name of the person , and could you tell me the blood analysis (tests) Names and their values found (all of them in the text) ,and the general summary from the text and i need the response to be like this: Date: (the date)\n\Person Name:\n(person name)\n\nGeneral Summary:\n (the summary)\n\nAnalysis and Values:\n(the analysis): (the values) \n\n" . $chunkText],
                ],
                'max_tokens' => 1000,  // You can adjust this based on the desired length of the completion
            ];

            // Initialize cURL session
            $ch = curl_init($endpoint);

            // Set cURL options
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            // Execute cURL session and get the response
            $response = curl_exec($ch);
            print_r($response);
            // Check for cURL errors
            if (curl_errno($ch)) {
                echo 'Curl error: ' . curl_error($ch);
                // Handle the error as needed
            }

            // Close cURL session
            curl_close($ch);

            // Decode the JSON response
            $response_data = json_decode($response, true);

            // Check if 'choices' key exists and is not null
            if (isset($response_data['choices']) && !empty($response_data['choices'])) {
                $choices = $response_data['choices'];
                $generated_text = $choices[0]['message']['content'];
                // Extracted content from the user message
                $responses[] = $generated_text;
            } else {
                echo "No choices in the response.\n";
            }
        }

        // Combine responses from chunks into a single string
        $combined_response = implode('', $responses);

        return $combined_response;
    } else {
        // Set the data for the API request
        $data = [
            'model' => 'gpt-3.5-turbo-1106',  // Specify the chat model
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => "Analyze the following text and provide a date of performed blood analysis and the name of the person , and could you tell me the blood analysis (tests) Names and their values found (all of them in the text) ,and the general summary from the text and i need the response to be like this: Date: (the date)\n\Person Name:\n(person name)\n\nGeneral Summary:\n (the summary)\n\nAnalysis and Values:\n(the analysis): (the values) \n\n" . $input_text],
            ],
            'max_tokens' => 1000,  // You can adjust this based on the desired length of the completion
        ];

        // Initialize cURL session
        $ch = curl_init($endpoint);

        // Set cURL options
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Execute cURL session and get the response
        $response = curl_exec($ch);
        print_r($response);
        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
            // Handle the error as needed
        }

        // Close cURL session
        curl_close($ch);

        // Decode the JSON response
        $response_data = json_decode($response, true);

        // Check if 'choices' key exists and is not null
        if (isset($response_data['choices']) && !empty($response_data['choices'])) {
            $choices = $response_data['choices'];
            $generated_text = $choices[0]['message']['content'];
            // Extracted content from the user message
            return $generated_text;
        } else {
            echo "No choices in the response.\n";
        }
    }
}

// Function to extract text from specific pages in a PDF using PDF Parser
function extractTextFromPDFPages($pdf, $startPage, $endPage)
{
    $text = '';

    foreach ($pdf->getPages() as $pageNumber => $page) {
        if ($pageNumber >= $startPage && $pageNumber <= $endPage) {
            $text .= $page->getText();
        }
    }

    return $text;
}
