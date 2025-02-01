async function validateForm(event) {
    event.preventDefault(); // Prevent form submission

    const phone = document.getElementById('phone').value;
    const password = document.getElementById('password').value;

    // Simple validation (you can add more complex validation)
    const loginUrl = `https://om8zdfeo2h.execute-api.ap-south-1.amazonaws.com/login/${phone}/${password}`;
    console.log(loginUrl);
    fetch(loginUrl)
        .then(response => response.json())
        .then(response => {
            if (response.hasOwnProperty("ref_id")) {
                set_cookie("ref_id", response.ref_id);
                set_cookie("fname", response.fname);
                set_cookie("lname", response.lname);
                window.location.href = "http://localhost/CricketT20"; // Replace with your desired URL
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
