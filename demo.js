YUI().use("io-base", "event-delegate", "node-base", "panel", function (Y) {

    var panel = new Y.Panel({
        srcNode : '#panel',
        width   : 950,
        modal   : true,
        centered: true,
        visible: false
    });
    panel.render();


    Y.all(".folder-link").on("click", function (e) {
        e.preventDefault();
        var params = (e.currentTarget.get("href").split("?")[1].split("&"));
        var config = {};
        Y.one("#content").setContent("<em>請稍待...</em>");
        Y.each(params, function (param) {
            var o = param.split("=");
            config[o[0]] = o[1];
        });
        var url = "proxy.php?device_id=" + config["did"] + "&fullfilename=/" + config["s"] + "/miiiCasa_Photos" + decodeURIComponent(config["d"]);
        Y.io(url, {
            on: {
                success: function (a, o) {
                    Y.one("#content").setContent(o.responseText);
                }
            }
        });
        window.scrollTo(0, 0);

    });

    Y.delegate("click", function (e) {

        e.preventDefault();
        var node = e.currentTarget;

        panel.set("headerContent", node.get("title"));
        panel.set("bodyContent", "<img src=\"" + node.get("href") + "\">");
        panel.show();

    }, "#content", ".photo-link");

});
