# Requirements Document

## Introduction

The User Authentication module provides secure login and session management for the Web-Based Market Management System (WBMM). It validates user credentials against the MySQL database, establishes a session upon successful login, exposes user role and name to downstream modules (e.g., Dashboard), and protects all non-public routes via a CodeIgniter 4 auth filter. It also includes login error handling and a password recovery flow.

## Glossary

- **WBMM**: Web-Based Market Management System — the public market management application.
- **Auth_Controller**: The CodeIgniter 4 controller responsible for handling login, logout, and password recovery requests.
- **Auth_Filter**: The CodeIgniter 4 HTTP filter that intercepts requests to protected routes and enforces authentication.
- **Session**: The server-side PHP session managed by CodeIgniter 4's Session library, stored using the configured session driver.
- **User**: A registered system account stored in the `users` table with a role of either `admin` or `staff`.
- **User_Model**: The CodeIgniter 4 model that queries the `users` table in the MySQL database.
- **Password_Hash**: A bcrypt hash of the user's password stored in the `users` table.
- **Login_Form**: The HTML form presented to unauthenticated visitors at the `/login` route.
- **Recovery_Token**: A time-limited, single-use token generated for password reset requests.
- **Flash_Message**: A one-time session message displayed to the user after a redirect.

---

## Requirements

### Requirement 1: Users Table Structure

**User Story:** As a system developer, I want a well-defined `users` table, so that the Authentication, Dashboard, and Records modules share a consistent data contract.

#### Acceptance Criteria

1. THE User_Model SHALL define the `users` table with the following columns: `id` (INT, primary key, auto-increment), `name` (VARCHAR 100, not null), `email` (VARCHAR 150, unique, not null), `password` (VARCHAR 255, not null), `role` (ENUM `admin`/`staff`, not null), `created_at` (DATETIME), `updated_at` (DATETIME).
2. THE User_Model SHALL store passwords exclusively as bcrypt hashes; plaintext passwords SHALL NOT be persisted.
3. THE User_Model SHALL expose `id`, `name`, `email`, and `role` as readable fields to other modules; the `password` field SHALL be excluded from default select results.

---

### Requirement 2: Login Page

**User Story:** As a market staff member or administrator, I want a login page, so that I can authenticate and access the system.

#### Acceptance Criteria

1. WHEN an unauthenticated user visits any protected route, THE Auth_Filter SHALL redirect the user to `/login`.
2. THE Auth_Controller SHALL render the Login_Form at the `GET /login` route.
3. THE Login_Form SHALL contain an email field, a password field, a CSRF token field, and a submit button.
4. WHILE a user is already authenticated, THE Auth_Controller SHALL redirect the user away from `/login` to `/dashboard`.

---

### Requirement 3: Credential Validation

**User Story:** As a registered user, I want my credentials validated against the database, so that only authorised accounts can access the system.

#### Acceptance Criteria

1. WHEN the Login_Form is submitted, THE Auth_Controller SHALL validate that the email field is a non-empty, properly formatted email address.
2. WHEN the Login_Form is submitted, THE Auth_Controller SHALL validate that the password field is non-empty and between 8 and 72 characters.
3. WHEN validation passes, THE User_Model SHALL query the `users` table for a record matching the submitted email address.
4. WHEN a matching email record is found, THE Auth_Controller SHALL verify the submitted password against the stored Password_Hash using `password_verify()`.
5. IF the submitted email does not match any record, THEN THE Auth_Controller SHALL reject the login attempt without revealing whether the email or password was incorrect.
6. IF `password_verify()` returns false, THEN THE Auth_Controller SHALL reject the login attempt without revealing whether the email or password was incorrect.

---

### Requirement 4: Session Establishment

**User Story:** As an authenticated user, I want a session created upon successful login, so that I remain authenticated across page requests.

#### Acceptance Criteria

1. WHEN credential validation succeeds, THE Auth_Controller SHALL regenerate the session ID before writing session data.
2. WHEN credential validation succeeds, THE Session SHALL store the following data: `user_id`, `user_name`, `user_role`, and `is_logged_in` (boolean true).
3. WHEN credential validation succeeds, THE Auth_Controller SHALL redirect the user to `/dashboard`.
4. THE Session SHALL remain valid for the duration configured in `app/Config/Session.php` and SHALL expire upon browser close if no persistent session is configured.

---

### Requirement 5: Login Error Handling

**User Story:** As a user who enters incorrect credentials, I want clear error feedback, so that I know my login attempt failed without exposing security details.

#### Acceptance Criteria

