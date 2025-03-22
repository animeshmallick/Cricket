function closeBanner() {
    document.getElementById("banner").style.display = 'none';
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