<?php

namespace App\Services;

use App\Core\Database;

class AuthService
{
    private $conn;
    private $mail;
    private $config;
    public function __construct(Database $db, MailService $mailService)
    {
        $this->config = require __DIR__ . '/../../config/mail.php';
        $this->conn = $db->connect();
        $this->mail = $mailService;
    }

    /**
     * Summary of registerUser
     * @param mixed $name
     * @param mixed $email
     * @param mixed $password
     * @return array{error: string, success: bool|array{message: string, success: bool}}
     */
    public function registerUser($name, $email, $password): array
    {
         $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return ['success' => false, 'error' => 'emaill already exist'];
        }

        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, is_email_verified, status) VALUES (?, ?, ?, 0, 2)");
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("sss", $name, $email, $hashedPassword);
        $stmt->execute();

        $userId = $this->conn->insert_id;

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $deleteOld = $this->conn->prepare("DELETE FROM email_verification WHERE user_id = ?");
        $deleteOld->bind_param('i', $userId);
        $deleteOld->execute();

        $stmt = $this->conn->prepare("INSERT INTO email_verification (user_id, email, token, expires_at) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('isss', $userId, $email, $token, $expiresAt);
        $stmt->execute();

        $userVerificationSent = $this->mail->sendVerificationEmail($email, $name, $token);

        sleep(3);

        try {
            $this->mail->sendNewUserJoined($this->config['admin_email'], $name, $email);
        } catch (\Exception $e) {
            error_log('email failed: ' . $e->getMessage());
        }
        if ($userVerificationSent ) {
            return ['success' => true, 'message' => 'Please chack mail and verify the Email'];
        } else {
            return ['success' => false, 'error' => 'Registration successful but email sending failed'];
        }
    }

    /**
     * Summary of verifyUserEmail
     * @param mixed $token
     * @return array{error: string, success: bool|array{message: string, success: bool}}
     */
    public function verifyUserEmail($token): array
    {
        if (!$token) {
            return ['success' => false, 'error' => 'Not a valid link'];
        }

        $stmt = $this->conn->prepare("SELECT user_id FROM email_verification WHERE token = ? AND expires_at > NOW()");
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $verification = $result->fetch_assoc();

        if (!$verification) {
            return ['success' => false, 'error' => 'Verification link is invalid or expired.'];
        }

        $userId = $verification['user_id'];

        $updateStmt = $this->conn->prepare("UPDATE users SET is_email_verified = 1, email_verified_at = NOW() WHERE id = ?");
        $updateStmt->bind_param('i', $userId);
        $updateStmt->execute();

        $deleteStmt = $this->conn->prepare("DELETE FROM email_verification WHERE token = ?");
        $deleteStmt->bind_param('s', $token);
        $deleteStmt->execute();

        return ['success' => true, 'message' => 'Email verified succcessfully you can login'];
    }

    /**
     * Summary of authenticate
     * @param mixed $email
     * @param mixed $password
     * @return array{error: string, success: bool|array{success: bool, user: array|bool}}
     */
    public function authenticate($email, $password): array
    {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ['success' => false, 'error' => 'User not exist.'];
        }

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'error' => 'Invalid credentials.'];
        }

        if (isset($user['is_email_verified']) && (int)$user['is_email_verified'] !== 1) {
            return ['success' => false, 'error' => 'Please verify your email'];
        }
        if (isset($user['is_active']) && $user['is_active'] != 1) {
            return ['success' => false, 'error' => 'Your account has been deactivated'];
        }
        if (isset($user['status']) && $user['status'] != 1) {
            $error = ($user['status'] == 0) ? 'Your account has been rejected by admin.' : 'Your account is not approved by admin.';
            return ['success' => false, 'error' => $error];
        }
        if ($user['deleted_at'] !== null) {
            return ['success' => false, 'error' => 'Invalid Credentials.'];
        }

        return ['success' => true, 'user' => $user];
    }

    /**
     * Summary of sendPasswordResetLink
     * @param mixed $email
     * @return array{error: string, success: bool|array{message: string, success: bool}}
     */
    public function sendPasswordResetLink($email): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

            $stmt = $this->conn->prepare("INSERT INTO forgot_password (user_id, email, token, expires_at, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param('isss', $user['id'], $email, $token, $expiresAt);
            $created = $stmt->execute();

            if ($created) {
                $resetLink = "http://localhost:4000/reset-password?token=" . urlencode($token);
                $mailSent = $this->mail->sendResetPasswordEmail($email, $user['name'], $resetLink);

                if (!$mailSent) {
                    return ['success' => false, 'error' => 'Email sendingg failed.'];
                }
            }
        }
        return ['success' => true, 'message' => 'We will chack if the email exiists'];
    }

    /**
     * Summary of validateResetToken
     * @param mixed $token
     * @return array{error: string, success: bool|array{record: array|bool, success: bool}}
     */
    public function validateResetToken($token): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM forgot_password WHERE token = ? LIMIT 1");
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $record = $result->fetch_assoc();

        if (!$record) {
            return['success' => false, 'error' => 'Invalid reset link'];
        }

        if (strtotime($record['expires_at'] < time())) {
            return ['success' => false, 'error' => 'reset link has expired'];
        }

        return ['success' => true, 'record' => $record];
    }

    /**
     * Summary of resetUserPassword
     * @param mixed $token
     * @param mixed $password
     * @return array{error: string, success: bool|array{message: string, success: bool}}
     */
    public function resetUserPassword($token, $password): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM forgot_password WHERE token = ? LIMIT 1");
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $record = $result->fetch_assoc();

        if (!$record) {
            return ['success' => false, 'error' => 'Invalid reset link.'];
        }

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $record['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            return ['success' => false, 'error' => 'User not found.'];
        }

        $hashedPass = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param('si', $hashedPass, $record['user_id']);
        $stmt->execute();

        $stmt = $this->conn->prepare("DELETE FROM forgot_password WHERE token = ?");
        $stmt->bind_param('s', $token);
        $stmt->execute();

        return ['success' => true, 'message' => 'Password updated successfully.'];
    }
}