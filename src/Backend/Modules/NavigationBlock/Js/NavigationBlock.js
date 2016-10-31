/**
 * Interaction for the navigation_block module
 *
 * @author    Tijs Verkoyen <tijs@sumocoders.be>
 * @author    Thomas Deceuninck <thomas@fronto.be>
 */
jsBackend.NavigationBlock =
{
    generatePath: function () {
        $.ajax(
            {
                data: {
                    fork: { module: 'NavigationBlock', action: 'GeneratePath' },
                    alias: $('#alias').val()
                },
                success: function (data, textStatus) {
                    alias = data.data;
                    $('#generatedUrl').html(alias);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    url = '';
                    $('#generatedUrl').html(url);
                }
            });
    },
    // init, something like a constructor
    init: function () {
        jsBackend.NavigationBlock.generatePath();

        $('#alias').on('keyup', function (e) {
            jsBackend.NavigationBlock.generatePath();
        });

    }
}

$(jsBackend.NavigationBlock.init);
