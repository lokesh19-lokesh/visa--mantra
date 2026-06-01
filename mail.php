<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize direct input fields
    $form_source = isset($_POST['form_source']) ? htmlspecialchars(strip_tags(trim($_POST['form_source']))) : 'unknown';
    $name = isset($_POST['name']) ? htmlspecialchars(strip_tags(trim($_POST['name']))) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(strip_tags(trim($_POST['phone']))) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $message = isset($_POST['message']) ? htmlspecialchars(strip_tags(trim($_POST['message']))) : '';
    
    $sector = isset($_POST['sector']) ? htmlspecialchars(strip_tags(trim($_POST['sector']))) : '';
    $destination = isset($_POST['destination']) ? htmlspecialchars(strip_tags(trim($_POST['destination']))) : '';
    
    $applied_position = isset($_POST['applied_position']) ? htmlspecialchars(strip_tags(trim($_POST['applied_position']))) : '';
    $applied_destination = isset($_POST['applied_destination']) ? htmlspecialchars(strip_tags(trim($_POST['applied_destination']))) : '';
    $experience = isset($_POST['experience']) ? htmlspecialchars(strip_tags(trim($_POST['experience']))) : '';

    // Validate inputs
    if (empty($name) || empty($phone) || empty($email)) {
        echo "Required fields are missing. Please go back and try again.";
        exit;
    }

    // 2. Setup Email Parameters
    $to = "info@visamantra.in"; // Primary contact mailbox
    $subject = "The Visa Mantra Website Lead - " . ucwords(str_replace('_', ' ', $form_source));
    
    // Create random boundary for multi-part email (required for attachments)
    $semi_rand = md5(time());
    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

    // Set email headers
    $headers = "From: Careers The Visa Mantra <no-reply@visamantra.in>\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"{$mime_boundary}\"\r\n";

    // 3. Construct HTML email body
    $email_content = "--{$mime_boundary}\r\n";
    $email_content .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
    $email_content .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    
    $html_body = "
    <html>
    <head>
        <title>New Career Inquiry</title>
        <style>
            body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; margin: 0; padding: 20px; }
            .container { background-color: #ffffff; padding: 30px; border-radius: 8px; border-top: 4px solid #e53935; }
            h2 { color: #1b1212; margin-top: 0; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
            th { background-color: #f8fafc; color: #1e293b; width: 30%; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>New Recruitment Inquiry Received</h2>
            <p>A user has submitted an inquiry form from the The Visa Mantra website details below:</p>
            <table>
                <tr><th>Form Section</th><td>" . ucwords(str_replace('_', ' ', $form_source)) . "</td></tr>
                <tr><th>Full Name</th><td>{$name}</td></tr>
                <tr><th>Phone Number</th><td>{$phone}</td></tr>
                <tr><th>Email Address</th><td>{$email}</td></tr>";

    if (!empty($sector)) {
        $html_body .= "<tr><th>Target Industry</th><td>{$sector}</td></tr>";
    }
    if (!empty($destination)) {
        $html_body .= "<tr><th>Target Destination</th><td>{$destination}</td></tr>";
    }
    if (!empty($applied_position)) {
        $html_body .= "<tr><th>Applied Job</th><td>{$applied_position}</td></tr>";
        $html_body .= "<tr><th>Job Country</th><td>{$applied_destination}</td></tr>";
        $html_body .= "<tr><th>Experience Outline</th><td>{$experience}</td></tr>";
    }

    $html_body .= "
                <tr><th>Message / Profile</th><td>" . nl2br($message) . "</td></tr>
            </table>
        </div>
    </body>
    </html>";

    $email_content .= $html_body . "\r\n\r\n";

    // 4. Handle Resume / File Attachment if exists
    if ($form_source === 'job_application' && isset($_FILES['resume']) && $_FILES['resume']['error'] == UPLOAD_ERR_OK) {
        $file_name = basename($_FILES['resume']['name']);
        $file_temp = $_FILES['resume']['tmp_name'];
        $file_type = $_FILES['resume']['type'];
        $file_size = $_FILES['resume']['size'];

        // Read file contents
        $handle = fopen($file_temp, "r");
        $content = fread($handle, $file_size);
        fclose($handle);
        $encoded_content = chunk_split(base64_encode($content));

        $email_content .= "--{$mime_boundary}\r\n";
        $email_content .= "Content-Type: {$file_type}; name=\"{$file_name}\"\r\n";
        $email_content .= "Content-Disposition: attachment; filename=\"{$file_name}\"\r\n";
        $email_content .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $email_content .= $encoded_content . "\r\n\r\n";
    }

    $email_content .= "--{$mime_boundary}--";

    // 5. Deliver Email
    $mail_success = @mail($to, $subject, $email_content, $headers);
    
    // 6. Renders themed successful submission screen
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Submission Confirmed | The Visa Mantra</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/style.css">
        <style>
            body {
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
            }
            .success-card {
                background: var(--card-bg);
                border: 1px solid rgba(229, 57, 53, 0.25);
                border-radius: 8px;
                padding: 50px;
                box-shadow: 0 15px 40px rgba(0,0,0,0.5);
                max-width: 600px;
            }
            .success-icon {
                font-size: 4.5rem;
                color: var(--accent-gold);
                margin-bottom: 25px;
            }
        </style>
    </head>
    <body>
        <div class="container d-flex justify-content-center">
            <div class="success-card">
                <div class="success-icon">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h1 class="fw-bold mb-3" style="color: var(--accent-gold);">Thank You!</h1>
                <h3 class="mb-3">Inquiry Submitted Successfully</h3>
                <p class="text-muted mb-4 fs-6">We have received your career details. One of our lead recruitment managers based in Hyderabad will verify your credentials and contact you within 24 business hours.</p>
                <a href="index.html" class="btn btn-gold px-4 py-3">Back to Homepage</a>
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    </body>
    </html>
    <?php
} else {
    header("Location: index.html");
    exit;
}
?>
