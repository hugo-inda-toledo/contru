/**
 * Created by Kupletsky Sergey on 17.10.14.
 *
 * Material Sidebar (Profile menu)
 * Tested on Win8.1 with browsers: Chrome 37, Firefox 32, Opera 25, IE 11, Safari 5.1.7
 * You can use this sidebar in Bootstrap (v3) projects. HTML-markup like Navbar bootstrap component will make your work easier.
 * Dropdown menu and sidebar toggle button works with JQuery and Bootstrap.min.js
 */

// Sidebar toggle
//
// -------------------
$(document).ready(function() {
    //set active item sidebar
    browser_url = window.location.href.split('/');
    $sidebar = $('#topbar');
    $('#topbar').find('li.dropdown').clone().appendTo('.restore');
    if ($.inArray('dev', browser_url) == -1) {
        controller = browser_url[3].split('?');
        controller = controller[0];
        (browser_url.length > 4) ? browser_url = '/' + controller + '/' + browser_url[3] : browser_url = '/' + controller;
    } else {
        controller = browser_url[5].split('?');
        controller = controller[0];
        (browser_url.length > 6) ? browser_url = '/' + browser_url[3] + '/' + browser_url[4] + '/' + controller + '/' + browser_url[6] :
            browser_url = '/' + browser_url[3] + '/' + browser_url[4] + '/' + controller;
    }
    $('#topbar').find('a').each(function(index, el) {
        if ($(this).attr('href') == browser_url) {
            if($(this).parents('.dropdown-menu').length>0){
                $(this).parent().addClass('active');
                $(this).parents('.dropdown-menu').prev().addClass('btn-material-blue-grey-800');
            }else{
                $(this).addClass('btn-material-blue-grey-800');
            }
            // $(this).parent().parent().parent().addClass('open');
        } else {
            if (!$(this).parent().parent().parent().hasClass('open')) {
                elem_url = $(this).attr('href').split('/');
                if ($.inArray(controller, elem_url) > -1) {
                    $(this).parent().parent().prev().addClass('btn-material-blue-grey-800');
                }
            }
        }
    });
    //toggle minimized sidebar TODO: fix icons collapsed
    $(document).on('click', '.menu-toggle', function(event) {
        event.preventDefault();
        $(function () {
          $('[data-toggle="tooltip"]').tooltip( {container: 'body'} );
        });
        $span_collapse = $(this);
        if ($span_collapse.hasClass('collapse-on')) {
            $('#sidebar').parent().attr('class', '').addClass('col-sm-2 col-md-2').css('width', '16.66666667%');
            $('#sidebar').parent().siblings().attr('class', '').css('width', '83.33333333%').addClass('col-sm-10 col-md-10');
            $span_collapse.removeClass('collapse-on');
            $('#sidebar ul.nav.sidebar-nav li').empty();
            $('.restore').find('li.dropdown').clone().appendTo('#sidebar ul.nav.sidebar-nav');
            $('[data-toggle="tooltip"]').tooltip('disable');
        } else {
            $('#sidebar ul.nav.sidebar-nav li').each(function(index, el) {
                $icon = $(this).children('a').find('i');
                $(this).children('a').text('');
                $(this).children('a').append($icon);
                $(this).children('ul').detach();
            });
            $('#sidebar').parent().attr('class', '').addClass('col-sm-1 col-md-1').css('width', '5%');
            $('#sidebar').parent().siblings().attr('class', '').css('width', '95%').addClass('col-sm-11 col-md-11');
            $span_collapse.addClass('collapse-on');
            $('[data-toggle="tooltip"]').tooltip('enable');
        }
    });
    //sidebar behavior for parent and child items
    $(document).on('click', '#sidebar li a', function(event) {
        event.preventDefault();
        if ($(this).attr('href') == '#') {
            if (!$('.menu-toggle').hasClass('collapse-on')) {
                if ($(this).parent().hasClass('open')) {
                    $(this).parent().removeClass('open');
                    $(this).parent().removeClass('shadow-z-2');
                    $(this).parent().addClass('shadow-z-1');
                } else {
                    $(this).parent().addClass('open');
                    $(this).parent().removeClass('shadow-z-1');
                    $(this).parent().addClass('shadow-z-2');
                }
            }
        } else {
            window.location.href = $(this).attr('href');
        }
    });
});

/**
 * Created by Kupletsky Sergey on 08.09.14.
 *
 * Add JQuery animation to bootstrap dropdown elements.
 */
(function($) {
    var dropdown = $('.dropdown');
    // Add slidedown animation to dropdown
    dropdown.on('show.bs.dropdown', function(e){
        $(this).find('.dropdown-menu').first().stop(true, true).slideDown();
    });
    // Add slideup animation to dropdown
    dropdown.on('hide.bs.dropdown', function(e){
        $(this).find('.dropdown-menu').first().stop(true, true).slideUp();
    });
})(jQuery);

(function(removeClass) {
	jQuery.fn.removeClass = function( value ) {
		if ( value && typeof value.test === "function" ) {
			for ( var i = 0, l = this.length; i < l; i++ ) {
				var elem = this[i];
				if ( elem.nodeType === 1 && elem.className ) {
					var classNames = elem.className.split( /\s+/ );
					for ( var n = classNames.length; n--; ) {
						if ( value.test(classNames[n]) ) {
							classNames.splice(n, 1);
						}
					}
					elem.className = jQuery.trim( classNames.join(" ") );
				}
			}
		} else {
			removeClass.call(this, value);
		}
		return this;
	}
})(jQuery.fn.removeClass);