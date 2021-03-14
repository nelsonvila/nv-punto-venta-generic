<?php

namespace App\Http\Controllers;

use App\Caja;
use App\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Compra;
use App\DetalleCompra;

class CajaController extends Controller
{
    //
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar==''){
            $cajas = Caja::join('users','cajas.idusuario','=','users.id')
            ->select('cajas.id','cajas.fecha',
            'cajas.monto_inicio','cajas.monto_final','cajas.estado','users.usuario')
            ->orderBy('cajas.id', 'desc')->paginate(3);
        }
        else{
            $cajas = Caja::join('users','cajas.idusuario','=','users.id')
            ->select('cajas.id','cajas.fecha',
                'cajas.monto_inicio','cajas.monto_final','cajas.estado','users.usuario')
            ->where('cajas.'.$criterio, 'like', '%'. $buscar . '%')->orderBy('cajas.id', 'desc')->paginate(3);
        }

        return [
            'pagination' => [
                'total'        => $cajas->total(),
                'current_page' => $cajas->currentPage(),
                'per_page'     => $cajas->perPage(),
                'last_page'    => $cajas->lastPage(),
                'from'         => $cajas->firstItem(),
                'to'           => $cajas->lastItem(),
            ],
            'cajas' => $cajas
        ];
    }

    public function buscarCompra(Request $request){

        if (!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;
        $cajas = Compra::where('num_compra','=', $filtro)
        ->select('id','idproveedor','idusuario','tipo_identificacion','num_compra','fecha_compra')->orderBy('num_compra', 'asc')->take(1)->get();

        return ['cajas' => $cajas];

    }

    public function obtenerCabecera(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $compra = Compra::join('proveedores','cajas.idproveedor','=','proveedores.id')
        ->join('users','cajas.idusuario','=','users.id')
        ->select('cajas.id','cajas.tipo_identificacion',
        'cajas.num_compra','cajas.fecha_compra','cajas.total',
        'cajas.estado','proveedores.nombre','users.usuario')
        ->where('cajas.id','=',$id)
        ->orderBy('cajas.id', 'desc')->take(1)->get();

        return ['compra' => $compra];


    }

    public function obtenerDetalles(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $detalles = DetalleCompra::join('productos','detalle_cajas.idproducto','=','productos.id')
        ->select('detalle_cajas.cantidad','detalle_cajas.precio','productos.nombre as producto')
        ->where('detalle_cajas.idcompra','=',$id)
        ->orderBy('detalle_cajas.id','desc')->get();

        return ['detalles' => $detalles];

    }

    public function pdf(Request $request,$id){

        $cajas = Compra::join('proveedores','cajas.idproveedor','=','proveedores.id')
        ->join('users','cajas.idusuario','=','users.id')
        ->select('cajas.id','cajas.tipo_identificacion','cajas.num_compra',
        'cajas.created_at','cajas.total',
        'cajas.estado','proveedores.nombre','proveedores.tipo_documento',
        'proveedores.num_documento','proveedores.direccion','proveedores.email',
        'proveedores.telefono','users.usuario','users.nombre as nom_usuario')
        ->where('cajas.id','=',$id)
        ->orderBy('cajas.id', 'desc')->take(1)->get();

        $detalles = DetalleCompra::join('productos','detalle_cajas.idproducto','=','productos.id')
        ->select('detalle_cajas.cantidad','detalle_cajas.precio','productos.id',
        'productos.nombre as producto','productos.stock')
        ->where('detalle_cajas.id','=',$id)
        ->orderBy('detalle_cajas.id', 'desc')->get();

        $numcompra=Compra::select('num_compra')->where('id',$id)->get();

        $pdf = \PDF::loadView('pdf.compra',['cajas'=>$cajas,'detalles'=>$detalles]);
        return $pdf->download('compra-'.$numcompra[0]->num_compra.'.pdf');



    }



    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();

            $mytime= new \DateTime();
            $compra = new Compra();
            $compra->idproveedor = $request->idproveedor;
            $compra->idusuario = \Auth::user()->id;
            $compra->tipo_identificacion = $request->tipo_identificacion;
            $compra->num_compra = $request->num_compra;
            $compra->fecha_compra = $mytime->format('Y-m-d H:i:s');
            $compra->total = $request->total;
            $compra->estado = 'Registrado';
            $compra->save();

            //Array de detalles
            $detalles = $request->data;


            //Recorro todos los elementos

            foreach($detalles as $a=>$det)
            {
                $detalle = new DetalleCompra();
                /*enviamos valores a las propiedades del objeto detalle*/
                /*al idcompra del objeto detalle le envio el id del objeto compra, que es el objeto que se ingresó en la tabla cajas de la bd*/
                $detalle->idcompra = $compra->id;
                $detalle->idproducto = $det['idproducto'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->precio = $det['precio'];
                $detalle->save();
            }
            $this->sumarStock($detalles);

            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
        }
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $compra = Compra::findOrFail($request->id);
        $compra->estado = 'Anulado';
        $compra->save();
    }
    public function sumarStock($detalles){

        foreach($detalles as $a=>$det)
        {
            $detalle = Producto::where('id',$det['idproducto'])->first();
            $detalle->stock = $detalle->stock + $det['cantidad'];
            $detalle->save();
        }
    }
}
