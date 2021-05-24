$(document).ready(function () {

    //DOM Cache
    var $dashboardContainer = $('.dashboard-container');
    var $iconContainer = $('#side-icon-container');
    var $clickableButton = $iconContainer.find('#side-bar-button');

    //Hide Side bar by default
    $dashboardContainer.hide();
    //Call function on button click
    $clickableButton.click(toggleSideBar);

    function toggleSideBar(e) {
        e.preventDefault();
        $dashboardContainer.slideToggle(200);
    }
});