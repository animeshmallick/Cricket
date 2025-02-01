function set_cookie(name,value){
    const date = new Date();
    date.setTime(date.getTime()+(60*60*1000));
    const expires = "; expires="+date.toUTCString();
    document.cookie = `${name}=${value}`+expires+"; path=/";
}
function w3_open() {
    document.getElementById("side-bar-container").style.display = "block";
}

function w3_close() {
    document.getElementById("side-bar-container").style.display = "none";
}
function getCookie(name) {
    const cookies = document.cookie.split('; ');
    for (const cookie of cookies) {
        const [key, value] = cookie.split('=');
        if (key === name) {
            return value;
        }
    }
    return null;
}
document.addEventListener('click', function(e) {
    let sidebar = document.getElementById('side-bar-container');
    const sidebarIcon = document.getElementById('side-bar-icon');
    if (!sidebar.contains(e.target) && !sidebarIcon.contains(e.target)) {
        w3_close();
    }
});
async function fill_header(){
    fetch('../model_ui/header/header.php')
        .then(async response => document.getElementById('header').innerHTML = await response.text())
        .catch(error => console.log(error));
}
function redirect_to(path){
    const url = `${window.location.protocol}//${window.location.hostname}/${path}`;
    console.log(url);
    window.location.href = url;
}
function logout(){
    if (confirm("Are you sure?")) {
        set_cookie('ref_id', '');
        set_cookie('fname', '');
        set_cookie('lname', '');
        redirect_to('Cricket');
        console.log('logout');
    }
}