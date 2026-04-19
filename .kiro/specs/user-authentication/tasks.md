# Implementation Plan: User Authentication Login Module — WBMM

## Overview

Implement the full authentication module for the Web-Based Market Management System on CodeIgniter 4 with MySQL. Tasks are ordered so each step builds on the previous one, ending with full integration and test coverage.

## Tasks

- [x] 1. Set up database migrations and test infrastructure
  - [x] 1.1 Create `users` table migration
    - Create `app/Database/Migrations/{timestamp}_CreateUsersTable.php`
    - Define columns: `id`, `name`, `email` (UNIQUE), `password`, `role` (ENUM `admin`/`staff`), `created_at`, `updated_at`
    - Use `ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`
    - _Requirements: 1.1_

  - [x] 1.2 Create `password_resets` table migration
    - Create `app/Database/Migrations/{timestamp}_CreatePasswordResetsTable.php`
    - Define columns: `id`, `email`, `token` (UNIQUE, 64 chars), `expires_at`, `used` (TINYINT default 0), `created_at`
    - Add indexes on `token` and `email`
    - _Requirements: 8.2_

  - [x] 1.3 Create a database seeder for a test admin user
    - Create `app/Database/Seeds/AuthSeeder.php`
    - Insert one `admin` user with a bcrypt-hashed password using `password_hash()`
    - _Requirements: 1.2_

  - [x] 1.4 Configure PHPUnit test suites
    - Update `phpunit.xml.dist` to add three test suites: `Auth Unit` (`tests/unit/Auth`), `Auth Feature` (`tests/feature/Auth`), `Auth Property` (`tests/property/Auth`)
    - Create the three directory paths with `.gitkeep` files
    - Install `giorgiosironi/eris` via Composer for property-based tests: `composer require --dev giorgiosironi/eris`
    - _Requirements: (test infrastructure)_

- [x] 2. Implement User_Model
  - [x] 2.1 Create `app/Models/UserModel.php`
    - Extend `CodeIgniter\Model`, set `$table = 'users'`, `$primaryKey = 'id'`, `$allowedFields`, `$useTimestamps = true`
    - Implement `findByEmail(string $email)` — selects `id`, `name`, `email`, `role` only (no `password`)
    - Implement `findByEmailWithPassword(string $email)` — selects all fields for auth use only
    - Implement `updatePassword(int $id, string $hash)` — updates `password` field via Query Builder
    - Use parameterised Query Builder for all queries
    - _Requirements: 1.1, 1.2, 1.3, 10.2_

  - [ ]* 2.2 Write property test for password storage (Property 1)
    - Create `tests/property/Auth/PasswordHashingTest.php`
    - **Property 1: Password storage is always a bcrypt hash**
    - For any plaintext string, assert stored value passes `password_verify()` and does not equal plaintext
    - Run ≥ 100 iterations with eris generators
    - Tag: `// Feature: user-authentication, Property 1: Password storage is always a bcrypt hash`
    - **Validates: Requirements 1.2**

  - [ ]* 2.3 Write property test for safe user fetch (Property 2)
    - Create `tests/property/Auth/UserModelTest.php`
    - **Property 2: User fetch never exposes the password field**
    - For any user record, assert `findByEmail()` result contains `id`, `name`, `email`, `role` and does NOT contain `password`
    - Tag: `// Feature: user-authentication, Property 2: User fetch never exposes the password field`
    - **Validates: Requirements 1.3**

- [x] 3. Implement PasswordReset_Model
  - [x] 3.1 Create `app/Models/PasswordResetModel.php`
    - Extend `CodeIgniter\Model`, set `$table = 'password_resets'`, `$allowedFields`, `$useTimestamps = true`
    - Implement `createToken(string $email, string $token, string $expiresAt)` — inserts new reset record
    - Implement `findValidToken(string $token)` — returns record where `used = 0` and `expires_at > NOW()`
    - Implement `invalidateToken(string $token)` — sets `used = 1` for the given token
    - Use parameterised Query Builder for all queries
    - _Requirements: 8.2, 8.5, 10.2_

