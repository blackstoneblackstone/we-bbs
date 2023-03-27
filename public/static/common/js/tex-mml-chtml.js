window.MathJax = {
    tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']]
    },
    svg: {
        fontCache: 'global'
    },
    options: {
        enableMenu: false
    }
};

(function () {
    var script = document.createElement('script');
    script.src = 'https://cdn.bootcss.com/mathjax/3.2.2/es5/tex-mml-chtml.js';
    script.async = true;
    document.head.appendChild(script);
})();