function settle_bid(bid_id, type, session){
    if (window.location.hostname.includes('localhost')) {
        alert("Cannot perform action from localhost.");
    }else {
        const userResponse = prompt("Type WIN or LOSS as input.", "LOSS");
        let execute = false;
        if (userResponse != null && userResponse.toLowerCase() === 'win') {
            execute = true;
        }
        if(userResponse != null && userResponse.toLowerCase() === 'loss') {
            execute = true
        }
        if(execute) {
            const xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    window.location.href = "https://www.cricketipl.in/Cricket/admin/admin_match_"+type+"_dashboard.php?session=" + session;
                }
            };
            xmlhttp.open("GET", "https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/settle_bid/"+type+"/" + bid_id + "/" + userResponse.toLowerCase(), true);
            xmlhttp.send();
        }
    }
}