- [x] 4. Implement Auth_Filter
  - [x] 4.1 Create `app/Filters/AuthFilter.php`
    - Implement `CodeIgniter\Filters\FilterInterface`
    - In `before()`: check `session()->get('is_logged_in')`; if absent/false, redirect to `/login` with flash error `'Authentication required.'`
    - When session key is absent (expired), redirect with flash `'Your session has expired. Please log in again.'`
    - Leave `after()` empty
    - _Requirements: 6.1, 6.2, 6.4_

  - [x] 4.2 Register `AuthFilter` in `app/Config/Filters.php`
    - Add alias `'auth' => \App\Filters\AuthFilter::class` to `$aliases`
    - Apply filter globally with exceptions for `GET /login`, `POST /login`, `GET /forgot-password`, `POST /forgot-password`
    - _Requirements: 6.3_

  - [ ]* 4.3 Write property test for unauthenticated redirect (Property 3)
    - Create `tests/property/Auth/AuthFilterTest.php`
    - **Property 3: Unauthenticated requests to protected routes are always redirected**
    - For any URI that is not a public route and any session without `is_logged_in = true`, assert response is a redirect to `/login`
    - Tag: `// Feature: user-authentication, Property 3: Unauthenticated requests to protected routes are always redirected`
    - **Validates: Requirements 2.1, 6.1, 6.2**

- [x] 5. Implement Auth_Controller — login and logout
  - [x] 5.1 Create `app/Controllers/AuthController.php`
    - Extend `BaseController`, load `form` and `url` helpers
    - Implement `loginForm()` for `GET /login`: render `auth/login` view; redirect to `/dashboard` if `is_logged_in` is true
    - Implement `loginProcess()` for `POST /login`:
      1. Check rate-limit block (`login_blocked_until` in session)
      2. Run CI4 validation: email (valid_email, required), password (required, min_length[8], max_length[72])
      3. Call `UserModel::findByEmailWithPassword()`
      4. Call `password_verify()`; on failure increment `login_attempts`; if ≥ 5 set `login_blocked_until = time() + 600`
      5. On success: `session()->regenerate()`, write `user_id`, `user_name`, `user_role`, `is_logged_in = true`, clear rate-limit keys, redirect to `/dashboard`
      6. On failure: `redirect()->back()->withInput()->with('errors', ...)`
    - Implement `logout()` for `POST /logout`: `session()->destroy()`, redirect to `/login` with success flash
    - Register a `GET /logout` route that returns 405
    - _Requirements: 2.2, 2.4, 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 4.1, 4.2, 4.3, 5.1, 5.2, 5.3, 5.4, 7.1, 7.2, 7.3, 10.1, 10.3_

  - [ ]* 5.2 Write property test for invalid email validation (Property 4)
    - Create `tests/property/Auth/LoginValidationTest.php`
    - **Property 4: Invalid email format always fails validation**
    - For any string that is not a valid email, assert CI4 validation rejects it before credential lookup
    - Tag: `// Feature: user-authentication, Property 4: Invalid email format always fails validation`
    - **Validates: Requirements 3.1**

  - [ ]* 5.3 Write property test for password length boundary (Property 5)
    - Add to `tests/property/Auth/LoginValidationTest.php`
    - **Property 5: Password length validation enforces [8, 72] range**
    - For any string with length < 8 or > 72, assert validation rejects it; for length in [8, 72] assert it passes
    - Tag: `// Feature: user-authentication, Property 5: Password length validation enforces [8, 72] range`
    - **Validates: Requirements 3.2**

  - [ ]* 5.4 Write property test for bcrypt round-trip (Property 6)
    - Create `tests/property/Auth/BcryptRoundTripTest.php`
    - **Property 6: bcrypt verify is a correct round trip**
    - For any plaintext, assert `password_verify($plain, password_hash($plain, PASSWORD_BCRYPT))` is true; for any different string assert false
    - Tag: `// Feature: user-authentication, Property 6: bcrypt verify is a correct round trip`
    - **Validates: Requirements 3.4**

  - [ ]* 5.5 Write property test for session data contract (Property 7)
    - Create `tests/property/Auth/SessionContractTest.php`
    - **Property 7: Session data contract is complete and accurate after login**
    - For any valid user record, after successful login assert session contains exactly `user_id`, `user_name`, `user_role`, `is_logged_in = true` with matching values
    - Tag: `// Feature: user-authentication, Property 7: Session data contract is complete and accurate after login`
    - **Validates: Requirements 4.2, 9.1, 9.2, 9.3**

  - [ ]* 5.6 Write property test for rate limiter (Property 8)
    - Create `tests/property/Auth/RateLimiterTest.php`
    - **Property 8: Rate limiter blocks after five consecutive failures**
    - For any sequence of 5 consecutive failures, assert the 6th attempt is blocked regardless of credential validity
    - Tag: `// Feature: user-authentication, Property 8: Rate limiter blocks after five consecutive failures`
    - **Validates: Requirements 5.3**

  - [ ]* 5.7 Write property test for logout session cleanup (Property 9)
    - Create `tests/property/Auth/LogoutTest.php`
    - **Property 9: Logout removes all session auth keys**
    - For any authenticated session, after `POST /logout` assert none of `user_id`, `user_name`, `user_role`, `is_logged_in` remain
    - Tag: `// Feature: user-authentication, Property 9: Logout removes all session auth keys`
    - **Validates: Requirements 7.1, 9.4**

