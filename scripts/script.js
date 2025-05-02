let scorecard_timer;
setTimeout(() => {
    clearInterval(scorecard_timer);
    clearTimeout(this);
}, 600000);

function validate_register_form() {
    let fname = document.forms["register_form"]["fname"].value;
    let phone = document.forms["register_form"]["phone"].value;
    let password = document.forms["register_form"]["password"].value;
    let confirm_password = document.forms["register_form"]["confirm_password"].value;

    if (fname.length < 2) {
        alert("First name must be at least 2 characters");
        return false;
    }
    if (phone.length !== 10) {
        alert("Phone Number must be of 10 digits.");
        return false;
    }
    if (password.length < 4) {
        alert("Password must be more than 2 characters");
        return false;
    }
    if (password !== confirm_password) {
        alert("Password does not match");
        return false;
    }
    return true;
}

function setCookie(name, value){
    const date = new Date();
    date.setTime(date.getTime()+(15*60*1000));
    const expires = "; expires="+date.toUTCString();
    document.cookie = `${name}=${value}`+expires+"; path=/";
}
function delete_cookie(name){
    document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/`;
}

function w3_open() {
    document.getElementById("side-bar-container").style.display = "block";
}

function w3_close() {
    document.getElementById("side-bar-container").style.display = "none";
}
function getCookie(name) {
    const cookies = document.cookie.split('; ');
    for (const cookie of cookies) {
        const [key, value] = cookie.split('=');
        if (key === name) {
            return value;
        }
    }
    return null;
}
document.addEventListener('click', function(e) {
    let sidebar = document.getElementById('side-bar-container');
    const sidebarIcon = document.getElementById('side-bar-icon');
    if (sidebar !== null && !sidebar.contains(e.target) && !sidebarIcon.contains(e.target)) {
        w3_close();
    }
});
async function fill_header(ref_id){
    fetchWrapper(`${window.location.protocol}//${window.location.hostname}/Cricket/model_ui/header/`)
        .then(async response => document.getElementById('header').innerHTML = await response.text())
        .then(async () => {
            fetchWrapper('https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_user_balance/'+ref_id,
                {method: "GET", headers: {"ref_id": ref_id}})
                .then(async response => {return await response.json()})
                .then(async balance => {
                    document.getElementById('balance').innerHTML = '&#8377;' + balance.balance;
                    if(window.location.pathname.includes('wallet_transaction')){
                        document.getElementById('max_withdraw_amount').innerHTML = balance.max_withdraw_amount;
                    }
                })
                .catch(error => console.log(error));
        })
        .catch(error => console.log(error));
}
async function fill_footer(){
    fetchWrapper(`${window.location.protocol}//${window.location.hostname}/Cricket/model_ui/footer/`)
        .then(async response => document.getElementById('footer').innerHTML = await response.text())
        .catch(error => console.log(error));
}
function redirect_to(path){
    const url = `${window.location.protocol}//${window.location.hostname}/${path}`;
    console.log(url);
    window.location.href = url;
}
function fill_scorecard(ref_id){
    fetchWrapper(`${window.location.protocol}//${window.location.hostname}/Cricket/model_ui/scorecard/`)
        .then(async response => document.getElementById('scorecard').innerHTML = await response.text())
        .then(async () => fill_scorecard_content(ref_id))
        .catch(error => console.log(error));
}
function fill_scorecard_content(ref_id){
    const series_id = getCookie('series_id');
    const match_id = getCookie('match_id');
    if(series_id === null || match_id === null){
        clearInterval(scorecard_timer);
        redirect_to('Cricket/');
        return;
    }
    fetchWrapper(`https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_scorecard/${series_id}/${match_id}`,
        {method: "GET", headers: {"ref_id": ref_id}})
        .then(async response => {return await response.json()})
        .then(async score => {
            update_scorecard(score);
            if(window.location.pathname.includes('match'))
                enable_session_buttons(score, ref_id);
            scorecard_timer = setTimeout(() => fill_scorecard_content(ref_id), 6000);
        })
        .catch(error => console.log(error));
}
function update_scorecard(scorecard){
    document.getElementById('scorecard').style.height = 'auto';
    const crr = (scorecard.innings === 1 ? scorecard.team1_score.runs / get_formated_over(scorecard.balls_played) : scorecard.team2_score.runs / get_formated_over(scorecard.balls_played));
    const rrr = scorecard.innings === 2 ? (scorecard.team1_score.runs - scorecard.team2_score.runs) / (20 - get_formated_over(scorecard.balls_played)) : 0;
    if(scorecard.match_additional_details[0].includes('won')) {
        document.getElementById('match_additional_details').parentElement.style = `animation: breathe 1s infinite ease-in-out;`;
    }else{
        if(scorecard.innings === 1 && scorecard.source.includes('bot'))
            document.getElementsByClassName('team-hover')[0].style = 'animation: breathe 2s infinite ease-in-out;';
        if(scorecard.innings === 2 && scorecard.source.includes('bot'))
            document.getElementsByClassName('team-logo')[1].style = 'animation: breathe-team-logo 2s infinite ease-in-out;';
    }
    document.getElementById('match_name').innerHTML = ipl_formated(scorecard.teams[0]).toUpperCase() + ' vs ' + ipl_formated(scorecard.teams[1]).toUpperCase();

    document.getElementById('team1_logo')
        .setAttribute('src', `${window.location.protocol}//${window.location.hostname}/Cricket/images/logo/${ipl_formated(scorecard.teams[0])}.png`)
    document.getElementById('team2_logo')
        .setAttribute('src', `${window.location.protocol}//${window.location.hostname}/Cricket/images/logo/${ipl_formated(scorecard.teams[1])}.png`)

    document.getElementById("team1_name").innerHTML = ipl_formated(scorecard.teams[0]).toUpperCase();
    document.getElementById("team1_score").innerHTML = scorecard.team1_score.runs + "/" + scorecard.team1_score.wickets;
    document.getElementById('team1_overs').innerHTML = " (" + scorecard.team1_score.over + " ov)";
    document.getElementById("team2_name").innerHTML = ipl_formated(scorecard.teams[1]).toUpperCase();
    document.getElementById("team2_score").innerHTML = scorecard.team2_score.runs + "/" + scorecard.team2_score.wickets;;
    document.getElementById('team2_overs').innerHTML = " (" + (scorecard.team2_score.over === null ? '0' : scorecard.team2_score.over) + " ov)";
    if (scorecard.innings === 2){
        document.getElementById("match_additional_details").innerHTML = scorecard.match_additional_details[0].replaceAll(".", "");
        document.getElementById("match_additional_details").parentElement.style.display = "flex";
    }

    if (scorecard.batsmen.batsman1.name !== null) {
        document.getElementById('batsman1').innerHTML =
            scorecard.batsmen.batsman1.name + " " + scorecard.batsmen.batsman1.runs + " (" + scorecard.batsmen.batsman1.balls + ")";
        document.getElementById('batsman1_detail').innerHTML =
            scorecard.batsmen.batsman1.fours + " 4s, " + scorecard.batsmen.batsman1.sixes + " 6s";
    }else{
        document.getElementById('batsman1').innerHTML = "";
        document.getElementById('batsman1_detail').innerHTML = "";
    }

    if(scorecard.batsmen.batsman2.name !== null) {
        document.getElementById('batsman2').innerHTML =
            scorecard.batsmen.batsman2.name + " " + scorecard.batsmen.batsman2.runs + " (" + scorecard.batsmen.batsman2.balls + ")";
        document.getElementById('batsman2_detail').innerHTML =
            scorecard.batsmen.batsman2.fours + " 4s, " + scorecard.batsmen.batsman2.sixes + " 6s";
    }else{
        document.getElementById('batsman2').innerHTML = "";
        document.getElementById('batsman2_detail').innerHTML = "";
    }

    if(scorecard.bowler.bowler1.name !== null){
        document.getElementById('bowler1').innerHTML = scorecard.bowler.bowler1.name;
        document.getElementById('bowler1_detail').innerHTML = scorecard.bowler.bowler1.runs + " (" + scorecard.bowler.bowler1.overs + ") " + scorecard.bowler.bowler1.wickets + "W";
    }else {
        document.getElementById('bowler1').innerHTML = "";
        document.getElementById('bowler1_detail').innerHTML = "";
    }
    if (scorecard.source.includes('default'))
        document.getElementById('current_player_details').style.display = 'none';
    else
        document.getElementById('current_player_details').style.display = 'flex';

    create_current_over_balls_container(scorecard.this_over);

    if(isNaN(crr))
        document.getElementById('crr').parentElement.parentElement.style.display = 'none';
    else
        document.getElementById('crr').innerHTML = crr !== 0 ? (crr.toFixed(2)) : "";

    if(scorecard.innings === 2){
        document.getElementById('rrr').innerHTML = rrr.toFixed(2);
        document.getElementById('rrr').parentElement.parentElement.style.display = 'flex';
    }
    document.getElementById('rrr').innerHTML = rrr !== 0 ? rrr.toFixed(2) : "";

    if (scorecard.partnership !== null && scorecard.partnership.trim().length > 0)
        document.getElementById('partnership').innerHTML = scorecard.partnership;
    else
        document.getElementById('partnership').parentElement.parentElement.style.display = 'none';

    if (scorecard.last_batsman !== null && scorecard.last_batsman.trim().length > 0)
        document.getElementById('last_batsman').innerHTML = scorecard.last_batsman;
    else
        document.getElementById('last_batsman').parentElement.parentElement.style.display = 'none';

    if (scorecard.last_wicket_at !== null && scorecard.last_wicket_at.trim().length > 0)
        document.getElementById('last_wicket').innerHTML = scorecard.last_wicket_at;
    else
        document.getElementById('last_wicket').parentElement.parentElement.style.display = 'none';

    update_themes(scorecard.teams);

    let progressBar = document.getElementById("progressBar");
    progressBar.style.width = (scorecard.balls_played / 1.20) + "%";


    document.getElementById('timer').innerHTML = "&nbsp";
    console.log('Scorecard Updated');
}
function ipl_formated(team){
    team = team.toLowerCase();
    team = team.replace(' ', '');
    if (team.includes('delhi'))
        return "DC";
    else if (team.includes('chennai'))
        return "CSK";
    else if (team.includes('mumbai'))
        return "MI";
    else if (team.includes('kolkata'))
        return "KKR";
    else if (team.includes('bangalore') || team.includes('bengaluru'))
        return "RCB";
    else if (team.includes('rajasthan'))
        return "RR";
    else if (team.includes('lucknow'))
        return "LSG";
    else if (team.includes('gujarat'))
        return "GT";
    else if(team.includes('punjab'))
        return "PBKS";
    else if(team.includes('hydrabad') || team.includes("hyderabad"))
        return "SRH";
    else
        return team.substring(0,3);
}
function update_themes(teams){
    if (teams[0].toLowerCase().includes('kolkata'))
        document.getElementById('team1_container').style.background = `linear-gradient(90deg, whitesmoke, #6304c2)`;
    if (teams[1].toLowerCase().includes('kolkata'))
        document.getElementById('team2_container').style.background = `linear-gradient(90deg, whitesmoke, #6304c2)`;

    if (teams[0].toLowerCase().includes('rajasthan'))
        document.getElementById('team1_container').style.background = `linear-gradient(90deg, whitesmoke, deeppink)`;
    if (teams[1].toLowerCase().includes('rajasthan'))
        document.getElementById('team2_container').style.background = `linear-gradient(90deg, whitesmoke, deeppink)`;

    if (teams[0].toLowerCase().includes('hydrabad'))
        document.getElementById('team1_container').style.background = `linear-gradient(90deg, yellow, orangered)`;
    if (teams[1].toLowerCase().includes('hydrabad'))
        document.getElementById('team2_container').style.background = `linear-gradient(90deg, whitesmoke, #6304c2)`;

    if (teams[0].toLowerCase().includes('lucknow'))
        document.getElementById('team1_container').style.background = `linear-gradient(90deg, orange, royalblue)`;
    if (teams[1].toLowerCase().includes('lucknow'))
        document.getElementById('team2_container').style.background = `linear-gradient(90deg, orange, royalblue)`;

    if (teams[0].toLowerCase().includes('bangalore') || teams[0].toLowerCase().includes('bengaluru')) {
        document.getElementById('team1_container').style.background = `linear-gradient(90deg, black, orangered)`;
        document.getElementById('team1_score').style.color = `gold`;
        document.getElementById('team1_name').style.color = `gold`;
    }
    if (teams[1].toLowerCase().includes('bangalore') || teams[1].toLowerCase().includes('bengaluru')) {
        document.getElementById('team2_container').style.background = `linear-gradient(90deg, black, orangered)`;
        document.getElementById('team2_score').style.color = `gold`;
        document.getElementById('team2_name').style.color = `gold`;
    }
    if (teams[0].toLowerCase().includes('chennai')) {
        document.getElementById('team1_container').style.background = `linear-gradient(90deg, orange, yellow)`;
        document.getElementById('team1_score').style.color = `royalblue`;
        document.getElementById('team1_name').style.color = `royalblue`;
    }
    if (teams[1].toLowerCase().includes('chennai')) {
        document.getElementById('team2_container').style.background = `linear-gradient(90deg, orange, yellow)`;
        document.getElementById('team2_score').style.color = `royalblue`;
        document.getElementById('team2_name').style.color = `royalblue`;
    }
    if (teams[0].toLowerCase().includes('gujarat')) {
        document.getElementById('team1_container').style.background = `linear-gradient(90deg, black, white)`;
        document.getElementById('team1_score').style.color = `gold`;
        document.getElementById('team1_name').style.color = `gold`;
    }
    if (teams[1].toLowerCase().includes('gujarat')) {
        document.getElementById('team2_container').style.background = `linear-gradient(90deg, black, white)`;
        document.getElementById('team2_score').style.color = `gold`;
        document.getElementById('team2_name').style.color = `gold`;
    }
    if (teams[0].toLowerCase().includes('mumbai')) {
        document.getElementById('team1_container').style.background = `linear-gradient(90deg, blue, orange)`;
        document.getElementById('team1_score').style.color = `gold`;
        document.getElementById('team1_name').style.color = `gold`;
    }
    if (teams[1].toLowerCase().includes('mumbai')) {
        document.getElementById('team2_container').style.background = `linear-gradient(90deg, blue, orange)`;
        document.getElementById('team2_score').style.color = `gold`;
        document.getElementById('team2_name').style.color = `gold`;
    }
    if (teams[0].toLowerCase().includes('delhi')) {
        document.getElementById('team1_container').style.background = `linear-gradient(90deg, blue, ghostwhite)`;
        document.getElementById('team1_score').style.color = `white`;
        document.getElementById('team1_name').style.color = `white`;
    }
    if (teams[1].toLowerCase().includes('delhi')) {
        document.getElementById('team2_container').style.background = `linear-gradient(90deg, blue, ghostwhite)`;
        document.getElementById('team2_score').style.color = `white`;
        document.getElementById('team2_name').style.color = `white`;
    }
    if (teams[0].toLowerCase().includes('hydrabad') || teams[0].toLowerCase().includes("hyderabad")) {
        document.getElementById('team1_container').style.background = `linear-gradient(90deg, darkorange, orange)`;
        document.getElementById('team1_score').style.color = `white`;
        document.getElementById('team1_name').style.color = `white`;
    }
    if (teams[1].toLowerCase().includes('hydrabad') || teams[1].toLowerCase().includes("hyderabad")) {
        document.getElementById('team2_container').style.background = `linear-gradient(90deg, darkorange, orange)`;
        document.getElementById('team2_score').style.color = `white`;
        document.getElementById('team2_name').style.color = `white`;
    }
    if (teams[0].toLowerCase().includes('punjab')) {
        document.getElementById('team1_container').style.background = `linear-gradient(90deg, red, white)`;
        document.getElementById('team1_score').style.color = `maroon`;
        document.getElementById('team1_name').style.color = `white`;
    }
    if (teams[1].toLowerCase().includes('punjab')) {
        document.getElementById('team2_container').style.background = `linear-gradient(90deg, red, white)`;
        document.getElementById('team2_score').style.color = `maroon`;
        document.getElementById('team2_name').style.color = `white`;
    }
}
function enable_session_buttons(scorecard, ref_id){
    if (scorecard.balls_played > 6 && scorecard.balls_played < 30 && scorecard.innings === 1)
        document.getElementById('a1').classList.remove('disabled');
    else
        document.getElementById('a1').classList.add('disabled');

    if (scorecard.balls_played > 30 && scorecard.balls_played < 54 && scorecard.innings === 1)
        document.getElementById('b1').classList.remove('disabled');
    else
        document.getElementById('b1').classList.add('disabled');

    if (scorecard.balls_played > 54 && scorecard.balls_played < 84 && scorecard.innings === 1)
        document.getElementById('c1').classList.remove('disabled');
    else
        document.getElementById('c1').classList.add('disabled');

    if (scorecard.balls_played > 84 && scorecard.balls_played < 114 && scorecard.innings === 1)
        document.getElementById('d1').classList.remove('disabled');
    else
        document.getElementById('d1').classList.add('disabled');

    if (scorecard.balls_played > 6 && scorecard.balls_played < 30 && scorecard.innings === 2)
        document.getElementById('a2').classList.remove('disabled');
    else
        document.getElementById('a2').classList.add('disabled');

    if (scorecard.balls_played > 30 && scorecard.balls_played < 54 && scorecard.innings === 2)
        document.getElementById('b2').classList.remove('disabled');
    else
        document.getElementById('b2').classList.add('disabled');

    if (scorecard.balls_played > 54 && scorecard.balls_played < 84 && (scorecard.team1_score.runs - scorecard.team2_score.runs) > 15 && scorecard.team2_score.wickets < 10 && scorecard.innings === 2)
        document.getElementById('c2').classList.remove('disabled');
    else
        document.getElementById('c2').classList.add('disabled');

    //if (scorecard.balls_played > 90 && scorecard.balls_played < 114 && (scorecard.team1_score.runs - scorecard.team2_score.runs) > 15 && scorecard.team2_score.wickets < 10 && scorecard.innings === 2)
    //    document.getElementById('d2').classList.remove('disabled');
    //else
    //    document.getElementById('d2').classList.add('disabled');

    if (scorecard.innings === 1 || (scorecard.balls_played < 114 && (scorecard.team2_score.runs === 0 || (scorecard.team1_score.runs - scorecard.team2_score.runs) > 15) && scorecard.team2_score.wickets < 10))
            document.getElementById('winner').classList.remove('disabled');
    else
        document.getElementById('winner').classList.add('disabled');

    //if (scorecard.innings === 1 || (scorecard.balls_played < 90 && (scorecard.team2_score.runs === 0 || (scorecard.team1_score.runs - scorecard.team2_score.runs) > 15) && scorecard.team2_score.wickets < 10))
    //    document.getElementById('special').classList.remove('disabled');
    //else
    //    document.getElementById('special').classList.add('disabled');

    fetchWrapper(`https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_match/${scorecard.series_id}/${scorecard.match_id}`,
        {method: "GET", headers: {"ref_id": ref_id}})
        .then(async response => {
            return await response.json();
        }).then(response => {
        if (response.hasOwnProperty('extra_sessions_enabled') && response.extra_sessions_enabled)
            document.getElementById('extra-sessions').style.display = 'flex';
    }).catch(e => console.log(e));

}
function get_formated_over(balls_played){
    let x = balls_played % 6;
    let y = Math.floor(balls_played / 6);
    return y + x/6;
}
function create_current_over_balls_container(balls){
    document.getElementById('current-over-container').textContent = '';
    const container = document.querySelector('#current-over-container');

    if(balls.length === 0)
        container.parentElement.children[0].style.display = 'none';
    else {
        container.parentElement.children[0].style.display = 'block';
    }

    // Function to create ball elements with animations
    balls.forEach(ball => {
        let bgColor = 'bg-gray-700';
        let textColor = 'text-white';
        if (ball.includes('6')) {
            bgColor = 'bg-green-500';
        } else if (ball.includes('4')) {
            bgColor = 'bg-green-800';
        } else if (ball.includes('W')) {
            bgColor = 'bg-red-500';
        } else if(ball.includes('w') || ball.includes('nb')){
            bgColor = 'bg-blue-500';
        }

        const ballDiv = document.createElement('div');
        ballDiv.classList.add('ball-hover', 'w-10', 'h-8', 'flex', 'items-center', 'justify-center', 'rounded-full', 'text-xl', 'font-bold', 'shadow-md', bgColor, textColor);
        ballDiv.textContent = ball;

        container.appendChild(ballDiv);
    });
}
const team_badge = document.querySelector('.team-hover')
if(team_badge != null){
    team_badge.addEventListener('touchstart', function() {
        team_badge.classList.add('active');
    });
    team_badge.addEventListener('touchend', function() {
        team_badge.classList.remove('active');
    });
}


