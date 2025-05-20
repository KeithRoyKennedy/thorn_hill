<?php
/**
 * Test client for staff data web service
 * 
 * This script demonstrates how to use the staff data web service.
 */

// Sample staff data in tab-separated format
$sampleData = "John\tDoe\tjohn.doe@example.com\tm\tEngineering\tJane Smith\tjane.smith@example.com
Jane\tSmith\tjane.smith@example.com\tf\tEngineering\tJane Smith\tjane.smith@example.com
Bob\tJohnson\tbob.johnson@example.com\tm\tMarketing\tSarah Williams\tsarah.williams@example.com
Sarah\tWilliams\tsarah.williams@example.com\tf\tMarketing\tSarah Williams\tsarah.williams@example.com
Michael\tBrown\tmichael.brown@example.com\tm\tHR\tEmily Davis\temily.davis@example.com
Emily\tDavis\temily.davis@example.com\tf\tHR\tEmily Davis\temily.davis@example.com";

// URL of the web service
$serviceUrl = 'http://localhost/staff_service.php';

// Set up cURL
$ch = curl_init($serviceUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ['data' => $sampleData]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Output the response
echo "HTTP Response Code: $httpCode\n";
echo "Response:\n";
echo $response;
echo "\n";

// Parse and display the JSON response in a more readable format
$jsonResponse = json_decode($response, true);
if ($jsonResponse) {
    echo "\nParsed Response:\n";
    echo "Status: " . $jsonResponse['status'] . "\n";
    echo "Message: " . $jsonResponse['message'] . "\n";
    
    if (isset($jsonResponse['details'])) {
        echo "Batch ID: " . $jsonResponse['details']['batch_id'] . "\n";
        echo "Staff Processed: " . $jsonResponse['details']['staff_processed'] . "\n";
        echo "Departments Created: " . $jsonResponse['details']['departments_created'] . "\n";
    }
}