- [x] 6. Implement Auth_Controller — password recovery
  - [x] 6.1 Add `forgotPasswordForm()` to `AuthController`
    - Handle `GET /forgot-password`: render `auth/forgot_password` view
    - _Requirements: 8.1_

  - [x] 6.2 Add `forgotPasswordProcess()` to `AuthController`
    - Handle `POST /forgot-password`
    - Validate email field (valid_email, required)
    - Look up email in `users` table via `UserModel::findByEmail()`
    - If found: generate token with `bin2hex(random_bytes(32))`, call `PasswordResetModel::createToken()` with `expires_at = NOW() + 60 minutes`, send reset link via CI4 Email service
    - If not found: show same confirmation message (anti-enumeration)
    - _Requirements: 8.2, 8.3_

  - [x] 6.3 Add `resetPasswordForm()` and `resetPasswordProcess()` to `AuthController`
    - `GET /reset-password/{token}`: call `PasswordResetModel::findValidToken()`; if invalid/expired redirect to `/forgot-password` with flash; if valid render `auth/reset_password` view
    - `POST /reset-password/{token}`: validate new password (min_length[8]); call `UserModel::updatePassword()` with new bcrypt hash; call `PasswordResetModel::invalidateToken()`; redirect to `/login` with success flash
    - _Requirements: 8.4, 8.5, 8.6_

  - [ ]* 6.4 Write property test for token expiry window (Property 10)
    - Create `tests/property/Auth/PasswordRecoveryTest.php`
    - **Property 10: Recovery token expiry is always ~60 minutes in the future**
    - For any valid email, assert `expires_at` stored in `password_resets` is > `NOW()` and ≤ `NOW() + 61 minutes`
    - Tag: `// Feature: user-authentication, Property 10: Recovery token expiry is always ~60 minutes in the future`
    - **Validates: Requirements 8.2**

  - [ ]* 6.5 Write property test for password reset correctness (Property 11)
    - Create `tests/property/Auth/PasswordResetTest.php`
    - **Property 11: Password reset updates hash and invalidates token**
    - For any valid token and new password ≥ 8 chars, assert `password_verify($new, $storedHash)` is true and token `used = 1`
    - Tag: `// Feature: user-authentication, Property 11: Password reset updates hash and invalidates token`
    - **Validates: Requirements 8.5**

