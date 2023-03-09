# php-data-tabulator

A PHP library to turn rows of data (e.g.: from database query result) into aggregated tabular data format

## Installation

### Library

    $ git clone https://github.com/onlinesid/php-data-tabulator.git

### Dependencies

#### [`Composer`](https://github.com/composer/composer) (*will use the Composer ClassLoader*)

    $ wget http://getcomposer.org/composer.phar
    $ php composer.phar require onlinesid/php-data-tabulator

## Usage

    $rows = [
        ['id' => 7, 'u_id' => 1, 'u_name' => 'Joan', 'a_id' => 'PK', 'a_name' => 'Packing', 'num' => 10.5, ],
        ['id' => 4, 'u_id' => 1, 'u_name' => 'Joan', 'a_id' => 'PK', 'a_name' => 'Packing', 'num' =>  0.5, ],
        ['id' => 2, 'u_id' => 1, 'u_name' => 'Joan', 'a_id' => 'DR', 'a_name' => 'Driving', 'num' =>  2.3, ],
        ['id' => 5, 'u_id' => 2, 'u_name' => 'Robb', 'a_id' => 'DR', 'a_name' => 'Driving', 'num' =>  8.7, ],
    ];
    $tabulator = new DataTabulator($rows);

    $table = $tabulator->to2DTable('Name', 'u_id', 'u_name', 'a_id', 'a_name', 'num');

    // Expected result ($table) is something like:
    //
    //    Name       Packing (PK)     Driving (DR)
    //    Joan (1)          11               2.3
    //    Robb (2)           0               8.7

## Running the tests

    $ php bin/phpunit
