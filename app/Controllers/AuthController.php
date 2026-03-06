<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use App\Validate\Validate;
use App\Sanitize\Sanitize;

class AuthController extends Controller
{

    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Summary of create
     * @return void
     */
    public function create(): void
    {
        include_once __DIR__ . '/../Middleware/checkRole.php';
        $this->view('auth/register');
    }

    public function store(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
                $name = Sanitize::name($_POST['name'] ?? '');
                $email = Sanitize::email($_POST['email'] ?? '');
                $password = Sanitize::trim($_POST['password'] ?? '');
                $confirm_password = Sanitize::trim($_POST['confirm_password'] ?? '');
    
                $_SESSION['old'] = compact('name', 'email');
    
                $validator = new Validate();
                $validator->required('name', $name, 'Name is required.')
                          ->name('name', $name)
                          ->required('email', $email, 'Email is required.')
                          ->email('email', $email)
                          ->required('password', $password, 'Password is required.')
                          ->minLength('password', $password, 8)
                          ->required('confirm_password', $confirm_password, 'Please confirm your password.')
                          ->matches('confirm_password', $confirm_password, $password, 'Passwords do not match.');
    
                if ($validator->fails()) {
                    $_SESSION['error'] = $validator->getFirstError();
                    header('Location: /register');
                    exit;
                }
    
                $result = $this->authService->registerUser($name, $email, $password);
    
                if ($result['success']) {
                    $_SESSION['success'] = $result['message'];
                    header('Location: /login');
                } else {
                    $_SESSION['error'] = $result['error'];
                    header('Location: /register');
                }
                exit;
            }
        } catch (\Exception $e) {
            $this->log(message: $e->getMessage(), context: ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
            $this->view(path: 'auth/register');
        }
    }

    /**
     * Summary of verifyEmail
     * @return void
     */
    public function verifyEmail(): void
    {
        $token = $_GET['token'] ?? '';
        try {
            $result = $this->authService->verifyUserEmail($token);
    
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['error'];
            }
    
            header(header: 'Location: /login');
            exit;
        } catch (\Exception $e) {
            $this->log(message: $e->getMessage(), context: ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
        }
    }

    /**
     * Summary of login
     * @return void
     */
    public function login(): void
    {      
        include_once __DIR__ . '/../Middleware/checkRole.php';
        $this->view(path: 'auth/login');
    }

    /**
     * Summary of authenticate
     * @return void
     */
    public function authenticate(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
            $email = Sanitize::email(value: $_POST['email'] ?? '');
            $password = Sanitize::trim(value: $_POST['password'] ?? '');
    
            $_SESSION['old'] = compact(var_name: 'email');
    
            $validate = new Validate();
            $validate->required(field: 'email', value: $email, message: 'Email is required.')
                      ->email(field: 'email', value: $email)
                      ->required('password', $password, message: 'Password is required.');
    
            if ($validate->fails()) {
                $_SESSION['error'] = $validate->getFirstError();
                header('Location: /login');
                exit;
            }
    
            $result = $this->authService->authenticate(email: $email, password: $password);
    
            if (!$result['success']) {
                $_SESSION['error'] = $result['error'];
                header(header: 'Location: /login');  
                exit;
            }
    
            $_SESSION['user'] = $result['user'];
            unset($_SESSION['old']);
    
            header('Location: ' . ($result['user']['role'] === 'admin' ? '/admin' : '/'));
            exit;
            }
        } catch (\Exception $e) {
            $this->log(message: $e->getMessage(), context: ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
        }
    }

    /**
     * Summary of showResetForm
     * @return void
     */
    public function showResetForm(): void
    {
        include_once __DIR__ . '/../Middleware/checkRole.php';
        $this->view(path: 'auth/reset');
    }

    /**
     * Summary of sendResetLink
     * @return void
     */
    public function sendResetLink(): void
    {
        try {

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /reset');
                exit;
            }
    
            $email = Sanitize::email(value: $_POST['email'] ?? '');
            $validate = new Validate();
            $validate->required(field: 'email', value: $email)->email(field: 'email', value: $email);
            
            if ($validate->fails()) {
                header('Location: /reset?error=' . urlencode($validate->getFirstError()));
                exit;
            }
    
            $result = $this->authService->sendPasswordResetLink(email: $email);
    
            if ($result['success']) {
                // header('Location: /reset?success=' . urlencode($result['message']));
                $_SESSION['success'] = $result['message'];
                header('Location: /reset');
            } else {
                $_SESSION['error'] = $result['error'];
                header('Location: /reset');
                // header('Location: /reset?error=' . urlencode($result['error']));
            }
            exit;
        } catch (\Exception $e) {
            $this->log($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
        }
    }

    /**
     * Summary of resetPassword
     * @return void
     */
    public function  resetPassword(): void
    {

    try {
        $token = $_GET['token'] ?? '';

        if (!$token) {
            $_SESSION['error'] = 'Invalid reset link.';
            header('Location: /reset');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $result = $this->authService->validateResetToken($token);

            if (!$result['success']) {
                $_SESSION['error'] = $result['error'];
                header('Location: /reset');
                exit;
            }

            $this->view('auth/reset_password', ['token' => $token]);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = Sanitize::trim($_POST['password'] ?? '');
            $passwordConfirm = Sanitize::trim($_POST['password_confirm'] ?? '');

            $validate = new Validate();
            $validate->required('password', $password, 'Password is required.')
                      ->minLength('password', $password, 8)
                      ->required('password_confirm', $passwordConfirm, 'Please confirm your password.')
                      ->matches('password_confirm', $passwordConfirm, $password, 'Passwords do not match.');

            if ($validate->fails()) {
                $_SESSION['error'] = $validate->getFirstError();
                $this->view('auth/reset_password', ['token' => $token]);
                return;
            }

            $result = $this->authService->resetUserPassword($token, $password);

            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
                header('Location: /login');
            } else {
                $_SESSION['error'] = $result['error'];
                header('Location: /reset');
            }
            exit;
        }
        } catch (\Exception $e) {
            $this->log($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
        }
    }

    /**
     * Summary of logout
     * @return void
     */
    public function logout(): void
    {
        session_start();
        session_destroy();
        header('Location: /login');
    }

    /**
     * Summary of showUnauthorized
     * @return void
     */
    public function showUnauthorized()
    {
        header("Location: /unauthorized");
    }
}