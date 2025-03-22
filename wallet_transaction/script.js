function fill_wallet_transaction_tickets() {
    const ref_id = getCookie('ref_id');
    fetch("https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_user_wallet_transaction_tickets/" + ref_id)
        .then(response => response.json())
        .then(data => fill_transaction_ticket_content(data))
        .catch(error => console.error('Error:', error));
}
function fill_transaction_ticket_content(transactions){
    transactions.sort((a, b) => parseDate(b.timestamp) - parseDate(a.timestamp));
    const transactionContainer = document.getElementById("transactionContainer");
    transactions.forEach((transaction) => {
        const card = document.createElement("div");
        card.innerHTML = `
                    <div class="card-inner">
                        <div>
                            <div class="sub-title">Type : ${transaction.transaction_type}</div>
                            <div class="tran_status">Amount : ${transaction.amount}</div>
                            <div class="tran_status">Status: ${transaction.status}</div>
                            <div class="tran_status">Placed At: ${transaction.timestamp}</div>
                        </div>
                    </div>
                `;
        card.addEventListener("click", () => {
            card.classList.toggle("flipped");
        });
        transactionContainer.appendChild(card);
    });
}