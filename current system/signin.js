const signupButton = document.getElementById('register');
const loginButton = document.getElementById('signin');
const loginForm = document.getElementById('login');
const signupForm = document.getElementById('signup');

signupButton.addEventListener('click', function() {
    loginForm.style.display = 'none';
    signupForm.style.display = 'block';
});

loginButton.addEventListener('click', function() {
    signupForm.style.display = 'none';
    loginForm.style.display = 'block';
});
