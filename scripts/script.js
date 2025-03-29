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

function set_cookie(name,value){
    const date = new Date();
    date.setTime(date.getTime()+(60*60*1000));
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
async function fill_header(){
    fetch(`${window.location.protocol}//${window.location.hostname}/Cricket/model_ui/header/`)
        .then(async response => document.getElementById('header').innerHTML = await response.text())
        .then(async () => {
            const ref_id = getCookie('ref_id');
            fetch('https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_user_balance/'+ref_id)
                .then(async response => {return await response.json()})
                .then(async balance => {
                    document.getElementById('balance').innerHTML = '&#8377;' + balance.balance;
                })
                .catch(error => console.log(error));
        })
        .catch(error => console.log(error));
}
async function fill_footer(){
    fetch(`${window.location.protocol}//${window.location.hostname}/Cricket/model_ui/footer/`)
        .then(async response => document.getElementById('footer').innerHTML = await response.text())
        .catch(error => console.log(error));
}
function redirect_to(path){
    const url = `${window.location.protocol}//${window.location.hostname}/${path}`;
    console.log(url);
    window.location.href = url;
}
function logout(){
    if (confirm("Are you sure?")) {
        delete_cookie('ref_id');
        delete_cookie('fname');
        delete_cookie('lname');
        delete_cookie('user_type');
        delete_cookie('match_id');
        delete_cookie('series_id');
        delete_cookie('ghost_ref_id');
        delete_cookie('ghost_fname');
        delete_cookie('ghost_lname');
        delete_cookie('ghost_mode');
        redirect_to('Cricket/');
        console.log('logout');
    }
}
function fill_scorecard(){
    const series_id = getCookie('series_id');
    const match_id = getCookie('match_id');
    fetch(`${window.location.protocol}//${window.location.hostname}/Cricket/model_ui/scorecard/`)
        .then(async response => document.getElementById('scorecard').innerHTML = await response.text())
        .then(async () => {
            fetch(`https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_scores/${series_id}/${match_id}/latest`)
                .then(async response => {return await response.json()})
                .then(async score => {
                    update_scorecard(score);
                    scorecard_timer = setInterval(() => {
                        fetch(`https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_scores/${series_id}/${match_id}/latest`)
                            .then(async response => {return await response.json()})
                            .then(async score => {
                                update_scorecard(score);
                                if(window.location.pathname.includes('match'))
                                    enable_session_buttons();
                            })
                    }, 6000);
                })
                .catch(error => console.log(error));
        })
        .catch(error => console.log(error));
}
function update_scorecard(scorecard){
    const crr = (scorecard.innings === 1 ? scorecard.team1_score.runs / get_formated_over(scorecard.team1_score.over) : scorecard.team2_score.runs / get_formated_over(scorecard.team2_score.over));
    const rrr = scorecard.innings === 2 ? (scorecard.team1_score.runs - scorecard.team2_score.runs) / (20 - get_formated_over(scorecard.team2_score.over)) : 0;
    document.getElementsByClassName('team-hover')[scorecard.innings - 1].style = 'animation: breathe 2s infinite ease-in-out;';
    document.getElementsByClassName('team-logo')[scorecard.innings - 1].style = 'animation: breathe-team-logo 2s infinite ease-in-out;';
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
    document.getElementById("match_additional_details").innerHTML = scorecard.match_additional_details[0].replaceAll(".","");

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
    if(scorecard.bowler.bowler2.name !== null) {
        document.getElementById('bowler2').innerHTML = scorecard.bowler.bowler2.name;
        document.getElementById('bowler2_detail').innerHTML = scorecard.bowler.bowler2.runs + " (" + scorecard.bowler.bowler2.overs + ") " + scorecard.bowler.bowler2.wickets + "W";
    }else{
        document.getElementById('bowler2').innerHTML = "";
        document.getElementById('bowler2_detail').innerHTML = "";
    }
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

    document.getElementById('partnership').innerHTML = scorecard.partnership;

    document.getElementById('last_batsman').innerHTML = scorecard.last_batsman;
    document.getElementById('last_wicket').innerHTML = scorecard.last_wicket_at;

    update_themes(scorecard.teams);

    let progressBar = document.getElementById("progressBar");
    let cur_over = get_formated_over(scorecard.over);
    progressBar.style.width = (cur_over * 5) + "%";


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
        return team;
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
}
function enable_session_buttons(){
    const series_id = getCookie('series_id');
    const match_id = getCookie('match_id');
    fetch(`https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_scores/${series_id}/${match_id}/latest`)
        .then(async response => {
            return await response.json()
        })
        .then(scorecard => {
            try{
                if(document.getElementById('sessions') !== null) {
                    if (scorecard.over_id > 101 && scorecard.over_id < 106)
                        document.getElementById('a1').classList.remove('disabled');
                    if (scorecard.over_id > 105 && scorecard.over_id < 110)
                        document.getElementById('b1').classList.remove('disabled');
                    if (scorecard.over_id > 109 && scorecard.over_id < 116)
                        document.getElementById('c1').classList.remove('disabled');
                    if (scorecard.over_id > 115 && scorecard.over_id < 120)
                        document.getElementById('d1').classList.remove('disabled');

                    if (scorecard.over_id > 201 && scorecard.over_id < 206)
                        document.getElementById('a2').classList.remove('disabled');
                    if (scorecard.over_id > 205 && scorecard.over_id < 210)
                        document.getElementById('b2').classList.remove('disabled');
                    if (scorecard.over_id > 209 && scorecard.over_id < 216)
                        document.getElementById('c2').classList.remove('disabled');
                    if (scorecard.over_id > 215 && scorecard.over_id < 220 && (scorecard.team1_score.runs - scorecard.team2_score.runs) > 10)
                        document.getElementById('d2').classList.remove('disabled');

                    if(scorecard.over_id < 200)
                        document.getElementById('winner').classList.remove('disabled');
                    else if (scorecard.over_id < 220 && (scorecard.team1_score.runs - scorecard.team2_score.runs) > 20)
                        if (scorecard.team2_score.wickets < 10)
                            document.getElementById('winner').classList.remove('disabled');
                }
            }catch(e){
                console.log(e);
            }
        }).then(() => {
            fetch(`https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_match/${series_id}/${match_id}`)
                .then(async response => {
                    return await response.json();
                }).then(response => {
                    if (response.hasOwnProperty('extra_sessions_enabled') && response.extra_sessions_enabled)
                        document.getElementById('extra-sessions').style.display = 'flex';
                }).catch(e => console.log(e));
        }).catch(error => console.log(error));

}
function get_formated_over(over){
    let x = over * 10;
    let y = Math.floor(x / 10);
    let z = x % 10;
    return y + z/6;
}
function create_current_over_balls_container(balls){
    document.getElementById('current-over-container').textContent = '';
    const container = document.querySelector('#current-over-container');

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
    if (dateStr && dateStr.includes(", ") && dateStr.includes("/") && dateStr.includes(":")) {
        const [datePart, timePart] = dateStr.split(", ");
        const [day, month, year] = datePart.split("/").map(Number);
        const [hours, minutes, seconds] = timePart.split(":").map(Number);
        return new Date(year, month - 1, day, hours, minutes, seconds);
    }
    return new Date(2000, 1, 1, 0, 0, 0);
};

//Refresh the AUTH cookies and extend time by 1hr if user is active
['ref_id', 'fname', 'lname', 'user_type'].forEach(cookie => {
    if (getCookie(cookie) !== null)
    set_cookie(cookie, getCookie(cookie))
});
function disable_ghost_mode(){
    set_cookie('ref_id', getCookie('ghost_ref_id'));
    set_cookie('fname', getCookie('ghost_fname'));
    set_cookie('lname', getCookie('ghost_lname'));

    delete_cookie('ghost_ref_id');
    delete_cookie('ghost_fname');
    delete_cookie('ghost_lname');
    delete_cookie('ghost_mode');

    redirect_to('Cricket/');
}
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