const live_badge = document.querySelector('.live-badge')
if(live_badge != null) {
    live_badge.addEventListener('touchstart', function () {
        live_badge.classList.add('active');
    });
    live_badge.addEventListener('touchend', function () {
        live_badge.classList.remove('active');
    });
}

const balls = document.querySelector('.balls')
if(balls != null) {
    balls.addEventListener('touchstart', function () {
        balls.classList.add('active');
    });
    balls.addEventListener('touchend', function () {
        balls.classList.remove('active');
    });
}
const parseDate = (dateStr) => {
    if(typeof dateStr === 'object')
        dateStr = dateStr[dateStr.length - 1];

    if (dateStr && dateStr.includes(", ") && dateStr.includes("/") && dateStr.includes(":")) {
        const [datePart, timePart] = dateStr.split(", ");
        const [day, month, year] = datePart.split("/").map(Number);
        const [hours, minutes, seconds] = timePart.split(":").map(Number);
        return new Date(year, month - 1, day, hours, minutes, seconds);
    }
    return new Date(2000, 1, 1, 0, 0, 0);
};
function refreshPage(btn) {
    // Add click animation
    btn.classList.add("clicked");

    // Create ripple effect
    let ripple = document.createElement("span");
    ripple.classList.add("ripple");
    btn.appendChild(ripple);

    // Remove the ripple after animation ends
    setTimeout(() => {
        ripple.remove();
        btn.classList.remove("clicked");
        location.reload(); // Refresh page
    }, 600);
}
function openScorecardPopup(team, ref_id) {
    document.getElementById('scorecard').style.height = '100vh';
    const modal = document.getElementById("playerModal");
    const overlay = document.querySelector(".overlay");
    modal.innerHTML = `<div class="title" style="font-size:2rem">Loading Player Details</div>`
    const url = "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_detailed_score/"+getCookie('series_id')+"/"+getCookie('match_id');
    fetchWrapper(url, {method: "GET", headers: {"ref_id": ref_id}})
        .then(response => response.json())
        .then(data => {
            let batsmen = data[team+'_batsmen'];
            let bowlers = data[team+'_bowlers'];
            modal.innerHTML = `
                <div class="title" style="font-size: 1.5rem">${document.getElementById(team + '_name').innerHTML + " : Innings"}</div>
                <div class="sub-title">Batsmen Details</div>
                <div class="details_scorecard_container">
                    ${batsmen.map(p => 
                        `<div class="player_detail">
                            <div class="player_name" style="background: linear-gradient(90deg, ${p.status.includes('not out') ? 'green' : 'red'}, greenyellow);}">${p.name.replaceAll('†', '')}</div>
                            <div class="player_score">${p.runs}(${p.balls})</div>
                            <div class="player_status" style="background: linear-gradient(90deg, ${p.status.includes('not out') ? 'green' : 'red'}, greenyellow);}">${formatted_status(p.status.replace('†',''))}</div>
                        </div>`
                    ).join('')}
                </div>
                <div class="separator"></div>
                <div class="sub-title">Bowler Details</div>
                <div class="details_scorecard_container">
                    ${bowlers.map(p =>
                        `<div class="player_detail">
                            <div class="player_name" style="background: linear-gradient(90deg, royalblue, greenyellow);}">${p.name}</div>
                            <div class="player_status" style="background: linear-gradient(90deg, royalblue, yellow);}">${p.overs} Ov - ${p.runs} R - ${p.wickets} W</div>
                        </div>`
                    ).join('')}
                </div>
                <button class="close-detailed-scorecard-btn" onclick='closeScorecardPopup()'>Close</button>`;
        })
        .catch(error => console.error('Error fetching data:', error));
    modal.style.display = "block";
    overlay.style.display = "block";
}

function closeScorecardPopup() {
    document.getElementById('scorecard').style.height = 'auto';
    document.getElementById("playerModal").style.display = "none";
    document.querySelector(".overlay").style.display = "none";
}

function formatted_status(status){
    return status.replace(/c ([A-Za-z]+) ([A-Za-z]+) /g, (match, first, last) => `c ${first.charAt(0)}.${last} `)
        .replace(/b ([A-Za-z]+) ([A-Za-z]+)/g, (match, first, last) => `b ${first.charAt(0)}.${last}`);
}
async function fetchWrapper(url, options, retries = 3, delay = 1000) {
    for (let i = 0; i < retries; i++) {
        try {
            const response = await fetch(url, options);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response;
        } catch (error) {
            console.error(`Attempt ${i + 1} failed: ${error.message}`);
            if (i < retries - 1) {
                await new Promise(resolve => setTimeout(resolve, delay));
            }
        }
    }
}