function set_cookie(name,value){
    const date = new Date();
    date.setTime(date.getTime()+(60*60*1000));
    const expires = "; expires="+date.toUTCString();
    document.cookie = `${name}=${value}`+expires+"; path=/";
}