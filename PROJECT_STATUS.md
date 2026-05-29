# WBMM — Project Completion Checklist

This document tracks delivery against the original specification.

## Core deliverables

| # | Item | Status |
|---|------|--------|
| 1 | schema.sql | Done |
| 2 | app/Config/Routes.php | Done |
| 3 | app/Config/Filters.php | Done |
| 4 | app/Config/Security.php (CSRF) | Done |
| 5 | app/Filters/AuthFilter.php | Done |
| 6–12 | All Models | Done |
| 13–23 | All Controllers | Done |
| 24 | layouts/main.php | Done |
| 25 | auth/login.php | Done |
| 26 | dashboard/index.php | Done |
| 27 | stalls/ (4 views) | Done |
| 28 | vendors/ (4 views) | Done |
| 29 | assignments/ (2 views) | Done |
| 30 | payments/ (3 views + PDF) | Done |
| 31 | records/ (5 views) | Done |
| 32 | reports/ (2 views) | Done |
| 33 | rates/ (2 views) | Done |
| 34 | notifications/index.php | Done |
| 35 | users/ (3 views) | Done |
| 36 | public/assets/css/custom.css | Done |
| 37 | public/assets/js/wbmm.js | Done |
| 38 | README.md | Done |

## Modules

| Module | Status |
|--------|--------|
| 1 Authentication | Done |
| 2 Dashboard | Done |
| 3 Stall Management | Done |
| 4 Vendor Management | Done |
| 5 Vendor-Stall Assignment | Done |
| 6 Arkalaba Collection | Done |
| 7 Rate Management | Done |
| 8 Collector Remittance | Done |
| 9 Records & Reports | Done |
| 10 Notifications & Alerts | Done |
| 11 User Management | Done |

## Extras

- setup.bat — Windows database installer
- PHPUnit tests (unit + auth feature)
- Postman collection
- .env / env templates
- Sample assignments + payments in schema

## Quick test accounts

| Role | Email | Password |
|------|-------|----------|
| admin | admin@wbmm.com | Admin@1234 |
| supervisor | supervisor@wbmm.com | Admin@1234 |
| collector | collector1@wbmm.com | Admin@1234 |
| staff | staff@wbmm.com | Admin@1234 |
