const rmCheck = document.getElementById("rembMe");
const usernameInput = document.getElementById("username");
const passwordInput = document.getElementById("password");

if (localStorage.getItem("checkbox") === "inget") {
    rmCheck.checked = true;
    usernameInput.value = localStorage.getItem("username") || "";
    passwordInput.value = localStorage.getItem("password") || "";
} else {
    rmCheck.checked = false;
    usernameInput.value = "";
    passwordInput.value = "";
}

function lsRembMe() {
    if (rmCheck.checked && usernameInput.value !== "" && passwordInput.value !== "") {
        localStorage.setItem("username", usernameInput.value);
        localStorage.setItem("password", passwordInput.value);
        localStorage.setItem("checkbox", "inget");
    } else {
        localStorage.removeItem("username");
        localStorage.removeItem("password");
        localStorage.removeItem("checkbox");
    }
}
