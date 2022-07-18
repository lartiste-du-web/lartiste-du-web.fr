const username = document.querySelector('input[name=username]');
const email = document.querySelector('input[name=email]');
const pswd1 = document.querySelector('input[name=password]');
const pswd2 = document.querySelector('input[name=password2]');

const usernameInfo = document.querySelector('.username-info');
const passwordStrength = document.querySelector('.password-info');
const confirmInfo = document.querySelector('.confirm-info');

username.addEventListener('change', e => {
    if(/^[a-z0-9\_]+$/.test(username.value)) {
        usernameInfo.innerText = '';
    } else {
        usernameInfo.innerText = 'Votre nom d\'utilisateur ne doit pas être vide et peut contenir seulement des lettres minuscules, des chiffres, des - ou _';
    }
});

function strength(password='') {
    const coefficient = 25;
    if(password.length < 8) {
        return ['red', 0];
    }
    let [small, caps, digits, others] = Array(4).fill(false);
    for(let char of password.split('')) {
        if('abcdefghijklmnopqrstuvwxyz'.includes(char)) {
            small = true;
        } else if('ABCDEFGHIJKLMNOPQRSTUVWXYZ'.includes(char)) {
            caps = true;
        } else if('0123456789'.includes(char)) {
            digits = true;
        } else {
            others = true;
        }
    }
    let num = [small, caps, digits, others].reduce((acc, val) => acc + (val ? 1 : 0), -1);
    let base = ([30, 70, 120, 200])[num];
    let max = ([50, 75, 100, 100])[num];
    let percentage = Math.min(100, Math.min(max, parseInt(base * password.length / coefficient)));
    let color = percentage <= 25 ? 'red' : (percentage <= 50 ? 'orange' : (percentage <= 75 ? 'yellow' : 'lime'));
    return [color, percentage];
}

function update() {
    let [color, percentage] = strength(pswd1.value);
    passwordStrength.style.setProperty('--color', color);
    passwordStrength.style.setProperty('--percentage', percentage);
    passwordStrength.title = "Force de votre mot de passe: " + Math.floor(percentage) + "%";
    
    if(pswd1.value == pswd2.value) {
        confirmInfo.innerText = '';
        pswd2.style.background = '';
        delete pswd2.style.background;
    } else {
        confirmInfo.innerText = 'Les deux mots de passe ne correspondent pas !';
        pswd2.style.background = '#ff0000aa';
    }
    window.requestAnimationFrame(update);
}
update();