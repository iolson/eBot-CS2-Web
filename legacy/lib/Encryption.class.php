<?php
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/*  AES implementation in PHP */
/*    (c) Chris Veness 2005-2011 www.movable-type.co.uk/scripts */
/*    Right of free use is granted for all commercial or non-commercial use providing this */
/*    copyright notice is retainded. No warranty of any form is offered. */
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

class Aes
{
    /**
     * AES Cipher function: encrypt 'input' with Rijndael algorithm
     *
     * @param input message as byte-array (16 bytes)
     * @param w     key schedule as 2D byte-array (Nr+1 x Nb bytes) -
     *              generated from the cipher key by keyExpansion()
     * @return ciphertext as byte-array (16 bytes)
     */
    public static function cipher($input, $w)    // main cipher function [§5.1]
    {$Nb = 4;                 // block size (in words): no of columns in state (fixed at 4 for AES)
        $Nr = count($w) / $Nb - 1; // no of rounds: 10/12/14 for 128/192/256-bit keys

        $state = [];  // initialise 4xNb byte-array 'state' with input [§3.4]
        for ($i = 0; $i < 4 * $Nb; $i++) {
            $state[$i % 4][floor($i / 4)] = $input[$i];
        }

        $state = self::addRoundKey($state, $w, 0, $Nb);

        for ($round = 1; $round < $Nr; $round++) {  // apply Nr rounds
            $state = self::subBytes($state, $Nb);
            $state = self::shiftRows($state, $Nb);
            $state = self::mixColumns($state, $Nb);
            $state = self::addRoundKey($state, $w, $round, $Nb);
        }

        $state = self::subBytes($state, $Nb);
        $state = self::shiftRows($state, $Nb);
        $state = self::addRoundKey($state, $w, $Nr, $Nb);

        $output = [4 * $Nb];  // convert state to 1-d array before returning [§3.4]
        for ($i = 0; $i < 4 * $Nb; $i++) {
            $output[$i] = $state[$i % 4][floor($i / 4)];
        }

        return $output;
    }

    private static function addRoundKey($state, $w, $rnd, $Nb)  // xor Round Key into state S [§5.1.4]
    {for ($r = 0; $r < 4; $r++) {
        for ($c = 0; $c < $Nb; $c++) {
            $state[$r][$c] ^= $w[$rnd * 4 + $c][$r];
        }
    }

        return $state;
    }

    private static function subBytes($s, $Nb)    // apply SBox to state S [§5.1.1]
    {for ($r = 0; $r < 4; $r++) {
        for ($c = 0; $c < $Nb; $c++) {
            $s[$r][$c] = self::$sBox[$s[$r][$c]];
        }
    }

        return $s;
    }

    private static function shiftRows($s, $Nb)    // shift row r of state S left by r bytes [§5.1.2]
    {$t = [4];
        for ($r = 1; $r < 4; $r++) {
            for ($c = 0; $c < 4; $c++) {
                $t[$c] = $s[$r][($c + $r) % $Nb];
            }  // shift into temp copy
            for ($c = 0; $c < 4; $c++) {
                $s[$r][$c] = $t[$c];
            }           // and copy back
        }          // note that this will work for Nb=4,5,6, but not 7,8 (always 4 for AES):

        return $s;  // see fp.gladman.plus.com/cryptography_technology/rijndael/aes.spec.311.pdf
    }

    private static function mixColumns($s, $Nb)   // combine bytes of each col of state S [§5.1.3]
    {for ($c = 0; $c < 4; $c++) {
        $a = [4];  // 'a' is a copy of the current column from 's'
        $b = [4];  // 'b' is a•{02} in GF(2^8)
        for ($i = 0; $i < 4; $i++) {
            $a[$i] = $s[$i][$c];
            $b[$i] = $s[$i][$c] & 0x80 ? $s[$i][$c] << 1 ^ 0x011B : $s[$i][$c] << 1;
        }
        // a[n] ^ b[n] is a•{03} in GF(2^8)
        $s[0][$c] = $b[0] ^ $a[1] ^ $b[1] ^ $a[2] ^ $a[3]; // 2*a0 + 3*a1 + a2 + a3
        $s[1][$c] = $a[0] ^ $b[1] ^ $a[2] ^ $b[2] ^ $a[3]; // a0 * 2*a1 + 3*a2 + a3
        $s[2][$c] = $a[0] ^ $a[1] ^ $b[2] ^ $a[3] ^ $b[3]; // a0 + a1 + 2*a2 + 3*a3
        $s[3][$c] = $a[0] ^ $b[0] ^ $a[1] ^ $a[2] ^ $b[3]; // 3*a0 + a1 + a2 + 2*a3
    }

        return $s;
    }

