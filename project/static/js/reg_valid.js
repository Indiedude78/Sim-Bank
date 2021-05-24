const pw = document.getElementById("p1");
const userName = document.getElementById("username");
const form = document.getElementById("user-registration");
const errorEle = document.getElementById("error-msg");

form.addEventListener('submit', (e) => {
    let messages = []
    if (!pw.value.match(new RegExp("[A-Z]"))) {
        messages.push("Password must contain an upper case letter\n")
    }
    if (!pw.value.match(new RegExp("[a-z]"))) {
        messages.push("Password must contain a lower case letter\n")
    }
    if (!pw.value.match(new RegExp("[0-9]"))) {
        messages.push("Password must contain a number\n")
    }
    if (userName.value.includes("@")) {
        messages.push("Username cannot contain '@'\n")
    }

    if (messages.length > 0) {
        e.preventDefault()
        errorEle.innerText = messages.join(' ')
    }

})
