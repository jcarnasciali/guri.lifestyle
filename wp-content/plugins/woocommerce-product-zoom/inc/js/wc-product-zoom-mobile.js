/**
 * 
 * Put all gallery images to one thumbs div and the main image to another separate div. 
 * The path to full-size image has to be in 'data-full' attribute.
 * Then call $('#your-main-image-div').gallery( {
 *      thumbs: 'your-thumbs-div-id',
 *      zoomFactor: 2.5 - any number, 3 by default
 *      zoomWidth: 1 is the factor of main div image width,
 *      zoomHeight: 1 so this and the previous are the aspect ratio,
 *      zoomPosition: right/left/top/bottom/overlay,
 *      zoomBackground: #fff (only "#hex-color"),
 *      zoomBorder: 1px solid #ccc (right like in original css),
 *      lensBorder: 1px solid #ccc (right like in original css),
 *      maxImagesPerRow: 4
 * });
 * 
 * Example:
 * <div id="gallery-main">
 *      <img src="/uploads/2016/11/img1-medium.png" data-full="/uploads/2016/11/img1-full.png" />
 * </div>
 * <div id="gallery-thumbs">
 *      <img src="/uploads/2016/11/img1-medium.png" data-full="/uploads/2016/11/img1-full.png" />
 *      <img src="/uploads/2016/11/img2-medium.png" data-full="/uploads/2016/11/img3-full.png" />
 *      <img src="/uploads/2016/11/img2-medium.png" data-full="/uploads/2016/11/img4-full.png" />
 * </div>
*/

"use strict"; 

