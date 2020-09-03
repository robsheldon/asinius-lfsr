<?php

/*******************************************************************************
*                                                                              *
*   Asinius\LFSR\GaloisLFSR                                                    *
*                                                                              *
*   Provides an easy-to-use linear feedback shift register.                    *
*   See also: http://datagenetics.com/blog/november12017/index.html            *
*                                                                              *
*   LICENSE                                                                    *
*                                                                              *
*   Copyright (c) 2020 Rob Sheldon <rob@rescue.dev>                            *
*                                                                              *
*   Permission is hereby granted, free of charge, to any person obtaining a    *
*   copy of this software and associated documentation files (the "Software"), *
*   to deal in the Software without restriction, including without limitation  *
*   the rights to use, copy, modify, merge, publish, distribute, sublicense,   *
*   and/or sell copies of the Software, and to permit persons to whom the      *
*   Software is furnished to do so, subject to the following conditions:       *
*                                                                              *
*   The above copyright notice and this permission notice shall be included    *
*   in all copies or substantial portions of the Software.                     *
*                                                                              *
*   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS    *
*   OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF                 *
*   MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.     *
*   IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY       *
*   CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,       *
*   TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE          *
*   SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.                     *
*                                                                              *
*   https://opensource.org/licenses/MIT                                        *
*                                                                              *
*******************************************************************************/

/*******************************************************************************
*                                                                              *
*   Notes                                                                      *
*                                                                              *
*   The LFSR by default uses maximal-length LFSR cycles, but the constructor   *
*   allows the application to select different taps if necessary.              *
*                                                                              *
*******************************************************************************/

/*******************************************************************************
*                                                                              *
*   Examples                                                                   *
*                                                                              *
*******************************************************************************/

/*
    //  Create a new 8-bit LFSR with default tap values
    //  (not recommended for production!)
    \Asinius\LFSR\GaloisLFSR::ENABLE_DANGEROUS_UNSAFE_DEFAULTS();
    $lfsr = new \Asinius\LFSR\GaloisLFSR(8, $starting_value = 1);
    //  Read the next value from this cycle.
    $value = $lfsr->read();
*/

namespace Asinius\LFSR;

/*******************************************************************************
*                                                                              *
*   Constants                                                                  *
*                                                                              *
*******************************************************************************/