    /**
     * Key expansion for Rijndael cipher(): performs key expansion on cipher key
     * to generate a key schedule
     *
     * @param key cipher key byte-array (16 bytes)
     * @return key schedule as 2D byte-array (Nr+1 x Nb bytes)
     */
    public static function keyExpansion($key)  // generate Key Schedule from Cipher Key [§5.2]
    {$Nb = 4;              // block size (in words): no of columns in state (fixed at 4 for AES)
        $Nk = count($key) / 4;  // key length (in words): 4/6/8 for 128/192/256-bit keys
        $Nr = $Nk + 6;        // no of rounds: 10/12/14 for 128/192/256-bit keys

        $w = [];
        $temp = [];

        for ($i = 0; $i < $Nk; $i++) {
            $r = [$key[4 * $i], $key[4 * $i + 1], $key[4 * $i + 2], $key[4 * $i + 3]];
            $w[$i] = $r;
        }

        for ($i = $Nk; $i < ($Nb * ($Nr + 1)); $i++) {
            $w[$i] = [];
            for ($t = 0; $t < 4; $t++) {
                $temp[$t] = $w[$i - 1][$t];
            }
            if ($i % $Nk == 0) {
                $temp = self::subWord(self::rotWord($temp));
                for ($t = 0; $t < 4; $t++) {
                    $temp[$t] ^= self::$rCon[$i / $Nk][$t];
                }
            } elseif ($Nk > 6 && $i % $Nk == 4) {
                $temp = self::subWord($temp);
            }
            for ($t = 0; $t < 4; $t++) {
                $w[$i][$t] = $w[$i - $Nk][$t] ^ $temp[$t];
            }
        }

        return $w;
    }

    private static function subWord($w)    // apply SBox to 4-byte word w
    {for ($i = 0; $i < 4; $i++) {
        $w[$i] = self::$sBox[$w[$i]];
    }

        return $w;
    }

    private static function rotWord($w)    // rotate 4-byte word w left by one byte
    {$tmp = $w[0];
        for ($i = 0; $i < 3; $i++) {
            $w[$i] = $w[$i + 1];
        }
        $w[3] = $tmp;

        return $w;
    }

