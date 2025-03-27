function fill_bids() {
    const ref_id = getCookie('ref_id');
    fetch("https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_user_bids/" + ref_id + "/session")
        .then(response => response.json())
        .then(data => {
            fetch("https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_user_bids/" + ref_id + "/winner")
                .then(response => response.json())
                .then(response => data.concat(response))
                .then(data => {
                    fetch(`https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_scores/${getCookie('series_id')}/${getCookie('match_id')}/latest`)
                        .then(response => response.json())
                        .then(score => score.teams)
                        .then(teams => {
                            fill_bid_content(data.filter(bid => bid.match_id === getCookie('match_id') && bid.series_id === getCookie('series_id')), teams)
                        })
                        .catch(error => console.error('Error:', error));
                })
                .catch(error => console.error('Error:', error))
        })
        .then(() => {
            document.getElementById("loading").style.display = "none";
        })
        .catch(error => console.error('Error:', error));
}
function fill_bid_content(bids, teams){
    bids.sort((a, b) => parseDate(b.timestamp) - parseDate(a.timestamp));
    const bidsContainer = document.getElementById("bidsContainer");

    let totalSpend = 0, totalCollected = 0;
    bids.forEach((bid) => {
        totalSpend += parseInt(bid.amount);
        if (bid.status === "win")
            totalCollected += Math.floor((1 + parseFloat(bid.rate)) * parseFloat(bid.amount));
    });
    const summaryCard = document.createElement("div");
    summaryCard.className =`card`;
    summaryCard.innerHTML = `
        <div class="card-inner">
            <div>
                <div class="sub-title">Match Summary</div>
                <div style="display: flex;width: 100%">
                    <div style="text-align: center; width: 50%"><p class="bid_amount" style="font-size: 1.5rem">Played <span class="amount_span_card" style="font-size: 1.75rem">&#8377;${totalSpend}</span></p></div>
                    <div style="text-align: center; width: 50%"><p class="bid_amount" style="font-size: 1.5rem">WIN <span class="amount_span_card" style="font-size: 1.75rem">&#8377;${totalCollected}</span></p></div>
                </div>
            </div>
        </div>
    `;
    bidsContainer.appendChild(summaryCard);

    bids.forEach((bid) => {
        const card = document.createElement("div");
        let statusClass = "";
        if (bid.status === "win") statusClass = "won";
        else if (bid.status === "loss") statusClass = "lost";
        else if (bid.status === "placed") statusClass = "pending";

        const runs_slot = bid.type === 'session' ? bid.slot === 'x' ? `Runs ${bid.runs_max} or Less` : (bid.slot === 'y' ? `Runs ${bid.runs_min} to ${bid.runs_max}` : `Runs ${bid.runs_min} or More`) :
            (bid.type === 'winner' ? bid.slot === 'x' ? teams[0]+" Wins" : teams[1]+" Wins" : "--");

        card.className = `card ${statusClass}`;
        card.innerHTML = `
                    <div class="card-inner">
                        <div>
                            <div class="sub-title">${bid.bid_name === undefined || bid.bid_name === null || bid.bid_name.length === 0 ? '' : 'Name : ' + bid.bid_name}</div>
                            <div class="sub-title">${bid.type === 'session' ? `Innings ${bid.innings} : Over ${bid.session === 'a' ? '1 to 6' :
                                (bid.session === 'b' ? '7 to 10' : (bid.session === 'c' ? '11 to 16' : '17 to 20'))}` :
                                    bid.type === 'winner' ? 'Match Winner' : 'Special Bid'}</div>
                            <div style="display: flex;width: 100%">
                                <div style="width: 40%">
                                    <div style="text-align: center"><p class="bid_amount">PUT <span class="amount_span_card">&#8377;${bid.amount}</span></p></div>
                                    <div style="text-align: center"><p class="bid_amount">GET <span class="amount_span_card">&#8377;${Math.floor(bid.amount * (1 + bid.rate))}</span></span></p></div>
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