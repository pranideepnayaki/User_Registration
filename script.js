/**
 * User Registration Form Manager
 *
 * Handles form validation, modal interactions, and SPA behavior
 *
 * @author Your Name
 * @version 2.0
 */

class FormManager {
  constructor() {
    this.form = document.getElementById("userForm");
    this.modal = document.getElementById("verificationModal");
    this.validationRules = {
      firstName: {
        required: true,
        pattern: /^[A-Za-z\s\'-]{1,50}$/,
        message:
          "First name must contain only letters, spaces, hyphens, or apostrophes and be 1-50 characters long",
      },
      lastName: {
        required: true,
        pattern: /^[A-Za-z\s\'-]{1,50}$/,
        message:
          "Last name must contain only letters, spaces, hyphens, or apostrophes and be 1-50 characters long",
      },
      email: {
        required: true,
        pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        message: "Please enter a valid email address",
      },
      accountNumber: {
        required: true,
        pattern: /^[A-Za-z0-9]{12}$/,
        message: "Account number must be exactly 12 alphanumeric characters",
      },
      year: {
        required: true,
        message: "Please select a year",
      },
    };
    this.init();
  }

  /**
   * Initialize the form manager
   */
  init() {
    this.setupEventListeners();
    this.setupRealTimeValidation();
    // Update account preview if there's an existing value
    const accountInput = document.getElementById("accountNumber");
    if (accountInput && accountInput.value) {
      this.updateAccountPreview(accountInput.value);
    }
  }

  /**
   * Set up event listeners
   */
  setupEventListeners() {
    // Form submission
    this.form.addEventListener("submit", (e) => {
      e.preventDefault();
      this.handleSubmit();
    });

    // Account number formatting
    const accountInput = document.getElementById("accountNumber");
    accountInput.addEventListener("input", (e) => {
      this.formatAccountNumber(e.target);
    });

    // Modal close on outside click
    this.modal.addEventListener("click", (e) => {
      if (e.target === this.modal) {
        this.closeVerificationModal();
      }
    });

    // Escape key to close modal
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && this.modal.style.display === "block") {
        this.closeVerificationModal();
      }
    });
  }

  /**
   * Set up real-time validation
   */
  setupRealTimeValidation() {
    const inputs = this.form.querySelectorAll(
      "input[required], select[required]"
    );
    inputs.forEach((input) => {
      input.addEventListener("blur", () => this.validateField(input));
      input.addEventListener("input", () => this.clearFieldError(input));
    });
  }

  /**
   * Validate individual field
   */
  validateField(field) {
    const fieldName = field.name;
    const value = field.value.trim();
    const rules = this.validationRules[fieldName];

    if (!rules) return true;

    let isValid = true;
    let errorMessage = "";

    // Required field validation
    if (rules.required && !value) {
      isValid = false;
      errorMessage = `${this.getFieldDisplayName(fieldName)} is required`;
    }
    // Pattern validation
    else if (value && rules.pattern && !rules.pattern.test(value)) {
      isValid = false;
      errorMessage = rules.message;
    }

    this.updateFieldStatus(field, isValid, errorMessage);
    return isValid;
  }

  /**
   * Update field validation status
   */
  updateFieldStatus(field, isValid, errorMessage) {
    const errorElement = document.getElementById(field.name + "Error");

    if (isValid) {
      field.classList.remove("input-error");
      field.classList.add("input-valid");
      if (errorElement) errorElement.style.display = "none";
    } else {
      field.classList.remove("input-valid");
      field.classList.add("input-error");
      if (errorElement) {
        errorElement.textContent = errorMessage;
        errorElement.style.display = "block";
      }
    }
  }

  /**
   * Clear field error state
   */
  clearFieldError(field) {
    field.classList.remove("input-error");
    const errorElement = document.getElementById(field.name + "Error");
    if (errorElement) errorElement.style.display = "none";
  }

  /**
   * Get display name for field
   */
  getFieldDisplayName(fieldName) {
    const displayNames = {
      firstName: "First Name",
      lastName: "Last Name",
      email: "Email Address",
      accountNumber: "Account Number",
      year: "Year",
    };
    return displayNames[fieldName] || fieldName;
  }

  /**
   * Format account number input
   */
  formatAccountNumber(input) {
    // Convert to uppercase and remove non-alphanumeric characters
    let value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, "");
    input.value = value;

    // Update preview
    this.updateAccountPreview(value);
  }

  /**
   * Update account number preview
   */
  updateAccountPreview(value) {
    const preview = document.getElementById("accountPreview");
    if (value.length > 0) {
      // Format with dashes for better readability
      const formatted = value.match(/.{1,4}/g)?.join("-") || value;
      preview.textContent = formatted;
      preview.classList.add("has-value");
    } else {
      preview.textContent = "Account number will appear here";
      preview.classList.remove("has-value");
    }
  }

  /**
   * Validate entire form
   */
  validateForm() {
    const inputs = this.form.querySelectorAll(
      "input[required], select[required]"
    );
    let isFormValid = true;

    inputs.forEach((input) => {
      if (!this.validateField(input)) {
        isFormValid = false;
      }
    });

    return isFormValid;
  }

  /**
   * Handle form submission
   */
  handleSubmit() {
    console.log("Form submitted!");
    if (this.validateForm()) {
      console.log("Form is valid, showing verification modal...");
      this.showVerificationModal();
    } else {
      console.log("Form is invalid, focusing first error...");
      // Focus on first invalid field
      const firstError = this.form.querySelector(".input-error");
      if (firstError) {
        firstError.focus();
      }
    }
  }

  /**
   * Show verification modal
   */
  showVerificationModal() {
    const formData = new FormData(this.form);
    const data = Object.fromEntries(formData);

    console.log("Showing verification modal with data:", data);

    // Generate verification display
    const verificationHtml = `
            <div class="verification-item">
                <label>First Name:</label>
                <span class="value">${data.firstName}</span>
            </div>
            <div class="verification-item">
                <label>Last Name:</label>
                <span class="value">${data.lastName}</span>
            </div>
            <div class="verification-item">
                <label>Email Address:</label>
                <span class="value">${data.email}</span>
            </div>
            <div class="verification-item">
                <label>Account Number:</label>
                <span class="value">${data.accountNumber}</span>
            </div>
            <div class="verification-item">
                <label>Year:</label>
                <span class="value">${data.year}</span>
            </div>
        `;

    document.getElementById("verificationData").innerHTML = verificationHtml;
    this.modal.style.display = "block";

    // Prevent body scroll when modal is open
    document.body.style.overflow = "hidden";

    // Focus on the modal for accessibility
    this.modal.focus();
  }

  /**
   * Close verification modal
   */
  closeVerificationModal() {
    console.log("Closing verification modal");
    this.modal.style.display = "none";
    // Restore body scroll
    document.body.style.overflow = "auto";
  }

  /**
   * Confirm submission
   */
  confirmSubmission() {
    console.log("Confirming submission...");
    // Show loading spinner
    const loadingSpinner = document.getElementById("loadingSpinner");
    loadingSpinner.style.display = "block";

    // Set confirmed flag
    document.getElementById("confirmedInput").value = "true";

    // Simulate form processing delay, then submit
    setTimeout(() => {
      this.processFormSubmission();
    }, 0);
  }

  /**
   * Process form submission
   */
  processFormSubmission() {
    console.log("Processing form submission...");

    // Log form data for debugging
    const formData = new FormData(this.form);
    const data = Object.fromEntries(formData);

    console.log("Form Data Ready for POST Submission:", data);
    console.log("Form Method:", this.form.method);
    console.log("Form Action:", this.form.action);

    // Submit the form to PHP
    this.form.submit();
  }

  /**
   * Reset form to initial state
   */
  resetForm() {
    // Reset the form to its initial state
    this.form.reset();

    // Clear all validation states
    const inputs = this.form.querySelectorAll("input, select");
    inputs.forEach((input) => {
      input.classList.remove("input-valid", "input-error");
    });

    // Clear all error messages
    const errorElements = this.form.querySelectorAll(".error-message");
    errorElements.forEach((element) => {
      element.style.display = "none";
      element.textContent = "";
    });

    // Reset account preview
    this.updateAccountPreview("");

    // Generate new CSRF token
    this.generateNewCSRFToken();
  }

  /**
   * Generate new CSRF token (placeholder for real implementation)
   */
  generateNewCSRFToken() {
    // In a real SPA, you'd make an AJAX call to get a new token
    // For now, we'll keep the existing one
    console.log("CSRF token maintained for SPA");
  }
}

