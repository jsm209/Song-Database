(function() {
  "use strict";

  // yo if for some reason you looked at the source files tab when you inspected
  // the elements and found this password, good for you, you sly dog you.
  // but seriously i'll replace this with a backend solution later, for now
  // this will pass.
  let PASSWORD = "rainydawg";

  window.addEventListener("load", initialize);

  function initialize() {
    // Event listener for the submit button at the end of the form for adding
    // songs to the database.
    $("submit").addEventListener("click", function() {
      if ($("song_name").value != "" && $("artist_name").value != "" &&
          $("song_release_date").value != "" && $("album_name").value != "" &&
          $("song_genre").value != "" && $("song_medium").value != "") {
            submit();
      } else {
        $("feedback").innerText = "Please fill out the entire form before submitting.";
      }
    });

    // Event listener for clicking the menu button for "Add Song".
    $("search").addEventListener("click", searchSongs);
    $("menu-add").addEventListener("click", function() {
      $("song-form").classList.remove("hidden");
      $("song-search").classList.add("hidden");
      $("results-area").classList.add("hidden");
    });

    // Event listener for clicking the menu button for "Search".
    $("menu-search").addEventListener("click", function() {
      $("song-form").classList.add("hidden");
      $("song-search").classList.remove("hidden");
      $("results-area").classList.remove("hidden");
    });

    // Event listener for login button.
    $("passwordSubmit").addEventListener("click", function() {
      if ($("password").value == PASSWORD) {
        $("login").classList.add("hidden");
        $("song-search").classList.remove("hidden");
        $("results-area").classList.remove("hidden");
        $("titlebar").classList.remove("hidden");
      } else {
        $("passwordHelp").innerText = "Incorrect password.";
        $("password").value = "";
      }
    });
  }

  /**
   * Using the values input by the user into the song submit form, will add
   * a song to the database. Once it adds the song, it will tell the user it
   * has done so and clear the form of its fields.
   */
  function submit() {
    let params = new FormData();
    params.append("song_name", $("song_name").value);
    params.append("artist_name", $("artist_name").value);
    params.append("song_release_date", $("song_release_date").value);
    params.append("album_name", $("album_name").value);
    params.append("song_genre", $("song_genre").value);
    params.append("song_medium", $("song_medium").value);
    fetch("insert_song.php", {
      method: "POST",
      body: params
    })
    .then(JSON.stringify)
    .then(JSON.parse)
    .then(console.log)
    .then(function() {
      // Gives user feedback that song has been added, clears the form.
      $("feedback").innerText = "Successfully added " + $("song_name").value + " to the library!";
      $("song_name").value = "";
      $("artist_name").value = "";
      $("song_release_date").value = "";
      $("album_name").value = "";
      $("song_genre").value = "";
      $("song_medium").value = "";
    })
    .catch(function(error) {
      console.log("we giving errors like: " + error);
      $("feedback").innerText = error;
    });
  }

  /**
   * Will delete the song from the database that is contained in this
   * particular table data row.
   *
   * WARNING: There is a key assumption that the previous element in the
   * DOM is the element containing the id number of the song.
   * In other words, the delete button immediately comes after the ID in the
   * table for this to work.
   */
  function delete_song() {
    let songName = this.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerText;
    if (confirm("Are you sure you want to delete " + songName + "?")) {
      let params = new FormData();
      let id = this.previousSibling.innerText;
      params.append("id", id);
      fetch("delete_song.php", {method: "POST", body: params})
        .then(checkStatus)
        .then(function(response) {
          searchSongs();
        })
        .catch(console.log);
    }
  }

  /**
   * Will search the song database by the given category and search term.
   * After doing so, will render the results in the results table.
   */
  function searchSongs() {
    fetch("search.php?column=" + $("search-column").value + "&term=" + $("search-term").value)
    .then(checkStatus)
    .then(JSON.parse)
    .then(renderSongs)
    .catch(console.log);
  }

  /**
   * Given an array of songs, where each song is an array containing information
   * about the song, will add a row to the table of results for each song containing
   * the corresponding information for each song.
   * @param {Array} songArray - The array of arrays of songs and their information.
   */
  function renderSongs(songArray) {
    $("results-table").innerHTML = "";
    console.log(songArray);
    let tableArea = $("results-table");
    songArray.forEach((songObj) => {
      let newRow = document.createElement("tr");
      let keys = Object.keys(songObj);
      keys.forEach((key) => {
        // This has been commented out because the ID needs to be shown for
        // easy song deletion. May or may not be reverted later.
        //if (key != "id") {
          let data = document.createElement("td");
          data.innerText = songObj[key];
          newRow.appendChild(data);
        //}
      });
      let btn = document.createElement("button");
      btn.innerText = "X";
      btn.addEventListener("click", delete_song);
      newRow.appendChild(btn);
      tableArea.appendChild(newRow);
    });
  }

  /* ------------------------------ Helper Functions  ------------------------------ */
  // Note: You may use these in your code, but do remember that your code should not have
  // any functions defined that are unused.

  /**
   * Returns the element that has the ID attribute with the specified value.
   * @param {string} id - element ID
   * @returns {object} DOM object associated with id.
   */
  function $(id) {
    return document.getElementById(id);
  }

  /**
   * Returns the first element that matches the given CSS selector.
   * @param {string} query - CSS query selector.
   * @returns {object} The first DOM object matching the query.
   */
  function qs(query) {
    return document.querySelector(query);
  }

  /**
   * Returns the array of elements that match the given CSS selector.
   * @param {string} query - CSS query selector
   * @returns {object[]} array of DOM objects matching the query.
   */
  function qsa(query) {
    return document.querySelectorAll(query);
  }

  /**
	 * Helper function to return the response's result text if successful, otherwise
	 * returns the rejected Promise result with an error status and corresponding text
	 * @param {object} response - response to check for success/error
	 * @returns {object} - valid result text if response was successful, otherwise rejected
	 *                     Promise result
	 */
	function checkStatus(response) {
		if (response.status >= 200 && response.status < 300 || response.status == 0) {
			return response.text();
		} else {
			return Promise.reject(new Error(response.status + ": " + response.statusText));
		}
  }

})();
