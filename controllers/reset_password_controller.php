<?php
namespace Controllers;

require_once __DIR__ . '/../database/databaseConnection.php';
require_once __DIR__ . '/../database/user.php';

class ResetPasswordController {
    private $userModel;

    public function __construct() {
        $this->userModel = \User::getInstance();
    }

    public function handleReset() {
        // Validate input
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!$email || empty($newPassword) || empty($confirmPassword)) {
            $this->redirectWithError('All fields are required');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $this->redirectWithError('Passwords do not match');
            return;
        }

        try {
            // Check if user exists
            $user = $this->userModel->selectUserByEmail($email);
            
            if (!$user) {
                $this->redirectWithError('No account found with this email');
                return;
            }

            // Update password
            $success = $this->userModel->updatePassword($user['id'], $newPassword);
            
            if ($success) {
                $this->redirectWithSuccess('Password has been reset successfully');
            } else {
                $this->redirectWithError('Failed to reset password');
            }
        } catch (\Exception $e) {
            error_log("Password reset error: " . $e->getMessage());
            $this->redirectWithError('An error occurred while resetting password');
        }
    }

    private function redirectWithError($error) {
        header('Location: /forgot_password?error=' . urlencode($error));
        exit();
    }

    private function redirectWithSuccess($message) {
        header('Location: /forgot_password?success=' . urlencode($message));
        exit();
    }
}

// Instantiate and handle the request
$controller = new ResetPasswordController();
$controller->handleReset();