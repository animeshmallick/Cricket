function fill_recharges() {
    const ref_id = getCookie('ref_id');
    fetch("https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_all_recharges/" + ref_id)
        .then(response => response.json())
        .then(data => fill_recharges_content(data))
        .catch(error => console.error('Error:', error));
}
function fill_recharges_content(recharges){
    recharges.sort((a, b) => parseDate(b.time) - parseDate(a.time));
    const rechargesContainer = document.getElementById("rechargesContainer");

    recharges.forEach((recharge) => {
        const card = document.createElement("div");
        card.innerHTML = `
                    <div class="card-inner">
                        <div style="display: flex; justify-content: space-between">
                            <div class="sub-title">${get_formated_recharge_from(recharge.from)}</div>
                            <div style="text-align: center"><span class="amount_span_card">&#8377;${Math.abs(recharge.amount)}</span></div>
                        </div>
                        <div class="time">Placed At: ${recharge.time}</div>
                    </div>
                `;
        if (recharge.from.includes('bidder_refund'))
            card.children[0].style.backgroundColor = 'lightgreen';
        else if (recharge.from.includes('bidder_'))
            card.children[0].style.backgroundColor = 'coral';
        else if (recharge.from.includes('referral'))
            card.children[0].style.backgroundColor = 'lightblue';
        else if (isFinite(Number(recharge.from)))
            card.children[0].style.backgroundColor = 'yellow';

        rechargesContainer.appendChild(card);
    });
}
function get_formated_recharge_from(recharge_from){
    if (recharge_from.includes('bidder_refund'))
        return 'Bid Winner';
    else if (recharge_from.includes('bidder_'))
        return 'New Bid Placed';
    else if (recharge_from.includes('referral'))
        return 'Referral Bonus';
    else if (isFinite(Number(recharge_from)))
        return 'Wallet Recharge';
    else
        return recharge_from;
}