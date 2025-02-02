<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moving Text</title>
    <style>
        @keyframes moveText {
            0% { transform: translateX(0); }
            50% { transform: translateX(100px); } /* Adjust distance as needed */
            100% { transform: translateX(0); }
        }

        .moving-text {
            font-size: 24px;
            font-weight: bold;
            display: inline-block;
            animation: moveText 2s linear infinite; /* Adjust duration as needed */
        }
    </style>
</head>
<body>
<div class="moving-text">Moving Text</div>
</body>
</html>
