<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $to = "chris@cswhitney.com";  // Replace with your email
    $subject = "New Contact Form Message with Attachment";

    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $body = "Name: $name\nEmail: $email\n\nMessage:\n$message";

    $headers = "From: $email";

    // Handle file upload
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $file_tmp = $_FILES['attachment']['tmp_name'];
        $file_name = $_FILES['attachment']['name'];
        $file_size = $_FILES['attachment']['size'];
        $file_type = $_FILES['attachment']['type'];
        $file_content = file_get_contents($file_tmp);
        $encoded_content = chunk_split(base64_encode($file_content));

        $boundary = md5("random"); // define boundary with a md5 hashed value

        // Headers
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: $email\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary = $boundary\r\n\r\n";

        // Plain text
        $body = "--$boundary\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode("Name: $name\nEmail: $email\n\nMessage:\n$message"));

        // Attachment
        $body .= "--$boundary\r\n";
        $body .= "Content-Type: $file_type; name=\"$file_name\"\r\n";
        $body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= "X-Attachment-Id: ".rand(1000,99999)."\r\n\r\n";
        $body .= $encoded_content."\r\n";
        $body .= "--$boundary--";
    }

    // Send Email
    $mail_sent = mail($to, $subject, $body, $headers);

    if ($mail_sent) {
        echo "Message sent successfully!";
    } else {
        echo "Failed to send message.";
    }
} else {
    echo "Invalid Request";
}
?>
