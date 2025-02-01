<?php
include "Common.php";
$common = new Common();
if($common->is_user_logged_in()){
    $common->redirect_to('Cricket/home/');
}else{
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cricket World Hub</title>

    <!-- CSS Styles -->
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        /* Body */
        body {
            background-color: #f5f5f5;
            font-family: 'Roboto', sans-serif;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            background-color: #2c3e50;
            color: white;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        header .logo img {
            max-width: 150px;
        }

        header nav ul {
            list-style: none;
            display: flex;
        }

        header nav ul li {
            margin-right: 20px;
        }

        header nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        header nav ul li a:hover {
            color: #f39c12;
        }

        header .login-btn {
            padding: 10px 20px;
            background-color: #f39c12;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        header .login-btn:hover {
            background-color: #e67e22;
        }

        /* Hero Section */
        .hero {
            background-color:grey;
            height: 400px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        .hero-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 1rem;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }

        .cta-btn {
            background-color: #f39c12;
            color: white;
            padding: 8px 15px;
            margin: 1rem;
            text-decoration: none;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .cta-btn:hover {
            background-color: #e67e22;
        }


        /* Footer Section */
        footer {
            background-color: #2c3e50;
            color: white;
            padding: 30px 0;
            text-align: center;
        }

        footer .footer-links ul {
            list-style: none;
            margin-bottom: 20px;
        }

        footer .footer-links ul li {
            display: inline-block;
            margin-right: 20px;
        }

        footer .footer-links ul li a {
            color: white;
            text-decoration: none;
        }

        footer .social-media a {
            color: white;
            margin-right: 15px;
            text-decoration: none;
        }

        footer .social-media a:hover {
            color: #f39c12;
        }
    </style>
</head>
<body>

<!-- Header Section -->
<header>
    <div class="logo">
        <img src="https://via.placeholder.com/150x50?text=Cricket+World+Hub" alt="Cricket Logo">
    </div>

</header>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-overlay">
        <span style="font-size: 2rem">Welcome to Cricket T20</span>
        <div class="padding"></div>
        <a href="login/index.php" class="cta-btn">Login</a>
    </div>
</section>



<!-- Footer Section -->
<footer>
    <div class="footer-links">
        <ul>
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Terms of Service</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
    </div>
    <div class="social-media">
        <a href="#" target="_blank">Facebook</a>
        <a href="#" target="_blank">Twitter</a>
        <a href="#" target="_blank">Instagram</a>
    </div>
</footer>

</body>
</html>
<?php } ?>