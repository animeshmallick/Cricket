function settle_bid_all(type, ref_id){
    if (window.location.hostname.includes('localhost')) {
        alert("Cannot perform action from localhost.");
    }else {
        const userResponse = prompt("Enter Runs for session. Or Enter x or y for winners", "0-x");
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
                    fetchWrapper(url, {method: "GET", headers: {"ref_id": ref_id}})
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
                    fetchWrapper(url, {method: "GET", headers: {"ref_id": ref_id}})
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
function fill_all_wallet_transaction_tickets(ref_id) {
    fetchWrapper("https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_user_wallet_transaction_tickets/any",
        {method: "GET", headers: {"ref_id": ref_id}})
        .then(response => response.json())
        .then(data => fill_transaction_ticket_content(ref_id, data))
        .catch(error => console.error('Error:', error));
}
function fill_transaction_ticket_content(ref_id, transactions){
    transactions.sort((a, b) => parseDate(b.timestamp) - parseDate(a.timestamp));
    const transactionContainer = document.getElementById("transactionContainer");
    let admin_table = [];
    transactions.forEach((transaction) => {
        if (transaction.status === 'settled'){
            let flag = false;
            admin_table.forEach(admin => {
                if (admin.name === transaction.settled_by){
                    if(transaction.transaction_type === 'add') {
                        admin.amount_taken += transaction.amount;
                        admin.count += 1;
                        flag = true;
                    }
                    if(transaction.transaction_type === 'withdraw') {
                        admin.amount_given += transaction.amount;
                        admin.count += 1;
                        flag = true;
                    }
                }
            });
            if(!flag){
                admin_table.push({
                    name: transaction.settled_by,
                    amount_taken: transaction.transaction_type === 'add' ? transaction.amount : 0,
                    amount_given: transaction.transaction_type === 'withdraw' ? transaction.amount : 0,
                    count: 1
                });
            }
        }
        const card = document.createElement("div");
        card.classList.add('card-inner');
        transactionContainer.appendChild(card);
        const cardInner = document.createElement('div');
        cardInner.style.borderRadius = "1rem";
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
            settleButton.onclick = function() {settle_ticket(ref_id, transaction.id);};
            cardInner.classList.add('pending-user');
            cardInner.appendChild(settleButton);
        }else {
            const settledAt = document.createElement('div');
            settledAt.classList.add('tran_status');
            settledAt.textContent = `Settled At: ${transaction.settled_timestamp}`;
            cardInner.appendChild(settledAt);

            const settledBy = document.createElement('div');
            settledBy.classList.add('tran_status');
            settledBy.textContent = `Settled By: ${transaction.settled_by}`;
            cardInner.appendChild(settledBy);

            cardInner.classList.add('active-user');
        }
        const today = new Date();
        const formattedDate = `${String(today.getDate()).padStart(2, '0')}/${String(today.getMonth() + 1).padStart(2, '0')}/${today.getFullYear()}`;
        if(transaction.timestamp && transaction.timestamp.toString().includes(formattedDate)){
            //cardInner.classList.add('active-user');
        }
        card.appendChild(cardInner);
    });
    let tbody = document.querySelector('tbody');
    let total_profit = 0;
    let total_resolved = 0;
    let total_taken = 0;
    let total_given = 0;

    admin_table.forEach(admin => {
        total_taken += admin.amount_taken;
        total_given += admin.amount_given;
        total_resolved += admin.count;
        total_profit += admin.amount_taken - admin.amount_given;
        let tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${admin.name}</td>
            <td>${admin.count}</td>
            <td>${admin.amount_taken}</td>
            <td>${admin.amount_given}</td>
            <td>${admin.amount_taken - admin.amount_given}</td>
        `;
        tbody.appendChild(tr);
    });
    let tr = document.createElement('tr');
    tr.style.fontWeight = 'bold';
    tr.innerHTML = `
            <td>Total</td>
            <td>${total_resolved}</td>
            <td>${total_taken}</td>
            <td>${total_given}</td>
            <td>${total_profit}</td>
    `;
    tbody.appendChild(tr);
    filter_tickets('open');
}
function settle_ticket(ref_id, ticket_id){
    if (window.location.hostname.includes('localhost') && false) {
        alert("Cannot perform action from localhost.");
    }else {
        const userResponse = prompt("Are you sure, You want to settle the bid. Type yes or reject", "no");
        if(userResponse.toLowerCase() === 'yes') {
            fetchWrapper("https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/settle_ticket/" + ticket_id + "/" + ref_id,
                {method: "GET", headers: {"ref_id": ref_id}})
                .then(response => {
                    if (response.status === 200) {
                        alert("Ticket Settled Successfully");
                        redirect_to('Cricket/admin/view_tickets.php');
                    } else {
                        alert("Ticket Settled Failed");
                    }
                })
                .catch(e => console.log(e));
        }
        if(userResponse.toLowerCase() === 'reject'){
            fetchWrapper("https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/reject_ticket/" + ticket_id + "/" + ref_id,
                {method: "GET", headers: {"ref_id": ref_id}})
                .then(response => {
                    if (response.status === 200) {
                        alert("Ticket Rejected Successfully");
                        redirect_to('Cricket/admin/view_tickets.php');
                    } else {
                        alert("Ticket Rejection Failed");
                    }
                })
                .catch(e => console.log(e));
        }
    }
}
var card_data = null;
function fill_all_users_card(ref_id) {
    fetchWrapper("https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_all_users?with_balance=true",
        {method: "GET", headers: {"ref_id": ref_id}})
        .then(response => response.json())
        .then(data => fill_user_card_content(data, false))
        .catch(error => console.error('Error:', error));
}
function fill_user_card_content(users, sort){
    card_data = users;
    let total_withdraw_balance = 0;
    if(sort)
        users.sort((a, b) => (b.balance) - (a.balance));
    else{
        users.sort((a, b) => parseDate(b.last_login) - parseDate(a.last_login));
    }

    const usersContainer = document.getElementById("usersContainer");
    usersContainer.innerHTML = "";
    const div = document.createElement("div");
    div.classList.add('title');
    div.textContent = "Total Users : " + users.length;
    usersContainer.appendChild(div);
    users.forEach((user) => {
        let withdraw_balance = user.balance;
        if (user.type !== 'admin') {
            withdraw_balance -= user.hasOwnProperty('hold_amount') ? user.hold_amount : 0;
            withdraw_balance -= 100;
            withdraw_balance = Math.max(withdraw_balance, 0);
            total_withdraw_balance += withdraw_balance;
        }else{
            withdraw_balance = 0;
        }
        const card = document.createElement("div");
        card.classList.add('card-inner');
        usersContainer.appendChild(card);
        const cardInner = document.createElement('div');
        cardInner.innerHTML = `
            <div class="tran_status name" style="font-weight: bold;font-size: 1.2rem">Name : ${user.fname + " " + user.lname}</div>
            <div class="tran_status phone">Phone : ${user.phone}</div>
            <div class="tran_status">Reffered To : ${user.referral_count === undefined ? 0 : user.referral_count} users</div>
            <div class="tran_status">Refferal From : ${user.referral_from}</div>
            <div class="separator"></div>
            <div class="tran_status phone">Balance : ₹${user.balance}</div>
            <div class="tran_status">Hold Balance : ${user.hasOwnProperty('hold_amount') ? user.hold_amount : 0}</div>
            <div class="tran_status">Max Withdraw Balance : ${withdraw_balance}</div>
            <div class="separator"></div>
            <div class="tran_status status">Status : ${user.status}</div>
            <div class="tran_status status">IsSecured : ${user.secured ? "Yes" : "No"}</div>
            <div class="tran_status type">Type: ${user.type}</div>
            <div class="tran_status">Activated By: ${user.activated_by}</div>
            <div class="separator"></div>
            <div class="tran_status">Last Login At: ${typeof user.last_login === 'object' ? user.last_login[user.last_login.length - 1] : user.last_login}</div>
        `;
        const today = new Date();
        const formattedDate = `${String(today.getDate()).padStart(2, '0')}/${String(today.getMonth() + 1).padStart(2, '0')}/${today.getFullYear()}`;
        if(typeof user.last_login === 'object' && user.last_login[user.last_login.length - 1].toString().includes(formattedDate)){
            cardInner.classList.add('active-user');
        }
        card.appendChild(cardInner);
    });
    document.getElementById('total_withdraw_amount').innerHTML = total_withdraw_balance;
}
function filter_user(keyword){
    if(!isNaN(keyword) && keyword.trim() !== '') {
        let total_users = 0;
        document.querySelectorAll('.card-inner').forEach((card) => {
            if (card.children[0].children[1].innerHTML.includes(keyword) || card.children[0].children[8].innerHTML.includes(keyword)) {
                card.style.display = 'block';
                total_users++;
            } else {
                card.style.display = 'none';
            }
        });
        document.getElementById('usersContainer').children[0].textContent = "Total Users : " + total_users;
    }else{
        let total_users = 0;
        document.querySelectorAll('.card-inner').forEach((card) => {
            let search = card.children[0].children[0].innerHTML + "+" +
                                card.children[0].children[10].innerHTML;
            if (search.toLowerCase().includes(keyword.toLowerCase())) {
                card.style.display = 'block';
                total_users++;
            } else {
                card.style.display = 'none';
            }
        });
        document.getElementById('usersContainer').children[0].textContent = "Total Users : " + total_users;
    }
}
function filter_tickets(value){
    let count = 0;
    if(value === 'all'){
        document.querySelectorAll('.card-inner').forEach((card) => {
            card.style.display = 'block';
            count++;
        });
    }else if(value === 'closed'){
        document.querySelectorAll('.card-inner').forEach((card) => {
            if(card.children[0].children[5].innerHTML.toLowerCase().includes('settled')){
                card.style.display = 'block';
                count++;
            }else {
                card.style.display = 'none';
            }
        });
    }else if(value === 'open') {
        document.querySelectorAll('.card-inner').forEach((card) => {
            if (card.children[0].children[5].innerHTML.toLowerCase().includes('placed')) {
                card.style.display = 'block';
                count++;
            } else {
                card.style.display = 'none';
            }
        });
    } else if(value === 'withdraw'){
        document.querySelectorAll('.card-inner').forEach((card) => {
            if (card.children[0].children[0].innerHTML.toLowerCase().includes('withdraw')) {
                card.style.display = 'block';
                count++;
            } else {
                card.style.display = 'none';
            }
        });
    }else if(value === 'add'){
        document.querySelectorAll('.card-inner').forEach((card) => {
            if (card.children[0].children[0].innerHTML.toLowerCase().includes('add')) {
                card.style.display = 'block';
                count++;
            } else {
                card.style.display = 'none';
            }
        });
    }else if(value === 'yesterday'){
        document.querySelectorAll('.card-inner').forEach((card) => {
            const today = new Date();
            const formattedDate = `${String(today.getDate() - 1).padStart(2, '0')}/${String(today.getMonth() + 1).padStart(2, '0')}/${today.getFullYear()}`;
            const timestamp = card.children[0].children[6].innerHTML;
            if(timestamp.includes(formattedDate)){
                card.style.display = 'block';
                count++;
            }else {
                card.style.display = 'none';
            }
        });
    }else if(value === 'today'){
        document.querySelectorAll('.card-inner').forEach((card) => {
            const today = new Date();
            const formattedDate = `${String(today.getDate()).padStart(2, '0')}/${String(today.getMonth() + 1).padStart(2, '0')}/${today.getFullYear()}`;
            const timestamp = card.children[0].children[6].innerHTML;
            if(timestamp.includes(formattedDate)){
                card.style.display = 'block';
                count++;
            }else {
                card.style.display = 'none';
            }
        });
    }
    document.getElementById('ticket_count').innerHTML = count.toString();
}