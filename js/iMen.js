function touchiMen() { 
    var userName = prompt("What is your name?", "Enter your name here."); 
 
    if (userName) { 
        alert("It is good to meet you, " + userName + "."); 
        document.getElementById("imenimg").src = "imenres/imen_happy.png"; 
    } 
}