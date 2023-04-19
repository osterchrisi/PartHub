# ![Favicon](assets/favicon/favicon-32x32.png?raw=true "Favicon") PartHub - Parts Inventory and BOM Tool

If you want to manage small and medium parts inventory and BOMs, **PartHub** is here for you. No matter if you are a niche manufacturer looking for a way to keep track of your parts or you own a mid-sized electronics company in need of professional ERP management.

**PartHub** is written in PHP and Javascript and makes heavy use of Bootstrap 5 for the front-end. The flashy user interface might not stay for the long run. Data is stored in an SQL database for highly efficient data manipulation and a response time quicker than a bat out of hell.

# Features
 - Keep an up-to-date inventory of your parts easily
 - Multiple storage locations per part, move stock between locations
 - Send mail upon reaching minimum stock in given location
 - Easily manage BOMs by uploading CSV file or simply adding parts
 - Execute BOMs when building projects, stock gets automatically deducted
 - Track stock history
 - Works in any browser and is mobile-friendly
 - Multi-user, teams
 - Import data via CSV files
 - Barcode scanner integration
# Future development
 - KiCad Support
 - Mouser API Support

# Screenshots
![Parts](assets/screenshots/Parts.png?raw=true "Parts Inventory")
![Create new BOM](assets/screenshots/Create-BOM.png?raw=true "Create new BOM")
![Show BOM](assets/screenshots/Show-BOM.png?raw=true "Show BOM Details")
![Build BOM](assets/screenshots/Build-BOM.png?raw=true "Build BOM")

# Installation
**PartHub** is currently my personal pleasure, so you need to create your own database and point to it in the `config/credentials.php` file. A full-featured installation process for self-hosting will follow.

## Database
Works with SQL databases like MariaDB or MySQL.