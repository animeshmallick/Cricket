function startTour(){
    if(getCookie('show_tour') !== null) {
        const tour = new Shepherd.Tour({
            useModalOverlay: true,
            defaultStepOptions: {
                classes: 'shadow-md bg-gray-dark',
                scrollTo: true
            }
        });

        tour.addStep({
            title: 'Scorecard',
            text: 'This is the scorecard of the match',
            attachTo: {element: '#scorecard', on: 'bottom'},
            highlightClass: 'shepherd-highlight',
            buttons: [
                {text: 'Next', action: tour.next},
                {text: 'Skip', action: tour.complete}
            ]
        });

        tour.addStep({
            title: 'Room',
            text: 'Select Room based on the amount to play with.',
            attachTo: {element: '.play-container', on: 'bottom'},
            highlightClass: 'shepherd-highlight',
            buttons: [
                {text: 'Back', action: tour.back},
                {text: 'Next', action: tour.next},
                {text: 'Skip', action: tour.complete}
            ]
        });

        tour.addStep({
            title: 'Change Amount',
            text: 'Move the Slider to change amount',
            attachTo: {element: '.slider-div', on: 'bottom'},
            highlightClass: 'shepherd-highlight',
            buttons: [
                {text: 'Back', action: tour.back},
                {text: 'Next', action: tour.next},
                {text: 'Skip', action: tour.complete}
            ]
        });

        tour.addStep({
            title: 'Balls Left',
            text: 'Balls remaining in the current session.',
            attachTo: {element: '#balls_remaining', on: 'bottom'},
            highlightClass: 'shepherd-highlight',
            buttons: [
                {text: 'Back', action: tour.back},
                {text: 'Next', action: tour.next},
                {text: 'Skip', action: tour.complete}
            ]
        });
        tour.addStep({
            title: 'Balls Left for Biding',
            text: 'Balls remaining before the session closes.',
            attachTo: {element: '#session_close_in_balls', on: 'bottom'},
            highlightClass: 'shepherd-highlight',
            buttons: [
                {text: 'Back', action: tour.back},
                {text: 'Next', action: tour.next},
                {text: 'Skip', action: tour.complete}
            ]
        });

        tour.addStep({
            title: 'Option 1',
            text: 'This is the first option and below 2 are the remaining options for the session.',
            attachTo: {element: '#slot_a', on: 'top'},
            highlightClass: 'shepherd-highlight',
            buttons: [
                {text: 'Back', action: tour.back},
                {text: 'Next', action: tour.next},
                {text: 'Skip', action: tour.complete}
            ]
        });

        tour.addStep({
            title: 'Expected Runs',
            text: 'Expected Runs for This Slot. Select if you think the actual runs would fall in this range at the end of this session.',
            attachTo: {element: '#slot_a_runs', on: 'top'},
            highlightClass: 'shepherd-highlight',
            buttons: [
                {text: 'Back', action: tour.back},
                {text: 'Next', action: tour.next},
                {text: 'Skip', action: tour.complete}
            ]
        });

        tour.addStep({
            title: 'Required Runs for the expected slot to win.',
            text: 'Required Score for this session to win. Select if you think this would be true.',
            attachTo: {element: '#slot_a_runs_1', on: 'top'},
            highlightClass: 'shepherd-highlight',
            buttons: [
                {text: 'Back', action: tour.back},
                {text: 'Next', action: tour.next},
                {text: 'Skip', action: tour.complete}
            ]
        });
        tour.addStep({
            title: 'Amount and Returns',
            text: 'Put X & Get Y, means if you place a bid with RsX and if your prediction is correct then you will receive RsY',
            attachTo: {element: '#slot_a_amount', on: 'top'},
            highlightClass: 'shepherd-highlight',
            buttons: [
                {text: 'Back', action: tour.back},
                {text: 'Next', action: tour.next},
                {text: 'Skip', action: tour.complete}
            ]
        });
        tour.addStep({
            title: 'Option 2',
            text: 'Similarly this is the second option',
            attachTo: {element: '#slot_b', on: 'bottom'},
            highlightClass: 'shepherd-highlight',
            buttons: [
                {text: 'Back', action: tour.back},
                {text: 'Next', action: tour.next},
                {text: 'Skip', action: tour.complete}
            ]
        });
        tour.addStep({
            title: 'Option 3',
            text: 'Similarly this is the third option',
            attachTo: {element: '#slot_c', on: 'bottom'},
            highlightClass: 'shepherd-highlight',
            buttons: [
                {text: 'Back', action: tour.back},
                {text: 'Next', action: tour.next},
                {text: 'Skip', action: tour.complete}
            ]
        });
        tour.addStep({
            title: 'Place Bid',
            text: 'Click on the button to place the bid with the selected option and amount.',
            attachTo: {element: '#placeBidBtn', on: 'bottom'},
            highlightClass: 'shepherd-highlight',
            buttons: [
                {text: 'Back', action: tour.back},
                {text: 'Next', action: tour.next},
                {text: 'Skip', action: tour.complete}
            ]
        });
        tour.addStep({
            title: 'Change the Session',
            text: 'Click on the button to change the Session.',
            attachTo: {element: '.change-session-btn', on: 'bottom'},
            highlightClass: 'shepherd-highlight',
            buttons: [
                {text: 'Back', action: tour.back},
                {text: 'Next', action: tour.next},
                {text: 'Skip', action: tour.complete}
            ]
        });
        tour.addStep({
            title: 'Show you placed bids for this match',
            text: 'Click on the button to see all the bids on this match.',
            attachTo: {element: '#show_all_bids', on: 'bottom'},
            highlightClass: 'shepherd-highlight',
            buttons: [
                {text: 'Back', action: tour.back},
                {text: 'Done', action: tour.complete}
            ]
        });
        tour.start();
        tour.on('complete', () => {
            document.cookie = "show_tour=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
        });
    }
}

