function closeBanner() {
    document.getElementById("banner").style.display = 'none';
}
function show_banner() {
    setTimeout(() => {
        if(Math.floor(Math.random() * 1000) % 5 === 0)
            document.getElementById("banner").style.display = 'block';
    }, 500);
}

let t = 10;
setInterval(() => {
    t--;
    document.getElementById("banner_time").innerHTML = t.toString();
}, 1000);
// Auto-hide banner after 10 seconds
setTimeout(() => {
    closeBanner();
}, 10000);