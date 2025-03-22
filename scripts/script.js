let scorecard_timer;
setTimeout(() => {
    clearInterval(scorecard_timer);
    clearTimeout(this);
}, 600000);

function validate_register_form() {
    let fname = document.forms["register_form"]["fname"].value;
    let phone = document.forms["register_form"]["phone"].value;
    let password = document.forms["register_form"]["password"].value;
    let confirm_password = document.forms["register_form"]["fname"].value;

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
    if (password === confirm_password) {
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
        document.cookie = "ref_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
        document.cookie = "fname=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
        document.cookie = "lname=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
        document.cookie = "user_type=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
        document.cookie = "match_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
        document.cookie = "series_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
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
                            })
                    }, 6000);
                })
                .catch(error => console.log(error));
        })
        .catch(error => console.log(error));
}
function update_scorecard(scorecard){
    const crr = (scorecard.innings === 1 ? scorecard.team1_score.runs / get_formated_over(scorecard.team1_score.over) : scorecard.team2_score.runs / get_formated_over(scorecard.team2_score.over));
    document.getElementsByClassName('team-hover')[scorecard.innings - 1].style = 'animation: breathe 2s infinite ease-in-out;';
    document.getElementsByClassName('team-logo')[scorecard.innings - 1].style = 'animation: breathe-team-logo 2s infinite ease-in-out;';
    document.getElementById('match_name').innerHTML = scorecard.teams[0].split(' ')[0] + ' vs ' + scorecard.teams[1].split(' ')[0];

    document.getElementById('team1_logo')
        .setAttribute('src', `${window.location.protocol}//${window.location.hostname}/Cricket/images/logo/${scorecard.teams[0].toLowerCase()}.png`)
    document.getElementById('team2_logo')
        .setAttribute('src', `${window.location.protocol}//${window.location.hostname}/Cricket/images/logo/${scorecard.teams[1].toLowerCase()}.png`)

    document.getElementById("team1_name").innerHTML = scorecard.teams[0];
    document.getElementById("team1_score").innerHTML = scorecard.team1_score.runs + "/" + scorecard.team1_score.wickets;
    document.getElementById('team1_overs').innerHTML = " (" + scorecard.team1_score.over + " ov)";
    document.getElementById("team2_name").innerHTML = scorecard.teams[1];
    document.getElementById("team2_score").innerHTML = scorecard.team2_score.runs + "/" + scorecard.team2_score.wickets;;
    document.getElementById('team2_overs').innerHTML = " (" + scorecard.team2_score.over + " ov)";
    document.getElementById("match_additional_details").innerHTML = scorecard.match_additional_details[0];

    document.getElementById('batsman1').innerHTML =
        scorecard.batsmen.batsman1.name+" "+scorecard.batsmen.batsman1.runs+" ("+scorecard.batsmen.batsman1.balls+")";
    document.getElementById('batsman1_detail').innerHTML =
        scorecard.batsmen.batsman1.fours+" 4s, "+scorecard.batsmen.batsman1.sixes+" 6s";

    document.getElementById('batsman2').innerHTML =
        scorecard.batsmen.batsman2.name+" "+scorecard.batsmen.batsman2.runs+" ("+scorecard.batsmen.batsman2.balls+")";
    document.getElementById('batsman2_detail').innerHTML =
        scorecard.batsmen.batsman2.fours+" 4s, "+scorecard.batsmen.batsman2.sixes+" 6s";

    document.getElementById('bowler1').innerHTML = scorecard.bowler.bowler1.name;
    document.getElementById('bowler1_detail').innerHTML = scorecard.bowler.bowler1.runs+" ("+scorecard.bowler.bowler1.overs+") "+scorecard.bowler.bowler1.wickets+"W";

    document.getElementById('bowler2').innerHTML = scorecard.bowler.bowler2.name;
    document.getElementById('bowler2_detail').innerHTML = scorecard.bowler.bowler2.runs+" ("+scorecard.bowler.bowler2.overs+") "+scorecard.bowler.bowler2.wickets+"W";

    create_current_over_balls_container(scorecard.this_over);

    document.getElementById('crr').innerHTML = crr !== 0 ? (crr.toFixed(2)) : "";
    //document.getElementById('rrr').innerHTML = rrr !== 0 ? ("Req. RR : "+ rrr.toFixed(2)) : "";

    document.getElementById('partnership').innerHTML = scorecard.partnership;

    document.getElementById('last_batsman').innerHTML = scorecard.last_batsman;
    document.getElementById('last_wicket').innerHTML = scorecard.last_wicket_at;

    document.getElementById('timer').innerHTML = "&nbsp";
    console.log('Scorecard Updated');
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
                    if (scorecard.over_id > 206 && scorecard.over_id < 210)
                        document.getElementById('b2').classList.remove('disabled');
                    if(scorecard.over_id < 200)
                        document.getElementById('winner').classList.remove('disabled');
                    else if (scorecard.over_id < 220 && (scorecard.team1_score.runs - scorecard.team2_score.runs) > 10)
                        if (scorecard.team2_score.wickets < 10)
                            document.getElementById('winner').classList.remove('disabled');
                }
            }catch(e){
                console.log(e);
            }
        })
        .catch(error => console.log(error));

}
function get_formated_over(over){
    let x = over * 10;
    let y = x / 10;
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
    const [datePart, timePart] = dateStr.split(", ");
    const [day, month, year] = datePart.split("/").map(Number);
    const [hours, minutes, seconds] = timePart.split(":").map(Number);
    return new Date(year, month - 1, day, hours, minutes, seconds);
};