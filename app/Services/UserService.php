<?php 

namespace App\Services;

use App\Core\Database;

class UserService
{
    private $conn;

    public function __construct(Database $db)
    {
        $this->conn = $db->connect();
    }

    /**
     * Summary of getUserById
     * @param mixed $id
     * @return array|bool|null
     */
    public function getUserById($id): array|bool|null
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Summary of getUserProfile
     * @param mixed $id
     * @return array|bool|null
     */
    public function getUserProfile($id): array|bool|null
    {
        $stmt = $this->conn->prepare("SELECT * FROM user_profile WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Summary of updateUserProfile
     * @param mixed $id
     * @param mixed $name
     * @param mixed $email
     * @param mixed $password
     * @return array{error: string, success: bool|array{message: string, success: bool}}
     */
    public function updateUserProfile($id, $name, $email, $password = null): array
    {
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        if (!$stmt) {
            return ['success' => false, 'error' => 'Failed to prepare user update statement.'];
        }

        $stmt->bind_param("ssi", $name, $email, $id);
        if (!$stmt->execute()) {
            return ['success' => false, 'error' => 'Failed to update user profile.'];
        }

        if (!empty($password)) {
            $hashedPass = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            if (!$stmt) {
                return ['success' => false, 'error' => 'Failed to prepare password update statement.'];
            }

            $stmt->bind_param("si", $hashedPass, $id);
            if (!$stmt->execute()) {
                return ['success' => false, 'error' => 'Failed to update password.'];
            }
        }

        return ['success' => true, 'message' => 'Profile updated successfully'];
    }

    /**
     * Summary of updateProfile
     * @param mixed $id
     * @param mixed $bio
     * @param mixed $avatar
     * @return array{error: string, success: bool|array{message: string, success: bool}}
     */
    public function updateProfile($id, $bio, $avatar = null): array
    {
        $stmt = $this->conn->prepare("SELECT id FROM user_profile WHERE user_id = ?");
        if (!$stmt) {
            return ['success' => false, 'error' => 'Failed to prepare profile lookup statement.'];
        }

        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            return ['success' => false, 'error' => 'Failed to load current profile.'];
        }

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            if ($avatar) {
                $stmt = $this->conn->prepare("UPDATE user_profile SET bio = ?, avatar = ? WHERE user_id = ?");
                if (!$stmt) {
                    return ['success' => false, 'error' => 'Failed to prepare profile update statement.'];
                }

                $stmt->bind_param("ssi", $bio, $avatar, $id);
            } else {
                $stmt = $this->conn->prepare("UPDATE user_profile SET bio = ? WHERE user_id = ?");
                if (!$stmt) {
                    return ['success' => false, 'error' => 'Failed to prepare bio update statement.'];
                }

                $stmt->bind_param("si", $bio, $id);
            }
            if (!$stmt->execute()) {
                return ['success' => false, 'error' => 'Failed to update profile details.'];
            }
        } else {
            if ($avatar) {
                $stmt = $this->conn->prepare("INSERT INTO user_profile (user_id, bio, avatar) VALUES (?, ?, ?)");
                if (!$stmt) {
                    return ['success' => false, 'error' => 'Failed to prepare profile insert statement.'];
                }

                $stmt->bind_param("iss", $id, $bio, $avatar);
            } else {
                $stmt = $this->conn->prepare("INSERT INTO user_profile (user_id, bio) VALUES (?, ?)");
                if (!$stmt) {
                    return ['success' => false, 'error' => 'Failed to prepare profile insert statement.'];
                }

                $stmt->bind_param("is", $id, $bio);
            }
            if (!$stmt->execute()) {
                return ['success' => false, 'error' => 'Failed to create profile details.'];
            }
        }

        return ['success' => true, 'message' => 'Profile updated successfully'];
    }

    /**
     * Summary of changePassword
     * @param mixed $userId
     * @param mixed $email
     * @param mixed $oldPassword
     * @param mixed $newPassword
     * @return array{error: string, success: bool|array{message: string, success: bool}}
     */
    public function changePassword($userId, $email, $oldPassword, $newPassword): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();   

        if (!$user || !password_verify($oldPassword, $user['password'])) {
            return ['success' => false, 'error' => 'Old password is incorrect.'];
        }

        $hashedPass = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPass, $userId);
        $stmt->execute();
        
        return ['success' => true, 'message' => 'Password updated successfully.'];
    }

    /**
     * Summary of uploadAvatar
     * @param mixed $userId
     * @param mixed $file
     * @return array{error: string, success: bool|array{filename: string, success: bool}}
     */
    public function uploadAvatar($userId, $file): array
    {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $file['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if (!in_array(strtolower($ext), $allowed)) {
            return ['success' => false, 'error' => 'Only this allowed (jpg, jpeg, png, gif)'];
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'error' => 'greater than 5MB'];
        }

        $profile = $this->getUserProfile($userId);
        if ($profile && !empty($profile['avatar'])) {
            $oldAvatarPath = __DIR__ . '/../../storage/avatars/' . $profile['avatar'];
            if (file_exists($oldAvatarPath)) {
                @unlink($oldAvatarPath);
            }
        }

        $uploadDir = __DIR__ . '/../../storage/avatars/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        $newFilename = 'avatar_' . $userId . '_' . time() . '.' . $ext;
        $uploadPath = $uploadDir . $newFilename;

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return ['success' => false, 'error' => 'uploading faileds'];
        }

        return ['success' => true, 'filename' => $newFilename];

    }
}