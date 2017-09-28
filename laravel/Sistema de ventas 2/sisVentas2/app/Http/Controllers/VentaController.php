<?php

namespace sisVentas\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use sisVentas\Http\Requests\VentaFormRequest;

use sisVentas\Venta;
use sisVentas\DetalleVenta;
use DB;

use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;


use sisVentas\User;
use sisVentas\Http\Controllers\Controller;



class VentaController extends Controller
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
            $ventas=DB::table('venta as v')
            ->join('persona as p','v.idcliente','=','p.idpersona')
            ->join('detalle_venta as dv','v.idventa','=','dv.idventa')
            ->select('v.idventa','v.fecha_hora','p.nombre','v.tipo_comprobante','v.serie_comprobante','v.num_comprobante','v.impuesto','v.estado','v.total_venta')
            ->where('v.num_comprobante','LIKE','%'.$query.'%')
            ->orderBy('v.idventa','desc')
            ->groupBy('v.idventa','v.fecha_hora','p.nombre','v.tipo_comprobante','v.serie_comprobante','v.num_comprobante','v.impuesto','v.estado')
            //paginacion de los registro
            ->paginate(5);
            //retornamos la vista 
            return view('ventas.venta.index',["ventas"=>$ventas,"searchText"=>$query]);
        }
    }


    public function create()
    {
// la condicion solo traera solo cliente. hay caso que los provee tambien son  asi que solo omitimos el where
    	$personas=DB::table('persona')
      // ->where('tipo_persona','=','Cliente')
       ->get();
    	$articulos=DB::table('articulo as art')
    	// vamos a obtener el precio articulo  mediante  estableceremos el precio promedio del articulo/////////////////o el ultimo precio de venta de almacen
    	->join('detalle_ingreso as di','art.idarticulo','=','di.idarticulo')
    	->select(DB::raw('CONCAT(art.codigo," ",art.nombre) AS articulo'),'art.idarticulo','art.stock',DB::raw('AVG(di.precio_venta)as precio_promedio'))
    	->where('art.estado','=','Activo')
    	->where('art.stock','>','0')
        ->groupBy('articulo','art.idarticulo','art.stock')
    	->get();

    return view("ventas.venta.create",["personas"=>$personas,"articulos"=>$articulos]);

/*

 mostrar el ultimo precio de venta del articulo en vez del precio promedio, solo cambien el código por estas lineas:
 laravel antes de la 5.4

$articulos = DB::table('articulo as art')
            ->join('detalle_ingreso as di', 'art.idarticulo', '=', 'di.idarticulo')
            ->select(DB::raw('CONCAT(art.codigo, " ", art.nombre) as articulo'), 'art.idarticulo', 'art.stock', 'di.precio_venta')
            ->where('art.estado', '=', 'Activo')
            ->where('art.stock', '>', '0')
            ->orderBy('di.iddetalle_ingreso', 'DESC')
            ->limit(1)
            ->get();  


*/





    }

    public function store(VentaFormRequest $request)
    {

		try {
			/*iniciamos una transaccion porque debemos almacenar en BD el ingreso y despues su detaller pero se deben almacenar ambos por algun problema se registra ingreso como su detalles*/

			DB::beginTransaction();

			/*agregamos  todos los atributos del modelo*/
			$venta =new Venta;
			$venta->idcliente=$request->get('idcliente');
			$venta->tipo_comprobante=$request->get('tipo_comprobante');
			$venta->serie_comprobante=$request->get('serie_comprobante');
			$venta->num_comprobante=$request->get('num_comprobante');
			$venta->total_venta=$request->get('total_venta');
			//llamamos al tiempo
			$mytime=Carbon::now('America/Lima');
			$venta->fecha_hora=$mytime->toDateTimeString();
			$venta->impuesto='18';
			$venta->estado='A';
			$venta->save();


			/*desde un solo formulario enviaremo los datos de modelo.Ademas
			Vamos a enviar la variable para almacenar los articulos al detalle ingreso 
			
			dato: no solo enviamos un detalle sino un arreglo de detalle

			*/
			$idarticulo= $request->get('idarticulo');
			$cantidad= $request->get('cantidad');
			$descuento= $request->get('descuento');
			$precio_venta= $request->get('precio_venta');



			/*vamos a crear una estructura interactiva humana para que recorra el articulo
			Declaramos la variable para que recorra el arreglo	
			*/

			// creamos variable que inicie en 0 para la busqueda
			$cont = 0;
			// la cantida que ingresar de articulo
			while ($cont< count($idarticulo)) {
    			$detalle = new DetalleVenta();
    			$detalle->idventa= $venta->idventa;
    			$detalle->idarticulo= $idarticulo[$cont];
    			$detalle->cantidad= $cantidad[$cont];
    			$detalle->descuento= $descuento[$cont];
    			$detalle->precio_venta= $precio_venta[$cont];
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

		return Redirect::to('ventas/venta');

    }


    public function show($id)
    {
    	// declaro la variable ingreso  va hacer igual al ingreso del index 
    	$venta=DB::table('venta as v')
            ->join('persona as p','v.idcliente','=','p.idpersona')
            ->join('detalle_venta as dv','v.idventa','=','dv.idventa')
            ->select('v.idventa','v.fecha_hora','p.nombre','v.tipo_comprobante','v.serie_comprobante','v.num_comprobante','v.impuesto','v.estado','v.total_venta')
            /*como solo necesito un solo venta indico con el where  indico que sea igual a la variable id del show */
            ->where('v.idventa','=',$id)

            ->groupBy('v.idventa','v.fecha_hora','p.nombre','v.tipo_comprobante','v.serie_comprobante', 'v.num_comprobante','v.impuesto','v.estado','v.total_venta')

            /*Como no llamo a ningun group by, se soluciona con el metodo first osea que coincida con el  id  con el de la condicion */
            ->first();

            /*Ahora creamos una nueva variable detalles y se muestra todo los detalle de ese valor en  especifico--unimos con la tabla ingreso  y definimos
            */

            $detalles=DB::table('detalle_venta as d')
            ->join('articulo as a','d.idarticulo','=','a.idarticulo')
            ->select('a.nombre as articulo','d.cantidad','d.descuento','d.precio_venta')
            ->where('d.idventa','=',$id)->get();

        return view("ventas.venta.show",["venta"=>$venta,"detalles"=>$detalles]);    

    }


    public  function destroy($id)
    {
    	// cuando se destruya una tabla cambiara  de estado A a C
    	$venta=Venta::findOrFail($id);
    	$venta->Estado='C';
    	$venta->update();
    	return Redirect::to('ventas/venta');

    }




}
