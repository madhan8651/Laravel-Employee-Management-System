# Employee Management System – REST API Documentation

## Base URL

http://127.0.0.1:8000/api

## Authentication

The API uses Laravel Sanctum Bearer token authentication.

### Login
- Method: POST
- Endpoint: `/login`
- Authentication: Not required
- Success: 200 OK
- Validation failure: 422 Unprocessable Content

Send the user's email, password, and device name. The response provides an access token.

For protected endpoints, include:

`Authorization: Bearer YOUR_ACCESS_TOKEN`

### Logout
- Method: POST
- Endpoint: `/logout`
- Authentication: Required
- Success: 200 OK

## Employee endpoints

| Method | Endpoint | Purpose | Success |
|---|---|---|---|
| GET | `/employees` | List employees | 200 |
| GET | `/employees/{id}` | View an employee | 200 |
| POST | `/employees` | Create an employee | 201 |
| PUT | `/employees/{id}` | Update an employee | 200 |
| DELETE | `/employees/{id}` | Soft-delete an employee | 204 |

All employee endpoints require authentication.

### Authorization

- Admin users can list, view, create, update, and delete employees.
- Employee users can view only their own employee record.
- Employee users cannot create, update, or delete employee records.
- Unauthorized requests return 401.
- Authenticated users without permission receive 403.

## Search, filters and pagination

The employee listing endpoint supports these query parameters:

| Parameter | Purpose |
|---|---|
| `search` | Search employees |
| `department_id` | Filter by department |
| `status` | Filter by active, inactive, or terminated |
| `per_page` | Set the number of records per page |

Examples:

- `/employees?search=Madhan`
- `/employees?status=active`
- `/employees?department_id=4`
- `/employees?per_page=5`
- `/employees?department_id=4&status=active&per_page=5`

Invalid filter values return 422.

The listing response includes employee data, pagination links, and pagination metadata.

## HTTP status codes

| Code | Meaning |
|---|---|
| 200 | Request successful |
| 201 | Resource created |
| 204 | Resource deleted; no response body |
| 401 | Authentication required or invalid |
| 403 | Permission denied |
| 404 | Resource not found |
| 422 | Validation failed |
| 429 | Too many requests |
| 500 | Internal server error |

## Rate limiting and CORS

- API requests are rate-limited to 60 requests per minute per authenticated user or IP.
- Login has a separate limit of 5 failed attempts per minute.
- CORS is configured for the local frontend origins:
  - `http://localhost:3000`
  - `http://127.0.0.1:3000`

## API response format

Successful employee listing responses contain:
- `data`: Employee records
- `links`: Pagination navigation links
- `meta`: Pagination information
- `status`: Operation status
- `message`: Response message

Validation errors return HTTP 422 with a message and field-level errors.

## API versioning

The current endpoints use the `/api` prefix.

For a future version, routes can be grouped under:

`/api/v1`

For example:

`http://127.0.0.1:8000/api/v1/employees`

Versioning allows a new API implementation to be introduced without immediately breaking clients that still depend on the existing API.

## Automated testing

The Postman collection is stored at:

`postman/Employee-Management-API.postman_collection.json`

The Newman JSON test report is stored at:

`postman/reports/employee-api-test-report.json`

Latest test execution:
- Requests: 14
- Assertions: 15
- Failures: 0
