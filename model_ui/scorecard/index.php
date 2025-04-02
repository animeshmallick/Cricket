<div id="scorecard-container" class="shadow-lg border border-gray-500 p-1 bg-gray-310 card-container">
    <!-- Match Info -->
    <div class="flex justify-between items-center pl-1">
        <span id='match_name' class="card-title">Loading Teams</span>
        <span class="bg-red-500 text-white px-3 py-1 rounded-full live-badge">LIVE</span>
    </div>

    <!-- Team Logos and Scores -->
    <div class="space-y-1">
        <div class="team-hover flex justify-between items-center text-sm rounded-md" id="team1_container" onclick="openScorecardPopup('team1')">
            <img id="team1_logo" src="../../images/logo/india.png" alt="India" class="h-10 w-10 team-logo"/>
            <span id='team1_name' class="team-badge">Loading</span>
            <span id='team1_score' class="score text-yellow-400"></span>
            <span id='team1_overs' class="text-xl"></span>
        </div>
        <div class="progress-container">
            <div class="progress">
                <div id="progressBar" class="progress-bar"></div>
            </div>
        </div>
        <div class="team-hover flex justify-between items-center text-sm rounded-md" id="team2_container" onclick="openScorecardPopup('team2')">
            <img id='team2_logo' src="../../images/logo/england.png" alt="Australia" class="h-10 w-10 team-logo" />
            <span id='team2_name' class="team-badge">Loading</span>
            <span id='team2_score' class="score text-yellow-400"></span>
            <span id='team2_overs' class="text-xl"></span>
        </div>
    </div>
    <div class="separator"></div>
    <div class="flex items-center text-blue-600 match-summary" style="display: none">
        <span id='match_additional_details' class="live-score">Loading Match Status</span>
    </div>

    <!-- Player Details -->
    <div class="team-hover mt-1 pl-2 pr-2 flex justify-between items-center text-sm rounded-md" id="current_player_details">
        <div class="player">
            <div>
                <div><span id='batsman1' class="text-black player">Loading Batsman</span></div>
                <div><span id='batsman1_detail' class="text-black">Loading Batsman</span></div>
            </div>
            <div>
                <div><span id='batsman2' class="text-black player">Loading Batsman</span></div>
                <div><span id='batsman2_detail' class="text-black">Loading Batsman</span></div>
            </div>
        </div>
        <div class="player">
            <div style="margin-bottom: 0.4rem;">
                <div><span id='bowler1' class="text-black player">Loading Bowler</span></div>
                <div><span id='bowler1_detail' class="text-black">Loading Bowler</span></div>
            </div>
        </div>
    </div>

    <div class="mt-1 flex justify-between items-center text-sm text-gray-800">
        <div class="flex items-center">
            <span>&nbsp;C.RR: <span id="crr"></span></span>
        </div>
        <div class="flex items-center" style="display: none">
            <span>&nbsp;R.RR: <span id="rrr"></span></span>
        </div>
    </div>

    <!-- Partnership and Last Wicket -->
    <div class="mt-1 text-sm text-gray-300">
        <div class="flex items-center gap-1">
            <svg class="w-5 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 12h4v8h12v-8h4L12 2z"></path></svg>
            <span>Partnership: <span id='partnership' class="text-yellow-300"></span></span>
        </div>
        <div class="flex items-center gap-1">
            <svg class="w-5 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 12h4v8h12v-8h4L12 2z"></path></svg>
            <span>Last Wicket At : <span id='last_wicket' class="text-red-400"></span></span>
        </div>
        <div class="flex items-center gap-1">
            <svg class="w-5 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 12h4v8h12v-8h4L12 2z"></path></svg>
            <span>Last Batsman: <span id='last_batsman' class="text-red-400"></span></span>
        </div>
    </div>
    <div class="small-separator"></div>
    <!-- Current Over Scores -->
    <div class="text-l text-gray-300">
        <div class="sub-title">This Over : </div>
        <div class="ball-container" id="current-over-container">
        </div>
    </div>
    <div style="display: none;text-align: right"><span id="timer" class="text-xs">0sec ago</span></div>
</div>
<div class="overlay" onclick="closeScorecardPopup()"></div>
<div class="modal" id="playerModal"></div>