- [x] 7. Create views
  - [x] 7.1 Create `app/Views/auth/login.php`
    - Render email field, password field with `autocomplete="off"`, `csrf_field()`, and submit button
    - Display flash errors from `session('errors')` and generic flash from `session('error')`
    - Do NOT repopulate the password field (`old('email')` is fine; `old('password')` must be omitted)
    - _Requirements: 2.3, 5.1, 5.2, 5.4, 10.1, 10.4_

  - [x] 7.2 Create `app/Views/auth/forgot_password.php`
    - Render email field, `csrf_field()`, and submit button
    - Display flash messages
    - _Requirements: 8.1_

  - [x] 7.3 Create `app/Views/auth/reset_password.php`
    - Render new password field, confirm password field, `csrf_field()`, and submit button
    - Display flash validation errors
    - _Requirements: 8.4_

- [x] 8. Register routes
  - [x] 8.1 Update `app/Config/Routes.php`
    - Add: `GET /login`, `POST /login`, `POST /logout`, `GET /forgot-password`, `POST /forgot-password`, `GET /reset-password/(:segment)`, `POST /reset-password/(:segment)`
    - Add explicit `GET /logout` route returning 405
    - _Requirements: 2.1, 7.3_

- [ ] 9. Checkpoint — run all tests and verify integration
  - Ensure all tests pass, ask the user if questions arise.
  - Run `php spark migrate` and `php spark db:seed AuthSeeder` to verify migrations
  - Confirm `Auth_Filter` is applied to all protected routes and public routes are accessible without a session

- [ ] 10. Write feature and unit tests
  - [ ] 10.1 Create `tests/feature/Auth/LoginPageTest.php`
    - `GET /login` returns 200 with form (_Requirements: 2.2_)
    - Login form contains email, password, CSRF, submit (_Requirements: 2.3_)
    - Authenticated user redirected from `/login` to `/dashboard` (_Requirements: 2.4_)
    - Password field not repopulated after failure (_Requirements: 5.4_)
    - Password field has `autocomplete="off"` (_Requirements: 10.4_)

  - [ ] 10.2 Create `tests/feature/Auth/LoginProcessTest.php`
    - Unknown email returns generic error (_Requirements: 3.5_)
    - Wrong password returns generic error (_Requirements: 3.6_)
    - Session ID changes after successful login (_Requirements: 4.1_)
    - Successful login redirects to `/dashboard` (_Requirements: 4.3_)
    - Failed form validation shows flash errors (_Requirements: 5.1_)
    - Failed credential check shows generic flash (_Requirements: 5.2_)

  - [ ] 10.3 Create `tests/feature/Auth/AuthFilterTest.php`
    - Unauthenticated redirect includes flash message (_Requirements: 6.2_)
    - Expired session redirects with session-expired message (_Requirements: 6.4_)

  - [ ] 10.4 Create `tests/feature/Auth/LogoutTest.php`
    - `POST /logout` redirects to `/login` with success flash (_Requirements: 7.2_)
    - `GET /logout` returns 405 (_Requirements: 7.3_)

  - [ ] 10.5 Create `tests/feature/Auth/PasswordRecoveryTest.php`
    - `GET /forgot-password` returns 200 with form (_Requirements: 8.1_)
    - Unknown email shows same confirmation as success (_Requirements: 8.3_)
    - Valid token renders reset form (_Requirements: 8.4_)
    - Expired/invalid token redirects with flash (_Requirements: 8.6_)

  - [ ] 10.6 Create `tests/unit/Auth/SmokeTest.php`
    - `users` migration creates all required columns (_Requirements: 1.1_)
    - `Session::$expiration` is set to expected value (_Requirements: 4.4_)
    - CSRF filter applied to POST routes (_Requirements: 10.1_)
    - `Session::$cookieSecure` and `cookieSameSite` config values (_Requirements: 10.5_)
    - `Auth_Filter` alias registered in `Filters.php` (_Requirements: 6.3_)

- [ ] 11. Final checkpoint — ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for a faster MVP
- Each task references specific requirements for traceability
- Property tests use `giorgiosironi/eris` with ≥ 100 iterations per property
- All property tests must be tagged with `// Feature: user-authentication, Property N: ...`
- Use the `$tests` database group (SQLite3 in-memory) for all automated tests
- Never store plaintext passwords; always use `password_hash($password, PASSWORD_BCRYPT)`
- All DB queries must go through CI4's Query Builder (parameterised) — no raw interpolation
