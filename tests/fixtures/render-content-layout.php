<?php
require __DIR__.'/../../vendor/autoload.php';
$app=require __DIR__.'/../../bootstrap/app.php';$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$payload=app(App\Services\CmsDraftService::class)->build();
foreach(App\Services\ContentSchema::groups() as $group=>$definition){
 foreach($definition['fields'] as $path=>$field){
  if($field['type']==='media') continue;
  data_set($payload['settings'],$group.'.'.$path,rtrim(mb_substr(str_repeat('Proyek Santama Karya Surakarta ',50),0,$field['max'])));
 }
}
// Synthetic, local-only fixtures exercise both aspect ratios without remote photo availability.
foreach(['portrait'=>[600,900],'landscape'=>[1200,600]] as $variant=>[$width,$height]){
 $image=imagecreatetruecolor($width,$height);$base=imagecolorallocate($image,219,226,233);$accent=imagecolorallocate($image,217,107,39);imagefill($image,0,0,$base);
 imagefilledrectangle($image,0,(int)($height/3),$width,(int)($height*2/3),$accent);imagepng($image,base_path('.runtime/sprint4-'.$variant.'.png'));imagedestroy($image);
}
$portrait=url('/__sprint4/portrait.png');$landscape=url('/__sprint4/landscape.png');
foreach($payload['settings']['services']['items'] as $i=>&$item) $item['image_url']=$i%2?$portrait:$landscape;unset($item);
foreach($payload['portfolio']['projects'] as $i=>&$project) $project['image_url']=$i%2?$portrait:$landscape;unset($project);
$payload['slides']['about']=[['image_url'=>$portrait,'alt_text'=>'Fixture portrait','position_x'=>50,'position_y'=>50],['image_url'=>$landscape,'alt_text'=>'Fixture landscape','position_x'=>50,'position_y'=>50]];
foreach($payload['slides']['hero'] as &$slide) $slide['image_url']=$landscape;unset($slide);
$payload['settings']['contact']['background_url']=$landscape;
$payload['settings']['seo']=['title'=>str_repeat('A',70),'description'=>str_repeat('B',160),'og_image_url'=>$landscape];
file_put_contents(base_path('.runtime/sprint4-long-content.html'),view('landing',['settings'=>$payload['settings'],'slides'=>$payload['slides'],'portfolio'=>$payload['portfolio']])->render());
