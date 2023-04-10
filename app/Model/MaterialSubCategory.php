<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Model;

class MaterialSubCategory extends Model
{
    //use Validator;
    protected $primaryKey = "sub_cat_id";
    protected $table = "material_subcategory";
    protected $fillable = ['erp_id', 'cat_id', 'name', 'name_ar', 'description', 'status'];
    // protected $attributes = ['is_synced'=>0];
    protected static $columns = [
        "created_at" => "Created Date",
        "created_by" => "Created By",
        "updated_at" => "Modified Date",
        "updated_by" => "Modified By",
        "deleted_at" => "Deleted Date"
    ];
    protected $hidden = ['created_source','created_at','created_by','updated_source','updated_at','updated_by','deleted_at'];
    public $timestamps = true;

    function category() {
		return $this->belongsTo('App\Model\MaterialCategory', 'cat_id','cat_id');
	}

    function image() {
        return $this->belongsTo('App\Model\Image', 'sub_cat_id','ref_id')->where('ref_type','subcategory')->select(['img_id', 'ref_id', "path"]);
	}

    function materials() {
        return $this->hasMany('App\Model\Material', 'sub_cat_id','sub_cat_id');
    }

    function add($data) {
        try {
			$material_subcat =  parent::add($data);
			return $material_subcat;
		}
		catch(\Exception $ex){
			Error::trigger("material_subcat.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $sub_cat_id, array $change_conditions = []) {
        try {
			$material_subcat = parent::change($data, $sub_cat_id, $change_conditions);
			return $material_subcat;
		} catch(Exception $ex) {
			Error::trigger("material_subcat.change", [$ex->getMessage()]);
		}
    }
}
