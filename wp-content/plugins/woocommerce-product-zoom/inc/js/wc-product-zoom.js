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
    var $thumbsDivSections = '';
    var $thumbsImages = '';
    var $thumbsImagesDivs = '';
    var $zoomWindow = "";
    var $zoomLens = "";
    var $zoomTip = '';
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
    var zoomedWidth = 0; //width of zoomed background image
    var zoomedHeight = 0; //height of zoomed background image
    var lensWidth = 0;
    var lensHeight = 0;
    var isZoomded = false;
    var cssTransitionDuration = 120;
    var jqueryTransitionDuration = 250;
    var mouseX, mouseY, prevMouseX, prevMouseY;
    var maxZoomWidthOrHeight;
    var maxThumbHeight = 87;
    var verticalAlignTickCnt = 0;
    var zoomWindowPositionTimer;

    var params = {};

    /**
     * init plugin
     */
    function init(prms) {
        params = prms;
        
        maxZoomWidthOrHeight = Math.max(params.zoomWidth, params.zoomHeight);

        $mainDiv = $('#' + params.main_div);
        $mainDivImage = $mainDiv.children('img');
        $thumbsDiv = $('#' + params.thumbs);
        $thumbsDivSections = $thumbsDiv.children('section');
        $thumbsImagesDivs = $thumbsDiv.children('section').children('div');
        $thumbsImages = $thumbsImagesDivs.children('img');
        $variationsSelect = $(".variations select");
        productID = /\d+/.exec($(".product").attr("id"))[0];

        maxImagesPerRow = (+params.maxImagesPerRow > 0) ? +params.maxImagesPerRow : $thumbsImagesDivs.length;

        if(params.zoomPosition === 'overlay')
            isOverlayed = true;
              
        zoomWidth = (undefined !== params.zoomWidth) ? params.zoomWidth : 1;
        zoomHeight = (undefined !== params.zoomHeight) ? params.zoomHeight : 1;
        zoomBorder = undefined !== params.zoomBorder ? params.zoomBorder : '3px solid #ddd';
        zoomFactor = (undefined !== params.zoomFactor || params.zoomFactor > 1) ? params.zoomFactor : 3;        

        resizeThumbs();
        mainDivHeightMaxOfThumbs();
        addZoomWindowAndLens();
        addTipDiv();
        mainDivImageVerticalAlign();
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
        }
    }
    
    /**
     * Add the zoom window and the zoom lens
     */
    function addZoomWindowAndLens() {
        $("body").append('<div id="zoom-window" style="display:none;"><img style="display:none;" /></div>');
        $zoomWindow = $('#zoom-window');
        $mainDiv.prepend('<div id="zoom-lens" style="display:none;"></div>');
        $zoomLens = $('#zoom-lens');       

        zoomBorderWidth = + params.zoomBorder.split(" ")[0].slice(0, -2);
        lensBorderWidth = + params.lensBorder.split(" ")[0].slice(0, -2);
    }

    /**
     * Toggle the zoom window
     */
    function toggleZoomWindow(e) { 
        var fullImgSrc = $mainDivImage.data('full');
        isZoomded = !isZoomded; 

        //remove listeners in different moments to exclude a double firing
        if(isZoomded) {           
            $mainDivImage.off('click', toggleAndMoveTip);
            $mainDivImage.off('click', toggleZoomWindow);
            $mainDivImage.off('click', toggleZoomLens);
            
            $zoomLens.on('click', toggleAndMoveTip);
            $zoomLens.on('click', toggleZoomWindow);
            $zoomLens.on('click', toggleZoomLens);
        } else {         
            $mainDivImage.on('click', toggleAndMoveTip);
            $mainDivImage.on('click', toggleZoomWindow);
            $mainDivImage.on('click', toggleZoomLens);
            
            $zoomLens.off('click', toggleAndMoveTip);
            $zoomLens.off('click', toggleZoomWindow);
            $zoomLens.off('click', toggleZoomLens);
        }

        //zoomed image async loading      
        putZoomedImageToBackground(fullImgSrc);
        
        if(isZoomded) {
            $zoomWindow.toggle();
            zoomWindowPositionTimer = setInterval(function() {
                if(isOverlayed) {
                    setZoomWindowPositionIfOverlay(e);
                } else {                    
                    setZoomWindowPosition();
                }
            }, 100);
        }

        $zoomWindow.animate({
            opacity: isZoomded ? 1 : -0.5,
            width: isZoomded ? zoomWidth * $mainDivImage.width() : 0,
            height: isZoomded ? zoomHeight * $mainDivImage.height() : 0
        }, {
            duration: jqueryTransitionDuration,
            easing: "easeInOutQuart",
            progress: function() {
                if(isOverlayed) {
                    setZoomWindowPositionIfOverlay(e);
                } else {                    
                    setZoomWindowPosition();
                }
            },
            complete: function() {
                if(!isZoomded) {
                    $zoomWindow.toggle();
                    clearInterval(zoomWindowPositionTimer);
                }
            }
        });
        
    }

    /**
     * Toggle a zoom lens
     */
    function toggleZoomLens(e) {        
        var lensWidth, lensHeight;

        if(zoomWidth / zoomHeight >= 1) {
            lensWidth = $mainDivImage.width() / zoomFactor;
            lensHeight = $mainDivImage.height() / zoomWidth * zoomHeight / zoomFactor;
            if(isOverlayed) {
                lensWidth = $mainDivImage.width() * zoomWidth / zoomFactor;
                lensHeight = $mainDivImage.height() * zoomHeight / zoomWidth * zoomHeight / zoomFactor;
            }
        } else {
            lensWidth = $mainDivImage.width() / zoomHeight * zoomWidth / zoomFactor;
            lensHeight = $mainDivImage.height() / zoomFactor;
            if(isOverlayed) {
                lensWidth = $mainDivImage.width() * zoomWidth / zoomHeight * zoomWidth / zoomFactor;
                lensHeight = $mainDivImage.height() * zoomHeight / zoomFactor;
            }
        }

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
                stickZoomLensToCursor({
                    pageX: mouseX,
                    pageY: mouseY
                });
                moveZoom();
            },
            complete: function() {
                if(!isZoomded)
                    $zoomLens.toggle();
            }
        });
    }

    /**
     * Set position of a zoom window if NOT overlaying, 'right' by default
     */
    function setZoomWindowPosition() {
        var marginLeftRight = ($mainDiv.width() - $mainDivImage.width()) / 2;
        var marginTopBottom = ($mainDiv.height() - $mainDivImage.height()) / 2;
        
        zoomPosition = {
            top: $mainDiv.offset().top + marginTopBottom + $mainDivImage.height() / 2 - $zoomWindow.height() / 2 - zoomBorderWidth,
            left: $mainDiv.width() + $mainDiv.offset().left
        };

        switch(params.zoomPosition) {
            case 'left':
                zoomPosition.left = -zoomWidth * $mainDivImage.width() + $mainDiv.offset().left;
            break;
            case 'top':
                zoomPosition.top = -zoomHeight * $mainDivImage.height() + $mainDiv.offset().top;
                zoomPosition.left = $mainDiv.offset().left + marginLeftRight + $mainDivImage.width() / 2 - $zoomWindow.width() / 2 - zoomBorderWidth;
                //and see if we went out of border on the left side
                if(zoomPosition.left < $mainDiv.offset().left)
                    zoomPosition.left = $mainDiv.offset().left;
            break;
            case 'bottom':
                zoomPosition.top = $mainDiv.height() + $mainDiv.offset().top;
                zoomPosition.left = $mainDiv.offset().left + marginLeftRight + $mainDivImage.width() / 2 - $zoomWindow.width() / 2 - zoomBorderWidth;
                //and see if we went out of border on the left side
                if(zoomPosition.left < $mainDiv.offset().left)
                    zoomPosition.left = $mainDiv.offset().left;
            break; 
        }

        $zoomWindow.css("left", zoomPosition.left);
        $zoomWindow.css("top", zoomPosition.top);
    }

    /**
     *  Set position of a zoom window while toggling if it is overlaying
     */
    function setZoomWindowPositionIfOverlay(e) {

        var windowHalfW = $zoomWindow.width() / 2;
        var windowHalfH = $zoomWindow.height() / 2;

        var marginLeftRight = 0;
        var marginTopBottom = 0;
        
        if(Number($mainDivImage.css('margin-left').slice(0, -2)) != 0) {
            marginLeftRight += Number($mainDivImage.css('margin-left').slice(0, -2));
        }

        if($mainDivImage.css("position") == "relative") {
            if($mainDivImage.css("left").indexOf("auto") == -1) {
                marginLeftRight += Number($mainDivImage.css('left').slice(0, -2));
            } 
            if($mainDivImage.css("top").indexOf("auto") == -1) {
                marginTopBottom += Number($mainDivImage.css('top').slice(0, -2));
            } 
        }
        
        var leftEdge = $mainDiv.offset().left + marginLeftRight;
        var topEdge = $mainDiv.offset().top + marginTopBottom;
        var rightEdge = $mainDiv.offset().left + $mainDivImage.width() - zoomBorderWidth + marginLeftRight;
        var bottomEdge = $mainDiv.offset().top + $mainDivImage.height() - zoomBorderWidth + marginTopBottom;

        var leftCss = e.pageX - windowHalfW - marginLeftRight;
        var topCss = e.pageY - windowHalfH - marginTopBottom;

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
     * Stick a zoom lens to a cursor
     */
    function stickZoomLensToCursor(e) {

        var lensHalfW = $zoomLens.width() / 2;
        var lensHalfH = $zoomLens.height() / 2;

        var leftCss, topCss, leftEdge, topEdge, rightEdge, bottomEdge,
            marginLeftRight = 0, marginTopBottom = 0;

        if(Number($mainDivImage.css('margin-left').slice(0, -2)) != 0) {
            marginLeftRight += Number($mainDivImage.css('margin-left').slice(0, -2));
        }
        if(Number($mainDivImage.css('margin-top').slice(0, -2)) != 0) {
            marginTopBottom += Number($mainDivImage.css('margin-top').slice(0, -2));
        }
        

        if(isOverlayed) {  
            leftEdge = zoomBorderWidth + $mainDivImage.position().left + marginLeftRight;
            topEdge = zoomBorderWidth + $mainDivImage.position().top + marginTopBottom;
            rightEdge = $zoomWindow.width() + zoomBorderWidth - lensBorderWidth + $mainDivImage.position().left + marginLeftRight;
            bottomEdge = $zoomWindow.height() + zoomBorderWidth - lensBorderWidth + $mainDivImage.position().top + marginTopBottom;

            leftCss = +e.pageX + $mainDivImage.position().left - $mainDivImage.offset().left + marginLeftRight - lensHalfW - lensBorderWidth;
            topCss = +e.pageY + $mainDivImage.position().top - $mainDivImage.offset().top + marginTopBottom - lensHalfH - lensBorderWidth; 
        } else {
            leftEdge = $mainDivImage.position().left + marginLeftRight;
            topEdge = $mainDivImage.position().top + marginTopBottom;
            rightEdge = $mainDivImage.position().left + $mainDivImage.width() + marginLeftRight - lensBorderWidth;
            bottomEdge = $mainDivImage.position().top + $mainDivImage.height() + marginTopBottom - lensBorderWidth;

            leftCss = +e.pageX + $mainDivImage.position().left - $mainDivImage.offset().left + marginLeftRight - lensHalfW - lensBorderWidth;
            topCss = +e.pageY + $mainDivImage.position().top - $mainDivImage.offset().top + marginTopBottom - lensHalfH - lensBorderWidth;
        }

        if(leftCss < leftEdge)
            leftCss = leftEdge;
        if(leftCss + $zoomLens.width() + lensBorderWidth > rightEdge)
            leftCss = rightEdge - $zoomLens.width() - lensBorderWidth;
        if(topCss < topEdge)
            topCss = topEdge;
        if(topCss + $zoomLens.height() + lensBorderWidth > bottomEdge)
            topCss = bottomEdge - $zoomLens.height() - lensBorderWidth;

        $zoomLens.css({
            left: leftCss + 'px',
            top: topCss + 'px'
        });
    }

    /**
     * Move zoom on the zoomed image
     */
    function moveZoom(e) {

        var zoomX, zoomY;
        var marginLeftRight = 0, marginTopBottom = 0;

        if($mainDivImage.css('margin').indexOf("auto") == -1) {
            marginLeftRight += Number($mainDivImage.css('margin-left').slice(0, -2));
            marginTopBottom += Number($mainDivImage.css('margin-top').slice(0, -2));
        }

        if(isOverlayed) {
            if(zoomWidth >= zoomHeight) {
                zoomX = (+$zoomLens.css('left').slice(0, -2) - $mainDivImage.position().left - marginLeftRight) * zoomFactor;
                zoomY = (+$zoomLens.css('top').slice(0, -2) - $mainDivImage.position().top - marginTopBottom) * zoomFactor * (zoomWidth / zoomHeight);
            } else {
                zoomX = (+$zoomLens.css('left').slice(0, -2) - $mainDivImage.position().left - marginLeftRight) * zoomFactor * (zoomHeight / zoomWidth);
                zoomY = (+$zoomLens.css('top').slice(0, -2) - $mainDivImage.position().top - marginTopBottom) * zoomFactor;
            }
        } else {
            zoomX = (+$zoomLens.css('left').slice(0, -2) - $mainDivImage.position().left - marginLeftRight) * zoomFactor * maxZoomWidthOrHeight + 1;
            zoomY = (+$zoomLens.css('top').slice(0, -2) - $mainDivImage.position().top - marginTopBottom) * zoomFactor * maxZoomWidthOrHeight;
        }

        $zoomWindow.css('background-position', '-' + zoomX + 'px ' + '-' + zoomY + 'px');
    } 

    /**
     * Zoomed image in a zoom window actually is a background
     */
    function putZoomedImageToBackground(fullImgSrc) {
        $zoomWindow.css('background-image', 'url(' + $mainDivImage.attr('src') + ')');
        var downloadingImage = new Image();
        downloadingImage.onload = function(){            
            $zoomWindow.css('background-image', 'url(' + $mainDivImage.data('full') + ')');
            mainDivImageVerticalAlign();
        };
        downloadingImage.src = fullImgSrc;
    }

    /**
     * Add a tip sticked to a cursor
     */
    function addTipDiv() {
        $mainDiv.prepend('<p id="zoom-tip">Click to zoom</p>');
        $zoomTip = $('#zoom-tip');
    }

    /**
     * Toggle a tip and move it with a cursor
     */
    function toggleAndMoveTip(e) {
        $mainDivImage.removeAttr("title");

        var leftCss, topCss, leftEdge, topEdge, rightEdge, bottomEdge,
            marginLeftRight = 0, marginTopBottom = 0;

        if(Number($mainDivImage.css('margin-left').slice(0, -2)) != 0) {
            marginLeftRight += Number($mainDivImage.css('margin-left').slice(0, -2));
        }
        if(Number($mainDivImage.css('margin-top').slice(0, -2)) != 0) {
            marginTopBottom += Number($mainDivImage.css('margin-top').slice(0, -2));
        }
        
        leftEdge = $mainDivImage.position().left + marginLeftRight;
        topEdge = $mainDivImage.position().top + marginTopBottom - $zoomTip.height() / 2;
        rightEdge = $mainDivImage.position().left + $mainDivImage.width() + marginLeftRight;
        bottomEdge = $mainDivImage.position().top + $mainDivImage.height() + marginTopBottom - $zoomTip.height() / 2;

        leftCss = +e.pageX + $mainDivImage.position().left - $mainDivImage.offset().left + marginLeftRight;
        topCss = +e.pageY + $mainDivImage.position().top - $mainDivImage.offset().top + marginTopBottom - $zoomTip.height() / 2;

        if(e.type == 'mousemove') {
            if(leftCss < leftEdge || $zoomLens.css('display') == 'block')
                $zoomTip.css('display', "none");
            else if(leftCss > rightEdge || $zoomLens.css('display') == 'block')
                $zoomTip.css('display', "none");
            else if(topCss < topEdge || $zoomLens.css('display') == 'block')
                $zoomTip.css('display', "none");
            else if(topCss > bottomEdge || $zoomLens.css('display') == 'block')
                $zoomTip.css('display', "none");
            else 
                $zoomTip.css('display', "block");
        } else if(e.type = "click") {
            $zoomTip.toggle();
        }

        $zoomTip.css({
            left: leftCss + 'px',
            top: topCss + 'px'
        });
    }

    /**
     * Utility function - allow us to look if mouse not moving
     */
    function mouseTracking(e) {
        if(mouseX !== undefined && mouseY !== undefined) {
            prevMouseX = mouseX;
            prevMouseY = mouseY;
        }
        mouseX = e.pageX;
        mouseY = e.pageY;
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
     * Set the size each time some image is zoomed
     */
    function setZoomedImageSize() {
        if(isOverlayed) {
            zoomedWidth = $mainDivImage.width() *  zoomFactor * maxZoomWidthOrHeight;
            zoomedHeight = $mainDivImage.height() * zoomFactor * maxZoomWidthOrHeight;
        } else {
            zoomedWidth = $mainDivImage.width() * zoomFactor * maxZoomWidthOrHeight;
            zoomedHeight = $mainDivImage.height() * zoomFactor * maxZoomWidthOrHeight;
        }

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
        
        $mainDivImage.on('click', toggleAndMoveTip);
        $mainDivImage.on('click', toggleZoomWindow);
        $mainDivImage.on('click', toggleZoomLens);
        $mainDivImage.on('click', setZoomedImageSize);  

        $(document).on('mousemove', mouseTracking);

        $mainDivImage.on('mousemove', stickZoomLensToCursor);

        $('body').on('mousemove', toggleAndMoveTip);

        $zoomLens.on('mousemove', moveZoom);
        $zoomLens.on('mousemove', stickZoomLensToCursor);

        $zoomTip.on('click', toggleAndMoveTip);
        $zoomTip.on('click', toggleZoomWindow);
        $zoomTip.on('click', toggleZoomLens);
        $zoomTip.on('click', setZoomedImageSize);  

        $('body').on('change', '.variations select', removeSrcset);  
        $('body').on('change', '.variations select', setZoomedImageSize);  
        $('body').on('change', '.variations select', toggleZoomWindowAndZoomImage);  

        $(window).resize(function() {
            resizeThumbs();
            mainDivHeightMaxOfThumbs();
        });

        $(window).load(function() {
            removeSrcset();
            $("#wcpz-thumbs section div.wcpz-thumb-active").click();
            mainDivImageVerticalAlign();
            mainDivHeightMaxOfThumbs();
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