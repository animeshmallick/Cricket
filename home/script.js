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
        if (match.series_id.includes('dummy') && match.match_id.includes('dummy') && getCookie('user_type') !== 'admin') {
                return;
        }
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
        }else if (match.type === 'hide') {
            return;
        }
        const out_div = document.createElement('div');
        matchCardWrapper.appendChild(out_div);

        const top_div = document.createElement('div');
        top_div.style.display = 'flex';
        out_div.appendChild(top_div);

        const bottom_div = document.createElement('div');
        out_div.appendChild(bottom_div);

        const left_div = document.createElement('div');
        left_div.classList.add('left-div');
        top_div.appendChild(left_div);

        const right_div = document.createElement('div');
        right_div.classList.add('right-div');
        right_div.style.overflow = 'hidden';
        top_div.appendChild(right_div);

        const cover_image = document.createElement('img');
        cover_image.src = `../images/cover/${match.cover_img}.png`;
        cover_image.style.width = '11rem';
        cover_image.style.height = '4rem';
        cover_image.style.borderRadius = '0.5rem';
        cover_image.style.objectFit = 'cover';
        cover_image.onerror = function (){
            this.src = `../images/cover/ipl.png`;
            this.onerror = null;
        };
        cover_image.alt = 'Cover Image';
        right_div.appendChild(cover_image);

        const teams = document.createElement('div');
        teams.classList.add('team_name');
        teams.textContent = match.teams.join(' vs ');
        left_div.appendChild(teams);

        const score = document.createElement('span');
        score.style.fontSize = '0.85rem';
        score.textContent = `${match.score.replaceAll('null', '0').replaceAll('(20)', '')}`;
        left_div.appendChild(score);

        const match_details_div = document.createElement('div');
        match_details_div.classList.add('match-details');
        if (match.details) {
            let index = match.details.indexOf('(');
            match_details_div.textContent = `${match.details.toString().substring(0, index === -1 ? match.details.length : index)}`;
        }
        bottom_div.appendChild(match_details_div);

        if (match.type === 'live') {
            const liveFlag = document.createElement('span');
            liveFlag.classList.add('live-flag');
            liveFlag.textContent = 'LIVE';
            left_div.appendChild(liveFlag);
        }

        matchList.appendChild(matchCardWrapper);

        // Trigger animation on page load
        setTimeout(() => {
            matchCardWrapper.style.transform = 'translateY(0)';
            matchCardWrapper.style.opacity = '1';
        }, 300); // Delay animation for smooth effect
    }
});