<div class="modal fade" id="add_recharges" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title2" id="exampleModalLabel">Add Recharge</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="add_driver_data">
            <div class="row">
              <div class="col">
                <input type="hidden" name="seller_id" id="seller_id_recharge" value="">
                <input type="hidden" name="url" id="url" value="">
                <input type="number" maxlength="30" minlength="0" class="form-control input_Length"  value="" placeholder="Recharge Money" name="recharge" id="recharge" required>
                <div class="lengthmessage">
                 @csrf
                </div>
              </div>
           
            </div>
            <br>
          
        
         
         
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary add_recharge">Add</button>
        </div>
      </div>
    </form>
    </div>
  </div>



  