$(document).ready(function () {

    //DOM Cache
    var $dashboardContainer = $('.dashboard-container');
    var $iconContainer = $('#side-icon-container');
    var $clickableButton = $iconContainer.find('#side-bar-button');
    var $icon = $('#side-bar-button .material-icons');
    console.log($icon.html());

    //Hide Side bar by default
    $dashboardContainer.hide();
    //Call function on button click
    $clickableButton.click(toggleSideBar);

    function toggleSideBar(e) {
        e.preventDefault();
        $dashboardContainer.slideToggle(200);
        if ($icon.html() == "close") {
            $icon.html("menu");
        }
        else {
            $icon.html("close");
        }
        console.log($icon.html());
    }
});