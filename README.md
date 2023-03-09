# ![Favicon](./etc/favicon/favicon-32x32.png?raw=true "Favicon") PartHub - Parts Inventory and BOM Tool

If you want to manage small and medium parts inventory and BOMs, **PartHub** is here for you. No matter if you are a niche manufacturer looking for a way to keep track of your parts or you own a mid-sized electronics company in need of professional ERP management.

**PartHub** is written in PHP 8.1 and Javascript and makes heavy use of Bootstrap for the front-end. The flashy user interface might not stay for the long run. Data is stored in an SQL database for highly efficient data manipulation and a response time quicker than a bat out of hell.

# Features
 - Keep an up-to-date inventory of your parts easily
 - Multiple storage locations per part
 - Move stock between locations
 - Send mail upon reaching minimum stock in given location
 - Easily create BOMs by simply adding parts
 - Execute BOMs when building projects, stock gets automatically deducted
 - Track stock history
 - Works in any browser
 - Multi-user
 - Import data via CSV files

# Screenshots
![Parts](./etc/screenshots/Parts.png?raw=true "Parts Inventory")
![Stock Levels](./etc/screenshots/Show-Stock.png?raw=true "Stock Levels")
![Create new BOM](./etc/screenshots/Create-BOM.png?raw=true "Create new BOM")
![Show BOM](etc/screenshots/Show-BOM.png?raw=true "Show BOM Details")
![Build BOM](etc/screenshots/Build-BOM.png?raw=true "Build BOM")

# Installation
**PartHub** is currently my personal pleasure, so you need to create your own database and point to it in the `config/credentials.php` file. A full-featured installation process for self-hosting will follow.

## Database
Works with SQL databases like MariaDB or MySQL.