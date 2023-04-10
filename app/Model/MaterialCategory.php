<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Model;

class MaterialCategory extends Model
{
    //use Validator;
    protected $primaryKey = "cat_id";
    protected $table = "material_category";
    protected $fillable = ['erp_id', 'name', 'name_ar', 'description', 'status'];
    // protected $attributes = ['is_synced'=>0];
    protected static $columns = [
        "created_at" => "Created Date",
        "created_by" => "Created By",
        "updated_at" => "Modified Date",
        "updated_by" => "Modified By",
        "deleted_at" => "Deleted Date"
    ];
    public $timestamps = true;
    protected $hidden = ['created_source','created_at','created_by','updated_source','updated_at','updated_by','deleted_at'];

    function subcategories() {
        return $this->hasMany('App\Model\MaterialSubCategory', 'cat_id','cat_id');
    }

    function image() {
        return $this->belongsTo('App\Model\Image', 'cat_id','ref_id')->where('ref_type','category')->select(['img_id', 'ref_id',"path"]);
	}

    function add($data) {
        try {
			$material_cat =  parent::add($data);
			return $material_cat;
		}
		catch(\Exception $ex){
			Error::trigger("material_cat.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $cat_id, array $change_conditions = []) {
        try {
			$material_cat = parent::change($data, $cat_id, $change_conditions);
			return $material_cat;
		} catch(Exception $ex) {
			Error::trigger("material_cat.change", [$ex->getMessage()]);
		}
    }
}
