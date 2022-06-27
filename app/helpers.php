<?php

use Illuminate\Http\Request;

/**
 * @param $builder
 * @param Request $request
 * @param bool $sort si es falso no se trata de ordenar
 * @return array|mixed
 */
function paginate($builder, Request $request, bool $sort= true){
    $request->validate([
        'perPage' => 'integer|min:1|max:1000',
        'page' => 'integer|min:1',
        'descargar' => 'in:true,false,1,0',
        'sortBy' => '',
        'sortDesc' => 'in:asc,desc',
    ]);
    if($request->sortBy && $sort){
        $builder->orderBy($request->get('sortBy','id'),$request->get('sortDesc','asc'));
    }
    $builder->with($request->get('with',[]));
    $builder->withCount($request->get('withCount',[]));
    if($request->get('descargar',false)){
        $ret = $builder->get();
        if($append = $request->get('append',[])){
            foreach($ret as $r){
                $r->append($append);
            }
        }
        return [
            'data' => $ret,
            'total' => count($ret),
            'last_page' => 1,
        ];
    }else{
        $ret = $builder->paginate($request->get('perPage',10),['*'],'page',$request->get('page',1));
        if($append = $request->get('append',[])){
            $ret->each(function($a) use ($append) {$a->append($append);});
        }
        return $ret;
    }
}
