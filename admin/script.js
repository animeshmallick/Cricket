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
            <div class="sub-title">Type : ${transaction.transaction_type}</div>
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