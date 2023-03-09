<?php

namespace OnlineSid\DataTabulator\Tests;

use OnlineSid\DataTabulator\DataTabulator;
use \PHPUnit_Framework_TestCase;

class DataTabulatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Testing a bit more complex columns reordering
     */
    function testReorder()
    {
        $rows = [
            ['id' => 7, 'u_id' => 1, 'u_name' => 'Joan', 'a_id' => 'PK', 'a_name' => 'Packing', 'num' => 10.5, ],
            ['id' => 4, 'u_id' => 1, 'u_name' => 'Joan', 'a_id' => 'PK', 'a_name' => 'Packing', 'num' =>  0.5, ],
            ['id' => 2, 'u_id' => 1, 'u_name' => 'Joan', 'a_id' => 'DR', 'a_name' => 'Driving', 'num' =>  2.3, ],
            ['id' => 5, 'u_id' => 2, 'u_name' => 'Robb', 'a_id' => 'DR', 'a_name' => 'Driving', 'num' =>  8.7, ],
            ['id' => 15, 'u_id' => 2, 'u_name' => 'Robb', 'a_id' => 'LF', 'a_name' => 'Lifting', 'num' =>  7, ],
        ];
        $tabulator = new DataTabulator($rows);

        $table = $tabulator->to2DTable('Name', 'u_id', 'u_name', 'a_id', 'a_name', 'num');

        // Expected result ($table) is something like:
        //
        //    Name       Packing (PK)     Driving (DR)       Lifting (LF)
        //    Joan (1)          11               2.3               0
        //    Robb (2)           0               8.7               7

        $this->assertEquals(3, $table->getNbRows());
        $this->assertEquals(4, $table->getNbColumns());


        // testing with headers included with reverse row IDs ordering
        $arr = $table->toArray(true, false, ['LF', 'DR', 'PK',]);


        // Expected result ($arr) is something like:
        //
        //    Name       Lifting (LF)      Driving (DR)      Packing (PK)
        //    Joan (1)          0                2.3             11
        //    Robb (2)          7                8.7              0


        // first row
        $this->assertEquals('Name', $arr[0][0]);
        $this->assertEquals('LF', $arr[0][1]);
        $this->assertEquals('DR', $arr[0][2]);
        $this->assertEquals('PK', $arr[0][3]);
        // second row
        $this->assertEquals(1, $arr[1][0]);
        $this->assertEquals(0, $arr[1][1]);
        $this->assertEquals(2.3, $arr[1][2]);
        $this->assertEquals(11, $arr[1][3]);
        // third row
        $this->assertEquals(2, $arr[2][0]);
        $this->assertEquals(7, $arr[2][1]);
        $this->assertEquals(8.7, $arr[2][2]);
        $this->assertEquals(0, $arr[2][3]);
    }

    /**
     * Testing a simple scenario
     */
    function testSimple()
    {
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

        $this->assertEquals(3, $table->getNbRows());
        $this->assertEquals(3, $table->getNbColumns());

        $this->assertEquals(11  , $table->getAggregateValue(1, 'PK'));
        $this->assertEquals( 2.3, $table->getAggregateValue(1, 'DR'));
        $this->assertEquals( 0  , $table->getAggregateValue(2, 'PK'));
        $this->assertEquals( 8.7, $table->getAggregateValue(2, 'DR'));

        $this->assertEquals( 'Name', $table->getColumnLabel(0));
        $this->assertEquals( 'Packing', $table->getColumnLabel(1));
        $this->assertEquals( 'Driving', $table->getColumnLabel(2));

        $this->assertEquals( 'Name', $table->getRowLabel(0));
        $this->assertEquals( 'Joan', $table->getRowLabel(1));
        $this->assertEquals( 'Robb', $table->getRowLabel(2));

        $this->assertEquals(11, $table->getAggregateValueFromRowCol(1, 1));
        $this->assertEquals(0, $table->getAggregateValueFromRowCol(2, 1));
        $this->assertEquals(2.3, $table->getAggregateValueFromRowCol(1, 2));
        $this->assertEquals(8.7, $table->getAggregateValueFromRowCol(2, 2));

        // testing with headers included
        $arr = $table->toArray(true);
        // first row
        $this->assertEquals('Name', $arr[0][0]);
        $this->assertEquals('Packing', $arr[0][1]);
        $this->assertEquals('Driving', $arr[0][2]);
        // second row
        $this->assertEquals('Joan', $arr[1][0]);
        $this->assertEquals(11, $arr[1][1]);
        $this->assertEquals(2.3, $arr[1][2]);
        // third row
        $this->assertEquals('Robb', $arr[2][0]);
        $this->assertEquals(0, $arr[2][1]);
        $this->assertEquals(8.7, $arr[2][2]);

        // testing with headers excluded
        $arr = $table->toArray(false);
        // first row
        $this->assertEquals('Joan', $arr[0][0]);
        $this->assertEquals(11, $arr[0][1]);
        $this->assertEquals(2.3, $arr[0][2]);
        // second row
        $this->assertEquals('Robb', $arr[1][0]);
        $this->assertEquals(0, $arr[1][1]);
        $this->assertEquals(8.7, $arr[1][2]);

        // testing with headers included AND returning row/col IDs instead of row/col labels
        $arr = $table->toArray(true, false);
        // first row
        $this->assertEquals('Name', $arr[0][0]);
        $this->assertEquals('PK', $arr[0][1]);
        $this->assertEquals('DR', $arr[0][2]);
        // second row
        $this->assertEquals(1, $arr[1][0]);
        $this->assertEquals(11, $arr[1][1]);
        $this->assertEquals(2.3, $arr[1][2]);
        // third row
        $this->assertEquals(2, $arr[2][0]);
        $this->assertEquals(0, $arr[2][1]);
        $this->assertEquals(8.7, $arr[2][2]);

        // testing with headers included with reverse row IDs ordering
        $arr = $table->toArray(true, false, ['DR', 'PK',]);
        // first row
        $this->assertEquals('Name', $arr[0][0]);
        $this->assertEquals('DR', $arr[0][1]);
        $this->assertEquals('PK', $arr[0][2]);
        // second row
        $this->assertEquals(1, $arr[1][0]);
        $this->assertEquals(2.3, $arr[1][1]);
        $this->assertEquals(11, $arr[1][2]);
        // third row
        $this->assertEquals(2, $arr[2][0]);
        $this->assertEquals(8.7, $arr[2][1]);
        $this->assertEquals(0, $arr[2][2]);
    }
}