(function($) {

    //html elements
    var $mainDiv = '';
    var $mainDivImage = '';
    var $galleryImagesLinks = '';
    var $thumbsDiv = '';
    var $thumbsImages = '';
    var $thumbsDivSections = '';
    var $thumbsImagesDivs = '';
    var $zoomWindow = "";
    var $zoomLens = "";
    var $zoomTip = '';
    var $zoomTipFixed = '';
    var $zoomMovementTipFixed = '';
    var $variationsSelect = "";

    //parameters received from server
    var zoomWidth = 0;
    var zoomHeight = 0;
    var zoomPosition = {};
    var zoomBorder = '';
    var lensBorder = '';
    var zoomFactor = 0;
    var isOverlayed = false;
    var zoomBorderWidth = 3;
    var lensBorderWidth = 1;
    var maxImagesPerRow = 4;
    var productID = 0;
    var variationsArr = {};

    //calculated parameters
    var zoomedWidth = 0;
    var zoomedHeight = 0;
    var lensWidth = 0;
    var lensHeight = 0;
    var isZoomded = false;
    var cssTransitionDuration = 120;
    var jqueryTransitionDuration = 250;
    var isSrcsetAndSizesCopied = false;
    var mouseX, mouseY, prevMouseX, prevMouseY;
    var maxZoomWidthOrHeight;
    var screenWidth, screenHeight, screenAspectRatio;
    var zoomX = 0, zoomY = 0;
    var maxThumbHeight = 87;
    var verticalAlignTickCnt = 0;

    var params = {};

    /**
     * init plugin
     */
    function init(prms) {
        params = prms;

        $mainDiv = $('#' + params.main_div);
        $mainDivImage = $mainDiv.children('img');
        $thumbsDiv = $('#' + params.thumbs);
        $thumbsDivSections = $thumbsDiv.children('section');
        $thumbsImagesDivs = $thumbsDiv.children('section').children('div');
        $thumbsImages = $thumbsImagesDivs.children('img');
        $variationsSelect = $(".variations select");
        productID = /\d+/.exec($(".product").attr("id"))[0];

        maxImagesPerRow = (+params.maxImagesPerRow > 0) ? +params.maxImagesPerRow : $thumbsImagesDivs.length;
        
        //always overlayed for mobile devices
        isOverlayed = true;
        
        maxZoomWidthOrHeight = Math.max(zoomWidth, zoomHeight);

        zoomWidth = screenWidth / $mainDivImage.width();
        zoomHeight = screenHeight / $mainDivImage.height();                            
        zoomBorder = undefined !== params.zoomBorder ? params.zoomBorder : '3px solid #ddd';
        zoomFactor = (undefined !== params.zoomFactor || params.zoomFactor > 1) ? params.zoomFactor : 3;

        setScreenParams();

        resizeThumbs();
        mainDivHeightMaxOfThumbs();
        addZoomWindowAndLens();
        mainDivImageVerticalAlign();
        addTipDiv();
        addEventListeners();
    }

    /**
     * Replace the main image and zoomed image respectively (even if it is hidden) when thumb is clicked or if variations changed.
     */
    function toggleZoomWindowAndZoomImage(e) {
        
        if($variationsSelect.length !== 0 && $(this).parent().parent().attr("id") !== "wcpz-thumbs") {
            jQuery.ajax({
                url: wcpz_ajax.ajax_url,
                data: {
                    action: 'wcpz_get_wc_variations',
                    id: productID
                },
                type: 'POST',
                success: function(response) {
                    variationsArr = JSON.parse(response);
                    var fullImgSrc = "";
                    var currentMediumImgSrc = "";
                    
                    //is the 'Choose variant' option is selected
                    if($variationsSelect.children("option:selected").val() === "") {
                        fullImgSrc = variationsArr[Object.keys(variationsArr)[0]]["full"];
                        currentMediumImgSrc = variationsArr[Object.keys(variationsArr)[0]]["large"];
                    } else {
                        fullImgSrc = variationsArr[$variationsSelect.children("option:selected").val()]["full"];
                        currentMediumImgSrc = variationsArr[$variationsSelect.children("option:selected").val()]["large"];                        
                    }

                    $mainDivImage.attr('data-full', fullImgSrc);      
                    $mainDivImage.data('full', fullImgSrc);      
                    //after thumb click main image should automatically adjust its width
                    $mainDivImage.width("100%");
                    $mainDivImage.attr('src', currentMediumImgSrc);
                    $zoomWindow.css('background-image', 'url(' + currentMediumImgSrc + ')');

                    //and vertical align
                    putZoomedImageToBackground(fullImgSrc);
                    mainDivImageVerticalAlign();
                    toggleTips();
                }
            });
        } else {
            var $currentImg = $(this).children("img");
            var currentMediumImgSrc = $currentImg.attr('src');
            var fullImgSrc = $currentImg.data('full');

            //after thumb click main image should automatically adjust its width
            $mainDivImage.width("100%");
            
            $thumbsImagesDivs.removeClass("wcpz-thumb-active");
            $(this).addClass("wcpz-thumb-active");

            $mainDivImage.attr('src', currentMediumImgSrc);
            $mainDivImage.data('full', fullImgSrc);
            
            $zoomWindow.css('background-image', 'url(' + $mainDivImage.attr('src') + ')');

            if(isZoomded)
                toggleZoomWindow(e);
            $zoomLens.hide();

            //and vertical align
            mainDivImageVerticalAlign();
            toggleTips();
        }
    }

    //add the zoom window at the start
    function addZoomWindowAndLens() {
        $("body").append('<div id="zoom-window" style="display:none;"><img style="display:none;" /></div>');
        $zoomWindow = $('#zoom-window');
        $mainDiv.prepend('<div id="zoom-lens" style="display:none;"></div>');
        $zoomLens = $('#zoom-lens');       

        zoomBorderWidth = + params.zoomBorder.split(" ")[0].slice(0, -2);
        lensBorderWidth = + params.lensBorder.split(" ")[0].slice(0, -2);
    }

    /**
     * Add the zoom window and the zoom lens
     */
    function toggleZoomWindow(e) { 
        var fullImgSrc = $mainDivImage.data('full');
        isZoomded = !isZoomded; 
        
        var zoomWidthLocal = zoomWidth;
        var zoomHeightLocal = zoomHeight;

        toggleTips(e);

        //zoomed image async loading      
        putZoomedImageToBackground(fullImgSrc);
        
        if(isZoomded)
            $zoomWindow.toggle();

        $zoomWindow.animate({
            opacity: isZoomded ? 1 : -0.5,
            width: isZoomded ? screenWidth : 0,
            height: isZoomded ? screenHeight : 0
        }, {
            duration: jqueryTransitionDuration,
            easing: "easeInOutQuart",
            progress: function() {
                setZoomWindowPosition(e);
                zoomX = zoomedWidth / 2 - screenWidth / 2;
                zoomY = zoomedHeight / 2 - screenHeight / 2;
                $zoomWindow.css('background-position', '-' + zoomX + 'px ' + '-' + zoomY + 'px');
            },
            complete: function() {
                if(!isZoomded)
                    $zoomWindow.toggle();
            }
        });
        
    }

    /**
     * Toggle a zoom lens
     */
    function toggleZoomLens(e) {        
        var lensWidth, lensHeight;
        var imageAspectRatio = $mainDivImage.width() / $mainDivImage.height();
        
        lensWidth = screenWidth / zoomedWidth * screenWidth;
        lensHeight = screenHeight / zoomedHeight * screenHeight;

        if(isZoomded)
            $zoomLens.toggle();

        $zoomLens.animate({
            width: isZoomded ? lensWidth : 0,
            height: isZoomded ? lensHeight : 0,
            opacity: isZoomded ? 1 : 0,
        }, {
            duration: jqueryTransitionDuration, 
            easing: "easeInOutQuart",
            progress: function() {
            },
            complete: function() {
                if(!isZoomded)
                    $zoomLens.toggle();
            }
        });
    }

    /**
     *  Set position of a zoom window while toggling if it is overlaying
     */
    function setZoomWindowPosition(e) {

        var windowHalfW = screenWidth / 2;
        var windowHalfH = screenHeight / 2;
          
        var leftEdge = 0;
        var topEdge = 0;
        var rightEdge = screenWidth - zoomBorderWidth;
        var bottomEdge = screenHeight - zoomBorderWidth;

        var leftCss = e.clientX;
        var topCss = e.clientY;

        if(leftCss + $zoomWindow.width() > rightEdge)
            leftCss = rightEdge - $zoomWindow.width() - zoomBorderWidth;
        if(leftCss < leftEdge)
            leftCss = leftEdge;
        if(topCss + $zoomWindow.height() > bottomEdge)
            topCss = bottomEdge - $zoomWindow.height() - zoomBorderWidth;
        if(topCss < topEdge)
            topCss = topEdge;

        $zoomWindow.css({
            left: leftCss + 'px',
            top: topCss + 'px'
        });
    } 

    /**
     * Move zoom on the zoomed image
     */
    function moveZoom(e) {
        
        var lensHalfW = $zoomLens.width() / 2;
        var lensHalfH = $zoomLens.height() / 2;

        var leftCss, topCss, leftEdge, topEdge, rightEdge, bottomEdge,
            marginLeftRight = 0, marginTopBottom = 0;

        leftEdge = zoomBorderWidth;
        topEdge = zoomBorderWidth;
        rightEdge = zoomedWidth;
        bottomEdge = zoomedHeight;

        zoomX += mouseX - screenWidth / 2;
        zoomY += mouseY - screenHeight / 2;

        if(zoomX < leftEdge)
            zoomX = leftEdge;
        if(zoomX + $zoomWindow.width() > rightEdge)
            zoomX = rightEdge - $zoomWindow.width();
        if(zoomY < topEdge)
            zoomY = topEdge;
        if(zoomY + $zoomWindow.height() > bottomEdge)
            zoomY = bottomEdge - $zoomWindow.height();

        $zoomWindow.css('background-position', '-' + zoomX + 'px ' + '-' + zoomY + 'px');
    } 

    /**
     * Zoomed image in a zoom window actually is a background
     */
    function putZoomedImageToBackground(fullImgSrc) {
        var downloadingImage = new Image();
        downloadingImage.onload = function(){            
            $zoomWindow.css('background-image', 'url(' + $mainDivImage.data('full') + ')');
            mainDivImageVerticalAlign();
        };
        downloadingImage.src = fullImgSrc;
    }

    /**
     * Add a tip sticked to the top-right and bottom-right corners
     */
    function addTipDiv() {
        $mainDiv.prepend('<p id="zoom-tip">Tap to zoom</p>');
        $zoomTip = $('#zoom-tip');
        $zoomWindow.append('<p id="zoom-tip-fixed">Tap here to close</p>');
        $zoomTipFixed = $('#zoom-tip-fixed');
        $zoomWindow.append('<p id="zoom-movement-tip-fixed">Tap the picture to move it</p>');
        $zoomMovementTipFixed = $('#zoom-movement-tip-fixed');
    }

    /**
     * Toggle a tips
     */
    function toggleTips(e) {
        $mainDivImage.removeAttr("title");
        var leftCss, topCss, leftCssFixed, topCssFixed, topCssMovementFixed;
        
        leftCss = $mainDivImage.position().left + $mainDivImage.width() - $zoomTip.width() - Number($zoomTip.css("padding-left").slice(0, -2)) * 2;
        topCss = $mainDivImage.position().top;
        topCssFixed = zoomBorderWidth;
        topCssMovementFixed = screenHeight - zoomBorderWidth - Math.abs($zoomMovementTipFixed.height()) - Number($zoomMovementTipFixed.css("padding-top").slice(0, -2)) * 2;

        if($mainDivImage.css('margin').indexOf("auto") == -1) {
            leftCss += Number($mainDivImage.css('margin-left').slice(0, -2));
            topCss += Number($mainDivImage.css('margin-top').slice(0, -2));
        }

        if(isZoomded) {
            $zoomTip.hide(cssTransitionDuration);
            $zoomTipFixed.show(cssTransitionDuration);
            $zoomMovementTipFixed.show(cssTransitionDuration);
        } else {
            $zoomTip.show(cssTransitionDuration);
            $zoomTipFixed.hide(cssTransitionDuration);
            $zoomMovementTipFixed.hide(cssTransitionDuration);
        }

        $zoomTip.css({
            left: leftCss + 'px',
            top: topCss + 'px'
        });

        $zoomTipFixed.css({
            right: zoomBorderWidth + 'px',
            top: topCssFixed + 'px'
        });

        $zoomMovementTipFixed.css({
            right: zoomBorderWidth + 'px',
            top: topCssMovementFixed + 'px'
        });
    }
    /*
    *
     * Utility function - allow us to store mouse position
     */
    function mouseTracking(e) {
        if(mouseX !== undefined && mouseY !== undefined) {
            prevMouseX = mouseX;
            prevMouseY = mouseY;
        }
        mouseX = e.clientX;
        mouseY = e.clientY;
    }

    /**
     * Make the height of a main image constant and equals to the biggest one of thumbs
     */
    function mainDivHeightMaxOfThumbs() {
        var maxOfThumbs = 0;
        $thumbsImages.each(function() {
            if(maxOfThumbs < $(this).attr("data-height") * $mainDivImage.width() / $(this).attr("data-width"))
                maxOfThumbs = $(this).attr("data-height") * $mainDivImage.width() / $(this).attr("data-width");
        });
        $mainDiv.height(maxOfThumbs);
        mainDivImageVerticalAlign();
    }

    /**
     * Main image vertical align
     */
    function mainDivImageVerticalAlign() { 
        var contHeight = $mainDiv.height();
        var imgHeight = $mainDivImage.height();
        $mainDivImage.css('top', (contHeight - imgHeight) / 2 + 'px');        
        verticalAlignTickCnt ++;
        //make a vertical align for 10 sec
        if(verticalAlignTickCnt < 500) {
            setTimeout(mainDivImageVerticalAlign, 200);
        }

    }

    /**
     * Set screen params
     */
    function setScreenParams() {
        screenWidth = $(window).width();
        screenHeight = $(window).height();
        screenAspectRatio = screenWidth / screenHeight;
    }

    /**
     * Set the size each time some image is zoomed
     */
    function setZoomedImageSize() {
        
        zoomWidth = screenWidth / $mainDivImage.width();
        zoomHeight = screenHeight / $mainDivImage.height();
        maxZoomWidthOrHeight = Math.max(zoomWidth, zoomHeight);

        zoomedWidth = $mainDivImage.width() *  zoomFactor * maxZoomWidthOrHeight;
        zoomedHeight = $mainDivImage.height() * zoomFactor * maxZoomWidthOrHeight;

        $("#zoom-window").css("background-size", zoomedWidth + 'px ' + zoomedHeight + 'px');
    }

    /**
     * Resize thumbs respective to a container size
     */
    function resizeThumbs() {
        var activeThumbWidth = 100 * 1.1 / maxImagesPerRow;
        var thumbWidth = (100 - activeThumbWidth) / (maxImagesPerRow - 1);
        var thumbHeight = $thumbsDiv.width() * activeThumbWidth / 100;
        var sectionWidth = maxImagesPerRow * maxThumbHeight;

        var divsInRowsDifference = ($($thumbsDivSections[0]).children('div').length - $($thumbsDivSections[$thumbsDivSections.length - 1]).children('div').length);

        $thumbsImagesDivs.css({
            transitionDuration: cssTransitionDuration + 'ms',
            width: thumbWidth + '%',
            height: thumbHeight + 'px',
            maxHeight: maxThumbHeight + 'px',
            maxWidth: maxThumbHeight + 'px',
        });

        $thumbsDiv.children('div.wcpz-thumb-active').css({
            width: activeThumbWidth + '%'
        });

        $thumbsImages.each(function() {
            if(thumbHeight > maxThumbHeight || thumbHeight == 0)
                thumbHeight = maxThumbHeight;
            $(this).css('margin-top', (thumbHeight - $(this).height()) / 2 + 'px');
        });

        $thumbsDivSections.width(sectionWidth);
        //if in the last row there are less thumbs than in the first, center it
        if(divsInRowsDifference != 0) {
            var theLastSectionMargin = divsInRowsDifference * thumbWidth / 2;
            $($thumbsDivSections[$thumbsDivSections.length - 1]).children('div:first-child').css('margin-left', theLastSectionMargin + '%');
        }

        $thumbsDiv.height(Math.ceil($thumbsImages.length / maxImagesPerRow) * thumbHeight);
    }

    /**
     * Remove srcset attr.
     */
    function removeSrcset() {
        //adjust width only if img has a srcset attr
        if($mainDivImage.attr("srcset") !== undefined)
            $mainDivImage.width($mainDivImage.attr("data-width"));
        
        $mainDivImage.removeAttr("srcset");
        $mainDivImage.removeAttr("sizes");
    }

    /**
     * Add event listeners
     */
    function addEventListeners() {    

        $thumbsImagesDivs.on('click', toggleZoomWindowAndZoomImage);
        $thumbsImagesDivs.on('click', setZoomedImageSize);

        $mainDivImage.on('click', toggleZoomWindow);
        $mainDivImage.on('click', setZoomedImageSize);

        $zoomWindow.on('click', moveZoom);

        $zoomTipFixed.on('click', toggleZoomWindow);
        $zoomTipFixed.on('click', setZoomedImageSize);
        $zoomTipFixed.on('click', toggleZoomLens); 

        $('body').on('change', '.variations select', removeSrcset);  
        $('body').on('change', '.variations select', setZoomedImageSize);  
        $('body').on('change', '.variations select', toggleZoomWindowAndZoomImage);  

        $(document).on('mousemove', mouseTracking);

        $(window).resize(function() {
            resizeThumbs();
            mainDivHeightMaxOfThumbs();
            setScreenParams();
            mainDivImageVerticalAlign();
            toggleTips();
        });

        $(window).load(function() {
            removeSrcset();
            $("#wcpz-thumbs section div.wcpz-thumb-active").click();

            toggleTips();
            mainDivImageVerticalAlign();
            resizeThumbs();
        });
    }

    /**
     * Add gallery function to jQuery
     */
    $.fn.gallery = function(params) {
        params = $.extend(params, {
            main_div: this.attr('id')
        });
        if($("#" + this.attr("id")).length > 0 && this.attr("id").indexOf("wcpz-main") != -1)
            init(params);
    };

})(jQuery);