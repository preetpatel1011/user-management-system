<?php

namespace App\Services;

use App\Core\Database;

class AdminService
{
    private $conn;
    private $mail;

    public function __construct(Database $db, MailService $mailService)
    {
        $this->conn = $db->connect();
        $this->mail = $mailService;
    }

    /**
     * Summary of getUserById
     * @param mixed $id
     * @return array|bool|null
     */
    public function getUserById($id): array|bool|null
    {
        $users = $this->getAllUsers();
        
        foreach ($users as $user) {
            if ($user['id'] == $id) {
                return $user;
            }
        }
        
        return null;
    }

    /**
     * Summary of getLatestUsers
     * @return array<array|bool|null>
     */
    public function getLatestUsers(): array
    {
        $stmt = $this->conn->prepare("SELECT id, name, email, created_at FROM users WHERE deleted_at IS NULL AND role = 'user' ORDER BY id DESC LIMIT 5");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        return $users;
    }

    /**
     * Summary of getAllUsers
     * @return array<array|bool|null>
     */
    public function getAllUsers(): array
    {
        $stmt = $this->conn->prepare("SELECT id, name, email, role, email_verified_at, is_active, is_email_verified, status, created_at FROM users WHERE deleted_at IS NULL AND role = 'user' ORDER BY id DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        return $users;
    }

    /**
     * Summary of getAllAdmins
     * @return array<array|bool|null>
     */
    public function getAllAdmins(): array
    {
        $stmt = $this->conn->prepare("SELECT id, name, email, created_at FROM users WHERE deleted_at IS NULL AND role = 'admin' ORDER BY id DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        return $users;
    }

    /**
     * Summary of getUsersPaginated
     * @param mixed $page
     * @param mixed $limit
     * @return array{currentPage: mixed, totalPages: float, totalUsers: mixed, users: array}
     */
    public function getUsersPaginated($page = 1, $limit = 10): array
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM users WHERE deleted_at IS NULL AND role = 'user'");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $totalUsers = $row['total'];

        $offset = ($page - 1) * $limit;
        $stmt = $this->conn->prepare("SELECT id, name, email, role, email_verified_at, is_active, is_email_verified, status, created_at FROM users WHERE deleted_at IS NULL AND role = 'user' ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        $totalPages = ceil($totalUsers / $limit);

        return [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ];
    }

    /**
     * Summary of searchUsersPaginated
     * @param mixed $searchValue
     * @param mixed $page
     * @param mixed $limit
     * @return array{currentPage: mixed, searchQuery: mixed, totalPages: float, totalUsers: mixed, users: array}
     */
    public function searchUsersPaginated($searchValue, $page = 1, $limit = 10): array
    {
        $search = "%$searchValue%";

        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM users WHERE deleted_at IS NULL AND role = 'user' AND (name LIKE ? OR email LIKE ?)");
        $stmt->bind_param("ss", $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $totalUsers = $row['total'];

        $offset = ($page - 1) * $limit;
        $stmt = $this->conn->prepare("SELECT id, name, email, role, email_verified_at, is_active, is_email_verified, status, created_at FROM users WHERE deleted_at IS NULL AND role = 'user' AND (name LIKE ? OR email LIKE ?) ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ssii", $search, $search, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        $totalPages = ceil($totalUsers / $limit);

        return [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'searchQuery' => $searchValue
        ];
    }

    /**
     * Summary of updateUser
     * @param mixed $id
     * @param mixed $name
     * @param mixed $email
     * @param mixed $isEmailVerify
     * @param mixed $password
     * @return array{message: string, success: bool}
     */
    public function updateUser($id, $name, $email, $isEmailVerify, $password = null): array
    {
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, is_email_verified = ?, email_verified_at = CURRENT_TIMESTAMP, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssii", $name, $email, $isEmailVerify, $id);
        $stmt->execute();
        $stmt->close();

        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $id);
            $stmt->execute();
        }
        return ['success' => true, 'message' => 'User Updated'];
    }

    /**
     * Summary of deleteUser
     * @param mixed $id
     * @return array{message: string, success: bool}
     */
    public function deleteUser($id): array
    {
        $stmt = $this->conn->prepare("UPDATE users SET deleted_at = NOW(), is_active = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        return ['success' => true, 'message' => 'User Deleted'];
    }

    /**
     * Summary of updateAdminProfile
     * @param mixed $id
     * @param mixed $name
     * @param mixed $email
     * @return array{message: string, success: bool}
     */
    public function updateAdminProfile($id, $name, $email)
    {
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $id);
        $stmt->execute();
        
        return ['success' => true, 'message' => 'Profile Updated'];
    }

    /**
     * Summary of changeAdminPassword
     * @param mixed $userId
     * @param mixed $email
     * @param mixed $oldPass
     * @param mixed $newPass
     * @return array{error: string, success: bool|array{message: string, success: bool}}
     */
    public function changeAdminPassword($userId, $email, $oldPass, $newPass): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user || !password_verify($oldPass, $user['password'])) {
            return ['success' => false, 'error' => 'Old password is incorrect.'];
        }
        
        $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPass, $userId);
        $stmt->execute();

