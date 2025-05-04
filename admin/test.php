<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
include "../Common.php";
$common = new Common();
$data = $common->get_all_users_with_balance();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f6f6f6;
            margin: 0;
            padding: 20px;
        }
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }
        .filter-bar div {
            display: flex;
            flex-direction: column;
        }
        .filter-bar input[type="text"],
        .filter-bar select {
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .filter-bar label {
            font-size: 13px;
            margin-bottom: 4px;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            padding: 15px;
            margin-bottom: 15px;
        }
        .card h3 {
            margin: 0 0 5px;
        }
        .card small {
            color: gray;
        }
        .totals {
            margin-top: 20px;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<h2>User Dashboard</h2>
<div class="totals">
    <h3>Totals</h3>
    <p>Total Users: <span id="totalUsers">0</span></p>
    <p>Total Balance: ₹<span id="totalBalance">0</span></p>
    <p>Total Withdrawn Balance: ₹<span id="totalWithdrawBalance">0</span></p>
</div>
<div class="filter-bar">
    <div>
        <label for="nameFilter">Search by Name:</label>
        <input type="text" id="nameFilter" placeholder="Enter name..." onkeyup="filterCards()">
    </div>
    <div>
        <label for="statusFilter">Status:</label>
        <select id="statusFilter" onchange="filterCards()">
            <option value="all">All</option>
            <option value="active">Active</option>
            <option value="pending">Pending</option>
        </select>
    </div>
    <div>
        <label>
            <input type="checkbox" id="recentLoginFilter" onchange="filterCards()">
            Last 48 Hours
        </label>
    </div>
    <div>
        <label>
            <input type="checkbox" id="securedFilter" onchange="filterCards()">
            Secured Only
        </label>
    </div>
    <div>
        <label>
            <input type="checkbox" id="onlineFilter" onchange="filterCards()">
            Online (Last 60 min)
        </label>
    </div>
    <div>
        <label for="sortFilter">Sort by:</label>
        <select id="sortFilter" onchange="filterCards()">
            <option value="last_login">Last Login (Newest)</option>
            <option value="balance">Balance (High to Low)</option>
        </select>
    </div>
</div>

<div id="userContainer">
    <?php
    foreach ($data as $user) {
        $fullName = $user->fname . ' ' . $user->lname;
        $status = strtolower($user->status);
        $lastLogin = isset($user->last_login) && (is_countable($user->last_login) && count($user->last_login) > 0)
            ? end($user->last_login) : ($user->last_login ?? null);
        $lastLoginTimestamp = $lastLogin ? strtotime(str_replace(",", "", $lastLogin)) : 0;
        $balance = (int)$user->balance;
        $withdrawBalance = (int)$user->withdraw_balance;
        $isSecured = isset($user->secured) && $user->secured === true ? 'true' : 'false';

        echo "<div class='card'
        data-status='$status'
        data-name='" . strtolower(htmlspecialchars($fullName)) . "'
        data-lastlogin='$lastLoginTimestamp'
        data-secured='$isSecured'
        data-balance='$balance'
        data-withdrawbalance='$withdrawBalance'>
        <h3>$fullName</h3>
        <p><strong>Phone:</strong> {$user->phone}</p>
        <p><strong>Status:</strong> {$user->status}</p>
        <p><strong>Balance:</strong> ₹{$balance}</p>
        <p><strong>Secured:</strong> " . ($isSecured === 'true' ? 'Yes' : 'No') . "</p>
        <small>Last Login: $lastLogin</small>
    </div>";
    }
    ?>
</div>

<script>
    function filterCards() {
        const statusValue = document.getElementById('statusFilter').value.toLowerCase();
        const nameValue = document.getElementById('nameFilter').value.toLowerCase();
        const recentOnly = document.getElementById('recentLoginFilter').checked;
        const securedOnly = document.getElementById('securedFilter').checked;
        const onlineOnly = document.getElementById('onlineFilter').checked;
        const sortBy = document.getElementById('sortFilter').value;

        const now = Date.now();
        const hours48 = 48 * 60 * 60 * 1000;
        const minutes60 = 60 * 60 * 1000;

        const container = document.getElementById('userContainer');
        const cards = Array.from(container.querySelectorAll('.card'));

        let totalUsers = 0;
        let totalBalance = 0;
        let totalWithdrawBalance = 0;

        const filtered = cards.filter(card => {
            const status = card.getAttribute('data-status');
            const name = card.getAttribute('data-name');
            const lastLogin = parseInt(card.getAttribute('data-lastlogin')) * 1000;
            const isSecured = card.getAttribute('data-secured') === 'true';
            const balance = parseInt(card.getAttribute('data-balance'));
            const withdrawBalance = parseInt(card.getAttribute('data-withdrawbalance'));

            const statusMatch = (statusValue === 'all' || status === statusValue);
            const nameMatch = name.includes(nameValue);
            const recentMatch = !recentOnly || (now - lastLogin <= hours48);
            const securedMatch = !securedOnly || isSecured;
            const onlineMatch = !onlineOnly || (now - lastLogin <= minutes60);

            const match = statusMatch && nameMatch && recentMatch && securedMatch && onlineMatch;

            if (match) {
                totalUsers++;
                totalBalance += balance;
                totalWithdrawBalance += withdrawBalance;
            }

            return match;
        });

        filtered.sort((a, b) => {
            if (sortBy === 'balance') {
                return parseInt(b.getAttribute('data-balance')) - parseInt(a.getAttribute('data-balance'));
            } else {
                return parseInt(b.getAttribute('data-lastlogin')) - parseInt(a.getAttribute('data-lastlogin'));
            }
        });

        container.innerHTML = '';
        filtered.forEach(card => container.appendChild(card));

        // Update totals
        document.getElementById('totalUsers').textContent = totalUsers;
        document.getElementById('totalBalance').textContent = totalBalance;
        document.getElementById('totalWithdrawBalance').textContent = totalWithdrawBalance;
    }

    window.addEventListener('DOMContentLoaded', filterCards);
</script>

</body>
</html>
