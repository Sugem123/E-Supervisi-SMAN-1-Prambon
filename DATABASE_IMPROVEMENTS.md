# Database Improvements Documentation

## Overview

This document describes the improvements made to the database design to better integrate the `users` and `guru` tables with automatic user creation and role management.

## Key Features

1. **Automatic User Creation**: When a new `guru` record is created, a corresponding `users` record is automatically created via a database trigger.
2. **Role Management**: When a guru is promoted to supervisor (or demoted back to guru), the corresponding user's role is automatically updated.
3. **Email as Login**: The system now uses email as the primary login method instead of username.
4. **Prevention of Duplicate Accounts**: The system prevents creation of duplicate user accounts.

## Database Schema Changes

### Users Table
- Made `email` field required and unique
- Set default role to 'guru'
- Removed the need for username field for login

### Guru Table
- Added `is_supervisor` field (TINYINT, default 0)
- Maintained foreign key relationship with `users.id`

## Triggers

### 1. Auto-create User on Guru Insert (`trg_after_guru_insert`)
This trigger automatically creates a user when a new guru is inserted:

- Creates a unique default email using the format `guru{ID}@example.com`
- Sets a default password (must be changed by admin or user)
- Sets the role based on the `is_supervisor` field value
- Updates the guru record with the new user ID

### 2. Update Role on Supervisor Status Change (`trg_after_guru_update`)
This trigger updates the user's role when the guru's supervisor status changes:

- Promotes a guru to supervisor when `is_supervisor` changes from 0 to 1
- Demotes a supervisor back to guru when `is_supervisor` changes from 1 to 0

## Implementation Files

1. Migration file: `app/Database/Migrations/2025-10-29-000000_ImplementUserGuruIntegration.php`
2. Controller: `app/Controllers/Admin/GuruController.php`
3. Views: `app/Views/admin/guru/` (index.php, create.php, edit.php)
4. Model updates: `app/Models/GuruModel.php`

## Usage

### Creating a New Guru
1. Navigate to Admin > Data Guru > Tambah Guru
2. Fill in the guru details including name and email
3. Optionally check "Jadikan sebagai Supervisor" to create the guru as a supervisor
4. Submit the form

When the form is submitted:
- A new record is created in the `guru` table
- The trigger automatically creates a corresponding record in the `users` table
- The user's email is updated to the provided email address

### Promoting/Demoting a Guru
1. Navigate to Admin > Data Guru
2. Find the guru you want to modify
3. Click the "Angkat sebagai Supervisor" (up arrow) button to promote, or
   "Turunkan sebagai Guru" (down arrow) button to demote
4. The user's role will be automatically updated

### Editing Guru Details
1. Navigate to Admin > Data Guru
2. Click the edit icon for the guru you want to modify
3. Update the details as needed
4. Save the changes

## Best Practices

1. **Email Management**: When changing a guru's email, it's recommended to send a password reset or verification email for security.
2. **Default Passwords**: The default password should be changed immediately after account creation.
3. **Role Changes**: Changing the `is_supervisor` field automatically updates the user's role, ensuring consistency.

## Security Considerations

1. The default password hash should be replaced with a more secure method in production.
2. Consider implementing a password reset flow when creating new accounts.
3. Email changes should trigger verification processes to ensure security.