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
function validateAvatar(avatar) {

	var avatarRegEx = /^[^\n]+.[a-zA-Z]{3,4}$/;

	if (avatarRegEx.test(avatar))
		return true;
	else
		return false;

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
