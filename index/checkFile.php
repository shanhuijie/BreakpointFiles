<?php
$post = !empty($_POST['file']) ? $_POST : '';
$checkFile = new checkFile($post['file']);

class checkFile{
    private $filename;                              // 文件名
    private $filepath = "../file/uploads/";         // 分片上传目录

    public function __construct($filename){
      $this ->filename = $filename;
      $this ->filepath .= md5($this ->filename);
      $this ->checkDir();  
    }

    public function checkDir(){
        if(file_exists($this ->filepath)){
            $i = 0;
            while ($i<(2<<15)) {
                if(!file_exists($filename = $this->filepath.'/'. $this->filename.'_'.$i)&&$i>0){
                    $data['code'] = 3;
 
                    $data['msg'] = 'success';
     
                    $data['page'] = $i;
                    echo json_encode($data);break;
                }
                $i++;
            }
         }else{
         			$data['code'] = -3;
 
                    $data['msg'] = 'success';
     
                    $data['page'] = 0;

                    echo json_encode($data);
         }

    }


}
