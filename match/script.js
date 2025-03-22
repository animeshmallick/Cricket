function closeBanner() {
    document.getElementById("banner").style.display = 'none';
}
if(Math.floor(Math.random() * 1000) % 4 !== 0)
    closeBanner();

let t = 10;
setInterval(() => {
    t--;
    document.getElementById("banner_time").innerHTML = t.toString();
}, 1000);
// Auto-hide banner after 10 seconds
setTimeout(() => {
    closeBanner();
}, 10000);