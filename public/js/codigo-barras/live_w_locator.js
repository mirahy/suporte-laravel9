function carregarScriptLeitorCBarras(componenteHTML,botaoAcao) {
    var App = {
        init: function() {
			App.attachStart();
        },
		attachStart: function() {
            jQuery(".controls-capture").on("click", "button.start-capture", function(e) {
                e.preventDefault();
				Quagga.init(this.state, function() {
                    App.attachListeners();
                    try {
                        Quagga.start();
                    }
                    catch(err) {
                        alert("Não foi possível ativar o recurso da câmera!\n" + err.message);
                    }
				});
				jQuery(".controls-capture").off("click", "button.start-capture");
            });
        },
        attachListeners: function() {
            jQuery(".controls-capture").on("click", "button.stop-capture", function(e) {
                e.preventDefault();
                Quagga.stop();
				App.detachListeners();
				App.init();
            });
        },
        detachListeners: function() {
            jQuery(".controls-capture").off("click", "button.stop-capture");
        },
        state: {
            inputStream: {
                type : "LiveStream",
                constraints: {
                    width: 320,
                    height: 240,
                    facing: "user" // or user
                }
            },
            locator: {
                patchSize: "medium",
                halfSample: true
            },
            numOfWorkers: 4,
            decoder: {
                readers : ["code_128_reader"]
            },
            locate: true
        },
        lastResult : null
    };

    App.init();

    Quagga.onProcessed(function(result) {
        var drawingCtx = Quagga.canvas.ctx.overlay,
            drawingCanvas = Quagga.canvas.dom.overlay;

        if (result) {
            if (result.boxes) {
                drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
                result.boxes.filter(function (box) {
                    return box !== result.box;
                }).forEach(function (box) {
                    Quagga.ImageDebug.drawPath(box, {x: 0, y: 1}, drawingCtx, {color: "green", lineWidth: 2});
                });
            }

            if (result.box) {
                Quagga.ImageDebug.drawPath(result.box, {x: 0, y: 1}, drawingCtx, {color: "#00F", lineWidth: 2});
            }

            if (result.codeResult && result.codeResult.code) {
                Quagga.ImageDebug.drawPath(result.line, {x: 'x', y: 'y'}, drawingCtx, {color: 'red', lineWidth: 3});
            }
        }
    });

    Quagga.onDetected(function(result) {
        var code = result.codeResult.code;

        if (App.lastResult !== code) {
            App.lastResult = code;
            if (confirm("Confirmar: \""+code + "\" ?")) {
                var regex = /^[0-9]{10}$/;
                //code = "0000000016"
                if (regex.test(code)){
                    jQuery(componenteHTML)[0].value = code;
                    jQuery(botaoAcao).click();
                    /*Quagga.stop();
                    App.detachListeners();
                    App.init();*/
                }
            }
            /*var $node = null, canvas = Quagga.canvas.dom.image;

            $node = jQuery('<li><div class="thumbnail"><div class="imgWrapper"><img /></div><div class="caption"><h4 class="code"></h4></div></div></li>');
            $node.find("img").attr("src", canvas.toDataURL());
            $node.find("h4.code").html(code);
            jQuery("#result_strip ul.thumbnails").prepend($node);*/
        }
    });

};
