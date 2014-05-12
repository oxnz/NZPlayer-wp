function NZPlayerController(ajaxurl, playlist) {
	this.cover = document.getElementById("cover");
	this.title = document.getElementById("track-title");
	this.artist = document.getElementById("track-artist");
	this.album = document.getElementById("track-album");
	this.trackid = document.getElementById("track-id");
	this.duration = document.getElementById("duration");
	this.timesep = document.getElementById("timesep");
	this.volbar = document.getElementById("volbar");
	this.seekbar = document.getElementById("seekbar");

	this.audio = new Audio();
	this.audio.volume = 0.7;
	this.audio.loop = false;
	this.ajaxurl = ajaxurl;
	this.playlist = playlist;
	this.playindex = 0;

	this.play = function() {
		if ("" == this.audio.currentSrc)
			this.audio.src = this.playlist[this.playindex];
		this.audio.play();
	}
	this.voldown = function() {
	}
}
