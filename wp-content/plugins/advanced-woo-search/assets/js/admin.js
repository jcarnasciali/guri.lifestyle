jQuery(document).ready(function ($) {
    'use strict';

    var $reindexBlock = $('#aws-reindex');
    var $reindexBtn = $('#aws-reindex .button');
    var $reindexProgress = $('#aws-reindex .reindex-progress');
    var syncStatus = 'sync';
    var processed  = 0;
    var toProcess  = 0;
    var processed = 0;

    var $clearCacheBtn = $('#aws-clear-cache .button');


    // Reindex table
    $reindexBtn.on( 'click', function(e) {

        e.preventDefault();

        $reindexBlock.addClass('loading');

        sync();

    });


    function sync() {

        $.ajax({
            type: 'POST',
            url: aws_vars.ajaxurl,
            data: {
                action: 'aws-reindex'
            },
            dataType: "json",
            success: function (response) {
                if ( 'sync' !== syncStatus ) {
                    return;
                }

                toProcess = response.data.found_posts;
                processed = response.data.offset;

                processed = Math.floor( processed / toProcess * 100 );

                if ( 0 === response.data.found_posts && ! response.data.start ) {

                    // Sync finished
                    syncStatus = 'finished';

                    console.log( "Reindex finished!" );

                    $reindexBlock.removeClass('loading');

                } else {

                    console.log( response.data );

                    $reindexProgress.html ( processed + '%' );

                    // We are starting a sync
                    syncStatus = 'sync';

                    sync();
                }

            },
            error : function( jqXHR, textStatus ) {
                console.log( "Request failed: " + textStatus );
                cancelSync();
            },
            complete: function () {
            }
        });

    }

    function cancelSync() {
        $.ajax( {
            method: 'post',
            url: ajaxurl,
            data: {
                action: 'aws-cancel-index'
            }
        } );
    }


    // Clear cache
    $clearCacheBtn.on( 'click', function(e) {

        e.preventDefault();

        var $clearCacheBlock = $(this).closest('#aws-clear-cache');

        $clearCacheBlock.addClass('loading');

        $.ajax({
            type: 'POST',
            url: aws_vars.ajaxurl,
            data: {
                action: 'aws-clear-cache'
            },
            dataType: "json",
            success: function (data) {
                alert('Cache cleared!');
                $clearCacheBlock.removeClass('loading');
            }
        });

    });


});