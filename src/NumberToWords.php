<?php

namespace scattercz;

/**
 * Simple class to covert numbers and currency values to words
 * 
 * Inspired by code found on http://www.builder.cz/cz/forum/tema-1323303-zobrazeni-castky-slovy-v-php-castka-slovy
 *
 * @package NumberToWords
 * @category NumberToWords
 * @author Zdeněk Novotný <scatter@scatter.cz>
 */
class NumberToWords
{
    private $defaultWordsSeparator = ' ';

    private $minus = 'mínus';
    private $zero = ['nula', 'žádná'];
    private $two = 'dvě';

    private $namesSmall = ['jedna', 'dva', 'tři', 'čtyři', 'pět', 'šest', 'sedm', 'osm', 'devět', 'deset', 'jedenáct', 'dvanáct', 'třináct', 'čtrnáct', 'patnáct', 'šestnáct', 'sedmnáct', 'osmnáct', 'devatenáct'];
    private $namesTens = ['dvacet', 'třicet', 'čtyřicet', 'padesát', 'šedesát', 'sedmdesát', 'osmdesát', 'devadesát'];
    private $namesHundreds = ['jedno sto', 'dvě stě', 'tři sta', 'čtyři sta', 'pět set', 'šest set', 'sedm set', 'osm set', 'devět set'];
    private $namesThousands = ['jeden tisíc', 'dva tisíce', 'tři tisíce', 'čtyři tisíce', 'tisíc'];
    private $namesMillions = ['jeden milion', 'dva miliony', 'tři miliony', 'čtyři miliony', 'milionů'];

    private $decimalsPoints = ['celá', 'celá', 'celé', 'celé', 'celé', 'celých'];
    private $decimalsNamesHundreds = ['setin', 'setina', 'setiny', 'setiny', 'setiny', 'setin'];
    private $decimalsNamesThousands = ['tisícin', 'tisícina', 'tisíciny', 'tisíciny', 'tisíciny', 'tisícin'];

    private $namesCrownUnits = ['korun', 'koruna', 'koruny', 'koruny', 'koruny', 'korun'];
    private $namesCrownDecimals = ['haléřů', 'haléř', 'haléře', 'haléře', 'haléře', 'haléřů'];
    private $namesCurrencyDecimalsAmount = ['nula', 'jeden', 'dva'];

    private $outputType = 'number';

    // dummy
    function __construct() {}


    /**
     * Replace default words separator (space) with user defined
     *
     * @param string $amountInWords amount in words separated with space
     * @param string $wordsSeparator words separator
     * @return string
     */
    private function applyWordsSeparator(string $amountInWords, string $wordsSeparator): string
    {
        return str_replace(' ', $wordsSeparator, $amountInWords);
    }


    /**
     * Get amount part in words for tens and units
     * 
     * @param int $amount amount
     * @return string
     */
    private function getAmountPartBelowHundred(int $amount): string
    {
        $result = '';

        // return empty if 0 or rest below 100 empty
        if ($amount == 0) return '';

        $hundreds = floor($amount / 100);
        $amountBelowHundred = $amount - ($hundreds * 100);
        if ($amountBelowHundred == 0) return '';

        // get tens and units
        $tens = floor($amountBelowHundred / 10);
        $units = $amountBelowHundred - ($tens * 10);

        // add tens for 20 and more
        if ($tens >= 2) $result .= $this->namesTens[$tens - 2] . $this->defaultWordsSeparator;
    
        // add amount for 1-19
        if ($amount >= 1 && $amountBelowHundred < 20) {

            // currency has different format for amount 2 than number
            if ($this->outputType == 'currency' && $amountBelowHundred == 2) {
                $result .= $this->two . $this->defaultWordsSeparator;
            } else {
                $result .= $this->namesSmall[$amountBelowHundred - 1] . $this->defaultWordsSeparator;
            }

        // add amount for units when out of 1-19 range
        } else if ($units > 0) {
            $result .= $this->namesSmall[$units - 1] . $this->defaultWordsSeparator;
        }
    
        // return result
        return $result;
    }


    /**
     * Get amount part in words for hundreds
     * 
     * @param int $amount amount
     * @return string
     */
    private function getAmountPartHundreds(int $amount): string
    {
        // return empty if <100 or amount below 1000 empty
        if ($amount < 100) return '';

        $thousands = floor($amount / 1000);
        $amountBelowThousand = $amount - ($thousands * 1000);
        if ($amountBelowThousand < 100) return '';

        // get hundreds
        $hundreds = floor($amountBelowThousand / 100);

        // return result
        return $this->namesHundreds[$hundreds - 1] . $this->defaultWordsSeparator;
    }