    // sBox is pre-computed multiplicative inverse in GF(2^8) used in subBytes and keyExpansion [§5.1.1]
    private static $sBox = [
        0x63, 0x7C, 0x77, 0x7B, 0xF2, 0x6B, 0x6F, 0xC5, 0x30, 0x01, 0x67, 0x2B, 0xFE, 0xD7, 0xAB, 0x76,
        0xCA, 0x82, 0xC9, 0x7D, 0xFA, 0x59, 0x47, 0xF0, 0xAD, 0xD4, 0xA2, 0xAF, 0x9C, 0xA4, 0x72, 0xC0,
        0xB7, 0xFD, 0x93, 0x26, 0x36, 0x3F, 0xF7, 0xCC, 0x34, 0xA5, 0xE5, 0xF1, 0x71, 0xD8, 0x31, 0x15,
        0x04, 0xC7, 0x23, 0xC3, 0x18, 0x96, 0x05, 0x9A, 0x07, 0x12, 0x80, 0xE2, 0xEB, 0x27, 0xB2, 0x75,
        0x09, 0x83, 0x2C, 0x1A, 0x1B, 0x6E, 0x5A, 0xA0, 0x52, 0x3B, 0xD6, 0xB3, 0x29, 0xE3, 0x2F, 0x84,
        0x53, 0xD1, 0x00, 0xED, 0x20, 0xFC, 0xB1, 0x5B, 0x6A, 0xCB, 0xBE, 0x39, 0x4A, 0x4C, 0x58, 0xCF,
        0xD0, 0xEF, 0xAA, 0xFB, 0x43, 0x4D, 0x33, 0x85, 0x45, 0xF9, 0x02, 0x7F, 0x50, 0x3C, 0x9F, 0xA8,
        0x51, 0xA3, 0x40, 0x8F, 0x92, 0x9D, 0x38, 0xF5, 0xBC, 0xB6, 0xDA, 0x21, 0x10, 0xFF, 0xF3, 0xD2,
        0xCD, 0x0C, 0x13, 0xEC, 0x5F, 0x97, 0x44, 0x17, 0xC4, 0xA7, 0x7E, 0x3D, 0x64, 0x5D, 0x19, 0x73,
        0x60, 0x81, 0x4F, 0xDC, 0x22, 0x2A, 0x90, 0x88, 0x46, 0xEE, 0xB8, 0x14, 0xDE, 0x5E, 0x0B, 0xDB,
        0xE0, 0x32, 0x3A, 0x0A, 0x49, 0x06, 0x24, 0x5C, 0xC2, 0xD3, 0xAC, 0x62, 0x91, 0x95, 0xE4, 0x79,
        0xE7, 0xC8, 0x37, 0x6D, 0x8D, 0xD5, 0x4E, 0xA9, 0x6C, 0x56, 0xF4, 0xEA, 0x65, 0x7A, 0xAE, 0x08,
        0xBA, 0x78, 0x25, 0x2E, 0x1C, 0xA6, 0xB4, 0xC6, 0xE8, 0xDD, 0x74, 0x1F, 0x4B, 0xBD, 0x8B, 0x8A,
        0x70, 0x3E, 0xB5, 0x66, 0x48, 0x03, 0xF6, 0x0E, 0x61, 0x35, 0x57, 0xB9, 0x86, 0xC1, 0x1D, 0x9E,
        0xE1, 0xF8, 0x98, 0x11, 0x69, 0xD9, 0x8E, 0x94, 0x9B, 0x1E, 0x87, 0xE9, 0xCE, 0x55, 0x28, 0xDF,
        0x8C, 0xA1, 0x89, 0x0D, 0xBF, 0xE6, 0x42, 0x68, 0x41, 0x99, 0x2D, 0x0F, 0xB0, 0x54, 0xBB, 0x16];

    // rCon is Round Constant used for the Key Expansion [1st col is 2^(r-1) in GF(2^8)] [§5.2]
    private static $rCon = [
        [0x00, 0x00, 0x00, 0x00],
        [0x01, 0x00, 0x00, 0x00],
        [0x02, 0x00, 0x00, 0x00],
        [0x04, 0x00, 0x00, 0x00],
        [0x08, 0x00, 0x00, 0x00],
        [0x10, 0x00, 0x00, 0x00],
        [0x20, 0x00, 0x00, 0x00],
        [0x40, 0x00, 0x00, 0x00],
        [0x80, 0x00, 0x00, 0x00],
        [0x1B, 0x00, 0x00, 0x00],
        [0x36, 0x00, 0x00, 0x00]];
}

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
?>

<?php
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/*  AES counter (CTR) mode implementation in PHP */
/*    (c) Chris Veness 2005-2011 www.movable-type.co.uk/scripts */
/*    Right of free use is granted for all commercial or non-commercial use providing this */
/*    copyright notice is retainded. No warranty of any form is offered. */
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

