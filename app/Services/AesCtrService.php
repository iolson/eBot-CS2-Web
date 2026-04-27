<?php

namespace App\Services;

/**
 * AES-CTR (Counter Mode) encryption service.
 *
 * Must be byte-for-byte compatible with the JavaScript Aes.Ctr library and
 * the PHP Encryption class in legacy/lib/Encryption.class.php, which is used
 * by eBot Node.js to decrypt match commands.
 *
 * Algorithm:
 *  1. Derive key: AES-encrypt first $nBits/8 bytes of password (zero-padded) using
 *     the same bytes as the key — this gives a hardened 128/256-bit key.
 *  2. Build an 8-byte nonce counter block from [msec(2), rnd(2), sec(4)].
 *  3. XOR each block of plaintext with AES-encrypted counter.
 *  4. Prepend the 8-byte nonce to the ciphertext and base64-encode.
 */
class AesCtrService
{
    private const BLOCK_SIZE = 16; // AES block = 128 bits

    /**
     * Encrypt plaintext with AES-CTR using the legacy Encryption::encrypt algorithm.
     *
     * @param  int  $nBits  128, 192, or 256
     */
    public function encrypt(string $plaintext, string $password, int $nBits = 256): string
    {
        if (! in_array($nBits, [128, 192, 256], true)) {
            return '';
        }

        $nBytes = $nBits / 8;
        $key = $this->deriveKey($password, $nBytes);

        // Build 8-byte nonce: [0-1]=ms, [2-3]=rnd, [4-7]=sec
        $nonce = (int) (microtime(true) * 1000);
        $nonceMs = $nonce % 1000;
        $nonceSec = (int) ($nonce / 1000);
        $nonceRnd = random_int(0, 0xFFFF);

        $counterBlock = array_fill(0, 16, 0);
        for ($i = 0; $i < 2; $i++) {
            $counterBlock[$i] = ($nonceMs >> ($i * 8)) & 0xFF;
        }
        for ($i = 0; $i < 2; $i++) {
            $counterBlock[$i + 2] = ($nonceRnd >> ($i * 8)) & 0xFF;
        }
        for ($i = 0; $i < 4; $i++) {
            $counterBlock[$i + 4] = ($nonceSec >> ($i * 8)) & 0xFF;
        }

        // Nonce as raw string (first 8 bytes of counter block)
        $ctrTxt = '';
        for ($i = 0; $i < 8; $i++) {
            $ctrTxt .= chr($counterBlock[$i]);
        }

        $keyBytes = array_values(unpack('C*', substr($key, 0, $nBytes)));
        $keySchedule = $this->keyExpansion($keyBytes);
        $cipherBytes = '';
        $blockCount = (int) ceil(strlen($plaintext) / self::BLOCK_SIZE);

        for ($b = 0; $b < $blockCount; $b++) {
            // Set block counter in last 8 bytes of counter block
            for ($c = 0; $c < 4; $c++) {
                $counterBlock[15 - $c] = ($b >> ($c * 8)) & 0xFF;
                $counterBlock[15 - $c - 4] = 0; // upper 32 bits (we won't overflow)
            }

            $ctrKey = $this->aesEncryptBlock($counterBlock, $keySchedule);
            $blockLen = ($b < $blockCount - 1)
                ? self::BLOCK_SIZE
                : ((strlen($plaintext) - 1) % self::BLOCK_SIZE + 1);

            for ($i = 0; $i < $blockLen; $i++) {
                $cipherBytes .= chr($ctrKey[$i] ^ ord($plaintext[$b * self::BLOCK_SIZE + $i]));
            }
        }

        return base64_encode($ctrTxt.$cipherBytes);
    }

