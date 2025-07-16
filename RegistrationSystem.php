<?php
/**
 * User Registration System Class
 * 
 * Handles form validation, processing, and security
 * 
 * @author Your Name
 * @version 2.0
 */

class RegistrationSystem {
    private $errors = [];
    private $successMessage = '';
    private $formData = [];
    private $validationRules = [
        'firstName' => [
            'required' => true,
            'pattern' => '/^[A-Za-z\s\'-]{1,50}$/u',
            'message' => 'First name must contain only letters, spaces, hyphens, or apostrophes and be 1-50 characters long'
        ],
        'lastName' => [
            'required' => true,
            'pattern' => '/^[A-Za-z\s\'-]{1,50}$/u',
            'message' => 'Last name must contain only letters, spaces, hyphens, or apostrophes and be 1-50 characters long'
        ],
        'email' => [
            'required' => true,
            'message' => 'Please enter a valid email address'
        ],
        'accountNumber' => [
            'required' => true,
            'pattern' => '/^[A-Za-z0-9]{12}$/',
            'message' => 'Account number must be exactly 12 alphanumeric characters'
        ],
        'year' => [
            'required' => true,
            'options' => ['2025', '2024', '2023', '2022', '2021'],
            'message' => 'Please select a valid year'
        ]
    ];

    public function __construct() {
        $this->generateCSRFToken();
    }

    /**
     * Generate CSRF token for form security
     */
    private function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    /**
     * Validate CSRF token
     */
    private function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Sanitize input data
     */
    private function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate individual field
     */
    private function validateField($fieldName, $value) {
        if (!isset($this->validationRules[$fieldName])) {
            return true;
        }

        $rules = $this->validationRules[$fieldName];
        
        // Check if field is required and empty
        if ($rules['required'] && empty($value)) {
            $this->errors[$fieldName] = ucfirst($fieldName) . ' is required';
            return false;
        }

        // Skip further validation if field is empty and not required
        if (empty($value) && !$rules['required']) {
            return true;
        }

        // Email validation
        if ($fieldName === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$fieldName] = $rules['message'];
            return false;
        }

        // Pattern validation
        if (isset($rules['pattern']) && !preg_match($rules['pattern'], $value)) {
            $this->errors[$fieldName] = $rules['message'];
            return false;
        }

        // Options validation (for select fields)
        if (isset($rules['options']) && !in_array($value, $rules['options'])) {
            $this->errors[$fieldName] = $rules['message'];
            return false;
        }

        return true;
    }

    /**
     * Validate all form data
     */
    private function validateForm($data) {
        $this->errors = [];
        $isValid = true;

        foreach ($this->validationRules as $fieldName => $rules) {
            $value = isset($data[$fieldName]) ? $this->sanitizeInput($data[$fieldName]) : '';
            $this->formData[$fieldName] = $value;
            
            if (!$this->validateField($fieldName, $value)) {
                $isValid = false;
            }
        }

        return $isValid;
    }

    /**
     * Process form submission
     */
    public function processForm() {
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        // Check if this is a confirmed submission
        $isConfirmed = isset($_POST['confirmed']) && $_POST['confirmed'] === 'true';
        
        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCSRFToken($csrfToken)) {
            $this->errors['general'] = 'Security token validation failed. Please refresh the page and try again.';
            return false;
        }

        // Validate form data
        if (!$this->validateForm($_POST)) {
            return false;
        }

        // If this is just validation (not confirmed), return true without processing
        if (!$isConfirmed) {
            return 'validation_passed';
        }

        // Process confirmed submission
        return $this->handleSuccessfulSubmission();
    }

    /**
     * Handle successful form submission
     */
    private function handleSuccessfulSubmission() {
        // Log debug information to console/server logs instead of displaying on page
        error_log("=== Registration Form Submitted Successfully ===");
        error_log("PHP Version: " . PHP_VERSION);
        error_log("Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'));
        error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("Form Data: " . print_r($this->formData, true));
        error_log("CSRF Token: " . substr($this->getCSRFToken(), 0, 16) . "...");
        error_log("=== End Registration Log ===");
        
        // Here you would typically:
        // 1. Save to database
        // 2. Send email confirmation
        // 3. Log the registration
        
        $this->successMessage = 'Registration completed successfully! Your information has been processed.';
        
        // Clear form data on success to prevent showing sensitive information
        $this->formData = [];
        
        return true;
    }

    // Getter methods
    public function getErrors() {
        return $this->errors;
    }

    public function getSuccessMessage() {
        return $this->successMessage;
    }

    public function getFormData() {
        return $this->formData;
    }

    public function getCSRFToken() {
        return $_SESSION['csrf_token'] ?? '';
    }

    public function hasErrors() {
        return !empty($this->errors);
    }

    public function getFieldError($fieldName) {
        return $this->errors[$fieldName] ?? '';
    }

    public function getFieldValue($fieldName) {
        return $this->formData[$fieldName] ?? '';
    }

    public function hasFieldError($fieldName) {
        return isset($this->errors[$fieldName]);
    }

    public function hasGeneralError() {
        return isset($this->errors['general']);
    }

    public function getGeneralError() {
        return $this->errors['general'] ?? '';
    }
}