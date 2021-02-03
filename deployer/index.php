<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Web Tester</title>
    <script>
        function init() {
            var imgDefer = document.getElementsByTagName('img');
            for (var i=0; i<imgDefer.length; i++) {
                if(imgDefer[i].getAttribute('data-src')) {
                    imgDefer[i].setAttribute('src',imgDefer[i].getAttribute('data-src'));
                } } }
        window.onload = init;
    </script>
</head>
<body>
<h1>Web Tester</h1>
<hr>
<img src="data:image/png;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="images/GOPR2773.jpg">
<img src="data:image/png;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="images/GOPR2773.jpg">
<img src="data:image/png;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="images/GOPR2773.jpg">
<img src="data:image/png;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="images/GOPR2773.jpg">
<img src="data:image/png;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="images/GOPR2773.jpg">
</body>
</html>