    /**
     * Decrypt AES-CTR ciphertext produced by legacy Encryption::encrypt.
     */
    public function decrypt(string $ciphertext, string $password, int $nBits = 256): string
    {
        if (! in_array($nBits, [128, 192, 256], true)) {
            return '';
        }

        $raw = base64_decode($ciphertext);
        $nBytes = $nBits / 8;
        $key = $this->deriveKey($password, $nBytes);

        // Recover nonce from first 8 bytes
        $counterBlock = array_fill(0, 16, 0);
        for ($i = 0; $i < 8; $i++) {
            $counterBlock[$i] = ord($raw[$i]);
        }

        $keyBytes = array_values(unpack('C*', substr($key, 0, $nBytes)));
        $keySchedule = $this->keyExpansion($keyBytes);
        $ctData = substr($raw, 8);
        $nBlocks = (int) ceil(strlen($ctData) / self::BLOCK_SIZE);
        $plaintext = '';

        for ($b = 0; $b < $nBlocks; $b++) {
            for ($c = 0; $c < 4; $c++) {
                $counterBlock[15 - $c] = ($b >> ($c * 8)) & 0xFF;
                $counterBlock[15 - $c - 4] = 0;
            }

            $ctrKey = $this->aesEncryptBlock($counterBlock, $keySchedule);
            $block = substr($ctData, $b * self::BLOCK_SIZE, self::BLOCK_SIZE);

            for ($i = 0; $i < strlen($block); $i++) {
                $plaintext .= chr($ctrKey[$i] ^ ord($block[$i]));
            }
        }

        return $plaintext;
    }

    /**
     * Derive AES key using the legacy algorithm:
     *   1. Pad/truncate password to $nBytes
     *   2. Encrypt the first 16 bytes using themselves as the AES-128 key
     *   3. Expand the 16-byte output to $nBytes by repeating the first ($nBytes - 16) bytes
     *
     * This mirrors Encryption.class.php: Aes::cipher($pwBytes, Aes::keyExpansion($pwBytes))
     * where Aes::cipher always operates on a 16-byte block (takes only $input[0..15]).
     */
    private function deriveKey(string $password, int $nBytes): string
    {
        // Build $nBytes-length zero-padded password array
        $pwBytes = array_fill(0, $nBytes, 0);
        for ($i = 0; $i < $nBytes && $i < strlen($password); $i++) {
            $pwBytes[$i] = ord($password[$i]) & 0xFF;
        }

        // AES block is always 16 bytes; use the first 16 bytes as both key and plaintext
        $first16 = array_slice($pwBytes, 0, 16);
        $schedule = $this->keyExpansion($first16);
        $keyWords = $this->aesEncryptBlock($first16, $schedule);

        // Expand to $nBytes by appending the first ($nBytes - 16) bytes
        $extra = $nBytes - 16;
        if ($extra > 0) {
            $keyWords = array_merge($keyWords, array_slice($keyWords, 0, $extra));
        }

        return implode('', array_map('chr', $keyWords));
    }

    // -------------------------------------------------------------------------
    // Minimal AES primitives (ported from legacy Encryption.class.php / Aes)
    // -------------------------------------------------------------------------

    private function aesEncryptBlock(array $input, array $w): array
    {
        $Nb = 4;
        $Nr = count($w) / $Nb - 1;

        $state = [];
        for ($i = 0; $i < 4 * $Nb; $i++) {
            $state[$i % 4][(int) floor($i / 4)] = $input[$i] ?? 0;
        }

        $state = $this->addRoundKey($state, $w, 0, $Nb);

        for ($round = 1; $round < $Nr; $round++) {
            $state = $this->subBytes($state, $Nb);
            $state = $this->shiftRows($state, $Nb);
            $state = $this->mixColumns($state, $Nb);
            $state = $this->addRoundKey($state, $w, $round, $Nb);
        }

        $state = $this->subBytes($state, $Nb);
        $state = $this->shiftRows($state, $Nb);
        $state = $this->addRoundKey($state, $w, $Nr, $Nb);

        $output = [];
        for ($i = 0; $i < 4 * $Nb; $i++) {
            $output[$i] = $state[$i % 4][(int) floor($i / 4)];
        }

        return $output;
    }

