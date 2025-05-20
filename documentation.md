# Staff Data Web Service Documentation

## Overview
This web service allows clients to submit staff information in a tab-separated format. The service processes the data, archives existing records, and updates the database with the new information.

## Database Structure
The database consists of four tables:
1. `departments` - Stores department information and contact persons
2. `staff` - Stores current staff information
3. `batches` - Tracks submission batches
4. `staff_archive` - Archives historical staff data

## API Usage

### Endpoint
```
POST /staff_service.php
```

### Request Format
The request should be a standard HTTP POST with the following parameter:

| Parameter | Type   | Description                                                |
|-----------|--------|------------------------------------------------------------|
| data      | string | Tab-separated text with newlines between staff records     |

### Data Format
Each line in the `data` parameter should contain tab-separated fields in the following order:
1. First name
2. Surname
3. Email address
4. Gender (either "m" or "f")
5. Department name
6. Department contact person name
7. Department contact person email address

Example:
```
John	Doe	john.doe@example.com	m	Engineering	Jane Smith	jane.smith@example.com
Jane	Smith	jane.smith@example.com	f	Engineering	Jane Smith	jane.smith@example.com
```

### Response Format
The service returns a JSON response with the following structure:

```json
{
  "status": "success|error",
  "message": "Description of the result",
  "details": {
    "batch_id": 123,
    "staff_processed": 10,
    "departments_created": 3
  }
}
```

### Error Handling
- HTTP 400: Bad Request - No data provided
- HTTP 405: Method Not Allowed - Only POST method is supported
- HTTP 500: Internal Server Error - Error processing the data

## Implementation Example
Here's a simple PHP example of how to submit data to the service:

```php
$staffData = "John\tDoe\tjohn.doe@example.com\tm\tEngineering\tJane Smith\tjane.smith@example.com
Jane\tSmith\tjane.smith@example.com\tf\tEngineering\tJane Smith\tjane.smith@example.com";

$ch = curl_init('http://your-domain.com/staff_service.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ['data' => $staffData]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);
```

## Security Considerations

### Input Validation and Sanitization
- The service performs basic input validation for required fields and data types
- All input strings are trimmed to remove leading/trailing whitespace
- Gender values are validated and defaulted to a safe value if invalid
- The service skips invalid records rather than failing the entire batch

### SQL Injection Prevention
- PDO with prepared statements is used for all database operations
- Parameters are properly bound rather than concatenated into SQL queries
- Database credentials are stored in a separate configuration file

### Transaction Management
- Database operations use transactions to ensure data integrity
- Rollback functionality is implemented to prevent partial updates in case of errors

### Error Handling
- Production mode hides detailed error messages from end users
- Errors are caught and handled gracefully with appropriate HTTP status codes
- Error reporting can be configured via the config.php file

### Access Control
- The service only accepts POST requests to prevent unauthorized data retrieval
- Invalid request methods return appropriate error responses

### Data Archiving
- Historical data is preserved in an archive table rather than being deleted
- Each submission is tracked with a batch ID for audit purposes

### Additional Recommendations
- Deploy the service over HTTPS to encrypt data in transit
- Implement API authentication for production use
- Consider adding rate limiting to prevent abuse
- Regularly backup the database to prevent data loss
