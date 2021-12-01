//------------------------//------------------------//------------------------//
//------------------------//--------Gallery---------//------------------------//
//------------------------//------------------------//------------------------//

function Gallery(srcThumb, srcImage, title, id) {
  this.srcThumb = srcThumb;
  this.srcImage = srcImage;
  this.title = title;
  this.id = id;
}

Gallery.prototype.createMedia = function () {
  var obj = $("<a id = \"media" + this.id + "\" class=\"medias\" href=\"#\" data-toggle=\"tab\"><img class='image-from-gallery' src=\"" + this.srcThumb + "\" alt=\""+this.title+"\"></a>");
  obj.data('mediaObj', this);
  $('#galleryMediaGallery').append(obj);
};

Gallery.prototype.createPagination = function () {
  var obj = $("<ul><li id=\"fastbackwardmediagallery\"><a class=\"new-element\" ><i class=\"fa fa-fast-backward\"></i></a></li><li id=\"backwardmediagallery\" ><a class=\"new-element\" ><i class=\"fa fa-backward\"></i></a></li><li  ><span><b id=\"totalAssetsMediaGallery\" >"+totalAssetsMediaGallery+"</b> registros </span><span>Página <b id=\"pageNowMediaGallery\">" + pageNowMediaGallery + "</b> de <b id=\"totalPageAssetsMediaGallery\">" + totalPageAssetsMediaGallery + "</b></span></li><li id=\"forwardmediagallery\"><a class=\"new-element\"><i class=\"fa fa-forward\"></i></a></li><li id=\"fastforwardmediagallery\" ><a class=\"new-element\"><i class=\"fa fa-fast-forward\"></i></a></li></ul>");
  obj.data('mediaObj', this);
  $('#paginationMediaGallery').html(obj);
}

Gallery.prototype.validatePage = function () {
  $("#pageNowMediaGallery").html(pageNowMediaGallery);
  $("#totalAssetsMediaGallery").html(totalAssetsMediaGallery);
  $("#totalPageAssetsMediaGallery").html(totalPageAssetsMediaGallery);
  if (pageNowMediaGallery == 1) {
    $("#fastbackwardmediagallery").addClass("disabled");
    $("#backwardmediagallery").addClass("disabled");
    
  } else {
    $("#fastbackwardmediagallery").removeClass("disabled");
    $("#backwardmediagallery").removeClass('disabled');
  }
  if (pageNowMediaGallery == totalPageAssetsMediaGallery) {
    $("#fastforwardmediagallery").addClass('disabled');
    $("#forwardmediagallery").addClass('disabled');
  } else {
    $("#fastforwardmediagallery").removeClass('disabled');
    $("#forwardmediagallery").removeClass('disabled');
  }
}
Gallery.prototype.fastBackWardMediaGallery = function () {
  if(pageNowMediaGallery > 1){
    pageNowMediaGallery = 1;
    pageNowSendDataMediaGallery = 0;
    this.getAsset();
    this.validatePage();
  }
}
Gallery.prototype.backWardMediaGallery = function () {
  if(pageNowMediaGallery > 1){
    pageNowMediaGallery -= 1;
    pageNowSendDataMediaGallery -= 1;
    this.getAsset();
    this.validatePage();
  }
}

Gallery.prototype.forWardMediaGallery = function () {
  if(pageNowMediaGallery < totalPageAssetsMediaGallery){
    pageNowMediaGallery += 1;
    pageNowSendDataMediaGallery += 1;
    this.getAsset();
    this.validatePage();
  }
}

Gallery.prototype.fastForWardMediaGallery = function () {
  if(pageNowMediaGallery < totalPageAssetsMediaGallery){
    pageNowMediaGallery = totalPageAssetsMediaGallery;
    pageNowSendDataMediaGallery = totalPageAssetsMediaGallery - 1;
    this.getAsset();
    this.validatePage();
    
  }
}

Gallery.prototype.getAsset = function () {
  console.log(fullUrlBase + api + pageNowSendDataMediaGallery);
  $.ajax({
    type: 'GET',
    url: fullUrlBase + api + pageNowSendDataMediaGallery,
    success: function (responseText) {
       //Triggered if response status code is 200 (OK)
      console.log(responseText);
      totalAssetsMediaGallery = responseText.total;
      totalPageAssetsMediaGallery = responseText.total_pages;
      
      mediaGallery = [];
      for(var i = 0; i < responseText.items.length;i++){
        var item = responseText.items[i];
        mediaGallery.push( new Gallery(item.thumb,item.image,item.title,item.id));
      }
      
      $('#galleryMediaGallery').html("");
      for(var l = 0; l < mediaGallery.length; l++) {
		mediaGallery[l].createMedia();
		mediaGallery[l].mediaSelected();
	  }
    },
    complete: function (){
      setTimeout(function(){
        $('#galleryMediaGallery').show();
        $('#loaderMediaGallery').hide();
      },800);
    },
    beforeSend: function (){
      $('#galleryMediaGallery').hide();
      $('#loaderMediaGallery').show();
    }
  });
//  $.ajax({
//    type: 'GET',
//    url: fullUrlBase + 'api/sendmail/getassetmediagallery/' + pageNowSendDataMediaGallery
//  })
//    .done(function (responseText) {
//      // Triggered if response status code is 200 (OK)
//      console.log(responseText);
//      
//      mediaGallery = [];
//      for(var i = 0; i < responseText.items.length;i++){
//        var item = responseText.items[i];
//        mediaGallery.push( new Gallery(item.thumb,item.image,item.title,item.id));
//      }
//      
//      $('#galleryMediaGallery').html("");
//      $('#galleryMediaGallery').hide();
//      for(var l = 0; l < mediaGallery.length; l++) {
//		mediaGallery[l].createMedia();
//		mediaGallery[l].mediaSelected();
//	  }
//      $('#galleryMediaGallery').show(1000);
//    })
//      .beforeSend(function(){
//        alert("paginando");
//    })
//    .fail(function (jqXHR, status, error) {
//      // Triggered if response status code is NOT 200 (OK)
//      alert(jqXHR.responseText);
//    });
    
}

Gallery.prototype.mediaSelected = function () {
  var t = this;
  $('#galleryMediaGallery a#media' + this.id).on('click', function () {
    $('.image-galery-selected').removeClass('image-galery-selected');
    $(this).find('img').addClass('image-galery-selected');
    media.setGallery(t);
    media.imageSelected(t.srcImage, t.title);
  });
};
