# Deezer playlist to Spotify converter
This single file PHP program allows you to convert a (public) Deezer playlist to a (textual) list of Spotify track URIs which can then be pasted into a Spotify playlist.

This program first tries to find the track by its International Standard Recording Code (ISRC), after that it attempts to look it up by title, artist, album and then by title, artist.

To use this program on your own server you need:
* Configured web server and PHP environment, this program was tested on PHP 7.0.30 but expected to work on older versions of PHP
* A Client ID and Client Secret from Spotify, and place them in the ```$spotify_client_id``` and ```$spotify_client_secret``` variables near the top of the script. For getting your own Spotify Client ID and Secret see https://developer.spotify.com/documentation/general/guides/app-settings/#register-your-app

This program tries to be not too demanding on the Deezer and Spotify APIs by using a delay of 110ms between each track. You may have to increase the PHP execution timeout for this to work on larger playlists.
No guarantees that this program complies with current or future API terms of service and any other conditions.

# License
Copyright © 2018 Marvin van Bakkum

This project is licensed under the [GNU General Public License](https://www.gnu.org/licenses/gpl.html).
The full license can be found in [COPYING.md](COPYING.md).