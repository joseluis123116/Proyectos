<?php

namespace sisVentas;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    // hacer referencia hacia la tabla 

    protected $table='articulo';

//Declara Atributos
    protected $primaryKey='idarticulo';

//larabel  permite adicionar a la tabla 2 columna especifica cuando fue creado  y actualizado
//  agrege automaticamente - true 
//pero nuestro caso no necesitamos esas columnas

    public $timestamps=false;

//declaramos los atributos de filllable se le indica como arreglo
    protected $fillable =[
    	'idcategoria',
    	'codigo',
    	'nombre',
    	'stock',
    	'descripcion',
    	'imagen',
    	'estado'
    ];
    
//tambien podemos agregar atributos de tipo guarded
//los campo se especifica si se asigna ala modelo
    protected $guarded =[

    ];
}
