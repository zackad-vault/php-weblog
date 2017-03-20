renderYoutube();

function createIframe(url, className) {
    var container = document.createElement('div');
    var containerClass = document.createAttribute('class');
    containerClass.value = 'youtube-container';
    container.setAttributeNode(containerClass);
    
    var iframe = document.createElement('iframe');
    var classAttr = document.createAttribute('class');
    var frameBorder = document.createAttribute('frameborder');
    var src = document.createAttribute('src');
    var fullscreen = document.createAttribute('allowfullscreen');
    frameBorder.value = 0;
    src.value = url;
    classAttr.value = className;
    iframe.setAttributeNode(src);
    iframe.setAttributeNode(frameBorder);
    iframe.setAttributeNode(fullscreen);
    iframe.setAttributeNode(classAttr);

    container.appendChild(iframe);
    return container;
}

function renderYoutube() {
    var selector = document.querySelectorAll('.markdown-body img[alt="youtube"]');
    selector.forEach(function(item, index) {
        var ytid = item.getAttribute('src');
        var url = 'https://www.youtube.com/embed/' + ytid;
        var className = 'youtube-embed';
        var selector = document.querySelector('.markdown-body img[src="'+ytid+'"]');
        var iframe = createIframe(url, className);
        selector.parentNode.insertBefore(iframe, selector);
        selector.parentNode.removeChild(selector);
    });
}
