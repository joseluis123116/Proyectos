<?php

namespace sisVentas\Http\Requests;
use Illuminate\Http\Request;

use Illuminate\Foundation\Http\FormRequest;


class CategoriaFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //por defecto el retorno es false debemos cambiarlo a true
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
           
//Agregamos las reglas ,revisando la bd
// si es requerido o que si es nulo y la cantidad 
//dato : ojo el nombre que se pone aqui no es de la bd sino es el objeto del html  
            'nombre'=>'required|max:50',
            'descripcion'=>'max:256',
        ];
    }
}
