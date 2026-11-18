# Module 12 – Bug Reports

## BUG-001: Homepage Displays Incorrect Welcome Page

- **Module:** Routing and Authentication
- **Severity:** Medium
- **Status:** Fixed

### Description
The homepage displayed the Laravel welcome page instead of redirecting guests to the login page.

### Steps to Reproduce
1. Log out of the application.
2. Open the application homepage (`/`).
3. Observe the page displayed to the guest user.
4. Check whether Laravel's default welcome page appears instead of the login redirect.

### Root Cause
A duplicate root route was defined in `routes/console.php`, conflicting with the intended homepage route in `routes/web.php`.

### Fix
Removed the duplicate root route from `routes/console.php` while preserving the scheduled database backup.

### Verification
- Root route now points to `routes/web.php`.
- Guest homepage request returns HTTP 302.
- Homepage redirect feature test passes.

---

## BUG-002: Profile Photo Deleted After Old Photo Cleanup Failure

- **Module:** Employee Profile Management
- **Severity:** Medium
- **Status:** Fixed

### Description
When an employee updated their profile photo, failure to delete the previous photo caused the exception handler to delete the newly uploaded photo, even though the database transaction had already committed the new photo path.

### Steps to Reproduce
1. Log in as an employee.
2. Ensure the employee has an existing profile photo.
3. Open the employee profile update form.
4. Upload a replacement profile photo.
5. Simulate a failure when deleting the previous photo.
6. Submit the profile update.
7. Check whether the newly uploaded photo still exists.
8. Verify that the database references the new photo.

### Root Cause
Old-photo deletion was inside the main try block. Its exception was incorrectly handled as a profile-update failure.

### Fix
Moved old-photo deletion into a separate try-catch block. Cleanup failures are logged without deleting the new photo.

### Verification
Regression test:
`test_old_photo_cleanup_failure_does_not_delete_new_profile_photo`

Result: PASS (7 assertions).

Full WebAuthenticationTest:
19 passed (91 assertions).

---

## BUG-003: API Employee Photo Deleted After Cleanup Failure

- **Module:** API Employee Management
- **Severity:** Medium
- **Status:** Fixed

### Description
When an API employee update succeeded but deleting the old profile photo failed, the exception handler deleted the newly uploaded photo and returned HTTP 500.

### Steps to Reproduce
1. Authenticate as an administrator using the API.
2. Select an existing employee with a profile photo.
3. Submit an API PUT request to update the employee.
4. Include a replacement profile photo.
5. Simulate failure when deleting the old photo.
6. Check the API response.
7. Verify that the new photo exists and the employee update succeeded.

### Root Cause
Old-photo deletion was inside the same try-catch block as the employee database update.

### Fix
Separated old-photo cleanup from database update exception handling. Cleanup failures are logged without deleting the new photo or failing the API response.

### Verification
Regression test:
`test_api_old_photo_cleanup_failure_does_not_delete_new_photo`

Result: PASS (6 assertions).

PHP syntax check: Passed.

---

## BUG-004: Employee Photo Deleted After Old Photo Cleanup Failure

- **Module:** Admin Employee Management
- **Severity:** Medium
- **Status:** Fixed

### Description
When an employee update succeeded but deleting the old profile photo failed, the exception handler also deleted the newly uploaded photo. This left the database referencing a missing file.

### Steps to Reproduce
1. Log in as an administrator.
2. Open the employee management section.
3. Create an employee with an existing profile photo.
4. Open the employee edit form.
5. Upload a replacement profile photo.
6. Simulate failure when deleting the old photo.
7. Submit the update.
8. Verify that the new photo still exists and the database references it.

### Root Cause
Old-photo deletion was inside the same try-catch block as the employee database update.

### Fix
Separated old-photo cleanup from database update exception handling. Cleanup failures are logged without deleting the new photo or failing the employee update.

### Verification
Regression test:
`test_old_photo_cleanup_failure_does_not_delete_new_employee_photo`

Result: PASS (3 assertions).

PHP syntax check: Passed.

---

## BUG-005: False Employee Deletion Failure After Photo Cleanup Error

- **Module:** Admin Employee Management
- **Severity:** Medium
- **Status:** Fixed

### Description
When employee and linked user deletion succeeded but profile photo cleanup failed, the controller returned an error message even though the database operation had completed successfully.

### Steps to Reproduce
1. Log in as an administrator.
2. Create an employee with a linked user account and profile photo.
3. Open the employee management section.
4. Initiate deletion of the employee.
5. Simulate failure when deleting the employee's profile photo.
6. Check the deletion response.
7. Verify that the employee and linked user deletion completed successfully.
8. Confirm that the application does not incorrectly report the database deletion as failed.

**Note:** BUG-002 through BUG-005 reproduce simulated storage failures in automated tests. The Mockery tests are what reliably trigger these failures.

### Root Cause
Profile photo deletion was inside the same try-catch block as the database transaction.

### Fix
Separated photo cleanup from database deletion. Cleanup failures are logged without reporting the completed employee deletion as failed.

### Verification
Regression test:
`test_photo_cleanup_failure_does_not_report_employee_deletion_as_failed`

Result: PASS (7 assertions).

PHP syntax check: Passed.
