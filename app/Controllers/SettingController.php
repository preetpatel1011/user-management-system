<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AdminService;
use App\Services\UserService;
use App\Sanitize\Sanitize;
use App\Validate\Validate;

class SettingController extends Controller
{
    private $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * Summary of settings - To show website settings
     * @return void
     */
    public function settings(): void
    {   
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