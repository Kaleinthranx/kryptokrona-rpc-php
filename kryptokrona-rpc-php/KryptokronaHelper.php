<?php

/*
    Author: Kaleinthranx
    https://github.com/kaleinthranx
*/

class KryptokronaHelper {
    private $g_decimalDivisor = 100000;

    public function toAtomicUnits($amount): float {
        return round($amount * $this->g_decimalDivisor);
    }

    public function fromAtomicUnits(float $amount): float {
        if(strpos($amount, '.') !== false) {
            return $amount;
        }
        return round($amount / $this->g_decimalDivisor, strlen($this->g_decimalDivisor) - 1);
    }

    public function fromAtomicUnitsRecursive(array &$inputArray, array $keysToConvert) {
        array_walk_recursive(
            $inputArray,
            function (&$value, $key) use ($keysToConvert) {
                if(in_array($key, $keysToConvert)) {
                    $value = $this->fromAtomicUnits($value);
                }
            }
        );
    }

    public function toAtomicUnitsRecursive(array &$inputArray, array $keysToConvert) {
        array_walk_recursive(
            $inputArray,
            function (&$value, $key) use ($keysToConvert) {
                if(in_array($key, $keysToConvert)) {
                    $value = $this->toAtomicUnits($value);
                }
            }
        );
    }

    public function isHex($str) {
        $regex = '/^[0-9a-fA-F]+$/';
        return preg_match($regex, $str);
    }
}