function validateName(name) {
	var nameRegEx = /^[a-zA-Z]+$/;

	if (nameRegEx.test(name))
		return true;
	else
		return false;
}

function validateDOB(dob) {
	// yyyy-mm-dd
	var dobRegEx = /^\d{4}[-]\d{2}[-]\d{2}$/;

	if (dobRegEx.test(dob))
		return true;
	else
		return false;
}
function validateEmail(email) {
  // Basic email regex pattern
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

function validateUsername(uname) {

	var unameRegEx = /^[a-zA-Z0-9_]+$/;
	if (unameRegEx.test(uname))
		return true;
	else
		return false;
}


function validateLogin(event) {

	var uname = document.getElementById("username");
	var pwd = document.getElementById("password");
	var flag = true;

	if (!validateUsername(uname.value)) {
		document.getElementById(uname.id).classList.add("input-error");
		document.getElementById("error-text-" + uname.id).classList.remove("hidden");
		flag = false;
	}
	else {
		document.getElementById(uname.id).classList.remove("input-error");
		document.getElementById("error-text-" + uname.id).classList.add("hidden");
	}
	if (pwd.value.length !== 8) {
		document.getElementById(pwd.id).classList.add("input-error");
		document.getElementById("error-text-" + pwd.id).classList.remove("hidden");
		flag = false;
	}
	else {
		document.getElementById(pwd.id).classList.remove("input-error");
		document.getElementById("error-text-" + pwd.id).classList.add("hidden");
	}

	if (flag === false)
		event.preventDefault();
	else
		console.log("validation successfull, sending data to the server");
}

function nameHandler(event){
	var name = event.target;
	if(!validateName(name.value)){
		console.log("'" + name.value + "' is not a valid first name");
	}
}

function usernameHandler(event){
	var uname = event.target;
	if (!validateUsername(uname.value)) {
		// console.log("'" + uname.value + "' is not a valid username");
		document.getElementById(uname.id).classList.add("input-error");
		document.getElementById("error-text-" + uname.id).classList.remove("hidden");
	} 
	else {
		document.getElementById(uname.id).classList.remove("input-error");
		document.getElementById("error-text-" + uname.id).classList.add("hidden");
	}
}
function pwdHandler(event){
	var pwd = event.target;
	if (pwd.value.length < 6) {
		console.log("Password should be exactly 6 characters long");
	}
}
function cpwdHandler(event){
	var pwd = document.getElementById("password");
	var cpwd = event.target;
	if (pwd.value !== cpwd.value) {
		console.log("Your passwords: " + pwd.value + " and " + cpwd.value + " do not match");
	}
}
function dobHandler(event){
	var dob = event.target;
	if (!validateDOB(dob.value)) {
		//console.log("'" + dob.value + "' is not a valid date of birth");
		document.getElementById(dob.id).classList.add("input-error");
		document.getElementById("error-text-" + dob.id).classList.remove("hidden");
	}
	else {
		document.getElementById(dob.id).classList.remove("input-error");
		document.getElementById("error-text-" + dob.id).classList.add("hidden");
	}
}
function emailHandler(event) {
  const emailInput = event.target;
  const email = emailInput.value.trim();

  if (!validateEmail(email)) {
    console.log(`'${email}' is not a valid email address.`);
    emailInput.classList.add("error-input"); // Optional visual cue
    document.getElementById("error-text-email").classList.remove("hidden");
    return false;
  } else {
    emailInput.classList.remove("error-input");
    document.getElementById("error-text-email").classList.add("hidden");
    return true;
  }
}
