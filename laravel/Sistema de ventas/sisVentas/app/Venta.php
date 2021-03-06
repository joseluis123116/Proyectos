<?php

namespace sisVentas;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    //

// hacer referencia hacia la tabla 

    protected $table='venta';

//Declara Atributos
    protected $primaryKey='idventa';

//larabel  permite adicionar a la tabla 2 columna especifica cuando fue creado  y actualizado
//  agrege automaticamente - true 
//pero nuestro caso no necesitamos esas columnas

    public $timestamps=false;

//declaramos los atributos de filllable se le indica como arreglo
    protected $fillable =[
    	'idcliente',
    	'tipo_comprobante',
    	'serie_comporbante',
    	'num_comprobante',
    	'fecha_hora',
    	'impuesto',
    	'total_venta',
    	'estado'
    ];
    
//tambien podemos agregar atributos de tipo guarded
//los campo se especifica si se asigna ala modelo
    protected $guarded =[

    ];


}
