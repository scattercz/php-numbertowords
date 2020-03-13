<?php

namespace scattercz\Tests;

use scattercz\NumberToWords;
use PHPUnit\Framework\TestCase;

/**
 * Tests of NumberToWords class
 *
 * @package NumberToWords
 * @category NumberToWords
 * @author Zdeněk Novotný <scatter@scatter.cz>
 */
class NumberToWordsTest extends TestCase
{
    /**
     * Test invalid input type
     */
    public function testInvalidInputType()
    {
        $this->expectExceptionMessage('Input type must be integer or float.');

        $numberToWords = new NumberToWords;
        $numberToWords->convertNumber('jedna');
    }


    /**
     * Test invalid input range
     */
    public function testInvalidInputRange()
    {
        $this->expectExceptionMessage('Input is out of available range.');

        $numberToWords = new NumberToWords;
        $numberToWords->convertNumber(1999999999);
    }


    /**
     * Test invalid float decimals range
     */
    public function testInvalidFloatDecimalsRange()
    {
        $this->expectExceptionMessage('Decimals are out of available range.');

        $numberToWords = new NumberToWords;
        $numberToWords->convertNumber(3.1415);
    }

    /**
     * Test if number output is as expected
     */
    public function testNumberOutput()
    {
        $numberToWords = new NumberToWords;

        // whole numbers with default separator
        $this->assertEquals('nula', $numberToWords->convertNumber(0));
        $this->assertEquals('dvě', $numberToWords->convertNumber(2));
        $this->assertEquals('sedmnáct', $numberToWords->convertNumber(17));
        $this->assertEquals('třicet osm', $numberToWords->convertNumber(38));
        $this->assertEquals('jedno sto dva', $numberToWords->convertNumber(102));
        $this->assertEquals('jedno sto sedmnáct', $numberToWords->convertNumber(117));
        $this->assertEquals('dva tisíce pět set šedesát čtyři', $numberToWords->convertNumber(2564));
        $this->assertEquals('sedmnáct tisíc padesát šest', $numberToWords->convertNumber(17056));
        $this->assertEquals('třicet čtyři tisíc pět set sedmdesát osm', $numberToWords->convertNumber(34578));
        $this->assertEquals('jedno sto tisíc dva', $numberToWords->convertNumber(100002));
        $this->assertEquals('jedno sto dvanáct tisíc čtyři sta sedmdesát osm', $numberToWords->convertNumber(112478));
        $this->assertEquals('tři miliony čtyři sta padesát sedm tisíc osm set šedesát dva', $numberToWords->convertNumber(3457862));
        $this->assertEquals('devadesát šest milionů tři sta dvacet čtyři tisíc pět set sedmdesát osm', $numberToWords->convertNumber(96324578));
        $this->assertEquals('pět set sedmdesát osm milionů dvě stě čtyřicet jedna tisíc tři sta devadesát osm', $numberToWords->convertNumber(578241398));
        $this->assertEquals('mínus šedesát čtyři', $numberToWords->convertNumber(-64));
        $this->assertEquals('mínus jedenáct tisíc jedno sto jedenáct', $numberToWords->convertNumber(-11111));
        $this->assertEquals('třicet tisíc osm set', $numberToWords->convertNumber(30800));

        // decimal numbers
        $this->assertEquals('žádná celá dvě stě padesát pět', $numberToWords->convertNumber(0.255));
        $this->assertEquals('jedna celá čtyři', $numberToWords->convertNumber(1.4));
        $this->assertEquals('jedna celá čtyři setiny', $numberToWords->convertNumber(1.04));
        $this->assertEquals('jedna celá čtyři tisíciny', $numberToWords->convertNumber(1.004));
        $this->assertEquals('dvě celé pět set čtyři', $numberToWords->convertNumber(2.504));
        $this->assertEquals('čtyři celé třicet sedm tisícin', $numberToWords->convertNumber(4.037));
        $this->assertEquals('šest celých šedesát šest', $numberToWords->convertNumber(6.66));

        // different / none words separator
        $this->assertEquals('jedno a sto a tisíc a dva', $numberToWords->convertNumber(100002, ' a '));
        $this->assertEquals('třimilionyčtyřistapadesátsedmtisícosmsetšedesátdva', $numberToWords->convertNumber(3457862, ''));
    }

    /**
     * Test if currency output is as expected
     */
    public function testCurrencyOutput()
    {
        $numberToWords = new NumberToWords;

        // whole currency
        $this->assertEquals('jedna koruna', $numberToWords->convertCurrency(1));
        $this->assertEquals('dvě koruny', $numberToWords->convertCurrency(2));
        $this->assertEquals('čtrnáct korun', $numberToWords->convertCurrency(14));
        $this->assertEquals('třicet čtyři korun', $numberToWords->convertCurrency(34));
        $this->assertEquals('jedno sto dvě koruny', $numberToWords->convertCurrency(102));
        $this->assertEquals('dvě stě padesát šest korun', $numberToWords->convertCurrency(256));
        $this->assertEquals('tři tisíce tři sta třicet tři korun', $numberToWords->convertCurrency(3333));
        $this->assertEquals('třicet tisíc osm set korun', $numberToWords->convertCurrency(30800));
        $this->assertEquals('čtyřicet devět tisíc dvě stě osmdesát korun', $numberToWords->convertCurrency(49280));
        $this->assertEquals('tři sta sedmnáct tisíc dvě stě šedesát osm korun', $numberToWords->convertCurrency(317268));
        $this->assertEquals('devět milionů osm set padesát dva tisíc jedno sto čtyřicet šest korun', $numberToWords->convertCurrency(9852146));
        $this->assertEquals('jedenáct milionů dvě stě čtyřicet pět tisíc devět set osmdesát tři korun', $numberToWords->convertCurrency(11245983));
        $this->assertEquals('čtyři sta dvacet pět milionů šest set osmdesát devět tisíc sedm set deset korun', $numberToWords->convertCurrency(425689710));
        $this->assertEquals('mínus šest set čtyřicet šest korun', $numberToWords->convertCurrency(-646));

        // currency with decimals
        $this->assertEquals('tři koruny čtrnáct haléřů', $numberToWords->convertCurrency(3.14));
        $this->assertEquals('tři koruny patnáct haléřů', $numberToWords->convertCurrency(3.145));
        $this->assertEquals('pět korun dva haléře', $numberToWords->convertCurrency(5.02));
        $this->assertEquals('deset korun jeden haléř', $numberToWords->convertCurrency(10.01));
        $this->assertEquals('tři sta padesát šest korun čtyřicet pět haléřů', $numberToWords->convertCurrency(356.45));
        $this->assertEquals('třicet tisíc osm set korun jeden haléř', $numberToWords->convertCurrency(30800.01));
        $this->assertEquals('mínus tři sta padesát osm tisíc čtyři sta jedenáct korun dvacet sedm haléřů', $numberToWords->convertCurrency(-358411.27));
    }
}