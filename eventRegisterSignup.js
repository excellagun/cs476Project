var name = document.getElementById("name");
var uname = document.getElementById("username");
var pwd = document.getElementById("password");
var cpwd = document.getElementById("cpassword");
var dob = document.getElementById("dob");
var email = document.getElementById("email");

name.addEventListener("blur", nameHandler);
uname.addEventListener("blur", usernameHandler);
pwd.addEventListener("blur", pwdHandler);
cpwd.addEventListener("blur", cpwdHandler);
dob.addEventListener("blur", dobHandler);
email.addEventListener("blur", emailHandler);
