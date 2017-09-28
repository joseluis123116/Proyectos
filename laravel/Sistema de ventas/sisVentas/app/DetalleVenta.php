<?php

namespace sisVentas;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    //
    // hacer referencia hacia la tabla 

    protected $table='detalle_venta';

//Declara Atributos
    protected $primaryKey='iddetalle_venta';

//larabel  permite adicionar a la tabla 2 columna especifica cuando fue creado  y actualizado
//  agrege automaticamente - true 
//pero nuestro caso no necesitamos esas columnas

    public $timestamps=false;

//declaramos los atributos de filllable se le indica como arreglo
    protected $fillable =[
    
    	'idventa',
    	'idarticulo',
    	'cantidad',
    	'precio_venta',
    	'descuento'
    ];
    
//tambien podemos agregar atributos de tipo guarded
//los campo se especifica si se asigna ala modelo
    protected $guarded =[

    ];
}
