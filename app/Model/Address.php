<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Model;
use App\Message\Error;
use App\Validator\Address as Validator;
use DB;

class Address extends Model{
    use Validator;
  
    protected $primaryKey = "address_id";
    protected $table = "addresses";
    protected $fillable = ['address', 'address_title', 'latitude', 'longitude', 'status', 'created_source', 'created_by', 'created_at', 'updated_source', 'updated_by', 'updated_at'];
    protected $hidden = ['created_source','created_at','created_by','updated_source','updated_at','updated_by','deleted_at'];
    protected $attributes = [];

    public static function getTableColumns() {
        return self::$columns;
    }

    function add($data){
        if(!isset($data["status"])){
            $data["status"] = 1;
        }

        try{
            $address = parent::add($data);
            return $address;
        }
        catch(\Exception $ex){
            Error::trigger("address.add", [$ex->getMessage()]) ;
        }
    }

    function change(array $data, $address_id, array $change_conditions = []){
        if(!isset($data["status"])){
            $data["status"] = 1;
        }

        try {
           return parent::change($data, $address_id, $change_conditions);
        }
        catch(Exception $ex){
            Error::trigger("address.change", [$ex->getMessage()]) ;
        }

    }

    public function NewAddress(array $parm)
    {
      $address =  $this->add($parm);
      if(!is_object($address)){
        $errors[] = Error::get('address.add');
        return false;
      }
      else {
        return $address;
      }
    }

}