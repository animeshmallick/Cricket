function update_user_profile(){
    let fname = document.forms["update_user_profile_form"]["fname"].value;
    let password = document.forms["update_user_profile_form"]["password"].value;


    if (fname.length < 2) {
        alert("First name must be at least 2 characters");
        return false;
    }
    if (password.length < 4) {
        alert("Password must be more than 2 characters");
        return false;
    }
    alert('Update Profile is now disabled');
    return false;
}