    private function keyExpansion(array $key): array
    {
        $Nb = 4;
        $Nk = count($key) / 4;
        $Nr = $Nk + 6;
        $w = [];

        for ($i = 0; $i < $Nk; $i++) {
            $w[$i] = [$key[4 * $i], $key[4 * $i + 1], $key[4 * $i + 2], $key[4 * $i + 3]];
        }

        for ($i = $Nk; $i < $Nb * ($Nr + 1); $i++) {
            $temp = $w[$i - 1];
            if ($i % $Nk === 0) {
                $temp = $this->subWord($this->rotWord($temp));
                for ($t = 0; $t < 4; $t++) {
                    $temp[$t] ^= self::RCON[(int) ($i / $Nk)][$t];
                }
            } elseif ($Nk > 6 && $i % $Nk === 4) {
                $temp = $this->subWord($temp);
            }
            $w[$i] = [];
            for ($t = 0; $t < 4; $t++) {
                $w[$i][$t] = $w[$i - $Nk][$t] ^ $temp[$t];
            }
        }

        return $w;
    }

    private function addRoundKey(array $state, array $w, int $rnd, int $Nb): array
    {
        for ($r = 0; $r < 4; $r++) {
            for ($c = 0; $c < $Nb; $c++) {
                $state[$r][$c] ^= $w[$rnd * 4 + $c][$r];
            }
        }

        return $state;
    }

    private function subBytes(array $s, int $Nb): array
    {
        for ($r = 0; $r < 4; $r++) {
            for ($c = 0; $c < $Nb; $c++) {
                $s[$r][$c] = self::SBOX[$s[$r][$c]];
            }
        }

        return $s;
    }

    private function shiftRows(array $s, int $Nb): array
    {
        $t = [];
        for ($r = 1; $r < 4; $r++) {
            for ($c = 0; $c < 4; $c++) {
                $t[$c] = $s[$r][($c + $r) % $Nb];
            }
            for ($c = 0; $c < 4; $c++) {
                $s[$r][$c] = $t[$c];
            }
        }

        return $s;
    }

    private function mixColumns(array $s, int $Nb): array
    {
        for ($c = 0; $c < 4; $c++) {
            $a = [];
            $b = [];
            for ($i = 0; $i < 4; $i++) {
                $a[$i] = $s[$i][$c];
                $b[$i] = ($s[$i][$c] & 0x80) ? ($s[$i][$c] << 1) ^ 0x011B : $s[$i][$c] << 1;
            }
            $s[0][$c] = $b[0] ^ $a[1] ^ $b[1] ^ $a[2] ^ $a[3];
            $s[1][$c] = $a[0] ^ $b[1] ^ $a[2] ^ $b[2] ^ $a[3];
            $s[2][$c] = $a[0] ^ $a[1] ^ $b[2] ^ $a[3] ^ $b[3];
            $s[3][$c] = $a[0] ^ $b[0] ^ $a[1] ^ $a[2] ^ $b[3];
        }

        return $s;
    }

    private function subWord(array $w): array
    {
        for ($i = 0; $i < 4; $i++) {
            $w[$i] = self::SBOX[$w[$i]];
        }

        return $w;
    }

    private function rotWord(array $w): array
    {
        $tmp = $w[0];
        $w[0] = $w[1];
        $w[1] = $w[2];
        $w[2] = $w[3];
        $w[3] = $tmp;

        return $w;
    }

    private const SBOX = [
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
        0x8C, 0xA1, 0x89, 0x0D, 0xBF, 0xE6, 0x42, 0x68, 0x41, 0x99, 0x2D, 0x0F, 0xB0, 0x54, 0xBB, 0x16,
    ];

    private const RCON = [
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
        [0x36, 0x00, 0x00, 0x00],
    ];
}
