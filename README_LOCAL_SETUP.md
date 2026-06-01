# WBMM — Local Deployment & Setup Guide

This guide provides step-by-step instructions on how to run the **Web-Based Market Management System (WBMM)** locally on any Windows computer (such as a teacher's laptop or any developer's PC) without requiring an internet connection.

---

## 📋 Prerequisites
Before starting, make sure the computer has the following installed:
1. **XAMPP** (with PHP version 8.1, 8.2, or 8.3).
2. **Web Browser** (Google Chrome is recommended for testing).
3. **Composer** (Optional—used to download dependencies if they are not already copied).

---

## 🛠️ Step-by-Step Installation (Using XAMPP)

### Step 1: Copy the Project into XAMPP
1. Copy or clone the project folder (`WBMM`) and place it inside your XAMPP root directory:
   `C:\xampp\htdocs\WBMM`

### Step 2: Start Apache and MySQL
1. Open the **XAMPP Control Panel** on your computer.
2. Click **Start** next to **Apache**.
3. Click **Start** next to **MySQL**.

### Step 3: Create & Import the Database
1. Open your web browser and navigate to:
   `http://localhost/phpmyadmin/`
2. Click on **New** in the left sidebar to create a new database.
3. Name the database exactly: **`wbmm_db`** and click **Create**.
4. Select the newly created `wbmm_db` database, and click the **Import** tab at the top.
5. Click **Choose File** and select the database schema file located in the root of the project folder:
   `C:\xampp\htdocs\WBMM\schema.sql`
6. Scroll down to the bottom and click **Import** (or **Go**). Your database tables are now fully imported and active!

### Step 4: Configure the Environment File (`.env`)
Since `.env` is ignored by Git (to protect passwords), you need to create a local configuration file:
1. In the project root (`C:\xampp\htdocs\WBMM`), find the file named `env` (it has no file extension).
2. Rename or copy this file to **`.env`** (with a leading dot).
3. Open **`.env`** in a text editor (like Notepad) and set the following local configurations:
   ```env
   # Set environment to development to show detailed error screens locally
   CI_ENVIRONMENT = development

   # Set local base URL
   app.baseURL = 'http://localhost/WBMM/'

   # Local Database settings
   database.default.hostname = localhost
   database.default.database = wbmm_db
   database.default.username = root
   database.default.password = 
   database.default.DBDriver = MySQLi
   database.default.port = 3306
   ```
4. Save the file.

### Step 5: Install Project Dependencies (`vendor` folder)
Since the `vendor` dependencies are excluded from Git to keep the repository lightweight, they must be set up on the new computer:
* **Option A (If Composer is installed):**
  Open the command prompt in the project directory (`C:\xampp\htdocs\WBMM`) and run:
  ```bash
  composer install
  ```
* **Option B (Offline/No Composer):**
  Simply copy the existing **`vendor`** folder directly from the developer's computer and paste it into the cloned project root.

---

## 🚀 How to Run the Website Locally

You can run the site in two different ways depending on your preference:

### Method A: Directly via XAMPP (Easiest)
Our transparent `.htaccess` automatically routes all traffic into the `public` directory safely.
1. Open your browser and navigate to:
   👉 **`http://localhost/WBMM/`**
2. The login screen will load instantly, completely offline!

### Method B: Via CodeIgniter CLI
1. Open Command Prompt in the project folder and run:
   ```bash
   php spark serve
   ```
2. Open your browser and navigate to:
   👉 **`http://localhost:8080/`**

---

## 🔒 Default Logins for Testing (from schema.sql)
You can sign in with any of these pre-seeded roles to evaluate the system (all accounts share the default password: **`Admin@1234`**):

* **Administrator Account:**
  * **Email:** `admin@wbmm.com`
  * **Password:** `Admin@1234`
* **Supervisor Account:**
  * **Email:** `supervisor@wbmm.com`
  * **Password:** `Admin@1234`
* **Staff Account:**
  * **Email:** `staff@wbmm.com`
  * **Password:** `Admin@1234`
* **Collector Accounts:**
  * **Emails:** `collector1@wbmm.com` (Juan Maningil) or `collector2@wbmm.com` (Pedro Tigsingil)
  * **Password:** `Admin@1234`
