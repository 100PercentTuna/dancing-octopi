jQuery( document ).ready( function( $ ) {
    $( '#wp-admin-bar-wpaas-whats-new' ).on( 'click', function( event ) {
        event.preventDefault();
        var dataLayerAdminBar = window._expDataLayer || [];

        window.wp.apiRequest({
            path: '/wpaas/v1/dismiss-whats-new',
            type: 'POST'
        });

        var linkUrl = $(this).find('a').attr('href');

        dataLayerAdminBar.push({
            schema: 'add_event',
            version: 'v1',
            data: {
                eid: 'wp.tools/quick_links.whats_new.link.click',
                type: 'click',
            }
        });

        window.open(linkUrl, '_blank', 'noopener');
    });
} );
