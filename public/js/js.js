function isValidEmail(email) {
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('email').addEventListener('input', function() {
        var emailInput = document.getElementById('email');
        var emailAlert = document.getElementById('emailAlert');
        if (!isValidEmail(emailInput.value)) {
            emailAlert.style.display = 'block';
        } else {
            emailAlert.style.display = 'none';
        }
    });
});
