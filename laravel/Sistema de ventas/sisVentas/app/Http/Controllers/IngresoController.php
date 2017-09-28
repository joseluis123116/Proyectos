<?php

namespace sisVentas\Http\Controllers;

use Illuminate\Http\Request;
//use sisVentas\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use sisVentas\Http\Requests\IngresoFormRequest;
use sisVentas\Ingreso;
use sisVentas\DetalleIngreso;
use DB;
// para usar el formato de fecha y hora de la zona  almacenamos aqui
use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;


use sisVentas\User;
use sisVentas\Http\Controllers\Controller;








class IngresoController extends Controller
{
    //

    	 // creamos el constructor
    public function __construct()
    {
        $this->middleware('auth');

    }

      public function index(Request $request)
    {
          // validamos si existe  
        if ($request)
        {
            //determinamos el texto de busqueda y se almacena en el query
            $query=trim($request->get('searchText'));

            /*creamos una segunda variable llamada ingreso para consultar a la bd y unir ,en subtotal no esta agregado en la bd  entonces lo llamaos en laravel con el comando  DB::raw('sum(di.cantidad*precio_compra)'as total)*/

            $ingresos=DB::table('ingreso as i')
            ->join('persona as p','i.idproveedor','=','p.idpersona')
            ->join('detalle_ingreso as di','i.idingreso','=','di.idingreso')
            ->select('i.idingreso','i.fecha_hora','p.nombre','i.tipo_comprobante','i.serie_comprobante','i.num_comprobante','i.impuesto','i.estado',DB::raw('sum(di.cantidad*precio_compra)as total'))
            ->where('i.num_comprobante','LIKE','%'.$query.'%')
            ->orderBy('idingreso','desc')
            ->groupBy('i.idingreso','i.fecha_hora','p.nombre','i.tipo_comprobante','i.serie_comprobante','i.num_comprobante','i.impuesto','i.estado')
            //paginacion de los registro
            ->paginate(5);
            //retornamos la vista 
            return view('compras.ingreso.index',["ingresos"=>$ingresos,"searchText"=>$query]);
        }
    }


    public function create()
    {

    	$personas=DB::table('persona')->where('tipo_persona','=','Proveedor')->get();
    	$articulos=DB::table('articulo as art')
    	->select(DB::raw('CONCAT(art.codigo," ",art.nombre) AS articulo'),'art.idarticulo')
    	->where('art.estado','=','Activo')
    	->get();

    return view("compras.ingreso.create",["personas"=>$personas,"articulos"=>$articulos]);

    }

    public function store(IngresoFormRequest $request)
    {

		try {
			/*iniciamos una transaccion porque debemos almacenar en BD el ingreso y despues su detaller pero se deben almacenar ambos por algun problema se registra ingreso como su detalles*/

			DB::beginTransaction();

			/*agregamos  todos los atributos del modelo*/
			$ingreso =new Ingreso;
			$ingreso->idproveedor=$request->get('idproveedor');
			$ingreso->tipo_comprobante=$request->get('tipo_comprobante');
			$ingreso->serie_comprobante=$request->get('serie_comprobante');
			$ingreso->num_comprobante=$request->get('num_comprobante');
			
			//llamamos al tiempo
			$mytime=Carbon::now('America/Lima');
			$ingreso->fecha_hora=$mytime->toDateTimeString();
			$ingreso->impuesto='18';
			$ingreso->estado='A';
			$ingreso->save();


			/*desde un solo formulario enviaremo los datos de modelo.Ademas
			Vamos a enviar la variable para almacenar los articulos al detalle ingreso 
			
			dato: no solo enviamos un detalle sino un arreglo de detalle

			*/
			$idarticulo=$request->get('idarticulo');
			$cantidad=$request->get('cantidad');
			$precio_compra=$request->get('precio_compra');
			$precio_venta=$request->get('precio_venta');



			/*vamos a crear una estructura interactiva humana para que recorra el articulo
			Declaramos la variable para que recorra el arreglo	
			*/

			// creamos variable que inicie en 0 para la busqueda
			$cont = 0;
			// la cantida que ingresar de articulo
			while ($cont< count($idarticulo)) 
            {

			/* creamos un objeto detalle para hacer referencia a nuestro modelo detalle_ingreso*/

			$detalle = new DetalleIngreso();

			/* envio el ingreso del objeto id ingreso de objeto de ingreso  de la transaccion y tiene un id ingreso autogenerado*/
			$detalle->idingreso=$ingreso->idingreso;

			/* recibimos el arreglo de idarticulo ademas envia la posicion 0   almacena  y actualiza el contador y recorre  hasta que se cumple que el contador sea menor a la cantidad de los articulos*/
			$detalle->idarticulo=$idarticulo[$cont];
			$detalle->cantidad=$cantidad[$cont];
			$detalle->precio_compra=$precio_compra[$cont];
			$detalle->precio_venta=$precio_venta[$cont];
			$detalle->save();

				//actualizamos
				$cont=$cont+1;
			}


			DB::commit();

		} 
		catch (Exception $e) 
		{
			/* si sucede algun error se anula*/
			DB::rollback();
		}

		return Redirect::to('compras/ingreso');
    }


    public function show($id)
    {
    	// declaro la variable ingreso  va hacer igual al ingreso del index 
    	$ingreso=DB::table('ingreso as i')
            ->join('persona as p','i.idproveedor','=','p.idpersona')
            ->join('detalle_ingreso as di','i.idingreso','=','di.idingreso')
            ->select('i.idingreso','i.fecha_hora','p.nombre','i.tipo_comprobante','i.serie_comprobante','i.num_comprobante','i.impuesto','i.estado',DB::raw('sum(di.cantidad*precio_compra)as total'))
            /*como solo necesito un solo ingreso indico con el where  indico que sea igual a la variable id del show */
            ->where('i.idingreso','=',$id)

            ->groupBy('i.idingreso','i.fecha_hora','p.nombre','i.tipo_comprobante','i.serie_comprobante', 'i.num_comprobante','i.impuesto','i.estado')

            /*Como no llamo a ningun group by, se soluciona con el metodo first osea que coincida con el  id  con el de la condicion */
            ->first();

            /*Ahora creamos una nueva variable detalles y se muestra todo los detalle de ese valor en  especifico--unimos con la tabla ingreso  y definimos
            */

            $detalles=DB::table('detalle_ingreso as d')
            ->join('articulo as a','d.idarticulo','=','a.idarticulo')
            ->select('a.nombre as articulo','d.cantidad','d.precio_compra','d.precio_venta')
            ->where('d.idingreso','=',$id)->get();

        return view("compras.ingreso.show",["ingreso"=>$ingreso,"detalles"=>$detalles]);    

    }


    public  function destroy($id)
    {
    	// cuando se destruya una tabla cambiara  de estado A a C
    	$ingreso=Ingreso::findOrFail($id);
    	$ingreso->Estado='C';
    	$ingreso->update();
    	return Redirect::to('compras/ingreso');

    }

}
