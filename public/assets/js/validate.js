
function onRegister()
{
    document.getElementById('form').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const password = document.getElementById('password').value;
        const email = document.getElementById('email').value;
        let error = '';

        if (name === '') {
            error += 'Please enter name<br>';
        } else if (!name.match(/^[A-Za-z ]+$/)) {
            error += 'Enter a valid name<br>';
        }

        if (email === '') {
            error += 'Please enter email<br>';
        } else if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            error += 'Enter a valid email<br>';
        }

        if (password === '') {
            error += 'Please enter password<br>';
        } else if (password.length < 8) {
            error += 'Password must be at least 8 characters.<br>';
        }

        if (error) {
            document.getElementById('submitError').innerHTML = error;
            e.preventDefault();
        }
    });
}

function onLogin()
{
    document.getElementById('form').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        let error = '';
        
        if (email === '') {
            error += "Please enter email"
        } else if (!email.match(/^\S+@\S+\.\S+$/)) {
            error += 'Invalid email format.<br>'
        }

        if (error) {
            document.getElementById('submitError').innerHTML = error;
            e.preventDefault();
        }

        if (password === '') {
            error += "Please enter password"
        } 

    });
}

function onEditUser()
{
    document.getElementById('form').addEventListener('submit', function(e) {
		const name = document.getElementById('name').value.trim();
		const email = document.getElementById('email').value.trim();
		let error = '';

		if (!name.match(/^[A-Za-z ]+$/)) {
			error += 'Enter a valid name.<br>';
		}
        
		if (!email.match(/^\S+@\S+\.\S+$/)) {
            error += 'Invalid email format.<br>'
        };

		if (error) {
			document.getElementById('editUserError').innerHTML = error;
			e.preventDefault();
		}
	});
}

function userEdit()
{
    const form = document.getElementById('form');
    if (!form) {
        return;
    }

    form.addEventListener('submit', function(e) {
		const name = document.getElementById('name').value.trim();
		const email = document.getElementById('email').value.trim();
		let error = '';

        if (!name.match(/^[A-Za-z ]+$/)) {
			error += 'Enter a valid name.<br>';
		}		

        if (!email.match(/^\S+@\S+\.\S+$/)) {
            error += 'Invalid email format.<br>'
        }

		if (error) {
			document.getElementById('editProfileError').innerHTML = error;
			e.preventDefault();
		}
	});
}

document.addEventListener('DOMContentLoaded', function () {
    userEdit();

        const form = document.getElementById('resetPasswordForm');
        if (form) {
            form.onsubmit = function (e) {
                var newPass = document.getElementById('newPassword').value;
                var confirmPass = document.getElementById('confirmPassword').value;
                var errorDiv = document.getElementById('resetPasswordError');
                if (newPass !== confirmPass) {
                    e.preventDefault();
                    errorDiv.textContent = "New password and confirm password do not match.";
                    errorDiv.style.display = 'block';
                } else {
                    errorDiv.style.display = 'none';
                }
            };
        }
});