document.addEventListener('DOMContentLoaded', function () {
    const matchList = document.getElementById('match-list');
    fetch('https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_all_matches')
        .then(response => response.json())
        .then(response => response.sort((a, b) => b.id - a.id))
        .then(matches => {
            matches.forEach(match => {
                createMatchCard(match);
            });
        })
        .catch(error => {console.error('Error fetching matches:', error);});

    // Function to create match cards
    function createMatchCard(match) {
        const matchCardWrapper = document.createElement('a');
        matchCardWrapper.classList.add('match-card');
        matchCardWrapper.href = `../match/index.php?series_id=${match.series_id}&match_id=${match.match_id}`;

        // Assign classes based on the match type
        if (match.type === 'live') {
            matchCardWrapper.classList.add('ongoing');
        } else if (match.type === 'yet-to-start') {
            matchCardWrapper.classList.add('yet-to-start');
        } else if (match.type === 'completed') {
            matchCardWrapper.classList.add('completed');
        }

        const teams = document.createElement('h3');
        teams.textContent = match.teams.join(' vs ');
        matchCardWrapper.appendChild(teams);

        const status = document.createElement('p');
        status.textContent = `Status: ${match.type}`;
        matchCardWrapper.appendChild(status);

        if (match.type === 'live') {
            const liveFlag = document.createElement('span');
            liveFlag.classList.add('live-flag');
            liveFlag.textContent = 'LIVE';
            matchCardWrapper.appendChild(liveFlag);
        }

        matchList.appendChild(matchCardWrapper);

        // Trigger animation on page load
        setTimeout(() => {
            matchCardWrapper.style.transform = 'translateY(0)';
            matchCardWrapper.style.opacity = '1';
        }, 300); // Delay animation for smooth effect
    }
});