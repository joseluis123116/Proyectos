<?php

namespace sisVentas\Http\Controllers;

use Illuminate\Http\Request;


use sisVentas\User;
// hacemos referencia al redirect para redireccionar
use Illuminate\Support\Facades\Redirect;
//referencia al request
use sisVentas\Http\Requests\UsuarioFormRequest;
//referencias DB clase de laravel
use DB;

use sisVentas\Http\Controllers\Controller;


class UsuarioController extends Controller
{
    //

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
            $usuarios=DB::table('users')
            ->where('name','LIKE','%'.$query.'%')
            //->where('a.nombre','LIKE','%'.$query.'%')
            ->orderBy('id','desc')
            //paginacion de los registro
            ->paginate(5);
            //retornamos la vista 
            return view('seguridad.usuario.index',["usuarios"=>$usuarios,"searchText"=>$query]);
        }
    }


 //funcion create 
    public function create()
    {

        return view("seguridad.usuario.create");
    }

   public function store (UsuarioFormRequest $request)
    {
        $usuario=new User;
        $usuario->name=$request->get('name');
        $usuario->email=$request->get('email');
		$usuario->password=bcrypt($request->get('password'));
        $usuario->save();
        return Redirect::to('seguridad/usuario');
    }

    public function edit($id)
    {   //llamo un  formulario edit para modificar y luego seguridadar
        return view("seguridad.usuario.edit",["usuario"=>User::findOrFail($id)]);
    }//actualizar
    public function update(UsuarioFormRequest $request,$id)
    {
        //recibimos 2 parametros (objeto /request *- *- id usuario que se va modificar)

        $usuario=User::findOrFail($id);
        $usuario->name=$request->get('name');
        $usuario->email=$request->get('email');
		$usuario->password=bcrypt($request->get('password'));
        $usuario->update();
        return Redirect::to('seguridad/usuario');
    }//eliminar objeto - solo ocultamos  por eso la condicion es 0


    public function destroy($id)
    {
        $usuario= DB::table('users')
        ->where('id','=',$id)
        ->delete();
    
        return Redirect::to('seguridad/usuario');
    }






}
