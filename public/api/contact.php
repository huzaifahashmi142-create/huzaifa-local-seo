<?php
/**
 * Huzaifa Local SEO — first-party contact form handler.
 * Deploy alongside the static Astro build at /api/contact.php on PHP-capable hosting.
 * No external form services, no API keys. Recipient/config stays server-side only.
 */

// ---- Configuration (server-side only, never exposed to the frontend) ----
const RECIPIENT_EMAIL   = 'info@huzaifalocalseo.com';
const SITE_DOMAIN        = 'huzaifalocalseo.com';
const ALLOWED_ORIGINS    = ['https://huzaifalocalseo.com', 'https://www.huzaifalocalseo.com'];
const SENDER_EMAIL       = 'noreply@huzaifalocalseo.com'; // must be a real mailbox/alias on this domain
const MAX_FIELD_LENGTH   = 200;
const MAX_MESSAGE_LENGTH = 5000;
const MAX_BODY_BYTES     = 20000; // ~20KB request size cap
const RATE_LIMIT_WINDOW  = 60;    // seconds
const RATE_LIMIT_MAX     = 3;     // max submissions per IP per window
const RATE_LIMIT_DIR     = __DIR__ . '/.rl';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function respond(int $httpCode, bool $success, string $message, array $extra = []): void {
    http_response_code($httpCode);
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
    exit;
}

// ---- Method check ----
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, false, 'Method not allowed.');
}

// ---- Content-Type check ----
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') === false) {
    respond(415, false, 'Unsupported content type.');
}

// ---- Request size limit ----
$rawBody = file_get_contents('php://input', false, null, 0, MAX_BODY_BYTES + 1);
if ($rawBody === false || strlen($rawBody) > MAX_BODY_BYTES) {
    respond(413, false, 'Request too large.');
}

// ---- Origin / Referer check (defense in depth, not sole protection) ----
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$originHost = $origin ? parse_url($origin, PHP_URL_HOST) : null;
$refererHost = $referer ? parse_url($referer, PHP_URL_HOST) : null;
$hostOk = false;
foreach ([$originHost, $refererHost] as $h) {
    if ($h && (strcasecmp($h, SITE_DOMAIN) === 0 || strcasecmp($h, 'www.' . SITE_DOMAIN) === 0)) {
        $hostOk = true;
    }
}
if (!$hostOk && ($origin || $referer)) {
    respond(403, false, 'Request origin not allowed.');
}

// ---- Rate limiting (per IP, file-based) ----
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if (!is_dir(RATE_LIMIT_DIR)) {
    @mkdir(RATE_LIMIT_DIR, 0700, true);
}
$rlFile = RATE_LIMIT_DIR . '/' . preg_replace('/[^a-zA-Z0-9_.]/', '_', $ip) . '.json';
$now = time();
$hits = [];
if (is_file($rlFile)) {
    $decoded = json_decode((string) @file_get_contents($rlFile), true);
    if (is_array($decoded)) {
        $hits = array_filter($decoded, fn($t) => $t > $now - RATE_LIMIT_WINDOW);
    }
}
if (count($hits) >= RATE_LIMIT_MAX) {
    respond(429, false, 'Too many requests. Please wait a moment and try again.');
}
$hits[] = $now;
@file_put_contents($rlFile, json_encode(array_values($hits)));

// ---- Parse JSON body ----
$data = json_decode($rawBody, true);
if (!is_array($data)) {
    respond(400, false, 'Invalid request body.');
}

// ---- Honeypot (secondary layer only) ----
if (!empty($data['botcheck'])) {
    // Silently accept to not tip off bots, but do nothing further.
    respond(200, true, "Thank you! Your enquiry has been submitted. Our team will contact you using the details provided.");
}

function cleanField($value, int $maxLen): string {
    if (!is_string($value)) return '';
    $value = trim($value);
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $value); // strip control chars
    $value = str_replace(["\r", "\n"], ' ', $value); // prevent header injection in single-line fields
    if (function_exists('mb_substr')) {
        $value = mb_substr($value, 0, $maxLen);
    } else {
        $value = substr($value, 0, $maxLen);
    }
    return $value;
}

function cleanMessage($value, int $maxLen): string {
    if (!is_string($value)) return '';
    $value = trim($value);
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $value);
    if (function_exists('mb_substr')) {
        $value = mb_substr($value, 0, $maxLen);
    } else {
        $value = substr($value, 0, $maxLen);
    }
    return $value;
}

