function moveMeUp(ele) {
    let target = document.getElementsByTagName("nav")[0];
    if (target) {
        target.after(ele);
    }
}

moveMeUp(document.getElementById("flash"));