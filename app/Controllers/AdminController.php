<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AdminService;
use App\Services\UserService;
use App\Sanitize\Sanitize;
use App\Validate\Validate;

class AdminController extends Controller 
{

    private $adminService;
    private $userService;
    public function __construct(AdminService $adminService, UserService $userService)
    {
        $this->adminService = $adminService;
        $this->userService = $userService;
    }

    /**
     * Summary of index
     * @return void
     */
    public function index(): void
    {
        $user = $_SESSION['user'];
        $this->view('admin/index', ['user' => $user]);
    }

    /**
     * Summary of dashboard
     * @return void
     */
    public function dashboard(): void
    {
        // require __DIR__ . '/../Middleware/isAdmin.php';

        try {
            $user = $_SESSION['user'];
            $websiteName = $this->adminService->getWebsiteName();
            $users = $this->adminService->getLatestUsers();
            $this->view('admin/dashboard', ['user' => $user, 'siteSettings' => ['website_name' => $websiteName, 'logo_path' => null],'users' => $users]);
        } catch(\Exception $e) {
            $this->log($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
            $this->view('admin/dashboard', ['user' => $_SESSION['user'],'siteSettings' => ['website_name' => '', 'logo_path' => null],'users' => []]);
        }       
    }

    /**
     * Summary of usersList - To get all user from database
     * @return void
     */
    public function showUsers(): void
    {
        // require __DIR__ . '/../Middleware/isAdmin.php';

        try {
            $searchQuery = Sanitize::string($_GET['search'] ?? '');
            $page = Sanitize::int($_GET['page'] ?? 1);
            $limit = 10;

            if ($searchQuery) {
                $result = $this->adminService->searchUsersPaginated($searchQuery, $page, $limit);
            } else {
                $result = $this->adminService->getUsersPaginated($page, $limit);
            }

            $websiteName = $this->adminService->getWebsiteName();
            $this->view('admin/users/index', [
                'users' => $result['users'], 
                'siteSettings' => ['website_name' => $websiteName, 'logo_path' => null],
                'searchQuery' => $searchQuery, 
                'currentPage' => $result['currentPage'],
                'totalPages' => $result['totalPages'],
                'totalUsers' => $result['totalUsers']
            ]);
        } catch(\Exception $e) {
        $this->log($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
            $this->view('admin/users/index', [
            'users' => $result['users'], 
            'siteSettings' => ['website_name' => $websiteName, 'logo_path' => null],
            'searchQuery' => $searchQuery, 
            'currentPage' => $result['currentPage'],
            'totalPages' => $result['totalPages'],
            'totalUsers' => $result['totalUsers']
        ]);
        }
        
    }

    /**
     * Summary of adminOnlyUsers - Admin role user list
     * @return void
     */
    public function showAdmin(): void
    {
        // require __DIR__ . '/../Middleware/isAdmin.php';

        try {
            $users = $this->adminService->getAllAdmins();
            $websiteName = $this->adminService->getWebsiteName();
            $this->view('admin/users', ['users' => $users, 'siteSettings' => ['website_name' => $websiteName, 'logo_path' => null]]);
        } catch(\Exception $e) {
            $this->log($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
            $this->view('admin/users', ['users' => $users, 'siteSettings' => ['website_name' => $websiteName, 'logo_path' => null]]);
        }
    }

    /**
     * Summary of edit - To show the user's profile edit page
     * @return void
     */
    public function edit(): void
    {
        // require __DIR__ . '/../Middleware/isAdmin.php';

        try {
            $id = Sanitize::int($_GET['id'] ?? 0);
            $user = null;
    
            if ($id) {
                $user = $this->adminService->getUserById($id);
            }
    
            $this->view('admin/users/edit', ['user' => $user]);
        } catch (\Exception $e) {
            $this->log($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
            $this->view('admin/users/edit', ['user' => $user]);
        }
    }

    /**
     * Summary of update - To update the user's profile 
     * @return void
     */
    public function update(): void
    {
        // require __DIR__ . '/../Middleware/isAdmin.php';

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
                $id = Sanitize::int($_POST['id'] ?? 0);
                $name = Sanitize::name($_POST['name'] ?? '');
                $email = Sanitize::email($_POST['email'] ?? '');
                $is_email_verified = Sanitize::int($_POST['is_email_verified'] ?? 0);
                $password = Sanitize::trim($_POST['password'] ?? '');
                $confirm_password = Sanitize::trim($_POST['confirm_password'] ?? '');
    
                $validator = new Validate();
                $validator->required('id', (string)$id, 'User ID is required.')
                          ->required('name', $name, 'Name is required.')
                          ->name('name', $name)
                          ->required('email', $email, 'Email is required.')
                          ->email('email', $email);
                
                if (!empty($password)) {
                    $validator->minLength('password', $password, 8)
                              ->matches('confirm_password', $confirm_password, $password, 'Passwords do not match.');
                }
                
                if ($validator->fails()) {
                    $user = $this->adminService->getUserById($id);
                    $this->view('admin/edit_user_profile', ['user' => $user, 'error' => $validator->getFirstError()]);
                    return;
                }
    
                $result = $this->adminService->updateUser($id, $name, $email, $is_email_verified, $password);
                
                header('Location: /admin/users');
                exit;
            }
        } catch (\Exception $e) {
            $this->log($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
        }

    }

    /**
     * Summary of editProfile - To show admin updates profile form
     * @return void
     */
    public function editProfile(): void
    {
        // require __DIR__ . '/../Middleware/isAdmin.php';

        try {
            $user = $_SESSION['user'];
            $this->view('admin/edit', ['user' => $user]);
        } catch (\Exception $e) {
            $this->log($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
        }
    }

    /**
     * Summary of updateProfile - To update the admin's profile
     * @return void
     */
    public function updateProfile(): void
    {
        // require __DIR__ . '/../Middleware/isAdmin.php';

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
                $id = $_SESSION['user']['id'];
    
                $name = Sanitize::name($_POST['name'] ?? '');
                $email = Sanitize::email($_POST['email'] ?? '');
    
                $validate = new Validate();
                $validate->required('name', $name, 'Name is required.')
                          ->name('name', $name)
                          ->required('email', $email, 'Email is required.')
                          ->email('email', $email);
                
                if ($validate->fails()) {
                    $user = $_SESSION['user'];
                    $this->view('admin/edit_admin_profile', ['user' => $user, 'error' => $validate->getFirstError()]);
                    return;
                }
    
                $result = $this->adminService->updateAdminProfile($id, $name, $email);
    
                $_SESSION['user']['name'] = $name;
                $_SESSION['user']['email'] = $email;
                header('Location: /admin/dashboard');
            }
        } catch (\Exception $e) {
            $this->log($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
        }
    }

    /**
     * Summary of changePassword
     * @return void
     */
    public function changePassword(): void
    {
        // require __DIR__ . '/../Middleware/isAdmin.php';
        $error = '';
        $success = '';

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
                $id = $_SESSION['user']['id'];
    
                $old_password = Sanitize::trim($_POST['old_password'] ?? '');
                $new_password = Sanitize::trim($_POST['new_password'] ?? '');
                $confirm_password = Sanitize::trim($_POST['confirm_password'] ?? '');
    
                $validate = new Validate();
                $validate->required('old_password', $old_password, 'Old password is required.')
                          ->required('new_password', $new_password, 'New password is required.')
                          ->minLength('new_password', $new_password, 6)
                          ->required('confirm_password', $confirm_password, 'Please confirm new password.')
                          ->matches('confirm_password', $confirm_password, $new_password, 'New password and confirm password do not match.');
                
                if ($validate->fails()) {
                    $error = $validate->getFirstError();
                } else {
                    $result = $this->adminService->changeAdminPassword($id, $_SESSION['user']['email'], $old_password, $new_password);
                    if ($result['success']) {
                        $success = $result['message'];
                        header("Location: /dashboard");
                    } else {
                        $error = $result['error'];
                    }
                }
                $user = $_SESSION['user'];
                $this->view('admin/dashboard', ['user' => $user, 'resetPasswordError' => $error, 'resetPasswordSuccess' => $success]);
                return;
            }
        } catch (\Exception $e) {
            $this->log($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
        }
    }

    /**
     * Summary of settings - To show website settings
     * @return void
     */
    public function settings(): void
    {
        // require __DIR__ . '/../Middleware/isAdmin.php';
        
        try {
            $websiteName = $this->adminService->getWebsiteName();
            $success = $_GET['success'] ?? '';
            $error = $_GET['error'] ?? '';
            
            $this->view('admin/settings/settings', [
                'websiteName' => $websiteName,
                'siteSettings' => ['website_name' => $websiteName, 'logo_path' => null],
                'success' => $success,
                'error' => $error
            ]);
        } catch (\Exception $e) {
            $this->log($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
        }
    }

    /**
     * Summary of updateSettings - To update website settings 
     * @return void
     */
    public function updateSettings(): void
    {
        // require __DIR__ . '/../Middleware/isAdmin.php';
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /admin/dashboard');
                exit;
            }
            
            $websiteName = Sanitize::string($_POST['website_name'] ?? '');
            
            $validate = new Validate();
            $validate->required('website_name', $websiteName, 'Website name is required.');
            
            if ($validate->fails()) {
                header('Location: /admin/settings?error=' . urlencode($validate->getFirstError()));
                exit;
            }
            
            $adminId = $_SESSION['user']['id'];
            
            $result = $this->adminService->updateWebsiteName($websiteName, $adminId);
            
            if ($result) {
                header('Location: /admin/settings?success=' . urlencode('Settings updated successfully'));
            } else {
                header('Location: /admin/settings?error=' . urlencode('Failed to update settings'));
            }
            exit;
        } catch (\Exception $e) {
            $this->log($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $_SESSION['error'] = "Something went wrong please refresh";
        }
        
    }
}