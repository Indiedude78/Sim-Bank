$(document).ready(function () {

    //Config
    var speed = 1500;
    var width = 1500;
    var pause = 4000;
    var currentSlide = 1;
    //DOM Cache
    var $sliderContainer = $("#image-slider");
    var $slideList = $sliderContainer.find(".slides");
    var $image = $slideList.find(".image");
    //console.log($image.length);
    var $welcomeContainer = $("#welcome-text");
    var $introductionText = $welcomeContainer.find("#introduction-header");
    var $loginText = $welcomeContainer.find("#login-header");
    //console.log($introductionText);

    var interval;

    startSlideShow();
    $sliderContainer.on('mouseenter', showText).on('mouseleave', hideText);

    function startSlideShow() {
        interval = setInterval(function () {
            $slideList.animate({ 'margin-left': '-=' + width }, speed, function () {
                currentSlide++;
                if (currentSlide === $image.length) {
                    currentSlide = 1;
                    $slideList.css('margin-left', 0)
                }
            });
        }, pause);
    }


    function showText() {
        $introductionText.slideDown();
        $loginText.slideDown();
    }

    function hideText() {
        $introductionText.slideUp();
        $loginText.slideUp();
    }


});