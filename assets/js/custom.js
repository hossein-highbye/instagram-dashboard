jQuery(document).ready(function($) {
    var currentUrl = window.location.pathname;

    var lastPart = currentUrl.substring(currentUrl.lastIndexOf('/') + 1);

    if (lastPart === 'user_dashboard.php') {
        $('#dashboard').addClass('active');
    }
});