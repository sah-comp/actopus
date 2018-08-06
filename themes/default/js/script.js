// container for intervals
var intervals = new Array();
var intervalsCounter = 0;

// On your marks, get set...
$(document).ready(function() {

    scaleTextareas(); // make textareas .scaleable scale on amount on text it has
    clairvoyants(); // initializes all clairvoyants/autocomplete elements
    tabbed(); // init all tabbed unorded lists
    sortables(); // init all sortables
    periodicals(); // init all intervals

    /**
     * Make elements sticky
     */
    $('.sticky').scrollToFixed();

    /**
     * The notifications section will animate a little to catch atttention by users.
     */
    $('.notifications').slideDown('slow');

    /**
     * When the languagechooser changes all elements that do not match the choosen
     * language will be hidden and the choosen language element will be visible.
     */
    $('.languagechooser select').change(function() {
        var choosen = $(this).val();
        var url = $(this).attr('data-href') + choosen;
        $('.i18n:not(.' + choosen + ')').slideUp('slow', function() {
            $('.i18n.' + choosen).slideDown('slow');
            $.get(url);
        });
    });

    /**
     * Select or de-select all checkboxes.
     *
     * @todo only apply onto certain groups of checkboxes not all
     */
    $('input.all[type=checkbox]').live('click', function() {
        var state = $(this).is(':checked');
        $('input.selector[type=checkbox]').each(function() {
            $(this).attr('checked', state);
        });
    });

    /**
     * Hook any sitemap link.
     */
    $("#sitemap a:not('.sm2_expander'), a.root").live('click', function() {
        if ($(this).hasClass('active')) return false; // is already active, noop
        $("#sitemap a:not('.sm2_expander'), a.root").removeClass('active');
        $(this).addClass('active');
        //alert($(this).attr('href'));
        //$('#article-container').slideUp('slow');
        $.get($(this).attr('href'), function(data) {
            //$('#article-slice-container').slideUp('slow');
            $('#article-slice-container').empty();
            $('#article-slice-container').append(data);
            //$('#article-slice-container').slideDown('slow');
        }, 'html');
        //$('#slice-container').slideUp('slow');
        return false;
    });

    /**
     * Hook any article link.
     */
    $("#article-container a.hook").live('click', function() {
        if ($(this).hasClass('active')) return false; // is already active, noop
        $("#article-container a.hook").removeClass('active');
        $(this).addClass('active');
        $.get($(this).attr('href'), function(data) {
            $('#slice-container').empty();
            $('#slice-container').append(data);
        }, 'html');
        return false;
    });

    /**
     * Hook any slice link.
     */
    $("#slice-container div.cms-slice:not('.active')").live('click', function() {
        $(this).addClass('active');
        var container = $(this).attr('data-container');
        var url = $(this).attr('data-href');
        //var url = $(this).attr('href');
        $.get(url, function(data) {
            $('#' + container).empty();
            $('#' + container).append(data);
        }, 'html');
        return false;
    });

    /**
     * Hook add a page or article when on cms.
     */
    $("li.scaffold-cms-add-page > a, li.scaffold-cms-add-article > a").live('click', function() {
        //alert($(this).attr('href'));
        var url = $(this).attr('href');
        //var url = $(this).attr('href');
        $.get(url, function(data) {
            $('#article-container').append(data);
        }, 'html');
        return false;
    });

    /**
     * Allow metaKey-click to call the url given in data-href of a table row or cms slice.
     *
     * @todo check if the url is good or bite the dust.
     *
     * On Mac OS X the meta key is "cmd", "propeller", "blumenkohl", i guess on windows
     * it is the "Windows" key. Might be some other key on your OS.
     *
     * Add classes "table item" to a table row element (tr) to let user metaKey-click a table row
     * to call the url given in the attribute data-href of the tr. This is used in the template
     * shared/scaffold/table.
     */
    $('tr.table.item').live('click', function(event) {
        if (event.metaKey) {
            event.preventDefault();
            var url = $(this).attr('data-href');
            window.location.href = url;
            return false;
        }
    });

    /**
     * All elements that have class .updateonchange will make a post request
     * that updates an given target element after sending a post request build
     * from a url and added up by optional parameters given in data-fragments.
     */
    $('.updateonchange').live('change', function() {
        var target = $(this).attr('data-target');
        var fragments = jQuery.parseJSON($(this).attr('data-fragments')); //extend url with these values
        var url = $(this).attr('data-href'); //+$(this).val();
        for (var key in fragments) {
            url = url + $('#' + key).val() + '/';
        }
        $.post(url, function(data) {
            $('#' + target).empty().append(data);
        });
        return false;
    });

    /**
     * All elements that have class .updateonclick will make a post request
     * that updates an given target element after sending a post request build
     * from a url and added up by optional parameters given in data-fragments.
     *
     * This does the same as .updateonchange for select elements, but with click.
     */
    $('.updateonclick').live('click', function() {
        if ($(this).hasClass('ask')) {
            if (!confirm('Bestätigen Sie das die Aktualisierung tatsächlich durchgeführt werden soll')) {
                return false;
            }
        }
        var target = $(this).attr('data-target');
        var fragments = jQuery.parseJSON($(this).attr('data-fragments')); //extend url with these values
        var url = $(this).attr('data-href'); //+$(this).val();
        for (var key in fragments) {
            url = url + $('#' + key).val() + '/';
        }
        $.post(url, function(data) {
            $('#' + target).empty().append(data);
        });
        return false;
    });

    /**
     * All elements that have class .attachremote will make a post request
     * that updates an given target element after sending a post request build
     * from a url and added up by optional parameters given in data-fragments.
     */
    $('.attachremote').live('click', function() {
        var target = $(this).attr('data-target');
        var remote = $(this).attr('data-remote');
        var fragments = jQuery.parseJSON($(this).attr('data-fragments')); //extend url with these values
        var url = $(this).attr('href'); //+$(this).val();
        for (var key in fragments) {
            url = url + $('#' + key).val() + '/';
            $('#' + key).val(''); /* reset the elements value immediatlye */
        }
        $.post(url, function(data) {
            $('#' + target).empty().append(data);
        });
        $('#' + remote).val(''); /* reset the remote attach controllers value */
        return false;
    });

    /**
     * all and future detach links send a post request and then
     * fade out and finally detach the element.
     */
    $('.detach').live('click', function() {
        if ($(this).hasClass('ask')) {
            if (!confirm("Soll der Eintrag tatsächlich entfernt werden?")) {
                return false;
            }
        }
        var target = $(this).attr('data-target');
        var url = $(this).attr('href');
        $.post(url, function(data) {
            $('#' + target).fadeOut('fast', function() {
                $('#' + target).detach();
            });
        });
        return false;
    });

    /**
     * all and future attach links post request a url and
     * insert a new element into the *-additional zone.
     */
    $('.attach').live('click', function() {
        var target = $(this).attr('data-target');
        var url = $(this).attr('href');
        $.post(url, function(data) {
            $('#' + target).append(data);
        });
        return false;
    });

    /**
     * All and future elements with class .toggle
     * will toggle the display block and none state of
     * the targetted element.
     */
    $('.toggle').live('click', function() {
        var container = $(this).attr('data-container');
        $('#' + container).toggle();
    });

    /**
     * all and future drop links send a post request and then
     * fade out and finally detach the element.
     */
    $('.drop').live('click', function() {
        var target = $(this).attr('data-target');
        var url = $(this).attr('href');
        $.post(url, function(data) {
            $('#' + target).fadeOut('fast', function() {
                $('#' + target).detach();
            });
        });
        return false;
    });

    /**
     * Form with class inplace will be sent as POST and update an element in the DOM
     * given by the data-container attribute.
     */
    $('.inplace').live('submit', function() {
        // submit the form
        var form = $(this);
        var container = form.attr('data-container');
        if ($('#' + container).hasClass('active')) $('#' + container).removeClass('active');
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            success: function(response) {
                $('#' + container).empty();
                $('#' + container).append(response);
            }
        });
        // return false to prevent normal browser submit and page navigation
        return false;
    });

    /**
     * Form with class otherplace will be sent as POST and append the response to an
     * element in the DOM given by the data-container attribute.
     */
    $('.otherplace').live('submit', function() {
        // submit the form
        var form = $(this);
        var container = form.attr('data-container');
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            success: function(response) {
                //$('#'+container).empty();
                $('#' + container).append(response);
            }
        });
        // return false to prevent normal browser submit and page navigation
        return false;
    });

    /**
     * Form with class metaplace will be sent as POST and replace the response to an
     * element in the DOM given by the data-container attribute. This will be the
     * complete article-slice-container.
     */
    $('.metaplace').live('submit', function() {
        // submit the form
        var form = $(this);
        var container = form.attr('data-container');
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            success: function(response) {
                $('#' + container).empty();
                $('#' + container).append(response);
            }
        });
        // return false to prevent normal browser submit and page navigation
        return false;
    });

    /**
     * On a scaffold table form a click on a checkbox will call a php script
     * to add the clicked item to the list of selected items.
     */
    $('.scaffold-table').on('click', '.selector', function() {
        var url = $(this).attr('data-collector');
        $.ajax({
            type: 'GET',
            url: url,
            cache: false,
            error: function(hxr, errtxt, errorcode) {
                //alert('Error ' + errtxt);
            },
            success: function() {
                //alert('Collected or scratched');
            }
        });
        return true;
    });


});