class Encryption extends Aes
{
    /**
     * Encrypt a text using AES encryption in Counter mode of operation
     *  - see http://csrc.nist.gov/publications/nistpubs/800-38a/sp800-38a.pdf
     *
     * Unicode multi-byte character safe
     *
     * @param plaintext source text to be encrypted
     * @param password  the password to use to generate a key
     * @param nBits     number of bits to be used in the key (128, 192, or 256)
     * @return encrypted text
     */
    public static function encrypt($plaintext, $password, $nBits)
    {
        $blockSize = 16;  // block size fixed at 16 bytes / 128 bits (Nb=4) for AES
        if (! ($nBits == 128 || $nBits == 192 || $nBits == 256)) {
            return '';
        }  // standard allows 128/192/256 bit keys
        // note PHP (5) gives us plaintext and password in UTF8 encoding!

        // use AES itself to encrypt password to get cipher key (using plain password as source for
        // key expansion) - gives us well encrypted key
        $nBytes = $nBits / 8;  // no bytes in key
        $pwBytes = [];
        for ($i = 0; $i < $nBytes; $i++) {
            $pwBytes[$i] = ord(substr($password, $i, 1)) & 0xFF;
        }
        $key = Aes::cipher($pwBytes, Aes::keyExpansion($pwBytes));
        $key = array_merge($key, array_slice($key, 0, $nBytes - 16));  // expand key to 16/24/32 bytes long

        // initialise 1st 8 bytes of counter block with nonce (NIST SP800-38A §B.2): [0-1] = millisec,
        // [2-3] = random, [4-7] = seconds, giving guaranteed sub-ms uniqueness up to Feb 2106
        $counterBlock = [];
        $nonce = floor(microtime(true) * 1000);   // timestamp: milliseconds since 1-Jan-1970
        $nonceMs = $nonce % 1000;
        $nonceSec = floor($nonce / 1000);
        $nonceRnd = floor(rand(0, 0xFFFF));

        for ($i = 0; $i < 2; $i++) {
            $counterBlock[$i] = self::urs($nonceMs, $i * 8) & 0xFF;
        }
        for ($i = 0; $i < 2; $i++) {
            $counterBlock[$i + 2] = self::urs($nonceRnd, $i * 8) & 0xFF;
        }
        for ($i = 0; $i < 4; $i++) {
            $counterBlock[$i + 4] = self::urs($nonceSec, $i * 8) & 0xFF;
        }

        // and convert it to a string to go on the front of the ciphertext
        $ctrTxt = '';
        for ($i = 0; $i < 8; $i++) {
            $ctrTxt .= chr($counterBlock[$i]);
        }

        // generate key schedule - an expansion of the key into distinct Key Rounds for each round
        $keySchedule = Aes::keyExpansion($key);
        // print_r($keySchedule);

        $blockCount = ceil(strlen($plaintext) / $blockSize);
        $ciphertxt = [];  // ciphertext as array of strings

        for ($b = 0; $b < $blockCount; $b++) {
            // set counter (block #) in last 8 bytes of counter block (leaving nonce in 1st 8 bytes)
            // done in two stages for 32-bit ops: using two words allows us to go past 2^32 blocks (68GB)
            for ($c = 0; $c < 4; $c++) {
                $counterBlock[15 - $c] = self::urs($b, $c * 8) & 0xFF;
            }
            for ($c = 0; $c < 4; $c++) {
                $counterBlock[15 - $c - 4] = self::urs($b / 0x100000000, $c * 8);
            }

            $cipherCntr = Aes::cipher($counterBlock, $keySchedule);  // -- encrypt counter block --

            // block size is reduced on final block
            $blockLength = $b < $blockCount - 1 ? $blockSize : (strlen($plaintext) - 1) % $blockSize + 1;
            $cipherByte = [];

            for ($i = 0; $i < $blockLength; $i++) {  // -- xor plaintext with ciphered counter byte-by-byte --
                $cipherByte[$i] = $cipherCntr[$i] ^ ord(substr($plaintext, $b * $blockSize + $i, 1));
                $cipherByte[$i] = chr($cipherByte[$i]);
            }
            $ciphertxt[$b] = implode('', $cipherByte);  // escape troublesome characters in ciphertext
        }

        // implode is more efficient than repeated string concatenation
        $ciphertext = $ctrTxt.implode('', $ciphertxt);
        $ciphertext = base64_encode($ciphertext);

        return $ciphertext;
    }

