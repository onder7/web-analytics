<?php
class SmtpMailer {
    public function send(array $settings, $subject, $body) {
        $host = (string) ($settings['smtp_host'] ?? '');
        $port = (int) ($settings['smtp_port'] ?? 0);
        $encryption = (string) ($settings['smtp_encryption'] ?? '');
        $fromEmail = (string) ($settings['from_email'] ?? '');
        $recipient = (string) ($settings['recipient_email'] ?? '');

        if ($host === '' || $port < 1 || $port > 65535 ||
            !in_array($encryption, ['tls', 'ssl', 'none'], true) ||
            (!filter_var($host, FILTER_VALIDATE_IP) &&
                !filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) ||
            !filter_var($fromEmail, FILTER_VALIDATE_EMAIL) ||
            !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('E-posta gönderimi için SMTP ayarları geçersiz veya eksik.');
        }

        $transport = $encryption === 'ssl' ? 'ssl://' : 'tcp://';
        $connectionHost = filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? '[' . $host . ']' : $host;
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'peer_name' => $host
            ]
        ]);
        $socket = @stream_socket_client(
            $transport . $connectionHost . ':' . $port,
            $errorCode,
            $errorMessage,
            15,
            STREAM_CLIENT_CONNECT,
            $context
        );
        if (!is_resource($socket)) {
            throw new RuntimeException('SMTP sunucusuna bağlanılamadı: ' . $errorMessage, (int) $errorCode);
        }

        stream_set_timeout($socket, 15);
        try {
            $this->expect($socket, [220]);
            $this->command($socket, 'EHLO ' . $this->clientName(), [250]);

            if ($encryption === 'tls') {
                $this->command($socket, 'STARTTLS', [220]);
                if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new RuntimeException('SMTP TLS bağlantısı kurulamadı.');
                }
                $this->command($socket, 'EHLO ' . $this->clientName(), [250]);
            }

            $username = (string) ($settings['smtp_username'] ?? '');
            $password = (string) ($settings['smtp_password'] ?? '');
            if ($username !== '') {
                $this->authenticate($socket, $username, $password);
            }

            $this->command($socket, 'MAIL FROM:<' . $fromEmail . '>', [250]);
            $this->command($socket, 'RCPT TO:<' . $recipient . '>', [250, 251]);
            $this->command($socket, 'DATA', [354]);
            $this->write($socket, $this->message($settings, $fromEmail, $recipient, $subject, $body));
            $this->write($socket, "\r\n.\r\n");
            $this->expect($socket, [250]);
            $this->command($socket, 'QUIT', [221]);
        } finally {
            fclose($socket);
        }
    }

    private function authenticate($socket, $username, $password) {
        $response = $this->command($socket, 'AUTH PLAIN ' . base64_encode("\0" . $username . "\0" . $password), [235, 334], false);
        if ($response[0] === 235) {
            return;
        }
        if ($response[0] === 334) {
            $this->command($socket, base64_encode("\0" . $username . "\0" . $password), [235]);
            return;
        }

        $this->command($socket, 'AUTH LOGIN', [334]);
        $this->command($socket, base64_encode($username), [334]);
        $this->command($socket, base64_encode($password), [235]);
    }

    private function message(array $settings, $fromEmail, $recipient, $subject, $body) {
        $fromName = trim((string) ($settings['from_name'] ?? ''));
        $from = $fromName === ''
            ? $fromEmail
            : '=?UTF-8?B?' . base64_encode($fromName) . '?= <' . $fromEmail . '>';
        $subject = '=?UTF-8?B?' . base64_encode((string) $subject) . '?=';
        $body = str_replace(["\r\n", "\r"], "\n", (string) $body);
        $body = str_replace("\n", "\r\n", $body);
        $headers = [
            'Date: ' . date(DATE_RFC2822),
            'From: ' . $from,
            'To: <' . $recipient . '>',
            'Subject: ' . $subject,
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: base64'
        ];

        return implode("\r\n", $headers) . "\r\n\r\n" . chunk_split(base64_encode($body), 76, "\r\n");
    }

    private function command($socket, $command, array $expectedCodes, $throwOnUnexpected = true) {
        $this->write($socket, $command . "\r\n");
        $response = $this->readResponse($socket);
        if ($throwOnUnexpected && !in_array($response[0], $expectedCodes, true)) {
            throw new RuntimeException('SMTP sunucusu komutu reddetti: ' . $response[1]);
        }
        return $response;
    }

    private function expect($socket, array $expectedCodes) {
        $response = $this->readResponse($socket);
        if (!in_array($response[0], $expectedCodes, true)) {
            throw new RuntimeException('SMTP sunucusu beklenmeyen yanıt verdi: ' . $response[1]);
        }
        return $response;
    }

    private function readResponse($socket) {
        $lines = [];
        do {
            $line = fgets($socket, 2048);
            if ($line === false) {
                $metadata = stream_get_meta_data($socket);
                throw new RuntimeException(!empty($metadata['timed_out'])
                    ? 'SMTP sunucusu zaman aşımına uğradı.'
                    : 'SMTP sunucusundan yanıt okunamadı.');
            }
            $line = rtrim($line, "\r\n");
            $lines[] = $line;
        } while (strlen($line) >= 4 && $line[3] === '-');

        if (!preg_match('/^(\d{3})/', $lines[0], $matches)) {
            throw new RuntimeException('SMTP sunucusundan geçersiz yanıt alındı.');
        }

        return [(int) $matches[1], implode(' ', $lines)];
    }

    private function write($socket, $data) {
        $remaining = $data;
        while ($remaining !== '') {
            $written = fwrite($socket, $remaining);
            if ($written === false || $written === 0) {
                throw new RuntimeException('SMTP sunucusuna veri gönderilemedi.');
            }
            $remaining = substr($remaining, $written);
        }
    }

    private function clientName() {
        $host = gethostname();
        return is_string($host) && preg_match('/^[A-Za-z0-9.-]+$/', $host) ? $host : 'localhost';
    }
}
