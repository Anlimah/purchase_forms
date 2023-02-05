function flashMessage(bg_color, message) {
    const flashMessage = document.getElementById("flashMessage");

    flashMessage.classList.add(bg_color);
    flashMessage.innerHTML = message;
    
    setTimeout(function() {
        $("#flashMessage").fadeIn(500).addClass("show");
      }, 500);
    
      setTimeout(function() {
        $("#flashMessage").fadeOut(500, function() {
          $("#flashMessage").remove();
        });
      }, 5000);
}