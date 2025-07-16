<?php
/**
 * Enhanced User Registration System with Verification Modal
 * 
 * IMPORTANT: This file must be saved with a .php extension (e.g., registration.php)
 * and run through a web server with PHP support.
 * 
 * Features:
 * - Client-side verification modal before submission
 * - Server-side validation and processing
 * - CSRF protection
 * - Real-time form validation
 * 
 * @author Your Name
 * @version 2.0
 */

// Include the RegistrationSystem class
require_once 'RegistrationSystem.php';

// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simple error handling for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize the registration system
$registration = new RegistrationSystem();
$formProcessed = false;

// Process form if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formProcessed = $registration->processForm();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>User Registration</h1>
            <p id="headerSubtext">Please fill out all required fields</p>
        </div>

        <?php if ($registration->hasGeneralError()): ?>
            <div class="general-error">
                <strong>⚠️ Error:</strong> <?php echo htmlspecialchars($registration->getGeneralError()); ?>
            </div>
        <?php endif; ?>

        <?php if ($formProcessed === true): ?>
            <div class="success-message" id="successMessage">
                <h3>✅ Registration Successful!</h3>
                <p><?php echo htmlspecialchars($registration->getSuccessMessage()); ?></p>
                <p><strong>Thank you for registering with us!</strong></p>
                <div style="margin-top: 20px;">
                    <button onclick="resetToFreshForm()" class="submit-button" style="background: var(--primary-color);">
                        Register Another User
                    </button>
                </div>
            </div>
            <script>
                // Hide the "Please fill out all required fields" text on server success
                document.getElementById('headerSubtext').style.display = 'none';
            </script>
        <?php endif; ?>
        
        <!-- Always present success message for SPA behavior -->
        <div class="success-message" id="spaSuccessMessage" style="display: none;">
            <h3>✅ Registration Successful!</h3>
            <p>Registration completed successfully! Your information has been processed.</p>
            <p><strong>Thank you for registering with us!</strong></p>
            <div style="margin-top: 20px;">
                <button onclick="resetToFreshForm()" class="submit-button" style="background: var(--primary-color);">
                    Register Another User
                </button>
            </div>
        </div>
        
        <form id="userForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" novalidate style="<?php echo ($formProcessed === true) ? 'display: none;' : ''; ?>">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($registration->getCSRFToken()); ?>">
            <input type="hidden" name="confirmed" id="confirmedInput" value="false">
            
            <div class="form-grid">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input 
                                type="text" 
                                id="firstName" 
                                name="firstName" 
                                required 
                                autocomplete="given-name"
                                value="<?php echo htmlspecialchars($registration->getFieldValue('firstName')); ?>"
                                class="<?php echo $registration->hasFieldError('firstName') ? 'input-error' : ''; ?>"
                            >
                            <span class="input-icon">👤</span>
                        </div>
                        <div class="error-message" id="firstNameError" style="<?php echo $registration->hasFieldError('firstName') ? 'display: block;' : 'display: none;'; ?>">
                            <?php echo $registration->hasFieldError('firstName') ? htmlspecialchars($registration->getFieldError('firstName')) : ''; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lastName">Last Name <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input 
                                type="text" 
                                id="lastName" 
                                name="lastName" 
                                required 
                                autocomplete="family-name"
                                value="<?php echo htmlspecialchars($registration->getFieldValue('lastName')); ?>"
                                class="<?php echo $registration->hasFieldError('lastName') ? 'input-error' : ''; ?>"
                            >
                            <span class="input-icon">👤</span>
                        </div>
                        <div class="error-message" id="lastNameError" style="<?php echo $registration->hasFieldError('lastName') ? 'display: block;' : 'display: none;'; ?>">
                            <?php echo $registration->hasFieldError('lastName') ? htmlspecialchars($registration->getFieldError('lastName')) : ''; ?>
                        </div>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required 
                            autocomplete="email"
                            value="<?php echo htmlspecialchars($registration->getFieldValue('email')); ?>"
                            class="<?php echo $registration->hasFieldError('email') ? 'input-error' : ''; ?>"
                        >
                        <span class="input-icon">📧</span>
                    </div>
                    <div class="error-message" id="emailError" style="<?php echo $registration->hasFieldError('email') ? 'display: block;' : 'display: none;'; ?>">
                        <?php echo $registration->hasFieldError('email') ? htmlspecialchars($registration->getFieldError('email')) : ''; ?>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="accountNumber">Account Number <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input 
                            type="text" 
                            id="accountNumber" 
                            name="accountNumber" 
                            maxlength="12" 
                            required 
                            placeholder="Enter 12-character alphanumeric code"
                            value="<?php echo htmlspecialchars($registration->getFieldValue('accountNumber')); ?>"
                            class="<?php echo $registration->hasFieldError('accountNumber') ? 'input-error' : ''; ?>"
                        >
                        <span class="input-icon">🔢</span>
                    </div>
                    <div class="error-message" id="accountNumberError" style="<?php echo $registration->hasFieldError('accountNumber') ? 'display: block;' : 'display: none;'; ?>">
                        <?php echo $registration->hasFieldError('accountNumber') ? htmlspecialchars($registration->getFieldError('accountNumber')) : ''; ?>
                    </div>
                    <div class="account-preview" id="accountPreview">
                        Account number will appear here
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="year">Year <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select 
                            id="year" 
                            name="year" 
                            required
                            class="<?php echo $registration->hasFieldError('year') ? 'input-error' : ''; ?>"
                        >
                            <option value="">Select a year</option>
                            <?php 
                            $years = ['2025', '2024', '2023', '2022', '2021'];
                            $selectedYear = $registration->getFieldValue('year');
                            foreach ($years as $year): 
                            ?>
                                <option value="<?php echo htmlspecialchars($year); ?>" <?php echo ($selectedYear === $year) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($year); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="input-icon">📅</span>
                    </div>
                    <div class="error-message" id="yearError" style="<?php echo $registration->hasFieldError('year') ? 'display: block;' : 'display: none;'; ?>">
                        <?php echo $registration->hasFieldError('year') ? htmlspecialchars($registration->getFieldError('year')) : ''; ?>
                    </div>
                </div>

                <button type="submit" class="submit-button">
                    Submit
                </button>
            </div>
        </form>
    </div>

    <!-- Verification Modal -->
    <div id="verificationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>🔍 Verify Your Information</h2>
                <p>Please confirm that all details are accurate and truthful</p>
            </div>
            <div class="modal-body">
                <div class="warning-notice">
                    <span class="icon">⚠️</span>
                    <strong>Important Notice:</strong> By confirming, you verify that all information provided is truthful and accurate. False information may result in registration rejection.
                </div>

                <div class="verification-data" id="verificationData">
                    <!-- Populated by JavaScript -->
                </div>

                <div class="loading" id="loadingSpinner"></div>

                <div class="modal-actions">
                    <button class="modal-btn btn-cancel" type="button" onclick="closeVerificationModal()">
                        Cancel
                    </button>
                    <button class="modal-btn btn-confirm" type="button" onclick="confirmSubmission()">
                        Confirm & Submit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>