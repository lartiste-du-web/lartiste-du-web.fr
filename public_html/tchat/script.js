const MESSAGE_COMBINE_TIME_LEN = 2 * 60 * 1000;

function getElementScrollScale(domElement){
    return domElement.scrollTop / (domElement.scrollHeight - domElement.clientHeight);
}

function setElementScrollScale(domElement,scale){
        domElement.scrollTop = (domElement.scrollHeight - domElement.clientHeight) * scale;
}

const scrollMessage = document.querySelector('#scroll-message');

let MESSAGE_FIRST_ID = undefined;
let MESSAGES_ID = [];
let LAST_DATE = 0, PREV_LAST_DATE = 0;
let LAST_AUTHOR, PREV_LAST_AUTHOR;
let MSG_COUNT = 0, PREV_MSG_COUNT = 0;
function gotPrevMessages(messages) {
    if(messages.error != null) {
        return;
    }
    const UP = document.querySelector('#messages .message-div:first-of-type');
    const PARENT = document.createElement('div');
    PARENT.classList.add('pev-msg-cont');
    document.querySelector('#messages').prepend(PARENT);
    [PREV_LAST_AUTHOR, PREV_MSG_COUNT, PREV_LAST_DATE] = [undefined, undefined, 0];
    for(let msg of messages.res.reverse()) {
        if(!MESSAGES_ID.includes(msg.id)) {
            MESSAGE_FIRST_ID = Math.min(MESSAGE_FIRST_ID, msg.id);
            MESSAGES_ID.unshift(msg.id);
            let div = PARENT.querySelector('.message-div:last-of-type');
            const date = new Date(msg.timestamp + ' UTC');
            if(PREV_LAST_AUTHOR != msg.public_id || PREV_MSG_COUNT >= 10 || date.getTime() - PREV_LAST_DATE >= MESSAGE_COMBINE_TIME_LEN) {
                PREV_MSG_COUNT = 0;
                div = document.createElement('div');
                div.classList.add('message-div');
                const author = document.createElement('div');
                author.classList.add('little-profile');
                const round = document.createElement('div');
                round.classList.add('round-image');
                author.appendChild(round);
                const image = document.createElement('img');
                image.src = msg?.public_id ? ('/img/profiles/' + msg.public_id + '.png') : '/img/default_user.png';
                image.onerror = function() {
                    this.onerror = null;
                    this.src = '/img/default_user.png';
                }
                round.appendChild(image);
                const name = document.createElement('div');
                author.appendChild(name);
                if(msg.verified) {
                    const verify = document.createElement('div');
                    verify.classList.add('verified');
                    const verif_img = document.createElement('img');
                    verif_img.src = '/img/account_verified.svg';
                    verify.appendChild(verif_img);
                    author.appendChild(verify);
                }
                const display = document.createElement('div');
                display.classList.add('display-name');
                display.innerHTML = msg.display_name;
                name.appendChild(display);
                div.appendChild(author);
            }
            PREV_MSG_COUNT++;
            PREV_LAST_AUTHOR = msg.public_id;
            PREV_LAST_DATE = date.getTime();
            const content = document.createElement('div');
            content.classList.add('message-content');
            content.innerHTML = parseCustomMarkup(msg.message);
            content.title = "Envoyé le " + date.toLocaleDateString() + " à " + date.toLocaleTimeString() + ".";
            div.appendChild(content);
            PARENT.appendChild(div);
        }
    }
    UP.scrollIntoViewIfNeeded();
}
scrollMessage.addEventListener('scroll', e => {
    if(getElementScrollScale(scrollMessage) == 0 && MESSAGE_FIRST_ID) {
        $.ajax('loadMessages.php?before=' + MESSAGE_FIRST_ID).done((response, code) => code == 'success' && gotPrevMessages(response));
    }
});
function gotMessages(messages) {
    if(messages.error != null) {
        return;
    }
    let scroll_after = getElementScrollScale(scrollMessage);
    scroll_after = scroll_after == 1 || isNaN(scroll_after);
    for(let msg of messages.res.reverse()) {
        if(!MESSAGES_ID.includes(msg.id)) {
            if(!MESSAGE_FIRST_ID) {
                MESSAGE_FIRST_ID = msg.id;
            }
            MESSAGES_ID.push(msg.id);
            let div = document.querySelector('#messages > .message-div:last-of-type');
            const date = new Date(msg.timestamp + ' UTC');
            if(LAST_AUTHOR != msg.public_id || MSG_COUNT >= 10 || date.getTime() - LAST_DATE >= MESSAGE_COMBINE_TIME_LEN) {
                MSG_COUNT = 0;
                div = document.createElement('div');
                div.classList.add('message-div');
                const author = document.createElement('div');
                author.classList.add('little-profile');
                const round = document.createElement('div');
                round.classList.add('round-image');
                author.appendChild(round);
                const image = document.createElement('img');
                image.src = msg?.public_id ? ('/img/profiles/' + msg.public_id + '.png') : '/img/default_user.png';
                image.onerror = function() {
                    this.onerror = null;
                    this.src = '/img/default_user.png';
                }
                round.appendChild(image);
                const name = document.createElement('div');
                author.appendChild(name);
                if(msg.verified) {
                    const verify = document.createElement('div');
                    verify.classList.add('verified');
                    const verif_img = document.createElement('img');
                    verif_img.src = '/img/account_verified.svg';
                    verify.appendChild(verif_img);
                    author.appendChild(verify);
                }
                const display = document.createElement('div');
                display.classList.add('display-name');
                display.innerHTML = msg.display_name;
                name.appendChild(display);
                div.appendChild(author);
            }
            MSG_COUNT++;
            LAST_AUTHOR = msg.public_id;
            LAST_DATE = date.getTime();
            const content = document.createElement('div');
            content.classList.add('message-content');
            content.innerHTML = parseCustomMarkup(msg.message);
            content.title = "Envoyé le " + date.toLocaleDateString() + " à " + date.toLocaleTimeString() + ".";
            div.appendChild(content);
            document.querySelector('#messages').appendChild(div);
        }
    }
    if(scroll_after) {
        setElementScrollScale(scrollMessage, 1);
    }
}
function loadMessages(){
    $.ajax('loadMessages.php').done((response, code) => code == 'success' && gotMessages(response));
}
window.addEventListener('DOMContentLoaded', function() {
    loadMessages();
    setInterval(loadMessages, 750);
});