/**
 * Copies ui.item.[property] to a element targetted by the elements id.
 */
function dispatchValues(spread, ui) {
    var fields = jQuery.parseJSON(spread);
    for (var key in fields) {
        if (fields.hasOwnProperty(key)) {
            var value = ui.item[fields[key]];
            $('#' + key).val(value);
        }
    }
}

/**
 * Scale all textareas dynamically on the page
 * Requires jQuery
 * @see http://drasticcode.com/2009/5/26/resizing-textareas-as-you-type-with-jquery
 */
function scaleTextareas() {
    if (!$('textarea.scaleable').length) return;
    $('textarea.scaleable').each(function(i, t) {
        var m = 0;
        $($(t).val().split("\n")).each(function(i, s) {
            m += (s.length / (t.offsetWidth / 10)) + 1;
        });
        t.style.height = Math.floor(m + 4) + 'em';
    });
    setTimeout(scaleTextareas, 1000);
};

/**
 * for each element that has class autocomplete setup a jQuery.ui autocomplete widget
 * that will call a data-source to find items and, if selected copies the selected items
 * attributes to html elements defined in data-spread.
 * You should add parameter callback=? to your source urls to allow JSONP result.
 *
 * @todo refactor code so that later added .autocomplete elements will also profit
 *
 * @uses dispatchValues()
 */
