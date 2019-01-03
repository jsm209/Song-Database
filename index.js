//
// -- your description of what this file does here --
//

(function() {
  "use strict";

  // MODULE GLOBAL VARIABLES, CONSTANTS, AND HELPER FUNCTIONS CAN BE PLACED
  // HERE

  /**
   *  Add a function that will be called when the window is loaded.
   */
  window.addEventListener("load", initialize);

  /**
   *  CHANGE: Describe what your initialize function does here.
   */
  function initialize() {
    $("submit").addEventListener("click", function() {
      console.log("This button was clicked.");
      submit();
    });
  }

  /**
   *  Make sure to always add a descriptive comment above
   *  every function detailing what it's purpose is
   *  Use JSDoc format with @param and @return.
   */
  function exampleFunction1() {
    /* SOME CODE */
  }

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
    .then(checkStatus)
    .then(JSON.parse)
    .then(console.log())
    .catch(console.log);
  }

  /**
   *  Make sure to always add a descriptive comment above
   *  every function detailing what it's purpose is
   *  @param {variabletype} someVariable This is a description of someVariable, including, perhaps, preconditions.
   *  @returns A description of what this function is actually returning
   */
  function exampleFunction2(someVariable) {
    /* SOME CODE */
    return something;
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
