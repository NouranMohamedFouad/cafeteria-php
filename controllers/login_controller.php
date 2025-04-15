<?php
namespace Controllers;

require_once __DIR__ . '/../database/databaseConnection.php';
require_once __DIR__ . '/../database/user.php';

class LoginController {
    private $userModel;

    public function __construct() {
        $this->userModel = \User::getInstance();
    }

    public function handleLogin() {
        // Start session
        session_start();

        // Validate input
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (!$email || empty($password)) {
            $this->redirectWithError('Invalid input');
            return;
        }

        try {
            $user = $this->userModel->verifyLogin($email, $password);

            if ($user) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['name'];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header('Location: /home');
                } else {
                    header('Location: /home');
                }
                exit();
            } else {
                // Login failed
                $this->redirectWithError('Invalid email or password');
            }
        } catch (\Exception $e) {
            // Log the error
            error_log("Login error: " . $e->getMessage());
            $this->redirectWithError('An error occurred during login');
        }
    }

    private function redirectWithError($error) {
        header('Location: /login?error=' . urlencode($error));
        exit();
    }
}

// Instantiate and handle the request
$controller = new LoginController();
$controller->handleLogin(); 