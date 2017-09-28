<?php

namespace sisVentas;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    //
     // hacer referencia hacia la tabla 

    protected $table='persona';

//Declara Atributos
    protected $primaryKey='idpersona';

//larabel  permite adicionar a la tabla 2 columna especifica cuando fue creado  y actualizado
//  agrege automaticamente - true 
//pero nuestro caso no necesitamos esas columnas

    public $timestamps=false;

//declaramos los atributos de filllable se le indica como arreglo
    protected $fillable =[
    	'tipo_persona',
    	'nombre',
    	'tipo_documento',
    	'num_documento',
    	'direccion',
    	'telefono',
    	'email'
    
    ];
    
//tambien podemos agregar atributos de tipo guarded
//los campo se especifica si se asigna ala modelo
    protected $guarded =[

    ];
}
