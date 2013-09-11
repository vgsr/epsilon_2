/**
 * front-page.js
 * 
 * Handles fixing/unfixing header navigation when scrolling for the front-page.
 */
( function() {
	var body = document.getElementsByTagName( 'body' )[0],
	    main = document.getElementById( 'main' );

	if ( -1 == body.className.indexOf( 'home' ) || undefined == main )
		return false;

	document.onscroll = function() {
		if ( 441 > window.innerWidth )
			return false;

		if ( 'pageYOffset' in window ) {
			if ( -1 != body.className.indexOf( 'small-ribbon' ) ) {
				if ( 0 == window.pageYOffset ) {
					body.className = body.className.replace( ' small-ribbon', '' );
				}

			// Use small-ribbon class down from 120px from top
			} else if ( 119 < window.pageYOffset ) {
				body.className += ' small-ribbon';
				window.scrollBy(0, -119);
			}
		} else { // lt IE9
			console.log( 'Get yourself a decent browser.' ); // Or use document.documentElement.scrollTop
		}
	}
} )();
