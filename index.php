<?php
/**
 * Staff Data Web Service - Documentation Page
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Data Web Service Documentation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
        }
        h1, h2, h3 {
            color: #2c3e50;
        }
        h1 {
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        code {
            background: #f5f5f5;
            padding: 2px 5px;
            border-radius: 3px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .section {
            margin-bottom: 30px;
        }
        .endpoint {
            background: #e9f7fe;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Staff Data Web Service Documentation</h1>
        
        <div class="section">
            <h2>Overview</h2>
            <p>This web service allows clients to submit staff information in a tab-separated format. The service processes the data, archives existing records, and updates the database with the new information.</p>
        </div>
        
        <div class="section">
            <h2>Database Structure</h2>
            <p>The database consists of four tables:</p>
            <ol>
                <li><strong>departments</strong> - Stores department information and contact persons</li>
                <li><strong>staff</strong> - Stores current staff information</li>
                <li><strong>batches</strong> - Tracks submission batches</li>
                <li><strong>staff_archive</strong> - Archives historical staff data</li>
            </ol>
        </div>
        
        <div class="section">
            <h2>API Usage</h2>
            
            <h3>Endpoint</h3>
            <div class="endpoint">POST /staff_service.php</div>
            
            <h3>Request Format</h3>
            <p>The request should be a standard HTTP POST with the following parameter:</p>
            
            <table>
                <tr>
                    <th>Parameter</th>
                    <th>Type</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td>data</td>
                    <td>string</td>
                    <td>Tab-separated text with newlines between staff records</td>
                </tr>
            </table>
            
            <h3>Data Format</h3>
            <p>Each line in the <code>data</code> parameter should contain tab-separated fields in the following order:</p>
            <ol>
                <li>First name</li>
                <li>Surname</li>
                <li>Email address</li>
                <li>Gender (either "m" or "f")</li>
                <li>Department name</li>
                <li>Department contact person name</li>
                <li>Department contact person email address</li>
            </ol>
            
            <h4>Example:</h4>
            <pre>John	Doe	john.doe@example.com	m	Engineering	Jane Smith	jane.smith@example.com
Jane	Smith	jane.smith@example.com	f	Engineering	Jane Smith	jane.smith@example.com</pre>
            
            <h3>Response Format</h3>
            <p>The service returns a JSON response with the following structure:</p>
            
            <pre>{
  "status": "success|error",
  "message": "Description of the result",
  "details": {
    "batch_id": 123,
    "staff_processed": 10,
    "departments_created": 3
  }
}</pre>
            
            <h3>Error Handling</h3>
            <ul>
                <li>HTTP 400: Bad Request - No data provided</li>
                <li>HTTP 405: Method Not Allowed - Only POST method is supported</li>
                <li>HTTP 500: Internal Server Error - Error processing the data</li>
            </ul>
        </div>
        
        <div class="section">
            <h2>Implementation Example</h2>
            <p>Here's a simple PHP example of how to submit data to the service:</p>
            
            <pre>&lt;?php
$staffData = "John\tDoe\tjohn.doe@example.com\tm\tEngineering\tJane Smith\tjane.smith@example.com\n"
          . "Jane\tSmith\tjane.smith@example.com\tf\tEngineering\tJane Smith\tjane.smith@example.com";

$ch = curl_init('http://your-domain.com/staff_service.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ['data' => $staffData]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);
?&gt;</pre>
        </div>
        
        <div class="section">
            <h2>Security Considerations</h2>
            
            <h3>Input Validation and Sanitization</h3>
            <ul>
                <li>The service performs basic input validation for required fields and data types</li>
                <li>All input strings are trimmed to remove leading/trailing whitespace</li>
                <li>Gender values are validated and defaulted to a safe value if invalid</li>
                <li>The service skips invalid records rather than failing the entire batch</li>
            </ul>
            
            <h3>SQL Injection Prevention</h3>
            <ul>
                <li>PDO with prepared statements is used for all database operations</li>
                <li>Parameters are properly bound rather than concatenated into SQL queries</li>
                <li>Database credentials are stored in a separate configuration file</li>
            </ul>
            
            <h3>Transaction Management</h3>
            <ul>
                <li>Database operations use transactions to ensure data integrity</li>
                <li>Rollback functionality is implemented to prevent partial updates in case of errors</li>
            </ul>
            
            <h3>Error Handling</h3>
            <ul>
                <li>Production mode hides detailed error messages from end users</li>
                <li>Errors are caught and handled gracefully with appropriate HTTP status codes</li>
                <li>Error reporting can be configured via the config.php file</li>
            </ul>
            
            <h3>Access Control</h3>
            <ul>
                <li>The service only accepts POST requests to prevent unauthorized data retrieval</li>
                <li>Invalid request methods return appropriate error responses</li>
            </ul>
            
            <h3>Data Archiving</h3>
            <ul>
                <li>Historical data is preserved in an archive table rather than being deleted</li>
                <li>Each submission is tracked with a batch ID for audit purposes</li>
            </ul>
            
            <h3>Additional Recommendations</h3>
            <ul>
                <li>Deploy the service over HTTPS to encrypt data in transit</li>
                <li>Implement API authentication for production use</li>
                <li>Consider adding rate limiting to prevent abuse</li>
                <li>Regularly backup the database to prevent data loss</li>
            </ul>
        </div>
        
        <div class="section">
            <h2>Test Client</h2>
            <p>A test client is available at <a href="test_client.php">test_client.php</a> to help you test the service.</p>
        </div>

        <footer>
            <p><small>&copy; <?php echo date('Y'); ?> Thorn Hill Staff Data Web Service</small></p>
        </footer>
    </div>
</body>
</html>