//  For convenience, this library provides default taps for many LFSR bit sizes.
//  YOU SHOULD NOT USE THESE IN PRODUCTION.
//  Use your own LFSR taps. Anyone who is able to figure out your taps and has
//  any number from your cycle will be able to determine the next number.
//  These are taken from "Table of Linear Feedback Shift Registers", Ward &
//  Molteno, 2007.
//  I only imported LFSRs for up to 256-bit integer support. Beyond that,
//  you're on your own. You can find additional max-length tap values at
//  http://users.ece.cmu.edu/~koopman/lfsr/index.html
const LFSR_DEFAULT_TAPS = [
    [],
    [],
    [2, 1],
    [3, 2],
    [4, 3],
    [5, 4, 3, 2],
    [6, 5, 3, 2],
    [7, 6, 5, 4],
    [8, 6, 5, 4],
    [9, 8, 6, 5],
    [10, 9, 7, 6],
    [11, 10, 9, 7],
    [12, 11, 8, 6],
    [13, 12, 10, 9],
    [14, 13, 11, 9],
    [15, 14, 13, 11],
    [16, 14, 13, 11],
    [17, 16, 15, 14],
    [18, 17, 16, 13],
    [19, 18, 17, 14],
    [20, 19, 16, 14],
    [21, 20, 19, 16],
    [22, 19, 18, 17],
    [23, 22, 20, 18],
    [24, 23, 21, 20],
    [25, 24, 23, 22],
    [26, 25, 24, 20],
    [27, 26, 25, 22],
    [28, 27, 24, 22],
    [29, 28, 27, 25],
    [30, 29, 26, 24],
    [31, 30, 29, 28],
    [32, 30, 26, 25],
    [33, 32, 29, 27],
    [34, 31, 30, 26],
    [35, 34, 28, 27],
    [36, 35, 29, 28],
    [37, 36, 33, 31],
    [38, 37, 33, 32],
    [39, 38, 35, 32],
    [40, 37, 36, 35],
    [41, 40, 39, 38],
    [42, 40, 37, 35],
    [43, 42, 38, 37],
    [44, 42, 39, 38],
    [45, 44, 42, 41],
    [46, 40, 39, 38],
    [47, 46, 43, 42],
    [48, 44, 41, 39],
    [49, 45, 44, 43],
    [50, 48, 47, 46],
    [51, 50, 48, 45],
    [52, 51, 49, 46],
    [53, 52, 51, 47],
    [54, 51, 48, 46],
    [55, 54, 53, 49],
    [56, 54, 52, 49],
    [57, 55, 54, 52],
    [58, 57, 53, 52],
    [59, 57, 55, 52],
    [60, 58, 56, 55],
    [61, 60, 59, 56],
    [62, 59, 57, 56],
    [63, 62, 59, 58],
    [64, 63, 61, 60],
    [65, 64, 62, 61],
    [66, 60, 58, 57],
    [67, 66, 65, 62],
    [68, 67, 63, 61],
    [69, 67, 64, 63],
    [70, 69, 67, 65],
    [71, 70, 68, 66],
    [72, 69, 63, 62],
    [73, 71, 70, 69],
    [74, 71, 70, 67],
    [75, 74, 72, 69],
    [76, 74, 72, 71],
    [77, 75, 72, 71],
    [78, 77, 76, 71],
    [79, 77, 76, 75],
    [80, 78, 76, 71],
    [81, 79, 78, 75],
    [82, 78, 76, 73],
    [83, 81, 79, 76],
    [84, 83, 77, 75],
    [85, 84, 83, 77],
    [86, 84, 81, 80],
    [87, 86, 82, 80],
    [88, 80, 79, 77],
    [89, 86, 84, 83],
    [90, 88, 87, 85],
    [91, 90, 86, 83],
    [92, 90, 87, 86],
    [93, 91, 90, 87],
    [94, 93, 89, 88],
    [95, 94, 90, 88],
    [96, 90, 87, 86],
    [97, 95, 93, 91],
    [98, 97, 91, 90],
    [99, 95, 94, 92],
    [100, 98, 93, 92],
    [101, 100, 95, 94],
    [102, 99, 97, 96],
    [103, 102, 99, 94],
    [104, 103, 94, 93],
    [105, 104, 99, 98],
    [106, 105, 101, 100],
    [107, 105, 99, 98],
    [108, 103, 97, 96],
    [109, 107, 105, 104],
    [110, 109, 106, 104],
    [111, 109, 107, 104],
    [112, 108, 106, 101],
    [113, 111, 110, 108],
    [114, 113, 112, 103],
    [115, 110, 108, 107],
    [116, 114, 111, 110],
    [117, 116, 115, 112],
    [118, 116, 113, 112],
    [119, 116, 111, 110],
    [120, 118, 114, 111],
    [121, 120, 116, 113],
    [122, 121, 120, 116],
    [123, 122, 119, 115],
    [124, 119, 118, 117],
    [125, 120, 119, 118],
    [126, 124, 122, 119],
    [127, 126, 124, 120],
    [128, 127, 126, 121],
    [129, 128, 125, 124],
    [130, 129, 128, 125],
    [131, 129, 128, 123],
    [132, 130, 127, 123],
    [133, 131, 125, 124],
    [134, 133, 129, 127],
    [135, 132, 131, 129],
    [136, 134, 133, 128],
    [137, 136, 133, 126],
    [138, 137, 131, 130],
    [139, 136, 134, 131],
    [140, 139, 136, 132],
    [141, 140, 135, 128],
    [142, 141, 139, 132],
    [143, 141, 140, 138],
    [144, 142, 140, 137],
    [145, 144, 140, 139],
    [146, 144, 143, 141],
    [147, 145, 143, 136],
    [148, 145, 143, 141],
    [149, 142, 140, 139],
    [150, 148, 147, 142],
    [151, 150, 149, 148],
    [152, 150, 149, 146],
    [153, 149, 148, 145],
    [154, 153, 149, 145],
    [155, 151, 150, 148],
    [156, 153, 151, 147],
    [157, 155, 152, 151],
    [158, 153, 152, 150],
    [159, 156, 153, 148],
    [160, 158, 157, 155],
    [161, 159, 158, 155],
    [162, 158, 155, 154],
    [163, 160, 157, 156],
    [164, 159, 158, 152],
    [165, 162, 157, 156],
    [166, 164, 163, 156],
    [167, 165, 163, 161],
    [168, 162, 159, 152],
    [169, 164, 163, 161],
    [170, 169, 166, 161],
    [171, 169, 166, 165],
    [172, 169, 165, 161],
    [173, 171, 168, 165],
    [174, 169, 166, 165],
    [175, 173, 171, 169],
    [176, 167, 165, 164],
    [177, 175, 174, 172],
    [178, 176, 171, 170],
    [179, 178, 177, 175],
    [180, 173, 170, 168],
    [181, 180, 175, 174],
    [182, 181, 176, 174],
    [183, 179, 176, 175],
    [184, 177, 176, 175],
    [185, 184, 182, 177],
    [186, 180, 178, 177],
    [187, 182, 181, 180],
    [188, 186, 183, 182],
    [189, 187, 184, 183],
    [190, 188, 184, 177],
    [191, 187, 185, 184],
    [192, 190, 178, 177],
    [193, 189, 186, 184],
    [194, 192, 191, 190],
    [195, 193, 192, 187],
    [196, 194, 187, 185],
    [197, 195, 193, 188],
    [198, 193, 190, 183],
    [199, 198, 195, 190],
    [200, 198, 197, 195],
    [201, 199, 198, 195],
    [202, 198, 196, 195],
    [203, 202, 196, 195],
    [204, 201, 200, 194],
    [205, 203, 200, 196],
    [206, 201, 197, 196],
    [207, 206, 201, 198],
    [208, 207, 205, 199],
    [209, 207, 206, 204],
    [210, 207, 206, 198],
    [211, 203, 201, 200],
    [212, 209, 208, 205],
    [213, 211, 208, 207],
    [214, 213, 211, 209],
    [215, 212, 210, 209],
    [216, 215, 213, 209],
    [217, 213, 212, 211],
    [218, 217, 211, 210],
    [219, 218, 215, 211],
    [220, 211, 210, 208],
    [221, 219, 215, 213],
    [222, 220, 217, 214],
    [223, 221, 219, 218],
    [224, 222, 217, 212],
    [225, 224, 220, 215],
    [226, 223, 219, 216],
    [227, 223, 218, 217],
    [228, 226, 217, 216],
    [229, 228, 225, 219],
    [230, 224, 223, 222],
    [231, 229, 227, 224],
    [232, 228, 223, 221],
    [233, 232, 229, 224],
    [234, 232, 225, 223],
    [235, 234, 229, 226],
    [236, 229, 228, 226],
    [237, 236, 233, 230],
    [238, 237, 236, 233],
    [239, 238, 232, 227],
    [240, 237, 235, 232],
    [241, 237, 233, 232],
    [242, 241, 236, 231],
    [243, 242, 238, 235],
    [244, 243, 240, 235],
    [245, 244, 241, 239],
    [246, 245, 244, 235],
    [247, 245, 243, 238],
    [248, 238, 234, 233],
    [249, 248, 245, 242],
    [250, 247, 245, 240],
    [251, 249, 247, 244],
    [252, 251, 247, 241],
    [253, 252, 247, 246],
    [254, 253, 252, 247],
    [255, 253, 252, 250],
];



