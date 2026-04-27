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
     * @param int $nBits 128, 192, or 256
     */
    public function encrypt(string $plaintext, string $password, int $nBits = 256): string
    {
        if (! in_array($nBits, [128, 192, 256], true)) {
            return '';
        }

        $nBytes = $nBits / 8;
        $key    = $this->deriveKey($password, $nBytes);

        // Build 8-byte nonce: [0-1]=ms, [2-3]=rnd, [4-7]=sec
        $nonce    = (int) (microtime(true) * 1000);
        $nonceMs  = $nonce % 1000;
        $nonceSec = (int) ($nonce / 1000);
        $nonceRnd = random_int(0, 0xffff);

        $counterBlock = array_fill(0, 16, 0);
        for ($i = 0; $i < 2; $i++) {
            $counterBlock[$i]   = ($nonceMs >> ($i * 8)) & 0xff;
        }
        for ($i = 0; $i < 2; $i++) {
            $counterBlock[$i + 2] = ($nonceRnd >> ($i * 8)) & 0xff;
        }
        for ($i = 0; $i < 4; $i++) {
            $counterBlock[$i + 4] = ($nonceSec >> ($i * 8)) & 0xff;
        }

        // Nonce as raw string (first 8 bytes of counter block)
        $ctrTxt = '';
        for ($i = 0; $i < 8; $i++) {
            $ctrTxt .= chr($counterBlock[$i]);
        }

        $keyBytes    = array_values(unpack('C*', substr($key, 0, $nBytes)));
        $keySchedule = $this->keyExpansion($keyBytes);
        $cipherBytes = '';
        $blockCount  = (int) ceil(strlen($plaintext) / self::BLOCK_SIZE);

        for ($b = 0; $b < $blockCount; $b++) {
            // Set block counter in last 8 bytes of counter block
            for ($c = 0; $c < 4; $c++) {
                $counterBlock[15 - $c]     = ($b >> ($c * 8)) & 0xff;
                $counterBlock[15 - $c - 4] = 0; // upper 32 bits (we won't overflow)
            }

            $ctrKey   = $this->aesEncryptBlock($counterBlock, $keySchedule);
            $blockLen = ($b < $blockCount - 1)
                ? self::BLOCK_SIZE
                : ((strlen($plaintext) - 1) % self::BLOCK_SIZE + 1);

            for ($i = 0; $i < $blockLen; $i++) {
                $cipherBytes .= chr($ctrKey[$i] ^ ord($plaintext[$b * self::BLOCK_SIZE + $i]));
            }
        }

        return base64_encode($ctrTxt . $cipherBytes);
    }

    /**
     * Decrypt AES-CTR ciphertext produced by legacy Encryption::encrypt.
     */
    public function decrypt(string $ciphertext, string $password, int $nBits = 256): string
    {
        if (! in_array($nBits, [128, 192, 256], true)) {
            return '';
        }

        $raw    = base64_decode($ciphertext);
        $nBytes = $nBits / 8;
        $key    = $this->deriveKey($password, $nBytes);

        // Recover nonce from first 8 bytes
        $counterBlock = array_fill(0, 16, 0);
        for ($i = 0; $i < 8; $i++) {
            $counterBlock[$i] = ord($raw[$i]);
        }

        $keyBytes    = array_values(unpack('C*', substr($key, 0, $nBytes)));
        $keySchedule = $this->keyExpansion($keyBytes);
        $ctData      = substr($raw, 8);
        $nBlocks     = (int) ceil(strlen($ctData) / self::BLOCK_SIZE);
        $plaintext   = '';

        for ($b = 0; $b < $nBlocks; $b++) {
            for ($c = 0; $c < 4; $c++) {
                $counterBlock[15 - $c]     = ($b >> ($c * 8)) & 0xff;
                $counterBlock[15 - $c - 4] = 0;
            }

            $ctrKey = $this->aesEncryptBlock($counterBlock, $keySchedule);
            $block  = substr($ctData, $b * self::BLOCK_SIZE, self::BLOCK_SIZE);

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
            $pwBytes[$i] = ord($password[$i]) & 0xff;
        }

        // AES block is always 16 bytes; use the first 16 bytes as both key and plaintext
        $first16  = array_slice($pwBytes, 0, 16);
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
        $w  = [];

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
                $b[$i] = ($s[$i][$c] & 0x80) ? ($s[$i][$c] << 1) ^ 0x011b : $s[$i][$c] << 1;
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
        $tmp  = $w[0];
        $w[0] = $w[1];
        $w[1] = $w[2];
        $w[2] = $w[3];
        $w[3] = $tmp;

        return $w;
    }

    private const SBOX = [
        0x63, 0x7c, 0x77, 0x7b, 0xf2, 0x6b, 0x6f, 0xc5, 0x30, 0x01, 0x67, 0x2b, 0xfe, 0xd7, 0xab, 0x76,
        0xca, 0x82, 0xc9, 0x7d, 0xfa, 0x59, 0x47, 0xf0, 0xad, 0xd4, 0xa2, 0xaf, 0x9c, 0xa4, 0x72, 0xc0,
        0xb7, 0xfd, 0x93, 0x26, 0x36, 0x3f, 0xf7, 0xcc, 0x34, 0xa5, 0xe5, 0xf1, 0x71, 0xd8, 0x31, 0x15,
        0x04, 0xc7, 0x23, 0xc3, 0x18, 0x96, 0x05, 0x9a, 0x07, 0x12, 0x80, 0xe2, 0xeb, 0x27, 0xb2, 0x75,
        0x09, 0x83, 0x2c, 0x1a, 0x1b, 0x6e, 0x5a, 0xa0, 0x52, 0x3b, 0xd6, 0xb3, 0x29, 0xe3, 0x2f, 0x84,
        0x53, 0xd1, 0x00, 0xed, 0x20, 0xfc, 0xb1, 0x5b, 0x6a, 0xcb, 0xbe, 0x39, 0x4a, 0x4c, 0x58, 0xcf,
        0xd0, 0xef, 0xaa, 0xfb, 0x43, 0x4d, 0x33, 0x85, 0x45, 0xf9, 0x02, 0x7f, 0x50, 0x3c, 0x9f, 0xa8,
        0x51, 0xa3, 0x40, 0x8f, 0x92, 0x9d, 0x38, 0xf5, 0xbc, 0xb6, 0xda, 0x21, 0x10, 0xff, 0xf3, 0xd2,
        0xcd, 0x0c, 0x13, 0xec, 0x5f, 0x97, 0x44, 0x17, 0xc4, 0xa7, 0x7e, 0x3d, 0x64, 0x5d, 0x19, 0x73,
        0x60, 0x81, 0x4f, 0xdc, 0x22, 0x2a, 0x90, 0x88, 0x46, 0xee, 0xb8, 0x14, 0xde, 0x5e, 0x0b, 0xdb,
        0xe0, 0x32, 0x3a, 0x0a, 0x49, 0x06, 0x24, 0x5c, 0xc2, 0xd3, 0xac, 0x62, 0x91, 0x95, 0xe4, 0x79,
        0xe7, 0xc8, 0x37, 0x6d, 0x8d, 0xd5, 0x4e, 0xa9, 0x6c, 0x56, 0xf4, 0xea, 0x65, 0x7a, 0xae, 0x08,
        0xba, 0x78, 0x25, 0x2e, 0x1c, 0xa6, 0xb4, 0xc6, 0xe8, 0xdd, 0x74, 0x1f, 0x4b, 0xbd, 0x8b, 0x8a,
        0x70, 0x3e, 0xb5, 0x66, 0x48, 0x03, 0xf6, 0x0e, 0x61, 0x35, 0x57, 0xb9, 0x86, 0xc1, 0x1d, 0x9e,
        0xe1, 0xf8, 0x98, 0x11, 0x69, 0xd9, 0x8e, 0x94, 0x9b, 0x1e, 0x87, 0xe9, 0xce, 0x55, 0x28, 0xdf,
        0x8c, 0xa1, 0x89, 0x0d, 0xbf, 0xe6, 0x42, 0x68, 0x41, 0x99, 0x2d, 0x0f, 0xb0, 0x54, 0xbb, 0x16,
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
        [0x1b, 0x00, 0x00, 0x00],
        [0x36, 0x00, 0x00, 0x00],
    ];
}
