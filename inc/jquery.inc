<!-- jQuery and jQuery UI -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

<!-- jQuery ValidatiojnLibrary -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/additional-methods.min.js"></script>

<!-- Local CSS -->
<link rel="stylesheet" type="text/css" href="css/main.php">

<!-- Time calculation library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script>
    moment().format();
</script>

<!-- QTip Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qtip2/3.0.2/jquery.qtip.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/qtip2/3.0.2/jquery.qtip.min.css" type="text/css">

<!-- Message Box plugin -->
<script src="inc/jquery.msgbox.min.js"></script>

<!-- Porgress Bar -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css">
<script type="text/javascript" >
//NProgress.configure({ parent: '#workspace' });
NProgress.configure({ showSpinner: false });

$(document).ajaxStart(function () {
	NProgress.start();
})

$(document).ajaxStop(function () {
	NProgress.done();
});
</script>