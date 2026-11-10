<?php
class TotpAuthenticator {
    public function generateSecret() {
        return $this->base32Encode(random_bytes(20));
    }

    public function encryptSecret($secret) {
        $key = $this->getEncryptionKey();
        $iv = random_bytes(12);
        $tag = '';
        $ciphertext = openssl_encrypt($secret, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($ciphertext === false) {
            throw new RuntimeException('2FA gizli anahtarı şifrelenemedi.');
        }

        return base64_encode($iv . $tag . $ciphertext);
    }

    public function decryptSecret($encryptedSecret) {
        $encrypted = base64_decode((string) $encryptedSecret, true);
        if (!is_string($encrypted) || strlen($encrypted) < 29) {
            throw new RuntimeException('Kayıtlı 2FA gizli anahtarı geçersiz.');
        }

        $secret = openssl_decrypt(
            substr($encrypted, 28),
            'aes-256-gcm',
            $this->getEncryptionKey(),
            OPENSSL_RAW_DATA,
            substr($encrypted, 0, 12),
            substr($encrypted, 12, 16)
        );
        if (!is_string($secret)) {
            throw new RuntimeException('2FA gizli anahtarı çözümlenemedi; şifreleme anahtarını doğrulayın.');
        }

        return $secret;
    }

    public function matchingStep($secret, $code, $timestamp = null, $window = 1) {
        $code = preg_replace('/\s+/', '', (string) $code);
        if (!preg_match('/^\d{6}$/', $code)) {
            return null;
        }

        $currentStep = (int) floor(($timestamp ?? time()) / 30);
        for ($offset = -$window; $offset <= $window; $offset++) {
            $step = $currentStep + $offset;
            if ($step >= 0 && hash_equals($this->codeForStep($secret, $step), $code)) {
                return $step;
            }
        }

        return null;
    }

    public function codeForStep($secret, $step) {
        $key = $this->base32Decode($secret);
        $counter = pack('N*', 0) . pack('N*', (int) $step);
        $hash = hash_hmac('sha1', $counter, $key, true);
        $offset = ord($hash[strlen($hash) - 1]) & 0x0f;
        $binary = ((ord($hash[$offset]) & 0x7f) << 24)
            | ((ord($hash[$offset + 1]) & 0xff) << 16)
            | ((ord($hash[$offset + 2]) & 0xff) << 8)
            | (ord($hash[$offset + 3]) & 0xff);

        return str_pad((string) ($binary % 1000000), 6, '0', STR_PAD_LEFT);
    }

    public function recoveryCodes($count = 10) {
        $codes = [];
        for ($index = 0; $index < $count; $index++) {
            $codes[] = strtoupper(bin2hex(random_bytes(5)));
        }
        return $codes;
    }

    public function provisioningUri($secret, $email, $issuer = 'WebStats') {
        $label = rawurlencode($issuer . ':' . $email);
        return 'otpauth://totp/' . $label . '?' . http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
            'algorithm' => 'SHA1',
            'digits' => 6,
            'period' => 30
        ], '', '&', PHP_QUERY_RFC3986);
    }

    private function base32Encode($value) {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $binary = '';
        foreach (str_split($value) as $character) {
            $binary .= str_pad(decbin(ord($character)), 8, '0', STR_PAD_LEFT);
        }

        $encoded = '';
        foreach (str_split($binary, 5) as $chunk) {
            if (strlen($chunk) < 5) {
                $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            }
            $encoded .= $alphabet[bindec($chunk)];
        }

        return $encoded;
    }

    private function base32Decode($value) {
        $value = strtoupper(preg_replace('/[\s=-]/', '', (string) $value));
        if ($value === '' || !preg_match('/^[A-Z2-7]+$/', $value)) {
            throw new InvalidArgumentException('2FA gizli anahtarı geçersiz.');
        }

        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $binary = '';
        foreach (str_split($value) as $character) {
            $position = strpos($alphabet, $character);
            if ($position === false) {
                throw new InvalidArgumentException('2FA gizli anahtarı geçersiz.');
            }
            $binary .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
        }

        $decoded = '';
        foreach (str_split($binary, 8) as $byte) {
            if (strlen($byte) === 8) {
                $decoded .= chr(bindec($byte));
            }
        }

        return $decoded;
    }

    private function getEncryptionKey() {
        $path = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . '.web-analytics-2fa.key';
        if (is_file($path)) {
            $key = file_get_contents($path);
            if ($key === false || strlen($key) !== 32) {
                throw new RuntimeException('2FA anahtarı okunamadı veya geçersiz.');
            }
            return $key;
        }

        $key = random_bytes(32);
        $handle = @fopen($path, 'x');
        if ($handle === false) {
            if (is_file($path)) {
                return $this->getEncryptionKey();
            }
            throw new RuntimeException('2FA şifreleme anahtarı oluşturulamadı. Uygulama üst klasörünün yazma izinlerini kontrol edin.');
        }

        try {
            if (fwrite($handle, $key) !== strlen($key)) {
                throw new RuntimeException('2FA şifreleme anahtarı diske yazılamadı.');
            }
        } finally {
            fclose($handle);
        }
        @chmod($path, 0600);

        return $key;
    }
}