/**
 * Global function to reset to fresh form (for SPA behavior)
 */
function resetToFreshForm() {
  if (window.formManager) {
    // Hide both success messages
    const successMessage = document.getElementById("successMessage");
    if (successMessage) {
      successMessage.style.display = "none";
    }

    const spaSuccessMessage = document.getElementById("spaSuccessMessage");
    if (spaSuccessMessage) {
      spaSuccessMessage.style.display = "none";
    }

    // Show form again
    const form = document.getElementById("userForm");
    if (form) {
      form.style.display = "block";
    }

    // Show and update header text
    const headerSubtext = document.getElementById("headerSubtext");
    if (headerSubtext) {
      headerSubtext.style.display = "block";
      headerSubtext.textContent = "Please fill out all required fields";
    }

    // Reset form to clean state
    window.formManager.resetForm();

    console.log("Form reset to fresh state - True SPA behavior");
  }
}

/**
 * Global functions for modal actions
 */
function closeVerificationModal() {
  if (window.formManager) {
    window.formManager.closeVerificationModal();
  }
}

function confirmSubmission() {
  if (window.formManager) {
    window.formManager.confirmSubmission();
  }
}

/**
 * Initialize the form manager when DOM is loaded
 */
document.addEventListener("DOMContentLoaded", () => {
  console.log("DOM loaded, initializing form manager...");
  window.formManager = new FormManager();
});
