<?php
require __DIR__.'/../../vendor/autoload.php';
$app=require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$payload=app(App\Services\CmsDraftService::class)->build();
$payload['slides']['about']=array_slice($payload['slides']['hero'],0,2);
foreach(['draft','empty'] as $variant){
 if($variant==='empty') $payload['portfolio']=['projects'=>[],'categories'=>[]];
 file_put_contents(base_path('.runtime/sprint2-'.$variant.'.html'),view('landing',['settings'=>$payload['settings'],'slides'=>$payload['slides'],'portfolio'=>$payload['portfolio']])->render());
}
