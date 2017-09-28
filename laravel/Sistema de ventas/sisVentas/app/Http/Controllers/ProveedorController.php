<?php

namespace sisVentas\Http\Controllers;

use Illuminate\Http\Request;

use sisVentas\Persona;
// hacemos referencia al redirect para redireccionar
use Illuminate\Support\Facades\Redirect;
//referencia al request
use sisVentas\Http\Requests\PersonaFormRequest;
//referencias DB clase de laravel
use DB;

use sisVentas\User;
use sisVentas\Http\Controllers\Controller;


class ProveedorController extends Controller
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

            $personas=DB::table('persona')
            //Busqueda
            ->where('nombre','LIKE','%'.$query.'%')

            //Busqueda por tipo tipo cliente
            ->where ('tipo_persona','=','Proveedor')

             // busqueda por numero de documento //la condicion or where es para  realizar otro tipo de busqueda
            ->orwhere('num_documento','LIKE','%'.$query.'%')

            //Busqueda por tipo tipo cliente
            ->where ('tipo_persona','=','Proveedor')


            ->orderBy('idpersona','desc')
            //paginacion de los registro
            ->paginate(5);
            //retornamos la vista  Cliente de ventas
            return view('compras.proveedor.index',["personas"=>$personas,"searchText"=>$query]);
        }
    }

    //funcion create 
    public function create()
    {
        //retornamos a ala vista 
        return view("compras.proveedor.create");
    }

    //funcion para ventasar- y validamos la persona request

    public function store (PersonaFormRequest $request)
    {
        $persona=new Persona;
        $persona->tipo_persona='Proveedor';
        $persona->nombre=$request->get('nombre');
        $persona->tipo_documento=$request->get('tipo_documento');
        $persona->num_documento=$request->get('num_documento');
        $persona->direccion=$request->get('direccion');
        $persona->telefono=$request->get('telefono');
        $persona->email=$request->get('email');
        $persona->save();
        return Redirect::to('compras/proveedor');

    }
    // mostrar 
    public function show($id)
    {    //enviando  parametro del modelo persona hacia la funcion findorfail
        return view("compras.proveedor.show",["persona"=>Persona::findOrFail($id)]);
    }//editar
    public function edit($id)
    {   //llamo un  formulario edit para modificar y luego ventasar
        return view("compras.proveedor.edit",["persona"=>Persona::findOrFail($id)]);
    }//actualizar
    public function update(PersonaFormRequest $request,$id)
    {
        //recibimos 2 parametros (objeto /request *- *- id persona que se va modificar)

        $persona=Persona::findOrFail($id);

        $persona->nombre=$request->get('nombre');
        $persona->tipo_documento=$request->get('tipo_documento');
        $persona->num_documento=$request->get('num_documento');
        $persona->direccion=$request->get('direccion');
        $persona->telefono=$request->get('telefono');
        $persona->email=$request->get('email');

        $persona->update();
        return Redirect::to('compras/proveedor');
    }//eliminar objeto - solo ocultamos  por eso la condicion es 0

    public function destroy($id)
    {
        $persona=Persona::findOrFail($id);
        $persona->tipo_persona='Inactivo';
        $persona->update();
        return Redirect::to('compras/proveedor');
    }
}