function insertAtCursor(myField, myValue) {
    //IE support
    if (document.selection) {
        myField.focus();
        sel = document.selection.createRange();
        sel.text = myValue;
    }
    //MOZILLA and others
    else if (myField.selectionStart || myField.selectionStart == '0') {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        myField.value = myField.value.substring(0, startPos)
            + myValue
            + myField.value.substring(endPos, myField.value.length);
        myField.selectionStart = startPos + myValue.length;
        myField.selectionEnd = startPos + myValue.length;
    } else {
        myField.value += myValue;
    }
}

function sendMessageFormSubmit(e) {
    e.preventDefault();
    if(SENDING_MESSAGE) {
        return;
    }
    SENDING_MESSAGE = true;
    const message = e.target['message'].value;
    e.target.style.cursor = (e.target['message'].style.cursor = (e.target['valider'].style.cursor = 'wait'));
    $.ajax({
        url: 'sendMessage.php',
        method: 'POST',
        data: {
            message
        },
        success: function(res) {
            SENDING_MESSAGE = false;
            e.target.style.cursor = (e.target['message'].style.cursor = (e.target['valider'].style.cursor = 'inherit'));
            if(res.success) {
                e.target['message'].value = '';
            } else {
                $(messageForm).effect("shake", {
                    distance: 10,
                    times: 2
                });
                console.error(res.error);
            }
        },
        error: function(res) {
            SENDING_MESSAGE = false;
            e.target.style.cursor = (e.target['message'].style.cursor = (e.target['valider'].style.cursor = 'inherit'));
            $(messageForm).effect("shake", {
                distance: 10,
                times: 2
            });
            console.error(res);
        }
    });
}

const messageForm = document.querySelector('#message-form');
messageForm['message'].addEventListener('keydown', e => {
    if(e.keyCode == 13) {
        e.preventDefault();
        if(e.ctrlKey || e.shiftKey) {
            insertAtCursor(e.target, '\n');
            return;
        }
        sendMessageFormSubmit({
            preventDefault: () => true,
            target: messageForm
        });
    }
});
function checkLines() {
    messageForm['message'].style.height = '1px';
    document.querySelector('#messages-container').style.setProperty('--input-lines', Math.max(1, Math.round((messageForm['message'].scrollHeight / Math.min(window.innerHeight, window.innerWidth) * 200 - 1) / 5)));
    messageForm['message'].style.height = '';
    
    window.requestAnimationFrame(checkLines);
}
checkLines();
let SENDING_MESSAGE = false;
messageForm.addEventListener('submit', sendMessageFormSubmit);