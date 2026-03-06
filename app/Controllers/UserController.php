<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\UserService;
use App\Sanitize\Sanitize;
use App\Services\AdminService;
use App\Validate\Validate;

class UserController extends Controller
{
    private $userService;
    private $adminService;
    public function __construct(UserService $userService, AdminService $adminService)
    {
        $this->userService = $userService;
        $this->adminService = $adminService;
    }

    /**
     * Summary of index
     * @return void
     */
    public function index()
    {
        $user = $_SESSION['user'];
        $this->view('users/index', ['user' => $user]);
    }

    /**
     * Summary of dashboard
     * @return void
     */
    public function dashboard()
    {
        // require __DIR__ . '/../Middleware/isUser.php';
        $user = $_SESSION['user'];
        $profile = null;
        $websiteName = '';
        try {
            $websiteName = $this->adminService->getWebsiteName();
    
            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard");
            }
            $profile = $this->userService->getUserProfile($user['id']);
            $this->view('users/dashboard', ['user' => $user, 'profile' => $profile, 'siteSettings' => ['website_name' => $websiteName, 'logo_path' => null]]);
        } catch (\Exception $e) {
            $this->log($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
            $this->view('users/dashboard', ['user' => $user, 'profile' => $profile, 'siteSettings' => ['website_name' => $websiteName, 'logo_path' => null]]);
        }
    }

    /**
     * Summary of edit
     * @return void
     */
    public function edit() {
        // require __DIR__ . '/../Middleware/isUser.php';

        $user = $_SESSION['user'];

        try {
            $profile = $this->userService->getUserProfile($user['id']);
    
            $this->view('users/edit', ['user' => $user, 'profile' => $profile]);
        } catch (\Exception $e) {
            $this->log($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
            $this->view('users/edit', ['user' => $user, 'profile' => $profile]);
        }
    }

    /**
     * Summary of update
     * @return void
     */
    public function update()
    {
        // require __DIR__ . '/../Middleware/isUser.php';

        try {
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
                $id = $_SESSION['user']['id'];
                $profile = $this->userService->getUserProfile($id);
    
                $name = Sanitize::name($_POST['name'] ?? '');
                $email = Sanitize::email($_POST['email'] ?? '');
                $password = Sanitize::trim($_POST['password'] ?? '');
                $confirm_password = Sanitize::trim($_POST['confirm_password'] ?? '');
                $bio = Sanitize::trim($_POST['bio'] ?? '');
    
                $validate = new Validate();
                $validate->required('name', $name, 'Name is required.')
                          ->name('name', $name)
                          ->required('email', $email, 'Email is required.')
                          ->email('email', $email);
                
                if (!empty($password)) {
                    $validate->minLength('password', $password, 8)
                              ->matches('confirm_password', $confirm_password, $password, 'Passwords do not match.');
                }
                
                if ($validate->fails()) {
                    $userData = $_SESSION['user'];
                    $this->view('users/edit', ['user' => $userData, 'profile' => $profile, 'error' => $validate->getFirstError()]);
                    return;
                }
    
                $avatarPath = null;
    
                if (!empty($_FILES['avatar']['name'])) {
                    $uploadResult = $this->userService->uploadAvatar($id, $_FILES['avatar']);
    
                    if (!$uploadResult['success']) {
                        $userData = $_SESSION['user'];
                        $this->view('users/edit', ['user' => $userData, 'profile' => $profile, 'error' => $uploadResult['error']]);
                        return;
                    }
                    $avatarPath = $uploadResult['filename'];
                }
    
                $result = $this->userService->updateUserProfile($id, $name, $email, $password);
                if (!$result['success']) {
                    $userData = $_SESSION['user'];
                    $this->view('users/edit', ['user' => $userData, 'profile' => $profile, 'error' => $result['error']]);
                    return;
                }

                $profileResult = $this->userService->updateProfile($id, $bio, $avatarPath);
                if (!$profileResult['success']) {
                    $userData = $_SESSION['user'];
                    $this->view('users/edit', ['user' => $userData, 'profile' => $profile, 'error' => $profileResult['error']]);
                    return;
                }
    
                $_SESSION['user']['name'] = $name;
                $_SESSION['user']['email'] = $email;
            
                header('Location: /dashboard');
                exit;
            }
        } catch (\Exception $e) {
            $this->log($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
            header('Location: /dashboard');
            exit;
        }
    }

    /**
     * Summary of showChangePasswordForm
     * @return void
     */
    public function showChangePasswordForm()
    {
        // require __DIR__ . '/../Middleware/isUser.php';
        
        $user = $_SESSION['user'];
        $this->view('users/change_password', ['user' => $user]);
    }

    /**
     * Summary of changePassword
     * @return void
     */
    public function changePassword()
    {
        // require __DIR__ . '/../Middleware/isUser.php';
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $userId = $_SESSION['user']['id'];
                $userEmail = $_SESSION['user']['email'];
    
                $currentPassword = Sanitize::trim($_POST['current_password'] ?? '');
                $newPassword = Sanitize::trim($_POST['new_password'] ?? '');
                $confirmPassword = Sanitize::trim($_POST['confirm_password'] ?? '');
    
                $validate = new Validate();
                $validate->required('current_password', $currentPassword, 'Current password is required.')
                          ->required('new_password', $newPassword, 'New password is required.')
                          ->required('confirm_password', $confirmPassword, 'Confirm password is required.')
                          ->minLength('new_password', $newPassword, 8, 'New password must be at least 8 characters.')
                          ->matches('confirm_password', $confirmPassword, $newPassword, 'Passwords do not match.');
    
                if ($validate->fails()) {
                    $user = $_SESSION['user'];
                    $this->view('users/change_password', [
                        'user' => $user,
                        'error' => $validate->getFirstError()
                    ]);
                    return;
                }
    
                $result = $this->userService->changePassword($userId, $userEmail, $currentPassword, $newPassword);
    
                if (!$result['success']) {
                    $user = $_SESSION['user'];
                    $this->view('users/change_password', [
                        'user' => $user,
                        'error' => $result['error']
                    ]);
                    return;
                }
    
                $_SESSION['success'] = $result['message'];
                header('Location: /dashboard');
                exit;
            }
        } catch (\Exception $e) {
            $this->log($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
        }
    }
}