$name    = cleanField($data['name'] ?? '', MAX_FIELD_LENGTH);
$email   = cleanField($data['email'] ?? '', MAX_FIELD_LENGTH);
$phone   = cleanField($data['phone'] ?? '', 40);
$city    = cleanField($data['city'] ?? '', MAX_FIELD_LENGTH);
$service = cleanField($data['service'] ?? '', MAX_FIELD_LENGTH);
$business = cleanField($data['business'] ?? '', MAX_FIELD_LENGTH);
$message = cleanMessage($data['message'] ?? '', MAX_MESSAGE_LENGTH);
$sourcePage = cleanField($data['source_page'] ?? '', 300);

// ---- Server-side validation ----
$errors = [];
if ($name === '') $errors[] = 'name';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'email';
if ($city === '') $errors[] = 'city';
if ($service === '') $errors[] = 'service';
if ($message === '') $errors[] = 'message';

if (!empty($errors)) {
    respond(422, false, 'Please fill in all required fields correctly.', ['fields' => $errors]);
}

// Validate source page belongs to this site (defense against open abuse)
if ($sourcePage !== '' && stripos($sourcePage, SITE_DOMAIN) === false) {
    $sourcePage = 'https://' . SITE_DOMAIN . '/contact/';
}

// ---- Build email (escaped for HTML) ----
function esc(string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

$subject = 'New Website Enquiry | Huzaifa Local SEO | ' . $service;
// Subject must stay single-line; cleanField already stripped \r\n.

$timestamp = date('Y-m-d H:i:s T');

$plainBody = "NEW WEBSITE ENQUIRY\n\n"
    . "Name: {$name}\n"
    . "Email: {$email}\n"
    . "Phone: " . ($phone !== '' ? $phone : 'Not provided') . "\n"
    . "City: {$city}\n"
    . "Interested Service: {$service}\n"
    . ($business !== '' ? "Business: {$business}\n" : '')
    . "\nMessage:\n{$message}\n\n"
    . "Source Page:\n" . ($sourcePage !== '' ? $sourcePage : 'https://' . SITE_DOMAIN . '/contact/') . "\n\n"
    . "Submitted At:\n{$timestamp}\n\n"
    . "Website:\nhttps://" . SITE_DOMAIN . "/\n";

$htmlBody = '<div style="font-family:Arial,sans-serif;font-size:14px;color:#0b1324;line-height:1.6;">'
    . '<h2 style="margin:0 0 16px;">New Website Enquiry</h2>'
    . '<p><strong>Name:</strong> ' . esc($name) . '</p>'
    . '<p><strong>Email:</strong> ' . esc($email) . '</p>'
    . '<p><strong>Phone:</strong> ' . esc($phone !== '' ? $phone : 'Not provided') . '</p>'
    . '<p><strong>City:</strong> ' . esc($city) . '</p>'
    . '<p><strong>Interested Service:</strong> ' . esc($service) . '</p>'
    . ($business !== '' ? '<p><strong>Business:</strong> ' . esc($business) . '</p>' : '')
    . '<p><strong>Message:</strong><br>' . nl2br(esc($message)) . '</p>'
    . '<hr style="border:none;border-top:1px solid #e2e9f4;margin:20px 0;">'
    . '<p style="color:#64748b;font-size:12px;">Source Page: ' . esc($sourcePage) . '<br>'
    . 'Submitted At: ' . esc($timestamp) . '<br>'
    . 'Website: https://' . SITE_DOMAIN . '/</p>'
    . '</div>';

// ---- Headers (sanitized, no header injection) ----
$boundary = md5((string) microtime());
$headers = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';
$headers[] = 'From: Huzaifa Local SEO Website <' . SENDER_EMAIL . '>';
// Reply-To uses the validated customer email so replies go straight to the customer.
$safeReplyName = str_replace(['"', '\\'], '', $name);
$headers[] = 'Reply-To: "' . $safeReplyName . '" <' . $email . '>';
$headers[] = 'X-Mailer: PHP/' . phpversion();

$mimeBody = "--{$boundary}\r\n"
    . "Content-Type: text/plain; charset=UTF-8\r\n"
    . "Content-Transfer-Encoding: 8bit\r\n\r\n"
    . $plainBody . "\r\n"
    . "--{$boundary}\r\n"
    . "Content-Type: text/html; charset=UTF-8\r\n"
    . "Content-Transfer-Encoding: 8bit\r\n\r\n"
    . $htmlBody . "\r\n"
    . "--{$boundary}--";

// ---- Send ----
$sent = @mail(RECIPIENT_EMAIL, $subject, $mimeBody, implode("\r\n", $headers));

if (!$sent) {
    error_log('[contact-form] mail() failed for submission from ' . $ip);
    respond(502, false, "We couldn't submit your enquiry right now. Please try again.");
}

respond(200, true, 'Thank you! Your enquiry has been submitted. Our team will contact you using the details provided.');
