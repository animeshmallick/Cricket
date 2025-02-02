let slots_timer;
let slots_time = 0;
function update_session_slots(update_selected){
    const urlParams = new URLSearchParams(window.location.search);
    const session = urlParams.get('session');
    const room = urlParams.get('room');
    const series_id = getCookie('series_id');
    const match_id = getCookie('match_id');
    const amount = document.getElementById('bidSlider').value;
    const url = `../internal/GetSessionSlotDetails.php?series_id=${series_id}&match_id=${match_id}&session=${session}&room=${room}&amount=${amount}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            fill_slot_details(data, update_selected);
        })
        .then(() => {
            clearInterval(slots_timer);
            slots_timer = setInterval(() => {
                update_session_slots(false);
            }, 5000);
        })
        .catch(err => console.log(err));
}
function fill_slot_details(bid_master, update_selected){
    const amount = document.getElementById('bidSlider').value;
    document.getElementById('balls_remaining').innerHTML = bid_master.balls_left;
    document.getElementById('session_close_in_balls').innerHTML = bid_master.balls_left - 6;
    document.getElementById("slot_a_runs").innerHTML =
        "Runs 0 to " + (bid_master.predicted_runs_a - 1);
    document.getElementById('slot_a_runs_1').innerHTML = "(Max "+(bid_master.predicted_runs_a - bid_master.runs - 1)+" runs in "+bid_master.balls_left+" balls)";
    document.getElementById("slot_a_amount").innerHTML = "Put &#8377;" + amount +
        " & Take &#8377;" + Math.trunc(amount * bid_master.rate_1);

    document.getElementById("slot_b_runs").innerHTML =
        "Runs [" + bid_master.predicted_runs_a + " to " + bid_master.predicted_runs_b + "]";
    document.getElementById('slot_b_runs_1').innerHTML = "("+(bid_master.predicted_runs_a - bid_master.runs)+" to "+(bid_master.predicted_runs_b - bid_master.runs)+" runs in "+bid_master.balls_left+" balls)";
    document.getElementById("slot_b_amount").innerHTML = "Put &#8377;" + amount +
        " & Take &#8377;" + Math.trunc(amount * bid_master.rate_2);

    document.getElementById("slot_c_runs").innerHTML =
        "Runs " + (bid_master.predicted_runs_b + 1) + " or More";
    document.getElementById('slot_c_runs_1').innerHTML = "(Min "+(bid_master.predicted_runs_b - bid_master.runs + 1) +" runs in "+bid_master.balls_left+" balls)";
    document.getElementById("slot_c_amount").innerHTML = "Put &#8377;" + amount +
        " & Take &#8377;" + Math.trunc(amount * bid_master.rate_3);

    let slot_a = document.getElementById("slot_a");
    let slot_b = document.getElementById("slot_b");
    let slot_c = document.getElementById("slot_c");
    if(update_selected) {
        let max_rate = Math.max(bid_master.rate_1, bid_master.rate_2, bid_master.rate_3);
        if (bid_master.rate_3 === max_rate)
            slot_c.click();
        if (bid_master.rate_2 === max_rate)
            slot_b.click();
        if (bid_master.rate_1 === max_rate)
            slot_a.click();
        document.getElementById('bid_container').scrollIntoView({behavior: "smooth", block: "end"});
    }
    slots_time = 0;
    console.log("Slots Updated");
}