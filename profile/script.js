setTimeout(() => {document.getElementById('msg').innerHTML = '';}, 10000);
let oldFName = '',  oldLName = '', oldPassword = '';
function update_user_profile(){
    let fname = document.forms["update_user_profile_form"]["fname"].value;
    let lname = document.forms["update_user_profile_form"]["lname"].value;
    let password = document.forms["update_user_profile_form"]["password"].value;
    if (fname.length < 2)
        alert("First name must be at least 2 characters");
    if (password.length < 4)
        alert("Password must be more than 2 characters");

    if(fname === oldFName && lname === oldLName && password === oldPassword)
        redirect_to(`Cricket/profile/index.php?msg=No changes to save`);
    else {
        const ref_id = getCookie('ref_id');
        fetch(`https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/update_profile/${ref_id}/${fname}/${lname}/${password}`)
            .then(response => response.json())
            .then(data => redirect_to(`Cricket/profile/index.php?msg=${data.msg}`))
            .catch(error => redirect_to(`Cricket/profile/index.php?msg=${error}`));
    }
    return false;
}
function store_previous_profile_data() {
    oldFName = document.forms["update_user_profile_form"]["fname"].value;
    oldLName = document.forms["update_user_profile_form"]["lname"].value;
    oldPassword = document.forms["update_user_profile_form"]["password"].value;
}