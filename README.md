# WBMM — Web-Based Market Management System

## 1. Project Overview

The **Web-Based Market Management System (WBMM)** supports daily operations of the **General Santos City Public Market**, one of the largest public markets in Mindanao. The system is used by the Market Authority under the Local Government Unit (LGU) of General Santos City to manage vendors, stalls, arkalaba (rental fee) collections, permits, rates, and financial reports.

Key real-world roles:

| Role | Filipino term | Responsibility |
|------|---------------|----------------|
| Market Administrator | — | Full system access |
| Market Supervisor | — | Reports and oversight |
| Market Collector | **Maningil** / **Tigsingil** | Stall-to-stall arkalaba collection |
| Market Staff | — | Vendor registration and records |
| Vendor | — | Stall/spot renter |

---

## 2. Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | CodeIgniter 4 (PHP 8.x) |
| Database | MySQL 8.x |
| Server | Apache via XAMPP |
| Composer | Autoload, Dompdf |
| Frontend | Bootstrap 5 + Chart.js (CDN), server-rendered views |
| Auth | Session-based, bcrypt (`password_hash` / `password_verify`) |
| PDF | Dompdf (official receipts) |
| Testing | PHPUnit, Postman |

---

## 3. Setup Instructions

### Prerequisites

- XAMPP (Apache + MySQL + PHP 8.2+)
- Composer
- Git

### Steps

1. **Start XAMPP** — Start Apache and MySQL from the XAMPP Control Panel.

2. **Clone / copy the project** into `C:\xampp\htdocs\WBMM` (or your htdocs path).

3. **Install PHP dependencies:**
   ```bash
   cd C:\xampp\htdocs\WBMM
   composer install
   ```

4. **Create the database:**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create database: `wbmm_db`
   - Import `schema.sql` into `wbmm_db`

5. **Configure the application:**
   - `app/Config/App.php` — Set `$baseURL` (default: `http://localhost/WBMM/public/`)
   - `app/Config/Database.php` — Set hostname, username, password, database name

6. **Access the app:**
   ```
   http://localhost/WBMM/public/
   ```

### Quick setup (Windows / XAMPP)

Double-click **`setup.bat`** in the project root to recreate `wbmm_db` and import `schema.sql` automatically.

### Run tests

```bash
c:\xampp\php\php.exe vendor\bin\phpunit tests\
```

### Postman

Import **`postman/WBMM.postman_collection.json`** for route smoke tests (session cookie required for protected routes).

---

## 4. Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| admin | admin@wbmm.com | Admin@1234 |
| supervisor | supervisor@wbmm.com | Admin@1234 |
| collector | collector1@wbmm.com | Admin@1234 |
| collector | collector2@wbmm.com | Admin@1234 |
| staff | staff@wbmm.com | Admin@1234 |

---

## 5. Module Summary

| Module | Route prefix | Description |
|--------|--------------|-------------|
| Authentication | `/login` | Session login/logout |
| Dashboard | `/dashboard` | KPIs, charts, alerts |
| Stalls | `/stalls` | Physical stall/spot management |
| Vendors | `/vendors` | Vendor registration and profiles |
| Assignments | `/assignments` | Vendor–stall linking and permits |
| Arkalaba Collection | `/payments` | Payment recording and receipts |
| Records | `/records` | Transaction list and exports |
| Reports | `/records/summary`, `/records/overdue`, etc. | Financial and operational reports |
| Collector Remittance | `/reports/collector` | Per-collector collection summary |
| Rate Management | `/rates` | Versioned rental rates |
| User Management | `/users` | Staff account administration |
| Notifications | `/notifications` | Overdue, permits, vacant alerts |

---

## 6. Role Permissions Matrix

| Module | admin | supervisor | collector | staff |
|--------|:-----:|:----------:|:---------:|:-----:|
| Dashboard | ✓ | ✓ | → payments | ✓ |
| Notifications | ✓ | ✓ | ✓ | ✓ |
| Stalls (view) | ✓ | ✓ | ✓ | ✓ |
| Stalls (edit/delete) | ✓ | — | — | — |
| Vendors (view) | ✓ | ✓ | ✓ | ✓ |
| Vendors (create) | ✓ | — | — | ✓ |
| Vendors (edit/delete) | ✓ | — | — | — |
| Assignments | ✓ | — | — | ✓ |
| Terminate assignment | ✓ | — | — | — |
| Collect arkalaba | ✓ | view | ✓ | view |
| Records (own/all) | all | all | own | all |
| Financial summary | ✓ | ✓ | — | — |
| Collector remittance | ✓ | ✓ | — | — |
| Rate management | ✓ | — | — | — |
| User management | ✓ | — | — | — |

---

## 7. Payment Computation Examples

Rates use the version with the latest `effective_date` that is **≤ today**.

### Inside stall (sqm-based)

Monthly = sqm × inside_rate_per_sqm  
Weekly = monthly ÷ 4  
Daily = monthly ÷ 30  

**Example:** 6 sqm × ₱45.00/sqm = **₱270.00/month**  
- Weekly: ₱67.50  
- Daily: ₱9.00  

### Outside spot (flat fee)

- Daily: ₱25.00  
- Weekly: ₱150.00  
- Monthly: ₱500.00  

### Ambulant (flat daily only)

- Daily: ₱15.00  

---

## 8. Glossary

| Term | Meaning |
|------|---------|
| **Arkalaba** | Rental fee paid by vendors for stall or spot |
| **Maningil** | Market collector who collects arkalaba |
| **Resibo** | Official receipt issued after payment |
| **Vendor** | Person or business renting from the market |
| **Stall** | Physical space (inside, outside, or ambulant code) |
| **Vendor-Stall Assignment** | Link between a vendor and a stall with permit details |
| **Rate Version** | Historical rate record; never deleted; selected by effective date |

---

## Project status

See **[PROJECT_STATUS.md](PROJECT_STATUS.md)** for the full specification checklist (all 38 deliverables).

## File Structure

```
app/
  Controllers/   Auth, Dashboard, Stall, Vendor, Assignment, Payment,
                 Record, Report, Rate, Notification, User
  Models/        User, Stall, Vendor, VendorStall, Payment, Rate, AuditLog
  Views/         layouts, auth, dashboard, stalls, vendors, assignments,
                 payments, records, reports, rates, notifications, users
  Filters/       AuthFilter.php
public/
  assets/css/custom.css
  assets/js/wbmm.js
schema.sql
README.md
```

---

© 2026 WBMM — General Santos City Public Market · LGU General Santos City
