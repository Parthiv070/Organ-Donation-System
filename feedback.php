<?php
// ✅ PHPMailer includes (no "use" needed because not using Composer)
require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';
require_once 'PHPMailer/Exception.php';

include_once('helper/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $last = $_POST["last"];
    $email = $_POST["email"];
    $message = $_POST["message"];
    $userid = $_GET['userid'];

    if (empty($name) || empty($last) || empty($email) || empty($message)) {
        echo "Please fill in all the fields.";
    } else {
        // ✅ Save to MySQL
        $stmt = $mysqli->prepare("INSERT INTO feedback (first_name, last_name, email, message, feedback_user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $name, $last, $email, $message, $userid);

        if ($stmt->execute()) {
            // ✅ Send email using PHPMailer
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'parthivchudasama5@gmail.com';      // 🔁 Your Gmail address
                $mail->Password   = 'dlsh dnqf iljc sdqi';         // 🔁 Gmail App Password
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom($mail->Username, 'Organ Donation Website');
                $mail->addAddress('parthivchudasama5@gmail.com'); // 🔁 Your receiving email

                $mail->isHTML(false);
                $mail->Subject = "New Feedback from $name $last";
                $mail->Body    = "You've received a new feedback:\n\n"
                                . "Name: $name $last\n"
                                . "Email: $email\n"
                                . "User ID: $userid\n"
                                . "Message:\n$message";

                $mail->send();
                // echo "Email sent!";
            } catch (Exception $e) {
                echo "Mailer Error: " . $mail->ErrorInfo;
            }

            // Redirect after success
            header("Location: home.php?userid=" . $userid);
            exit();
        } else {
            echo "Database Error: " . $stmt->error;
        }

        $stmt->close();
        $mysqli->close();
    }
}
// $mail->Password   = 'dlsh dnqf iljc sdqi';           // ✅ App Password
include_once('helper/header.php');
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Feedback Form</title>
<link rel="stylesheet" href="static/form.css">

</head>
<br><br>
<br>
<div class="container">
    <form method="POST" action="feedback.php">
        <legend>Feedback Form</legend>
        <div class="form-row">
            <div class="input-data">
                <input type="text" id="name" name="name" required>
                <div class="underline"></div>
                <label for="name">First Name:</label><br>
            </div>
            <div class="input-data">
                <input type="text" id="last" name="last" required>
                <div class="underline"></div>
                <label for="last">Last Name:</label><br>
            </div>
        </div>
        <div class="form-row">
            <div class="input-data">
                <input type="text" id="email" name="email" required>
                <div class="underline"></div>
                <label for="email">Email:</label><br>
            </div>
        </div>
        <div class="form-row">
            <div class="input-data textarea">
                <textarea rows="8" cols="50" id="message" name="message" required></textarea>
                <br />
                <div class="underline"></div>
                <label for="message">Message:</label><br>
                <br>
                <div class="form-row submit-btn">
                    <div class="input-data">
                        <div class="inner"></div>
                        <input type="submit" value="Submit" class="button">
                        <?php if (isset($error_msg)) { ?>
                            <div class="error">
                                <?php echo $error_msg; ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</body>

</html>