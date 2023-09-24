<?php

/**
* The most awesome function for labeling
* Generate a parking name based on a given number.
*
* The method converts converts a number to a label.
* It goes A..Z then AA, AB..AZ, BA..ZZ then AAA and so forth
*
* @param int $number The number to generate the parking name from.
* @return string The generated parking name.
*/
if (! function_exists('generateParkingLabel')) {
    function generateParkingLabel($number)
    {
        $result = '';

        while ($number > 0) {
            $remainder = ($number - 1) % 26; // Convert 1-based to 0-based index
            $result = chr(65 + $remainder) . $result; // Convert ASCII value to letter
            $number = floor(($number - 1) / 26);
        }

        return $result;
    }
}

/**
* The reverse function of the most awesome function for labeling
* Turn a label e.g. AAA into a number 703, according to the generateParkingLabel
*
* @param int $str The parking nameparking label to generate the number from.
* @return string The number representing the parking label.
*/
if (! function_exists('generateNumberFromParkingLabel')) {
    function generateNumberFromParkingLabel($str) {
        $str = strtoupper($str); // Ensure the input is in uppercase
        $length = strlen($str);
        $result = 0;

        for ($i = 0; $i < $length; $i++) {
            $charValue = ord($str[$i]) - 64; // Convert letter to its 1-based position in the alphabet
            $result = $result * 26 + $charValue;
        }

        return $result;
    }
}