    /**
     * Decrypt a text encrypted by AES in counter mode of operation
     *
     * @param ciphertext source text to be decrypted
     * @param password   the password to use to generate a key
     * @param nBits      number of bits to be used in the key (128, 192, or 256)
     * @return decrypted text
     */
    public static function decrypt($ciphertext, $password, $nBits)
    {
        $blockSize = 16;  // block size fixed at 16 bytes / 128 bits (Nb=4) for AES
        if (! ($nBits == 128 || $nBits == 192 || $nBits == 256)) {
            return '';
        }  // standard allows 128/192/256 bit keys
        $ciphertext = base64_decode($ciphertext);

        // use AES to encrypt password (mirroring encrypt routine)
        $nBytes = $nBits / 8;  // no bytes in key
        $pwBytes = [];
        for ($i = 0; $i < $nBytes; $i++) {
            $pwBytes[$i] = ord(substr($password, $i, 1)) & 0xFF;
        }
        $key = Aes::cipher($pwBytes, Aes::keyExpansion($pwBytes));
        $key = array_merge($key, array_slice($key, 0, $nBytes - 16));  // expand key to 16/24/32 bytes long

        // recover nonce from 1st element of ciphertext
        $counterBlock = [];
        $ctrTxt = substr($ciphertext, 0, 8);
        for ($i = 0; $i < 8; $i++) {
            $counterBlock[$i] = ord(substr($ctrTxt, $i, 1));
        }

        // generate key schedule
        $keySchedule = Aes::keyExpansion($key);

        // separate ciphertext into blocks (skipping past initial 8 bytes)
        $nBlocks = ceil((strlen($ciphertext) - 8) / $blockSize);
        $ct = [];
        for ($b = 0; $b < $nBlocks; $b++) {
            $ct[$b] = substr($ciphertext, 8 + $b * $blockSize, 16);
        }
        $ciphertext = $ct;  // ciphertext is now array of block-length strings

        // plaintext will get generated block-by-block into array of block-length strings
        $plaintxt = [];

        for ($b = 0; $b < $nBlocks; $b++) {
            // set counter (block #) in last 8 bytes of counter block (leaving nonce in 1st 8 bytes)
            for ($c = 0; $c < 4; $c++) {
                $counterBlock[15 - $c] = self::urs($b, $c * 8) & 0xFF;
            }
            for ($c = 0; $c < 4; $c++) {
                $counterBlock[15 - $c - 4] = self::urs(($b + 1) / 0x100000000 - 1, $c * 8) & 0xFF;
            }

            $cipherCntr = Aes::cipher($counterBlock, $keySchedule);  // encrypt counter block

            $plaintxtByte = [];
            for ($i = 0; $i < strlen($ciphertext[$b]); $i++) {
                // -- xor plaintext with ciphered counter byte-by-byte --
                $plaintxtByte[$i] = $cipherCntr[$i] ^ ord(substr($ciphertext[$b], $i, 1));
                $plaintxtByte[$i] = chr($plaintxtByte[$i]);

            }
            $plaintxt[$b] = implode('', $plaintxtByte);
        }

        // join array of blocks into single plaintext string
        $plaintext = implode('', $plaintxt);

        return $plaintext;
    }

    /*
     * Unsigned right shift function, since PHP has neither >>> operator nor unsigned ints
     *
     * @param a  number to be shifted (32-bit integer)
     * @param b  number of bits to shift a to the right (0..31)
     * @return   a right-shifted and zero-filled by b bits
     */
    private static function urs($a, $b)
    {
        $a &= 0xFFFFFFFF;
        $b &= 0x1F;  // (bounds check)
        if ($a & 0x80000000 && $b > 0) {   // if left-most bit set
            $a = ($a >> 1) & 0x7FFFFFFF;   //   right-shift one bit & clear left-most bit
            $a = $a >> ($b - 1);           //   remaining right-shifts
        } else {                       // otherwise
            $a = ($a >> $b);               //   use normal right-shift
        }

        return $a;
    }
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
?>