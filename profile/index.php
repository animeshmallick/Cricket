<?php
include '../Common.php';
$common = new Common();
$user = $common->get_user_details_from_users($common->get_cookie('ref_id'));
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $common->is_user_logged_in()) {
?>
    <html lang="en">
    <head>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-BQY4C789R1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-BQY4C789R1');
        </script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>All Matches</title>
        <link rel="stylesheet" type = "text/css" href ="../model_ui/header/style.css?version=<?php echo time();?>">
        <link rel="stylesheet" type = "text/css" href ="../model_ui/footer/style.css?version=<?php echo time();?>">
        <link rel="stylesheet" type = "text/css" href ="../model_ui/scorecard/style.css?version=<?php echo time();?>">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" type = "text/css" href ="style.css?version=<?php echo time();?>">
        <link rel="stylesheet" type = "text/css" href ="../styles/style.css?version=<?php echo time();?>">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
        <script src="../model_ui/header/script.js?version=<?php echo time();?>"></script>
        <script src="script.js?version=<?php echo time();?>"></script>
        <script src="../scripts/script.js?version=<?php echo time();?>"></script>
    </head>
        <body onload="fill_header();fill_footer()">
            <div id="header"></div>
            <div id="header"></div>
            <div class="main_container">
                <div class="sub-title">Register</div>
                <p class="error" id="msg"><?php if(isset($_GET['msg'])) { echo $_GET['msg']; } ?></p>
                <form action="index.php" method="POST" onsubmit="return update_user_profile()" name="update_user_profile_form">
                    <label class="label" for="fname">First Name:</label>
                    <input type="text" id="fname" name="fname" placeholder="Your First Name" value="<?= $user->fname ?>" required>
                    <label class="label" for="lname">Last Name:</label>
                    <input type="text" id="lname" name="lname" placeholder="Your Last Name" value="<?= $user->lname ?>" required>
                    <label class="label" for="phone">Phone Number:</label>
                    <input type="number" id="phone" name="phone" placeholder="Enter 10 digit phone number" value="<?= $user->phone ?>" required readonly>
                    <label class="label" for="password">Create New Password:</label>
                    <input type="password" id="password" name="password" placeholder="Password" required value="<?= $user->password ?>">
                    <input type="submit" class="button" value="Save Changes">
                </form>
                <a class="button" href="../index.php">Go Home</a>
            </div>
            <div id="footer"></div>
        </body>
    </html>
<?php } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $common->is_user_logged_in()){
    $save_user_response = $common->update_user_profile($_POST['fname'], $_POST['lname'], $_POST['password']);
    header('Location: index.php?msg='.$save_user_response->status);
} ?>
