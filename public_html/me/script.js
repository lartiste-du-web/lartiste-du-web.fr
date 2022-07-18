const displayNameEditable = document.querySelector('#display-name-editable');
const usernameEditable = document.querySelector('#username-editable');
const imageZone = document.querySelector('#profile-picture');

const USER_PID = document.querySelector('#main-info').dataset.userPid;

let name = displayNameEditable.innerText;
let username = usernameEditable.innerText;
let picture = imageZone.src;

let newName = name;
let newUsername = username;
let newPicture = picture;

displayNameEditable.addEventListener('keytyped', e => {
   newName = e.target.innerText;
});

function readImageFile(file) {
    if (file.type && !file.type.startsWith('image/')) {
        console.log('File is not an image.', file.type, file);
        return;
    }
    return new Promise((res, rej) => {
        const reader = new FileReader();
        reader.addEventListener('load', event => {
            res(event.target.result);
        });
        reader.onerror = rej;
        reader.readAsDataURL(file);
    });
}

function updateCropCoord() {
    var {x, y, width, height} = imageCropCircle.getBoundingClientRect();
    var [x2, y2, dWidth, dHeight] = [x, y, width, height];
    var {x, y, width, height} = imageCropElem.getBoundingClientRect();
    if(x2 - x + dWidth > width) {
        var dx = x2 - x + dWidth - width;
        var lx = parseInt(imageCropCircle.style.getPropertyValue('left').replaceAll('px', ''));
        imageCropCircle.style.setProperty('left', (lx - dx) + 'px');
    }
    if(x2 - x < 0) {
        var dx = x2 - x;
        var lx = parseInt(imageCropCircle.style.getPropertyValue('left').replaceAll('px', ''));
        imageCropCircle.style.setProperty('left', (lx - dx) + 'px');
    }
    if(y2 - y + dHeight > height) {
        var dy = y2 - y + dHeight - height;
        var ly = parseInt(imageCropCircle.style.getPropertyValue('top').replaceAll('px', ''));
        imageCropCircle.style.setProperty('top', (ly - dy) + 'px');
    }
    if(y2 - y < 0) {
        var dy = y2 - y;
        var ly = parseInt(imageCropCircle.style.getPropertyValue('top').replaceAll('px', ''));
        imageCropCircle.style.setProperty('top', (ly - dy) + 'px');
    }
    return ({
        x: (x2 - x)/width,
        y: (y2 - y)/height,
        width: dWidth / width,
        height: dHeight / height
    });
}

const imageCropDiv = document.querySelector('#image-crop-container');
const imageCropElem = document.querySelector('#image-crop-container img');
const imageCropCircle = document.querySelector('#crop-section');
dragElement(imageCropCircle, updateCropCoord);

function setProfileImage(imageData) {
    imageCropDiv.style.display = 'block';
    imageCropElem.src = imageData;
    imageCropElem.onerror = function() {
        imageCropDiv.style.display = 'none';
        imageCropElem.onerror = null;
    }
    imageCropElem.onload = function() {
        imageCropCircle.style.top = imageCropCircle.style.left = '0px';
        var {x, y, width, height} = imageCropCircle.getBoundingClientRect();
        var [x2, y2, dWidth, dHeight] = [x, y, width, height];
        var {x, y, width, height} = imageCropElem.getBoundingClientRect();
        var dx = x2 - x;
        var lx = parseInt(imageCropCircle.style.getPropertyValue('left').replaceAll('px', ''));
        imageCropCircle.style.setProperty('left', (lx - dx + 0.5*(width - dWidth)) + 'px');
        var dy = y2 - y;
        var ly = parseInt(imageCropCircle.style.getPropertyValue('top').replaceAll('px', ''));
        imageCropCircle.style.setProperty('top', (ly - dy + 0.5*(height - dHeight)) + 'px');
    }
}

imageZone.addEventListener('click', event => {
    event.preventDefault();
    const input = document.createElement('input');
    input.type = 'file';
    input.onchange = async e => { 
        const file = e.target.files[0];
        if(!file) {
            return;
        }
        const imageData = await readImageFile(file);
        if(!imageData) {
            return;
        }
        setProfileImage(imageData);
    }
    input.click();
});
imageZone.ondragover = ev => {
    imageZone.style.cursor = 'drop';
    ev.preventDefault();
}
imageZone.ondrop = async ev => {
    console.log('File(s) dropped');
    
    // Prevent default behavior (Prevent file from being opened)
    ev.preventDefault();
    
    let file;
    
    if (ev.dataTransfer.items) {
        if(ev.dataTransfer.items.length != 1) {
            return;
        }
        // Use DataTransferItemList interface to access the file(s)
        for (let i = 0; i < ev.dataTransfer.items.length; i++) {
            // If dropped items aren't files, reject them
            if (ev.dataTransfer.items[i].kind === 'file') {
                file = ev.dataTransfer.items[i].getAsFile();
            }
        }
    } else {
        if(ev.dataTransfer.files.length != 1) {
            return;
        }
        // Use DataTransfer interface to access the file(s)
        for (let i = 0; i < ev.dataTransfer.files.length; i++) {
            file = ev.dataTransfer.files[i];
        }
    }
    
    if(!file) {
        return;
    }
    const imageData = await readImageFile(file);
    if(!imageData) {
        return;
    }
    setProfileImage(imageData);
};

