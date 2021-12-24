(function(){
  var shareIcon = document.querySelector('ul.dropdown-menu li.share-icon');
  if (shareIcon) {
    shareIcon.addEventListener("click", function(e) {
      e.stopPropagation();
      e.preventDefault();
      this.classList.toggle("open");
      this.parentNode.classList.toggle("open");
    });
  }
})();