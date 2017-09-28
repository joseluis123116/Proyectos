<?php

namespace sisVentas\Http\Controllers;

use Illuminate\Http\Request;

use sisVentas\Articulo;
// hacemos referencia al redirect para redireccionar
use Illuminate\Support\Facades\Redirect;
// para subir la imagen del cliente necesitamos 
use Illuminate\Support\Facades\Input;

//referencia al request
use sisVentas\Http\Requests\ArticuloFormRequest;
//referencias DB clase de laravel
use DB;

use sisVentas\User;
use sisVentas\Http\Controllers\Controller;


class ArticuloController extends Controller
{
    //
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
            $articulos=DB::table('articulo as a')
            // creamos un join porque esta relacionada con la tabla categoria
            ->join('categoria as c','a.idcategoria','=','c.idcategoria')
            // seleccionamos que cosas usaremos
            ->select('a.idarticulo','a.nombre','a.codigo','a.stock','c.nombre as categoria','a.descripcion','a.imagen','a.estado')
            // podemos agregar  otra condicion where para que muestre las categorias activas
            //->where('a.estado','=','Activo')
            ->where('a.nombre','LIKE','%'.$query.'%')
            ->orwhere('a.codigo','LIKE','%'.$query.'%')
            
            // $articulo->estado='Inactivo';
            //ordenamos
            ->orderBy('idarticulo','desc')
            //paginacion de los registro
            ->paginate(5);
            //retornamos la vista 
            return view('almacen.articulo.index',["articulos"=>$articulos,"searchText"=>$query]);
        }
    }

    //funcion create 
    public function create()
    {
    	//enviamos el listado de la categoria en un combo box
    	$categorias=DB::table('categoria')->where('condicion','=','1')->get();

        //retornamos a ala vista , ademas enviaremos el parametro categorias con sus valores
        return view("almacen.articulo.create",["categorias"=>$categorias]);
    }

    //funcion para almacenar- y validamos  articulo request con nuestro formulario del mantenimiento articulo

    public function store (ArticuloFormRequest $request)
    {
        $articulo=new Articulo;
        $articulo->idcategoria=$request->get('idcategoria');
        $articulo->codigo=$request->get('codigo');
        $articulo->nombre=$request->get('nombre');
        $articulo->stock=$request->get('stock');
        $articulo->descripcion=$request->get('descripcion');
		$articulo->estado='Activo';

		////cargamos la imagen

		//validamos la imagen  si esta vacio

		if (Input::hasFile('imagen')) {
			//creamos un archivo si no esta vacio y se almacena en esta variable
			$file=Input::file('imagen');
			//lo moveremos para que se guarde en la careta public - imagen -articulos
			$file-> move(public_path().'/imagenes/articulos/',$file->getClientOriginalName());
			//en nuestro objeto   vamos a enviarle
			$articulo->imagen=$file->getClientOriginalName();
		}

        $articulo->save();
        return Redirect::to('almacen/articulo');

    }
    // mostrar 
    public function show($id)
    {    //enviando  parametro del modelo categoria hacia la funcion findorfail
        return view("almacen.articulo.show",["articulo"=>Articulo::findOrFail($id)]);
    }//editar
    public function edit($id)
    {   

    	//implementamos una variable articulo con su referencia
    	$articulo=Articulo::findorfail($id);

    	//enviar el listado de categorias

    	$categorias=DB::table('categoria')
    	->where('condicion','=','1')->get();

    //llamo un  formulario edit para modificar y luego almacenar
    // obtenemos  la variable articulo y enviamos la categoria
        return view("almacen.articulo.edit",["articulo"=>$articulo,"categorias"=>$categorias]);


    }//actualizar
    public function update(ArticuloFormRequest $request,$id)
    {
        // valido antes de recibir 2 parametros (objeto /request *- *- id categoria que se va modificar)

        $articulo=Articulo::findOrFail($id);
       	$articulo->idcategoria=$request->get('idcategoria');
        $articulo->codigo=$request->get('codigo');
        $articulo->nombre=$request->get('nombre');
        $articulo->stock=$request->get('stock');
        $articulo->descripcion=$request->get('descripcion');
		$articulo->estado='Activo';

		////cargamos la imagen


		//validamos la imagen  si esta vacio

		if (Input::hasFile('imagen')) {
			//creamos un archivo si no esta vacio y se almacena en esta variable
			$file=Input::file('imagen');
			//lo moveremos para que se guarde en la careta public - imagen -articulos
			$file-> move(public_path().'/imagenes/articulos',$file->getClientOriginalName());
			//en nuestro objeto   vamos a enviarle
			$articulo->imagen=$file->getClientOriginalName();
		}

        $articulo->update();
        return Redirect::to('almacen/articulo');
    }//eliminar objeto - solo ocultamos  por eso la condicion es 0

    public function destroy($id)
    {
        $articulo=Articulo::findOrFail($id);
        $articulo->estado='Inactivo';
        $articulo->update();
        return Redirect::to('almacen/articulo');
    }



}