    /**
     * Get amount part in words for thousands
     * 
     * @param int $amount amount
     * @return string
     */
    private function getAmountPartThousands(int $amount): string
    {
        // return empty if <1000 or amount below 1000 empty
        if ($amount < 1000) return '';
    
        $millions = floor($amount / 1000000);
        $amountBelowMillion = $amount - ($millions * 1000000);
        if ($amountBelowMillion < 100) return '';

        // get thousands
        $thousands = $amountBelowMillion / 1000;

        // return result
        if ($thousands <= 4) {
            return $this->namesThousands[$thousands - 1] . $this->defaultWordsSeparator;
        } else {
            return $this->amountToWords($thousands) . $this->defaultWordsSeparator . $this->namesThousands[count($this->namesThousands) - 1] . ($amount >= 2000 && $amount < 5000 ? 'e' : '') . $this->defaultWordsSeparator;
        }
    }


    /**
     * Get amount part in words for millions
     * 
     * @param int $amount amount
     * @return string
     */
    private function getAmountPartMillions(int $amount): string
    {
        // return empty if <1000000
        if ($amount < 1000000) return '';
    
        // get millions
        $millions = floor($amount / 1000000);

        // return result
        if ($millions <= 4) {
            return $this->namesMillions[$millions - 1] . $this->defaultWordsSeparator;
        } else {
            return $this->amountToWords($millions) . $this->defaultWordsSeparator . $this->namesMillions[count($this->namesMillions) - 1] . $this->defaultWordsSeparator;
        }
    }


    /**
     * Convert amount to words
     * 
     * @param int $amount amount
     * @return string
     */
    private function amountToWords(int $amount): string
    {
        $result = '';

        // return specific values
        if ($amount == 0) return $this->zero[0];
        if ($amount == 2) return $this->two;

        // below zero - add minus and invert
        if ($amount < 0) {
            $result .= $this->minus . $this->defaultWordsSeparator;
            $amount *= -1;
        }

        // convert
        if ($amount >= 1000000) $result .= $this->getAmountPartMillions($amount);
        if ($amount >= 1000) $result .= $this->getAmountPartThousands($amount);
        if ($amount >= 100) $result .= $this->getAmountPartHundreds($amount);
        $result .= $this->getAmountPartBelowHundred($amount);

        // remove trailing default word separator(s)
        $result = preg_replace('/([[:space:]]+)$/', '', $result);

        // return result
        return $result;
    }


    /**
     * Convert decimals to words
     * 
     * @param string $decimals decimals
     * @return string
     */
    private function decimalsToWords(string $decimals): string
    {
        $result = '';
        $decimalsInt = intval($decimals);

        // convert
        if ($decimalsInt >= 100) $result .= $this->getAmountPartHundreds($decimalsInt);
        $result .= $this->getAmountPartBelowHundred($decimalsInt);

        if ($decimalsInt < 10 && strlen($decimals) == 2) $result .= $this->decimalsNamesHundreds[$decimalsInt >= 5 ? 5 : $decimalsInt];
        if ($decimalsInt < 100 && strlen($decimals) == 3) $result .= $this->decimalsNamesThousands[$decimalsInt >= 5 ? 5 : $decimalsInt];

        // remove trailing default word separator(s)
        $result = preg_replace('/([[:space:]]+)$/', '', $result);

        // return result
        return $result;
    }


    /**
     * Check input
     * 
     * @param mixed $amount amount
     * @return void
     * 
     * @throws \Exception if input is not integer or float or is out of range
     */
    private function checkInput($number): void
    {
        // check input type
        if (gettype($number) != 'integer' && gettype($number) != 'double') throw new \Exception('Input type must be integer or float.');

        // check input range
        if ($number < -999999999 || $number > 999999999) throw new \Exception('Input is out of available range.');

        // check decimals point range
        if (strpos($number, '.') > 0) {
            $numberParts = explode('.', $number);
            if ($numberParts[1] > 999) throw new \Exception('Decimals are out of available range.');
        }
    }


