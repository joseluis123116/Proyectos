<?php

namespace sisVentas\Http\Controllers;

use Illuminate\Http\Request;
//agrege esto 
//use sisVentas\Http\Request;
// llamamos al proyecto "carpeta"
use sisVentas\Categoria;
// hacemos referencia al redirect para redireccionar
use Illuminate\Support\Facades\Redirect;
//referencia al request
use sisVentas\Http\Requests\CategoriaFormRequest;
//referencias DB clase de laravel
use DB;

use sisVentas\User;
use sisVentas\Http\Controllers\Controller;


class CategoriaController extends Controller
{


	 // creamos el constructor
    public function __construct()
    {
        $this->middleware('auth');

    }

    //agregamos la funcion index
    //recibe parametros el objeto  de tipo request
    public function index(Request $request)
    {
          // validamos si existe  
        if ($request)
        {
            //determinamos el texto de busqueda y se almacena en el query

            $query=trim($request->get('searchText'));


            // utilizamos la clase DB y especificamos la tabla

            $categorias=DB::table('categoria')->where('nombre','LIKE','%'.$query.'%')
            
            // podemos agregar  otra condicion where para que muestre las categorias activas
            ->where ('condicion','=','1')
            //ordenamos
            ->orderBy('idcategoria','desc')
            //paginacion de los registro
            ->paginate(5);
            //retornamos la vista 
            return view('almacen.categoria.index',["categorias"=>$categorias,"searchText"=>$query]);
        }
    }

    //funcion create 
    public function create()
    {
        //retornamos a ala vista 
        return view("almacen.categoria.create");
    }

    //funcion para almacenar- y validamos la categoria request

    public function store (CategoriaFormRequest $request)
    {
        $categoria=new Categoria;
        $categoria->nombre=$request->get('nombre');
        $categoria->descripcion=$request->get('descripcion');
        $categoria->condicion='1';
        $categoria->save();
        return Redirect::to('almacen/categoria');

    }
    // mostrar 
    public function show($id)
    {    //enviando  parametro del modelo categoria hacia la funcion findorfail
        return view("almacen.categoria.show",["categoria"=>Categoria::findOrFail($id)]);
    }//editar
    public function edit($id)
    {   //llamo un  formulario edit para modificar y luego almacenar
        return view("almacen.categoria.edit",["categoria"=>Categoria::findOrFail($id)]);
    }//actualizar
    public function update(CategoriaFormRequest $request,$id)
    {
        //recibimos 2 parametros (objeto /request *- *- id categoria que se va modificar)

        $categoria=Categoria::findOrFail($id);
        $categoria->nombre=$request->get('nombre');
        $categoria->descripcion=$request->get('descripcion');
        $categoria->update();
        return Redirect::to('almacen/categoria');
    }//eliminar objeto - solo ocultamos  por eso la condicion es 0
    public function destroy($id)
    {
        $categoria=Categoria::findOrFail($id);
        $categoria->condicion='0';
        $categoria->update();
        return Redirect::to('almacen/categoria');
    }



}
