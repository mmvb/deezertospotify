<?php
/*
 * Copyright © 2018 Marvin van Bakkum
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
$spotify_client_id     = 'CLIENT_ID_HERE';
$spotify_client_secret = 'CLIENT_SECRET_HERE';

?><!DOCTYPE html>
<html>
	<head>
		<title></title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<style>
			body {
				font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
				background-color: #fff;
				color: #222;
			}

			div {
				max-width: 1280px;
				margin-left: auto;
				margin-right: auto;
				padding-left: 2%;
				padding-right: 2%;
			}
			
			pre {
				background-color: lightgreen;
				border-radius: 3px;
				border: 1px solid gray;
			}
		</style>
	</head>
	<body>
		<div>
			<h2>Convert Deezer to Spotify</h2>
			<form method="get">
				<label for="deezer_playlist_input">Deezer playlist id: <label><input id="deezer_playlist_input" name="deezer_playlist" placeholder="Deezer playlist id"><br>
				<button type="submit">Convert</button>
			</form>
		</div>
<?php
if (!empty($_GET['deezer_playlist'])):
?>
		<div>
			<h2>Result</h2>
			<pre>
<?php
	$playlist_json = file_get_contents('https://api.deezer.com/playlist/' . urlencode($_GET['deezer_playlist']) . '?limit=1000');
	$playlist = json_decode(utf8_encode($playlist_json));
	$songs = $playlist->tracks->data;
	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, 'https://accounts.spotify.com/api/token'); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode($spotify_client_id . ':' $spotify_client_secret)));
	$token_json = curl_exec($ch); 
	curl_close($ch);      
	$token = json_decode(utf8_encode($token_json));
	
	$number = 0;
	foreach($songs as $song) {
		$number++;
		
		$track_json = file_get_contents('https://api.deezer.com/track/' . urlencode($song->id));
		$track = json_decode(utf8_encode($track_json));
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.spotify.com/v1/search?q=isrc:' . urlencode($track->isrc) . '&type=track&limit=1'); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token->access_token));
		$search_json = curl_exec($ch);
		curl_close($ch);      
		$search = json_decode(utf8_encode($search_json));
		
		if (count($search->tracks->items) > 0) {
			$result = $number . '. ' . $song->artist->name . ' - ' . $song->title . ' ' . $search->tracks->items[0]->uri . "\r\n";
		} else {
			$ch = curl_init();
			$url = 'https://api.spotify.com/v1/search?q=' . rawurlencode('track:' . $track->title . ' artist:' . $track->artist->name . ' album:' . $track->album->title) . '&type=track&market=NL&limit=1';
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token->access_token));
			$search_json = curl_exec($ch);
			curl_close($ch);      
			$search = json_decode(utf8_encode($search_json));
			
			if (count($search->tracks->items) > 0) {
				$result = $number . '. ' . $song->artist->name . ' - ' . $song->title . ' ' . $search->tracks->items[0]->uri . " *\r\n";
			} else {
				$ch = curl_init();
				$url = 'https://api.spotify.com/v1/search?q=' . rawurlencode('track:' . $track->title_short . ' artist:' . $track->artist->name . ' album:' . $track->album->title) . '&type=track&market=NL&limit=1';
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token->access_token));
				$search_json = curl_exec($ch);
				curl_close($ch);      
				$search = json_decode(utf8_encode($search_json));
				
				if (count($search->tracks->items) > 0) {
					$result = $number . '. ' . $song->artist->name . ' - ' . $song->title . ' ' . $search->tracks->items[0]->uri . " **\r\n";
				} else {
					$ch = curl_init();
					$url = 'https://api.spotify.com/v1/search?q=' . rawurlencode('track:' . $track->title . ' artist:' . $track->artist->name) . '&type=track&market=NL&limit=1';
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token->access_token));
					$search_json = curl_exec($ch);
					curl_close($ch);      
					$search = json_decode(utf8_encode($search_json));
					
					if (count($search->tracks->items) > 0) {
						$result = $number . '. ' . $song->artist->name . ' - ' . $song->title . ' ' . $search->tracks->items[0]->uri . " ***\r\n";
					} else {
						$ch = curl_init();
						$url = 'https://api.spotify.com/v1/search?q=' . rawurlencode('track:' . $track->title_short . ' artist:' . $track->artist->name) . '&type=track&market=NL&limit=1';
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token->access_token));
						$search_json = curl_exec($ch);
						curl_close($ch);      
						$search = json_decode(utf8_encode($search_json));
						
						if (count($search->tracks->items) > 0) {
							$result = $number . '. ' . $song->artist->name . ' - ' . $song->title . ' ' . $search->tracks->items[0]->uri . " ****\r\n";
						} else {
							$result = 'NOT FOUND: ' . $number . '. ' . $song->artist->name . ' - ' . $song->title . "\r\n";
						}
					}
				}
			}
		}
		
		echo $result;
		
		while (@ob_end_flush());
		flush();
		
		usleep(110000);
	}
?>
			</pre>
		</div>
<?php
endif;
?>
	</body>
</html>