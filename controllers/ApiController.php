<?php
class ApiController {
    private $analyticsModel;
    private $websiteModel;
    private $geoIpLookup;

    public function __construct() {
        $this->analyticsModel = new Analytics();
        $this->websiteModel = new Website();
        $this->geoIpLookup = new GeoIpLookup();
    }

    public function track($action) {
        header('Content-Type: application/json; charset=utf-8');
        header('Vary: Origin');

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->respond(405, ['error' => 'Only POST requests are supported.']);
            return;
        }

        if (!in_array($action, ['pageview', 'event'], true)) {
            $this->respond(400, ['error' => 'Invalid tracking action.']);
            return;
        }

        $rawInput = file_get_contents('php://input');
        $data = [];
        if (is_string($rawInput) && $rawInput !== '') {
            $decoded = json_decode($rawInput, true);
            if (!is_array($decoded)) {
                $this->respond(400, ['error' => 'Invalid JSON payload.']);
                return;
            }
            $data = $decoded;
        } elseif (!empty($_POST)) {
            $data = $_POST;
        }

        $apiKey = $data['api_key'] ?? '';
        if (!is_string($apiKey) || strlen($apiKey) !== 64 || !ctype_xdigit($apiKey)) {
            $this->respond(401, ['error' => 'A valid API key is required.']);
            return;
        }

        $website = $this->websiteModel->findByApiKey($apiKey);
        if (!$website) {
            $this->respond(401, ['error' => 'Invalid API key.']);
            return;
        }

        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        if (!is_string($origin) || !$this->originMatchesWebsite($origin, $website['domain'])) {
            $this->respond(403, ['error' => 'This origin is not allowed for the website.']);
            return;
        }

        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type');

        if ($this->isBotUserAgent($_SERVER['HTTP_USER_AGENT'] ?? '')) {
            $this->respond(200, ['success' => true, 'ignored' => true]);
            return;
        }

        try {
            $this->enrichWithGeoData($data);
            if ($action === 'pageview') {
                $result = $this->trackPageView($website['id'], $data);
            } else {
                $result = $this->trackEvent($website['id'], $data);
            }

            if (!$result) {
                $this->respond(500, ['error' => 'Failed to track data.']);
                return;
            }
            $this->respond(200, ['success' => true]);
        } catch (Throwable $error) {
            AppLogger::error('tracking-api', $error->getMessage());
            try {
                (new NotificationService())->queueTrackingError((int) $website['id']);
            } catch (Throwable $notificationError) {
                AppLogger::error('tracking-notification', $notificationError->getMessage());
            }
            $this->respond(500, ['error' => 'An internal error occurred while tracking data.']);
        }
    }

    private function respond($status, array $payload) {
        Flight::response()->status($status);
        echo json_encode($payload, JSON_INVALID_UTF8_SUBSTITUTE);
    }

    private function originMatchesWebsite($origin, $domain) {
        if (!is_string($origin) || $origin === 'null') {
            return false;
        }

        $originParts = parse_url($origin);
        $domainUrl = preg_match('~^https?://~i', (string) $domain)
            ? (string) $domain
            : 'https://' . (string) $domain;
        $domainParts = parse_url($domainUrl);
        if (!is_array($originParts) || !is_array($domainParts) ||
            !in_array(strtolower($originParts['scheme'] ?? ''), ['http', 'https'], true) ||
            empty($originParts['host']) || empty($domainParts['host']) ||
            isset($originParts['user']) || isset($originParts['pass'])) {
            return false;
        }

        $originHost = strtolower(rtrim($originParts['host'], '.'));
        $domainHost = strtolower(rtrim($domainParts['host'], '.'));
        $hostsMatch = $originHost === $domainHost;
        if (!$hostsMatch) {
            $hostsMatch = preg_replace('/^www\\./', '', $originHost) === preg_replace('/^www\\./', '', $domainHost);
        }
        if (!$hostsMatch) {
            return false;
        }

        return !isset($domainParts['port']) || (int) ($originParts['port'] ?? 0) === (int) $domainParts['port'];
    }

    private function isBotUserAgent($userAgent) {
        if (!is_string($userAgent) || $userAgent === '') {
            return false;
        }

        return (bool) preg_match(
            '/(?:bot|crawler|spider|slurp|headless|phantomjs|selenium|playwright|puppeteer|lighthouse)/i',
            $userAgent
        );
    }

    private function enrichWithGeoData(&$data) {
        $ip = $this->getRealIpAddress();
        $data['ip_address'] = $ip;
        foreach ($this->geoIpLookup->cachedOrQueue($ip) as $key => $value) {
            $data[$key] = $value;
        }
    }

    private function getRealIpAddress() {
        $remoteIp = $_SERVER['REMOTE_ADDR'] ?? '';
        if (!filter_var($remoteIp, FILTER_VALIDATE_IP)) {
            return '127.0.0.1';
        }

        $trustedProxies = array_filter(array_map('trim', explode(',', getenv('TRUSTED_PROXIES') ?: '')));
        if (in_array($remoteIp, $trustedProxies, true)) {
            foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR'] as $header) {
                if (empty($_SERVER[$header])) {
                    continue;
                }
                foreach (explode(',', $_SERVER[$header]) as $candidate) {
                    $candidate = trim($candidate);
                    if (filter_var($candidate, FILTER_VALIDATE_IP)) {
                        return $candidate;
                    }
                }
            }
        }

        return $remoteIp;
    }

    private function trackPageView($websiteId, $data) {
        $visitorId = $data['visitor_id'] ?? '';
        if (!is_string($visitorId) || $visitorId === '' || strlen($visitorId) > 64) {
            throw new InvalidArgumentException('A valid visitor ID is required.');
        }

        $visitorDbId = $this->analyticsModel->getOrCreateVisitor($visitorId);
        return $this->analyticsModel->recordPageView($websiteId, $visitorDbId, $data);
    }

    private function trackEvent($websiteId, $data) {
        $visitorId = $data['visitor_id'] ?? '';
        $sessionId = $data['session_id'] ?? '';
        if (!is_string($visitorId) || $visitorId === '' || strlen($visitorId) > 64 ||
            !is_string($sessionId) || $sessionId === '' || strlen($sessionId) > 64) {
            throw new InvalidArgumentException('Valid visitor and session IDs are required.');
        }

        $visitorDbId = $this->analyticsModel->getOrCreateVisitor($visitorId);
        $db = Database::getConnection();
        $statement = $db->prepare(
            'INSERT INTO events (
                website_id, visitor_id, session_id, event_type, event_category,
                event_action, event_label, event_value, page_url, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
        );
        $result = $statement->execute([
            $websiteId,
            $visitorDbId,
            $sessionId,
            substr((string) ($data['event_type'] ?? 'custom'), 0, 50),
            isset($data['event_category']) ? substr((string) $data['event_category'], 0, 100) : null,
            isset($data['event_action']) ? substr((string) $data['event_action'], 0, 100) : null,
            isset($data['event_label']) ? substr((string) $data['event_label'], 0, 255) : null,
            isset($data['event_value']) && is_numeric($data['event_value']) ? $data['event_value'] : null,
            substr((string) ($data['page_url'] ?? ''), 0, 65535)
        ]);

        if ($result && ($data['event_category'] ?? '') === 'time-on-page' &&
            isset($data['event_value']) && is_numeric($data['event_value'])) {
            $this->analyticsModel->updateSessionDuration(
                $websiteId,
                $visitorDbId,
                $sessionId,
                $data['event_value']
            );
        }

        return $result;
    }
}
