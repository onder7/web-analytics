<?php
class InstallWizard {
    private const CORE_TABLES = ['users', 'websites', 'page_views'];

    public function status() {
        $configPath = $this->configurationPath();
        if (is_file($configPath)) {
            return ['installed' => true, 'error' => null];
        }

        try {
            $database = Database::getConnection();
            $placeholders = implode(',', array_fill(0, count(self::CORE_TABLES), '?'));
            $statement = $database->prepare(
                'SELECT COUNT(*) FROM information_schema.tables
                 WHERE table_schema = DATABASE() AND table_name IN (' . $placeholders . ')'
            );
            $statement->execute(self::CORE_TABLES);

            return [
                'installed' => (int) $statement->fetchColumn() === count(self::CORE_TABLES),
                'error' => null
            ];
        } catch (Throwable $error) {
            error_log('Installation status check failed: ' . $error->getMessage());
            return [
                'installed' => false,
                'error' => __('install.error.status_connection')
            ];
        }
    }

    public function handleRequest() {
        $requestedLocale = $_GET['lang'] ?? ($_POST['locale'] ?? null);
        if (is_string($requestedLocale) && in_array($requestedLocale, Lang::SUPPORTED, true)) {
            Lang::switchTo($requestedLocale);
            Lang::init($requestedLocale);
        }

        $status = $this->status();
        $error = $status['error'];
        $values = [
            'host' => 'localhost',
            'port' => '3306',
            'database' => '',
            'username' => '',
            'admin_username' => '',
            'admin_email' => ''
        ];
        $csrfToken = $_SESSION['install_csrf'] ?? '';
        if (!is_string($csrfToken) || strlen($csrfToken) !== 64) {
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['install_csrf'] = $csrfToken;
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            foreach ($values as $key => $default) {
                if (is_string($_POST[$key] ?? null)) {
                    $values[$key] = trim($_POST[$key]);
                }
            }

            $submittedToken = $_POST['csrf_token'] ?? '';
            if (!is_string($submittedToken) || !hash_equals($csrfToken, $submittedToken)) {
                $error = __('install.error.csrf');
            } else {
                try {
                    $this->install($_POST);
                    unset($_SESSION['install_csrf']);
                    header('Location: /login', true, 303);
                    exit;
                } catch (Throwable $installError) {
                    error_log('Installation failed: ' . $installError->getMessage());
                    $error = $installError->getMessage();
                }
            }
        }

        http_response_code($error !== null ? 400 : 200);
        require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'installer.php';
    }

    private function install(array $input) {
        $host = $this->inputString($input, 'host');
        $port = filter_var($this->inputString($input, 'port'), FILTER_VALIDATE_INT);
        $databaseName = $this->inputString($input, 'database');
        $databaseUser = $this->inputString($input, 'username');
        $databasePassword = is_string($input['password'] ?? null) ? $input['password'] : '';
        $adminUsername = $this->inputString($input, 'admin_username');
        $adminEmail = strtolower($this->inputString($input, 'admin_email'));
        $adminPassword = is_string($input['admin_password'] ?? null) ? $input['admin_password'] : '';
        $confirmPassword = is_string($input['admin_password_confirmation'] ?? null)
            ? $input['admin_password_confirmation']
            : '';

        if (!preg_match('/^[a-zA-Z0-9._-]{1,253}$/', $host)) {
            throw new InvalidArgumentException(__('install.error.host'));
        }
        if ($port === false || $port < 1 || $port > 65535) {
            throw new InvalidArgumentException(__('install.error.port'));
        }
        if (!preg_match('/^[a-zA-Z0-9_$-]{1,64}$/', $databaseName)) {
            throw new InvalidArgumentException(__('install.error.database'));
        }
        if ($databaseUser === '' || strlen($databaseUser) > 128 || strpos($databaseUser, "\0") !== false) {
            throw new InvalidArgumentException(__('install.error.database_user'));
        }
        if (!preg_match('/^[\p{L}\p{N}_.-]{3,50}$/u', $adminUsername)) {
            throw new InvalidArgumentException(__('install.error.admin_username'));
        }
        if (strlen($adminEmail) > 191 || !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(__('install.error.admin_email'));
        }
        if (strlen($adminPassword) < 12 || strlen($adminPassword) > 72) {
            throw new InvalidArgumentException(__('install.error.admin_password'));
        }
        if (!hash_equals($adminPassword, $confirmPassword)) {
            throw new InvalidArgumentException(__('install.error.password_mismatch'));
        }

        $configPath = $this->configurationPath();
        $configDirectory = dirname($configPath);
        if (!is_dir($configDirectory) || !is_writable($configDirectory)) {
            throw new RuntimeException(__('install.error.config_directory'));
        }
        if (is_file($configPath)) {
            throw new RuntimeException(__('install.error.config_exists'));
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $host,
            $port,
            $databaseName
        );
        try {
            $database = new PDO($dsn, $databaseUser, $databasePassword, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 10
            ]);
        } catch (PDOException $error) {
            throw new RuntimeException(__('install.error.connect'), 0, $error);
        }

        $tableCount = (int) $database->query(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()'
        )->fetchColumn();
        if ($tableCount !== 0) {
            throw new RuntimeException(__('install.error.database_not_empty'));
        }

        $schemaPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'db.sql';
        $schema = file_get_contents($schemaPath);
        if (!is_string($schema)) {
            throw new RuntimeException(__('install.error.schema_read'));
        }

        preg_match_all('/^[ \t]*(?:CREATE TABLE|ALTER TABLE)\b.*?;\s*/ims', $schema, $matches);
        if (count($matches[0]) < 15) {
            throw new RuntimeException(__('install.error.schema_invalid'));
        }

        foreach ($matches[0] as $statement) {
            $statement = preg_replace('/,\s*AUTO_INCREMENT\s*=\s*\d+/i', '', $statement);
            $statement = str_ireplace('utf8mb4_0900_ai_ci', 'utf8mb4_unicode_ci', $statement);
            $database->exec($statement);
        }

        $passwordHash = password_hash($adminPassword, PASSWORD_DEFAULT);
        if (!is_string($passwordHash)) {
            throw new RuntimeException(__('install.error.password_hash'));
        }

        $createAdmin = $database->prepare(
            'INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, 1)'
        );
        $createAdmin->execute([$adminUsername, $adminEmail, $passwordHash]);

        $config = [
            'host' => $host,
            'port' => (int) $port,
            'name' => $databaseName,
            'user' => $databaseUser,
            'pass' => $databasePassword
        ];
        $temporaryConfig = $configPath . '.' . bin2hex(random_bytes(8)) . '.tmp';
        $configContents = "<?php\nreturn " . var_export($config, true) . ";\n";
        $written = file_put_contents($temporaryConfig, $configContents, LOCK_EX);
        if ($written !== strlen($configContents)) {
            if (is_file($temporaryConfig)) {
                unlink($temporaryConfig);
            }
            throw new RuntimeException(__('install.error.config_write'));
        }
        if (DIRECTORY_SEPARATOR !== '\\' && !chmod($temporaryConfig, 0600)) {
            unlink($temporaryConfig);
            throw new RuntimeException(__('install.error.config_permissions'));
        }
        if (!rename($temporaryConfig, $configPath)) {
            unlink($temporaryConfig);
            throw new RuntimeException(__('install.error.config_activate'));
        }
    }

    private function inputString(array $input, $key) {
        return is_string($input[$key] ?? null) ? trim($input[$key]) : '';
    }

    private function configurationPath() {
        $projectName = basename(dirname(__DIR__));
        $configFileName = '.' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $projectName) . '-database.php';
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $configFileName;
    }
}
