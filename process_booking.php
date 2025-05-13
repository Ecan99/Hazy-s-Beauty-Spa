<?php
// Initialize response array
$response = array(
    'success' => false,
    'message' => ''
);

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Collect form data and sanitize inputs
    $fullName = filter_input(INPUT_POST, 'fullName', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $service = filter_input(INPUT_POST, 'service', FILTER_SANITIZE_STRING);
    $preferredDate = filter_input(INPUT_POST, 'preferredDate', FILTER_SANITIZE_STRING);
    $preferredTime = filter_input(INPUT_POST, 'preferredTime', FILTER_SANITIZE_STRING);
    $specialRequests = filter_input(INPUT_POST, 'specialRequests', FILTER_SANITIZE_STRING);
    
    // Validate required fields
    $errors = array();
    
    if (empty($fullName)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email address is required";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }
    
    if (empty($service)) {
        $errors[] = "Service selection is required";
    }
    
    if (empty($preferredDate)) {
        $errors[] = "Preferred date is required";
    }
    
    if (empty($preferredTime)) {
        $errors[] = "Preferred time is required";
    }
    
    // If there are no validation errors, proceed with sending email
    if (empty($errors)) {
        try {
            // Your email address where you want to receive bookings
            $to = "mphoecan95@gmail.com";
            $subject = "New Spa Booking: $service";
            
            // Create email message with booking details
            $message = "
            <html>
            <head>
                <title>New Booking</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .booking-details { background-color: #f5f5f5; padding: 15px; border-radius: 5px; }
                    h2 { color: #00bcd4; }
                </style>
            </head>
            <body>
                <h2>New Booking Received</h2>
                <div class='booking-details'>
                    <p><strong>Client:</strong> $fullName</p>
                    <p><strong>Email:</strong> $email</p>
                    <p><strong>Phone:</strong> $phone</p>
                    <p><strong>Service:</strong> $service</p>
                    <p><strong>Date:</strong> $preferredDate</p>
                    <p><strong>Time:</strong> $preferredTime</p>
                    <p><strong>Special Requests:</strong> $specialRequests</p>
                    <p><strong>Booking Time:</strong> " . date('Y-m-d H:i:s') . "</p>
                </div>
            </body>
            </html>
            ";
            
            // Set email headers
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: Hazy\'s Beauty & Spa Website <noreply@hazybeauty.com>' . "\r\n";
            $headers .= "Reply-To: $email" . "\r\n";
            
            // Send email
            if(mail($to, $subject, $message, $headers)) {
                // Also send confirmation to the client
                $clientSubject = "Booking Confirmation - Hazy's Beauty & Spa";
                $clientMessage = "
                <html>
                <head>
                    <title>Booking Confirmation</title>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .booking-details { background-color: #f5f5f5; padding: 15px; border-radius: 5px; }
                        h2 { color: #00bcd4; }
                    </style>
                </head>
                <body>
                    <h2>Thank you for booking with Hazy's Beauty & Spa!</h2>
                    <p>Dear $fullName,</p>
                    <p>Your appointment has been successfully booked with the following details:</p>
                    <div class='booking-details'>
                        <p><strong>Service:</strong> $service</p>
                        <p><strong>Date:</strong> $preferredDate</p>
                        <p><strong>Time:</strong> $preferredTime</p>
                    </div>
                    <p>If you need to cancel or reschedule, please do so at least 24 hours in advance to avoid cancellation fees.</p>
                    <p>We look forward to seeing you soon!</p>
                    <p>Best regards,<br>
                    Hazy's Beauty & Spa Team<br>
                    078 425 7651</p>
                </body>
                </html>
                ";
                
                mail($email, $clientSubject, $clientMessage, $headers);
                
                $response['success'] = true;
                $response['message'] = "Your appointment has been successfully booked! Check your email for confirmation details.";
            } else {
                $response['message'] = "There was a problem sending the booking. Please try again or contact us directly.";
            }
            
        } catch(Exception $e) {
            $response['message'] = "Error: " . $e->getMessage();
        }
        
    } else {
        // If there are validation errors, return them
        $response['message'] = "Please correct the following errors: <br>" . implode("<br>", $errors);
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>