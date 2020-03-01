// Scroll to top button javascript
// Get the button
toTopButton = document.getElementById("toTopButton");

var scroll = function() {
  var y = window.scrollY;
  if (y >= 200) {
    toTopButton.style.display = "block";
  } else {
    toTopButton.style.display = "none";
  }
};

// Scroll to top on button click
function goToTop() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}

window.addEventListener("scroll", scroll);