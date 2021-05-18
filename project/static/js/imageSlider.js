$(document).ready(function () {

    //Config
    var speed = 3000;
    var width = 720;
    var pause = 5000;
    var currentSlide = 1;
    //DOM Cache
    var $sliderContainer = $("#image-slider");
    var $slideList = $sliderContainer.find(".slides");
    var $image = $slideList.find(".image");
    console.log($image.length);

    setInterval(function () {
        $slideList.animate({ 'margin-left': '-=' + width }, speed, function () {
            currentSlide++;
            if (currentSlide === $image.length) {
                currentSlide = 1;
                $slideList.css('margin-left', 0)
            }
        });
    }, pause);

});