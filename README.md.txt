# Rainy Dawg Radio Song Database Project

Simple database project to manage songs of all mediums at Rainy Dawg Radio (RDR). Needs to be simple and easy to use for volunteers so that people can do volunteer hours/have a useful system to check if we have music. _The goal is if it's easy to use, then volunteer DJs can use it to build their show using our music, and if they use our music, DJs will move away from Spotify, and if that happens, RDR can be more professional/possibly become an FM radio station as opposed to being purely internet based._

## Deliverables:
- Users need to be able to insert songs with pertinent information.
- Users need to be able to search songs by the song name, artist name, and genre at the very least, however searching by all qualities would be great.
- Users need to be able to "delete" songs.


## Remaining Features/Tasks
- Users able to search songs
- Users able to delete songs (one way to do this would be to simply mark the song with a bool so it won't be displayed, and later a manager can manually remove them from the table later to confirm their deletion)
- Front end for song search

## Things that would be cool but are not necessary:
- Login system for users so we can track who is doing what.
- Front-end uses react instead.

## Implementation things you should probably know:
- Database is a multitable database, one for the artist, one for the album, one for the songs. I did this because artists may be responsible for multiple songs, or multiple songs can belong to multiple albums.
- Most of the code inserting is in common.php, along with some shared functions for sending success/error/debug messages.

## Concerns:
- Database isn't secure/backedup in any way, and not sure how to go about anything database security related. Would be a shame if the entire song database just disappeared one day.