        return ['success' => true, 'message' => 'Password Updated'];
    }

    /**
     * Summary of updateUserApprovalStatus
     * @param mixed $id
     * @param mixed $status
     * @return array{error: string, success: bool|array{message: string, success: bool}}
     */
    public function updateUserApprovalStatus($id, $status): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            return ['success' => false, 'error' => 'User not found'];
        }

        $stmt = $this->conn->prepare("UPDATE users SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ii", $status, $id);
        $stmt->execute();
        $stmt->close();

        if ($status == 1) {
            $this->mail->sendApprovalMail($user['email'], $user['name']);
        } else {
            $this->mail->sendRejectMail($user['email'], $user['name']);
        }

        return ['success' => true, 'message' => 'User status Updated'];
    }

    /**
     * Summary of updateUserActiveStatus
     * @param mixed $id
     * @param mixed $status
     * @return array{message: string, success: bool}
     */
    public function updateUserActiveStatus($id, $status): array
    {
        $stmt = $this->conn->prepare("UPDATE users SET is_active = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ii", $status, $id);
        $stmt->execute();
        $stmt->close();
        
        return ['success' => true, 'message' => 'User Activity Updated'];
    }

    /**
     * Summary of getWebsiteName
     */
    public function getWebsiteName()
    {
        $stmt = $this->conn->prepare("SELECT website_name FROM general_settings LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['website_name'] ?? 'Admin Dashboard';
    }

    /**
     * Summary of updateWebsiteName
     * @param mixed $websiteName
     * @param mixed $adminId
     * @return bool
     */
    public function updateWebsiteName($websiteName, $adminId = null): bool
    {
        $stmt = $this->conn->prepare("SELECT id FROM general_settings LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            if ($adminId) {
                $stmt = $this->conn->prepare("UPDATE general_settings SET website_name = ?, updated_by = ?, updated_at = CURRENT_TIMESTAMP WHERE id = 1");
                $stmt->bind_param("si", $websiteName, $adminId);
            } else {
                $stmt = $this->conn->prepare("UPDATE general_settings SET website_name = ?, updated_at = CURRENT_TIMESTAMP WHERE id = 1");
                $stmt->bind_param("s", $websiteName);
            }
        } else {
            if ($adminId) {
                $stmt = $this->conn->prepare("INSERT INTO general_settings (website_name, updated_by, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP)");
                $stmt->bind_param("si", $websiteName, $adminId);
            } else {
                $stmt = $this->conn->prepare("INSERT INTO general_settings (website_name, updated_at) VALUES (?, CURRENT_TIMESTAMP)");
                $stmt->bind_param("s", $websiteName);
            }
        }
        
        return $stmt->execute();
    }
}