/*******************************************************************************
*                                                                              *
*   \Asinius\LFSR\GaloisLFSR                                                   *
*                                                                              *
*******************************************************************************/

class GaloisLFSR
{
    protected           $_last_value = 0;
    protected           $_taps = 0;
    protected           $_stop_value = 0;
    private     static  $_dangerous_mode = false;


    /**
     * Calling this function allows LFSRs to be created without specifying
     * custom tap values. This is purposely designed to annoy developers and
     * their PHP automated code inspection tools. Please see README.md for
     * more information about choosing your own tap values.
     *
     * @return  void
     */
    public static function ENABLE_DANGEROUS_UNSAFE_DEFAULTS ()
    {
        static::$_dangerous_mode = true;
    }


    /**
     * Convert an array of bit indexes to an integer value.
     *
     * @param   array   $taps
     *
     * @internal
     *
     * @return  int
     */
    protected static function _taps_array_to_int ($taps)
    {
        return array_reduce($taps, function($taps_int, $tap){
            $taps_int |= 2 ** ($tap - 1);
            return $taps_int;
        }, 0);
    }


    /**
     * Create a new linear feedback shift register.
     *
     * @param   integer     $bits
     * @param   integer     $seed
     * @param   int|array   $taps
     *
     * @throws  TypeError
     * @throws  RuntimeException
     *
     * @return  lfsr
     */
    public function __construct ($bits, $seed, $taps = null)
    {
        if ( ! is_int($bits) ) {
            throw new \TypeError('Invalid argument type for $bits: expecting integer, got ' . gettype($bits), \Asinius\EINVAL);
        }
        if ( ! is_int($seed) ) {
            throw new \TypeError('Invalid argument type for $seed: expecting integer, got ' . gettype($seed), \Asinius\EINVAL);
        }
        //  The bit size for the LFSR must not exceed the runtime's
        //  maximum number of bits for an integer.
        //  Since PHP doesn't really have an "unsigned" type, the
        //  safe thing to do here is to ensure that $bits <=
        //  PHP_INT_SIZE * 8 - 1 (accounting for the unused sign bit).
        if ( $bits > (PHP_INT_SIZE * 8 - 1) ) {
            throw new \RuntimeException("This environment does not support ${bits}-bit LFSRs", \Asinius\EINVAL);
        }
        //  If the seed is less than 0 or greater than PHP_INT_MAX, that won't
        //  work either.
        if ( $seed < 0 || $seed > PHP_INT_MAX ) {
            throw new \RuntimeException("This environment doesn't support this seed value", \Asinius\EINVAL);
        }
        if ( $seed == 0 ) {
            throw new \RuntimeException('$seed must be greater than 0', \Asinius\EINVAL);
        }
        //  Calculate the number of bits required for the seed and make sure
        //  that doesn't exceed $bits.
        if ( $bits < floor(log($seed, 2)) + 1 ) {
            throw new \RuntimeException("\$seed is larger than $bits bits", \Asinius\EINVAL);
        }
        $this->_last_value = $seed;
        //  Finally, set up and validate the LFSR taps.
        if ( is_null($taps) ) {
            if ( ! static::$_dangerous_mode ) {
                throw new \RuntimeException("You must specify a custom tap solution for $bits bits; please see the README.md included with this library for more information", \Asinius\EINVAL);
            }
            if ( empty(LFSR_DEFAULT_TAPS[$bits]) ) {
                throw new \RuntimeException("No default tap solution for $bits bits", \Asinius\EINVAL);
            }
            $taps = LFSR_DEFAULT_TAPS[$bits];
        }
        if ( is_array($taps) ) {
            $taps = array_values($taps);
            if ( max($taps) > $bits ) {
                throw new \RuntimeException('The greatest bit in the selected tap is more than the number of bits used for this LFSR', \Asinius\EINVAL);
            }
            $taps = static::_taps_array_to_int($taps);
        }
        if ( is_int($taps) ) {
            if ( $bits < floor(log($taps, 2)) + 1 ) {
                throw new \RuntimeException("\$taps is larger than $bits bits", \Asinius\EINVAL);
            }
            if ( ! static::$_dangerous_mode && static::_taps_array_to_int(LFSR_DEFAULT_TAPS[$bits]) == $taps ) {
                throw new \RuntimeException("This tap value matches the default value for $bits bits and is not safe to use in production", \Asinius\EINVAL);
            }
            $this->_taps = $taps;
        }
        else {
            throw new \TypeError('Invalid argument type for $taps: expecting integer or array, got ' . gettype($taps), \Asinius\EINVAL);
        }
    }


