<div class="modal fade" id="pictureModal" data-bs-backdrop="static" tabindex="-1"  aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title2" id="exampleModalLabel">Images</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="modal-body">
                <img id="image_src" src="" class="img-responsive" style="height: 400px; width:400px;">
              </div>
              </div>
    </div>
  </div>
  {{-- //images --}}
<script>
  $(document).ready(function() {
  // $('.img-thumbnail').click(function() {
    $('.modal').delegate('.img-thumbnail', 'click',function (e){
    // $('#exampleModal').modal('show');
    var imgSrc = $(this).attr('src');
    $('#image_src').attr('src', imgSrc);
  });
  $('.modal ').delegate("#images", "click" , function(){
    
     $('#pictureModal').modal('show');

   
  });
});
function showImagesDetails(image, img_ttl_var, img_var){
  console.log(image.length);
  if(image.length > 0){
    let listing_images = '';
    let image_table = '<h6 class="mt-1 mb-1 pb-0 pt-0 text-center">Images </h6> <div class="table-responsive" style="overflow-x: scroll;"> <table class="table"> <thead> <tr> </tr> </thead> <tbody> <tr id="listing_images" class="images_empty"> </tr> </tbody> </table> </div>';
    $('#'+img_ttl_var).html(image_table);
    $.each(image, function(index, element) {
      listing_images += '<td class="py-1"> <a id="images" href="javascript:void(0)"><img style = "width:30px; height:30px;" src="'+element.path+'" class="img-thumbnail"></a></td>';
      // listing_images += '<div class="col-md-1">  </div>';
    });
    // console.log(listing_images);
    // console.log(img_var);

    $("#"+img_var).html(listing_images);
  }
}


</script>