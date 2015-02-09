// place the js code for drag and dropable sitemap here
// use code from http://boagworld.com/dev/creating-a-draggable-sitemap-with-jquery/
$(function() {
    $('#sitemap li').prepend('<div class="dropzone"></div>');

    $('#sitemap dl, #sitemap .dropzone').droppable({
        accept: '#sitemap li',
        tolerance: 'pointer',
        drop: function(e, ui) {
            var li = $(this).parent();
            var child = !$(this).hasClass('dropzone');
            if (child && li.children('ul').length == 0) {
                li.append('<ul/>');
            }
            if (child) {
                li.addClass('sm2_liOpen').removeClass('sm2_liClosed').children('ul').append(ui.draggable);
            }
            else {
                li.before(ui.draggable);
            }
			$('#sitemap li.sm2_liOpen').not(':has(li:not(.ui-draggable-dragging))').removeClass('sm2_liOpen');
            li.find('dl,.dropzone').css({ backgroundColor: '', borderColor: '' });
            // ajax get request with the serialized data
            //var sequence = $('#sitemap').draggable('serialize');
            var postdata = $('#sitemap').serializeTree('id', 'tree', '.dropzone');
            //alert('Result after dropping something '+postdata);
            $.get('/s/de/cms/reorder/?' + postdata);
            //sitemapHistory.commit();
        },
        over: function() {
            $(this).filter('dl').css({ backgroundColor: '#ccc' });
            $(this).filter('.dropzone').css({ borderColor: '#aaa' });
        },
        out: function() {
            $(this).filter('dl').css({ backgroundColor: '' });
            $(this).filter('.dropzone').css({ borderColor: '' });
        }
    });
    $('#sitemap li').draggable({
        handle: ' > dl',
        opacity: .8,
        addClasses: false,
        helper: 'clone',
        zIndex: 100,
        start: function(e, ui) {
            //sitemapHistory.saveState(this);
        }
    });
    //$('.sitemap_undo').click(sitemapHistory.restoreState);
    /*
    $(document).bind('keypress', function(e) {
        if (e.ctrlKey && (e.which == 122 || e.which == 26))
            sitemapHistory.restoreState();
    });
    */
	$('.sm2_expander').live('click', function() {
		$(this).parent().parent().toggleClass('sm2_liOpen').toggleClass('sm2_liClosed');
		return false;
	});
});