const cropMinus = document.querySelector('#div-minus');
const cropPlus = document.querySelector('#div-plus');

cropMinus.addEventListener('click', e => {
    let size = parseInt(imageCropCircle.style.getPropertyValue('--size').replaceAll('px', ''));
    if(isNaN(size)) {
        size = 32;
    }
    imageCropCircle.style.setProperty('--size', Math.max(16, size / 1.25) + 'px');
    updateCropCoord();
});

cropPlus.addEventListener('click', e => {
    let size = parseInt(imageCropCircle.style.getPropertyValue('--size').replaceAll('px', ''));
    if(isNaN(size)) {
        size = 32;
    }
    let {width, height} = imageCropElem.getBoundingClientRect();
    imageCropCircle.style.setProperty('--size', Math.min(Math.min(width, height)-2, size * 1.25) + 'px');
    updateCropCoord();
});

function crop(imageSrc, x, y, w, h, format = 'png') {
    return new Promise((res, rej) => {
        const img = new Image();
        img.onload = function() {
            const canvas = document.createElement('canvas');
            canvas.width = w;
            canvas.height = h;
            const context = canvas.getContext('2d');
            context.drawImage(img, x, y, w, h, 0, 0, w, h);
            res(canvas.toDataURL(format));
        }
        img.onerror = rej;
        img.src = imageSrc;
    });
}

const confirmCrop = document.querySelector('#confirm-crop');
confirmCrop.addEventListener('click', async event => {
    const {x, y, width, height} = updateCropCoord();
    const [cropX, cropY, cropW, cropH] = [x * imageCropElem.naturalWidth, y * imageCropElem.naturalHeight, width * imageCropElem.naturalWidth, height * imageCropElem.naturalHeight].map(x => Math.round(x));
    imageZone.src = (newPicture = await crop(imageCropElem.src, cropX, cropY, cropW, cropH));
    imageCropDiv.style.display = 'none';
    imageCropElem.onerror = null;
    imageCropElem.src = "";
});
const cancelCrop = document.querySelector('#cancel-crop');
cancelCrop.addEventListener('click', event => {
    imageCropDiv.style.display = 'none';
    imageCropElem.onerror = null;
    imageCropElem.src = "";
});

const changeInfo = document.querySelector('#unsaved-change-info');
const passwordChangeInfo = document.querySelector('#unsaved-change-info input');
const changeInfoConfirm = document.querySelector('#unsaved-change-info button');

let noPasswordChange, withPasswordChange, CHANGING_INFO = false;

changeInfoConfirm.addEventListener('click', event => {
    if(CHANGING_INFO) {
        return;
    }
    let data = {};
    if(withPasswordChange) {
        data['last_password'] = passwordChangeInfo.value;
    }
    if(newUsername != username) {
        data['username'] = newUsername;
    }
    if(newName != name) {
        data['display_name'] = newName;
    }
    if(newPicture != picture) {
        data['profile_picture'] = newPicture;
    }
    CHANGING_INFO = true;
    document.querySelectorAll('#unsaved-change-info *').forEach(el => el.style.cursor = 'wait');
    $.ajax({
        url: 'update.php',
        method: 'POST',
        data,
        success: function(res) {
            CHANGING_INFO = false;
            document.querySelectorAll('#unsaved-change-info *').forEach(el => el.style.cursor = 'inherit');
            if(res.res) {
                for(let change of res.res) {
                    if(change == 'USERNAME') {
                        username = newUsername;
                    }
                    if(change == 'DISPLAY_NAME') {
                        name = newName;
                    }
                    if(change == 'PROFILE_PICTURE') {
                        picture = newPicture;
                    }
                    if(change.startsWith('TOKEN=')) {
                        document.location.href = '/login/';
                    }
                }
            } else {
                $(changeInfo).effect("shake", {
                    distance: 5,
                    times: 3
                });
                console.error(res);
            }
        },
        error: function(res) {
            CHANGING_INFO = false;
            document.querySelectorAll('#unsaved-change-info *').forEach(el => el.style.cursor = 'inherit');
            $(changeInfo).effect("shake", {
                distance: 5,
                times: 3
            });
            console.error(res);
        }
    });
});

function frame() {
    newName = displayNameEditable.innerText;
    if(newName.length > 32) {
        newName = newName.substring(0, 32);
    }
    if(newName != displayNameEditable.innerHTML.replaceAll('&nbsp;', '\xa0')) {
        displayNameEditable.innerText = newName;
    }
    newUsername = usernameEditable.innerText.replaceAll(/[^0-9a-z\_\-]/g, '');
    if(newUsername != usernameEditable.innerHTML) {
        usernameEditable.innerText = newUsername;
    }
    
    noPasswordChange = picture != newPicture || name != newName;
    withPasswordChange = username != newUsername;
    
    changeInfo.style.display = withPasswordChange || noPasswordChange ? '' : 'none';
    passwordChangeInfo.style.display = withPasswordChange ? '' : 'none';
    
    window.requestAnimationFrame(frame);
}
frame();