let slots_timer;
let slots_time = 0;
function update_session_slots(update_selected){
    //fill_slot_details_default();
    const urlParams = new URLSearchParams(window.location.search);
    const session = urlParams.get('session');
    const room = urlParams.get('room');
    const series_id = getCookie('series_id');
    const match_id = getCookie('match_id');
    const amount = document.getElementById('bidSlider').value;
    const url = `${window.location.protocol}//${window.location.hostname}/Cricket/internal/GetSessionSlotDetails.php?series_id=${series_id}&match_id=${match_id}&session=${session}&room=${room}&amount=${amount}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if(data.hasOwnProperty('error')){
                alert(data.error);
                redirect_to('Cricket/');
            }else {
                fill_slot_details(data, update_selected);
            }
        })
        .then(() => {
            clearInterval(slots_timer);
            slots_timer = setInterval(() => {
                update_session_slots(false);
            }, 5000);
        })
        .catch(err => console.log(err));
}
function fill_slot_details_default(){
    document.getElementById('balls_remaining').innerHTML = "-";
    document.getElementById('session_close_in_balls').innerHTML = "-";
    document.getElementById("slot_a_runs").innerHTML = "-";
    document.getElementById('slot_a_runs_1').innerHTML = "-";
    document.getElementById("slot_a_amount").innerHTML = "-";
    document.getElementById("slot_b_runs").innerHTML = "-";
    document.getElementById('slot_b_runs_1').innerHTML = "-";
    document.getElementById("slot_b_amount").innerHTML = "-";
    document.getElementById("slot_c_runs").innerHTML = "-";
    document.getElementById('slot_c_runs_1').innerHTML = "-";
    document.getElementById("slot_c_amount").innerHTML = "-";
    document.getElementById("session_name").innerHTML = "-";
    let slot_a = document.getElementById("slot_a");
    let slot_b = document.getElementById("slot_b");
    let slot_c = document.getElementById("slot_c");
    slot_a.classList.remove("selected");
    slot_b.classList.remove("selected");
    slot_c.classList.remove("selected");
}
function fill_slot_details(bid_master, update_selected){
    document.getElementById("session_name").innerHTML = bid_master.session_name;
    const amount = document.getElementById('bidSlider').value;
    document.getElementById('balls_remaining').innerHTML = bid_master.balls_left;
    document.getElementById('session_close_in_balls').innerHTML = bid_master.balls_left - 6;
    document.getElementById("slot_a_runs").innerHTML = "Runs " + (bid_master.predicted_runs - 1) + " or less";
    document.getElementById('slot_a_runs_1').innerHTML = "(Max "+(bid_master.predicted_runs - bid_master.runs - 1)+" runs in "+bid_master.balls_left+" balls)";
    document.getElementById("slot_a_amount").innerHTML = "Put &#8377;" + amount +
        " & Take &#8377;" + Math.trunc(amount * bid_master.rate_1);

    document.getElementById("slot_b_runs").innerHTML = "Runs " + (bid_master.predicted_runs + 1) + " or more";
    document.getElementById('slot_b_runs_1').innerHTML = "(Min "+(bid_master.predicted_runs - bid_master.runs + 1)+" runs in "+bid_master.balls_left+" balls)";
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
        if(getCookie('show_tour') === null)
            document.getElementById('bid_container').scrollIntoView({behavior: "smooth", block: "end"});
    }
    slots_time = 0;
    console.log("Slots Updated");
}