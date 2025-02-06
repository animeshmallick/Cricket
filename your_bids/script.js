function fill_bids() {
    const ref_id = getCookie('ref_id');
    fetch("https://om8zdfeo2h.execute-api.ap-south-1.amazonaws.com/get_user_bids/" + ref_id + "/session")
        .then(response => response.json())
        .then(data => fill_bid_content(data))
        .then(() => {
            document.getElementById("loading").style.display = "none";
        })
        .catch(error => console.error('Error:', error));
}
function fill_bid_content(bids){
    bids.sort((a, b) => parseDate(b.timestamp) - parseDate(a.timestamp));
    const bidsContainer = document.getElementById("bidsContainer");
    bids.forEach((bid) => {
        const card = document.createElement("div");
        let statusClass = "";
        if (bid.status === "win") statusClass = "won";
        else if (bid.status === "loss") statusClass = "lost";
        else if (bid.status === "placed") statusClass = "pending";

        const runs_slot = bid.slot === 'x' ? `Runs ${bid.runs_max} or Less` : (bid.slot === 'y' ? `Runs ${bid.runs_min} to ${bid.runs_max}` : `Runs ${bid.runs_min} or More`);

        card.className = `card ${statusClass}`;
        card.innerHTML = `
                    <div class="card-inner">
                        <div>
                            <div class="sub-title">${bid.type}</div>
                            <div style="display: flex;width: 100%">
                                <div style="width: 40%">
                                    <div style="text-align: center"><p class="bid_amount">PUT <span class="amount_span_card">${bid.amount}</span></p></div>
                                    <div style="text-align: center"><p class="bid_amount">GET <span class="amount_span_card">${Math.floor(bid.amount * bid.rate)}</span></span></p></div>
                                </div>
                                <div style="width: 60%; text-align: center"><p class="bid_runs">${runs_slot}</p></div>
                            </div>
                            <div class="bid_runs_status">Status: ${bid.status}</div>
                            <div class="time">Placed At: ${bid.timestamp}</div>
                        </div>
                    </div>
                `;
        card.addEventListener("click", () => {
            card.classList.toggle("flipped");
        });
        bidsContainer.appendChild(card);
    });
}