function clairvoyants() {
    $('.autocomplete').each(function(index, element) {
        var spread = $(this).attr('data-spread'); // holds key/value array with ids and item attrs
        $(this).autocomplete({
            'minLength': 3,
            'autoFocus': false,
            'delay': 700,
            'source': $(this).attr('data-source'),
            focus: function(event, ui) {
                dispatchValues(spread, ui);
                return false;
            },
            select: function(event, ui) {
                dispatchValues(spread, ui);
                return false;
            }
        });
    });
}

/**
 * Each unordered list within a .tabbed element will have tabs.
 * If lasttab is a valid element on the page, that tab will be the default tab.
 */
function tabbed() {
    // Apply tabbed navigation on all unorderedlists found within a .tabbed class element
    if ($('.tabbed ul').length > 0) {
        if (lasttab && $('#' + lasttab).length) {
            $('.tabbed ul').idTabs(lasttab);
        } else {
            $('.tabbed ul').idTabs();
        }
        $('a.chubbytabby').click(function() {
            $('#lasttab').val($(this).attr('id').slice(4)); /* set the latest tab */
        });
    }
}

/**
 * Make divs sortable when they are in a .sortable container with clone.
 */
function sortables() {
    $('.sortable').sortable({
        items: '> div',
        axis: 'y',
        helper: 'clone',
        placeholder: 'ui-state-highlight',
        opacity: '.8',
        start: function(event, ui) {
            $(ui.item).show();
        },
        update: function(event, ui) {
            var url = $(this).attr('data-href');
            var container = $(this).attr('data-container');
            var sequence = $('#' + container).sortable('serialize');
            $.get(url + '?' + sequence);
        }
    });
}

/**
 * Add a interval to call a certain url on every .interval element.
 */
function periodicals() {
    $('.interval').each(function() {
        intervalsCounter = intervalsCounter + 1; // count one up
        var delay = $(this).attr('data-delay');
        var url = $(this).attr('data-href');
        var container = $(this).attr('data-container');
        //alert(container+' i '+intervalsCounter);
        intervals[intervalsCounter] = setInterval(function() {
            if ($('#' + container).length > 0) {
                $.get(url, function(data) {
                    $('#' + container).empty();
                    $('#' + container).append(data);
                }, 'html');
            } else {
                clearInterval(intervals[intervalsCounter]);
            }
        }, delay);
    });
}
