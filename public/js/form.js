/*!
 * Snippet from http://stackoverflow.com/a/18303822/6265296
 * to enable tab key in text area
 */
document.querySelector("textarea").addEventListener('keydown',function(e) {
    if(e.keyCode === 9) { // tab was pressed
        // get caret position/selection
        var start = this.selectionStart;
        var end = this.selectionEnd;

        var target = e.target;
        var value = target.value;

        // set textarea value to: text before caret + tab + text after caret
        target.value = value.substring(0, start)
            + "\t"
            + value.substring(end);

        // put caret at right position again (add one for the tab)
        this.selectionStart = this.selectionEnd = start + 1;

        // prevent the focus lose
        e.preventDefault();
    }
},false);

/*!
 * Tags article manager via ajax post
 */

closeButtonTag();

var taggleList = document.querySelector('.taggle_list');
taggleList.addEventListener('DOMNodeInserted', closeButtonTag);

function addTag(articleId, tagName) {
    var url = '/add/tags/';
    var data = [];
    data['articleId'] = articleId;
    data['tagName'] = tagName;
    postAjax(url, data);
}

function closeButtonTag() {
    var closeButton = document.querySelectorAll('.taggle_list .taggle .close');
    closeButton.forEach(function(el, index, arr){
        var self = el;
        var tagName = self.previousElementSibling.innerHTML;
        var articleId = document.location.pathname.split('/')[3] || null;
        if (index === arr.length - 1) {
            addTag(articleId, tagName);
        }
        var deleteTag = 'deleteTag(' + articleId + ',"' + tagName + '")';
        self.setAttribute('onclick', deleteTag);
    });
}

function deleteTag(articleId, tagName) {
    var url = '/delete/tags/';
    var data = [];
    data['articleId'] = articleId;
    data['tagName'] = tagName;
    postAjax(url, data);
}

/**
 * Post request to updating article tags
 * @param  {string}  url    url for posting data
 * @param  {array}   data   list of parameter name
 * @return {void}           return nothing
 */
function postAjax(url, data) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', url);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    var params = Object.keys(data).map(function(k) {
        return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
    }).join('&');
    xhr.send(params);
}