    /**
     * Return the next value in the cycle, using a very simple Galois LFSR.
     * The returned value is stored internally and used in the next call to
     * read() or peek().
     *
     * @return  int
     */
    public function read ()
    {
        $next = $this->peek();
        if ( $next === null ) {
            return null;
        }
        if ( $next === $this->_stop_value ) {
            $this->_last_value = null;
        }
        else {
            $this->_last_value = $next;
        }
        return $next;
    }


    /**
     * Return the next value in the cycle but don't store it; the next read()
     * or peek() will return the same value.
     *
     * @return  int
     */
    public function peek ()
    {
        if ( $this->_last_value === null ) {
            return null;
        }
        if ( $this->_last_value & 1 ) {
            return (int) ($this->_last_value >> 1) ^ $this->_taps;
        }
        return (int) ($this->_last_value >> 1);
    }


    /**
     * Set a value that will cause the LFSR to return only null values AFTER
     * returning the given value. This would be typically called with the seed
     * value so that the LFSR will return one complete cycle, including the
     * seed, and then halt.
     *
     * @param   int     $value
     *
     * @return  void
     */
    public function stop_after_value ($value)
    {
        if ( ! is_int($value) ) {
            throw new \RuntimeException("\$value must be an integer", \Asinius\EINVAL);
        }
        if ( $this->_stop_value !== 0 ) {
            throw new \RuntimeException('A stop value of ' . $this->_stop_value . ' has already been set for this LFSR', \Asinius\EINVAL);
        }
        $this->_stop_value = $value;
    }
}
