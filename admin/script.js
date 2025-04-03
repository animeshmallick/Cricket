function settle_bid_all(type){
    if (window.location.hostname.includes('localhost')) {
        alert("Cannot perform action from localhost.");
    }else {
        const userResponse = prompt("Type Runs for this session.", "0");
        let runs = 0;
        if (userResponse != null && Number.isInteger(Number(userResponse))) {
            runs = Number(userResponse);
        }
        if(runs > 0) {
            document.querySelectorAll('tr').forEach((tr) => {
                let run_min = tr.getAttribute('min');
                let run_max = tr.getAttribute('max');
                let winner = runs >= run_min && runs <= run_max;
                if (tr.children[3].innerHTML.includes('placed')) {
                    let url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/settle_bid/" + tr.id + "/" + type + "/" + (winner ? "win" : "loss");
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status.includes('successfully')) {
                                tr.style.backgroundColor = data.winner ? 'green' : 'red';
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
            //location.reload();
        }else if(userResponse.toLowerCase() === "x" || userResponse.toLowerCase() === "y"){
            document.querySelectorAll('tr').forEach((tr) => {
                let winner = tr.getAttribute('winner') === userResponse.toLowerCase();
                if(tr.children[3].innerHTML.includes('placed')) {
                    let url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/settle_bid/" + tr.id + "/" + type + "/" + (winner ? "win" : "loss");
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status.includes('successfully')) {
                                tr.style.backgroundColor = data.winner ? 'green' : 'red';
                            }
                        })
                        .catch(error => console.error('Error:', error))
                }
            });
            //location.reload();
        }
        else {
            alert("Please enter a valid number.");
        }
    }
}
function settle_bid(bid_id, type, session){
    if (window.location.hostname.includes('localhost')) {
        alert("Cannot perform action from localhost.");
    }else {
        const userResponse = prompt("Type WIN or LOSS as input.", "LOSS");
        let execute = false;
        if (userResponse != null && userResponse.toLowerCase() === 'win') {
            execute = true;
        }
        if(userResponse != null && userResponse.toLowerCase() === 'loss') {
            execute = true
        }
        if(execute) {
            const xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    window.location.href = "https://www.cricketipl.in/Cricket/admin/admin_match_"+type+"_dashboard.php?session=" + session;
                }
            };
            xmlhttp.open("GET", "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/settle_bid/"+bid_id+"/" + type + "/" + userResponse.toLowerCase(), true);
            xmlhttp.send();
        }
    }
}
function fill_all_wallet_transaction_tickets() {
    fetch("https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_user_wallet_transaction_tickets/any")
        .then(response => response.json())
        .then(data => fill_transaction_ticket_content(data))
        .catch(error => console.error('Error:', error));
}
function fill_transaction_ticket_content(transactions){
    transactions.sort((a, b) => parseDate(b.timestamp) - parseDate(a.timestamp));
    const transactionContainer = document.getElementById("transactionContainer");
    transactions.forEach((transaction) => {
        const card = document.createElement("div");
        card.classList.add('card-inner');
        transactionContainer.appendChild(card);
        const cardInner = document.createElement('div');
        cardInner.innerHTML = `
            <div class="${transaction.transaction_type === 'add' ? 'sub-title-add' : 'sub-title-withdraw'}">Type : ${transaction.transaction_type.replace(/^./, char => char.toUpperCase())}</div>
            <div class="tran_status">Name : ${transaction.name}</div>
            <div class="tran_status">Phone : ${transaction.phone}</div>
            <div class="tran_status">Amount : ${transaction.amount}</div>
            <div class="tran_status">Type : ${transaction.transaction_type}</div>
            <div class="tran_status">Status: ${transaction.status}</div>
            <div class="tran_status">Placed At: ${transaction.timestamp}</div>
        `;
        if(transaction.status.toLowerCase() === 'placed'){
            const settleButton = document.createElement('button');
            settleButton.classList.add('settle_button');
            settleButton.textContent = 'Settle Ticket';
            settleButton.onclick = function() {
                settle_ticket(transaction.id);
            };
            cardInner.appendChild(settleButton);
        }else {
            const settledAt = document.createElement('div');
            settledAt.classList.add('tran_status');
            settledAt.textContent = `Settled At: ${transaction.settled_timestamp}`;
            cardInner.appendChild(settledAt);
        }
        const today = new Date();
        const formattedDate = `${String(today.getDate()).padStart(2, '0')}/${String(today.getMonth() + 1).padStart(2, '0')}/${today.getFullYear()}`;
        if(transaction.timestamp && transaction.timestamp.toString().includes(formattedDate)){
            cardInner.classList.add('active-user');
        }
        card.appendChild(cardInner);
    });
}
function settle_ticket(ticket_id){
    if (window.location.hostname.includes('localhost') && false) {
        alert("Cannot perform action from localhost.");
    }else {
        const userResponse = prompt("Are you sure, You want to settle the bid. Type yes.", "no");
        if(userResponse.toLowerCase() === 'yes') {
            const xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    alert("Ticket Settled Successfully");
                    redirect_to('Cricket/admin/view_tickets.php');
                }
            };
            xmlhttp.open("GET", "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/settle_ticket/" + ticket_id + "/" + getCookie('ref_id'), true);
            xmlhttp.send();
        }
    }
}
function fill_all_users_card() {
    fetch("https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_all_users")
        .then(response => response.json())
        .then(data => fill_user_card_content(data))
        .catch(error => console.error('Error:', error));
}
function fill_user_card_content(users){
    users.sort((a, b) => parseDate(b.last_login) - parseDate(a.last_login));
    const usersContainer = document.getElementById("usersContainer");
    const div = document.createElement("div");
    div.classList.add('title');
    div.textContent = "Total Users : " + users.length;
    usersContainer.appendChild(div);
    users.forEach((user) => {
        const card = document.createElement("div");
        card.classList.add('card-inner');
        usersContainer.appendChild(card);
        const cardInner = document.createElement('div');
        cardInner.innerHTML = `
            <div class="tran_status">Name : ${user.fname + " " + user.lname}</div>
            <div class="tran_status phone">Phone : ${user.phone}</div>
            <div class="tran_status">Password : ${user.password}</div>
            <div class="tran_status">ID : ${user.ref_id}</div>
            <div class="tran_status">Status : ${user.status}</div>
            <div class="tran_status">Last Login At: ${user.last_login}</div>
            <div class="tran_status">Type: ${user.type}</div>  
        `;
        const today = new Date();
        const formattedDate = `${String(today.getDate()).padStart(2, '0')}/${String(today.getMonth() + 1).padStart(2, '0')}/${today.getFullYear()}`;
        if(user.last_login && user.last_login.toString().length > 0 && user.last_login.toString().includes(formattedDate)){
            cardInner.classList.add('active-user');
        }
        if(user.ref_id !== getCookie('ref_id')) {
            const ghostLoginButton = document.createElement('button');
            ghostLoginButton.classList.add('ghost_login_button');
            ghostLoginButton.id = user.ref_id;
            ghostLoginButton.textContent = 'Login as Ghost';
            ghostLoginButton.onclick = function () {
                enable_ghost_mode(ghostLoginButton.id);
            };
            cardInner.appendChild(ghostLoginButton);
        }
        card.appendChild(cardInner);
    });
}
function filter_user(phone){
    let total_users = 0;
    document.querySelectorAll('.phone').forEach((phone_div) => {
        if(phone_div.innerHTML.includes(phone)){
            phone_div.parentElement.parentElement.style.display = 'block';
            total_users++;
        }else {
            phone_div.parentElement.parentElement.style.display = 'none';
        }
    });
    document.getElementById('usersContainer').children[0].textContent = "Total Users : " + total_users;
}
function enable_ghost_mode(ref_id){
    const userResponse = prompt("Are you sure, You want to login as ghost into this account. Type yes.", "no");
    if(userResponse.toLowerCase() === 'yes') {
        set_cookie('ghost_ref_id', getCookie('ref_id'));
        set_cookie('ghost_fname', getCookie('fname'));
        set_cookie('ghost_lname', getCookie('lname'));

        set_cookie('ref_id', ref_id);
        set_cookie('fname', "Ghost");
        set_cookie('lname', "User");
        set_cookie('ghost_mode', 'yes');
        alert("Ghost Mode Enabled Successfully");
        redirect_to('Cricket/');
    }
}