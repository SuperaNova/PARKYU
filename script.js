
function validateLogin() {
    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;
    let errorMessage = document.getElementById("error-message");

    if (username === "admin" && password === "123") {
        alert("Login Successful!");
        window.location.href = "home.php"; 
        return false;
    } else {
        errorMessage.innerText = "Invalid Username or Password";
        errorMessage.style.color = "red";
        return false; 
    }
}
