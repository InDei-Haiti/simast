/**
 * Created by KoreLaviMAST on 2/8/2017.
 */
Highcharts.Chart.prototype.exportChartLocal = function (options) {

    var chart = this,
        svg = this.getSVG(), // Get the SVG
        canvas,
        a,
        href,
        extension,
        download = function () {

            var blob;

            // IE specific
            if (navigator.msSaveOrOpenBlob) {

                // Get PNG blob
                if (extension === 'png') {
                    blob = canvas.msToBlob();

                    // Get SVG blob
                } else {
                    blob = new MSBlobBuilder;
                    blob.append(svg);
                    blob = blob.getBlob('image/svg+xml');
                }

                navigator.msSaveOrOpenBlob(blob, 'chart.' + extension);

                // HTML5 download attribute
            } else {
                a = document.createElement('a');
                a.href = href;
                a.download = 'chart.' + extension;
                document.body.appendChild(a);
                a.click();
                a.remove();
            }
        },
        prepareCanvas = function () {
            canvas = document.createElement('canvas'); // Create an empty canvas
            window.canvg(canvas, svg); // Render the SVG on the canvas

            href = canvas.toDataURL('image/png');
            extension = 'png';
        };

    // Add an anchor and apply the download to the button
    /*if (options && options.type === 'image/svg+xml') {
        href = 'data:' + options.type + ',' + svg;
        extension = 'svg';
        download();

    } else {

        // It's included in the page or preloaded, go ahead
        if (window.canvg) {
            prepareCanvas();
            download();

            // We need to load canvg before continuing
        } else {
            this.showLoading();
            getScript(Highcharts.getOptions().global.canvasToolsURL, function () {
                chart.hideLoading();
                prepareCanvas();
                download();
            });
        }
    }*/
};
