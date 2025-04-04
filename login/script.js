async function validateForm(event) {
    document.getElementsByClassName('btn')[0].innerHTML = "Logging in...";
    event.preventDefault(); // Prevent form submission

    const phone = document.getElementById('phone').value;
    const password = document.getElementById('password').value;

    // Simple validation (you can add more complex validation)
    const loginUrl = `https://ablminqly0.execute-api.ap-south-1.amazonaws.com/Prod/login/${phone}/${password}`;
    console.log(loginUrl);
    fetch(loginUrl)
        .then(response => response.json())
        .then(response => {
            if (response.hasOwnProperty("ref_id")) {
                setCookie("ref_id", response.ref_id);
                setCookie("fname", response.fname);
                setCookie("lname", response.lname);
                setCookie("user_type", response.type);
                redirect_to('Cricket/');
                return true;
            } else {
                document.getElementById("message").innerHTML = response.error;
                return false;
            }
        })
        .catch(error => {
            document.getElementById("message").innerHTML = error;
            return false;
        })
    // Redirect to another page if validation is successful
}
