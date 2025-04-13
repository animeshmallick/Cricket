document.addEventListener('DOMContentLoaded', function() {
    if(getCookie('user_type') === 'admin'){
        fetchWrapper("https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_user_wallet_transaction_tickets/any",
            {method: "GET", headers: {"ref_id": getCookie("ref_id")}})
            .then(response => response.json())
            .then(data => {
                let openTickets = 0;
                data.forEach(ticket => {
                    if(ticket.status === 'placed'){
                        openTickets++;
                    }
                });
                document.getElementById('open_ticket').innerText = "T" + openTickets.toString();
            })
            .catch(error => console.error('Error:', error));
    }
});