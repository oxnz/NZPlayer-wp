jQuery(document).ready(function ($) {
	/*
	 * controller fixed when scroll after itself
	 */
	nzpc = $('#nzplayer-controller');
	$('#nzplayer-controller-wrapper').height($(nzpc).height());
	fixed = false;
	pos = nzpc.offset().top;
	$(window).scroll(function () {
		if ($(this).scrollTop() > pos) {
		   	if (false == fixed) {
				$(nzpc).css({
					position: "fixed",
					top: "0",
					background: "rgba(200, 20, 60, 0.7)",
					//background: "#fff",
					width: $(nzpc).width(),
				});
				fixed = true;
			}
		} else {
			if (true == fixed) {
				$(nzpc).css({
					position: "relative",
					background: "transparent",
				});
				fixed = false;
			}
		}
	});

	/*
	 * toggle css to adjust small width
	 */
	width = 340; // critical value
	$(window).resize(function (e) {
		w = $('#nzplayer').width();
		if (w < width) { // become narrow
			$('#nzplayer').attr("class", "adjusted");
			$('.cover > #cover').width(w);
			$('.cover > #cover').height(w);
			$('#nzplayer > .track').css({
				width: w,
				height: w+1, // border 1px
			});
		} else { // become wider
			$('#nzplayer').attr("class", "original");
			$('.cover > #cover').width(150);
			$('.cover > #cover').height(150);
			$('#nzplayer > .track').css({
				width: "",
				height: "",
			});
		}
		nzpc.width(nzpc.parent().width());
		pos = nzpc.offset().top;
	});
	$(window).resize();

	/*
	 * utility functions
	 */

	function strtime(seconds) {
		seconds = parseInt(seconds);
		min = parseInt(seconds/60);
		sec = seconds%60;
		if (min < 10)
			min = "0" + min;
		if (sec < 10)
			sec = "0" + sec;
		return min + ":" + sec;
	}

	/*
	 * audio init configure
	 */
	cover = document.getElementById("cover");
	title = document.getElementById("track-title");
	artist = document.getElementById("track-artist");
	album = document.getElementById("track-album");
	trackid = document.getElementById("track-id");
	duration = document.getElementById("duration");
	timesep = document.getElementById("timesep");

	/*
	 * settings
	 * load and play
	 */
	audio = new Audio();
	nzpc.ajaxurl = NZPlayerAjax.ajaxurl;
	nzpc.playlist = NZPlayerAjax.playlist;
	nzpc.playindex = 0;
	if (null != nzpc.playlist && 0 < nzpc.playlist.length) {
		audio.src = nzpc.playlist[nzpc.playindex]['source'];
		audio.volume = 0.6;
		audio.play();
	} else { // there's no music available
		$('#seekbar > #position').width(0);
	}

	/*
	 * audio event handlers
	 * refs: https://developer.apple.com/library/safari/documentation/AudioVideo/Reference/HTMLMediaElementClassReference/HTMLMediaElement/HTMLMediaElement.html#//apple_ref/javascript/instm/HTMLMediaElement/canPlayType
	 */
	audio.addEventListener('abort', function (event) {
		console.log(event.type);
	}, false);
	audio.addEventListener('canplay', function (event) {
		console.log(event.type);
	}, false);
	audio.addEventListener('canplaythrough', function (event) {
		console.log(event.type);
	}, false);
	audio.addEventListener('durationchange', function (event) {
		duration.innerText = strtime(audio.duration);
	}, false);
	audio.addEventListener('emptied', function (event) {
		console.log(event.type);
	}, false);
	audio.addEventListener('ended', function (event) {
		console.log(event.type);
		$('#forward').click();
	}, false);
	audio.addEventListener('error', function (e) {
		curtime.innerHTML = '<span class="fa fa-warning fa-fw"></span>';
		timesep.innerText = " ";
		switch (audio.error.code) {
			case audio.error.MEDIA_ERR_ABORTED:
				errmsg = "aborted";
				break;
			case audio.error.MEDIA_ERR_NETWORK:
				errmsg = "network error";
				break;
			case audio.error.MEDIA_ERR_DECODE:
				errmsg = "decode error";
				break;
			case audio.error.MEDIA_ERR_SRC_NOT_SUPPORTED:
				errmsg = "media source not supported";
				break;
			case audio.error.MEDIA_ERR_ENCRYPTED:
				errmsg = "media is encrypted";
				break;
			default:
				errmsg = "unknown error";
				break;
		}
		duration.innerText = errmsg;
		pos = nzpc.offset().top; // errmsg would overflow
	}, false);
	audio.addEventListener('loadeddata', function (event) {
		console.log(event.type);
	}, false);
	audio.addEventListener('loadedmetadata', function (event) {
		cover.src = nzpc.playlist[nzpc.playindex]['cover'];
		title.innerText = nzpc.playlist[nzpc.playindex]['title'];
		artist.innerText = nzpc.playlist[nzpc.playindex]['artist'];
		album.innerText = nzpc.playlist[nzpc.playindex]['album'];
		trackid.innerText = nzpc.playlist[nzpc.playindex]['id'];
		duration.innerText = strtime(audio.duration);
		timesep.innerText = "/";
		audio.play();
		//  title and other media info would wrap-line, so update the pos
		pos = nzpc.offset().top;
	}, false);
	audio.addEventListener('loadstart', function (event) {
		console.log(event.type);
	}, false);
	audio.addEventListener('pause', function (event) {
		console.log(event.type);
	}, false);
	audio.addEventListener('play', function (event) {
		console.log(event.type);
	}, false);
	audio.addEventListener('playing', function (event) {
		console.log(event.type);
	}, false);
	audio.addEventListener('progress', function (event) {
		// this would cause flash screen
		//curtime.innerHTML = '<span class="fa fa-spinner fa-fw fa-spin"></span>';
	}, false);
	audio.addEventListener('retechange', function (event) {
		console.log(event.type);
	}, false);
	audio.addEventListener('seeked', function (event) {
		console.log(event.type);
	}, false);
	audio.addEventListener('seeking', function (event) {
		console.log(event.type);
	}, false);
	audio.addEventListener('stalled', function (event) {
		curtime.innerHTML = '<span class="fa fa-exclamation fa-fw"></span>';
	}, false);
	audio.addEventListener('suspend', function (event) {
		// this would cause flash screen
		//curtime.innerHTML = '<span class="fa fa-spinner fa-fw fa-spin"></span>';
	}, false);
	audio.addEventListener('timeupdate', function (event) {
		$('#seekbar > #position').width(
			$('#seekbar').width() * audio.currentTime/audio.duration);
		curtime.innerText = strtime(audio.currentTime);
	}, false);
	audio.addEventListener('volumechange', function (event) {
		$('#volbar > .barval').width($('#volbar').width() * audio.volume);
		mute = $('#mute > span');
		if (0.5 > audio.volume) { // low volume
			mute.attr("class", "fa fa-volume-down fa-lg fa-fw");
		} else {
			mute.attr("class", "fa fa-volume-up fa-lg fa-fw");
		}
		if (audio.muted) {
			mute.attr("class", "fa fa-volume-off fa-lg fa-fw");
		}
	}, false);
	audio.addEventListener('waiting', function (event) {
		curtime.innerHTML = '<span class="fa fa-spinner fa-fw fa-spin"></span>';
	}, false);

	/*
	 * audio controll buttons
	 */

	/*
	 * previous & next
	 */
	function nzrequest(optname) {
		jQuery.post(
			nzpc.ajaxurl,
			{ action: 'nzplayer_ctl', option: optname },
			function(response) {
				switch (optname) {
					case "next":
					case "prev":
						nzpc.playlist = JSON.parse(response);
						nzpc.playindex = (optname == "next") ?
							0 : nzpc.playlist.length - 1;
						audio.src = nzpc.playlist[nzpc.playindex]['source'];
						audio.play();
						break;
					default:
						console.log("unknow request");
						break;
				}
			}
		);
	}
	$('.toolbar > #backward').click(function () {
		if (0 < nzpc.playindex) {
			audio.src = nzpc.playlist[--nzpc.playindex]['source'];
		} else {
			nzrequest("prev");
		}
	});
	/*
	 * toggle play/pause
	 */
	$('#play > span').toggle(function() {
		$(this).attr("class", "fa fa-play fa-lg fa-fw");
		audio.pause();
	},
	function () {
		$(this).attr("class", "fa fa-pause fa-lg fa-fw");
		audio.play();
	});
	$('.toolbar > #forward').click(function () {
		if (nzpc.playindex < nzpc.playlist.length-1) {
			audio.src = nzpc.playlist[++nzpc.playindex]['source'];
		} else {
			nzrequest("next");
		}
	});
	/*
	 * toggle mode
	 */
	$('#mode > span').toggle(function () {
		$(this).attr("class", "fa fa-random fa-lg fa-fw");
		audio.loop = false;
	},
	function () {
		$(this).attr("class", "fa fa-repeat fa-lg fa-fw");
		audio.loop = true;
	},
	function () {
		$(this).attr("class", "fa fa-list-ol fa-lg fa-fw");
		audio.loop = false;
	});
	/*
	 * toggle mute
	 */
	$('#mute > span').click(function () { audio.muted = !audio.muted; });
	/*
	 * set volume
	 */
	$('.toolbar > #volbar').click(function (e) {
		audio.volume = e.offsetX/$('#volbar').width();
	});
	$('.toolbar > #heart').toggle(function () {
		$('#heart > span').attr("class", "fa fa-heart-o fa-lg fa-fw");
	},
	function () {
		$('#heart > span').attr("class", "fa fa-heart fa-lg fa-fw");
	});
	/*
	 * seeking time
	 */
	$('#nzplayer-controller > #seekbar').click(function (event) {
		audio.currentTime = audio.duration*event.offsetX/$('#seekbar').width();
	});

	/*
	 * keyboard shortcuts
	 */
	/*
	$(window).keypress(function(e) {
		console.log(e.which + ':' + e.keyCode);
		switch (e.keyCode) {
			case 32:
				console.log("pause");
				console.log(e.target);
				x = e;
				return true;
		}
		return;
	});
	z = document.getElementById("toolbar");
	z.addEventListener('keypress', function(e) {
		console.log(e.keyCode);
	});
	z.addEventListener('click', function(e) {
		console.log('clicked detected');
	});
	*/

});