1. IF form validation fails, THEN THE Auth_Controller SHALL redisplay the Login_Form with a Flash_Message listing the specific validation errors.
2. IF credential validation fails (wrong email or password), THEN THE Auth_Controller SHALL redisplay the Login_Form with a generic Flash_Message stating that the email or password is incorrect.
3. WHEN five consecutive failed login attempts occur from the same session within a 10-minute window, THE Auth_Controller SHALL temporarily block further login attempts from that session for 10 minutes and display an appropriate Flash_Message.
4. THE Login_Form SHALL NOT repopulate the password field after a failed attempt.

---

### Requirement 6: Route Protection via Auth Filter

**User Story:** As a system administrator, I want all non-public routes protected, so that unauthenticated users cannot access market management features.

#### Acceptance Criteria

1. THE Auth_Filter SHALL check for the presence of `is_logged_in` equal to true in the Session on every request to a protected route.
2. IF `is_logged_in` is absent or false, THEN THE Auth_Filter SHALL redirect the request to `/login` with a Flash_Message indicating that authentication is required.
3. THE Auth_Filter SHALL be registered in `app/Config/Filters.php` and applied to all routes except `GET /login`, `POST /login`, `GET /forgot-password`, and `POST /forgot-password`.
4. WHEN an authenticated user's session expires, THE Auth_Filter SHALL redirect the user to `/login` with a session-expired Flash_Message.

---

### Requirement 7: Logout

**User Story:** As an authenticated user, I want to log out, so that my session is terminated and the system is secured.

#### Acceptance Criteria

1. WHEN a `POST /logout` request is received, THE Auth_Controller SHALL destroy the current Session.
2. WHEN the Session is destroyed, THE Auth_Controller SHALL redirect the user to `/login` with a Flash_Message confirming successful logout.
3. THE Auth_Controller SHALL reject `GET /logout` requests and return a 405 Method Not Allowed response.

---

### Requirement 8: Password Recovery

**User Story:** As a user who has forgotten their password, I want a password recovery flow, so that I can regain access to my account.

#### Acceptance Criteria

1. THE Auth_Controller SHALL render a password recovery request form at `GET /forgot-password` containing an email field and a CSRF token field.
2. WHEN a recovery request is submitted with a valid email, THE Auth_Controller SHALL generate a Recovery_Token, store it in the `password_resets` table with the associated email and an expiry timestamp 60 minutes in the future, and send a reset link to the provided email address.
3. IF the submitted email does not match any record in the `users` table, THEN THE Auth_Controller SHALL display the same confirmation message as a successful request to prevent email enumeration.
4. WHEN a user visits the reset link containing a valid, unexpired Recovery_Token, THE Auth_Controller SHALL render a password reset form.
5. WHEN the password reset form is submitted with a new password of at least 8 characters, THE Auth_Controller SHALL update the Password_Hash in the `users` table and invalidate the Recovery_Token.
6. IF the Recovery_Token is expired or not found, THEN THE Auth_Controller SHALL redirect the user to `/forgot-password` with a Flash_Message stating the link is invalid or expired.

---

### Requirement 9: Session Data Contract for Downstream Modules

**User Story:** As a Dashboard module developer, I want standardised session keys set after login, so that I can display the user's name and role without additional database queries.

#### Acceptance Criteria

1. THE Session SHALL expose `user_name` (string) containing the authenticated user's full name as stored in the `users` table.
2. THE Session SHALL expose `user_role` (string, value `admin` or `staff`) containing the authenticated user's role.
3. THE Session SHALL expose `user_id` (integer) containing the authenticated user's primary key.
4. WHEN the Session is destroyed on logout, THE Auth_Controller SHALL ensure all three keys (`user_name`, `user_role`, `user_id`) are removed from the Session.

---

### Requirement 10: Security Hardening

**User Story:** As a system administrator, I want the authentication module to follow security best practices, so that the system is protected against common web attacks.

#### Acceptance Criteria

1. THE Auth_Controller SHALL enforce CSRF token validation on all `POST` requests using CodeIgniter 4's built-in CSRF protection.
2. THE User_Model SHALL use parameterised queries (via CodeIgniter 4's Query Builder) for all database interactions to prevent SQL injection.
3. WHEN a successful login occurs, THE Auth_Controller SHALL regenerate the session ID to prevent session fixation attacks.
4. THE Login_Form SHALL set the `autocomplete="off"` attribute on the password field.
5. WHERE HTTPS is enabled on the server, THE Session SHALL be configured with `session.cookieSecure = true` and `session.cookieSameSite = Lax`.
