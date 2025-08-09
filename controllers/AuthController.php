<?php
class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function loginForm() {
        if (isset($_SESSION['user_id'])) {
            Flight::redirect('/dashboard');
            return;
        }
        
        Flight::render('auth/login', ['title' => 'Giriş Yap']);
    }
    
    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            Flight::render('auth/login', [
                'title' => 'Giriş Yap',
                'error' => 'Email ve şifre alanları zorunludur.'
            ]);
            return;
        }
        
        $user = $this->userModel->authenticate($email, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            
            Flight::redirect('/dashboard');
        } else {
            Flight::render('auth/login', [
                'title' => 'Giriş Yap',
                'error' => 'Email veya şifre hatalı.'
            ]);
        }
    }
    
    public function registerForm() {
        if (isset($_SESSION['user_id'])) {
            Flight::redirect('/dashboard');
            return;
        }
        
        Flight::render('auth/register', ['title' => 'Kayıt Ol']);
    }
    
    public function register() {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $errors = [];
        
        if (empty($username)) {
            $errors[] = 'Kullanıcı adı zorunludur.';
        }
        
        if (empty($email)) {
            $errors[] = 'Email zorunludur.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Geçerli bir email adresi giriniz.';
        } elseif ($this->userModel->emailExists($email)) {
            $errors[] = 'Bu email adresi zaten kullanılmaktadır.';
        }
        
        if (empty($password)) {
            $errors[] = 'Şifre zorunludur.';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Şifre en az 6 karakter olmalıdır.';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Şifreler eşleşmiyor.';
        }
        
        if (!empty($errors)) {
            Flight::render('auth/register', [
                'title' => 'Kayıt Ol',
                'errors' => $errors,
                'username' => $username,
                'email' => $email
            ]);
            return;
        }
        
        if ($this->userModel->create($username, $email, $password)) {
            Flight::render('auth/login', [
                'title' => 'Giriş Yap',
                'success' => 'Kayıt başarılı! Şimdi giriş yapabilirsiniz.'
            ]);
        } else {
            Flight::render('auth/register', [
                'title' => 'Kayıt Ol',
                'error' => 'Kayıt sırasında bir hata oluştu.',
                'username' => $username,
                'email' => $email
            ]);
        }
    }
    
    public function logout() {
        session_destroy();
        Flight::redirect('/login');
    }
}




