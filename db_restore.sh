#!/bin/bash

echo "==================================================="
echo "  WBMM Database Restore Utility (MySQL / XAMPP)"
echo "==================================================="
echo ""

read -p "Enter Hostname [localhost]: " hostname
hostname=${hostname:-localhost}

read -p "Enter Username [root]: " username
username=${username:-root}

read -p "Enter Database Name [wbmm_db]: " database
database=${database:-wbmm_db}

read -s -p "Enter Password (press Enter for none): " password
echo ""

read -p "Enter the path to the backup .sql file: " sqlfile

if [ ! -f "$sqlfile" ]; then
    echo ""
    echo "[ERROR] Backup file '${sqlfile}' not found!"
    echo ""
    read -p "Press Enter to continue..."
    exit 1
fi

echo ""
echo "Restoring database '${database}' from '${sqlfile}'..."

# Try running mysql
if mysql -h "$hostname" -u "$username" --password="$password" -e "CREATE DATABASE IF NOT EXISTS ${database};" 2>/dev/null && \
   mysql -h "$hostname" -u "$username" --password="$password" "$database" < "$sqlfile" 2>/dev/null; then
    echo ""
    echo "[SUCCESS] Database restored successfully from '${sqlfile}'!"
else
    # Try XAMPP default path on Linux/macOS
    if /opt/lampp/bin/mysql -h "$hostname" -u "$username" --password="$password" -e "CREATE DATABASE IF NOT EXISTS ${database};" 2>/dev/null && \
       /opt/lampp/bin/mysql -h "$hostname" -u "$username" --password="$password" "$database" < "$sqlfile" 2>/dev/null; then
        echo ""
        echo "[SUCCESS] Database restored successfully from '${sqlfile}'!"
    else
        echo ""
        echo "[ERROR] Restore failed. Please verify if MySQL is running and credentials are correct."
    fi
fi

echo ""
read -p "Press Enter to continue..."
