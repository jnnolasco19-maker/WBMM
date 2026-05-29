#!/bin/bash

echo "==================================================="
echo "  WBMM Database Backup Utility (MySQL / XAMPP)"
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

backup_dir="database_backups"
mkdir -p "$backup_dir"

cur_date=$(date +"%Y-%m-%d")
cur_time=$(date +"%H%M%S")
filename="${backup_dir}/backup_${database}_${cur_date}_${cur_time}.sql"

echo ""
echo "Backing up database '${database}' to '${filename}'..."

# Try running mysqldump
if mysqldump -h "$hostname" -u "$username" --password="$password" "$database" > "$filename" 2>/dev/null; then
    echo ""
    echo "[SUCCESS] Backup completed successfully!"
    echo "File saved at: ${filename}"
else
    # Try XAMPP default path on Linux/macOS
    if /opt/lampp/bin/mysqldump -h "$hostname" -u "$username" --password="$password" "$database" > "$filename" 2>/dev/null; then
        echo ""
        echo "[SUCCESS] Backup completed successfully!"
        echo "File saved at: ${filename}"
    else
        echo ""
        echo "[ERROR] Backup failed. Please verify if MySQL is running and credentials are correct."
        rm -f "$filename"
    fi
fi

echo ""
read -p "Press Enter to continue..."
