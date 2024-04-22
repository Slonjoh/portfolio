<?php
  /**
  * Requires the "PHP Email Form" library
  * The "PHP Email Form" library is available only in the pro version of the template
  * The library should be uploaded to: vendor/php-email-form/php-email-form.php
  * For more info and help: https://bootstrapmade.com/php-email-form/
  */
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF protection: Check if the CSRF token matches
    if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] !== $_POST['csrf_token']) {
        die("CSRF validation failed. Please try again.");
    }

    // Sanitize input to prevent XSS
    function sanitize_input($data) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    // Retrieve and sanitize form data
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $subject = sanitize_input($_POST['subject']);
    $message = sanitize_input($_POST['message']);

    // Basic input validation
    if (strlen($name) < 4) {
        die("Name must be at least 4 characters.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || preg_match("/[\r\n]/", $email)) {
        die("Please enter a valid email address.");
    }

    if (strlen($subject) < 8) {
        die("Subject must be at least 8 characters.");
    }

    if (empty($message)) {
        die("Message cannot be empty.");
    }

    // Contact email address where messages will be sent
    $receiving_email_address = 'besttec1997@gmail.com'; // Change to your email address

    // Initialize PHP_Email_Form library
    if (file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php')) {
        include($php_email_form);
    } else {
        die('Unable to load the "PHP Email Form" Library!');
    }

    $contact = new PHP_Email_Form;
    $contact->ajax = true;
    $contact->to = $receiving_email_address;
    $contact->from_name = $name;
    $contact->from_email = $email;
    $contact->subject = $subject;

    // Uncomment below if you want to use SMTP with correct credentials
    /*
    $contact->smtp = array(
        'host' => 'your_smtp_host',
        'username' => 'your_smtp_username',
        'password' => 'your_smtp_password',
        'port' => '587' // or 465 for SSL
    );
    */

    // Add the form data to the email
    $contact->add_message($name, 'From');
    $contact->add_message($email, 'Email');
    $contact->add_message($message, 'Message', 10);

    // Send the email
    if ($contact->send()) {
        echo "Your message has been sent. Thank you!";
    } else {
        echo "There was an error sending your message. Please try again later.";
    }

    // Regenerate CSRF token for the next request
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
} else {
    // Generate initial CSRF token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
