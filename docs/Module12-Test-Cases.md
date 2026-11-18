# Module 12 – Testing and Debugging
## Test Case Documentation

**Project:** Laravel Employee Management System  
**Framework:** PHPUnit  
**Execution command:** `php artisan test`  
**Latest full test result:** 108 passed (382 assertions)  
**Duration:** 33.39 seconds

## Test Cases

| ID | Module | Scenario | Steps | Expected Result | Actual Result | Status |
|---|---|---|---|---|---|---|
| TC-001 | Login | Login with valid email | Submit valid credentials | User is authenticated | Login successful | PASS |
| TC-002 | Login | Login with invalid credentials | Submit incorrect credentials | Login is rejected | Login rejected | PASS |
| TC-003 | Login | Login using username | Submit valid username and password | User is authenticated | Login successful | PASS |
| TC-004 | Login | Inactive user login | Attempt login with inactive account | Access is denied | Access denied | PASS |
| TC-005 | Logout | Authenticated user logout | Log in and submit logout request | Session is invalidated | Logout successful | PASS |
| TC-006 | Authentication | Guest dashboard access | Open dashboard without login | Redirect to login | Redirect verified | PASS |
| TC-007 | Authentication | Unverified user access | Attempt dashboard access | Access is denied | Access denied | PASS |
| TC-008 | Authorization | Employee accesses admin list | Log in as employee and open admin list | HTTP 403 | Access denied | PASS |
| TC-009 | Authorization | Admin accesses employee list | Log in as admin and open employee list | Access granted | Access granted | PASS |
| TC-010 | Employee CRUD | Create employee with linked account | Submit valid employee details | Employee and user are created | Creation successful | PASS |
| TC-011 | Employee CRUD | Reject forged user ID | Submit forged user ID during creation | Server assigns user ID securely | Forged ID ignored | PASS |
| TC-012 | Employee CRUD | Update employee profile | Submit updated profile details | Updated information is saved | Update successful | PASS |
| TC-013 | Employee CRUD | Delete employee account | Admin deletes employee account | Account is deleted and employee record is preserved as designed | Deletion behavior verified | PASS |
| TC-014 | Department CRUD | Reject duplicate department | Create department with existing name | Validation error | Duplicate rejected | PASS |
| TC-015 | Department CRUD | Require department name | Submit department without name | Validation error | Validation enforced | PASS |
| TC-016 | Department CRUD | Unauthorized department update | Attempt update as non-admin | HTTP 403 | Access denied | PASS |
| TC-017 | Validation | Reject invalid employee email | Submit invalid email | Validation error | Invalid email rejected | PASS |
| TC-018 | Validation | Reject negative salary | Submit negative salary | Validation error | Negative salary rejected | PASS |
| TC-019 | Validation | Reject duplicate employee code | Submit existing employee code | Validation error | Duplicate rejected | PASS |
| TC-020 | Validation | Reject underage employee | Submit employee under 18 | Age validation error | Invalid age rejected | PASS |
| TC-021 | File Upload | Upload valid profile photo | Upload valid image | File is stored successfully | Upload successful | PASS |
| TC-022 | File Upload | Reject invalid profile photo | Upload unsupported file type | Validation error | Invalid upload rejected | PASS |
| TC-023 | File Upload | Unauthorized photo access | Employee requests another employee's photo | HTTP 403 | Access denied | PASS |
| TC-024 | Search | Search employee by name | Enter employee name | Matching employee is returned | Search successful | PASS |
| TC-025 | Search | Search employee by code | Enter employee code | Matching employee is returned | Search successful | PASS |
| TC-026 | Pagination | Employee listing pagination | Request employee listing with multiple records | Results are paginated | Pagination verified | PASS |
| TC-027 | API | Employee API JSON structure | Send authenticated GET request | Paginated JSON structure is returned | JSON structure verified | PASS |
| TC-028 | API | Invalid API pagination value | Send invalid per_page parameter | HTTP 422 | Invalid parameter rejected | PASS |
| TC-029 | Security | Unauthenticated API request | Request protected API endpoint as guest | Unauthorized response | Request rejected | PASS |
| TC-030 | Security | POST without CSRF token | Submit POST request without CSRF token | Request is rejected | Request rejected | PASS |

## Test Execution Evidence

Latest full test execution:

```text
Tests:    108 passed (382 assertions)
Duration: 33.39s
```

Command:

```powershell
php artisan test
```

**Result:** All tests passed in the latest recorded execution.

**Note:** The actual results summarize the corresponding automated test scenarios. They are not separate manual executions of all 30 cases.



---

## Module 13 – Caching, Queues, Mail, Events, and Task Scheduling

### Implementation Verification

| ID | Area | Verification | Expected Result | Observed Result | Status |
|---|---|---|---|---|---|
| M13-001 | Caching | Dashboard statistics use Cache::remember() | Statistics are cached | Implementation verified | PASS |
| M13-002 | Cache Invalidation | Employee and department changes invalidate dashboard cache | Stale statistics are cleared | Implementation verified | PASS |
| M13-003 | Queues | Employee welcome email is dispatched through a queued listener | Email is processed asynchronously | Queue processing verified | PASS |
| M13-004 | Failed Jobs | Test job failure, retry, and cleanup | Failed job can be retried and removed | Retry and cleanup verified | PASS |
| M13-005 | Mail | Welcome, birthday, and password setup email views | Email content renders correctly | Mail rendering verified | PASS |
| M13-006 | Notifications | Mail and database notifications | Notifications are stored and displayed | Notification workflow verified | PASS |
| M13-007 | Events | EmployeeCreated event dispatches after transaction commit | Registered listeners execute after successful creation | Implementation verified | PASS |
| M13-008 | Activity Logging | Employee activity is recorded by the observer | Employee changes are logged | Implementation verified | PASS |
| M13-009 | Artisan Commands | Database backup and birthday reminder commands | Commands execute successfully | Manual command execution verified | PASS |
| M13-010 | Scheduling | Scheduled commands are registered and triggered by Windows Task Scheduler | Scheduled tasks are checked automatically | Scheduler configuration and execution verified | PASS |
| M13-011 | Carbon | Employee age and service duration calculations | Date calculations return expected values | Tinker verification completed | PASS |
| M13-012 | Collections and Helpers | collect(), map(), filter(), groupBy(), and str() | Collection and string operations work correctly | Tinker verification completed | PASS |
| M13-013 | Localization | Navigation, dashboard, and navbar translations | Translated labels appear correctly | UI verification completed | PASS |

**Note:** Module 13 entries document implementation and manual verification. They are not claims that each row corresponds to a separate PHPUnit test.

