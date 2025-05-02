function header_onload(type){
    if(type){
        fetchWrapper("https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_user_wallet_transaction_tickets/any",
            {method: "GET", headers: {"ref_id": 'Exception'}})
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
}