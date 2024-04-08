<div>
    <h1 style="display: flex; align-items: center;"><img src="public/favicon.ico?raw=true" alt="Favicon" style="vertical-align: middle;">&nbsp;PartHub - Parts Inventory and BOM Tool</h1>
</div>

If you want to manage small and medium parts inventory and BOMs, **PartHub** is here for you. No matter if you are a passionate hobbyist, a niche manufacturer or a mid-sized electronics company looking for a way to keep track of your parts and not get slain by the learning curve of a full-blown ERP system.

**PartHub** is a Laravel application written in PHP and Javascript and makes heavy use of Bootstrap 5 for the front-end. Data is stored in an SQL database for highly efficient data manipulation and a response time quicker than a bat out of hell.

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
![Parts](public/screenshots/Parts.png?raw=true "Parts Inventory")
![BOMs](public/screenshots/BOMs.png?raw=true "BOM List")

# Installation
**PartHub** is currently my personal pleasure. I'm working on a nice outline how to host this on your own server.

## Requirements
- PHP 8.1 with mbstring, Image Processing, Exif, GD Image or Imagick
- Node
- Composer
- SQL databse (MySQL or MariaDB)
- Optionally your own webserver if you want to configure more in-depth

## Database
Works with SQL databases like MariaDB or MySQL.
