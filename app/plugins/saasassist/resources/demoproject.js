
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");

document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));

$(document).ready(function()
{
	var cookie_name = 'devprom-demo-' + devpromOpts.project;
	if ( $.cookies.get(cookie_name) != '1' ) {
		_gat._getTracker("UA-10541243-1")._trackEvent('demo-running', devpromOpts.template);
		$.cookies.set(cookie_name, '1', {hoursToLive:16384});
	}
});

(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter25318133 = new Ya.Metrika({id:25318133,
                    webvisor:true,
                    clickmap:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");

document.write(unescape("%3Cnoscript%3E%3Cdiv%3E%3Cimg src='//mc.yandex.ru/watch/25318133' style='position:absolute; left:-9999px;' alt='' /%3E%3C/div%3E%3C/noscript%3E"));
