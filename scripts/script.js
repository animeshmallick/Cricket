let scorecard_timer;
setTimeout(() => {
    clearInterval(scorecard_timer);
    clearTimeout(this);
}, 600000);

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
    if (!sidebar.contains(e.target) && !sidebarIcon.contains(e.target)) {
        w3_close();
    }
});
async function fill_header(){
    fetch('../model_ui/header/header.php')
        .then(async response => document.getElementById('header').innerHTML = await response.text())
        .then(async () => {
            const ref_id = getCookie('ref_id');
            fetch('https://om8zdfeo2h.execute-api.ap-south-1.amazonaws.com/get_user_balance/'+ref_id)
                .then(async response => {return await response.json()})
                .then(async balance => {
                    document.getElementById('balance').innerHTML = '&#8377;' + balance.balance;
                })
                .catch(error => console.log(error));
        })
        .catch(error => console.log(error));
}
async function fill_footer(){
    fetch('../model_ui/footer/footer.php')
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
        set_cookie('ref_id', '');
        set_cookie('fname', '');
        set_cookie('lname', '');
        redirect_to('Cricket');
        console.log('logout');
    }
}
function fill_scorecard(){
    const series_id = getCookie('series_id');
    const match_id = getCookie('match_id');
    fetch('../model_ui/scorecard/scorecard.php')
        .then(async response => document.getElementById('scorecard').innerHTML = await response.text())
        .then(async () => {
            fetch(`https://om8zdfeo2h.execute-api.ap-south-1.amazonaws.com/scores/${series_id}/${match_id}/latest`)
                .then(async response => {return await response.json()})
                .then(async score => {
                    update_scorecard(score);
                    scorecard_timer = setInterval(() => {
                        update_scorecard(score);
                    }, 6000);
                })
                .catch(error => console.log(error));
        })
        .catch(error => console.log(error));
}
function update_scorecard(scorecard){
    let over_str_1 = scorecard.team1_score.overs;
    let over_str_2 = scorecard.team2_score.overs;
    if(scorecard.innings === 1){
        over_str_1 -= 1;
        let bls = get_valid_balls(scorecard.this_over);
        over_str_1 = over_str_1+"."+bls;
        if(bls === 6 || bls === 0)
            over_str_1 = scorecard.team1_score.overs;
    }
    if(scorecard.innings === 2){
        over_str_2 -= 1;
        let bls = get_valid_balls(scorecard.this_over);
        over_str_2 = over_str_2+"."+bls;
        if(bls === 6 || bls === 0)
            over_str_2 = scorecard.team2_score.overs;
    }
    let total_balls = 0;
    if(scorecard.innings === 1){
        let x = parseFloat(over_str_1) * 10;
        total_balls = (x * 6 / 10) + (x % 10);
    }else{
        let x = parseFloat(over_str_2) * 10;
        total_balls = (x * 6 / 10) + (x % 10);
    }
    if(total_balls <= 0){
        over_str_1 = 0;
        over_str_2 = 0;
    }
    let crr = 0;
    crr = (scorecard.innings === 1 ? scorecard.team1_score.runs : scorecard.team2_score.runs) / total_balls * 6;
    let rrr = scorecard.innings === 2 ? (6 * (scorecard.team1_score.runs - scorecard.team2_score.runs + 1) / (120 - total_balls)) : 0;
    let team_score = [];
    document.getElementById('match_name').innerHTML = scorecard.teams[0] + ' vs ' + scorecard.teams[1];
    document.getElementById('team1_logo').setAttribute('src', `../images/logo/${scorecard.teams[0].toLowerCase()}.png`)
    document.getElementById('team2_logo').setAttribute('src', `../images/logo/${scorecard.teams[1].toLowerCase()}.png`)
    document.getElementById("team1_name").innerHTML = scorecard.teams[0];
    team_score[0] = scorecard.team1_score.runs + "/" + scorecard.team1_score.wickets;
    document.getElementById("team1_score").innerHTML = team_score[0];
    document.getElementById('team1_overs').innerHTML = " (" + over_str_1 + " ov)";
    document.getElementById("team2_name").innerHTML = scorecard.teams[1];
    team_score[1] = scorecard.team2_score.runs + "/" + scorecard.team2_score.wickets;
    document.getElementById("team2_score").innerHTML = team_score[1];
    document.getElementById('team2_overs').innerHTML = " (" + over_str_2 + " ov)";
    document.getElementById("match_additional_details").innerHTML = scorecard.match_additional_details[0];
    document.getElementById('bowler').innerHTML = scorecard.bowler;
    document.getElementById('batsman1').innerHTML = scorecard.batsmen[0];
    document.getElementById('batsman2').innerHTML = scorecard.batsmen[1];
    //document.getElementById('current-over-id').innerHTML = scorecard.teams[scorecard.innings - 1]+" | " +team_score[scorecard.innings - 1];
    create_current_over_balls_container(scorecard.this_over);
    document.getElementById('this_over_summary').innerHTML =
        "Over " + (scorecard.innings === 1 ? over_str_1 : over_str_2)+"  :  " + scorecard.this_over_summary;

    document.getElementById('crr').innerHTML = crr !== 0 ? (crr.toFixed(2)) : "";
    //document.getElementById('rrr').innerHTML = rrr !== 0 ? ("Req. RR : "+ rrr.toFixed(2)) : "";

    document.getElementById('partnership').innerHTML = scorecard.partnership.replaceAll(' Runs, ', ' (').replaceAll(' B', ')');
    document.getElementById('last_batsman').innerHTML = scorecard.last_batsman + " @ " +scorecard.last_wicket_at;
    document.getElementById('timer').innerHTML = "&nbsp";
    console.log('Scorecard Updated');
}
function get_valid_balls(this_over){
    let count = 0;
    for (let i=0;i<this_over.length;i++){
        if(this_over[i].includes('w') || this_over[i].includes('nb'))
            continue;
        count++;
    }
    return count;
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
        ballDiv.classList.add('ball-hover', 'w-12', 'h-8', 'flex', 'items-center', 'justify-center', 'rounded-full', 'text-xl', 'font-bold', 'shadow-md', bgColor, textColor);
        ballDiv.textContent = ball;

        container.appendChild(ballDiv);
    });
}