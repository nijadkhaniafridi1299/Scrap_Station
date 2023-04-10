<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Model;

class Material extends Model
{
    //use Validator;
    protected $primaryKey = "mat_id";
    protected $table = "material";
    protected $fillable = ['erp_id','product_code', 'sub_cat_id', 'name', 'name_ar', 'basic_unit', 'description', 'start_price', 'end_price', 'status'];
    protected $hidden = ['created_source','created_at','created_by','updated_source','updated_at','updated_by','deleted_at'];
    // protected $attributes = ['is_synced'=>0];
    protected static $columns = [
        "created_at" => "Created Date",
        "created_by" => "Created By",
        "updated_at" => "Modified Date",
        "updated_by" => "Modified By",
        "deleted_at" => "Deleted Date"
    ];
    protected $appends = ['subcategoryname','subcategoryname_ar','categoryname','categoryname_ar'];
    public $timestamps = true;

    function subcategory() {
		return $this->belongsTo('App\Model\MaterialSubCategory', 'sub_cat_id','sub_cat_id');
	}

    function image() {
        return $this->belongsTo('App\Model\Image', 'mat_id','ref_id')->where('ref_type','category')->select(['img_id', 'ref_id',"path"]);
	}

    function sellerlistings() {
        return $this->hasMany('App\Model\SellerListing', 'mat_id','mat_id')->where("status",7);
    }

    function verifiedsellerlistings() {
        return $this->hasMany('App\Model\SellerListing', 'mat_id','mat_id')->where("status",7)->where("is_verified",1);
    }

    function verifiedbuyerlistings() {
        return $this->hasMany('App\Model\BuyerListing', 'mat_id','mat_id')->where("status",7)->where("is_verified",1);
    }

    function getSubcategorynameAttribute() {
        return $this->subcategory->name;
	}
    function getSubcategorynamearAttribute() {
        return $this->subcategory->name_ar;
	}

    function getCategorynameAttribute() {
		return $this->subcategory->category->name;
	}

    function getCategorynamearAttribute() {
		return $this->subcategory->category->name_ar;
	}

    function add($data) {
        try {
			$material =  parent::add($data);
			return $material;
		}
		catch(\Exception $ex){
			Error::trigger("material.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $mat_id, array $change_conditions = []) {
        try {
			$material = parent::change($data, $mat_id, $change_conditions);
			return $material;
		} catch(Exception $ex) {
			Error::trigger("material.change", [$ex->getMessage()]);
		}
    }
}
