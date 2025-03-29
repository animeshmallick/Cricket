function fill_ipl_points_table() {
    fetch("https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_ipl_points_table")
        .then(response => response.json())
        .then(data => {
            data.teams.sort((t1, t2) => {
                if(t1.points === t2.points)
                    return t2.run_rate - t1.run_rate;
                return t2.points - t1.points;
            });

            const tableBody = document.getElementById("pointsTable");
            data.teams.forEach(team => {
                let row = `<tr>
                <td style="font-weight: bolder">${team.name}</td>
                <td>${team.played}</td>
                <td>${team.win}</td>
                <td>${team.lost}</td>
                <td>${team.points}</td>
                <td>${team.run_rate > 0 ? "+"+team.run_rate.toFixed(3):team.run_rate.toFixed(3)}</td>
                <td class="form">${team.form.map(f => `<span class="${f}">${f}</span>`).join('')}</td>
            </tr>`;
                tableBody.innerHTML += row;
            });
            console.log('Points Table Displayed');
        })
        .catch(error => console.error('Error:', error));
}