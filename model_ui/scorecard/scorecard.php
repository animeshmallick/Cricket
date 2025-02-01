<div id="scorecard-container" class="shadow-lg border border-gray-500 p-1 bg-gray-310 card-container">
    <!-- Match Info -->
    <div class="flex justify-between items-center pl-4">
        <span id='match_name' class="card-title">IND vs AUS</span>
        <span class="bg-red-500 text-white px-3 py-1 rounded-full live-badge">LIVE</span>
    </div>

    <!-- Team Logos and Scores -->
    <div class="space-y-1">
        <div class="team-hover flex justify-between items-center text-sm bg-gray-500 p-2 rounded-md">
            <img id="team1_logo" src="../images/logo/india.png" alt="India" class="h-5 w-10" />
            <span id='team1_name' class="team-badge">Team 1</span>
            <span id='team1_score' class="score text-yellow-400">245/6</span>
            <span id='team1_overs' class="small-text">(40.2 ov)</span>
        </div>
        <div class="h-1 bg-gray-800 mb-4"></div>
        <div class="team-hover flex justify-between items-center text-sm bg-gray-500 p-2 rounded-md">
            <img id='team2_logo' src="../images/logo/england.png" alt="Australia" class="h-5 w-10" />
            <span id='team2_name' class="team-badge">Team 2</span>
            <span id='team2_score' class="score text-yellow-400">220/10</span>
            <span id='team2_overs' class="small-text">(50 ov)</span>
        </div>
    </div>

    <!-- Match Status -->
    <div class="mt-2 flex justify-between items-center text-sm text-gray-800">
        <div class="flex items-center">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12" y2="16"></line></svg>
            <span>C.RR: <span class="small-text" id="crr">6.1</span></span>
        </div>
        <div class="flex items-center text-blue-600">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 12h4v8h12v-8h4L12 2z"></path></svg>
            <span id='match_additional_details' class="live-score">Match Status</span>
        </div>
    </div>

    <!-- Player Details -->
    <div class="team-hover mt-2 flex justify-between items-center text-sm bg-gray-500 p-1 rounded-md">
        <div class="player">
            <div style="margin-bottom: 0.4rem;display: flex">
                <span id='batsman1' class="text-green-400">Batsman 1 : 20 (11)</span>
                <svg class="mt-0.5 text-gray-200 ml-2 w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12" y2="16"></line></svg>
            </div>
            <div style="margin-bottom: 0.4rem;display: flex">
                <span id='batsman2' class="text-green-400">Batsman 1 : 20 (11)</span>
                <svg class="mt-0.95 text-gray-200 ml-2 w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12" y2="16"></line></svg>
            </div>
        </div>
        <div class="player">
            <div style="margin-bottom: 0.4rem;">
                <span id='bowler' class="text-green-400">Bowler : 3.2</span>
                <svg class="mt-0.95 text-gray-200 w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12" y2="16"></line></svg>
            </div>
        </div>
    </div>

    <!-- Partnership and Last Wicket -->
    <div class="mt-1 text-l text-gray-300">
        <div class="flex items-center gap-1">
            <svg class="w-5 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 12h4v8h12v-8h4L12 2z"></path></svg>
            <span>Partnership: <span id='partnership' class="text-yellow-300">0 (0)</span></span>
        </div>
        <div class="flex items-center gap-1">
            <svg class="w-5 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 12h4v8h12v-8h4L12 2z"></path></svg>
            <span>Last Wicket: <span id='last_batsman' class="text-red-400">Abc 0 (0)</span></span>
        </div>
    </div>
    <div class="small-separator"></div>
    <!-- Current Over Scores -->
    <div class="text-l text-gray-300">
        <span class="font-bold text-red-400">This Over : </span>
        <span id="this_over_summary" class="text-yellow-200"></span>
        <div class="ball-container justify-between items-center" id="current-over-container">
        </div>
    </div>
    <div style="display: none;text-align: right"><span id="timer" class="text-xs">0sec ago</span></div>
</div>