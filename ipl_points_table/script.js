function fill_ipl_points_table(ref_id) {
    fetchWrapper("https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/get_ipl_points_table",
        {method: "GET", headers: {"ref_id": ref_id}})
        .then(response => response.json())
        .then(data => {
            data.teams.sort((t1, t2) => {
                if(t1.points === t2.points)
                    return t2.run_rate - t1.run_rate;
                return t2.points - t1.points;
            });

            const tableBody = document.getElementById("pointsTable");
            let i = 1;
            data.teams.forEach(team => {
                let row = `<tr>
                <td class="name ${i<=4 ? 'qualified' : ''}" style="font-weight: bolder">${team.name}</td>
                <td>${team.played}</td>
                <td>${team.win}</td>
                <td>${team.lost}</td>
                <td style="font-weight: bold">${team.points}</td>
                <td>${team.run_rate > 0 ? "+"+team.run_rate.toFixed(3):team.run_rate.toFixed(3)}</td>
            </tr>`;
                tableBody.innerHTML += row;
                i++;
            });
        })
        .catch(error => console.error('Error:', error));
}