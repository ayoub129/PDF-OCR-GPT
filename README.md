# PDF TO GPT-3.5-TURBO-1106

## Overview

**pdf-gpt** is a script designed to analyze PDFs located in the "pdfs" folder. The script reads PDFs one by one and utilizes GPT-3.5-Turbo to extract key information, including:

1. **Report Number**
2. **Analytical Names**
3. **Ratings**
4. **Summary**

The extracted data is then stored in a database with two tables:

1. **report**
2. **result_project**

## Prerequisites

### 1. Install Tesseract-OCR exe file

You need to install Tesseract-OCR exe file and run it in the folder.

### 2. Set Up Database

1. Create a database named **pfdextracteddata**.
2. Import the SQL file provided.

### 3. Obtain GPT Key

1. Get your GPT key.
2. Use it in the `$api_key` variable within the `pdfread.php` file.

## Usage

2. Ensure Poppler is installed and added to your environment variables.
3. Set up the database by creating **pfdextracteddata** and importing the SQL file.
4. Obtain a GPT key and update the `$api_key` variable in `pdfread.php`.
5. Run the script to process PDFs and store the extracted information in the database.
