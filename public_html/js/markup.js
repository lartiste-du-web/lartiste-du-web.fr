function parseCustomMarkup(msg, recursiveColors = true, allowEmbeds = false) {
    const COLORS = {
        "white": "white",
        "red": "red",
        "yellow": "yellow",
        "green": "green",
        "lime": "lime",
        "blue": "blue",
        "light-blue": "#5ABAFA",
        "cyan": "cyan",
        "black": "black",
        "pink": "pink",
        "purple": "magenta"
    };
    
    return msg
        .replaceAll(/\\./g, function(replaced, position) {
            return `&#${replaced.charCodeAt(1)};`
        })
        .replaceAll(' ', '&nbsp;')
        .replaceAll(/\r?\n/g, '<br>')
        .replaceAll(/\@[a-z0-9\_\-]{1,32}/g, function(replaced, position) {
            return `<a href="/user/?username=${replaced.substring(1).replaceAll('-', '%2D').replaceAll('_', '%5f')}">${replaced.split('').map(x => '&#' + x.charCodeAt(0) + ';').join('')}</a>`;
        })
        .replaceAll(/\*\*([^\*]*|(\*(?!\*)))*\*\*/g, function(replaced, position) {
            return `<b>${replaced.replaceAll('**', '')}</b>`;
        })
        .replaceAll(/\_\_([^\_]*|(\_(?!\_)))*\_\_/g, function(replaced, position) {
            return `<span style="text-decoration: underline;">${replaced.replaceAll('__', '')}</span>`;
        })
        .replaceAll(/\~[^\~]*\~/g, function(replaced, position) {
            return `<span style="text-decoration: line-through;">${replaced.replaceAll('~', '')}</span>`;
        })
        .replaceAll(/\/\/([^\/]*|(\/(?!\/)))*\/\//g, function(replaced, position) {
            return `<span style="font-style: italic;">${replaced.replaceAll('//', '')}</span>`;
        })
        .replaceAll(/\|\|([^\|]*|(\|(?!\|)))*\|\|/g, function(replaced, position) {
            return `<span class="spoiler">${replaced.replaceAll('||', '')}</span>`;
        })
        // no recursion regex: \{[a-zA-Z\-]+\}\{([^\}])*\}
        .replaceAll(/\{[a-zA-Z\-]+\}\{(?<=\{)(?:[^{}]+|\{[^}]+\})+(?=\})\}/g, function(replaced, position) {
            replaced = replaced.substring(1, replaced.length - 1);
            let [keyword, ...text] = replaced.split('}{');
            text = text.join('}{');
            if(COLORS[keyword]) {
                return `<span style="color: ${COLORS[keyword.toLowerCase()] ?? 'inherit'};">${parseCustomMarkup(text, recursiveColors, false)}</span>`;
            }
            return `{${replaced}}`;
        })
    ;
}