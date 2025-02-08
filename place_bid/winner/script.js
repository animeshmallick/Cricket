let slots_timer;
let slots_time = 0;
function update_winner_slots(update_selected){
    fill_winner_slot_details_default();
    const urlParams = new URLSearchParams(window.location.search);
    const session = urlParams.get('session');
    const room = urlParams.get('room');
    const series_id = getCookie('series_id');
    const match_id = getCookie('match_id');
    const amount = document.getElementById('bidSlider').value;
    fetch(`${window.location.protocol}//${window.location.hostname}/Cricket/internal/GetWinnerSlotDetails.php?series_id=${series_id}&match_id=${match_id}&session=${session}&room=${room}&amount=${amount}`)
        .then(response => response.json())
        .then(data => {
            fill_winner_slot_details(data, update_selected);
        })
        .then(() => {
            clearInterval(slots_timer);
            slots_timer = setInterval(() => {
                update_winner_slots(false);
            }, 5000);
        })
        .catch(err => console.log(err));
}
function fill_winner_slot_details_default(){
    document.getElementById('balls_remaining').style.display = 'block';
    document.getElementById("slot_a_runs").innerHTML = "Loading...";
    document.getElementById("slot_a_amount").innerHTML = "Loading...";
    document.getElementById("slot_b_runs").innerHTML = "Loading...";
    document.getElementById("slot_b_amount").innerHTML = "Loading...";
    let slot_a = document.getElementById("slot_a");
    let slot_b = document.getElementById("slot_b");
    slot_a.classList.remove("selected");
    slot_b.classList.remove("selected");
    slot_a.classList.add("disabled");
    slot_b.classList.add("disabled");
    slot_a.disabled = true;
    slot_b.disabled = true;
    console.log("Slots Loading");
}
function fill_winner_slot_details(bid_master, update_selected){
    const amount = document.getElementById('bidSlider').value;
    if (bid_master.innings === 1)
        document.getElementById('balls_remaining').style.display = 'none';
    else
        document.getElementById('balls_remaining').innerHTML = bid_master.target;
    document.getElementById("slot_a_runs").innerHTML = bid_master.team_a + " Wins the Match";
    document.getElementById("slot_a_amount").innerHTML = "Put &#8377;" + amount +
        " & Take &#8377;" + Math.trunc(amount * bid_master.rate_1);

    document.getElementById("slot_b_runs").innerHTML = bid_master.team_b + " Wins the Match";
    document.getElementById("slot_b_amount").innerHTML = "Put &#8377;" + amount +
        " & Take &#8377;" + Math.trunc(amount * bid_master.rate_2);

    let slot_a = document.getElementById("slot_a");
    let slot_b = document.getElementById("slot_b");
    if(update_selected) {
        let max_rate = Math.max(bid_master.rate_1, bid_master.rate_2);
        if (bid_master.rate_2 === max_rate)
            slot_b.click();
        if (bid_master.rate_1 === max_rate)
            slot_a.click();
        document.getElementById('bid_container').scrollIntoView({behavior: "smooth", block: "end"});
    }
    slots_time = 0;
    console.log("Slots Updated");
}