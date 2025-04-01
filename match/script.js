function closeBanner() {
    document.getElementById("banner").style.display = 'none';
}
function show_banner() {
    if(Math.floor(Math.random() * 1000) % 8 === 0)
        document.getElementById("banner").style.display = 'block';
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