    /**
     * Convert number to words
     * 
     * @param mixed $amount amount
     * @param string $wordsSeparator custom words separator
     * @return string
     * 
     * @throws \Exception
     */
    public function convertNumber($number, string $wordsSeparator = ' '): string
    {
        // set output type
        $this->outputType = 'number';

        // check input
        $this->checkInput($number);

        // we have decimals
        if (strpos($number, '.') > 0) {

            // split by decimals point
            $numberParts = explode('.', $number);

            // units and decimals are empty
            if (empty($numberParts[0]) && empty($numberParts[1])) {
                $amountInWords = $this->amountToWords(0);

            // deimals are not empty
            } else {
                $amountInWords = (empty($numberParts[0]) ? $this->zero[1] : $this->amountToWords($numberParts[0])) . $this->defaultWordsSeparator;
                $amountInWords .= $this->decimalsPoints[$numberParts[0] >= 5 ? 5 : intval($numberParts[0])] . $this->defaultWordsSeparator;
                $amountInWords .= $this->decimalsToWords($numberParts[1]);
            }

        // no decimals
        } else {

            // get amount in words
            $amountInWords = $this->amountToWords($number);
        }

        // apply words separator if different from default
        if ($wordsSeparator != $this->defaultWordsSeparator) $amountInWords = $this->applyWordsSeparator($amountInWords, $wordsSeparator);

        return $amountInWords;
    }


