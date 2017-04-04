/**
 * The admin panel functions within the WC Product Zoom plugin.
 */

"use strict";

jQuery(document).ready(function($) {
    //script is included to each page, so check if page is a gallery admin page
    if($("#wcpz-zoom-factor").length == 0)
        return;

    //Html elements defining
    var $zoomFactor = $("#wcpz-zoom-factor");
    var $zoomWidth = $("#wcpz-zoom-width");
    var $zoomHeight = $("#wcpz-zoom-height");
    var $imagesPerRow = $("#wcpz-max-images-per-row");

    var $zoomWindowBorderWidth = $("#wcpz-zoom-border-width");
    var $zoomWindowBorderType = $("#wcpz-zoom-border-type");
    var $zoomWindowBorderColor = $("#wcpz-zoom-border-color");
    var $zoomBorder = $("#wcpz-zoom-border");

    var $lensBorderWidth = $("#wcpz-lens-border-width");
    var $lensBorderType = $("#wcpz-lens-border-type");
    var $lensBorderColor = $("#wcpz-lens-border-color");
    var $lensBorder = $("#wcpz-lens-border");

    var $zoomBorderTable = $("#wcpz-zoom-border-width").parents("table");
    var $lensBorderTable = $("#wcpz-lens-border-width").parents("table");

    function init() {
        addListeners();
        makeAdditions();
        
        lensBorderToHidden();
        zoomBorderToHidden();
    }

    /**
     * Make additional parameters of inputs, new elements, etc.
     */
    function makeAdditions() {
        //all number inputs should have step, max and min 
        $zoomFactor.attr("min", "1");
        $zoomFactor.attr("max", "10");
        $zoomFactor.attr("step", "0.1");
         
        $zoomWidth.attr("min", "1");
        $zoomWidth.attr("max", "10");
        $zoomWidth.attr("step", "0.1");
         
        $zoomHeight.attr("min", "1");
        $zoomHeight.attr("max", "10");
        $zoomHeight.attr("step", "0.1");

        $imagesPerRow.attr("min", "1");
        $imagesPerRow.attr("max", "1000");
        $imagesPerRow.attr("step", "1");
        
        $zoomWindowBorderWidth.attr("min", "1");
        $zoomWindowBorderWidth.attr("max", "10");
        $zoomWindowBorderWidth.attr("step", "1");
        
        $lensBorderWidth.attr("min", "1");
        $lensBorderWidth.attr("max", "10");
        $lensBorderWidth.attr("step", "1");

        //custom styles
        $(".woocommerce table.form-table th").css("padding-left", "20px");
        $("#wcpz-zoom-border").parents("tr").hide();
        $("#wcpz-lens-border").parents("tr").hide();
    }

    /**
     * Listen for values of zoom border parameters changing and put them to the hidden field.
     * Zoom border is a complex value like '3px solid #123456' and it is stored in db in the same form.
     * To provide this, each value that changing separately should be concatenated to one hidden field
     */
    function zoomBorderToHidden(e) {
        //get width of the zoom window border
        var width = $zoomWindowBorderWidth.val();
        //get type 
        var type = $zoomWindowBorderType.children('option:selected').val();
        //get color
        var color = $zoomWindowBorderColor.val();
        //and form the parameter string  
        var style = width + "px " + type + " " + color;
        $zoomBorder.attr("value", style);
        setBorderOfSelect($zoomBorderTable, style);
    }

    /**
     * Listen for values of zoom border parameters changing and put them to the hidden field. The same as above.
     */
    function lensBorderToHidden(e) {
        //get width of the lens border
        var width = $lensBorderWidth.val();
        //get type 
        var type = $lensBorderType.children('option:selected').val();
        //get color
        var color = $lensBorderColor.val();
        //and form the parameter string  
        var style = width + "px " + type + " " + color;
        $lensBorder.attr("value", style);
        setBorderOfSelect($lensBorderTable, style);
    }

    /**
     * Set the border style of zoom/lens border select
     */
    function setBorderOfSelect($elem, style) {
        $elem.css("border-left", style);
    }

    function addListeners() {
        $zoomWindowBorderWidth.on("input", zoomBorderToHidden);
        $zoomWindowBorderType.on("change", zoomBorderToHidden);
        $zoomWindowBorderColor.on("blur", zoomBorderToHidden);

        $lensBorderWidth.on("input", lensBorderToHidden);
        $lensBorderType.on("change", lensBorderToHidden);
        $lensBorderColor.on("blur", lensBorderToHidden);

        $("body").on("click", lensBorderToHidden);
        $("body").on("click", zoomBorderToHidden);
    }

    init();

});