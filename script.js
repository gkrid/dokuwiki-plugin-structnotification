jQuery(function() {

    jQuery('#plugin__structnotification_cancel').click('on', function (event) {
        if (!window.confirm(LANG.plugins.telleveryone.cancel_confirm)) {
            event.preventDefault();
        }
    });
    jQuery('.plugin__structnotification_delete').click('on', function (event) {
        if (!window.confirm(LANG.plugins.telleveryone.delete_confirm)) {
            event.preventDefault();
        }
    });
});