    /**
     * Convert currency to words
     * 
     * @param mixed $amount amount
     * @param string $wordsSeparator custom words separator
     * @return string
     * 
     * @throws \Exception
     */
    public function convertCurrency($number, string $wordsSeparator = ' '): string
    {
        // set output type
        $this->outputType = 'currency';

        // check input
        $this->checkInput($number);

        // we have decimals
        if (strpos($number, '.') > 0) {

            // round to 2 decimals
            $number = round($number, 2);

            // split by decimals point, sanitize decimals
            $numberParts = explode('.', $number);
            $numberParts[1] = intval(substr($numberParts[1], 0, 2));

            // get amounts in words
            $amountInWordsUnits = $this->amountToWords($numberParts[0]);

            if ($numberParts[1] < 3) {
                $amountInWordsDecimals = $this->namesCurrencyDecimalsAmount[$numberParts[1]];
            } else {
                $amountInWordsDecimals = $this->amountToWords($numberParts[1]);
            }

            // get last 2 unit numbers
            if ($numberParts[0] < 0) $numberParts[0] *= -1;
            $numberParts[0] = intval(substr($numberParts[0], strlen($numberParts[0]) - 2, strlen($numberParts[0])));

            // finalize
            $amountInWords = $amountInWordsUnits . $this->defaultWordsSeparator . $this->namesCrownUnits[$numberParts[0] >= 5 ? 5 : $numberParts[0]] . $this->defaultWordsSeparator;
            $amountInWords .= $amountInWordsDecimals . $this->defaultWordsSeparator . $this->namesCrownDecimals[$numberParts[1] >= 5 ? 5 : $numberParts[1]];

            // no decimals
        } else {

            // get amount in words
            $amountInWords = $this->amountToWords($number);

            // get last 2 unit numbers
            $number = intval(substr($number, strlen($number) - 2, strlen($number)));

            // finalize
            $amountInWords .= $this->defaultWordsSeparator . $this->namesCrownUnits[$number >= 5 ? 5 : $number];
        }

        // apply words separator if different from default
        if ($wordsSeparator != $this->defaultWordsSeparator) $amountInWords = $this->applyWordsSeparator($amountInWords, $wordsSeparator);

        return $amountInWords;
    }

/*
    // currency to words
    public function CurrencyToWords($number, $separator = ' ')
    {
        $this->wordsSeparator = $separator;
        $this->outputType = 'currency';

        // we have decimals
        if (strpos($number, '.') > 0) {
            $numberParts = explode('.', $number);
            $numberParts[1] = substr($numberParts[1], 0, 2);

            $textKc = $this->numberToWords($numberParts[0]);
            $textH = $this->numberToWords($numberParts[1]);

            if ($numberParts[0] < 0) $numberParts[0] *= -1;
            $numberParts[0] = substr($numberParts[0], strlen($numberParts[0]) - 2, strlen($numberParts[0]));

            $currencyString = $textKc . $this->wordsSeparator;
            if ($numberParts[0] == 0 || $numberParts[0] >= 5) $currencyString .= 'korun';
            if ($numberParts[0] == 1) $currencyString .= 'koruna';
            if ($numberParts[0] > 1 && $numberParts[0] < 5) $currencyString .= 'koruny';
            $currencyString .= $this->wordsSeparator . $textH . $this->wordsSeparator;
            if ($numberParts[1] == 0 || $numberParts[1] >= 5) $currencyString .= 'haléřů';
            if ($numberParts[1] == 1) $currencyString .= 'haléř';
            if ($numberParts[1] > 1 && $numberParts[1] < 5) $currencyString .= 'haléře';

            return $currencyString;

        // no decimals
        } else {
            $currencyString = $this->numberToWords($number) . $this->wordsSeparator;

            $number = substr($number, strlen($number) - 2, strlen($number));

            if ($number == 0 || $number >= 5) $currencyString .= 'korun';
            if ($number == 1) $currencyString .= 'koruna';
            if ($number > 1 && $number < 5) $currencyString .= 'koruny';
            $currencyString .= $this->wordsSeparator . 'nula' . $this->wordsSeparator . 'haléřů';

            return $currencyString;
        }
    }

    private function ApplySeparator($amountInWords)
    {
        return str_replace(' ', $this->wordsSeparator, $amountInWords);
    }

    private function GetAmountPart_BelowHundred($amount)
    {
        if ($amount == 0) return "";
    
        $hundreds = floor($amount / 100);
    
        $amountBelowHundred = $amount - ($hundreds * 100);
    
        if ($amountBelowHundred == 0) return "";
    
        $tens = floor($amountBelowHundred / 10);
        $units = $amountBelowHundred - ($tens * 10);
    
        $result = "";
    
        if ($tens >= 2) {
            $result.= $this->namesTens[$tens - 2].$this->wordsSeparator;
        }
    
        if ($amount >= 1 && $amountBelowHundred < 20) {
            if ($this->outputType == "currency" && $amountBelowHundred == 2) {
                $result.= $this->two.$this->wordsSeparator;
            } else {
                $result.= $this->namesSmall[$amountBelowHundred - 1].$this->wordsSeparator;
            }
        } else if ($units > 0) {
            $result.= $this->namesSmall[$units - 1].$this->wordsSeparator;
        }
    
        return $result;
    }

    private function GetAmountPart_Hundreds($amount)
    {
        if ($amount < 100) return "";
    
        $thousands = floor($amount / 1000);
        $amountBelowThousand = $amount - ($thousands * 1000);
        if ($amountBelowThousand < 100) return "";
        $hundreds = floor($amountBelowThousand / 100);
    
        $result = "";
        $result .= $this->namesHundreds[$hundreds - 1] . $this->wordsSeparator;
    
        return $result;
    }
    
    private function GetAmountPart_Thousands($amount)
    {
        if ($amount < 1000) return "";
    
        $millions = floor($amount / 1000000);
        $amountBelowMillion = $amount - ($millions * 1000000);
        if ($amountBelowMillion < 100) return "";
    
        $thousands = $amountBelowMillion / 1000;
        $result = "";
    
        if ($thousands <= 4) {
            $result .= $this->namesThousands[$thousands - 1] . $this->wordsSeparator;
        } else {
            $result .= $this->AmmountWords($thousands) . $this->wordsSeparator.$this->namesThousands[count($this->namesThousands) - 1] . ($amount >= 2000 && $amount < 5000 ? 'e' : '') . $this->wordsSeparator;
        }
    
        return $result;
    }
    
    private function GetAmountPart_Millions($amount)
    {
        if ($amount < 1000000) return "";
    
        $millions = floor($amount / 1000000);
    
        $result = "";
    
        if ($millions <= 4) {
            $result.= $this->namesMillions[$millions - 1] . $this->wordsSeparator;
        } else {
            $result.= $this->AmmountWords($millions).$this->wordsSeparator.$this->namesMillions[count($this->namesMillions) - 1].$this->wordsSeparator;
        }
    
        return $result;
    }

    private function AmmountWords($amount)
    {
        if ($amount == 0) return $this->zero;
        if ($amount == 2) return $this->two; // aby to vrátilo "dvě" místo "dva"
    
        $result = "";
    
        if ($amount < 0) {
            $result .= $this->minus . $this->wordsSeparator;
            $amount *= -1;
        }

        if ($amount >= 1000000) {
            $result.= $this->GetAmountPart_Millions($amount);
        }
    
        if ($amount >= 1000) {
            $result.= $this->GetAmountPart_Thousands($amount);
        }
    
        if ($amount >= 100) {
            $result.= $this->GetAmountPart_Hundreds($amount);
        }
    
        $result.= $this->GetAmountPart_BelowHundred($amount);
    
        $result = $this->ApplySeparator($result);
    
        return $result;
    }

    // number to words
    public function NumberToWords($number, $separator = ' ')
    {
        $this->wordsSeparator = $separator;
        $this->outputType = 'number';

        return $this->AmmountWords($number);
    }
*/

}