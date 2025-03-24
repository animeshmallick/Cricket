<html lang="en">
<head>
    <title>Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="../styles/style.css?version=<?php echo time(); ?>">
    <link rel="stylesheet" type="text/css" href="style.css?version=<?php echo time(); ?>">
    <link rel="icon" type="image/x-icon" href="../cricket.ico">
    <script src="../scripts/script.js?version=<?php echo time(); ?>"></script>
</head>
<?php
include "../Common.php";
$common = new Common();
if ($_SERVER['REQUEST_METHOD'] === 'GET' &&
    !$common->is_user_logged_in()) { ?>
        <body>
        <div id="header"></div>
        <div class="main_container">
            <div class="sub-title">Register</div>
            <p class="error" id="msg"><?php if(isset($_GET['msg'])) { echo $_GET['msg']; } ?></p>
            <form action="index.php" method="POST" onsubmit="return validate_register_form()" name="register_form">
                <label class="label" for="fname">First Name:</label>
                <input type="text" id="fname" name="fname" placeholder="Your First Name" required>
                <label class="label" for="lname">Last Name:</label>
                <input type="text" id="lname" name="lname" placeholder="Your Last Name">
                <label class="label" for="phone">Phone Number:</label>
                <input type="number" id="phone" name="phone" placeholder="Enter 10 digit phone number" required>
                <label class="label" for="password">Create New Password:</label>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <label class="label" for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                <input type="number" id="ref_id" name="ref_id" value="<?php echo get_unique_ref_id($common); ?>" readonly required hidden="hidden">
                <input type="submit" class="button" value="Register">
            </form>
            <a class="button" href="../index.php">Go Home</a>
        </div>
        <div class="gap"></div>
        <div class="separator"></div>
        <div id="footer"></div>
        </body>
<?php }

else if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$common->is_user_logged_in()){
    if (!$common->is_new_phone_number($_POST['phone'])){
        header('Location: index.php?msg=Phone%20Number%20Already%20Registered');
    } else {
        $ref_id = $_POST['ref_id'];
        if ($common->insert_new_user($_POST['fname'], $_POST['lname'], $_POST['phone'], $_POST['password'],
            $ref_id, 'pending')) {
            $common->set_cookie('user_ref_id', $ref_id);
            $common->set_cookie('user_type', 'pending');
            $common->set_cookie('fname', $_POST['fname']);
            $common->set_cookie('lname', $_POST['lname']);
            ?>
            <body>
            <div class="container">
                <h1>Attention Required</h1>
                <div class="separator"></div>
                <h2 style="color: #1cb604">OTP : <?= $ref_id ?></h2>
                <p style="color:red;font-size: 1.5rem" id="message">
                    To activate your account send a Whatsapp/Text Message as "ACTIVATE <?php echo $ref_id; ?>" to +91 (9153217256) from your registered mobile number (<?php echo $_POST['phone']; ?>).
                </p>
                <div class="separator"></div>
                <div class="register-link">
                    <button class="button"><a href="../index.php" class="link">Logout/Home</a></button>
                </div>
            </div>
            </body>
            <?php
        } else {
            header('Location: index.php?msg=Please%20try%20again');
        }
    }
} else {
    header("Location: ../index.php");
}

function get_unique_ref_id(Common $common): int
{
    for ($i = 0; $i < 100; $i++) {
        $ref_id = mt_rand(10000000, 99999999);
        if($common->validate_unique_ref_id($ref_id))
            return $ref_id;
    }
    return -1;
}

?>
</html>