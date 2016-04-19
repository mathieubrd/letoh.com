function Rating(starCount, imgSize, divID) {
	this.starCount = starCount;
  this.imgSize = imgSize;
	this.plainStar = "assets/images/plain_star.png";
	this.emptyStar = "assets/images/star.png";
  this.divID = divID;

  this.selectStar = function() {
    alert("lol");
  };
  
  var div = document.getElementById(this.divID);
  div.style.border = "1px solid black";
  div.style.display = "inline-block";
  div.onclick = function() {
    this.selectStar();
  }
    
  for (var i = 0; i < this.starCount; i++) {
    var img = document.createElement("img");
    img.src = this.emptyStar;
    img.height = this.imgSize;

    div.appendChild(img);
  }
}