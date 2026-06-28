<?php
/**
 * Minimal, dependency-free mailer.
 *
 * send_mail() uses authenticated SMTP when SMTP_HOST is configured in
 * config.php, and otherwise falls back to PHP's mail(). This gives reliable
 * delivery on hosts where the local mail() transport is unreliable.
 */
declare(strict_types=1);

/**
 * Send a plain-text email.
 *
 * @param array $opts from, from_name, reply_to
 */
function send_mail(string $to, string $subject, string $body, array $opts = []): bool
{
    $from     = $opts['from']      ?? (defined('MAIL_FROM') && MAIL_FROM ? MAIL_FROM : 'enquiry@thewbillboard.com');
    $fromName = $opts['from_name'] ?? (defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'The Walking Billboard');
    $replyTo  = $opts['reply_to']  ?? $from;

    if (defined('SMTP_HOST') && SMTP_HOST !== '') {
        $ok = smtp_send(
            (string) SMTP_HOST,
            defined('SMTP_PORT') ? (int) SMTP_PORT : 465,
            defined('SMTP_SECURE') ? (string) SMTP_SECURE : 'ssl',
            defined('SMTP_USER') ? (string) SMTP_USER : '',
            defined('SMTP_PASS') ? (string) SMTP_PASS : '',
            $from, $fromName, $to, $subject, $body, $replyTo
        );
        if ($ok) {
            return true;
        }
        // SMTP failed — fall through to mail() as a last resort.
    }

    $headers  = 'From: ' . $fromName . ' <' . $from . ">\r\n"
              . 'Reply-To: ' . $replyTo . "\r\n"
              . "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8";

    return @mail($to, mail_encode_subject($subject), $body, $headers, '-f' . $from);
}

/** RFC 2047 encode a subject only when it contains non-ASCII bytes. */
function mail_encode_subject(string $subject): string
{
    return preg_match('/[\x80-\xFF]/', $subject)
        ? '=?UTF-8?B?' . base64_encode($subject) . '?='
        : $subject;
}

/**
 * Low-level SMTP send over a socket. Supports implicit SSL (port 465) and
 * STARTTLS (port 587), with AUTH LOGIN. Returns true only on a 250 after DATA.
 */
function smtp_send(
    string $host, int $port, string $secure,
    string $user, string $pass,
    string $from, string $fromName, string $to, string $subject, string $body, string $replyTo,
    ?array &$log = null
): bool {
    $transport = ($secure === 'ssl') ? "ssl://{$host}" : $host;
    $errno = 0; $errstr = '';
    // Shared-hosting mail servers usually present a cert for the server's own
    // hostname, not mail.<domain>, so strict verification fails with errno 0.
    // Relax verification — we're connecting to our own trusted mail host.
    $ctx = stream_context_create(['ssl' => [
        'verify_peer'       => false,
        'verify_peer_name'  => false,
        'allow_self_signed' => true,
        'SNI_enabled'       => true,
        'peer_name'         => $host,
    ]]);
    if ($log !== null) { $log[] = "Connecting to {$transport}:{$port} …"; }
    error_clear_last();
    $fp = @stream_socket_client("{$transport}:{$port}", $errno, $errstr, 20, STREAM_CLIENT_CONNECT, $ctx);
    if (!$fp) {
        if ($log !== null) {
            $why = $errstr;
            if ($why === '' && ($e = error_get_last()) && !empty($e['message'])) {
                $why = $e['message'];
            }
            $log[] = "CONNECT FAILED: [{$errno}] " . ($why !== '' ? $why : '(no detail — likely TLS handshake or blocked port)');
        }
        return false;
    }
    stream_set_timeout($fp, 20);

    $read = static function () use ($fp): string {
        $data = '';
        while (($line = fgets($fp, 515)) !== false) {
            $data .= $line;
            // Final line of a (possibly multiline) reply has a space at index 3.
            if (strlen($line) < 4 || $line[3] === ' ') {
                break;
            }
        }
        return $data;
    };
    $ok = static fn (string $resp, $codes): bool => in_array((int) substr($resp, 0, 3), (array) $codes, true);
    $lastCode = 0;
    $cmd = static function (string $c) use ($fp, $read, &$log, &$lastCode): string {
        fwrite($fp, $c . "\r\n");
        if ($log !== null) {
            $shown = ($lastCode === 334) ? '****** (credentials)'
                   : (strlen($c) > 120 ? '<message, ' . strlen($c) . ' bytes>' : $c);
            $log[] = 'C: ' . $shown;
        }
        $resp = $read();
        $lastCode = (int) substr($resp, 0, 3);
        if ($log !== null) { $log[] = 'S: ' . rtrim($resp); }
        return $resp;
    };

    $fail = static function () use ($fp): bool {
        @fwrite($fp, "QUIT\r\n");
        @fclose($fp);
        return false;
    };

    $ehloName = $_SERVER['SERVER_NAME'] ?? 'localhost';

    $greeting = $read();
    if ($log !== null) { $log[] = 'S: ' . rtrim($greeting); }
    if (!$ok($greeting, 220))                  { return $fail(); } // greeting
    if (!$ok($cmd('EHLO ' . $ehloName), 250))  { return $fail(); }

    if ($secure === 'tls') {
        if (!$ok($cmd('STARTTLS'), 220))       { return $fail(); }
        if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT
                | STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
            return $fail();
        }
        if (!$ok($cmd('EHLO ' . $ehloName), 250)) { return $fail(); }
    }

    if ($user !== '') {
        if (!$ok($cmd('AUTH LOGIN'), 334))       { return $fail(); }
        if (!$ok($cmd(base64_encode($user)), 334)) { return $fail(); }
        if (!$ok($cmd(base64_encode($pass)), 235)) { return $fail(); }
    }

    if (!$ok($cmd('MAIL FROM:<' . $from . '>'), 250))   { return $fail(); }
    if (!$ok($cmd('RCPT TO:<' . $to . '>'), [250, 251])) { return $fail(); }
    if (!$ok($cmd('DATA'), 354))                         { return $fail(); }

    // Normalise line endings, then dot-stuff lines beginning with '.'
    $body = str_replace(["\r\n", "\r"], "\n", $body);
    $body = (string) preg_replace('/^\./m', '..', $body);
    $body = str_replace("\n", "\r\n", $body);

    $domain = substr(strrchr($from, '@') ?: '@localhost', 1);
    $headers = [
        'Date: ' . date('r'),
        'From: ' . $fromName . ' <' . $from . '>',
        'To: <' . $to . '>',
        'Reply-To: ' . $replyTo,
        'Subject: ' . mail_encode_subject($subject),
        'Message-ID: <' . bin2hex(random_bytes(8)) . '@' . $domain . '>',
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
    ];
    $message = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.";

    if (!$ok($cmd($message), 250)) { return $fail(); }

    @fwrite($fp, "QUIT\r\n");
    @fclose($fp);
    return true;
}
