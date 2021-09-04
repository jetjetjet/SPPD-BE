<?php

namespace App\Wilayah\Models;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
	protected $table = 'desa';
  public $timestamps = false;
  protected $fillable = ['kecamatan_id', 'name'];
}
