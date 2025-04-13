let open_withdrawal_ticket = null;
let open_add_ticket = null;
function fill_wallet_transaction_tickets() {
    const ref_id = getCookie('ref_id');
    fetchWrapper("https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_user_wallet_transaction_tickets/" + ref_id,
        {method: "GET", headers: {"ref_id": ref_id}})
        .then(response => response.json())
        .then(data => fill_transaction_ticket_content(data))
        .catch(error => console.error('Error:', error));
}
function fill_transaction_ticket_content(transactions){
    transactions.sort((a, b) => parseDate(b.timestamp) - parseDate(a.timestamp));
    const transactionContainer = document.getElementById("transactionContainer");
    transactions.forEach((transaction) => {
        if (transaction.transaction_type === 'add' && transaction.status === 'placed')
            open_add_ticket = transaction;
        if (transaction.transaction_type === 'withdraw' && transaction.status === 'placed')
            open_withdrawal_ticket = transaction;
        const card = document.createElement("div");
        card.innerHTML = `
                    <div class="card-inner">
                        <div>
                            <div class="${transaction.transaction_type === 'add' ? 'sub-title-add' : 'sub-title-withdraw'}">Type : ${transaction.transaction_type.replace(/^./, char => char.toUpperCase())}</div>
                            <div class="tran_status">Amount : ${transaction.amount}</div>
                            <div class="tran_status">Status: ${transaction.status}</div>
                            <div class="tran_status">Placed At: ${transaction.timestamp}</div>
                            <div class="tran_status">Placed At: ${transaction.settled_timestamp === undefined ? "Not Settled" : transaction.settled_timestamp}</div>
                        </div>
                    </div>
                `;
        card.addEventListener("click", () => {
            card.classList.toggle("flipped");
        });
        if (transaction.status.includes('placed')) {
            card.children[0].style.backgroundColor = 'red';
            const edit_ticket_btn = document.createElement("button");
            edit_ticket_btn.classList.add('edit-ticket-btn');
            edit_ticket_btn.innerHTML = "Edit Ticket";
            edit_ticket_btn.onclick = ()=> openEditTicketPopup(transaction.id, transaction.amount);
            card.children[0].children[0].appendChild(edit_ticket_btn);
        }
        transactionContainer.appendChild(card);
    });
}
function openEditTicketPopup(tran_id, amount) {
    document.getElementById("newAmount").value = amount;
    document.getElementById("tran_id").innerHTML = tran_id;
    document.getElementById("popup").style.display = "flex";
}

function closeEditTicketPopup() {
    document.getElementById("popup").style.display = "none";
}

function updateTicketAmount() {
    const updatedAmount = document.getElementById("newAmount").value;
    const ticket_id = document.getElementById("tran_id").innerHTML;
    if (updatedAmount && !isNaN(updatedAmount)) {
        update_ticket_amount(ticket_id, updatedAmount);
        closeEditTicketPopup();
    } else {
        alert("Please enter a valid number.");
    }
}
function update_ticket_amount(ticket_id, updatedAmount){
    const ref_id = getCookie('ref_id');
    fetchWrapper("https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/update_ticket_amount/"+ticket_id+"/"+updatedAmount,
        {method: "GET", headers: {"ref_id": ref_id}})
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert("Ticket Updated Successfully");
                location.reload();
            } else {
                alert("Ticket Update Failed");
            }
        })
        .catch(error => console.error('Error:', error));
}